<?php 
require_once("../../../conexao.php");
$tabela = 'assinaturas';

$id = $_POST['id'];
$cliente = $_POST['cliente'];
$item = $_POST['item'];
$grupo = $_POST['grupo'];
$valor = $_POST['valor'];
$frequencia = $_POST['frequencia'];
$vencimento = $_POST['vencimento'];

if(strpos($valor, ',') !== false){
	$valor = str_replace('.', '', $valor);
	$valor = str_replace(',', '.', $valor);
}

if($id == ""){

	$query = $pdo->prepare("INSERT INTO $tabela SET 
		cliente = '$cliente',
		data = curDate(),
		pago = 'Não',
		grupo = '$grupo',
		item = '$item',
		valor = :valor,
		frequencia = '$frequencia',
		vencimento = '$vencimento'");

}else{

	$query = $pdo->prepare("UPDATE $tabela SET 
		cliente = '$cliente',
		grupo = '$grupo',
		item = '$item',
		valor = :valor,
		frequencia = '$frequencia',
		vencimento = '$vencimento'
		WHERE id = '$id'");

}

$query->bindValue(":valor", $valor);
$query->execute();

echo 'Salvo com Sucesso';
?>