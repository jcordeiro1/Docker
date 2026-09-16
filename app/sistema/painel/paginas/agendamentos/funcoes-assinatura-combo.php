<?php

function buscarAssinaturaComboAtiva($pdo, $cliente, $servico){
	$query = $pdo->prepare("SELECT a.*, ia.servico as id_servico_item, s.combo_qtd_sessoes, s.combo_validade_dias
		FROM assinaturas a
		INNER JOIN itens_assinaturas ia ON a.item = ia.id
		INNER JOIN servicos s ON ia.servico = s.id
		WHERE a.cliente = :cliente
		AND ia.servico = :servico
		AND a.ativo = 'Sim'
		AND (a.cancelado IS NULL OR a.cancelado != 'Sim')
		AND a.vencimento >= curDate()
		AND s.combo_ativo = 'Sim'
		ORDER BY a.id DESC
		LIMIT 1");

	$query->bindValue(":cliente", $cliente, PDO::PARAM_INT);
	$query->bindValue(":servico", $servico, PDO::PARAM_INT);
	$query->execute();

	return $query->fetch(PDO::FETCH_ASSOC);
}



function saldoAssinaturaCombo($pdo, $assinatura, $total_sessoes){
	$query = $pdo->prepare("SELECT COUNT(*) as total 
		FROM assinaturas_consumos 
		WHERE assinatura = :assinatura");

	$query->bindValue(":assinatura", $assinatura, PDO::PARAM_INT);
	$query->execute();

	$res = $query->fetch(PDO::FETCH_ASSOC);

	$usadas = (int)$res['total'];
	$restantes = (int)$total_sessoes - $usadas;

	if($restantes < 0){
		$restantes = 0;
	}

	return array(
		'usadas' => $usadas,
		'restantes' => $restantes
	);
}



function baixarAssinaturaCombo($pdo, $assinatura, $cliente, $servico, $agendamento, $usuario, $obs = ''){
	$query = $pdo->prepare("INSERT INTO assinaturas_consumos SET
		assinatura = :assinatura,
		cliente = :cliente,
		servico = :servico,
		agendamento = :agendamento,
		data_baixa = now(),
		usuario = :usuario,
		obs = :obs");

	$query->bindValue(":assinatura", $assinatura, PDO::PARAM_INT);
	$query->bindValue(":cliente", $cliente, PDO::PARAM_INT);
	$query->bindValue(":servico", $servico, PDO::PARAM_INT);
	$query->bindValue(":agendamento", $agendamento, PDO::PARAM_INT);
	$query->bindValue(":usuario", $usuario, PDO::PARAM_INT);
	$query->bindValue(":obs", $obs);

	return $query->execute();
}

?>