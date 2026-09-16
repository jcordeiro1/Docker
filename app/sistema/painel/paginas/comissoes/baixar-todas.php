<?php
require_once("../../../conexao.php");
$tabela = 'pagar';
@session_start();
$id_usuario = $_SESSION['id'];

// ----- ENTRADAS -----
$dataInicial = isset($_POST['data_inicial']) ? $_POST['data_inicial'] : '';
$dataFinal   = isset($_POST['data_final'])   ? $_POST['data_final']   : '';
$funcionario = isset($_POST['id_funcionario']) ? $_POST['id_funcionario'] : '';
$pgto        = isset($_POST['pgto']) ? $_POST['pgto'] : '';

// Defaults seguros (mesmo dia) se vierem vazios
if ($dataInicial === '') $dataInicial = date('Y-m-d');
if ($dataFinal   === '') $dataFinal   = date('Y-m-d');

// Se não tiver funcionário, não faz nada (mesmo comportamento da tela)
if ($funcionario === '') {
  echo 'Selecione um Funcionário';
  exit;
}

// ----- CAIXA ABERTO -----
$query1 = $pdo->query("SELECT * FROM caixas WHERE operador = '$id_usuario' AND data_fechamento IS NULL ORDER BY id DESC LIMIT 1");
$res1   = $query1->fetchAll(PDO::FETCH_ASSOC);
$id_caixa = (@count($res1) > 0) ? $res1[0]['id'] : 0;

// ----- BAIXA EM LOTE -----
// Observação: alguns bancos podem ter registros com 'Nao/Comissao' (sem acento).
// Sem alterar a lógica, aceitamos as duas grafias para evitar erro de filtro.
$sql = "
  UPDATE $tabela 
     SET pago         = 'Sim',
         usuario_baixa= :usuario_baixa,
         data_pgto    = CURDATE(),
         caixa        = :caixa,
         hora         = CURTIME(),
         pgto         = :pgto
   WHERE data_lanc BETWEEN :ini AND :fim
     AND pago IN ('Não','Nao')
     AND funcionario = :func
     AND tipo IN ('Comissão','Comissao')
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':usuario_baixa', $id_usuario, PDO::PARAM_INT);
$stmt->bindValue(':caixa',         $id_caixa,   PDO::PARAM_INT);
$stmt->bindValue(':pgto',          $pgto);
$stmt->bindValue(':ini',           $dataInicial);
$stmt->bindValue(':fim',           $dataFinal);
$stmt->bindValue(':func',          $funcionario, PDO::PARAM_INT);

try {
  $stmt->execute();
  echo 'Baixado com Sucesso';
} catch (Throwable $e) {
  // Não quebra a lógica da tela: devolve 500 e uma mensagem simples
  http_response_code(500);
  echo 'Erro ao baixar comissões';
}
