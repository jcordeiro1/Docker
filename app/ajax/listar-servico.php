<?php
define('SEC_WAF_MODE', 'detect');
require_once __DIR__ . '/../security.php';

require_once __DIR__ . '/../sistema/conexao.php';

$servico = $_POST['serv'] ?? '';

$nome = '';

if ($servico !== '') {
    $query = $pdo->query("SELECT * FROM servicos where id = '$servico' ");
    $res = $query->fetchAll(PDO::FETCH_ASSOC);

    if (@count($res) > 0) {
        $nome = $res[0]['nome'];
    }
}

echo $nome;
?>

