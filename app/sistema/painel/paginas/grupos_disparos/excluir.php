<?php 
$tabela = 'grupos_disparos';

require_once("../../../conexao.php");

$id = $_POST['id'] ?? 0;

// Exclui grupo
$pdo->query("DELETE FROM grupos_disparos WHERE id = '$id'");

// Exclui vínculos com clientes (se quiser garantir limpeza)
$pdo->query("DELETE FROM grupos_clientes WHERE grupo = '$id'");

echo "Excluído com Sucesso";
?>
