<?php
$mensagem = isset($mensagem) ? str_replace("%0A", "\n", $mensagem) : '-' ; 


$telefone = preg_replace("/[^0-9]/", "", $telefone);

// Verificar se o número tem menos de 11 dígitos
if (strlen($telefone) <= 11) {
    // Adicionar "55" na frente
    $telefone = "55" . $telefone;
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
  'appkey' => $token,
  'authkey' => $instancia,
  'to' => $telefone,
  'message' => $mensagem,
  'file' => $url_envio
  ),
));


$response = curl_exec($curl);
echo $response;
curl_close($curl);


?>