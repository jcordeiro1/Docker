<?php
// cron/cron_marketing.php
header('Content-Type: text/html; charset=utf-8');

// Use um tempo de execução generoso para scripts de CRON
set_time_limit(600); // 10 minutos

require_once("../sistema/conexao.php");

// ===== Emoji/UTF-8 completo no MySQL =====
$pdo->exec("SET NAMES utf8mb4");
$pdo->exec("SET CHARACTER SET utf8mb4");
$pdo->exec("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");

// ===== Helpers (Funções de Ajuda) =====
function onlyDigits(string $s): string {
    return preg_replace('/\D+/', '', $s);
}

// Verifica se o status da API indica sucesso ou um erro "final" (como número inexistente)
function isSendStatusSuccess(?string $status): bool {
    $status = (string)$status;
    // Adicione outros status de sucesso ou erro final aqui se necessário
    return ($status === "Mensagem enviada com sucesso." || $status === "O número não existe");
}

// ==================================================================
// ALTERNATIVA SEM FOR UPDATE (COMPATÍVEL COM MyISAM e InnoDB)
// ==================================================================

// Inicia uma transação (em MyISAM, isto não fará muito, mas não causa erro)
$pdo->beginTransaction();

try {
    // Passo 1: Seleciona apenas os IDs dos disparos pendentes
    $sql = "
      SELECT id
        FROM disparos
       WHERE data_disparo = CURDATE()
         AND hora <= CURTIME()
         AND status = 'Pendente'
       ORDER BY hora ASC, id ASC
       LIMIT 200
    ";
    $stmt = $pdo->query($sql);
    $idsToProcess = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!$idsToProcess) {
        $pdo->commit();
        echo 0;
        exit;
    }

    // Passo 2: Marca os disparos selecionados como 'Processando'
    // A condição AND status = 'Pendente' aqui é uma segurança extra
    $inClause = implode(',', array_fill(0, count($idsToProcess), '?'));
    $updateStmt = $pdo->prepare("UPDATE disparos SET status = 'Processando' WHERE id IN ($inClause) AND status = 'Pendente'");
    $updateStmt->execute($idsToProcess);

    // Passo 3: Seleciona os dados completos dos disparos que acabámos de marcar
    $selectFullData = $pdo->prepare("SELECT * FROM disparos WHERE id IN ($inClause)");
    $selectFullData->execute($idsToProcess);
    $rows = $selectFullData->fetchAll(PDO::FETCH_ASSOC);

    $pdo->commit();

} catch (Throwable $e) {
    $pdo->rollBack();
    error_log('[cron_marketing] Erro ao selecionar trabalhos: ' . $e->getMessage());
    echo "Erro ao selecionar trabalhos.";
    exit;
}


// ===== INÍCIO DO PROCESSAMENTO DOS DISPAROS =====

// Cache de campanhas para reduzir consultas repetidas
$campaignCache = [];

// Listas para agrupar operações de banco de dados
$idsToDelete = [];
$sendsPerCampaign = [];

foreach ($rows as $row) {
    $disparoId = (int)$row['id'];
    $campaignId = (int)$row['campanha'];
    $nomeCliente = (string)$row['nome'];
    $telefoneCliente = (string)$row['telefone'];

    // Normaliza telefone
    $tel = onlyDigits($telefoneCliente);
    if ($tel === '' || strlen($tel) < 10) {
        $idsToDelete[] = $disparoId; // Telefone inválido, marca para apagar
        continue;
    }
    if (strpos($tel, '55') !== 0) {
        $tel = '55' . $tel;
    }
    
    // Se a API estiver desligada, paramos o processamento aqui
    if ($api == 'Não') {
        continue;
    }

    // Carrega campanha do cache ou do banco
    if (!isset($campaignCache[$campaignId])) {
        $st = $pdo->prepare("SELECT * FROM marketing WHERE id = :id LIMIT 1");
        $st->execute([':id' => $campaignId]);
        $campaign = $st->fetch(PDO::FETCH_ASSOC);

        if (!$campaign) {
            $idsToDelete[] = $disparoId; // Campanha não existe mais, marca para apagar
            continue;
        }
        $campaignCache[$campaignId] = $campaign;
    } else {
        $campaign = $campaignCache[$campaignId];
    }

    // Seleciona mensagem (A/B testing)
    $msg1 = trim((string)$campaign['mensagem']);
    $msg2 = trim((string)$campaign['mensagem2']);
    $mensagemDisparo = ($msg1 && $msg2) ? (rand(0, 1) ? $msg1 : $msg2) : ($msg1 ?: $msg2);
    
    $isSuccess = false;
    $statusMsg = '';

    // Loop de envio (texto, arquivos, etc.)
    try {
        // Enviar texto (se houver)
        if ($mensagemDisparo !== '') {
            $telefone = $tel; // A include espera a variável $telefone
            $mensagem = $mensagemDisparo;
            require("texto.php"); // A include deve setar a variável $status_mensagem
            if (isSendStatusSuccess($status_mensagem)) $isSuccess = true;
        }

        // Enviar arquivos (imagem, áudio, documento)
        $filesToSend = [
            'arquivo'   => $campaign['arquivo'],
            'audio'     => $campaign['audio'],
            'documento' => $campaign['documento'],
        ];

        foreach ($filesToSend as $type => $file) {
            if ($file && $file !== 'sem-foto.png') {
                $telefone = $tel;
                $url_envio = $url_sistema . "sistema/painel/images/marketing/" . $file;
                $mensagem = pathinfo($file, PATHINFO_BASENAME); // Legenda/nome do arquivo
                require("marketing_file.php"); // A include deve setar $status_mensagem
                if (isSendStatusSuccess($status_mensagem)) $isSuccess = true;
            }
        }

    } catch (Throwable $e) {
        error_log("[cron_marketing] Falha no envio para disparo ID {$disparoId}: " . $e->getMessage());
    }

    // Decide o que fazer com o disparo baseado no sucesso e no tipo de API
    $deveRemover = false;
    if ($api === 'menuia') {
        if ($isSuccess) {
            $deveRemover = true;
        }
    } else {
        // Comportamento original para outras APIs: remove sempre após a tentativa
        $deveRemover = true;
    }

    if ($deveRemover) {
        $idsToDelete[] = $disparoId;
        // Prepara para contabilizar o envio
        if (!isset($sendsPerCampaign[$campaignId])) {
            $sendsPerCampaign[$campaignId] = 0;
        }
        $sendsPerCampaign[$campaignId]++;
    }
    
    usleep(150000); // Pausa de 0.15s para não sobrecarregar a API
}

// ===== FINALIZAÇÃO E LIMPEZA (OPERAÇÕES EM LOTE) =====

// Apaga todos os disparos processados com sucesso de uma só vez
if (!empty($idsToDelete)) {
    $inClause = implode(',', array_fill(0, count($idsToDelete), '?'));
    $deleteStmt = $pdo->prepare("DELETE FROM disparos WHERE id IN ($inClause)");
    $deleteStmt->execute($idsToDelete);
}

// Atualiza os contadores de envio para cada campanha afetada
if (!empty($sendsPerCampaign)) {
    $updateStmt = $pdo->prepare("UPDATE marketing SET envios = envios + ?, ultimo_envio = NOW() WHERE id = ?");
    foreach ($sendsPerCampaign as $campaignId => $count) {
        $updateStmt->execute([$count, $campaignId]);
    }
}

// Retorna quantos itens foram selecionados para processamento nesta execução
echo count($rows);
?>