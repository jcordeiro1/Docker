<?php 
require_once("../../../conexao.php");
$tabela = 'receber';
@session_start();
$id_usuario = $_SESSION['id'];

$data_atual = date('Y-m-d');

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$funcionario = $res[0]['funcionario'];
$servico = $res[0]['servico'];
$cliente = $res[0]['pessoa'];
$descricao = 'Comissão - '.$res[0]['descricao'];
$valor_conta = $res[0]['valor'];
$pgto = $res[0]['pgto'];

$query = $pdo->query("SELECT * FROM servicos where id = '$servico'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor = $res[0]['valor'];
$comissao = $res[0]['comissao'];
$dias_retorno = $res[0]['dias_retorno'];
$nome_servico = $res[0]['nome'];

$data_retorno = date('Y-m-d', strtotime("+$dias_retorno days",strtotime($data_atual)));

$query = $pdo->query("SELECT * FROM usuarios where id = '$funcionario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$comissao_func = $res[0]['comissao'];

if($comissao_func > 0){
	$comissao = $comissao_func;
}

if($tipo_comissao == 'Porcentagem'){
	$valor_comissao = ($comissao * $valor_conta) / 100;
}else{
	$valor_comissao = $comissao;
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


//verificar caixa aberto
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if(@count($res1) > 0){
	$id_caixa = @$res1[0]['id'];
}else{
	$id_caixa = 0;
}

$pdo->query("UPDATE $tabela SET pago = 'Sim', usuario_baixa = '$id_usuario', data_pgto = curDate(), caixa = '$id_caixa', hora = curTime() where id = '$id'");


if($lanc_comissao != 'Sempre'){
//lançar a conta a pagar para a comissão do funcionário
$pdo->query("INSERT INTO pagar SET descricao = '$descricao', tipo = 'Comissão', valor = '$valor_comissao', data_lanc = curDate(), data_venc = curDate(), usuario_lanc = '$id_usuario', foto = 'sem-foto.jpg', pago = 'Não', funcionario = '$funcionario', servico = '$servico', cliente = '$cliente', caixa = '$id_caixa', hora = curTime(), hora_alerta = '$hora_random'");
}

echo 'Baixado com Sucesso';


//dados do cliente
$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente' order by id desc limit 2");
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


$query = $pdo->query("SELECT * FROM agendamentos where cliente = '$cliente'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
for($i=0; $i < $total_reg; $i++){
$hash = $res[$i]['hash'];
if($hash != ""){
	require('../../../../ajax/api-excluir.php');
}
}
}

$telefone = '55'.preg_replace('/[ ()-]+/' , '' , $telefone);
if($msg_agendamento == 'Api'){

// agendar mensagem de retorno
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