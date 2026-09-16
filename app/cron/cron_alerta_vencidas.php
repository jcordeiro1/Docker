<?php
require_once("../sistema/conexao.php");

// Contas JÁ VENCIDAS (data_venc < hoje), não pagas, com pessoa válida,
// já passou da hora do alerta, ainda sem alerta geral, e ainda não alertadas hoje
$query = $pdo->query("
    SELECT *
    FROM receber
    WHERE data_venc < CURDATE()
      AND pago != 'Sim'
      AND pessoa > 0
      AND pessoa IS NOT NULL
      AND hora_alerta <= CURTIME()
      AND (data_alerta != CURDATE() OR data_alerta IS NULL)
      AND (alerta IS NULL OR alerta != 'Sim')
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

    // Formata data para dia/mês/ano
    $vencimentoF = implode('/', array_reverse(@explode('-', $vencimento)));

    // Se este cliente já recebeu mensagem neste ciclo, não envia de novo
    if (isset($clientesNotificados[$cliente])) {

        // Mas marca esta conta como alertada para não voltar depois
        $pdo->query("
            UPDATE receber
               SET alerta = 'Sim',
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

    $link_pgto = $url_sistema . 'conta/' . $id;

    $valorF = @number_format($valor, 2, ',', '.');

    // Enviar WhatsApp
    if ($msg_agendamento == 'Api' and $telefone_cliente != '') {

        $telefone = '55' . preg_replace('/[ ()-]+/', '', $telefone_cliente);

        $mensagem  = '💰 *' . $nome_sistema . '*%0A';
        $mensagem .= '_Sua conta Venceu_ %0A';
        $mensagem .= '*Descrição:* ' . $descricao . ' %0A';
        $mensagem .= '*Cliente:* ' . $nome_cliente . ' %0A';
        $mensagem .= '*Valor:* R$ ' . $valorF . ' %0A';
        $mensagem .= '*Vencimento:* ' . $vencimentoF . ' %0A%0A';
        $mensagem .= '*Link Pagamento:* %0A';
        $mensagem .= $link_pgto;

        require('texto.php');

        // Marca que este cliente já foi notificado neste ciclo
        $clientesNotificados[$cliente] = true;

        // ⚠ $hora_random deve estar definido em algum lugar antes deste script

        // Atualiza esta conta (mantendo sua lógica original, só acrescentando alerta)
        if (@$status_mensagem == "Mensagem enviada com sucesso." and $api == 'menuia') {
            $pdo->query("
                UPDATE receber
                   SET data_alerta = CURDATE(),
                       hora_alerta = '$hora_random',
                       alerta      = 'Sim'
                 WHERE id = '$id'
            ");
        }

        if ($api != 'menuia') {
            $pdo->query("
                UPDATE receber
                   SET data_alerta = CURDATE(),
                       hora_alerta = '$hora_random',
                       alerta      = 'Sim'
                 WHERE id = '$id'
            ");
        }

        // Marca TODAS as contas desse cliente, ainda não pagas, como alertadas
        // para nenhum cron cobrar de novo
        $pdo->query("
            UPDATE receber
               SET alerta = 'Sim',
                   data_alerta = CURDATE()
             WHERE pessoa = '$cliente'
               AND pago != 'Sim'
        ");
    }
}

echo $contas_pagar_vencidas;

?>
