<?php 
require_once("../../../conexao.php");
$tabela = 'manutencoes_protese';
$data_atual = date('Y-m-d');

/*
	Ajuste somente esta variável com a rota real do seu módulo de agendamento
*/
$link_agendamento = 'agendamentos';

$busca = '%' . @$_POST['busca'] . '%';

if(@$_POST['pagina'] == ""){
    @$_POST['pagina'] = 0;
}

$pagina = intval(@$_POST['pagina']);
$limite = $pagina * $itens_pag;

$query = $pdo->prepare("SELECT m.*, c.nome as nome_cliente, p.modelo as nome_protese 
    FROM $tabela m 
    INNER JOIN clientes c ON m.cliente = c.id
    INNER JOIN proteses p ON m.id_protese = p.id
    WHERE c.nome LIKE :busca or p.modelo LIKE :busca or m.tipo LIKE :busca or m.produtos_utilizados LIKE :busca or m.tipo_pele LIKE :busca or m.clima LIKE :busca or m.tipo_adesivo LIKE :busca
    ORDER BY m.id desc LIMIT $limite, $itens_pag");
$query->bindValue(":busca", $busca);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

$query2 = $pdo->prepare("SELECT COUNT(*) as total FROM $tabela m 
	INNER JOIN clientes c ON m.cliente = c.id
	INNER JOIN proteses p ON m.id_protese = p.id
	WHERE c.nome LIKE :busca or p.modelo LIKE :busca or m.tipo LIKE :busca or m.produtos_utilizados LIKE :busca or m.tipo_pele LIKE :busca or m.clima LIKE :busca or m.tipo_adesivo LIKE :busca");
$query2->bindValue(":busca", $busca);
$query2->execute();
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$total_reg2 = $res2[0]['total'];

$num_paginas = ceil($total_reg2 / $itens_pag);
$ultimo_reg = $num_paginas - 1;

if($total_reg > 0){

echo <<<HTML
<style>
.badge-manutencao{
	display:inline-block;
	padding:4px 8px;
	border-radius:12px;
	font-size:11px;
	font-weight:600;
	margin-left:6px;
	margin-top:3px;
}

.badge-vencida{
	background:#f8d7da;
	color:#721c24;
}

.badge-hoje{
	background:#fff3cd;
	color:#856404;
}

.badge-ok{
	background:#d4edda;
	color:#155724;
}

.badge-alerta-enviado{
	background:#d1ecf1;
	color:#0c5460;
}

.linha-vencida{
	background:#fff8f8;
}

.alerta-link{
	display:block;
	margin-top:4px;
	font-size:11px;
}
</style>

<small>
<table class="table table-hover">
<thead> 
<tr> 
<th>Cliente</th>	
<th class="esc">Prótese</th> 
<th class="esc">Data</th> 	
<th class="esc">Tipo</th> 	
<th class="esc">Próxima</th> 
<th style="width:180px">Ações</th>
</tr> 
</thead> 
<tbody>	
HTML;

for($i=0; $i < $total_reg; $i++){
	$id = $res[$i]['id'];
	$cliente = $res[$i]['cliente'];
	$id_protese = $res[$i]['id_protese'];
	$nome_cliente = $res[$i]['nome_cliente'];
	$nome_protese = $res[$i]['nome_protese'];
	$data_manutencao = $res[$i]['data_manutencao'];
	$tipo = $res[$i]['tipo'];
	$produtos_utilizados = $res[$i]['produtos_utilizados'];
	$observacoes = $res[$i]['observacoes'];
	$proxima_manutencao = $res[$i]['proxima_manutencao'];

	$tipo_pele = isset($res[$i]['tipo_pele']) ? $res[$i]['tipo_pele'] : '';
	$nivel_sudorese = isset($res[$i]['nivel_sudorese']) ? $res[$i]['nivel_sudorese'] : '';
	$clima = isset($res[$i]['clima']) ? $res[$i]['clima'] : '';
	$tipo_adesivo = isset($res[$i]['tipo_adesivo']) ? $res[$i]['tipo_adesivo'] : '';

	$alerta_manutencao_enviado = isset($res[$i]['alerta_manutencao_enviado']) ? $res[$i]['alerta_manutencao_enviado'] : 0;
	$data_alerta_manutencao = isset($res[$i]['data_alerta_manutencao']) ? $res[$i]['data_alerta_manutencao'] : '';

	$data_manutencaoF = $data_manutencao ? implode('/', array_reverse(explode('-', $data_manutencao))) : '';
	$proxima_manutencaoF = $proxima_manutencao ? implode('/', array_reverse(explode('-', $proxima_manutencao))) : '';

	$classe_linha = '';
	$status_badge = '';
	$link_alerta = '';
	$badge_alerta_enviado = '';

	if($proxima_manutencao != ""){
		if($proxima_manutencao < $data_atual){
			$classe_linha = 'linha-vencida';
			$status_badge = "<span class='badge-manutencao badge-vencida'>Vencida</span>";
			$link_alerta = "<a class='alerta-link text-danger' href='{$link_agendamento}?cliente={$cliente}&id_protese={$id_protese}'>Agendar manutenção</a>";
		}else if($proxima_manutencao == $data_atual){
			$status_badge = "<span class='badge-manutencao badge-hoje'>Vence Hoje</span>";
			$link_alerta = "<a class='alerta-link text-warning' href='{$link_agendamento}?cliente={$cliente}&id_protese={$id_protese}'>Agendar manutenção</a>";
		}else{
			$status_badge = "<span class='badge-manutencao badge-ok'>Em Dia</span>";
		}
	}

	if($alerta_manutencao_enviado == 1){
		$badge_alerta_enviado = "<span class='badge-manutencao badge-alerta-enviado'>Alerta Enviado</span>";
	}

	$nome_cliente_js = htmlspecialchars($nome_cliente, ENT_QUOTES);
	$nome_protese_js = htmlspecialchars($nome_protese, ENT_QUOTES);
	$tipo_js = htmlspecialchars($tipo, ENT_QUOTES);
	$produtos_utilizados_js = htmlspecialchars($produtos_utilizados, ENT_QUOTES);
	$observacoes_js = htmlspecialchars($observacoes, ENT_QUOTES);
	$tipo_pele_js = htmlspecialchars($tipo_pele, ENT_QUOTES);
	$nivel_sudorese_js = htmlspecialchars($nivel_sudorese, ENT_QUOTES);
	$clima_js = htmlspecialchars($clima, ENT_QUOTES);
	$tipo_adesivo_js = htmlspecialchars($tipo_adesivo, ENT_QUOTES);

echo <<<HTML
<tr class="{$classe_linha}">
<td>{$nome_cliente}</td>
<td class="esc">{$nome_protese}</td>
<td class="esc">{$data_manutencaoF}</td>
<td class="esc">{$tipo}</td>
<td class="esc">
	{$proxima_manutencaoF}
	{$status_badge}
	{$badge_alerta_enviado}
	{$link_alerta}
</td>
<td style="white-space:nowrap;">

	<big>
		<a href="javascript:void(0)" onclick="editar('{$id}','{$cliente}','{$id_protese}','{$data_manutencao}','{$tipo_js}','{$produtos_utilizados_js}','{$observacoes_js}','{$proxima_manutencao}','{$tipo_pele_js}','{$nivel_sudorese_js}','{$clima_js}','{$tipo_adesivo_js}')" title="Editar Dados">
			<i class="fa fa-edit text-primary"></i>
		</a>
	</big>

	<big>
		<a href="javascript:void(0)" onclick="mostrar('{$nome_cliente_js}','{$nome_protese_js}','{$data_manutencaoF}','{$tipo_js}','{$produtos_utilizados_js}','{$observacoes_js}','{$proxima_manutencaoF}','{$tipo_pele_js}','{$nivel_sudorese_js}','{$clima_js}','{$tipo_adesivo_js}')" title="Ver Dados">
			<i class="fa fa-info-circle text-secondary"></i>
		</a>
	</big>

	<big>
		<a href="{$link_agendamento}?cliente={$cliente}&id_protese={$id_protese}" title="Agendar Manutenção">
			<i class="fa fa-calendar text-success"></i>
		</a>
	</big>

	<li class="dropdown head-dpdn2" style="display: inline-block; list-style:none;">
		<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false" title="Excluir">
			<big><i class="fa fa-trash-o text-danger"></i></big>
		</a>

		<ul class="dropdown-menu" style="margin-left:-230px;">
			<li>
				<div class="notification_desc2">
					<p>Confirmar Exclusão? <a href="javascript:void(0)" onclick="excluir('{$id}')"><span class="text-danger">Sim</span></a></p>
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
<small><div align="center" id="mensagem-excluir"></div></small>
</table>
</small>

<hr>
<div class="row" align="center">
	<nav aria-label="Page navigation example">
		<ul class="pagination">
			<li class="page-item">
				<a onclick="listarManutencoes(0)" class="paginador" href="javascript:void(0)" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
					<span class="sr-only">Previous</span>
				</a>
			</li>
HTML;

for($i=0;$i<$num_paginas;$i++){
	$estilo = "";
	if($pagina >= ($i - 2) and $pagina <= ($i + 2)){
		if($pagina == $i){
			$estilo = "active";
		}

		$pag = $i+1;

echo <<<HTML
<li class="page-item {$estilo}">
	<a onclick="listarManutencoes({$i})" class="paginador" href="javascript:void(0)">{$pag}</a>
</li>
HTML;

	} 
} 

echo <<<HTML
			<li class="page-item">
				<a onclick="listarManutencoes({$ultimo_reg})" class="paginador" href="javascript:void(0)" aria-label="Next">
					<span aria-hidden="true">&raquo;</span>
					<span class="sr-only">Next</span>
				</a>
			</li>
		</ul>
	</nav>
</div>
HTML;

}else{
	echo '<small>Não possui nenhum registro Cadastrado!</small>';
}
?>

<script type="text/javascript">
function editar(id, cliente, id_protese, data_manutencao, tipo, produtos_utilizados, observacoes, proxima_manutencao, tipo_pele, nivel_sudorese, clima, tipo_adesivo){
	$('#id').val(id);
	$('#cliente').val(cliente).change();
	$('#id_protese').val(id_protese).change();
	$('#data_manutencao').val(data_manutencao);
	$('#tipo').val(tipo);
	$('#produtos_utilizados').val(produtos_utilizados).change();
	$('#observacoes').val(observacoes);
	$('#proxima_manutencao').val(proxima_manutencao);
	$('#tipo_pele').val(tipo_pele);
	$('#nivel_sudorese').val(nivel_sudorese);
	$('#clima').val(clima);
	$('#tipo_adesivo').val(tipo_adesivo);

	$('#titulo_inserir').text('Editar Registro');
	$('#modalForm').modal('show');
}

function limparCampos(){
	$('#id').val('');
	$('#cliente').val('').change();
	$('#id_protese').val('').change();
	$('#data_manutencao').val('');
	$('#tipo').val('');
	$('#produtos_utilizados').val('').change();
	$('#observacoes').val('');
	$('#proxima_manutencao').val('');
	$('#tipo_pele').val('');
	$('#nivel_sudorese').val('');
	$('#clima').val('');
	$('#tipo_adesivo').val('');
}
</script>

<script type="text/javascript">
function mostrar(nome_cliente, nome_protese, data_manutencao, tipo, produtos_utilizados, observacoes, proxima_manutencao, tipo_pele, nivel_sudorese, clima, tipo_adesivo){

	let sudorese_texto = '';
	if(nivel_sudorese == '1'){
		sudorese_texto = '1 - Transpira pouco';
	}else if(nivel_sudorese == '2'){
		sudorese_texto = '2 - Transpiração leve';
	}else if(nivel_sudorese == '3'){
		sudorese_texto = '3 - Transpiração moderada';
	}else if(nivel_sudorese == '4'){
		sudorese_texto = '4 - Transpira bastante';
	}else if(nivel_sudorese == '5'){
		sudorese_texto = '5 - Transpira muito';
	}else{
		sudorese_texto = nivel_sudorese;
	}

	$('#nome_dados').text(nome_cliente);
	$('#cliente_dados').text(nome_cliente);
	$('#protese_dados').text(nome_protese);
	$('#data_manutencao_dados').text(data_manutencao);
	$('#tipo_dados').text(tipo);
	$('#produtos_dados').text(produtos_utilizados);
	$('#observacoes_dados').text(observacoes);
	$('#proxima_manutencao_dados').text(proxima_manutencao);
	$('#tipo_pele_dados').text(tipo_pele);
	$('#nivel_sudorese_dados').text(sudorese_texto);
	$('#clima_dados').text(clima);
	$('#tipo_adesivo_dados').text(tipo_adesivo);

	$('#modalDados').modal('show');
}
</script>