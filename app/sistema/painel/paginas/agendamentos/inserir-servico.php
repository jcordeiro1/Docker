<?php 
$tabela = 'receber';
require_once("../../../conexao.php");
require_once("funcoes-assinatura-combo.php");
$data_atual = date('Y-m-d');

@session_start();
$usuario_logado = @$_SESSION['id'];
$id_usuario = @$_SESSION['id'];

$cliente = $_POST['cliente_agd'];
$data_pgto = $_POST['data_pgto'];
$id_agd = @$_POST['id_agd'];
$valor_serv = $_POST['valor_serv_agd'];

if (strpos($valor_serv, ',') !== false) {
    $valor_serv = str_replace('.', '', $valor_serv);
    $valor_serv = str_replace(',', '.', $valor_serv);
}

$descricao = $_POST['descricao_serv_agd'];
$funcionario = $_POST['funcionario_agd'];
$servico = $_POST['servico_agd'];
$obs = $_POST['obs'];
$pgto = $_POST['pgto'];
$usar_combo = isset($_POST['usar_combo']) ? $_POST['usar_combo'] : 'Não';

$valor_serv_original = $_POST['valor_serv_agd'];

$valor_serv_restante = str_replace(',', '.', preg_replace('/\./', '', $_POST['valor_serv_agd_restante']));
$pgto_restante = $_POST['pgto_restante'];
$data_pgto_restante = $_POST['data_pgto_restante'];

