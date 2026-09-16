<?php 
require_once("../../../conexao.php");
$tabela = 'pagar';
@session_start();
$id_usuario = $_SESSION['id'];

$id         = $_POST['id'];
$descricao  = isset($_POST['descricao']) ? $_POST['descricao'] : '';
$valor      = $_POST['valor'];
$valor      = str_replace('.', '', $valor);
$valor      = str_replace(',', '.', $valor);
$pessoa     = @$_POST['pessoa'];
$data_venc  = $_POST['data_venc'];
$data_pgto  = $_POST['data_pgto'];
$funcionario= @$_POST['funcionario'];
$forma_pgto = @$_POST['pgto'];

/* ===== Helpers seguros, sem alterar tua lógica de gravação ===== */
function hasColumn(PDO $pdo, string $table, string $col): bool {
  try {
    $st = $pdo->prepare("SHOW COLUMNS FROM {$table} LIKE :c");
    $st->execute([':c'=>$col]);
    return (bool)$st->fetch(PDO::FETCH_ASSOC);
  } catch(Throwable $e){ return false; }
}

function getNomeItem(PDO $pdo, ?int $prodId, ?int $servId): string {
  if ($prodId && $prodId > 0) {
    $q = $pdo->query("SELECT nome FROM produtos WHERE id = '$prodId' LIMIT 1");
    $r = $q->fetch(PDO::FETCH_ASSOC);
    if ($r && !empty($r['nome'])) return $r['nome'];
  }
  if ($servId && $servId > 0) {
    $q = $pdo->query("SELECT nome FROM servicos WHERE id = '$servId' LIMIT 1");
    $r = $q->fetch(PDO::FETCH_ASSOC);
    if ($r && !empty($r['nome'])) return $r['nome'];
  }
  return '';
}

/* ===== Tenta descobrir o item para completar a descrição ===== */
$produto_id = isset($_POST['produto']) ? (int)$_POST['produto'] : 0;
$servico_id = isset($_POST['servico']) ? (int)$_POST['servico'] : 0;

/* também aceita nomes diretos, se o form enviar */
$produto_nome_post = isset($_POST['produto_nome']) ? trim($_POST['produto_nome']) : '';
$servico_nome_post = isset($_POST['servico_nome']) ? trim($_POST['servico_nome']) : '';

/* se for edição e ainda não temos IDs, tenta puxar da própria linha do pagar */
if (! $produto_id && ! $servico_id && $id) {
  try {
    $prodCol = hasColumn($pdo, $tabela, 'produto');
    $servCol = hasColumn($pdo, $tabela, 'servico');
    if ($prodCol || $servCol) {
      $q = $pdo->query("SELECT ".($prodCol?'produto':'0')." AS produto, ".($servCol?'servico':'0')." AS servico FROM $tabela WHERE id = '$id' LIMIT 1");
      $r = $q->fetch(PDO::FETCH_ASSOC);
      if ($r) {
        if ($prodCol && ! $produto_id) $produto_id = (int)$r['produto'];
        if ($servCol && ! $servico_id) $servico_id = (int)$r['servico'];
      }
    }
  } catch(Throwable $e){}
}

/* Monta o nome do item por prioridade: nome enviado > lookup por id */
$nome_item = '';
if ($produto_nome_post !== '') $nome_item = $produto_nome_post;
if ($nome_item === '' && $servico_nome_post !== '') $nome_item = $servico_nome_post;
if ($nome_item === '') $nome_item = getNomeItem($pdo, $produto_id, $servico_id);

/* Se a descrição for “Comissão”/“Comissao” (com ou sem “-” e sem conteúdo depois), completa com o nome */
if ($nome_item !== '') {
  $desc_trim = trim($descricao);
  $ehComissaoSimples = preg_match('/^Comiss(ão|ao)\s*$/i', $desc_trim);
  $ehComissaoVazia   = preg_match('/^Comiss(ão|ao)\s*-\s*$/i', $desc_trim);

  if ($ehComissaoSimples || $ehComissaoVazia) {
    $descricao = 'Comissão - ' . $nome_item;
  } else if (stripos($desc_trim, 'Comissão -') === 0 || stripos($desc_trim, 'Comissao -') === 0) {
    // Se começa com "Comissão -" e não tem nada depois do hífen, completa
    $partes  = explode('-', $desc_trim, 2);
    $posfixo = isset($partes[1]) ? trim($partes[1]) : '';
    if ($posfixo === '') {
      $descricao = 'Comissão - ' . $nome_item;
    }
  }
}
/* ===== Fim do bloco de complemento de descrição ===== */

if($descricao == ""){
  echo 'Insira uma descrição!';
  exit();
}

if($funcionario == ""){
  $funcionario = 0;
}

if($pessoa == ""){
  $pessoa = 0;
}

if($data_pgto != ''){
  $usuario_pgto = $id_usuario;
  $pago = 'Sim';
  $pgto = " ,data_pgto = '$data_pgto'";
}else{
  $usuario_pgto = 0;
  $pago = 'Não';
  $pgto = "";
}

/* validar troca da foto (inalterado) */
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
  $foto = $res[0]['foto'];
}else{
  $foto = 'sem-foto.jpg';
}

/* upload da foto (inalterado) */
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);
$caminho = '../../img/contas/' .$nome_img;
$imagem_temp = @$_FILES['foto']['tmp_name']; 

if(@$_FILES['foto']['name'] != ""){
  $ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
  if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'pdf' or $ext == 'rar' or $ext == 'zip'){ 
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

if($funcionario == ""){
  $funcionario = 0;
}

/* caixa aberto (inalterado) */
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if(@count($res1) > 0){
  $id_caixa = @$res1[0]['id'];
}else{
  $id_caixa = 0;
}

/* gravação (inalterada) */
if($id == ""){
  $query = $pdo->prepare("INSERT INTO $tabela SET descricao = :descricao, tipo = 'Conta', valor = :valor, data_lanc = curDate(), data_venc = '$data_venc',  usuario_lanc = '$id_usuario', usuario_baixa = '$usuario_pgto', foto = '$foto', pessoa = '$pessoa', pago = '$pago', funcionario = '$funcionario', caixa = '$id_caixa', hora = curTime(), pgto = '$forma_pgto' $pgto, hora_alerta = '$hora_random'");
}else{
  $query = $pdo->prepare("UPDATE $tabela SET descricao = :descricao, valor = :valor, data_venc = '$data_venc', data_pgto = '$data_pgto', foto = '$foto', pessoa = '$pessoa', funcionario = '$funcionario', pgto = '$forma_pgto', caixa = '$id_caixa', hora = curTime() WHERE id = '$id'");
}

$query->bindValue(":descricao", "$descricao");
$query->bindValue(":valor", "$valor");
$query->execute();
$ultima_conta = $pdo->lastInsertId();

echo 'Salvo com Sucesso';

$data_vencF = implode('/', array_reverse(explode('-', $data_venc)));
$valorF = @number_format($valor, 2, '.', '');
$valorF = @number_format($valor, 2, ',', '.');

?>
