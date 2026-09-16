<?php
// /sistema/painel_cliente/editar-perfil.php

session_start();
require_once('../conexao.php');

// Opcionalmente, garante que s«Ñ usu«¡rio logado edita o pr«Ñprio perfil
if (empty($_SESSION['id'])) {
    http_response_code(403);
    echo 'Acesso negado.';
    exit();
}

// Garante que o m«±todo «± POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'M«±todo n«ªo permitido.';
    exit();
}

// Sanitiza e l«´ os dados do POST
$id         = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$nome       = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$telefone   = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$cpf        = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$senha      = filter_input(INPUT_POST, 'senha', FILTER_UNSAFE_RAW);
$conf_senha = filter_input(INPUT_POST, 'conf_senha', FILTER_UNSAFE_RAW);
$endereco   = filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// Normaliza (trim)
$id         = $id !== null ? (int)$id : 0;
$nome       = $nome !== null ? trim($nome) : '';
$telefone   = $telefone !== null ? trim($telefone) : '';
$cpf        = $cpf !== null ? trim($cpf) : '';
$senha      = $senha !== null ? trim($senha) : '';
$conf_senha = $conf_senha !== null ? trim($conf_senha) : '';
$endereco   = $endereco !== null ? trim($endereco) : '';

// Valida«®«Øes b«¡sicas
if ($id <= 0) {
    echo 'ID inv«¡lido.';
    exit();
}

if ($senha !== $conf_senha) {
    echo 'As senhas s«ªo diferentes!!';
    exit();
}

// Mant«±m a l«Ñgica: sempre gerar novo hash da senha informada
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);

try {
    // UPDATE 100% parametrizado (sem interpola«®«ªo na query)
    $query = $pdo->prepare("
        UPDATE clientes 
           SET nome       = :nome,
               telefone   = :telefone,
               cpf        = :cpf,
               senha_crip = :senha_crip,
               endereco   = :endereco
         WHERE id         = :id
        LIMIT 1
    ");

    $query->bindValue(':nome',       $nome,       PDO::PARAM_STR);
    $query->bindValue(':telefone',   $telefone,   PDO::PARAM_STR);
    $query->bindValue(':cpf',        $cpf,        PDO::PARAM_STR);
    $query->bindValue(':senha_crip', $senha_crip, PDO::PARAM_STR);
    $query->bindValue(':endereco',   $endereco,   PDO::PARAM_STR);
    $query->bindValue(':id',         $id,         PDO::PARAM_INT);

    $query->execute();

    // NªªO mudar essa mensagem: o JS do front usa ela
    echo 'Editado com Sucesso';
} catch (PDOException $e) {
    // N«ªo exp«Øe erro de banco
    http_response_code(500);
    echo 'Erro ao atualizar perfil.';
}
