<?php
require_once("../sistema/conexao.php");

// Contas que vencem HOJE, não pagas, sem alerta, com pessoa válida e já passou a hora do alerta
$query = $pdo->query("
    SELECT *
    FROM receber
    WHERE data_venc = CURDATE()
      AND pago != 'Sim'
      AND (alerta IS NULL OR alerta != 'Sim')
      AND pessoa > 0
      AND pessoa IS NOT NULL
      AND hora_alerta <= CURTIME()
");

$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagar_vencidas = @count($res);

// Controle para não enviar mais de 1 mensagem por cliente
$clientesNotificados = [];

for ($i = 0; $i < $contas_pagar_vencidas; $i++) {

    $valor      = $res[$i]['valor'];
    $descricao  = $res[$i]['descricao'];
    $id         = $res[$i]['id'];
    $cliente    = $res[$i]['pessoa'];
    $vencimento = $res[$i]['data_venc'];

    // Formata data de vencimento para dia/mês/ano
    $vencimentoF = implode('/', array_reverse(@explode('-', $vencimento)));

    // Se este cliente já recebeu mensagem neste ciclo, não envia de novo
    if (isset($clientesNotificados[$cliente])) {

        // Mesmo assim, marca esta conta como alertada para não voltar em execuções futuras
        $pdo->query("
            UPDATE receber
               SET alerta      = 'Sim',
                   data_alerta = CURDATE()
             WHERE id = '$id'
        ");

        continue;
    }

    // Busca dados do cliente
    $query2 = $pdo->query("SELECT * FROM clientes WHERE id = '$cliente'");
    $res2   = $query2->fetchAll(PDO::FETCH_ASSOC);

    if (@count($res2) > 0) {
        $nome_cliente     = $res2[0]['nome'];
        $telefone_cliente = $res2[0]['telefone'];
    } else {
        $nome_cliente     = 'Sem Registro';
        $telefone_cliente = "";
    }

    $link_pgto         = $url_sistema . 'conta/' . $id;
    $valor_multa_juros = 0; // Mantido para não alterar a lógica original

    $valorF = @number_format($valor, 2, ',', '.');

    // Enviar whatsapp
    if ($msg_agendamento == 'Api' and $telefone_cliente != '') {

        // Normaliza telefone (remove espaços, parênteses e traços) e adiciona DDI 55
        $telefone = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);

        $mensagem  = '💰 *' . $nome_sistema . '*%0A';
        $mensagem .= '_Sua Conta Vence Hoje_ %0A';
        $mensagem .= '*Descrição:* ' . $descricao . ' %0A';
        $mensagem .= '*Cliente:* ' . $nome_cliente . ' %0A';
        $mensagem .= '*Valor:* ' . $valorF . ' %0A';
        $mensagem .= '*Vencimento:* ' . $vencimentoF . ' %0A%0A';
        $mensagem .= '*Link Pagamento:* %0A';
        $mensagem .= $link_pgto;

        // Script responsável por enviar a mensagem (mantido como no original)
        require('texto.php');

        // Marca que este cliente já foi notificado neste ciclo
        $clientesNotificados[$cliente] = true;

        // Marca todas as contas em aberto deste cliente como alertadas
        // (para nenhum cron enviar novamente – nem este, nem o de vencidas)
        $pdo->query("
            UPDATE receber
               SET alerta      = 'Sim',
                   data_alerta = CURDATE()
             WHERE pessoa = '$cliente'
               AND pago != 'Sim'
        ");

        // Mantém sua lógica original (redundante, mas não quebra)
        if (@$status_mensagem == 'Mensagem enviada com sucesso.' and $api == 'menuia') {
            $pdo->query("
                UPDATE receber
                   SET alerta = 'Sim'
                 WHERE id = '$id'
            ");
        }

        if ($api != 'menuia') {
            $pdo->query("
                UPDATE receber
                   SET alerta = 'Sim'
                 WHERE id = '$id'
            ");
        }
    }
}

echo $contas_pagar_vencidas;

?>
