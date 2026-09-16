<?php
@session_start();
require("../sistema/conexao.php");
require("config/configApi.php");

$pago = 'Não';

//ref pix testes aprovado pay_wdx0czye9gp2u4tj
if(@$ref_pix == ""){
    $id_conta = filter_var(@$_GET['id'], @FILTER_SANITIZE_STRING);
    $query40 = $pdo->query("SELECT * FROM receber where id = '$id_conta'");
    $redirecionar = 'Sim';
}else{
    $query40 = $pdo->query("SELECT * FROM receber where ref_pix = '$ref_pix' order by id desc limit 1");
    $redirecionar = 'Não';
}

$res40 = $query40->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res40);
$id_conta_ref = @$res40[0]['id'];
$ref_pix = @$res40[0]['ref_pix'];
$pago = @$res40[0]['pago'];


// Verifica o pagamento se ainda não estiver marcado como pago
if ($api_pagamento == 'Asaas' and $pago != 'Sim') {
    $payment_id = $ref_pix;

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.asaas.com/v3/payments/' . $payment_id, // Consulta completa da cobrança
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'accept: application/json',
            'content-type: application/json',
            'User-Agent: MeuSistema/1.0',
            'access_token: ' . $access_token
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $resultado = json_decode($response);

    // Apenas para visualizar o retorno completo em teste:
    // echo "<pre>"; print_r($resultado); echo "</pre>"; exit();

    // Variáveis corretas agora:
    $status_api = $resultado->status ?? 'PENDING'; // Status
    $valor_liquido = $resultado->netValue ?? 0; // Valor líquido recebido
    $total_pago = $resultado->value ?? 0; // Valor da cobrança
    $tipo_pagamento = $resultado->billingType ?? 'PIX'; // Forma de pagamento

    //echo "Status: $status_api <br>";
    //echo "Valor Pago (líquido): R$ " . number_format($valor_liquido, 2, ',', '.') . "<br>";
    //echo "Valor Original: R$ " . number_format($total_pago, 2, ',', '.') . "<br>";
    //echo "Forma de Pagamento: $tipo_pagamento <br>";


    if($tipo_pagamento == 'CREDIT_CARD'){
        $tipo_pagamento = 'Cartão de Crédito';
    }
   
    if($tipo_pagamento == 'BOLETO'){
        $tipo_pagamento = 'Boleto';
    }

    if($tipo_pagamento == 'PIX '){
        $tipo_pagamento = 'Pix';
    }

    //echo $status_api;


    // Se confirmado ou recebido, atualiza como pago no banco
    if (in_array($status_api, ['RECEIVED', 'CONFIRMED'])) {   


     $valor = $total_pago;
     $id = $id_conta_ref;
     $tabela = 'receber';
     $data_pgto = date('Y-m-d');
     $forma_pgto =  $tipo_pagamento;
    require('../pagamentos/baixar_conta.php'); 

    if($redirecionar != 'Não') {
        echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=" . $url_sistema . "asaas_planos/obrigado.php?id=" . $id_conta . "'>";
         exit;
    }
    
     
    

    }

}

?>
