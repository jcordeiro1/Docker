<?php
@session_start();
require_once(__DIR__ . '/../../../conexao.php');

$pergunta = trim($_POST['pergunta'] ?? '');
$id_usuario = $_SESSION['id'] ?? null;

if ($pergunta && $id_usuario) {
    $data_criacao = date('Y-m-d H:i:s');

    // Salva a pergunta
    $stmt = $pdo->prepare("INSERT INTO perguntas (id_usuario, pergunta, data_criacao) VALUES (:id_usuario, :pergunta, :data_criacao)");
    $stmt->bindParam(':id_usuario', $id_usuario);
    $stmt->bindParam(':pergunta', $pergunta);
    $stmt->bindParam(':data_criacao', $data_criacao);
    $stmt->execute();

    // Buscar dados do usuário
    $query = $pdo->query("SELECT nome, telefone FROM usuarios WHERE id = '$id_usuario'");
    $res = $query->fetchAll(PDO::FETCH_ASSOC);
    $nome = $res[0]['nome'];
    $telefone = $res[0]['telefone'];

    // Monta mensagem para admin
    $mensagem = "❓ *Nova dúvida recebida*\n\n";
    $mensagem .= "👤 *Usuário:* $nome\n";
    $mensagem .= "📞 *Telefone:* $telefone\n";
    $mensagem .= "📝 *Pergunta:*\n$pergunta";

    // Corrige formato do telefone (com DDI Brasil)
    $telefone = '55' . preg_replace('/[ ()-]+/', '', $telefone);

    // Envia via API Menuia
    require('../../../../ajax/api-texto.php');
    //require(__DIR__ . '/.../../../../api/api-texto.php');

    echo "Pergunta enviada com sucesso!";
} else {
    echo "Erro: Campos obrigatórios não preenchidos.";
}
