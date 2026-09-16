<?php
// Ativar a exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$statusPay = $status ?? false; 
error_reporting(E_ALL);

$id_conta = $idConta ?? $_GET['id_agd'] ?? '';
if($id_conta != ""){
	  if(@$porc_servico > 0 && !$statusPay){ 
	  	echo 'Faça o pagamento antes de ir para o agendamento';
	  	exit();
	  }
	 require("../sistema/conexao.php");
	 $valor_pago = '0';
	 $query = $pdo->query("SELECT * FROM agendamentos_temp where id = '$id_conta'");
}else{
	 $query = $pdo->query("SELECT * FROM agendamentos_temp where ref_pix = '$ref_pix'");
}
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	$cliente = $res[0]['cliente'];
	$servico = $res[0]['servico'];
	$funcionario = $res[0]['funcionario'];
	$data = $res[0]['data'];
	$hora = $res[0]['hora'];
	$obs = $res[0]['obs'];
	$data_lanc = $res[0]['data_lanc'];
	$usuario = $res[0]['usuario'];
	$status = $res[0]['status'];	
	$hash = $res[0]['hash'];
	$ref_pix = $res[0]['ref_pix'];
	$data_agd = $res[0]['data'];
	$hora_do_agd = $res[0]['hora'];
	$valor = $res[0]['valor_pago'];

	if($valor > 0){
		$valor_pago = $valor;
	}

	if(@$forma_pgto == "pix"){
		$forma_pgto = "Pix";
	}else{
		$forma_pgto = "Cartão de Crédito";
	}

	$query = $pdo->query("SELECT * FROM servicos where id = '$servico' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome_serv = @$res[0]['nome'];
$tempo = @$res[0]['tempo'];

$servico_conc = $nome_serv." (Site)";


        $query = $pdo->query("INSERT INTO agendamentos SET funcionario = '$funcionario', cliente = '$cliente', hora = '$hora', data = '$data', usuario = '0', status = 'Agendado', obs = '$obs', data_lanc = curDate(), servico = '$servico', hash = '$hash', ref_pix = '$ref_pix', valor_pago = '$valor_pago'");

        $ult_id = $pdo->lastInsertId();

        if($id_conta == "" || $statusPay){
         $pdo->query("INSERT INTO receber SET descricao = '$servico_conc', tipo = 'Serviço', valor = '$valor_pago', data_lanc = curDate(), data_venc = curDate(), data_pgto = curDate(), usuario_lanc = '0', usuario_baixa = '0', foto = 'sem-foto.jpg', pessoa = '$cliente', pago = 'Sim', servico = '$servico', funcionario = '$funcionario', obs = '', pgto = '$forma_pgto', referencia = '$ult_id', hora = curTime(), hora_alerta = '$hora_random'");  
        $id_receber = $pdo->lastInsertId();
     	}




$dataF = implode('/', array_reverse(explode('-', $data)));
$horaF = date("H:i", @strtotime($hora));  


$query = $pdo->query("SELECT * FROM usuarios where id = '$funcionario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$intervalo = @$res[0]['intervalo'];
$nome_func = @$res[0]['nome'];
$tel_func = @$res[0]['telefone'];

$hora_minutos = @strtotime("+$tempo minutes", @strtotime($hora));			
$hora_final_servico = date('H:i:s', $hora_minutos);


if($msg_agendamento == 'Api'){


$query = $pdo->query("SELECT * FROM clientes where id = '$cliente' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nome = $res[0]['nome'];
$telefone = $res[0]['telefone'];
$tel_cli = $res[0]['telefone'];


$dataF = implode('/', array_reverse(explode('-', $data)));
$horaF = date("H:i", @strtotime($hora));

$mensagem = "📅 _Novo Agendamento_ \n";
$mensagem .= "Profissional: *{$nome_func}* \n";
$mensagem .= "Serviço: *{$nome_serv}* \n";
$mensagem .= "Data: *{$dataF}* \n";
$mensagem .= "Hora: *{$horaF}* \n";
$mensagem .= "Cliente: *{$nome}* \n";
if($obs != ""){
	$mensagem .= "Obs: *{$obs}* \n";
}

$telefone = '55'.preg_replace('/[ ()-]+/' , '' , $telefone);

require('../ajax/api-texto.php');

if($tel_func != $whatsapp_sistema){
	$telefone = '55'.preg_replace('/[ ()-]+/' , '' , $tel_func);
	require('../ajax/api-texto.php');	
}


$telefone = '55'.preg_replace('/[ ()-]+/' , '' , $tel_cli);
//agendar o alerta de confirmação
$hora_atual = date('H:i:s');
$data_atual = date('Y-m-d');
$hora_minutos = @strtotime("-$minutos_aviso hours", @strtotime($hora));
$nova_hora = date('H:i:s', $hora_minutos);


		$mensagem = "*Confirmação de Agendamento*\n";
		$mensagem .= "Profissional: *{$nome_func}*\n";
		$mensagem .= "Serviço: *{$nome_serv}*\n";
		$mensagem .= "Data: *{$dataF}*\n";
		$mensagem .= "Hora: *{$horaF}*\n";
		$mensagem .= "_(Digite o número com a opção desejada)_\n";
		$mensagem .= "1. 1️⃣ para confirmar ✅\n";		
		$mensagem .= "2. 2️⃣ para Cancelar ❌\n";		
		$id_envio = "{$ult_id}";
		$data_envio = "{$data_agd} {$hora_do_agd}";		
		
		require("../ajax/confirmacao.php");
		$id_hash = "$id";		
		$pdo->query("UPDATE agendamentos SET hash = '$id_hash' WHERE id = '$ult_id'");


}


while (@strtotime($hora) < @strtotime($hora_final_servico)){
		
		$hora_minutos = @strtotime("+$intervalo minutes", @strtotime($hora));			
		$hora = date('H:i:s', $hora_minutos);

		if(@strtotime($hora) < @strtotime($hora_final_servico)){
			$query = $pdo->query("INSERT INTO horarios_agd SET agendamento = '$ult_id', horario = '$hora', funcionario = '$funcionario', data = '$data_agd'");
		}
	

}

    if($id_conta != "" && !$statusPay)
    {
    	$query = $pdo->query("DELETE FROM agendamentos_temp where id = '$id_conta'");
    	echo "<script>window.location='../meus-agendamentos.php'</script>";
    }
    elseif($statusPay)
    {
        $query = $pdo->query("DELETE FROM agendamentos_temp where ref_pix = '$ref_pix'");
        echo json_encode(array("status" => "pago", "novoId" => $id_receber));
        exit();
    }
    else
    {
    	$query = $pdo->query("DELETE FROM agendamentos_temp where ref_pix = '$ref_pix'");
    }



 ?>