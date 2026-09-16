<?php
require_once("../../../conexao.php");

$tabela = 'simulacoes_protese';
$pasta_upload = __DIR__ . '/../../images/simulacoes/';

$id = @$_POST['id'];

if($id == ""){
	echo 'ID não informado';
	exit();
}

$query = $pdo->prepare("SELECT * FROM $tabela WHERE id = :id");
$query->bindValue(":id", $id);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if(@count($res) == 0){
	echo 'Registro não encontrado';
	exit();
}

$foto_original = $res[0]['foto_original'];
$imagem_simulada = $res[0]['imagem_simulada'];

if($foto_original != ""){
	$caminho_foto_original = $pasta_upload . $foto_original;
	if(file_exists($caminho_foto_original)){
		@unlink($caminho_foto_original);
	}
}

if($imagem_simulada != ""){
	$caminho_imagem_simulada = $pasta_upload . $imagem_simulada;
	if(file_exists($caminho_imagem_simulada)){
		@unlink($caminho_imagem_simulada);
	}
}

$query = $pdo->prepare("DELETE FROM $tabela WHERE id = :id");
$query->bindValue(":id", $id);
$query->execute();

echo 'Excluído com Sucesso';
?>