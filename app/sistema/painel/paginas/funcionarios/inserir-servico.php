<?php 
require_once("../../../conexao.php");
$tabela = 'servicos_func';

// Mesmos nomes de POST do seu formulário: id (funcionário) e servico
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$servico = isset($_POST['servico']) ? (int)$_POST['servico'] : 0;

// Mantém a lógica, só valida para não gravar lixo
if ($id <= 0) {
	echo 'Funcionário inválido!';
	exit();
}
if ($servico <= 0) {
	echo 'Serviço inválido!';
	exit();
}

$func = $id;

// Verifica se já existe (mesma lógica, só com prepared)
$stmt = $pdo->prepare("SELECT 1 FROM $tabela WHERE funcionario = :func AND servico = :serv LIMIT 1");
$stmt->execute([
	':func' => $func,
	':serv' => $servico
]);

if ($stmt->fetchColumn()) {
	echo 'Serviço já adicionado ao Funcionário!';
	exit();
}

// Insere (mesma lógica, só com prepared)
$stmt = $pdo->prepare("INSERT INTO $tabela (servico, funcionario) VALUES (:serv, :func)");
$stmt->execute([
	':serv' => $servico,
	':func' => $func
]);

echo 'Salvo com Sucesso';
?>
