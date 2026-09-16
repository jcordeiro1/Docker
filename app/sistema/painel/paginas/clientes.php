<?php 
@session_start();
require_once("verificar.php");
require_once("../conexao.php");

$pag = 'clientes';

//verificar se ele tem a permissão de estar nessa página
if(@$clientes == 'ocultar'){
    echo "<script>window.location='../index.php'</script>";
    exit();
}

?>

 <div class="row top-50">
<div class="col-md-8 float-esq" >	
  <a class="btn btn-primary" onclick="inserir()" class="btn btn-primary btn-flat btn-pri"><i class="fa fa-plus" aria-hidden="true"></i> <span class="esc">Novo Cliente</span></a>
</div>
<div class="col-md-3 float-esq" >
	<input onkeyup="listarClientes()" class="form-control" type="text" name="buscar" id="buscar" placeholder="Buscar por CPF, Nome ou Telefone" style="border-radius: 5px">
	<input type="hidden" id="pagina">
</div>
<div class="col-md-1 float-esq" >
   <button onclick="listarClientes()" id="btn-buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
</div>

</div>


<div class="bs-example widget-shadow" style="padding:15px" id="listar">
	
</div>

<!-- Modal Inserir-->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true" >&times;</span>
				</button>
			</div>
			<form id="form_cli">
			<div class="modal-body">

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail1">Nome</label>
								<input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required>    
							</div> 	
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail1">Telefone</label>
								<input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone" >    
							</div> 	
						</div>
					
					</div>

					<div class="row">

						<div class="col-md-7">
							<div class="form-group">
								<label for="exampleInputEmail1">Cpf</label>
								<input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF" >    
							</div> 	
						</div>

							<div class="col-md-5">
							<div class="form-group">
								<label for="exampleInputEmail1">Cartões</label>
								<input type="number" class="form-control" id="cartao" name="cartao"  value="0">    
							</div> 	
						</div>
					</div>

							

					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label for="exampleInputEmail1">Endereço</label>
								<input type="text" class="form-control" id="endereco" name="endereco" placeholder="Rua X Número 1 Bairro xxx" >    
							</div> 	
						</div>


						<div class="col-md-4">
							<div class="form-group">
								<label for="exampleInputEmail1">Nascimento</label>
								<input type="date" class="form-control" id="data_nasc" name="data_nasc" >    
							</div> 	
						</div>
						
					</div>


					
						<input type="hidden" name="id" id="id">

					<br>
					<small><div id="mensagem" align="center"></div></small>
				</div>

				<div class="modal-footer">      
					<button type="submit" class="btn btn-primary">Salvar</button>
				</div>
			</form>

			
		</div>
	</div>
</div>


