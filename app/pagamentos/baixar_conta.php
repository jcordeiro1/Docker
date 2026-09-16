<?php
@session_start();
$id_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
if ($id_usuario == "") {
    $id_usuario = 0;
}

$data_pgto  = date('Y-m-d');
$forma_pgto = 'Pix';

// garante que $id veio definido
$id = isset($id) ? (int)$id : 0;
if ($id <= 0) {
    // nada pra fazer
    return;
}

// busca conta a receber
$query = $pdo->query("SELECT * FROM receber WHERE id = '$id' LIMIT 1");
$res   = $query->fetchAll(PDO::FETCH_ASSOC);

if (@count($res) == 0) {
    // conta não encontrada, evita notice
    return;
}

$funcionario      = $res[0]['funcionario'];
$servico          = $res[0]['servico'];
$cliente          = $res[0]['pessoa'];
$descricao        = 'ComissÃ£o - ' . $res[0]['descricao'];
$tipo             = $res[0]['tipo'];
$valor            = $res[0]['valor'];
$pgto             = $res[0]['pgto'];
$valor_serv       = $res[0]['valor'];
$frequencia       = $res[0]['frequencia'];
$dias_frequencia  = $res[0]['frequencia'];
$data_venc        = $res[0]['data_venc'];
$hash             = $res[0]['hash'];

// se tiver agendamento vinculado, remove
if ($hash != "") {
    require("agendar-delete.php");
}

// dados do cliente
$query2 = $pdo->query("SELECT * FROM clientes WHERE id = '$cliente' LIMIT 1");
$res2   = $query2->fetchAll(PDO::FETCH_ASSOC);

$telefone_cliente = (@count($res2) > 0) ? $res2[0]['telefone'] : '';
$nome_cliente     = (@count($res2) > 0) ? $res2[0]['nome']     : 'Cliente';

// formata valor para exibição
$valorF = number_format($valor, 2, ',', '.');

/* ==================== RECORRÊNCIA (mantida) ==================== */

if ($frequencia > 0) {

    if ($dias_frequencia == 30 || $dias_frequencia == 31) {
        $novo_vencimento = date('Y-m-d', strtotime("+1 month", strtotime($data_venc)));
    } else if ($dias_frequencia == 90) {
        $novo_vencimento = date('Y-m-d', strtotime("+3 month", strtotime($data_venc)));
    } else if ($dias_frequencia == 180) {
        $novo_vencimento = date('Y-m-d', strtotime("+6 month", strtotime($data_venc)));
    } else if ($dias_frequencia == 360 || $dias_frequencia == 365) {
        $novo_vencimento = date('Y-m-d', strtotime("+12 month", strtotime($data_venc)));
    } else {
        $novo_vencimento = date('Y-m-d', strtotime("+$dias_frequencia days", strtotime($data_venc)));
    }

    // se $hora_random não estiver definido no contexto, cria um padrão
    if (!isset($hora_random) || empty($hora_random)) {
        $hora_random = date('H:i:s');
    }

    $pdo->query("
        INSERT INTO receber 
        SET descricao   = 'Plano Assinatura',
            tipo        = 'Assinatura',
            valor       = '$valor',
            data_lanc   = CURDATE(),
            data_venc   = '$novo_vencimento',
            usuario_lanc= '$id_usuario',
            foto        = 'sem-foto.jpg',
            pessoa      = '$cliente',
            pago        = 'Não',
            hora        = CURTIME(),
            frequencia  = '$frequencia',
            hora_alerta = '$hora_random'
    ");

    // envio de WhatsApp da recorrência continua desativado (como já estava)
}

/* ==================== CAIXA (mantido) ==================== */

// verifica se há caixa aberto para esse operador
$query1 = $pdo->query("
    SELECT * 
    FROM caixas 
    WHERE operador = '$id_usuario' 
      AND data_fechamento IS NULL 
    ORDER BY id DESC 
    LIMIT 1
");
$res1     = $query1->fetchAll(PDO::FETCH_ASSOC);
$id_caixa = (@count($res1) > 0) ? $res1[0]['id'] : 0;

/* ==================== BAIXA DA CONTA (mantida) ==================== */

$pdo->query("
    UPDATE receber 
       SET pgto         = '$forma_pgto',
           valor        = '$valor_serv',
           pago         = 'Sim',
           usuario_baixa= '$id_usuario',
           data_pgto    = '$data_pgto',
           caixa        = '$id_caixa',
           hora         = CURTIME()
     WHERE id = '$id'
");

/* ==================== RECIBO POR WHATSAPP ==================== */

if (!empty($telefone_cliente)) {

    // normaliza telefone
    $telefone = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);

    // se $url_sistema já estiver definido em outro include, usa ele;
    // senão, cai no padrão abaixo
    if (empty($url_sistema)) {
        $url_sistema = '<?php echo $url_sistema ?>';
    }

    $link_recibo = $url_sistema . '/sistema/painel/rel/recibo_class.php?id=' . $id;

    // monta texto do recibo
    $recibo  = "💳 *{$nome_sistema}*%0A";
    $recibo .= "✅ _Pagamento Confirmado_%0A";
    $recibo .= "-------------------------------%0A";
    $recibo .= "*Recibo de Pagamento*%0A";
    $recibo .= "-------------------------------%0A";
    $recibo .= "*Cliente:* {$nome_cliente}%0A";
    $recibo .= "*Descrição:* {$descricao}%0A";
    $recibo .= "*Valor:* R$ {$valorF}%0A";
    $recibo .= "*Data de Pagamento:* " . date('d/m/Y') . "%0A";
    $recibo .= "*Forma de Pagamento:* {$forma_pgto}%0A";
    $recibo .= "-------------------------------%0A";
    $recibo .= "Agradecemos pela preferência!%0A";
    $recibo .= "📄 *Recibo:*%0A{$link_recibo}";

    $mensagem = $recibo;

    // usa o mesmo endpoint de WhatsApp já usado pelo sistema
    require('../ajax/api-texto.php');
}

?>
