<?php  
require_once("../sistema/conexao.php");

// Seleciona contas a pagar que vencem HOJE, não pagas, com alerta pendente,
// mas IGNORA comissões de funcionário (tipo = 'Comissão' e funcionario > 0)
$query = $pdo->query("
    SELECT * 
    FROM pagar 
    WHERE data_venc = CURDATE() 
      AND pago != 'Sim' 
      AND hora_alerta <= CURTIME() 
      AND (alerta IS NULL OR alerta != 'Sim')
      AND NOT (tipo = 'Comissão' AND funcionario > 0)
");

$res = $query->fetchAll(PDO::FETCH_ASSOC);
$contas_pagar_vencidas = @count($res);

for ($i = 0; $i < $contas_pagar_vencidas; $i++) {
    $valor      = $res[$i]['valor'];
    $descricao  = $res[$i]['descricao'];
    $id         = $res[$i]['id'];
    
    $valorF = @number_format($valor, 2, ',', '.');

    // enviar whatsapp para o número do sistema (dono/financeiro)
    if ($msg_agendamento == 'Api' and $whatsapp_sistema != '') {
        $telefone = '55' . preg_replace('/[ ()-]+/', '', $whatsapp_sistema);
        $mensagem  = '💰 *' . $nome_sistema . '*%0A';
        $mensagem .= '_Conta Vencendo Hoje_ %0A';
        $mensagem .= '*Descrição:* ' . $descricao . ' %0A';
        $mensagem .= '*Valor:* ' . $valorF . ' %0A';
        
        require('texto.php');

        if (@$status_mensagem == "Mensagem enviada com sucesso." and $api == 'menuia') {
            $pdo->query("UPDATE pagar SET alerta = 'Sim' WHERE id = '$id'");
        }

        if ($api != 'menuia') {
            $pdo->query("UPDATE pagar SET alerta = 'Sim' WHERE id = '$id'");
        }
    }
}

echo $contas_pagar_vencidas;

?>
