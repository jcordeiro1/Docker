<?php
@session_start();
require_once("../../../conexao.php");

$tabela = 'avaliacoes_site';
$id = $_POST['id'] ?? '';
$valor = isset($_POST['valor']) ? (int)$_POST['valor'] : 0;

if(!$id){ echo 'ID inválido'; exit; }

$st = $pdo->prepare("UPDATE {$tabela} SET status=:v WHERE id=:id");
$st->execute([':v'=>$valor, ':id'=>$id]);

echo "Alterado com Sucesso";
