<?php
declare(strict_types=1);

@session_start();
header('Content-Type: application/json; charset=UTF-8');

// (opcional) guarda de sessão sem redirecionar HTML – segue a sua lógica
if (@$_SESSION['id'] == "" || @$_SESSION['aut_token_1010'] != "fdsfdsafda8855555") {
  echo json_encode(['status' => 'error', 'msg' => 'Sessão expirada'], JSON_UNESCAPED_UNICODE);
  exit;
}

require_once("../../../conexao.php");

// Config
$tabela = 'avaliacoes_site';

// Inputs
$place_id = trim($_POST['place_id'] ?? '');
$api_key  = trim($_POST['api_key']  ?? '');
$debug    = isset($_GET['debug']) ? 1 : 0;

if ($place_id === '' || $api_key === '') {
  echo json_encode(['status' => 'error', 'msg' => 'Place ID e API Key são obrigatórios'], JSON_UNESCAPED_UNICODE);
  exit;
}

// cURL disponível?
if (!function_exists('curl_init')) {
  echo json_encode(['status' => 'error', 'msg' => 'cURL não está habilitado no servidor.'], JSON_UNESCAPED_UNICODE);
  exit;
}

// ---------- Função que tenta múltiplas variações do Place Details ----------
function fetch_place_details_multi(string $place_id, string $api_key): array
{
  $tries = [
    // 1) PT-BR + flags
    [
      'fields'                  => 'reviews,rating,user_ratings_total',
      'language'                => 'pt-BR',
      'reviews_no_translations' => 'true',
      'reviews_sort'            => 'newest',
    ],
    // 2) sem idioma/flags (fallback “cru”)
    [
      'fields' => 'reviews,rating,user_ratings_total',
    ],
    // 3) EN (alguns perfis só devolvem reviews em en)
    [
      'fields'   => 'reviews,rating,user_ratings_total',
      'language' => 'en',
    ],
  ];

  $last = ['http' => 0, 'err' => null, 'status' => null, 'body' => null];

  foreach ($tries as $opt) {
    $params = array_merge($opt, [
      'place_id' => $place_id,
      'key'      => $api_key,
    ]);

    $url = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_FAILONERROR    => false,
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_TIMEOUT        => 20,
      CURLOPT_USERAGENT      => 'Painel-Avaliacoes/1.1',
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $resp      = curl_exec($ch);
    $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err_no    = curl_errno($ch);
    $err_msg   = curl_error($ch);
    curl_close($ch);

    $last = [
      'http'   => $http_code,
      'err'    => $err_no ? "cURL {$err_no}: {$err_msg}" : null,
      'status' => null,
      'body'   => $resp,
    ];

    if ($err_no !== 0 || $http_code !== 200 || !$resp) {
      continue;
    }

    $json = json_decode($resp, true);
    if (!is_array($json)) {
      $last['err'] = 'JSON inválido';
      continue;
    }

    $last['status'] = $json['status'] ?? 'UNKNOWN';

    // Se status OK e já vier reviews, ótimo
    if (($json['status'] ?? '') === 'OK') {
      if (!empty($json['result']['reviews'])) {
        return ['ok' => true, 'json' => $json, 'last' => $last];
      }

      // guarda tentativa OK sem reviews – pode usar depois
      $noReviewsFallback = ['ok' => true, 'json' => $json, 'last' => $last];
      // continua tentando outras variações; se nenhuma trouxer reviews, volta essa
    }
  }

  // Se chegou aqui e houve alguma OK sem reviews, devolve-a
  if (isset($noReviewsFallback)) {
    return $noReviewsFallback;
  }

  // Falha total
  return ['ok' => false, 'json' => null, 'last' => $last];
}

// ---------- Chamada ----------
$result = fetch_place_details_multi($place_id, $api_key);

// debug opcional
if ($debug) {
  echo json_encode($result, JSON_UNESCAPED_UNICODE);
  exit;
}

