<?php 
require_once("../../../conexao.php");

$cliente = @$_POST['cliente'];

if($cliente == ""){
	echo json_encode(array(
		'status' => 'Erro',
		'mensagem' => 'Cliente não informado'
	));
	exit();
}

$query = $pdo->prepare("SELECT modelo, cor, densidade, tamanho, observacoes FROM proteses WHERE cliente = :cliente ORDER BY id DESC LIMIT 1");
$query->bindValue(":cliente", $cliente);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if($total_reg > 0){

	$modelo = $res[0]['modelo'];
	$cor = $res[0]['cor'];
	$densidade = $res[0]['densidade'];
	$tamanho = $res[0]['tamanho'];
	$observacoes = $res[0]['observacoes'];

	echo json_encode(array(
		'status' => 'Sucesso',
		'modelo' => $modelo,
		'cor' => $cor,
		'densidade' => $densidade,
		'tamanho' => $tamanho,
		'observacoes' => $observacoes
	));

}else{
	echo json_encode(array(
		'status' => 'Vazio',
		'mensagem' => 'Nenhuma prótese encontrada para este cliente'
	));
}
?>