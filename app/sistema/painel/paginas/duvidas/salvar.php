<?php
$tabela = 'duvidas';
require_once("../../../conexao.php");
$titulo = $_POST['titulo'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$link_video = $_POST['link_video'] ?? '';
$data = date('Y-m-d');

if ($titulo == '' || $descricao == '') {
  echo "Preencha todos os campos obrigatórios.";
  exit();
}

try {
  $stmt = $pdo->prepare("INSERT INTO tutoriais SET titulo = :titulo, descricao = :descricao, link_video = :link_video, data = :data");
  $stmt->bindParam(':titulo', $titulo);
  $stmt->bindParam(':descricao', $descricao);
  $stmt->bindParam(':link_video', $link_video);
  $stmt->bindParam(':data', $data);
  $stmt->execute();
  echo "Salvo com Sucesso";
} catch (Exception $e) {
  echo "Erro ao salvar: " . $e->getMessage();
}
