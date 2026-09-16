<?php
@session_start();
header('Content-Type: application/json; charset=utf-8');

ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once(__DIR__ . '/../sistema/conexao.php');

function responderErro($mensagem, $codigo = 400){
	http_response_code($codigo);
	echo json_encode(array(
		'status' => 'Erro',
		'mensagem' => $mensagem
	), JSON_UNESCAPED_UNICODE);
	exit();
}

/*
|----------------------------------------------------------
| CONTEXTO MASTER x MARKETPLACE
| - loja:  /loja/<slug>/...  => envia ?loja=slug no fetch
| - master: sem loja         => usa banco individual do master
|----------------------------------------------------------
| Não confiar apenas na sessão antiga de tenant.
| Só assume tenant quando:
| - vier em GET
| - vier em POST
| - vier na URL bonita /loja/<slug>/
*/
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$tenant = '';
$loja_por_parametro = false;
$loja_por_path = false;

if (!empty($_GET['loja'])) {
	$tenant = preg_replace('/[^a-z0-9_-]/i', '', (string)$_GET['loja']);
	$loja_por_parametro = ($tenant !== '');
} elseif (!empty($_POST['loja'])) {
	$tenant = preg_replace('/[^a-z0-9_-]/i', '', (string)$_POST['loja']);
	$loja_por_parametro = ($tenant !== '');
} elseif (preg_match('~^/loja/([a-z0-9_-]+)(?:/|$)~i', $uriPath, $m)) {
	$tenant = preg_replace('/[^a-z0-9_-]/i', '', (string)$m[1]);
	$loja_por_path = ($tenant !== '');
}

if ($tenant !== '') {
	$_SESSION['tenant_slug'] = $tenant;
	$_GET['loja'] = $tenant;
} else {
	if (!$loja_por_parametro && !$loja_por_path) {
		unset($_GET['loja']);
		unset($_POST['loja']);
	}
}

/*
|----------------------------------------------------------
| Validar upload
|----------------------------------------------------------
*/
if (!isset($_FILES['foto'])) {
	responderErro('Nenhuma imagem enviada');
}

if (!isset($_FILES['foto']['tmp_name']) || !is_uploaded_file($_FILES['foto']['tmp_name'])) {
	responderErro('Arquivo inválido');
}

$ext = strtolower(pathinfo((string)$_FILES['foto']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, array('jpg', 'jpeg', 'png', 'webp'))) {
	responderErro('Formato de imagem não permitido');
}

$mime = 'image/jpeg';
if ($ext === 'png') {
	$mime = 'image/png';
} elseif ($ext === 'webp') {
	$mime = 'image/webp';
}

/*
|----------------------------------------------------------
| Pasta local de simulações
|----------------------------------------------------------
*/
$pasta = __DIR__ . '/../sistema/painel/images/simulacoes/';

if (!is_dir($pasta)) {
	responderErro('Pasta de simulações não encontrada');
}

if (!is_writable($pasta)) {
	responderErro('Pasta de simulações sem permissão de escrita');
}

/*
|----------------------------------------------------------
| Salvar imagem original no servidor
|----------------------------------------------------------
*/
$nomeOriginal = 'cliente_original_' . md5(uniqid((string)mt_rand(), true)) . '.' . $ext;
$caminhoOriginal = $pasta . $nomeOriginal;

if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoOriginal)) {
	responderErro('Erro ao salvar imagem original');
}

if (!file_exists($caminhoOriginal)) {
	responderErro('Arquivo original não foi gravado');
}

/*
|----------------------------------------------------------
| Buscar OpenAI Key no banco individual do contexto atual
|----------------------------------------------------------
*/
$queryCfg = $pdo->query("SELECT * FROM config LIMIT 1");
$config = $queryCfg->fetch(PDO::FETCH_ASSOC);

if (!$config) {
	@unlink($caminhoOriginal);
	responderErro('Configuração não encontrada');
}

$openai_key = trim((string)($config['openai_key'] ?? ''));
if ($openai_key === '') {
	@unlink($caminhoOriginal);
	responderErro('OpenAI Key não cadastrada no sistema');
}

