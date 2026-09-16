<?php 
require_once("../../../conexao.php");
$tabela = 'receber';

$id = $_POST['id'];
$id_plano = $_POST['id_plano'];

$pdo->query("DELETE from $tabela where id = '$id'");
$pdo->query("UPDATE assinaturas set cancelado = 'Sim' where id = '$id_plano'");
echo 'Excluído com Sucesso';
 ?>