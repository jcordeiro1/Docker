<?php 
$tabela = 'cobrancas';
require_once("../../../conexao.php");
@session_start();
$id_usuario = @$_SESSION['id'];

$data_atual = date('Y-m-d');

$valor = $_POST['valor'];

$valor = preg_replace('/[^\d.,]/', '', $valor);

if (strpos($valor, ',') !== false && strpos($valor, '.') !== false) {
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor); 
}
// Se o valor tem apenas vírgula
elseif (strpos($valor, ',') !== false) {
    $valor = str_replace('.', '', $valor); 
    $valor = str_replace(',', '.', $valor); 
}

// Garante que o valor seja formatado como número com 2 casas decimais
$valor = number_format((float)$valor, 2, '.', '');


$parcelas = $_POST['parcelas'];
$obs = $_POST['obs'];
$data_venc = $_POST['data_venc'];
$id = @$_POST['id'];
$id2 = @$_POST['id2'];
$cliente = @$_POST['cliente'];
$dias_frequencia = $_POST['frequencia'];

$query = $pdo->query("SELECT * from frequencias where dias = '$dias_frequencia'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$frequencia = $res[0]['frequencia'];


// Vazio não é recorrente <-- se nao espeficar as parcelas ele vai considerar como uma
if($parcelas == "" || $parcelas == "0"){
	$parcelas = 1;
}

$valor_parcela = $valor / $parcelas;

if ($id2 == '') {
	
$query = $pdo->prepare("INSERT INTO $tabela SET cliente = '$id', valor = :valor, parcelas = :parcelas, data = curDate(), usuario = '$id_usuario', obs = :obs, data_venc = :data_venc, frequencia = '$frequencia', ativo = 'Sim' ");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET cliente = '$cliente', valor = :valor, parcelas = :parcelas, data = curDate(), usuario = '$id_usuario', obs = :obs, data_venc = :data_venc, frequencia = '$frequencia' where id = '$id2' ");

}

$query->bindValue(":valor", "$valor");
$query->bindValue(":parcelas", "$parcelas");
$query->bindValue(":obs", "$obs");
$query->bindValue(":data_venc", "$data_venc");
$query->execute();
$ult_id = $pdo->lastInsertId();

if ($id == '') {
	$id = '$id2';
}
//dados do cliente
$query = $pdo->query("SELECT * from clientes where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_cliente = @$res[0]['nome'];
$tel_cliente = @$res[0]['telefone'];
$tel_cliente = '55'.preg_replace('/[ ()-]+/' , '' , $tel_cliente);
$telefone = @$tel_cliente;


$valor_total_juros = 0;
$valor_parcelas_soma = 0;
$grupo = uniqid();

// Pega a quantidade de parcelas e consome a APi para criar a cobrança de acordo com os meses
for($i=1; $i <= $parcelas; $i++){
$descricao =  'Assinatura - '.$nome_cliente.' ('.$i.')';


	$dias_parcela = $i - 1;
	$dias_parcela_2 = ($i - 1) * $dias_frequencia;

	if($i == 1){
		$novo_vencimento = $data_venc;
	}else{


		if($dias_frequencia == 30 || $dias_frequencia == 31){
			
			$novo_vencimento = date('Y-m-d', strtotime("+$dias_parcela month",strtotime($data_venc)));

		}else if($dias_frequencia == 90){ 
			$dias_parcela = $dias_parcela * 3;
			$novo_vencimento = date('Y-m-d', strtotime("+$dias_parcela month",strtotime($data_venc)));

		}else if($dias_frequencia == 180){ 

			$dias_parcela = $dias_parcela * 6;
			$novo_vencimento = date('Y-m-d', strtotime("+$dias_parcela month",strtotime($data_venc)));

		}else if($dias_frequencia == 360 || $dias_frequencia == 365){ 

			$dias_parcela = $dias_parcela * 12;
			$novo_vencimento = date('Y-m-d', strtotime("+$dias_parcela month",strtotime($data_venc)));

		}else{
			
			$novo_vencimento = date('Y-m-d', strtotime("+$dias_parcela_2 days",strtotime($data_venc)));
		}

	}


if($parcelas > 1){
	$recorrencia = 'Não';
	$texto_cobranca = 'Frequência Parcelas ';
}else{
	$recorrencia = 'Sim';
	$texto_cobranca = 'Recorrência ';
}



if ($dias_frequencia == 0) {
	$recorrencia = 'Não';
}
if ($id2 == '') {
	

$pdo->query("INSERT INTO receber SET tipo = 'Serviço', cliente = '$id', grupo = '$grupo', referencia = 'Cobrança', id_ref = '$ult_id', valor = '$valor', parcela = '$i', usuario_lanc = '$id_usuario', data_lanc = curDate(), data_venc = '$novo_vencimento', pago = 'Não', descricao = '$descricao', frequencia = '$dias_frequencia', recorrencia = '$recorrencia', pessoa = '$id' ");
$ult_id_conta = $pdo->lastInsertId();
}else{



if($token != "" and $instancia != ""){
//recuperar o hash e excluir o agendamento das mensagens
$query2 = $pdo->query("SELECT * from receber where referencia = 'Cobrança' and id_ref = '$id2'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
for($i=0; $i<@count($res2); $i++){
$hash = @$res2[$i]['hash'];
require("../../../../ajax/api-excluir.php");
}
}	

	$pdo->query("UPDATE receber SET cliente = '$cliente', referencia = 'Cobrança', id_ref = '$id2', valor = '$valor', parcela = '$i', usuario_lanc = '$id_usuario', data_lanc = curDate(), data_venc = '$novo_vencimento', pago = 'Não', descricao = '$descricao', frequencia = '$dias_frequencia', recorrencia = '$recorrencia' where id_ref = '$id2' and pago = 'Não' ");
}
if($token != "" and $instancia != ""){
//fazer agendamentos das mensagens das contas
$novo_vencimentoF = implode('/', array_reverse(explode('-', $novo_vencimento)));
$valor_parcela_finalF = number_format($valor, 2, '.', '');
$valor_parcela_finalF = number_format($valor, 2, ',', '.');
$data_env = date('Y/m/d', strtotime("-$dias_aviso days",strtotime($novo_vencimento)));

$link_pgto = $url_sistema.'receber/'.@$ult_id_conta;

$mensagem =  '_'.$nome_sistema.'_ %0A';
$mensagem = '💰_Lembrete de pagamento_ %0A';
$mensagem .= '😀 Cliente: *'.$nome_cliente.'* %0A';
$mensagem .= '💲Valor: *'.$valor_parcela_finalF.'* %0A';
$mensagem .= '🕐 Vencimento: *'.$novo_vencimentoF.'* %0A%0A';
$mensagem .= '*Link Pagamento Pix:* %0A';
$mensagem .= $link_pgto;
@$data_mensagem .= $data_env.' 00:00:00';


if(strtotime($data_env) > strtotime($data_atual)){
	require('../../../../ajax/api-agendar.php');
	$pdo->query("UPDATE receber SET hash2 = '$hash' where id = '$ult_id_conta'");
}

//agendar mensagem para o dia do vencimento
$mensagem =  '_'.$nome_sistema.'_ %0A';
$mensagem = '💰_Sua assinatura vence hoje_ %0A';
$mensagem .= '😀 Cliente: *'.$nome_cliente.'* %0A';
$mensagem .= '💲Valor: *'.$valor_parcela_finalF.'* %0A';
$mensagem .= '🕐 Vencimento: *'.$novo_vencimentoF.'* %0A%0A';
$mensagem .= '*Link Pagamento:* %0A';
$mensagem .= $link_pgto;
@$data_mensagem = $novo_vencimento.' 00:00:00';

require('../../../../ajax/api-agendar.php');


if ($id2 == '') {


$pdo->query("UPDATE receber SET hash = '$hash' where id = '$ult_id_conta'");
}
}
}

echo 'Salvo com Sucesso';


//enviar mensagem para o cliente

$data_vencF = date('d', strtotime($data_venc));
$dataF = implode('/', array_reverse(explode('-', $data_atual)));
$valorF = number_format($valor, 2, '.', '');
$valorF = number_format($valor, 2, ',', '.');
$valor_total_jurosF = number_format($valor_total_juros, 2, ',', '.');

$mensagem =  '_'.$nome_sistema.'_ %0A';
$mensagem .= '😀 Cliente: *'.$nome_cliente.'* %0A';
$mensagem .= '💲Valor: *'.$valorF.'* %0A';
$mensagem .= 'Data Cobrança: *'.$dataF.'* %0A';
$mensagem .= 'Dia Pgto Cobranças: *Dia '.$data_vencF.'* %0A';
$mensagem .= $texto_cobranca.': *'.$frequencia.'* %0A%0A';
if($parcelas > 1){
$mensagem .= '*Parcelas* %0A';
$query = $pdo->query("SELECT * FROM receber where referencia = 'Cobrança' and id_ref = '$ult_id'  order by id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	for($i=0; $i < $total_reg; $i++){
		$valor = $res[$i]['valor'];
		$parcela = $res[$i]['parcela'];
		$data_venc = $res[$i]['data_venc'];
		$data_vencF = implode('/', array_reverse(explode('-', $data_venc)));
		$valorF = number_format($valor, 2, '.', '');
		$valorF = number_format($valor, 2, ',', '.');
		$mensagem .= "($parcela) R$: *$valorF* Venc: $data_vencF\n";
	}
}

}

require('../../../../ajax/api-texto.php');

 ?>