/*
|----------------------------------------------------------
| Preparar data URL
|----------------------------------------------------------
*/
$binarioOriginal = @file_get_contents($caminhoOriginal);
if ($binarioOriginal === false || $binarioOriginal === '') {
	@unlink($caminhoOriginal);
	responderErro('Erro ao ler imagem original');
}

$dataUrlOriginal = 'data:' . $mime . ';base64,' . base64_encode($binarioOriginal);

/*
|----------------------------------------------------------
| Prompt do cliente final
|----------------------------------------------------------
*/
$prompt = "Create a realistic capillary prosthesis simulation based on the uploaded photo. ";
$prompt .= "STRICT RULES: preserve exactly the same person identity. ";
$prompt .= "Keep exactly the same face, same age, same facial features, same forehead, same expression, same beard, same eyebrows, same ears, same neck, same body proportion, same camera angle, same framing, same pose, same background and same lighting. ";
$prompt .= "Do not alter clothes. ";
$prompt .= "Do not alter skin tone. ";
$prompt .= "Do not retouch the skin. ";
$prompt .= "Do not make the person younger. ";
$prompt .= "Do not beautify the face. ";
$prompt .= "Do not change the beard color or beard shape. ";
$prompt .= "Do not change the eyebrow color or eyebrow shape. ";
$prompt .= "Do not change the background. ";
$prompt .= "Do not change the shoulders or clothing texture. ";
$prompt .= "ONLY modify the bald area of the scalp to simulate a professional hair prosthesis. ";
$prompt .= "The simulation must affect only the hair/prosthesis area. ";
$prompt .= "The prosthesis hair color must MATCH EXACTLY the visible natural side hair already present in the photo. ";
$prompt .= "Use the side hair as the ONLY color reference. ";
$prompt .= "If the existing hair is gray, white, salt-and-pepper, dark gray, brown-gray or mixed, the prosthesis must keep exactly the same tone and same mixture proportion. ";
$prompt .= "Preserve the exact same percentage of gray and white strands already visible in the remaining hair. ";
$prompt .= "Do not create darker hair than the side hair. ";
$prompt .= "Do not create black hair if the side hair is gray or mixed gray. ";
$prompt .= "Do not create brown hair if the side hair is gray or white. ";
$prompt .= "Do not warm the tone. ";
$prompt .= "Do not increase saturation. ";
$prompt .= "Do not recolor the existing hair. ";
$prompt .= "Do not recolor the prosthesis to an idealized younger tone. ";
$prompt .= "The prosthesis must blend naturally with the exact same color, tone, texture, density and direction of the existing side hair. ";
$prompt .= "Keep the same natural aging appearance of the hair. ";
$prompt .= "Do not change the skin, clothes or any other part of the image. ";
$prompt .= "Result must look like the same real person with a realistic capillary prosthesis preview only on the scalp, matching exactly the current side hair color.";

/*
|----------------------------------------------------------
| Chamada da OpenAI
|----------------------------------------------------------
*/
$payload = array(
	'model' => 'gpt-image-1',
	'prompt' => $prompt,
	'images' => array(
		array(
			'image_url' => $dataUrlOriginal
		)
	),
	'size' => '1024x1536',
	'quality' => 'medium',
	'output_format' => 'png'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/images/edits');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 180);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Authorization: Bearer ' . $openai_key
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

$resposta = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$erroCurl = curl_error($ch);
curl_close($ch);

if ($erroCurl !== '') {
	@unlink($caminhoOriginal);
	responderErro('Erro CURL: ' . $erroCurl, 500);
}

if ($resposta === '' || $resposta === false) {
	@unlink($caminhoOriginal);
	responderErro('Resposta vazia da IA', 500);
}

$json = json_decode($resposta, true);
if (!is_array($json)) {
	@unlink($caminhoOriginal);
	responderErro('Resposta inválida da IA', 500);
}

if ($http !== 200) {
	$mensagemErro = 'Erro ao gerar imagem';
	if (isset($json['error']['message']) && $json['error']['message'] !== '') {
		$mensagemErro = $json['error']['message'];
	}
	@unlink($caminhoOriginal);
	responderErro($mensagemErro, $http > 0 ? $http : 500);
}

if (!isset($json['data'][0]['b64_json']) || $json['data'][0]['b64_json'] === '') {
	@unlink($caminhoOriginal);
	responderErro('A IA não retornou imagem simulada', 500);
}

$imagemBinaria = base64_decode((string)$json['data'][0]['b64_json']);
if ($imagemBinaria === false || $imagemBinaria === '') {
	@unlink($caminhoOriginal);
	responderErro('Erro ao decodificar a imagem simulada', 500);
}

/*
|----------------------------------------------------------
| Salvar imagem simulada
|----------------------------------------------------------
*/
$nomeSimulada = 'cliente_simulada_' . md5(uniqid((string)mt_rand(), true)) . '.png';
$caminhoSimulada = $pasta . $nomeSimulada;

if (@file_put_contents($caminhoSimulada, $imagemBinaria) === false) {
	@unlink($caminhoOriginal);
	responderErro('Erro ao salvar imagem simulada', 500);
}

if (!file_exists($caminhoSimulada)) {
	@unlink($caminhoOriginal);
	responderErro('Arquivo da imagem simulada não foi gravado', 500);
}

/*
|----------------------------------------------------------
| Montar URLs públicas
|----------------------------------------------------------
*/
$proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$proto = 'https';
}
$host = (string)($_SERVER['HTTP_HOST'] ?? '');