<!-- Modal Cobranca -->
<div class="modal fade" id="modalCobranca" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_cob"></span></h4>
				<button id="btn-fechar-cob" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="form_cob">
			<div class="modal-body">

				<div class="row">
						<div class="col-md-6">							
								<label>Produtos Venda</label>
								<select class="form-control sel2" name="pro" id="pro" onchange="alterar()" style="width: 100%">	
								 <option value="">Selecione Um Produto</option>							
								<?php 
									$query = $pdo->query("SELECT * from produtos  order by id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$linhas = @count($res);
									if($linhas > 0){
									for($i=0; $i<$linhas; $i++){
									
								 ?>

								  <option value="<?php echo $res[$i]['valor_venda'] ?>"><?php echo $res[$i]['nome'] ?></option>

								<?php } } ?>
									
								</select>								
						</div>

						<div class="col-md-6">							
								<label>Serviços</label>
								<select class="form-control sel6 " name="ser" id="ser" onchange="alterar2()" style="width: 100%">	
								 <option value="">Selecione Um Serviço</option>							
								<?php 
									$query = $pdo->query("SELECT * from servicos  order by id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$linhas = @count($res);
									if($linhas > 0){
									for($i=0; $i<$linhas; $i++){
									
								 ?>

								  <option value="<?php echo $res[$i]['valor'] ?>"><?php echo $res[$i]['nome'] ?></option>

								<?php } } ?>
									
								</select>								
						</div>

						
					</div>
				

					<div class="row">
						<div class="col-md-3">							
								<label>Valor</label>
								<input type="text" class="form-control" id="valor_cob" name="valor" placeholder="Valor" onkeyup="mascara_valor('valor_cob')" required>							
						</div>

						<div class="col-md-5">							
								<label>Parcelas <small><small>(Não Obrigatória)</small></small></label>
								<input type="number" class="form-control" id="parcelas_cob" name="parcelas" placeholder="Parcelas" value="1">							
						</div>

						<div class="col-md-4">							
								<label>Vencimento</label>
								<input type="date" class="form-control" id="data_venc_cob" name="data_venc" placeholder="Data de Vencimento" value="<?php echo $data_atual ?>">							
						</div>

						
					</div>
				

					<div class="row">

						<div class="col-md-4">						
								<label>Frequência</label>
								<select class="form-control" name="frequencia" id="frequencia_cob">								
								<?php 
									$query = $pdo->query("SELECT * from frequencias  order by id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$linhas = @count($res);
									if($linhas > 0){
									for($i=0; $i<$linhas; $i++){
								 ?>
								  <option value="<?php echo $res[$i]['dias'] ?>"><?php echo $res[$i]['frequencia'] ?></option>

								<?php } } ?>
									
								</select>	
						</div>

						<div class="col-md-8">							
								<label>Observações</label>
								<input type="text" class="form-control" id="obs_cob" name="obs" placeholder="Observações" >							
						</div>

						
					</div>
					<input type="hidden" class="form-control" id="id_cob" name="id">

				<br>
				<small><div id="mensagem_cob" align="center"></div></small>
			</div>
			<div class="modal-footer">       
				<button id="btn_cobranca" type="submit" class="btn btn-primary">Salvar</button>
			</div>
			</form>
		</div>
	</div>
</div>


<!-- Modal Dados-->
<div class="modal fade" id="modalDados" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel"><span id="nome_dados"></span></h4>
				<button id="btn-fechar-perfil" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true" >&times;</span>
				</button>
			</div>
			
			<div class="modal-body">

				<div class="row" style="border-bottom: 1px solid #cac7c7;">
					<div class="col-md-6">							
						<span><b>Telefone: </b></span>
						<span id="telefone_dados"></span>
					</div>	

					<div class="col-md-6">							
						<span><b>Cartões: </b></span>
						<span id="cartoes_dados"></span>							
					</div>
									

				</div>


				<div class="row" style="border-bottom: 1px solid #cac7c7;">
					<div class="col-md-6">							
						<span><b>Cadastro: </b></span>
						<span id="data_cad_dados"></span>							
					</div>
					<div class="col-md-6">							
						<span><b>Nascimento: </b></span>
						<span id="data_nasc_dados"></span>
					</div>					

				</div>


				<div class="row" style="border-bottom: 1px solid #cac7c7;">
					<div class="col-md-6">							
						<span><b>Data Retorno: </b></span>
						<span id="retorno_dados"></span>							
					</div>
					<div class="col-md-6">							
						<span><b>Último Serviço: </b></span>
						<span id="servico_dados"></span>
					</div>					

				</div>


				<div class="row" style="border-bottom: 1px solid #cac7c7;">
					
					<div class="col-md-12">							
						<span><b>Endereço: </b></span>
						<span id="endereco_dados"></span>
					</div>					

				</div>


				<br>

				<small><table class="table table-hover">
	<thead> 
	<tr> 
	<th>Último Serviço</th>	
	<th class="esc">Data</th> 
	<th class="esc">Valor</th> 	
	<th class="esc">OBS</th> 	
	
	</tr> 
	</thead> 
	<tbody>
	<td><span id="servico_dados_tab"></span></td>
	<td><span id="data_dados_tab"></span></td>
	<td><span id="valor_dados_tab"></span></td>
	<td><span id="obs_dados_tab"></span></td>
	</tbody>
	</table></small>

	<hr>

	<div id="listar-debitos">

	</div>

	<div class="row" style="overflow: scroll; height:200px; scrollbar-width: thin;">
					<div style="border-bottom: 1px solid #000"><small>Cobranças do Cliente</small></div>

					<small><div id="listar_cobrancas">
						
					</div></small>
				</div>
			

			</div>

			
		</div>
	</div>
</div>

<!-- Modal Parcelas -->
<div class="modal fade" id="modalParcelas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">	
		<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_baixar">Parcelas</span></h4>
				<button id="btn-fechar-parcelas" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>			
			<div class="modal-body">
						
				<small><div id="listar_parcelas" style="overflow: scroll; height:380px; scrollbar-width: thin;"></div></small>

				<input type="hidden" id="id_emprestimo">
				<input type="hidden" id="id_cobranca">						
			</div>
			
		</div>
	</div>
</div>

<!-- Modal Contrato-->
<div class="modal fade" id="modalContrato" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_contrato"></span></h4>
				<button id="btn-fechar-conta" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>	
			<form id="form-contrato">	
			<div class="modal-body">

					<div>
						<textarea name="contrato" id="contrato" class="textareag"> </textarea>
					</div>
					<input type="hidden" name="id" id="id_contrato">

					<small><div id="mensagem-contrato" align="center"></div></small>
					
			</div>
			<div class="modal-footer">       
				<button type="submit" class="btn btn-primary">Gerar Relatório</button>
			</div>	
			</form>		

				

		</div>
	</div>
</div>


<!-- Modal Baixar -->
<div class="modal fade" id="modalBaixar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_baixar">Baixar Parcela</span></h4>
				<button id="btn-fechar-baixar" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form id="form_baixar">
			<div class="modal-body">
				

					<div class="row">
						<div class="col-md-4">							
								<label>Valor</label>
								<input type="text" class="form-control" id="valor_baixar" name="valor_baixar" placeholder="Valor" readonly="">							
						</div>						
					

							<div class="col-md-4">							
								<label>Total Juros</label>
								<input type="text" class="form-control" id="juros_baixar" name="juros_baixar" placeholder="Júros se Houver" onkeyup="calcular()">							
						</div>

						<div class="col-md-4">							
								<label>Multa R$</label>
								<input type="text" class="form-control" id="multa_baixar" name="multa_baixar" placeholder="Multa se Houver" onkeyup="calcular()" >							
						</div>


						
					</div>

				
					<div class="row">

						<div class="col-md-4">							
								<label>Data Pgto</label>
								<input type="date" class="form-control" id="data_baixa" name="data_baixa" placeholder="Data de Pagamento" value="<?php echo $data_atual ?>">							
						</div>

						<div class="col-md-4">							
								<label>Forma Pgto..</label>
								<select name="forma_pgto" id="forma_pgto" class="form-control" onchange="calcular()" >
									<option value="">Selecionar Forma PGTO </option>
									<?php 
										$query = $pdo->query("SELECT * from formas_pgto order by id asc");
						$res = $query->fetchAll(PDO::FETCH_ASSOC);
						$linhas = @count($res);
						for($i=0; $i<$linhas; $i++){
							echo '<option value="'.$res[$i]['nome'].'">'.$res[$i]['nome'].'</option>';		
						}

									 ?>
								</select>
															
						</div>

						<div class="col-md-4">							
								<label>Valor Final</label>
								<input type="text" class="form-control" id="valor_final" name="valor_final" placeholder="Total Pago" onkeyup="mascara_valor('valor_final')" required>							
						</div>	

						
					</div>


					<div class="row" align="right">
						<div class="col-md-12">		
                            <input type="checkbox" class="form-checkbox" id="residuo" name="residuo" value="Sim" style="display:inline-block;">
                            <label style="display:inline-block;"><small>Resíduo Próxima Parcela</small></label>
                        </div>


						
					</div>

					<input type="hidden" class="form-control" id="id_baixar" name="id">					

				<br>
				<small><div id="mensagem_baixar" align="center"></div></small>
			</div>
			<div class="modal-footer">       
				<button id="btn_salvar_baixar" type="submit" class="btn btn-primary">Baixar</button>
			</div>
			</form>
		</div>
	</div>
</div>


<form action="rel/recibo_class.php" method="post" style="display:none" target="_blank">
    <input type="hidden" name="id" value="<?=$id_conta;?>" id="id_conta_recibo">
    <input type="hidden" name="enviar" value="Sim">
    <button id="btn_form" type="submit"></button>
</form>




<script type="text/javascript">var pag = "<?=$pag?>"</script>
<script src="js/ajax.js"></script>

<script src="//js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

<script type="text/javascript">
	$(document).ready( function () {
		listarClientes()
		 $('.sel2').select2({
		 	dropdownParent: $('#form_cob')
	      
	    });

		  $('.sel6').select2({
		 	dropdownParent: $('#form_cob')
	      
	    });
	} );

</script>


<script type="text/javascript">
	
function listarClientes(pagina){

	$("#pagina").val(pagina);

  var busca = $("#buscar").val();
    $.ajax({
        url: 'paginas/' + pag + "/listar.php",
        method: 'POST',
        data: {busca, pagina},
        dataType: "html",

        success:function(result){
            $("#listar").html(result);
           
        }
    });
}

function listarDebitos(id){	 

    $.ajax({
        url: 'paginas/' + pag + "/listar-debitos.php",
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(result){
            $("#listar-debitos").html(result);
           
        }
    });
}
</script>




<script type="text/javascript">

$("#form_baixar").submit(function () {

	var id_emp = $('#id_emprestimo').val();
	var id_cob = $('#id_cobranca').val();
    event.preventDefault();
    var formData = new FormData(this);

    $('#mensagem_baixar').text('Carregando!!');
      $('#btn_salvar_baixar').hide();
  

    $.ajax({
        url: 'paginas/' + pag + "/baixar.php",
        type: 'POST',
        data: formData,

        success: function (mensagem) {
        	var split = mensagem.split("*");
            $('#mensagem_baixar').text('');
            $('#mensagem_baixar').removeClass()
            if (split[0].trim() == "Salvo com Sucesso") {
            	
                $('#btn-fechar-baixar').click();
                if(id_emp != ""){
                	 mostrarParcelasEmp(id_emp)
                }

                if(id_cob != ""){
                	 mostrarParcelas(id_cob);
                }

                $('#id_conta_recibo').val(split[1]);
                $("#btn_form").click();
                
               limparCampos();
                   	   

            } else {

                $('#mensagem_baixar').addClass('text-danger')
                $('#mensagem_baixar').text(split[0])
            }

              $('#btn_salvar_baixar').show();


        },

        cache: false,
        contentType: false,
        processData: false,

    });

});
</script>



<script type="text/javascript">
	
$("#form_cli").submit(function () {

    event.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: 'paginas/' + pag + "/salvar.php",
        type: 'POST',
        data: formData,

        success: function (mensagem) {
            $('#mensagem').text('');
            $('#mensagem').removeClass()
            if (mensagem.trim() == "Salvo com Sucesso") {

                $('#btn-fechar').click();

                var pagina = $("#pagina").val();
                listarClientes(pagina)          

            } else {

                $('#mensagem').addClass('text-danger')
                $('#mensagem').text(mensagem)
            }


        },

        cache: false,
        contentType: false,
        processData: false,

    });

});





function excluir(id){
    $.ajax({
        url: 'paginas/' + pag + "/excluir.php",
        method: 'POST',
        data: {id},
        dataType: "text",

        success: function (mensagem) {            
            if (mensagem.trim() == "Excluído com Sucesso") {                
                 var pagina = $("#pagina").val();
                listarClientes(pagina)              
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }

        },      

    });
}



	function listarTextoContrato(id){
	
    $.ajax({
        url: 'paginas/' + pag + "/texto-contrato.php",
        method: 'POST',
        data: {id},
        dataType: "html",

        success:function(result){            
            nicEditors.findEditor("contrato").setContent(result);	          
        }
    });
}


function alterar(){
	

	 var produt = $("#pro").val();

	 let valorFormatado = produt.toString().replace('.', ',');

	 $("#valor_cob").val(valorFormatado);
	 $("#pro").val('');


}

function alterar2(){
	

	 var ser = $("#ser").val();

	 let valorFormatado = ser.toString().replace('.', ',');

	 $("#valor_cob").val(valorFormatado);
	 $("#pro").val('');

}




$("#form-contrato").submit(function () {
	var id_emp = $('#id_contrato').val();
    event.preventDefault();
    nicEditors.findEditor('contrato').saveContent();
    var formData = new FormData(this);

    $.ajax({
        url: 'paginas/' + pag + "/salvar-contrato.php",
        type: 'POST',
        data: formData,

        success: function (mensagem) {
            $('#mensagem-contrato').text('');
            $('#mensagem-contrato').removeClass()
            if (mensagem.trim() == "Salvo com Sucesso") {                
                   
                let a= document.createElement('a');
                a.target= '_blank';
                a.href= 'rel/contrato_servico_class.php?id=' + id_emp;
                a.click();  	 

            } else {

                $('#mensagem-contrato').addClass('text-danger')
                $('#mensagem-contrato').text(mensagem)
            }


        },

        cache: false,
        contentType: false,
        processData: false,

    });

});

</script>



<script type="text/javascript">
	function baixar(id, cliente){
    $.ajax({
        url: 'paginas/receber/baixar.php',
        method: 'POST',
        data: {id},
        dataType: "text",

        success: function (mensagem) {            
            if (mensagem.trim() == "Baixado com Sucesso") {                
                listarDebitos(cliente);                
            } else {
                    $('#mensagem-excluir-baixar').addClass('text-danger')
                    $('#mensagem-excluir-baixar').text(mensagem)
                }

        },      

    });
}

</script>

<script type="text/javascript">
	function listarCobrancas(id){	

		 $.ajax({
	        url: 'paginas/' + pag + "/listar_cobrancas.php",
	        method: 'POST',
	        data: {id},
	        dataType: "html",

	        success:function(result){
	            $("#listar_cobrancas").html(result);
	           
	        }
	    });

	}
</script>



<script type="text/javascript">
	

$("#form_cob").submit(function () {

	$('#mensagem_cob').text('Carregando');
	$('#btn_cobranca').hide();

    event.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: 'paginas/' + pag + "/recorrencia.php",
        type: 'POST',
        data: formData,

        success: function (mensagem) {
            $('#mensagem_cob').text('');
            $('#mensagem_cob').removeClass()
            if (mensagem.trim() == "Salvo com Sucesso") {
            	
                $('#btn-fechar-cob').click();
                limparCampos();
                      

            } else {

                $('#mensagem_cob').addClass('text-danger')
                $('#mensagem_cob').text(mensagem)
            }

            $('#btn_cobranca').show();

        },

        cache: false,
        contentType: false,
        processData: false,

    });

});
</script>


