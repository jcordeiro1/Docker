<?php 
require_once("../../../conexao.php");

$item = $_POST['item'];
$query = $pdo->query("SELECT * FROM itens_assinaturas where id = '$item' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor = $res[0]['valor'];

echo $valor;
