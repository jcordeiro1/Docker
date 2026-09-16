<?php 
require_once("../../../conexao.php");
$tabela = 'perguntas';

$id = $_POST['id'];

//excluir as respostas e pergunta
$pdo->query("DELETE FROM respostas where pergunta = '$id'");
$pdo->query("DELETE FROM $tabela where id = '$id'");


echo 'Excluído com Sucesso';

?>