<?php 
@session_start();
require_once("../sistema/conexao.php");
$tabela = 'receber';
$id_usuario = 0;

$query_pgto = $pdo->query("SELECT * from receber where pago != 'Sim' and pago is not null and ref_pix is not null ");
$res_pgto = $query_pgto->fetchAll(PDO::FETCH_ASSOC);
$total_contas = @count($res_pgto);
for ($i_pgto = 0; $i_pgto < $total_contas; $i_pgto++) {
$id = $res_pgto[$i_pgto]['id'];
$ref_pix = $res_pgto[$i_pgto]['ref_pix'];
	
	if($api_pagamento == 'Asaas'){
			require("../asaas_contas/status.php");	
			echo $status_api.'<br>';		
		}else{
			require('../pagamentos/consultar_pagamento.php');     
			echo $status_api;

			if($status_api == 'approved'){
				require('../pagamentos/baixar_conta.php');  
			}
		}
	
	

}

echo '<br>';
echo 'Total Contas Receber <br>';
echo $total_contas;




//verificar pagamentos
$query_agd = $pdo->query("SELECT * FROM agendamentos_temp where ref_pix is not null");
$res_agd = $query_agd->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res_agd);
if($total_reg > 0){
	for($i_agd=0; $i_agd < $total_reg; $i_agd++){
		$ref_pix = $res_agd[$i_agd]['ref_pix'];

		if($api_pagamento == 'Asaas'){
			require("../asaas/status.php");			
		}else{
			require("../pagamentos/consultar_pagamento.php");
			if($status_api == 'approved'){
				require("../pagamentos/pagamento_aprovado.php");
			}
		}
		
	}
}

echo '<br><br><br>';
echo 'Total Agendamentos Temp <br>';
echo $total_reg;

 ?>