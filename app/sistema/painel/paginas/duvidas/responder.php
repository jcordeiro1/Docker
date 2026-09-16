<?php
require_once(__DIR__ . '/../../../conexao.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = (int) $_POST['id_pergunta'];
$resposta = strip_tags(trim($_POST['resposta']));

if ($id > 0 && !empty($resposta)) {
    try {
        // 1. Atualiza no banco
        $sql = "UPDATE perguntas SET resposta = :resposta, data_resp = NOW(), respondida = 'Sim' WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':resposta', $resposta);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // 2. Busca dados do usuário que fez a pergunta
        $queryPerg = $pdo->query("SELECT id_usuario, pergunta FROM perguntas WHERE id = '$id'");
        $resPerg = $queryPerg->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $resPerg['id_usuario'];
        $pergunta = $resPerg['pergunta'];

        $queryUser = $pdo->query("SELECT nome, telefone FROM usuarios WHERE id = '$id_usuario'");
        $resUser = $queryUser->fetch(PDO::FETCH_ASSOC);
        $nome = $resUser['nome'];
        $telefone = $resUser['telefone'];

        // 3. Formata telefone com DDI Brasil
        $telefone = '55' . preg_replace('/[ ()-]+/', '', $telefone);

        // 4. Monta mensagem
        $mensagem  = "💬 *Sua dúvida foi respondida!*\n\n";
        $mensagem .= "👤 *Nome:* $nome\n";
        $mensagem .= "📝 *Pergunta:* $pergunta\n";
        $mensagem .= "✅ *Resposta:* $resposta";

        // 5. Chama API da Menuia
        require('../../../../ajax/api-texto.php');

        echo 'Resposta enviada com sucesso!';
    } catch (PDOException $e) {
        echo 'Erro ao enviar resposta: ' . $e->getMessage();
    }
} else {
    echo 'Preencha todos os campos.';
}
?>



