<?php 
@session_start();
require_once("verificar.php");
require_once("../conexao.php");

$data_hoje = date('Y-m-d');

//verificar se ele tem a permissão de estar nessa página
if(@$planos == 'ocultar'){
    echo "<script>window.location='../index.php'</script>";
    exit();
}

$pag = 'planos';

?>

<div class="row">   
<div class="col-md-6">
	<a class="btn btn-primary" onclick="inserir()" class="btn btn-primary btn-flat btn-pri"><i class="fa fa-plus" aria-hidden="true"></i> Novo Plano / Assinatura</a>
</div>   
<div class="col-md-6" align="right">
	<a class="btn btn-success btn-flat btn-pri" href="rel/assinaturas_class.php" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Relatório</a>
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
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<form id="form">
				<div class="modal-body">

					<div class="row">

						<div class="col-md-6">
							<div class="form-group">
								<label>Assinaturas</label>
								<select onchange="listarPlanos()" class="form-control sel2" id="grupo" name="grupo" style="width:100%;">

									<?php 
									$query = $pdo->query("SELECT * FROM grupo_assinaturas ORDER BY id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);

									if($total_reg > 0){
										for($i=0; $i < $total_reg; $i++){
											echo '<option value="'.$res[$i]['id'].'">'.$res[$i]['nome'].'</option>';
										}
									}
									?>

								</select>
							</div>
						</div>


						<div class="col-md-6">
							<div class="form-group">
								<label>Planos</label>
								<div id="listar_planos"></div>
							</div>
						</div>

					</div>



					<div class="row">

						<div class="col-md-8">
							<div class="form-group">
								<label>Cliente</label>
								<select class="form-control sel2" id="cliente" name="cliente" style="width:100%;" required>
									<option value="">Selecionar Cliente</option>

									<?php 
									$query = $pdo->query("SELECT * FROM clientes ORDER BY id desc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);

									if($total_reg > 0){
										for($i=0; $i < $total_reg; $i++){
											echo '<option value="'.$res[$i]['id'].'">'.$res[$i]['nome'].'</option>';
										}
									}
									?>

								</select>
							</div>
						</div>


						<div class="col-md-4">
							<div class="form-group">
								<label>Valor</label>
								<input type="text" class="form-control" id="valor" name="valor" placeholder="Valor">
							</div>
						</div>

					</div>



					<div class="row">

						<div class="col-md-8">
							<div class="form-group">
								<label>Frequência</label>
								<select class="form-control sel2" id="frequencia" name="frequencia" style="width:100%;" required>

									<?php 
									$query = $pdo->query("SELECT * FROM frequencias ORDER BY id asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);

									if($total_reg > 0){
										for($i=0; $i < $total_reg; $i++){
											echo '<option value="'.$res[$i]['dias'].'">'.$res[$i]['frequencia'].'</option>';
										}
									}
									?>

								</select>
							</div>
						</div>


						<div class="col-md-4">
							<div class="form-group">
								<label>Data</label>
								<input type="date" class="form-control" id="vencimento" name="vencimento" value="<?php echo date('Y-m-d') ?>">
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




<!-- Modal Baixar-->
<div class="modal fade" id="modalBaixar" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">

			<div class="modal-header">
				<h4 class="modal-title">Baixar Plano</h4>
				<button id="btn-fechar-baixar" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<form id="form-baixar">
				<div class="modal-body">

					<div class="row">

						<div class="col-md-4">
							<div class="form-group">
								<label>Valor</label>
								<input type="text" class="form-control" id="valor_baixar" name="valor" required>
							</div>
						</div>


						<div class="col-md-4">
							<div class="form-group">
								<label>Forma de Pagamento</label>
								<select class="form-control" id="pgto_baixar" name="pgto" required>

									<?php 
									$query = $pdo->query("SELECT * FROM formas_pgto");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);

									if($total_reg > 0){
										for($i=0; $i < $total_reg; $i++){
											echo '<option value="'.$res[$i]['nome'].'">'.$res[$i]['nome'].'</option>';
										}
									}
									?>

								</select>
							</div>
						</div>


						<div class="col-md-4">
							<div class="form-group">
								<label>Pago Em</label>
								<input type="date" class="form-control" id="data_pgto_baixar" name="data_pgto" value="<?php echo $data_hoje ?>">
							</div>
						</div>

					</div>

					<input type="hidden" name="id" id="id_baixar">

					<br>
					<small><div id="mensagem-baixar" align="center"></div></small>

				</div>

				<div class="modal-footer">      
					<button id="btn_baixar" type="submit" class="btn btn-success">Baixar</button>
				</div>
			</form>

		</div>
	</div>
</div>



<script type="text/javascript">
var pag = "<?=$pag?>";
</script>

<script src="js/ajax.js"></script>


<script type="text/javascript">
$(document).ready(function() {

	listarPlanos();

	setTimeout(function(){
		alterarValor();
	},500);

	$('.sel2').select2({
		dropdownParent: $('#modalForm')
	});

});


function listarPlanos(){
	var grupo = $('#grupo').val();

	$.ajax({
		url: 'paginas/' + pag + "/listar_planos.php",
		method: 'POST',
		data: {grupo},
		dataType: "html",
		success:function(result){
			$("#listar_planos").html(result);

			setTimeout(function(){
				alterarValor();
			},300);
		}
	});
}



function alterarValor(){
	var item = $('#item').val();

	$.ajax({
		url: 'paginas/' + pag + "/valor.php",
		method: 'POST',
		data: {item},
		dataType: "html",
		success:function(result){
			$("#valor").val(result);
		}
	});
}
</script>



<script type="text/javascript">
$("#form-baixar").submit(function () {

	$('#btn_baixar').hide();
	$('#mensagem-baixar').text('Baixando!!');

	event.preventDefault();
	var formData = new FormData(this);

	$.ajax({
		url: 'paginas/' + pag + "/baixar.php",
		type: 'POST',
		data: formData,

		success: function (mensagem) {

			$('#mensagem-baixar').text('');
			$('#mensagem-baixar').removeClass();

			if (mensagem.trim() == "Baixado com Sucesso") {

				$('#btn-fechar-baixar').click();
				listar();

			} else {

				$('#mensagem-baixar').addClass('text-danger');
				$('#mensagem-baixar').text(mensagem);

			}

			$('#btn_baixar').show();

		},

		cache: false,
		contentType: false,
		processData: false

	});

});
</script>