<?php 
require_once("../../../conexao.php");
$tabela = 'manutencoes_protese';

$id = @$_POST['id'];

if($id == ""){
	echo 'ID inválido';
	exit();
}

$query = $pdo->prepare("DELETE FROM $tabela WHERE id = :id");
$query->bindValue(":id", $id);
$query->execute();

echo 'Excluído com Sucesso';
?>