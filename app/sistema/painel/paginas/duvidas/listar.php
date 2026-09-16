<?php 
require_once("../../../conexao.php");
$tabela = 'duvidas';
@session_start();

$nivel_usuario = $_SESSION['nivel'] ?? '';

$itens_pag = 4; // define quantos itens por página

$pagina = @$_POST['pagina'] ?? 0;
$limite = $pagina * $itens_pag;

$query = $pdo->query("SELECT * FROM tutoriais ORDER BY id DESC LIMIT $limite, $itens_pag");

$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
?>

<style>
.card-equal {
  display: table;
  height: 100%;
  width: 100%;
}
.panel-equal {
  display: table-cell;
  vertical-align: top;
  height: 100%;
}
</style>


<div class="row">
  <?php if ($total_reg > 0) {
    foreach ($res as $dados) {
      $id = $dados['id'];
      $titulo = $dados['titulo'];
      $descricao = $dados['descricao'];
      $link_video = $dados['link_video'];
      $data = date('d/m/Y', strtotime($dados['data']));
  ?>


 <div class="col-md-4 card-equal">
  <div class="panel panel-default panel-equal">

      <div class="panel-heading">
        <strong><?php echo $titulo ?></strong> <small class="text-muted"><?php echo "(" . $data . ")"; ?></small>
      </div>
      <div class="panel-body">
        <p><?php echo nl2br($descricao) ?></p>

        <?php if ($link_video != '') { ?>
          <a href="#" onclick="abrirVideo('<?php echo $link_video ?>')">
            <i class="fa fa-play"></i> Ver Vídeo
          </a>
        <?php } ?>
      </div>
      <?php if ($nivel_usuario == 'Administrador') { ?>
      <div class="panel-footer text-right">
        <a href="#" onclick="editar('<?php echo $id ?>', '<?php echo addslashes($titulo) ?>', '<?php echo addslashes($descricao) ?>', '<?php echo $link_video ?>')" class="btn btn-sm btn-primary">
          <i class="fa fa-edit"></i>
        </a>
    
    <!-- Botão Responder -->
    <a href="#" onclick="abrirResposta('<?php echo $id ?>')" class="btn btn-sm btn-info">
      <i class="fa fa-reply"></i>
    </a>
        <a href="#" onclick="excluir('<?php echo $id ?>')" class="btn btn-sm btn-danger">
          <i class="fa fa-trash"></i>
        </a>
      </div>
      <?php } ?>
    </div>
  </div>

  <?php } } else { ?>
    <div class="col-md-12">
      <p class="text-muted">Nenhum tutorial encontrado.</p>
    </div>
  <?php } ?>
</div>
<?php
$total_query = $pdo->query("SELECT COUNT(*) as total FROM tutoriais");
$total_result = $total_query->fetch(PDO::FETCH_ASSOC);
$total_itens = $total_result['total'];
$num_paginas = ceil($total_itens / $itens_pag);
?>

<div class="row" align="center">
  <nav aria-label="Page navigation">
    <ul class="pagination">
      <?php for ($i = 0; $i < $num_paginas; $i++): ?>
        <?php $estilo = ($i == $pagina) ? 'active' : ''; ?>
        <li class="page-item <?= $estilo ?>">
          <a href="#" class="page-link" onclick="listar(<?= $i ?>)"><?= $i + 1 ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<!-- Modal Vídeo -->
<div class="modal fade" id="modalVideo" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Visualizar Vídeo</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="embed-responsive embed-responsive-16by9">
          <iframe class="embed-responsive-item" id="videoFrame" src="" allowfullscreen></iframe>
        </div>
      </div>
    </div>
  </div>
</div>




<script>
function editar(id, titulo, descricao, link) {
  $('#id').val(id);
  $('#titulo').val(titulo);
  $('#descricao').val(descricao);
  $('#link_video').val(link);
  $('#modalCadastro').modal('show');
  $('#tituloModal').text('Editar Tutorial');
}

function excluir(id) {
  if (confirm('Deseja realmente excluir este tutorial?')) {
    $.post('paginas/duvidas/excluir.php', { id: id }, function(retorno) {
      if (retorno.trim() === "Excluído com Sucesso") {
        listar();
      } else {
        alert(retorno);
      }
    });
  }
}

function abrirVideo(link) {
  let embedLink = link;

  // Verifica se é link do Google Drive
  if (link.includes('drive.google.com')) {
    const idMatch = link.match(/[-\w]{25,}/);
    if (idMatch) {
      embedLink = `https://drive.google.com/file/d/${idMatch[0]}/preview`;
    }
  }

  $('#videoFrame').attr('src', embedLink);
  $('#modalVideo').modal('show');
  $('#modalVideo').on('hidden.bs.modal', function () {
    $('#videoFrame').attr('src', '');
  });
}

function abrirResposta(id) {
  $('#id_pergunta').val(id);
  $('#resposta').val('');
  $('#mensagem-resposta').text('');
  $('#modalResposta').modal('show');
}

</script>