if (!$result['ok']) {
  $last = $result['last'] ?? ['http' => 0, 'err' => null, 'status' => null];
  $msg  = "Falha na API do Google";
  if (!empty($last['status'])) $msg .= " ({$last['status']})";
  if (!empty($last['http']))   $msg .= " [HTTP {$last['http']}]";
  if (!empty($last['err']))    $msg .= " {$last['err']}";
  echo json_encode(['status' => 'error', 'msg' => $msg], JSON_UNESCAPED_UNICODE);
  exit;
}

$data = $result['json'];

// Status da API
$apiStatus = $data['status'] ?? 'UNKNOWN';
if ($apiStatus !== 'OK') {
  $err = $data['error_message'] ?? 'Falha ao consultar Place Details';
  echo json_encode(['status' => 'error', 'msg' => "Google Places retornou {$apiStatus}: {$err}"], JSON_UNESCAPED_UNICODE);
  exit;
}

// Reviews (pode vir vazio mesmo com user_ratings_total > 0)
$reviews   = $data['result']['reviews'] ?? [];
$userTotal = (int)($data['result']['user_ratings_total'] ?? 0);

if (!$reviews) {
  $msg = 'Nenhuma avaliação encontrada para este Place ID.';
  if ($userTotal > 0) {
    $msg .= ' Observação: o Google reporta '.$userTotal.' avaliação(ões),'
         .  ' mas não expôs o bloco "reviews" nesta chamada. Tente novamente mais tarde'
         .  ' ou considere integrar a Google Business Profile API para acesso completo.';
  }
  echo json_encode(['status' => 'error', 'msg' => $msg], JSON_UNESCAPED_UNICODE);
  exit;
}

// ---------- Persistência ----------
$importadas = 0;
$duplicadas = 0;

try {
  $pdo->beginTransaction();

  $sqlCheck = $pdo->prepare("SELECT id FROM {$tabela} WHERE externo_id = :externo_id LIMIT 1");
  $sqlIns   = $pdo->prepare("
    INSERT INTO {$tabela}
      (nome, nota, comentario, origem, data_avaliacao, foto, status, externo_id)
    VALUES
      (:nome, :nota, :comentario, 'Google', :data_avaliacao, :foto, 1, :externo_id)
  ");

  foreach ($reviews as $r) {
    $author_name = trim($r['author_name'] ?? 'Anônimo');

    $rating = (int)($r['rating'] ?? 5);
    if ($rating < 1) $rating = 1;
    if ($rating > 5) $rating = 5;

    $text = trim($r['text'] ?? '');

    $timeUnix = isset($r['time']) ? (int)$r['time'] : time();
    $data_avaliacao = date('Y-m-d', $timeUnix);

    $profile_photo = $r['profile_photo_url'] ?? null;
    $profile_photo = $profile_photo ? trim((string)$profile_photo) : null;
    if ($profile_photo !== null && !filter_var($profile_photo, FILTER_VALIDATE_URL)) {
      $profile_photo = null;
    }

    // ID externo determinístico para evitar duplicatas
    $externo_id = 'google_' . md5($author_name . '|' . (string)$timeUnix);

    // Deduplicação
    $sqlCheck->execute([':externo_id' => $externo_id]);
    if ($sqlCheck->fetchColumn()) {
      $duplicadas++;
      continue;
    }

    // Insert
    $sqlIns->execute([
      ':nome'           => $author_name,
      ':nota'           => $rating,
      ':comentario'     => ($text !== '' ? $text : null),
      ':data_avaliacao' => $data_avaliacao,
      ':foto'           => $profile_photo,
      ':externo_id'     => $externo_id,
    ]);

    $importadas++;
  }

  $pdo->commit();

  $msg = "Importação concluída! {$importadas} nova(s) avaliação(ões).";
  if ($duplicadas > 0) {
    $msg .= " {$duplicadas} já existiam.";
  }

  echo json_encode(['status' => 'success', 'msg' => $msg], JSON_UNESCAPED_UNICODE);
  exit;

} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo json_encode(['status' => 'error', 'msg' => 'Erro ao salvar no banco: '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
  exit;
}
?>
