<?php 
require_once("../../../conexao.php");
$tabela = 'receber';
@session_start();
$id_usuario = $_SESSION['id'];

/* ================= Helper: checar coluna (não quebra lógica) ================= */
function hasColumn(PDO $pdo, string $table, string $column): bool {
    try {
        $st = $pdo->prepare("SHOW COLUMNS FROM {$table} LIKE :c");
        $st->execute([':c' => $column]);
        return (bool)$st->fetch(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return false;
    }
}

/* ================= Campos originais (mantidos) ================= */
$id         = $_POST['id'];
$produto    = $_POST['produto'];
$valor      = $_POST['valor'];
$valor      = str_replace('.', '', $valor);
$valor      = str_replace(',', '.', $valor);
$pessoa     = $_POST['pessoa'];
$data_venc  = $_POST['data_venc'];
$data_pgto  = $_POST['data_pgto'];
$quantidade = $_POST['quantidade'];
$pgto       = $_POST['pgto'];

/* ===== NOVO: funcionário (comissão) vindo do formulário (opcional) ===== */
$funcionario_venda = (isset($_POST['funcionario_venda']) && $_POST['funcionario_venda'] !== '') ? (int)$_POST['funcionario_venda'] : null;

if($produto == 0){
    echo 'Cadastre um Produto e Depois selecione!';
    exit();
}

/* Produto + (NOVO) comissão do produto (se existir) */
$qProd = $pdo->query("SELECT * FROM produtos WHERE id = '$produto'");
$rProd = $qProd->fetchAll(PDO::FETCH_ASSOC);

$nome_produto = $rProd[0]['nome'];               // <<< guarda o nome do produto
$descricao    = 'Venda - ('.$quantidade.') '.$nome_produto;
$estoque      = $rProd[0]['estoque'];

$prod_tem_tipo  = array_key_exists('comissao_tipo',  $rProd[0]);
$prod_tem_valor = array_key_exists('comissao_valor', $rProd[0]);

$comissao_tipo_prod  = $prod_tem_tipo  ? (string)$rProd[0]['comissao_tipo']  : null;  // 'percent' | 'fixo'
$comissao_valor_prod = $prod_tem_valor ? (float)$rProd[0]['comissao_valor'] : null;

/* Calcula comissão total (se tiver funcionário e comissão configurada) */
$comissao_total = null;
if ($funcionario_venda && $comissao_tipo_prod !== null && $comissao_valor_prod !== null) {
    $valor_unitario_venda = (float)$rProd[0]['valor_venda'];
    if ($comissao_tipo_prod === 'percent') {
        $comissao_unit = $valor_unitario_venda * ($comissao_valor_prod / 100.0);
    } else {
        $comissao_unit = (float)$comissao_valor_prod; // fixo por unidade
    }
    $comissao_total = $comissao_unit * (int)$quantidade;
}

if($data_pgto != ''){
    $usuario_pgto = $id_usuario;
    $pago = 'Sim';
}else{
    $usuario_pgto = 0;
    $pago = 'Não';
}

if($quantidade > $estoque){
    echo 'Você não pode vendar mais do que você possui em estoque! Você tem '.$estoque.' produtos em estoque!';
    exit();
}

/* Atualiza estoque (mantido) */
$total_estoque = $estoque - $quantidade;
$pdo->query("UPDATE produtos SET estoque = '$total_estoque' WHERE id = '$produto'");

/* Foto (mantido) — usar variáveis próprias para não sobrescrever o resultado do produto */
$qRec = $pdo->query("SELECT * FROM $tabela WHERE id = '$id'");
$rRec = $qRec->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($rRec);
$foto  = $total_reg > 0 ? $rRec[0]['foto'] : 'sem-foto.jpg';

$nome_img    = date('d-m-Y H:i:s') .'-'.@$_FILES['foto']['name'];
$nome_img    = preg_replace('/[ :]+/' , '-' , $nome_img);
$caminho     = '../../img/contas/' .$nome_img;
$imagem_temp = @$_FILES['foto']['tmp_name']; 

if(@$_FILES['foto']['name'] != ""){
    $ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
    if(in_array($ext, ['png','jpg','jpeg','gif','pdf','rar','zip'])){ 
        if($foto != "sem-foto.jpg"){
            @unlink('../../img/contas/'.$foto);
        }
        $foto = $nome_img;
        move_uploaded_file($imagem_temp, $caminho);
    }else{
        echo 'Extensão de Imagem não permitida!';
        exit();
    }
}

/* Caixa aberto (mantido) */
$qCx   = $pdo->query("SELECT * FROM caixas WHERE operador = '$id_usuario' AND data_fechamento IS NULL ORDER BY id DESC LIMIT 1");
$rCx   = $qCx->fetchAll(PDO::FETCH_ASSOC);
$id_caixa = (@count($rCx) > 0) ? @$rCx[0]['id'] : 0;

/* ===== Checar colunas opcionais na tabela receber (não quebra lógica) ===== */
$rec_tem_func   = hasColumn($pdo, $tabela, 'funcionario_id');
$rec_tem_ctipo  = hasColumn($pdo, $tabela, 'comissao_tipo');
$rec_tem_cvalor = hasColumn($pdo, $tabela, 'comissao_valor');
$rec_tem_ctotal = hasColumn($pdo, $tabela, 'comissao_total');

/* Montagem dinâmica dos campos extras (quando existirem) */
$set_extra_update  = '';
$params_extra      = [];

if ($funcionario_venda && $rec_tem_func) {
    $set_extra_update  .= ", funcionario_id = :funcionario_id";
    $params_extra[':funcionario_id'] = $funcionario_venda;
}
if ($funcionario_venda && $rec_tem_ctipo && $comissao_tipo_prod !== null) {
    $set_extra_update  .= ", comissao_tipo = :comissao_tipo";
    $params_extra[':comissao_tipo'] = $comissao_tipo_prod;
}
if ($funcionario_venda && $rec_tem_cvalor && $comissao_valor_prod !== null) {
    $set_extra_update  .= ", comissao_valor = :comissao_valor";
    $params_extra[':comissao_valor'] = $comissao_valor_prod;
}
if ($funcionario_venda && $rec_tem_ctotal && $comissao_total !== null) {
    $set_extra_update  .= ", comissao_total = :comissao_total";
    $params_extra[':comissao_total'] = $comissao_total;
}

/* ===== INSERT / UPDATE (mantendo tua estrutura; só anexamos extras) ===== */
if($id == ""){
    // $hora_random segue como no seu original
    $sql = "INSERT INTO $tabela 
            SET descricao = :descricao,
                tipo = 'Venda',
                valor = :valor,
                data_lanc = CURDATE(),
                data_venc = '$data_venc',
                data_pgto = '$data_pgto',
                usuario_lanc = '$id_usuario',
                usuario_baixa = '$usuario_pgto',
                foto = '$foto',
                pessoa = '$pessoa',
                pago = '$pago',
                produto = '$produto',
                quantidade = '$quantidade',
                pgto = '$pgto',
                caixa = '$id_caixa',
                hora = CURTIME(),
                hora_alerta = '$hora_random'";

    if ($set_extra_update !== '') {
        $sql .= $set_extra_update;
    }

    $stmtVenda = $pdo->prepare($sql);

} else {
    $sql = "UPDATE $tabela 
            SET descricao = :descricao,
                valor     = :valor,
                data_venc = '$data_venc',
                data_pgto = '$data_pgto',
                foto      = '$foto',
                pessoa    = '$pessoa',
                produto   = '$produto',
                quantidade= '$quantidade'";

    if ($set_extra_update !== '') {
        $sql .= $set_extra_update;
    }

    $sql .= " WHERE id = '$id'";
    $stmtVenda = $pdo->prepare($sql);
}

/* binds originais */
$stmtVenda->bindValue(":descricao", $descricao);
$stmtVenda->bindValue(":valor",     $valor);

/* binds extras (se houver) */
if (!empty($params_extra)) {
    foreach ($params_extra as $k => $v) {
        $stmtVenda->bindValue($k, $v);
    }
}

$stmtVenda->execute();

/* =============== NOVO (mínimo): lançar comissão em PAGAR ================== */
if ($id == "" && $funcionario_venda && $comissao_total !== null && $comissao_total > 0) {
    $descricao_com = 'Comissão - '.$nome_produto; // <<< usa o nome salvo do produto
    $stmtCom = $pdo->prepare("
        INSERT INTO pagar SET
            descricao     = :desc,
            tipo          = 'Comissão',
            valor         = :valor,
            data_lanc     = CURDATE(),
            data_venc     = CURDATE(),
            data_pgto     = '0000-00-00',
            usuario_lanc  = :usu_lanc,
            usuario_baixa = 0,
            pago          = 'Não',
            funcionario   = :func,
            cliente       = :cliente,
            foto          = 'sem-foto.jpg'
    ");
    $stmtCom->bindValue(":desc",     $descricao_com);
    $stmtCom->bindValue(":valor",    number_format($comissao_total, 2, '.', ''));
    $stmtCom->bindValue(":usu_lanc", $id_usuario);
    $stmtCom->bindValue(":func",     $funcionario_venda);
    $stmtCom->bindValue(":cliente",  $pessoa);
    $stmtCom->execute();
}
/* ======================================================================== */

echo 'Salvo com Sucesso';
?>
