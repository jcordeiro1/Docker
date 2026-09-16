<?php 
require_once("../../../conexao.php");
$tabela = 'usuarios';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$acao = isset($_POST['acao']) ? (string)$_POST['acao'] : '';

if ($id <= 0) {
	echo 'ID inválido!';
	exit;
}

// mantém sua lógica (Sim/Não), só valida para não gravar lixo
if ($acao !== 'Sim' && $acao !== 'Não') {
	echo 'Ação inválida!';
	exit;
}

try {
	$stmt = $pdo->prepare("UPDATE {$tabela} SET ativo = :acao WHERE id = :id");
	$stmt->bindValue(':acao', $acao);
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
	$stmt->execute();

	echo 'Alterado com Sucesso';
} catch (Throwable $e) {
	echo 'Erro ao Alterar!';
}
?>
