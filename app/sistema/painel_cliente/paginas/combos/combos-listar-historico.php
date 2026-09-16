<?php 
require_once("../../../conexao.php");
@session_start();
$id_usuario = @$_SESSION['id'];

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM clientes_combos where id = '$id' and cliente = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if($total_reg == 0){
	echo '<small>Nenhum histórico encontrado!</small>';
	exit();
}

$query = $pdo->query("SELECT * FROM clientes_combos_itens where cliente_combo = '$id' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if($total_reg > 0){

	echo '<small>';
	echo '<table class="table table-hover">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>Data</th>';
	echo '<th>Serviço</th>';
	echo '<th>Agendamento</th>';
	echo '<th>Obs</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';

	for($i=0; $i < $total_reg; $i++){
		$servico = $res[$i]['servico'];
		$agendamento = $res[$i]['agendamento'];
		$obs = $res[$i]['obs'];
		$data_baixa = $res[$i]['data_baixa'];

		$data_baixaF = date('d/m/Y H:i', strtotime($data_baixa));

		$query2 = $pdo->query("SELECT nome FROM servicos where id = '$servico'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$nome_servico = @$res2[0]['nome'];

		echo '<tr>';
		echo '<td>'.$data_baixaF.'</td>';
		echo '<td>'.$nome_servico.'</td>';
		echo '<td>#'.$agendamento.'</td>';
		echo '<td>'.$obs.'</td>';
		echo '</tr>';
	}

	echo '</tbody>';
	echo '</table>';
	echo '</small>';

}else{
	echo '<small>Nenhuma baixa encontrada para este combo.</small>';
}
?>