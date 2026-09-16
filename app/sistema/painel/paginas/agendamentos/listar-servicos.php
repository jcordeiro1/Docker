<?php
require_once("../../../conexao.php");

// (opcional, mas recomendado em ajax)
header('Content-Type: text/html; charset=UTF-8');

// pega o funcionário (caso venha vazio, fica string vazia mesmo)
$func = isset($_POST['func']) ? trim((string)$_POST['func']) : '';

// se você sabe que funcionario é ID numérico, isso ajuda a evitar lixo
// (não muda a lógica: se vier inválido, vai cair em "Nenhum Serviço")
$funcId = (int)$func;

// 1) busca os serviços vinculados ao funcionário
$stmt = $pdo->prepare("SELECT servico FROM servicos_func WHERE funcionario = :func");
$stmt->execute([':func' => $funcId]);
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

// prepara a query do serviço ativo (reutiliza no loop)
$stmt2 = $pdo->prepare("SELECT nome FROM servicos WHERE id = :id AND ativo = 'Sim' LIMIT 1");

if (count($res) > 0) {
    for ($i = 0; $i < count($res); $i++) {

        $serv = $res[$i]['servico'];
        $servId = (int)$serv;

        // 2) filtra só serviços ativos
        $stmt2->execute([':id' => $servId]);
        $nome_serv = $stmt2->fetchColumn();

        // se não tiver serviço ativo com esse id, pula
        if ($nome_serv === false || $nome_serv === null || $nome_serv === '') {
            continue;
        }

        // evita quebrar HTML se tiver aspas/caracteres especiais
        $nome_serv_safe = htmlspecialchars((string)$nome_serv, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        echo '<option value="' . $servId . '">' . $nome_serv_safe . '</option>';
    }
} else {
    echo '<option value="">Nenhum Serviço</option>';
}
?>
