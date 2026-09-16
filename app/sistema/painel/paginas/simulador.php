<?php 
@session_start();
require_once("verificar.php");
require_once("../conexao.php");

$pag = 'simulador';

//verificar se ele tem a permissão de estar nessa página
if(@$simulador == 'ocultar'){
    echo "<script>window.location='../index.php'</script>";
    exit();
}

?>

<div class="row top-50">
	<div class="col-md-8 float-esq">	
		<a class="btn btn-primary" onclick="inserir()" class="btn btn-primary btn-flat btn-pri">
			<i class="fa fa-plus" aria-hidden="true"></i> <span class="esc">Nova Simulação</span>
		</a>
	</div>
	<div class="col-md-3 float-esq">
		<input onkeyup="listarSimulacoes()" class="form-control" type="text" name="buscar" id="buscar" placeholder="Buscar por Cliente ou Prótese" style="border-radius: 5px">
		<input type="hidden" id="pagina">
	</div>
	<div class="col-md-1 float-esq">
		<button onclick="listarSimulacoes()" id="btn-buscar" class="btn btn-primary"><i class="fa fa-search"></i></button>
	</div>
</div>

<div class="bs-example widget-shadow" style="padding:15px" id="listar">
	
</div>

<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><span id="titulo_inserir"></span></h4>
				<button id="btn-fechar" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<form id="form_simulacao" method="post" enctype="multipart/form-data">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Cliente</label>
								<select class="form-control sel2" name="cliente" id="cliente" style="width:100%" required>
									<option value="">Selecione um Cliente</option>
									<?php 
									$query = $pdo->query("SELECT * FROM clientes order by nome asc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$linhas = @count($res);
									if($linhas > 0){
										for($i=0; $i<$linhas; $i++){
									?>
									<option value="<?php echo $res[$i]['id'] ?>"><?php echo $res[$i]['nome'] ?></option>
									<?php } } ?>
								</select>
							</div> 	
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Prótese</label>
								<select class="form-control sel2" name="id_protese" id="id_protese" style="width:100%" required>
									<option value="">Selecione uma Prótese</option>
									<?php 
									$query = $pdo->query("SELECT p.*, c.nome as nome_cliente FROM proteses p INNER JOIN clientes c ON p.cliente = c.id order by p.id desc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$linhas = @count($res);
									if($linhas > 0){
										for($i=0; $i<$linhas; $i++){
											$descricao = $res[$i]['nome_cliente'].' - '.$res[$i]['modelo'];
									?>
									<option value="<?php echo $res[$i]['id'] ?>"><?php echo $descricao ?></option>
									<?php } } ?>
								</select>
							</div> 	
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Foto Original</label>
								<input type="file" class="form-control" id="foto_original" name="foto_original">
								<small id="nome_foto_original_atual" class="text-muted"></small>
								<div id="area_preview_foto_original" style="margin-top: 8px; display:none;">
									<img id="preview_foto_original" src="" style="max-width:100%; max-height:160px; border:1px solid #ddd; padding:3px; border-radius:4px;">
								</div>
							</div> 	
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Imagem Simulada</label>
								<input type="file" class="form-control" id="imagem_simulada" name="imagem_simulada">
								<small id="nome_imagem_simulada_atual" class="text-muted"></small>
								<div id="area_preview_imagem_simulada" style="margin-top: 8px; display:none;">
									<img id="preview_imagem_simulada" src="" style="max-width:100%; max-height:160px; border:1px solid #ddd; padding:3px; border-radius:4px;">
								</div>
							</div> 	
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Data da Simulação</label>
								<input type="date" class="form-control" id="data_simulacao" name="data_simulacao" value="<?php echo date('Y-m-d') ?>" required>
							</div> 	
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>Observações</label>
								<input type="text" class="form-control" id="observacoes" name="observacoes" placeholder="Observações">
							</div> 	
						</div>
					</div>

					<input type="hidden" name="id" id="id">
					<input type="hidden" name="foto_original_atual" id="foto_original_atual">
					<input type="hidden" name="imagem_simulada_atual" id="imagem_simulada_atual">

					<hr>

					<div class="row">
						<div class="col-md-6">
							<button id="btnGerarIA" type="button" class="btn btn-warning btn-block" onclick="gerarIAmodal()">
								<i id="iconGerarIA" class="fa fa-magic"></i> Gerar com IA
							</button>
						</div>

						<div class="col-md-6">
							<button type="button" class="btn btn-info btn-block" onclick="abrirCanvas()">
								<i class="fa fa-image"></i> Abrir Canvas
							</button>
						</div>
					</div>

					<br>
					<small><div id="mensagem_ia" align="center"></div></small>
					<small><div id="mensagem" align="center"></div></small>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Salvar</button>
				</div>
			</form>
			
		</div>
	</div>
</div>

<div class="modal fade" id="modalDados" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel"><span id="nome_dados"></span></h4>
				<button id="btn-fechar-perfil" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true" >&times;</span>
				</button>
			</div>
			
			<div class="modal-body">

				<input type="hidden" id="telefone_whatsapp_cliente">

				<div class="row" style="margin-bottom: 15px">
					<div class="col-md-6" align="center">
						<label>Foto Original</label><br>
						<img id="foto_original_dados" src="" width="220px">
					</div>
					<div class="col-md-6" align="center">
						<label>Imagem Simulada</label><br>
						<img id="imagem_simulada_dados" src="" width="220px">
						<div style="margin-top: 10px;">
							<a href="javascript:void(0)" onclick="enviarImagemWhatsapp()" class="btn btn-success btn-sm">
								<i class="fa fa-whatsapp"></i> Enviar no WhatsApp
							</a>
						</div>
					</div>
				</div>

				<div class="row" style="border-bottom: 1px solid #cac7c7;">
					<div class="col-md-4">							
						<span><b>Cliente: </b></span>
						<span id="cliente_dados"></span>
					</div>	

					<div class="col-md-4">							
						<span><b>Prótese: </b></span>
						<span id="protese_dados"></span>							
					</div>

					<div class="col-md-4">							
						<span><b>Data: </b></span>
						<span id="data_simulacao_dados"></span>							
					</div>
				</div>

				<div class="row" style="border-bottom: 1px solid #cac7c7;">
					<div class="col-md-12">							
						<span><b>Observações: </b></span>
						<span id="observacoes_dados"></span>
					</div>					
				</div>

			</div>
		</div>
	</div>
</div>

<script type="text/javascript">var pag = "<?=$pag?>"</script>
<script src="js/ajax.js"></script>

<script type="text/javascript">
	$(document).ready( function () {
		listarSimulacoes();

		$('.sel2').select2({
			dropdownParent: $('#modalForm')
		});
	} );
</script>

<script type="text/javascript">
function listarSimulacoes(pagina){

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
</script>

<script type="text/javascript">
function mostrarNomeArquivosAtuais(){
	var foto_original_atual = $('#foto_original_atual').val();
	var imagem_simulada_atual = $('#imagem_simulada_atual').val();

	if(foto_original_atual != ""){
		$('#nome_foto_original_atual').text('Arquivo atual: ' + foto_original_atual);
	}else{
		$('#nome_foto_original_atual').text('');
	}

	if(imagem_simulada_atual != ""){
		$('#nome_imagem_simulada_atual').text('Arquivo atual: ' + imagem_simulada_atual);
	}else{
		$('#nome_imagem_simulada_atual').text('');
	}
}
</script>

<script type="text/javascript">
function mostrarPreviewAtual(){
	var foto_original_atual = $('#foto_original_atual').val();
	var imagem_simulada_atual = $('#imagem_simulada_atual').val();

	if(foto_original_atual != ""){
		$('#preview_foto_original').attr('src', 'images/simulacoes/' + foto_original_atual + '?v=' + new Date().getTime());
		$('#area_preview_foto_original').show();
	}else{
		$('#preview_foto_original').attr('src', '');
		$('#area_preview_foto_original').hide();
	}

	if(imagem_simulada_atual != ""){
		$('#preview_imagem_simulada').attr('src', 'images/simulacoes/' + imagem_simulada_atual + '?v=' + new Date().getTime());
		$('#area_preview_imagem_simulada').show();
	}else{
		$('#preview_imagem_simulada').attr('src', '');
		$('#area_preview_imagem_simulada').hide();
	}
}
</script>

<script type="text/javascript">
function mostrarPreviewArquivo(input, idImagem, idArea){
	if(input.files && input.files[0]){
		var reader = new FileReader();

		reader.onload = function(e){
			$(idImagem).attr('src', e.target.result);
			$(idArea).show();
		}

		reader.readAsDataURL(input.files[0]);
	}
}
</script>

<script type="text/javascript">
$('#foto_original').change(function(){
	var arquivo = $(this).val().split('\\').pop();

	if(arquivo != ""){
		$('#nome_foto_original_atual').text('Novo arquivo: ' + arquivo);
		mostrarPreviewArquivo(this, '#preview_foto_original', '#area_preview_foto_original');
	}else{
		mostrarNomeArquivosAtuais();
		mostrarPreviewAtual();
	}
});
</script>

<script type="text/javascript">
$('#imagem_simulada').change(function(){
	var arquivo = $(this).val().split('\\').pop();

	if(arquivo != ""){
		$('#nome_imagem_simulada_atual').text('Novo arquivo: ' + arquivo);
		mostrarPreviewArquivo(this, '#preview_imagem_simulada', '#area_preview_imagem_simulada');
	}else{
		mostrarNomeArquivosAtuais();
		mostrarPreviewAtual();
	}
});
</script>

<script type="text/javascript">
$('#modalForm').on('shown.bs.modal', function () {
	mostrarNomeArquivosAtuais();
	mostrarPreviewAtual();
});
</script>

<script type="text/javascript">
$('#modalForm').on('hidden.bs.modal', function () {
	$('#nome_foto_original_atual').text('');
	$('#nome_imagem_simulada_atual').text('');
	$('#preview_foto_original').attr('src', '');
	$('#preview_imagem_simulada').attr('src', '');
	$('#area_preview_foto_original').hide();
	$('#area_preview_imagem_simulada').hide();
	$('#foto_original').val('');
	$('#imagem_simulada').val('');
	$('#mensagem').text('');
	$('#mensagem_ia').text('');
	$('#mensagem').removeClass();
	$('#mensagem_ia').removeClass();
	$('#btnGerarIA').data('processando', false);
	$('#btnGerarIA').prop('disabled', false);
	$('#iconGerarIA').removeClass();
	$('#iconGerarIA').addClass('fa fa-magic');
});
</script>

<script type="text/javascript">
$("#form_simulacao").submit(function () {

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
				listarSimulacoes(pagina);

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
</script>

<script type="text/javascript">
function excluir(id){
	$.ajax({
		url: 'paginas/' + pag + "/excluir.php",
		method: 'POST',
		data: {id},
		dataType: "text",

		success: function (mensagem) {            
			if (mensagem.trim() == "Excluído com Sucesso") {                
				var pagina = $("#pagina").val();
				listarSimulacoes(pagina);
			} else {
				$('#mensagem-excluir').addClass('text-danger')
				$('#mensagem-excluir').text(mensagem)
			}

		},      

	});
}
</script>

<script type="text/javascript">
function gerarIAmodal(){

	var id = $('#id').val();
	var botao = $('#btnGerarIA');
	var icone = $('#iconGerarIA');

	$('#mensagem_ia').text('');
	$('#mensagem_ia').removeClass();

	if(id == ""){
		$('#mensagem_ia').addClass('text-danger');
		$('#mensagem_ia').text('Primeiro salve o registro para gerar a simulação com IA');
		return;
	}

	if(botao.data('processando') == true){
		return;
	}

	botao.data('processando', true);
	botao.prop('disabled', true);

	icone.removeClass();
	icone.addClass('fa fa-spinner fa-spin');

	$('#mensagem_ia').addClass('text-info');
	$('#mensagem_ia').text('Gerando simulação com IA...');

	$.ajax({
		url: 'paginas/simulador/gerar_ia.php',
		method: 'POST',
		data: {id},
		dataType: "text",

		success: function (mensagem) {

			botao.data('processando', false);
			botao.prop('disabled', false);

			icone.removeClass();
			icone.addClass('fa fa-magic');

			$('#mensagem_ia').removeClass();

			if (mensagem.trim() == "Salvo com Sucesso") {

				$('#mensagem_ia').addClass('text-success');
				$('#mensagem_ia').text('Simulação gerada com sucesso');

				var pagina = $("#pagina").val();
				listarSimulacoes(pagina);

				$.ajax({
					url: 'paginas/simulador/buscar.php',
					method: 'POST',
					data: {id},
					dataType: "json",

					success: function (dados) {

						if(dados.foto_original != undefined){
							$('#foto_original_atual').val(dados.foto_original);
						}

						if(dados.imagem_simulada != undefined){
							$('#imagem_simulada_atual').val(dados.imagem_simulada);
						}

						if(typeof mostrarNomeArquivosAtuais === 'function'){
							mostrarNomeArquivosAtuais();
						}

						if(typeof mostrarPreviewAtual === 'function'){
							mostrarPreviewAtual();
						}
					}
				});

			}else{

				$('#mensagem_ia').addClass('text-danger');
				$('#mensagem_ia').text(mensagem);

			}
		},

		error: function () {

			botao.data('processando', false);
			botao.prop('disabled', false);

			icone.removeClass();
			icone.addClass('fa fa-magic');

			$('#mensagem_ia').removeClass();
			$('#mensagem_ia').addClass('text-danger');
			$('#mensagem_ia').text('Erro ao processar a simulação com IA');

		}
	});
}
</script>

<script type="text/javascript">
function abrirCanvas(){
	var id = $('#id').val();

	if(id == ""){
		$('#mensagem_ia').removeClass();
		$('#mensagem_ia').addClass('text-danger');
		$('#mensagem_ia').text('Primeiro salve o registro para abrir o canvas');
		return;
	}

	window.open('paginas/simulador/canvas.php?id=' + id, '_blank');
}
</script>

<script type="text/javascript">
function enviarImagemWhatsapp(){
	var telefone = $('#telefone_whatsapp_cliente').val();
	var imagem = $('#imagem_simulada_dados').attr('src');
	var cliente = $('#cliente_dados').text();

	if(telefone == ""){
		alert('Cliente não possui telefone cadastrado.');
		return;
	}

	if(imagem == ""){
		alert('Nenhuma imagem simulada disponível.');
		return;
	}

	telefone = telefone.replace(/\D/g, '');

	if(telefone == ""){
		alert('Telefone do cliente inválido.');
		return;
	}

	var nome_imagem = imagem.split('/').pop().split('?')[0];
	var url_imagem = window.location.origin + '/sistema/painel/images/simulacoes/' + nome_imagem;

	var texto = 'Olá ' + cliente + ', segue sua simulação de prótese: ' + url_imagem;
	var url = '';

	if(/Android|iPhone|iPad|iPod/i.test(navigator.userAgent)){
		url = 'https://api.whatsapp.com/send?phone=55' + telefone + '&text=' + encodeURIComponent(texto);
	}else{
		url = 'https://web.whatsapp.com/send?phone=55' + telefone + '&text=' + encodeURIComponent(texto);
	}

	window.open(url, '_blank');
}
</script>