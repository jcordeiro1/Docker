<?php 
require_once("../../../conexao.php");
$tabela = 'servicos_func';

// aceita func (padrão do seu JS) e fallback em id
$id_func = isset($_POST['func']) ? (int)$_POST['func'] : 0;
if ($id_func <= 0 && isset($_POST['id'])) {
	$id_func = (int)$_POST['id'];
}

if ($id_func <= 0) {
	echo '<small>Funcionário inválido!</small>';
	exit();
}

$stmt = $pdo->prepare("SELECT * FROM {$tabela} WHERE funcionario = :funcionario");
$stmt->bindValue(':funcionario', $id_func, PDO::PARAM_INT);
$stmt->execute();

$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if($total_reg > 0){

echo <<<HTML
	<small><small>
	<table class="table table-hover">
	<thead> 
	<tr> 
	<th>Serviço</th>		
	<th>Excluir</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

$stmt2 = $pdo->prepare("SELECT nome FROM servicos WHERE id = :id LIMIT 1");

for($i=0; $i < $total_reg; $i++){
	foreach ($res[$i] as $key => $value){}
	$id = (int)$res[$i]['id'];
	$servico = (int)$res[$i]['servico'];

	$stmt2->bindValue(':id', $servico, PDO::PARAM_INT);
	$stmt2->execute();
	$res2 = $stmt2->fetch(PDO::FETCH_ASSOC);

	$nome_servico = isset($res2['nome']) ? $res2['nome'] : 'Serviço não encontrado';

echo <<<HTML
<tr class="">
<td class="">{$nome_servico}</td>
<td>
	<li class="dropdown head-dpdn2" style="display: inline-block;">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><big><i class="fa fa-trash-o text-danger"></i></big></a>

	<ul class="dropdown-menu" style="margin-left:-230px;">
	<li>
	<div class="notification_desc2">
	<p>Confirmar Exclusão? <a href="#" onclick="excluirServico('{$id}', '{$id_func}')"><span class="text-danger">Sim</span></a></p>
	</div>
	</li>										
	</ul>
	</li>
</td>
</tr>
HTML;

}

echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-servico-excluir"></div></small>
</table>
</small></small>
HTML;

}else{
	echo '<small>Não possui nenhum Serviço Cadastrado!</small>';
}

?>

<script type="text/javascript">
	function excluirServico(id, func){
		$.ajax({
			url: 'paginas/' + pag + "/excluir-servico.php",
			method: 'POST',
			data: {id},
			dataType: "text",

			success: function (mensagem) {            
				if (mensagem.trim() == "Excluído com Sucesso") {   
					listarServicos(func);                   
				} else {
					$('#mensagem-servico-excluir').addClass('text-danger')
					$('#mensagem-servico-excluir').text(mensagem)
				}
			}
		});
	}
</script>
