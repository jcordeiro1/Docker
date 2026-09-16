<?php
$tabela = 'receber';
require_once("../../../conexao.php");
$data_atual = date('Y-m-d');

@session_start();
$usuario_logado = @$_SESSION['id'];
$id_usuario     = @$_SESSION['id'];

$cliente    = $_POST['cliente'];
$data_pgto  = $_POST['data_pgto'];
$id         = @$_POST['id'];
$valor_serv = $_POST['valor_serv'];
$valor_serv = str_replace('.', '', $valor_serv);
$valor_serv = str_replace(',', '.', $valor_serv);

$funcionario = $_POST['funcionario'];
$servico     = $_POST['servico'];
$obs         = $_POST['obs'];
$pgto        = @$_POST['pgto'];        // forma do 1º pagamento
$forma_pgto  = @$_POST['pgto'];

$valor_serv_restante = $_POST['valor_serv_agd_restante'];
$valor_serv_restante = str_replace('.', '', $valor_serv_restante);
$valor_serv_restante = str_replace(',', '.', $valor_serv_restante);
$pgto_restante       = $_POST['pgto_restante'];
$data_pgto_restante  = $_POST['data_pgto_restante'];

if ($valor_serv_restante == "") { $valor_serv_restante = 0; }

$valor_total_servico = $valor_serv + $valor_serv_restante;

/* --------- dados do serviço / comissão --------- */
$query  = $pdo->query("SELECT * FROM servicos WHERE id = '$servico'");
$res    = $query->fetchAll(PDO::FETCH_ASSOC);
$valor  = $res[0]['valor'];
$comissao      = $res[0]['comissao'];
$descricao     = $res[0]['nome'];
$descricao2    = 'Comissão - '.$res[0]['nome'];
$dias_retorno  = $res[0]['dias_retorno'];
$data_retorno  = date('Y-m-d', strtotime("+$dias_retorno days", strtotime($data_atual)));
$nome_servico  = $res[0]['nome'];

/* --------- dados do cliente --------- */
$query2 = $pdo->query("SELECT * FROM clientes WHERE id = '$cliente' ORDER BY id DESC LIMIT 2");
$res2   = $query2->fetchAll(PDO::FETCH_ASSOC);
$telefone     = $res2[0]['telefone'];
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

/* --------- comissão do funcionário --------- */
$query = $pdo->query("SELECT * FROM usuarios WHERE id = '$funcionario'");
$res   = $query->fetchAll(PDO::FETCH_ASSOC);
$comissao_func = $res[0]['comissao'];
if ($comissao_func > 0) { $comissao = $comissao_func; }

if ($tipo_comissao == 'Porcentagem') {
    $valor_comissao = ($comissao * $valor_total_servico) / 100;
} else {
    $valor_comissao = $comissao;
}

/* --------- taxas (1ª forma) --------- */
$query = $pdo->query("SELECT * FROM formas_pgto WHERE nome = '$pgto'");
$res   = $query->fetchAll(PDO::FETCH_ASSOC);
$valor_taxa = $res[0]['taxa'];

if ($valor_taxa > 0 && strtotime($data_pgto) <= strtotime($data_atual)) {
    if ($taxa_sistema == 'Cliente') {
        $valor_serv = $valor_serv + $valor_serv * ($valor_taxa / 100);
    } else {
        $valor_serv = $valor_serv - $valor_serv * ($valor_taxa / 100);
    }
}

/* --------- taxas (restante) --------- */
$query = $pdo->query("SELECT * FROM formas_pgto WHERE nome = '$pgto_restante'");
$res   = $query->fetchAll(PDO::FETCH_ASSOC);
$valor_taxa = @$res[0]['taxa'];

if ($valor_taxa > 0 && strtotime($data_pgto_restante) <= strtotime($data_atual)) {
    if ($taxa_sistema == 'Cliente') {
        $valor_serv_restante = $valor_serv_restante + $valor_serv_restante * ($valor_taxa / 100);
    } else {
        $valor_serv_restante = $valor_serv_restante - $valor_serv_restante * ($valor_taxa / 100);
    }
}

/* --------- caixa aberto --------- */
$query1 = $pdo->query("SELECT * FROM caixas WHERE operador = '$id_usuario' AND data_fechamento IS NULL ORDER BY id DESC LIMIT 1");
$res1   = $query1->fetchAll(PDO::FETCH_ASSOC);
$id_caixa = (@count($res1) > 0) ? @$res1[0]['id'] : 0;