$query = $pdo->query("SELECT * FROM receber where referencia = '$id_agd'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$agendamento_conta = @count($res);
$valor_recebido = @$res[0]['valor'];

$novo_valor_servico = $valor_recebido + $valor_serv;

if($valor_serv_restante == ""){
	$valor_serv_restante = 0;
}

$valor_total_servico = $valor_serv + $valor_serv_restante + $valor_recebido;

$query = $pdo->query("SELECT * FROM servicos where id = '$servico'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor = $res[0]['valor'];
$comissao = $res[0]['comissao'];
$descricao = $res[0]['nome'];
$descricao2 = 'Comissão - '.$res[0]['nome'];
$nome_servico = $res[0]['nome'];
$combo_ativo_servico = @$res[0]['combo_ativo'];
$combo_qtd_sessoes = (int)@$res[0]['combo_qtd_sessoes'];

$query = $pdo->query("SELECT * FROM usuarios where id = '$funcionario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$comissao_func = $res[0]['comissao'];

if($comissao_func > 0){
	$comissao = $comissao_func;
}

if($tipo_comissao == 'Porcentagem'){
	$valor_comissao = ($comissao * $valor_total_servico) / 100;
}else{
	$valor_comissao = $comissao;
}

$query = $pdo->query("SELECT * FROM formas_pgto where nome = '$pgto'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor_taxa = $res[0]['taxa'];

if($valor_taxa > 0 and strtotime($data_pgto) <=  strtotime($data_atual)){
	if($taxa_sistema == 'Cliente'){
		$valor_serv = $valor_serv + $valor_serv * ($valor_taxa / 100);
	}else{
		$valor_serv = $valor_serv - $valor_serv * ($valor_taxa / 100);
	}
}

$query = $pdo->query("SELECT * FROM formas_pgto where nome = '$pgto_restante'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor_taxa = @$res[0]['taxa'];

if($valor_taxa > 0 and $data_pgto_restante != '' and strtotime($data_pgto_restante) <=  strtotime($data_atual)){
	if($taxa_sistema == 'Cliente'){
		$valor_serv_restante = $valor_serv_restante + $valor_serv_restante * ($valor_taxa / 100);
	}else{
		$valor_serv_restante = $valor_serv_restante - $valor_serv_restante * ($valor_taxa / 100);
	}
}

//verificar caixa aberto
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if(@count($res1) > 0){
	$id_caixa = @$res1[0]['id'];
}else{
	$id_caixa = 0;
}

if(strtotime($data_pgto) <= strtotime($data_atual)){
	$pago = 'Sim';
	$data_pgto2 = $data_pgto;
	$usuario_baixa = $usuario_logado;
}else{
	$pago = 'Não';
	$data_pgto2 = '';
	$usuario_baixa = 0;
}

$hora_random = date('H:i:s');

if($valor_serv_restante > 0){
	if(strtotime($data_pgto_restante) <=  strtotime($data_atual)){
		$pago_restante = 'Sim';
		$data_pgto2_restante = $data_pgto_restante;
		$usuario_baixa_restante = $usuario_logado;
	}else{
		$pago_restante = 'Não';
		$data_pgto2_restante = '';
		$usuario_baixa_restante = 0;
	}

	//lançar o restante
	$pdo->query("INSERT INTO $tabela SET descricao = '$descricao', tipo = 'Serviço', valor = '$valor_serv_restante', data_lanc = curDate(), data_venc = '$data_pgto_restante', data_pgto = '$data_pgto2_restante', usuario_lanc = '$usuario_logado', usuario_baixa = '$usuario_baixa_restante', foto = 'sem-foto.jpg', pessoa = '$cliente', pago = '$pago_restante', servico = '$servico', funcionario = '$funcionario', obs = '$obs', pgto = '$pgto_restante', caixa = '$id_caixa', hora = curTime(), hora_alerta = '$hora_random'");	
}

$query2 = $pdo->query("SELECT * FROM servicos where id = '$servico'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$dias_retorno = $res2[0]['dias_retorno'];

//dados do cliente
$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$total_cartoes = $res2[0]['cartoes'];
$telefone = $res2[0]['telefone'];
$nome_cliente = $res2[0]['nome'];

// ====== BUSCAR LINK DE AVALIAÇÃO (GOOGLE) NO BANCO ======
$link_avaliacao_google = 'https://g.page/r/CW2oJnbXKDzREAE/review';

try {
	$stLink = $pdo->query("SELECT link_avaliacao_google 
						 FROM avaliacoes_site 
						 WHERE is_config = 1 
						 ORDER BY id DESC 
						 LIMIT 1");

	if ($stLink) {
		$rLink = $stLink->fetch(PDO::FETCH_ASSOC);
		if ($rLink && !empty($rLink['link_avaliacao_google'])) {
			$link_avaliacao_google = trim($rLink['link_avaliacao_google']);
		}
	}
} catch (Throwable $e) {
}

if($total_cartoes >= $quantidade_cartoes){
	$cartoes = 0;
}else{
	$cartoes = $total_cartoes + 1;
}

$data_retorno = date('Y-m-d', strtotime("+$dias_retorno days",strtotime($data_atual)));

$combo_usado = false;
$id_assinatura = 0;

if($usar_combo == 'Sim'){

	if($combo_ativo_servico != 'Sim'){
		echo 'Este serviço não está configurado como combo!';
		exit();
	}

	$assinatura = buscarAssinaturaComboAtiva($pdo, $cliente, $servico);

	if(!$assinatura){
		echo 'Cliente não possui assinatura ativa para este combo!';
		exit();
	}

	$id_assinatura = $assinatura['id'];

	$saldo = saldoAssinaturaCombo($pdo, $id_assinatura, $combo_qtd_sessoes);

	if($saldo['restantes'] <= 0){
		echo 'Este combo não possui sessões disponíveis!';
		exit();
	}

	$combo_usado = true;
}

if($combo_usado == false){
	if($valor_serv_original != 0){
		if($agendamento_conta == 0){
			$pdo->query("INSERT INTO $tabela SET descricao = '$descricao', tipo = 'Serviço', valor = '$valor_serv', data_lanc = curDate(), data_venc = '$data_pgto', data_pgto = '$data_pgto2', usuario_lanc = '$usuario_logado', usuario_baixa = '$usuario_baixa', foto = 'sem-foto.jpg', pessoa = '$cliente', pago = '$pago', servico = '$servico', funcionario = '$funcionario', obs = '$obs', pgto = '$pgto', caixa = '$id_caixa', hora = curTime(), hora_alerta = '$hora_random'");
		}else{
			$pdo->query("UPDATE $tabela SET valor = '$novo_valor_servico', data_pgto = curDate(), usuario_baixa = '$usuario_baixa', foto = 'sem-foto.jpg', pgto = '$pgto', caixa = '$id_caixa', hora = curTime() where referencia = '$id_agd'");
		}
	}
}else{
	$obs_combo = $obs;
	if($obs_combo != ''){
		$obs_combo .= ' / Pago via Combo';
	}else{
		$obs_combo = 'Pago via Combo';
	}

	$ok = baixarAssinaturaCombo($pdo, $id_assinatura, $cliente, $servico, $id_agd, $usuario_logado, $obs_combo);

	if($ok == false){
		echo 'Erro ao baixar sessão do combo!';
		exit();
	}
}

$ultimo_id = $pdo->lastInsertId();

$pdo->query("UPDATE agendamentos SET status = 'Concluído' where id = '$id_agd'");
$pdo->query("UPDATE clientes SET cartoes = '$cartoes', data_retorno = '$data_retorno', ultimo_servico = '$servico', alertado = 'Não' where id = '$cliente'");

if($combo_usado){
	$ultimo_id = 0;
}

echo 'Salvo com Sucesso*'.$ultimo_id; 

$telefone = '55'.preg_replace('/[ ()-]+/' , '' , $telefone);
if($msg_agendamento == 'Api'){

$mensagem  = 'Olá *'.$nome_cliente.'*! Tudo bem?%0A%0A';
$mensagem .= 'Aqui é o *'.$nome_sistema.'*. Queremos saber como foi sua experiência conosco — especialmente com o serviço de *'.$nome_servico.'*.%0A%0A';
$mensagem .= 'A sua opinião é muito importante para nós e nos ajuda a manter a qualidade e melhorar cada vez mais.%0A%0A';
$mensagem .= 'Se você gostou do atendimento, você poderia nos avaliar com *5 estrelas no Google*? ⭐⭐⭐⭐⭐%0A';
$mensagem .= '*Clique no link e deixe sua avaliação:*%0A'.$link_avaliacao_google.'%0A%0A';
$mensagem .= 'Desde já, muito obrigado pelo carinho e confiança.%0A';
$mensagem .= 'Será um prazer te receber novamente!%0A%0A';
$mensagem .= '— *'.$nome_sistema.'*';
$data_mensagem = $data_retorno.' 09:00';
	require('../../../../ajax/api-agendar.php');	
}
?>