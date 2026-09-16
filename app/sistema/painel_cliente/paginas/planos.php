<?php 
@session_start();
require_once("verificar.php");
require_once("../conexao.php");

$data_hoje = date('Y-m-d');


$pag = 'planos';

?>


<div class="bs-example widget-shadow" style="padding:15px" id="listar">
	
</div>





<script type="text/javascript">var pag = "<?=$pag?>"</script>
<script src="js/ajax.js"></script>

