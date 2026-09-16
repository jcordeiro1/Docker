<?php
@session_start();
require_once("../sistema/conexao.php");

$id = $_POST['id'] ?? '';

$query = $pdo->prepare("SELECT * FROM agendamentos WHERE id = :id");
$query->bindValue(":id", $id);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if (@count($res) > 0) {
  $_SESSION['editar_agendamento'] = [
    'servico' => $res[0]['servico'],
    'funcionario' => $res[0]['funcionario'],
    'data' => $res[0]['data'],
    'hora' => $res[0]['hora'],
    'id' => $id
  ];
  echo 'ok';
} else {
  echo 'erro';
}