if ($host === '') {
	@unlink($caminhoOriginal);
	@unlink($caminhoSimulada);
	responderErro('Host inválido para montar URL pública', 500);
}

$base = $proto . '://' . $host;

$urlOriginal = $base . '/sistema/painel/images/simulacoes/' . rawurlencode($nomeOriginal) . '?v=' . time();
$urlSimulada = $base . '/sistema/painel/images/simulacoes/' . rawurlencode($nomeSimulada) . '?v=' . time();

/*
|----------------------------------------------------------
| Enviar imagem simulada por WhatsApp
| segue padrão marketing_foto.php
|----------------------------------------------------------
*/
$nomeCliente = isset($_POST['nome']) ? trim((string)$_POST['nome']) : '';
$telefoneCliente = isset($_POST['telefone']) ? trim((string)$_POST['telefone']) : '';

if ($telefoneCliente !== '') {
	$telefoneNumeros = preg_replace('/[^\d]+/', '', $telefoneCliente);
	if ($telefoneNumeros !== '') {
		if (strpos($telefoneNumeros, '55') !== 0) {
			$telefoneNumeros = '55' . $telefoneNumeros;
		}

		$nomeSistema = trim((string)($config['nome'] ?? ''));
		if ($nomeSistema === '') {
			$nomeSistema = 'BarberBot';
		}

		$mensagem = "👋 *Olá";
		if ($nomeCliente !== '') {
			$mensagem .= " {$nomeCliente}";
		}
		$mensagem .= "*\n\n";
		$mensagem .= "Sua *simulação de prótese capilar* foi gerada com sucesso no *{$nomeSistema}*.\n\n";
		$mensagem .= "🖼️ Estou enviando sua imagem simulada abaixo.";

		$numeros_formatados = $telefoneNumeros;
		$url_arquivo = preg_replace('/\?v=\d+$/', '', $urlSimulada);

		$instancia = trim((string)($config['instancia'] ?? ''));
		$token = trim((string)($config['token'] ?? ''));

		if ($instancia !== '' && $token !== '') {
			$arquivoEnvioFoto = $_SERVER['DOCUMENT_ROOT'] . '/ajax/marketing_foto.php';
			if (is_file($arquivoEnvioFoto)) {
				require $arquivoEnvioFoto;
			}
		}
	}
}

/*
|----------------------------------------------------------
| Retorno final
|----------------------------------------------------------
| Compatibilidade:
| - imagem_simulada e foto_original retornam URL completa
| - arquivo_original e arquivo_simulada mantêm o nome do arquivo
*/
echo json_encode(array(
	'status' => 'Sucesso',
	'mensagem' => 'Simulação gerada com sucesso',
	'arquivo_original' => $nomeOriginal,
	'arquivo_simulada' => $nomeSimulada,
	'foto_original' => $urlOriginal,
	'imagem_simulada' => $urlSimulada,
	'url_original' => $urlOriginal,
	'url_simulada' => $urlSimulada
), JSON_UNESCAPED_UNICODE);
?>