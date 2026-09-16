<?php 
@session_start();
require("../../sistema/conexao.php");

$id_conta = $_GET['id'];
$data_hoje = date('Y-m-d');

$query = $pdo->prepare("SELECT * FROM receber where id = :id_conta");
$query->bindValue(":id_conta", "$id_conta");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$cliente = @$res[0]['pessoa'];
$data = $res[0]['data_venc'];
$pago = $res[0]['pago'];
$valor = $res[0]['valor'];
$ref_pix = $res[0]['ref_pix'];
$frequencia = $res[0]['frequencia'];
$dataF = implode('/', array_reverse(explode('-', $data)));
$descricao = $res[0]['descricao'];

$valorF = number_format($valor, 2, ',', '.');


$query = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_cliente =  @$res[0]['nome'];
$cpf_cliente =  '450.417.700-50';
$email_cliente =  'cliente@hotmail.com';

$total = $valor;
$valor_total = $valor;

require("configApi.php");

$nome       = $_POST['nome'] ?? null;
$cpf        = $_POST['cpf'] ?? null;
$cardnumber = $_POST['cardnumber'] ?? null;
$cep        = $_POST['cep'] ?? null;
$numero     = $_POST['numero'] ?? null;
$bairro      = $_POST['bairro'] ?? null;
$rua        = $_POST['rua'] ?? null;
$expdate    = $_POST['expdate'] ?? null;
$partes     = explode("/", $expdate);
$mes        = @$partes[0]; // "12"
$ano        = @$partes[1]; // "2025"

$ccv = $_POST['ccv'] ?? null;
$parcelas = $_POST['parcelas'] ?? null;
$telefone = $_POST['telefone'] ?? null;
$email = $_POST['email'] ?? null;

for ($i = $parcelas; $i <= $parcelas; $i++) {
    $valor_com_juros = floatval($total); // Substitua pelo valor que deseja parcelar
    $juros = 1.99 / 100; // Taxa de juros mensal
    $valor_parcela  = $valor_com_juros * ($juros / (1 - (1 + $juros) ** (-$i)));
    
}
if($parcelas < 2){
    $valor_parcela_ok  = floatval($total);
} else {
    $valor_parcela_ok  = number_format($valor_parcela, 2, '.', '');
}


$dados_cliente['name']      = $nome;
$dados_cliente['cpfCnpj']   = $cpf;
$curl_cliente = curl_init();
    curl_setopt_array($curl_cliente, array(
        CURLOPT_URL => 'https://api.asaas.com/v3/customers',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados_cliente),
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'User-agent: 123',
        'access_token: '.$access_token
    ),
    ));
    $response_cliente = curl_exec($curl_cliente);
    $resultado_cliente = json_decode($response_cliente);
    //var_dump($response_cliente);
curl_close($curl_cliente);

//gerando transacao
$dados_transacao['billingType']    = 'CREDIT_CARD';

    $dados_transacao['creditCard']['holderName']    = $nome;
    $dados_transacao['creditCard']['number']        = $cardnumber;
    $dados_transacao['creditCard']['expiryMonth']   = $mes;
    $dados_transacao['creditCard']['expiryYear']    = $ano;
    $dados_transacao['creditCard']['ccv']           = $ccv;
    
    $dados_transacao['creditCardHolderInfo']['name']                = $nome;
    $dados_transacao['creditCardHolderInfo']['email']               = $email;
    $dados_transacao['creditCardHolderInfo']['cpfCnpj']             = $cpf;
    $dados_transacao['creditCardHolderInfo']['postalCode']          = $cep;
    $dados_transacao['creditCardHolderInfo']['addressNumber']       = $numero;
    $dados_transacao['creditCardHolderInfo']['addressComplement']   = null;
    $dados_transacao['creditCardHolderInfo']['address']             = $rua;
    $dados_transacao['creditCardHolderInfo']['province']            = $bairro;
    
    $dados_transacao['creditCardHolderInfo']['phone']               = $telefone;
    $dados_transacao['creditCardHolderInfo']['mobilePhone']         = '';
    
    $dados_transacao['customer']            = @$resultado_cliente->id;
    $dados_transacao['dueDate']             = date('Y-m-d');
    $dados_transacao['installmentCount']    = $parcelas;
    $dados_transacao['installmentValue']    = $valor_parcela_ok;
    $dados_transacao['value']               = $valor_total;
    $dados_transacao['description']         = 'Venda Produto';
    $dados_transacao['externalReference']   = $id_conta;

$curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.asaas.com/v3/payments/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados_transacao),
    CURLOPT_HTTPHEADER => array(
        'accept: application/json',
        'content-type: application/json',
        'User-agent: 123',
        'access_token: '.$access_token
    ),
    ));
    $response = curl_exec($curl);
    $resultado = json_decode($response, true);
    //var_dump($response);
    if(@$resultado['errors'][0]['description'] <> ''){
        echo '
        <div class="alert alert-primary" role="alert">
            '.$resultado['errors'][0]['description'].'
        </div>';
    } else {
        echo '
        <div class="alert alert-success" role="alert">
            Transação processada com sucesso, aguardando confirmação...
        </div>';
    }
    if(@$resultado['id'] <> ''){
          $pdo->query("UPDATE receber SET ref_pix = '{$resultado['id']}' where id = '$id_conta'");        
    }
curl_close($curl);
?>