<?php 
@session_start();
require_once("verificar.php");
require_once("../conexao.php");

$pag = 'receber';

//percorrer para verificar se tem conta paga
$query = $pdo->query("SELECT * FROM receber where pago = 'Não' and ref_pix is not null ORDER BY id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){	
	for($i=0; $i < $total_reg; $i++){

		$ref_pix = $res[$i]['ref_pix'];
		$id = $res[$i]['id'];

		//verificar se o pagamento está aprovado
		if($ref_pix != ""){
			require_once("../../pagamentos/consultar_pagamento.php");
			if($status_api == 'approved'){
					require_once("../../pagamentos/baixar_conta.php");
				}			

		}

	}
}

?>




<div class="bs-example widget-shadow" style="padding:15px">

	<hr>
	<div id="listar">

	</div>
	
</div>





<script type="text/javascript">var pag = "<?=$pag?>"</script>
<script src="js/ajax.js"></script>

