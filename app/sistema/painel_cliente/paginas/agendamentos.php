<?php 
@session_start();
require_once("verificar.php");
require_once("../conexao.php");

$data_hoje = date('Y-m-d');


$pag = 'agendamentos';

?>


<div class="bs-example widget-shadow" style="padding:15px" id="listar">
	
</div>



<script type="text/javascript">var pag = "<?=$pag?>"</script>
<script src="js/ajax.js"></script>


<script type="text/javascript">
	function excluirAgd(id){
    $.ajax({
        url: '../../ajax/excluir.php',
        method: 'POST',
        data: {id},
        dataType: "text",

        success: function (mensagem) {  

             
            if (mensagem.trim() == "Excluído com Sucesso") {                
                listar();                
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
                 listar();  
            }

        },      

    });
}
</script>