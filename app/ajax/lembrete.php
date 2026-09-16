<?php
// WAF em modo DETECT para este endpoint (não bloqueia clientes, só registra log)
define('SEC_WAF_MODE', 'detect');
require_once __DIR__ . '/../security.php';

// Debug (se quiser tirar depois, pode)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Conexão com o banco (se o security.php já carregou, o require_once não duplica)
require_once __DIR__ . '/../sistema/conexao.php';

// Sessão sem dar "session already started"
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// se não existir, define menuia como padrão
if (!isset($api) || empty($api)) {
    $api = 'menuia';
}

$data    = date('Y-m-d', strtotime("$dias_aviso day"));
$datePT  = date('d/m/Y', strtotime("$dias_aviso day"));
$hoje    = date('d/m/Y');

$query   = "SELECT * FROM receber WHERE data_venc = '$data' AND pago != 'Sim' AND recorrencia = 'Sim' ";
$receber = $pdo->query($query);
$faturas = $receber->fetchAll(PDO::FETCH_ASSOC);

if (empty($faturas)) {
    echo "Nenhum aviso para hoje ($hoje).";
    exit();
}

foreach ($faturas as $fatura) {

    $grupo        = $fatura['grupo'];
    $descricao    = $fatura['descricao'];
    $valor        = $fatura['valor'];
    $usuario      = $fatura['pessoa'];
    $idCobranca   = $fatura['id'];
    $parcela      = $fatura['parcela'];
    $id_ref       = $fatura['id_ref'];
    $frequencia   = $fatura['frequencia'];
    $Nparcela     = $parcela + 1;
    $usuario_lanc = $fatura['usuario_lanc'];
    $data_venc    = date('Y-m-d', strtotime($data . ' +1 month'));

    // Obtendo valor original da cobrança
    $query      = "SELECT * FROM cobrancas WHERE id = '$id_ref'";
    $cobrancas  = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);
    $valorF     = $cobrancas[0]['valor'] ?? $valor;

    // Dados do cliente
    $query       = "SELECT * FROM clientes WHERE id = '$usuario'";
    $resCliente  = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

    if (empty($resCliente)) {
        continue; // sem cliente, não envia nada
    }

    $nome     = $resCliente[0]['nome'];
    $telefone = $resCliente[0]['telefone'] ?? 0;

    // normaliza telefone
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    if (strlen($telefone) <= 11 && strlen($telefone) > 1) {
        $telefone = '55' . $telefone;
    }

    // Monta mensagem
    $mensagem  = "_Assinatura vencerá em breve {$nome}_\n";
    $mensagem .= "Valor: *{$valor}*\n";
    $mensagem .= "Data: *{$datePT}*\n\n";
    $mensagem .= "*Link de Pagamento:*\n{$url_sistema}pagar/{$idCobranca}";

    // Envio somente se tiver número válido
    if ($telefone != 0) {

        // ============================
        // 1) ENVIO VIA MENUIA
        // ============================
        if ($api === "menuia") {

            $mensagem = str_replace("%0A", "\n", $mensagem);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL            => 'https://chatbot.menuia.com/api/create-message',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => array(
                    'appkey'  => $token,
                    'authkey' => $instancia,
                    'to'      => $telefone,
                    'message' => $mensagem,
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
        }

        // ============================
        // 2) ENVIO VIA WORDMENSAGENS
        // ============================
        else {

            // data/hora para o envio (agora)
            $data_envio = date('Y-m-d H:i:s');

            // tempo de aviso / confirmação (aqui deixei 0 – envia imediato)
            $horas_confirmacaoF = '0';

            // monta JSON da API wordmensagens
            $payload = json_encode([
                "instance"      => $instancia,
                "to"            => $telefone,
                "message"       => $mensagem,
                "msg_erro"      => "Desculpe, tente novamente mais tarde.",
                "msg_confirma"  => "Mensagem recebida. Obrigado! ✅",
                "msg_reagendar" => "",
                "id_consulta"   => (string)$idCobranca,
                "url_recebe"    => $url_sistema . "ajax/retorno.php",
                "data"          => $data_envio,
                "aviso"         => $horas_confirmacaoF
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL            => 'https://api.enviame.com.br/whatsapp/agendar/texto',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            // se quiser usar o id retornado:
            // $responseObj = json_decode($response, false);
            // $id_word = $responseObj->id ?? null;
        }
    }
}

echo "Lembretes enviados!";
