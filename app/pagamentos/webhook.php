<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);

include("./config.php");
require("../sistema/conexao.php");

/*
 * 1) Validação dos parâmetros do webhook
 *    Mercado Pago chama com ?topic=payment&id=123456789
 */
$topic = isset($_GET['topic']) ? trim($_GET['topic']) : '';
$id    = isset($_GET['id'])    ? trim($_GET['id'])    : '';

// Alguns setups usam "type" no lugar de "topic"
if ($topic === '' && isset($_GET['type'])) {
    $topic = trim($_GET['type']);
}

if ($topic === '' || $id === '') {
    // nada pra fazer
    exit('sem_dados');
}

/*
 * 2) Consulta do pagamento na API do Mercado Pago
 */
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL            => 'https://api.mercadopago.com/v1/payments/' . $id,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => '',
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => 'GET',
    CURLOPT_HTTPHEADER     => array('Authorization: Bearer ' . $TOKEN_MERCADO_PAGO),
));

$response_original = curl_exec($curl);
curl_close($curl);

$response = json_decode($response_original, true);
if (empty($response) || !is_array($response)) {
    exit('dados_vazios');
}

/*
 * 3) Campos do pagamento
 *    Alguns ambientes retornam no topo, outros dentro de "collection".
 *    Pegamos de forma segura (fallback).
 */
$idcobranca         = $response['external_reference']             ?? ($response['collection']['external_reference']     ?? null);
$status             = $response['status']                         ?? ($response['collection']['status']                 ?? null);
$payment_method_id  = $response['payment_method_id']              ?? ($response['collection']['payment_method_id']      ?? null);
$transaction_amount = $response['transaction_amount']             ?? ($response['collection']['transaction_amount']     ?? null);
$id_mercado_pago    = $response['id']                             ?? ($response['collection']['id']                     ?? null);

/*
 * 4) Pagamento aprovado → baixa segura + uso da rotina padrão (baixar_conta.php)
 */
if ($status === 'approved') {

    // id da conta no seu sistema: vem em external_reference
    $contaId = (int)$idcobranca;

    if ($contaId > 0) {

        // grava a ref do MP se ainda não tiver (não quebra nada se já tiver)
        $stmt = $pdo->prepare("
            UPDATE receber
               SET ref_pix = :ref
             WHERE id = :id
               AND (ref_pix IS NULL OR ref_pix = '')
        ");
        $stmt->execute([
            ':ref' => $id_mercado_pago,
            ':id'  => $contaId
        ]);

        // evita dupla baixa (se webhook e painel tentarem ao mesmo tempo)
        $row = $pdo->query("SELECT pago FROM receber WHERE id = '$contaId' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['pago'] !== 'Sim') {

            // usa a MESMA rotina de baixa do sistema (recorrência, caixa, etc.)
            $id = $contaId;                   // baixar_conta.php espera a variável $id
            require(__DIR__ . '/baixar_conta.php');
        }
    }

    echo json_encode(['status' => 'pago']);
    exit;
}

/*
 * 5) Outros status (pending, cancelled, etc.) → só responde
 */
echo json_encode(['status' => $status]);
exit;
?>
