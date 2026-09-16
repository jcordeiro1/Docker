<?php


if ($api == "menuia") 
{
   $mensagem = str_replace("%0A", "\n", $mensagem); 

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
      'licence' => 'hugocursos',
      'message' => $mensagem,
      ),
    ));
    
    $response = curl_exec($curl);
   
    curl_close($curl);
    
    
    $response = json_decode($response, true);

} 
else 
{
  
   
   $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.enviame.com.br/whatsapp/enviar/texto',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "instancia": "'.$instancia.'",
        "token": "'.$token.'",
        "para": "'.$telefone.'",
        "mensagem": "'.$mensagem.'"
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);

}

   
?>
  

