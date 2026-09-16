<?php 
require_once("../../../conexao.php");

$id = $_POST['grupo'];
echo '<select class="form-control sel2" id="item" name="item" style="width:100%;" onchange="alterarValor()">	';
$query = $pdo->query("SELECT * FROM itens_assinaturas where grupo = '$id' ORDER BY id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){	

	for($i=0; $i < $total_reg; $i++){
		echo '<option value="'.$res[$i]['id'].'">'.$res[$i]['nome'].'</option>';
	}

}
echo '</select>';
?>

