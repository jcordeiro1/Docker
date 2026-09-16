<?php

$url_destino = "https://gasto.jc.tec.br/painel/apis/bloqueio_cron.php";

// Parâmetros para enviar
$data = array('site' => $url_sistema);

// Inicializar CURL
$ch = curl_init();

// Configurar CURL
curl_setopt($ch, CURLOPT_URL, $url_destino);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Executar CURL
$response = curl_exec($ch);

// Verificar por erros
if(curl_errno($ch)){
    echo 'Erro ao fazer a solicitação: ' . curl_error($ch);
}

// Fechar CURL
curl_close($ch);

// Exibir resposta
$response = json_decode($response, true);
$status = $response['status'] ?? '';

if($status != 200)
{
    echo $response['message'] ?? 'Seu plano expirou, por favor renove seu plano para continuar utilizando nossos serviços';
    exit();
}


?>
