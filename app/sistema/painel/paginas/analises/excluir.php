<?php 
require_once("../../../conexao.php");
$tabela = 'analise_capilar';

$id = @$_POST['id'];

if($id == ""){
	echo 'ID inválido';
	exit();
}

$query = $pdo->prepare("SELECT * FROM $tabela WHERE id = :id");
$query->bindValue(":id", $id);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if(@count($res) > 0){
	$foto = $res[0]['foto'];

	if($foto != ""){
		$caminho_foto = "../../../images/analises/" . $foto;
		if(file_exists($caminho_foto)){
			@unlink($caminho_foto);
		}
	}
}

$query = $pdo->prepare("DELETE FROM $tabela WHERE id = :id");
$query->bindValue(":id", $id);
$query->execute();

echo 'Excluído com Sucesso';
?>