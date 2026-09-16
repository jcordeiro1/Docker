<?php
@$mensagem = str_replace("%0A", "\n", $mensagem); 
  $url = "http://apis.zape.fun/send-text";

  $data = array('instance' => "",
                'to' => "5545998317101",
                'token' => "48A3H-0J4-12379",
                'message' => "Mensagem a ser Enviada");


  $options = array('http' => array(
                 'method' => 'POST',
                 'content' => http_build_query($data)
  ));

  $stream = stream_context_create($options);

  $result = @file_get_contents($url, false, $stream);

  echo $result;
?>
  