/* --------- define situação da 1ª forma --------- */
if (strtotime($data_pgto) <= strtotime($data_atual)) {
    $pago          = 'Sim';
    $data_pgto2    = $data_pgto;
    $usuario_baixa = $usuario_logado;

    // lançar conta a pagar (comissão)
    $pdo->query("INSERT INTO pagar SET descricao = '$descricao2', tipo = 'Comissão', valor = '$valor_comissao', data_lanc = '$data_pgto', data_venc = '$data_pgto', usuario_lanc = '$usuario_logado', foto = 'sem-foto.jpg', pago = 'Não', funcionario = '$funcionario', servico = '$servico', cliente = '$cliente', caixa = '$id_caixa', hora = curTime(), hora_alerta = '$hora_random'");

    // agendar mensagem de retorno (mantido)
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
} else {
    $pago          = 'Não';
    $data_pgto2    = '';
    $usuario_baixa = 0;

    if ($lanc_comissao == 'Sempre') {
        $pdo->query("INSERT INTO pagar SET descricao = '$descricao2', tipo = 'Comissão', valor = '$valor_comissao', data_lanc = '$data_pgto', data_venc = '$data_pgto', usuario_lanc = '$usuario_logado', foto = 'sem-foto.jpg', pago = 'Não', funcionario = '$funcionario', servico = '$servico', cliente = '$cliente', caixa = '$id_caixa', hora = curTime(), hora_alerta = '$hora_random'");
    }
}

/* ===== 1) INSERE A PRIMEIRA PARTE (principal) ===== */
$pdo->query("INSERT INTO $tabela 
    SET descricao = '$nome_servico', tipo = 'Serviço', valor = '$valor_serv', 
        data_lanc = CURDATE(), data_venc = '$data_pgto', data_pgto = '$data_pgto2', 
        usuario_lanc = '$usuario_logado', usuario_baixa = '$usuario_baixa', 
        foto = 'sem-foto.jpg', pessoa = '$cliente', pago = '$pago', 
        servico = '$servico', funcionario = '$funcionario', obs = '$obs', 
        pgto = '$pgto', caixa = '$id_caixa', hora = CURTIME()");
$ultimo_id = $pdo->lastInsertId();

/* marca o próprio id como grupo (chave do recibo) */
$pdo->query("UPDATE $tabela SET grupo = '$ultimo_id' WHERE id = '$ultimo_id'");

/* ===== 2) SE HOUVER RESTANTE, INSERE VINCULANDO NO MESMO GRUPO ===== */
if ($valor_serv_restante > 0) {
    if (strtotime($data_pgto_restante) <= strtotime($data_atual)) {
        $pago_restante          = 'Sim';
        $data_pgto2_restante    = $data_pgto_restante;
        $usuario_baixa_restante = $usuario_logado;
    } else {
        $pago_restante          = 'Não';
        $data_pgto2_restante    = '';
        $usuario_baixa_restante = 0;
    }

    $pdo->query("INSERT INTO $tabela 
        SET descricao = '$nome_servico', tipo = 'Serviço', valor = '$valor_serv_restante', 
            data_lanc = CURDATE(), data_venc = '$data_pgto_restante', data_pgto = '$data_pgto2_restante', 
            usuario_lanc = '$usuario_logado', usuario_baixa = '$usuario_baixa_restante', 
            foto = 'sem-foto.jpg', pessoa = '$cliente', pago = '$pago_restante', 
            servico = '$servico', funcionario = '$funcionario', obs = '$obs', 
            pgto = '$pgto_restante', caixa = '$id_caixa', hora = CURTIME(), 
            grupo = '$ultimo_id'");
}

/* atualiza fidelidade do cliente (mantido) */
$query2 = $pdo->query("SELECT * FROM servicos WHERE id = '$servico'");
$res2   = $query2->fetchAll(PDO::FETCH_ASSOC);
$dias_retorno = $res2[0]['dias_retorno'];
$nome_servico = $res2[0]['nome'];

$query2 = $pdo->query("SELECT * FROM clientes WHERE id = '$cliente'");
$res2   = $query2->fetchAll(PDO::FETCH_ASSOC);
$total_cartoes = $res2[0]['cartoes'];
$cartoes       = $total_cartoes + 1;
$data_retorno  = date('Y-m-d', strtotime("+$dias_retorno days", strtotime($data_atual)));

$pdo->query("UPDATE clientes 
                SET cartoes = '$cartoes', data_retorno = '$data_retorno', 
                    ultimo_servico = '$servico', alertado = 'Não' 
              WHERE id = '$cliente'");

/* retorna id do grupo para impressão do recibo */
echo 'Salvo com Sucesso*' . $ultimo_id;
?>
