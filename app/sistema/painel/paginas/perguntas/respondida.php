<?php 
require_once("../../../conexao.php");
$tabela = 'perguntas';

$id = $_POST['id'];

//excluir as respostas e pergunta
$pdo->query("UPDATE $tabela SET respondida = 'Sim' where id = '$id'");


echo 'Respondida com Sucesso';

?>