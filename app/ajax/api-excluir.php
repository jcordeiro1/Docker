<?php
if (!isset($hash) || trim((string)$hash) === '') {
    return;
}

$hash = trim((string)$hash);

if (isset($api) && $api == 'menuia') {

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array(
        'appkey' => $token,
        'authkey' => $instancia,
        'cancelarAgendamento' => 'true',
        'message' => $hash,
      ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    return;
}

// Legado (Enviame): mantém comportamento antigo
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.enviame.com.br/whatsapp/agendar/cancelar',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "instancia": "'.$instancia.'",
    "codigo": "'.$hash.'"
}',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

?>
