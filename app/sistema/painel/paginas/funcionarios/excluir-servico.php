<?php
require_once("../../../conexao.php");
$tabela = 'servicos_func';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
	echo 'ID inválido!';
	exit();
}

try {
	// Mesma lógica: deletar pelo id (seguro com prepared statement)
	$stmt = $pdo->prepare("DELETE FROM {$tabela} WHERE id = :id");
	$stmt->bindValue(':id', $id, PDO::PARAM_INT);
	$stmt->execute();

	// Mantém exatamente a string que seu JS espera
	echo 'Excluído com Sucesso';

} catch (Throwable $e) {
	// Não vaza erro interno do banco / sistema
	echo 'Erro ao Excluir!';
}
?>
