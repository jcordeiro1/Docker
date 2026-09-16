<?php
// /sistema/autenticar_cliente.php

session_start();
require_once 'conexao.php';

// Garante que o acesso é via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Método de acesso inválido. Faça login novamente.',
    ];
    header('Location: acesso');
    exit();
}

// Sanitização básica + trim
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$senha    = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$telefone = $telefone !== null ? trim($telefone) : '';
$senha    = $senha !== null ? trim($senha) : '';

// Validação simples: campos obrigatórios
if ($telefone === '' || $senha === '') {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Informe telefone e senha para entrar.',
    ];
    header('Location: acesso');
    exit();
}

try {
    // Consulta segura usando prepared statement
    $query = $pdo->prepare('SELECT * FROM clientes WHERE telefone = :telefone LIMIT 1');
    $query->bindValue(':telefone', $telefone, PDO::PARAM_STR);
    $query->execute();
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Não expõe detalhes de erro
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Erro ao acessar o sistema. Tente novamente em alguns instantes.',
    ];
    header('Location: acesso');
    exit();
}

$total_reg = count($res);

if ($total_reg > 0) {
    // Verifica a senha hashada
    if (!password_verify($senha, $res[0]['senha_crip'])) {
        $_SESSION['flash'] = [
            'type'    => 'error',
            'message' => 'Usuário ou senha incorretos!',
        ];
        header('Location: acesso');
        exit();
    }

    // Login OK – reforça segurança da sessão
    session_regenerate_id(true);

    // Mesma lógica que você já usava
    $_SESSION['id']                  = $res[0]['id'];
    $_SESSION['nome']                = $res[0]['nome'];
    $_SESSION['aut_token_505052022'] = 'fdsfdsafda885574125';

    // Opcional: mensagem de sucesso (não é obrigatória)
    // $_SESSION['flash'] = [
    //     'type'    => 'success',
    //     'message' => 'Login realizado com sucesso!',
    // ];

    // Ir para o painel (mantendo a navegação)
    header('Location: painel_cliente');
    exit();
} else {
    // Usuário não encontrado
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Usuário ou senha incorretos!',
    ];
    header('Location: acesso');
    exit();
}