<script type="text/javascript">
	function calcular(){			
	var valor = $('#valor_baixar').val();
	var multa = $('#multa_baixar').val();
	var juros = $('#juros_baixar').val();
	var forma_pgto = $('#forma_pgto').val();	

	 $.ajax({
	        url: 'paginas/' + pag + "/calcular.php",
	        method: 'POST',
	        data: {valor, multa, juros, forma_pgto},
	        dataType: "html",

	        success:function(result){
	             $('#valor_final').val(result);
	        }
	    });

	

	}
</script>
<script type="text/javascript">

	$("#form-baixar").submit(function () {

		var cliente = $('#cliente_baixar').val()

		$('#btn_baixar').hide();
		$('#mensagem-baixar').text('Baixando!!');

    event.preventDefault();
    var formData = new FormData(this);

    $.ajax({
        url: 'paginas/receber/baixar.php',
        type: 'POST',
        data: formData,

        success: function (mensagem) {
            $('#mensagem-baixar').text('');
            $('#mensagem-baixar').removeClass()
            if (mensagem.trim() == "Baixado com Sucesso") {

                $('#btn-fechar-baixar').click();
                listarDebitos(cliente);          

            } else {

                $('#mensagem-baixar').addClass('text-danger')
                    $('#mensagem-baixar').text(mensagem)
            }

             $('#btn_baixar').show();


        },

        cache: false,
        contentType: false,
        processData: false,

    });

});


</script>