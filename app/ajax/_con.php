<?php
// URL do site que você deseja testar
$url = 'https://chatbot.menuia.com';

// Inicia uma sessão cURL
$curl = curl_init($url);

// Configurações da requisição cURL
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); // Tempo limite de conexão em segundos

// Executa a requisição e obtém a resposta
$response = curl_exec($curl);

// Verifica se houve algum erro na requisição
if ($response === false) {
    // Houve um erro ao se conectar ao site
    echo 'Erro ao se conectar ao site: ' . curl_error($curl);
} else {
    // Conexão bem-sucedida, exibe a resposta do site
    echo 'Conexão bem-sucedida. Resposta do site: <br>';
    echo $response;
}

// Fecha a sessão cURL
curl_close($curl);

?>
