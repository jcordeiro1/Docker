<?php 
$tabela = 'receber';
require_once("../../../conexao.php");
$data_atual = date('Y-m-d');

$parcela = $_POST['parcela'];
$valor = $_POST['valor'];
$data = $_POST['data'];
$telefone = $_POST['telefone'];
$multa = $_POST['multa'];
$juros = $_POST['juros'];
$id_conta = $_POST['id_par'];
$descricao = $_POST['descricao'];

$tel_cliente = '55'.preg_replace('/[ ()-]+/' , '' , $telefone);
$telefone = $tel_cliente;

if($juros == ""){
	$juros = 0;
}

if($multa == ""){
	$multa = 0;
}

$valorF = @number_format($valor, 2, '.', '');
$valorF = @number_format($valor, 2, ',', '.');
$jurosF = @number_format($juros, 2, ',', '.');
$multaF = @number_format($multa, 2, ',', '.');
$dataF = implode('/', array_reverse(explode('-', $data)));

if(@strtotime($data) < @strtotime($data_atual)){
	$titulo_mensagem = '⚠️Atenção!  regularize hoje!⚠️';
}else{
	$titulo_mensagem = '📩 Lembrete pagamento';
}

$link_pgto = "{$url_sistema}receber/{$id_conta}";

//mensagem da cobranÃ§a

$mensagem .= "_{$titulo_mensagem}_ \n";
$mensagem = "*{$nome_sistema}* \n";
$mensagem .= "Assinatura: *{$descricao}* \n";
if($multa > 0){
	$mensagem .= "Multa Atraso: *R$ {$multaF}* \n";
}

if($juros > 0){
	$mensagem .= "Juros Atraso: *R$ {$jurosF}* \n";
}

$mensagem .= "💲 Valor: *R$ {$valorF}* \n";
$mensagem .= "Vencimento: *{$dataF}* \n\n";
$mensagem .= "*Link Pagamento Pix:* \n";
$mensagem .= "$link_pgto";


require('../../../../ajax/api-texto.php');

?>

