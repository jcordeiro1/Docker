<?php 
$pag = 'grupos_disparos';

// Verifica se tem permissão para acessar a página
if(@$grupos_disparos == 'ocultar'){
	echo "<script>window.location='index.php'</script>";
	exit();
}
?>

<div class="breadcrumb-header justify-content-between">
	<div class="left-content mt-2">
		<a class="btn btn-primary text-white" onclick="inserir()" type="button">
			<i class="fa fa-plus"></i> Adicionar Grupo Disparos
		</a>

		<!-- BOTÃO EXCLUIR SELEÇÃO -->
		<div class="dropdown" style="display: inline-block;">                      
			<a href="#" aria-expanded="false" aria-haspopup="true" data-toggle="dropdown" class="btn btn-danger dropdown-toggle" id="btn-deletar" style="display:none">
				<i class="fa fa-trash"></i> Deletar
			</a>
			<ul class="dropdown-menu">
				<li style="padding:15px 5px 0 10px;">
					<p>
						Excluir Selecionados? 
						<a href="#" onclick="deletarSel()"><span class="text-danger"><button class="btn btn-danger btn-xs">Sim</button></span></a>
					</p>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="row row-sm">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-body" id="listar">
				<!-- Conteúdo dinâmico da listagem -->
			</div>
		</div>
	</div>
</div>

<input type="hidden" id="ids">

<!-- Modal Perfil -->
<div class="modal fade" id="modalForm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white" style="background:#007bff; color:#fff;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_inserir"></span></h4>
			</div>
			<form id="form">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-8">						
							<label>Nome do Grupo</label>
							<input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome do grupo" required>					
						</div>
						<div class="col-md-3" style="margin-top: 24px;">      
							<button type="submit" id="btn_salvar" class="btn btn-primary">
								Salvar <i class="fa fa-check"></i>
							</button>
						</div>
					</div>
					<input type="hidden" class="form-control" id="id" name="id">					
					<br>
					<small><div id="mensagem" align="center"></div></small>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Modal Add Cliente -->
<div class="modal fade" id="modalAdd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white" style="background:#007bff; color:#fff;">
				<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="exampleModalLabel"><span id="titulo_add"></span></h4>
			</div>
			<div class="modal-body">					
				<div class="row">
					<div class="col-md-7">
						<div id="listar_clientes"></div>
					</div>
					<div class="col-md-5">
						<br>
						<div id="listar_clientes_grupos" style="margin-top: 45px"></div>
					</div>
				</div>
				<input type="hidden" id="id_add">
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
var pag = "<?=$pag?>";

function listarClientes() {
	var id_grupo = $('#id_add').val();
	$.ajax({
		url: 'paginas/' + pag + "/listar_clientes.php",
		method: 'POST',
		data: { id_grupo },
		dataType: "html",
		success: function (result) {
			$("#listar_clientes").html(result);
		}
	});
}

function listarClientesGrupos() {
	var id_grupo = $('#id_add').val();
	$.ajax({
		url: 'paginas/' + pag + "/listar_clientes_grupos.php",
		method: 'POST',
		data: { id_grupo },
		dataType: "html",
		success: function (result) {
			$("#listar_clientes_grupos").html(result);
		}
	});
}
</script>


<script type="text/javascript">
function inserir() {
    $('#mensagem').text('');
    $('#titulo_inserir').text('Inserir Registro');
    $('#id').val('');
    $('#nome').val('');
    $('#modalForm').modal('show');
}
</script>

<script type="text/javascript">
var pag = "grupos_disparos";

$(document).ready(function () {
    listar(); // Chama a função para listar ao carregar a página

    // Submissão do formulário do modal (Salvar grupo)
    $("#form").submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'paginas/' + pag + "/salvar.php",
            type: 'POST',
            data: formData,
            success: function (mensagem) {
                $('#mensagem').text('');
                $('#mensagem').removeClass();
                if (mensagem.trim() == "Salvo com Sucesso") {
                    $('#modalForm').modal('hide');
                    listar();
                } else {
                    $('#mensagem').addClass('text-danger');
                    $('#mensagem').text(mensagem);
                }
            },
            cache: false,
            contentType: false,
            processData: false,
        });
    });
});

// Função para listar
function listar() {
    $.ajax({
        url: 'paginas/' + pag + "/listar.php",
        method: 'POST',
        data: {},
        dataType: "html",
        success: function (result) {
            $("#listar").html(result);
        }
    });
}
</script>

<script type="text/javascript">
function excluir(id) {
    if (confirm('Deseja realmente excluir este grupo?')) {
        $.ajax({
            url: 'paginas/grupos_disparos/excluir.php',
            method: 'POST',
            data: { id: id },
            success: function (result) {
                if (result.trim() == 'Excluído com Sucesso') {
                    listar(); // Atualiza a lista
                } else {
                    alert(result);
                }
            }
        });
    }
}
</script>

<script type="text/javascript">
function ativar(id, acao) {
    $.ajax({
        url: 'paginas/grupos_disparos/mudar-status.php',
        method: 'POST',
        data: { id: id, acao: acao },
        success: function (result) {
            if (result.trim() == 'Alterado') {
                listar(); // Atualiza a lista
            } else {
                alert(result);
            }
        }
    });
}
</script>
