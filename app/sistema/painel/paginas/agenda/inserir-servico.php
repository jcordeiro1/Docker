<?php 
$tabela = 'receber';
require_once("../../../conexao.php");
$data_atual = date('Y-m-d');

if(@$_POST['id_usuario'] != ""){
	$usuario_logado = $_POST['id_usuario'];
}else{
	@session_start();
$usuario_logado = @$_SESSION['id'];
}

$id_usuario = $usuario_logado;

$cliente = $_POST['cliente_agd'];
$data_pgto = $_POST['data_pgto'];
$id_agd = @$_POST['id_agd'];
$valor_serv = $_POST['valor_serv_agd'];
$descricao = $_POST['descricao_serv_agd'];
$funcionario = $_POST['funcionario_agd'];
$servico = $_POST['servico_agd'];
$obs = $_POST['obs'];
$pgto = $_POST['pgto'];

$valor_serv_restante = $_POST['valor_serv_agd_restante'];
$pgto_restante = $_POST['pgto_restante'];
$data_pgto_restante = $_POST['data_pgto_restante'];

$valor_serv_original = $_POST['valor_serv_agd'];

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

if($valor_taxa > 0 and strtotime($data_pgto_restante) <=  strtotime($data_atual)){
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


if(strtotime($data_pgto) <=  strtotime($data_atual)){
	$pago = 'Sim';
	$data_pgto2 = $data_pgto;
	$usuario_baixa = $usuario_logado;


	//lançar a conta a pagar para a comissão do funcionário
	$pdo->query("INSERT INTO pagar SET descricao = '$descricao2', tipo = 'Comissão', valor = '$valor_comissao', data_lanc = '$data_pgto', data_venc = '$data_pgto', usuario_lanc = '$usuario_logado', foto = 'sem-foto.jpg', pago = 'Não', funcionario = '$funcionario', servico = '$servico', cliente = '$cliente'");

}else{
	$pago = 'Não';
	$data_pgto2 = '';
	$usuario_baixa = 0;

	if($lanc_comissao == 'Sempre'){
		//lançar a conta a pagar para a comissão do funcionário
	$pdo->query("INSERT INTO pagar SET descricao = '$descricao2', tipo = 'Comissão', valor = '$valor_comissao', data_lanc = '$data_pgto', data_venc = '$data_pgto', usuario_lanc = '$usuario_logado', foto = 'sem-foto.jpg', pago = 'Não', funcionario = '$funcionario', servico = '$servico', cliente = '$cliente', caixa = '$id_caixa', hora = curTime()");
	}
}

if($valor_serv_restante > 0){
if(strtotime($data_pgto_restante) <=  strtotime($data_atual)){
	$pago_restante = 'Sim';
	$data_pgto2_restante = $data_pgto;
	$usuario_baixa_restante = $usuario_logado;
}else{
	$pago_restante = 'Não';
	$data_pgto2_restante = '';
	$usuario_baixa_restante = 0;
}

//lançar o restante
$pdo->query("INSERT INTO $tabela SET descricao = '$descricao', tipo = 'Serviço', valor = '$valor_serv_restante', data_lanc = curDate(), data_venc = '$data_pgto_restante', data_pgto = '$data_pgto2_restante', usuario_lanc = '$usuario_logado', usuario_baixa = '$usuario_baixa', foto = 'sem-foto.jpg', pessoa = '$cliente', pago = '$pago_restante', servico = '$servico', funcionario = '$funcionario', obs = '$obs', pgto = '$pgto_restante', caixa = '$id_caixa', hora = curTime(), hora_alerta = '$hora_random'");	
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
$link_avaliacao_google = 'https://g.page/r/CW2oJnbXKDzREAE/review'; // fallback se não tiver salvo

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
  // se der erro ou não existir coluna ainda, usa fallback
}


if($total_cartoes >= $quantidade_cartoes){
	$cartoes = 0;
}else{
	$cartoes = $total_cartoes + 1;
}

$data_retorno = date('Y-m-d', strtotime("+$dias_retorno days",strtotime($data_atual)));


if($valor_serv_original != 0){
	if($agendamento_conta == 0){
$pdo->query("INSERT INTO $tabela SET descricao = '$descricao', tipo = 'Serviço', valor = '$valor_serv', data_lanc = curDate(), data_venc = '$data_pgto', data_pgto = '$data_pgto2', usuario_lanc = '$usuario_logado', usuario_baixa = '$usuario_baixa', foto = 'sem-foto.jpg', pessoa = '$cliente', pago = '$pago', servico = '$servico', funcionario = '$funcionario', obs = '$obs', pgto = '$pgto', caixa = '$id_caixa', hora = curTime(), hora_alerta = '$hora_random'");
		}else{
			$pdo->query("UPDATE $tabela SET valor = '$novo_valor_servico', data_pgto = curDate(), usuario_baixa = '$usuario_baixa', foto = 'sem-foto.jpg', pgto = '$pgto' where referencia = '$id_agd'");
		}
}


$pdo->query("UPDATE agendamentos SET status = 'Concluído' where id = '$id_agd'");
$pdo->query("UPDATE clientes SET cartoes = '$cartoes', data_retorno = '$data_retorno', ultimo_servico = '$servico', alertado = 'Não' where id = '$cliente'");

echo 'Salvo com Sucesso'; 



$telefone = '55'.preg_replace('/[ ()-]+/' , '' , $telefone);
if($msg_agendamento == 'Api'){
// agendar mensagem de retorno (mais convincente)
$mensagem  = 'Olá *'.$nome_cliente.'*! Tudo bem? 😊%0A%0A';
$mensagem .= 'Aqui é o *'.$nome_sistema.'*. Queremos saber como foi sua experiência — principalmente com o serviço de *'.$nome_servico.'*.%0A%0A';
$mensagem .= 'Se você gostou do atendimento, você pode nos dar uma força agora? 🙏%0A';
$mensagem .= 'Uma avaliação de *5 estrelas* no Google ajuda MUITO o nosso trabalho a alcançar mais pessoas (leva *menos de 30 segundos*). ⭐⭐⭐⭐⭐%0A%0A';
$mensagem .= '*É só clicar no link abaixo e tocar em 5 estrelas:*%0A';
$mensagem .= $link_avaliacao_google.'%0A%0A';
$mensagem .= 'Obrigado de coração pela confiança, *'.$nome_cliente.'*!%0A';
$mensagem .= 'Vai ser um prazer te receber novamente.%0A%0A';
$mensagem .= '— *'.$nome_sistema.'*';

$data_mensagem = $data_retorno.' 09:00';
	require('../../../../ajax/api-agendar.php');	
}

?>