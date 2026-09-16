<?php 
require_once("../sistema/conexao.php");
session_start(); // Remover @ para que os erros sejam exibidos

$telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_SPECIAL_CHARS);
$nome = filter_var($_POST['nome'], FILTER_SANITIZE_SPECIAL_CHARS);
$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

if (!$id) {
    die('ID invalido.');
}

// Consulta para verificar o item de assinatura
$query = $pdo->prepare("SELECT * FROM itens_assinaturas WHERE id = :id");
$query->bindValue(":id", $id, PDO::PARAM_INT);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($res)) {
    die('Item de assinatura nao encontrado.');
}

$id_grupo = $res[0]['grupo'];
$valor = $res[0]['valor'];

// Cadastrar o cliente caso naoenha cadastro
$senha = '123';
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);
$query = $pdo->prepare("SELECT * FROM clientes WHERE telefone LIKE :telefone");
$query->bindValue(":telefone", $telefone);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if (empty($res)) {
    $query = $pdo->prepare("INSERT INTO clientes SET nome = :nome, telefone = :telefone, data_cad = CURDATE(), cartoes = '0', alertado = 'Nao', senha_crip = '$senha_crip'");
    $query->bindValue(":nome", $nome);
    $query->bindValue(":telefone", $telefone);    
    $query->execute();
    $id_cliente = $pdo->lastInsertId();
} else {
    $id_cliente = $res[0]['id'];
}

// Excluir assinaturas anteriores que estiverem pendentes
$pdo->prepare("DELETE FROM assinaturas WHERE cliente = :id_cliente AND item = :id AND pago != 'Sim'")
    ->execute([':id_cliente' => $id_cliente, ':id' => $id]);

$datahoje = date('Y-m-d');
$dataVencimento = date('Y-m-d', strtotime($datahoje . ' +30 days'));

// Assinatura recorrente
$urlRecorrencia = $url_sistema.'sistema/painel/paginas/clientes/recorrencia.php';

// Dados a serem enviados via POST
$data = [
    'id' => $id_cliente,
    'valor' => $valor,
    'parcelas' => 1,
    'data_venc' => $dataVencimento,
    'frequencia' => 30
];

// Inicializa o cURL
$ch = curl_init($urlRecorrencia);

// Configuracao do cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

$response = curl_exec($ch);
curl_close($ch);

// Marcar o agendamento
$pdo->prepare("INSERT INTO assinaturas SET cliente = :id_cliente, data = CURDATE(), pago = 'Nao', grupo = :id_grupo, item = :id, valor = :valor, frequencia = '30', vencimento = curDate(), data_vencimento = :dataVencimento")
    ->execute([':id_cliente' => $id_cliente, ':id_grupo' => $id_grupo, ':id' => $id, ':valor' => $valor, ':dataVencimento' => $dataVencimento]);

$ult_id = $pdo->lastInsertId();
echo 'Salvo*' . $ult_id;
?>
