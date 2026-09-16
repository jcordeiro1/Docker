<?php
define('SEC_WAF_MODE', 'detect');
require_once __DIR__ . '/../security.php';

require_once __DIR__ . '/../sistema/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$funcionario = (int)($_POST['func'] ?? 0);

if ($funcionario <= 0) {
    echo '';
    exit;
}

$st = $pdo->prepare("SELECT nome FROM usuarios WHERE id = :id LIMIT 1");
$st->bindValue(':id', $funcionario, PDO::PARAM_INT);
$st->execute();

$nome = (string)$st->fetchColumn();
echo $nome;

exit;