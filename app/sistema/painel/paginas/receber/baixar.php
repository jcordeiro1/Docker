<?php 
require_once("../../../conexao.php");
$tabela = 'receber';
@session_start();
$id_usuario = $_SESSION['id'];


$id = $_POST['id'];
$valor = $_POST['valor'];
$valor = str_replace(',', '.', $valor);
$data_pgto = $_POST['data_pgto'];
$forma_pgto = $_POST['pgto'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$funcionario = $res[0]['funcionario'];
$servico = $res[0]['servico'];
$cliente = $res[0]['pessoa'];
$descricao = 'Comissão - '.$res[0]['descricao'];
$tipo = $res[0]['tipo'];
$pgto = $res[0]['pgto'];
$valor_serv = $res[0]['valor'];
$frequencia = $res[0]['frequencia'];
$dias_frequencia = $res[0]['frequencia'];
$data_venc = $res[0]['data_venc'];
$hash = $res[0]['hash'];
$descricao_antiga = $res[0]['descricao'];



$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$telefone_cliente = @$res2[0]['telefone'];
$nome_cliente = @$res2[0]['nome'];

$valorF = number_format($valor, 2, ',', '.');

if($frequencia > 0){
		if($dias_frequencia == 30 || $dias_frequencia == 31){			
			$novo_vencimento = date('Y-m-d', @strtotime("+1 month",@strtotime($data_venc)));
		}else if($dias_frequencia == 90){			
			$novo_vencimento = date('Y-m-d', @strtotime("+3 month",@strtotime($data_venc)));
		}else if($dias_frequencia == 180){ 
			$novo_vencimento = date('Y-m-d', @strtotime("6 month",@strtotime($data_venc)));
		}else if($dias_frequencia == 360 || $dias_frequencia == 365){ 			
			$novo_vencimento = date('Y-m-d', @strtotime("+12 month",@strtotime($data_venc)));

		}else{			
			$novo_vencimento = date('Y-m-d', @strtotime("+$dias_frequencia days",@strtotime($data_venc)));
		}

		$novo_vencimentoF = implode('/', array_reverse(@explode('-', $novo_vencimento)));


		$pdo->query("INSERT INTO receber SET descricao = '$descricao_antiga', tipo = 'Assinatura', valor = '$valor', data_lanc = curDate(), data_venc = '$novo_vencimento', usuario_lanc = '$id_usuario', foto = 'sem-foto.jpg', pessoa = '$cliente', pago = 'Não', hora = curTime(), frequencia = '$frequencia', hora_alerta = '$hora_random', servico = '$servico', pgto = '$pgto'");
		
		$ultima_conta = $pdo->lastInsertId();

		

}


//verificar caixa aberto
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if(@count($res1) > 0){
	$id_caixa = @$res1[0]['id'];
}else{
	$id_caixa = 0;
}


if($tipo == 'Serviço'){
	$query = $pdo->query("SELECT * FROM servicos where id = '$servico'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$valor = $res[0]['valor'];
	$comissao = $res[0]['comissao'];

	if($tipo_comissao == 'Porcentagem'){
		$valor_comissao = ($comissao * $valor_serv) / 100;
	}else{
		$valor_comissao = $comissao;
	}

if($lanc_comissao != 'Sempre'){
	//lançar a conta a pagar para a comissão do funcionário
$pdo->query("INSERT INTO pagar SET descricao = '$descricao', tipo = 'Comissão', valor = '$valor_comissao', data_lanc = curDate(), data_venc = curDate(), usuario_lanc = '$id_usuario', foto = 'sem-foto.jpg', pago = 'Não', funcionario = '$funcionario', servico = '$servico', cliente = '$cliente', caixa = '$id_caixa', hora = curTime(), hora_alerta = '$hora_random' ");
}
}

$query = $pdo->query("SELECT * FROM formas_pgto where nome = '$pgto'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor_taxa = @$res[0]['taxa'];

if($valor_taxa > 0){
	if($taxa_sistema == 'Cliente'){
		$valor_serv = $valor_serv + $valor_serv * ($valor_taxa / 100);
	}else{
		$valor_serv = $valor_serv - $valor_serv * ($valor_taxa / 100);
	}
	
}


$pdo->query("UPDATE $tabela SET pgto = '$forma_pgto', valor = '$valor', pago = 'Sim', usuario_baixa = '$id_usuario', data_pgto = '$data_pgto', valor = '$valor_serv', caixa = '$id_caixa', hora = curTime() where id = '$id'");

echo 'Baixado com Sucesso';
 ?>