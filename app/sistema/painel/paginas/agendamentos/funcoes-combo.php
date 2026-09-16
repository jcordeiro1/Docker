<?php

function buscarComboAtivo($pdo, $cliente, $servico){
	$query = $pdo->prepare("SELECT * FROM clientes_combos 
		WHERE cliente = :cliente 
		AND servico = :servico 
		AND status = 'Ativo' 
		AND sessoes_restantes > 0
		AND (data_fim IS NULL OR data_fim >= curDate())
		ORDER BY id ASC LIMIT 1");
	$query->bindValue(":cliente", $cliente, PDO::PARAM_INT);
	$query->bindValue(":servico", $servico, PDO::PARAM_INT);
	$query->execute();
	return $query->fetch(PDO::FETCH_ASSOC);
}

function baixarCombo($pdo, $id_combo, $cliente, $servico, $agendamento, $usuario, $obs = ''){
	$query = $pdo->prepare("SELECT * FROM clientes_combos WHERE id = :id LIMIT 1");
	$query->bindValue(":id", $id_combo, PDO::PARAM_INT);
	$query->execute();
	$res = $query->fetch(PDO::FETCH_ASSOC);

	if(!$res){
		return false;
	}

	$sessoes_usadas = (int)$res['sessoes_usadas'] + 1;
	$sessoes_restantes = (int)$res['sessoes_restantes'] - 1;
	$status = 'Ativo';

	if($sessoes_restantes <= 0){
		$sessoes_restantes = 0;
		$status = 'Finalizado';
	}

	$query = $pdo->prepare("UPDATE clientes_combos SET 
		sessoes_usadas = :sessoes_usadas,
		sessoes_restantes = :sessoes_restantes,
		status = :status
		WHERE id = :id");
	$query->bindValue(":sessoes_usadas", $sessoes_usadas, PDO::PARAM_INT);
	$query->bindValue(":sessoes_restantes", $sessoes_restantes, PDO::PARAM_INT);
	$query->bindValue(":status", $status);
	$query->bindValue(":id", $id_combo, PDO::PARAM_INT);
	$query->execute();

	$query = $pdo->prepare("INSERT INTO clientes_combos_itens SET 
		cliente_combo = :cliente_combo,
		cliente = :cliente,
		servico = :servico,
		agendamento = :agendamento,
		usuario = :usuario,
		data_baixa = now(),
		obs = :obs");
	$query->bindValue(":cliente_combo", $id_combo, PDO::PARAM_INT);
	$query->bindValue(":cliente", $cliente, PDO::PARAM_INT);
	$query->bindValue(":servico", $servico, PDO::PARAM_INT);
	$query->bindValue(":agendamento", $agendamento, PDO::PARAM_INT);
	$query->bindValue(":usuario", $usuario, PDO::PARAM_INT);
	$query->bindValue(":obs", $obs);
	$query->execute();

	return true;
}
?>