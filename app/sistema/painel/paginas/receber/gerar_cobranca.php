<?php 
$tabela = 'receber';
require_once("../../../conexao.php");
$data_atual = date('Y-m-d');

$id_conta = $_POST['id'];
$valor = $_POST['valor'];
$data = $_POST['data'];
$telefone = $_POST['telefone'];
$descricao = $_POST['descricao'];



$tel_cliente = '55'.preg_replace('/[ ()-]+/' , '' , $telefone);
$telefone = $tel_cliente;

$valorF = @number_format($valor, 2, ',', '.');
$dataF = implode('/', array_reverse(@explode('-', $data)));

if(strtotime($data) < strtotime($data_atual)){
	$titulo_mensagem = 'Sua Parcela Venceu!';
}else{
	$titulo_mensagem = 'Lembrete de Pagamento!';
}

$link_pgto = $url_sistema.'conta/'.$id_conta;


//mensagem da cobrança
$mensagem = '*'.$nome_sistema.'* %0A';
$mensagem .= '_'.$titulo_mensagem.'_ %0A';
$mensagem .= 'Conta: '.$descricao.' %0A';
$mensagem .= 'Valor: *R$ '.$valorF.'* %0A';
$mensagem .= 'Vencimento: *'.$dataF.'* %0A%0A';
$mensagem .= '*Link Pagamento:* %0A';
$mensagem .= $link_pgto;

require("../../../../ajax/api-texto.php");

?>