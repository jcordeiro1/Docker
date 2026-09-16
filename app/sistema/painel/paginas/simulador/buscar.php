<?php 
require_once("../../../conexao.php");

$id = @$_POST['id'];

$query = $pdo->prepare("SELECT * FROM simulacoes_protese WHERE id = :id");
$query->bindValue(":id", $id);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$dados = array();

if(@count($res) > 0){

	$dados['foto_original'] = $res[0]['foto_original'];
	$dados['imagem_simulada'] = $res[0]['imagem_simulada'];

}

echo json_encode($dados);

?>