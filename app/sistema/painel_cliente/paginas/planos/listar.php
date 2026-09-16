<?php 
@session_start();
$id_cliente = $_SESSION['id'];
require_once("../../../conexao.php");
$tabela = 'assinaturas';

$data_atual = date('Y-m-d');

$query = $pdo->query("SELECT * FROM $tabela where cliente = '$id_cliente' ORDER BY id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){	

echo <<<HTML
	<small>
	<table class="table table-hover" id="tabela">
	<thead> 
	<tr> 
	
	<th>Assinatura</th> 	
	<th>Plano</th> 	
	<th>Valor</th> 
	<th>Data</th> 	
	<th>Vencimento</th> 	
	<th>Pagar</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

for($i=0; $i < $total_reg; $i++){
	foreach ($res[$i] as $key => $value){}
	$id = $res[$i]['id'];
	$cliente = $res[$i]['cliente'];	
	$data = $res[$i]['data'];
	$grupo = $res[$i]['grupo'];	
	$item = $res[$i]['item'];	
	$pago = $res[$i]['pago'];
	$valor = $res[$i]['valor'];	
	$ref_pix = $res[$i]['ref_pix'];	
	$frequencia = $res[$i]['frequencia'];
	$vencimento = $res[$i]['vencimento'];	
	
	
		$query2 = $pdo->query("SELECT * FROM grupo_assinaturas where id = '$grupo'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_grupo = $res2[0]['nome'];
		}else{
			$nome_grupo = 'Nenhum!';
		}

		$query2 = $pdo->query("SELECT * FROM itens_assinaturas where id = '$item'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_item = $res2[0]['nome'];
		}else{
			$nome_item = 'Nenhum!';
		}

		$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_cliente = $res2[0]['nome'];
		}else{
			$nome_cliente = 'Nenhum!';
		}
	
		$valorF = number_format($valor, 2, ',', '.');
	$dataF = implode('/', array_reverse(@explode('-', $data)));
	$vencimentoF = implode('/', array_reverse(@explode('-', $vencimento)));

	if($pago != "Sim"){
			$classe_alerta = 'text-danger';			
			$visivel = '';			
		}else{
			$classe_alerta = 'verde';
			$visivel = 'ocultar';			
		}


		//verificar se o pagamento está aprovado
		if($pago == 'Não' and $ref_pix != ""){
			require_once("../../../../pagamentos/consultar_pagamento.php");
			if($status_api == 'approved'){
					$id_usuario = $_SESSION['id'];
					$id = $res[$i]['id'];
					$valor = $res[$i]['valor'];
					$forma_pgto = 'MP';
					$data_pgto = $data_atual;
					require_once("aprovar_plano.php");
				}				
		}



echo <<<HTML
<tr class="">
<td><i class="fa fa-square {$classe_alerta}"></i>
{$nome_grupo}</td>
<td class="esc">{$nome_item}</td>
<td class="esc">{$valorF}</td>
<td class="esc">{$dataF}</td>
<td class="esc">{$vencimentoF}</td>
<td>
	
	<big><a class="{$visivel}" href="../../plano/{$id}" target="_blank" title="Efetuar Pagamento"><i class="fa fa-usd verde"></i></a></big>

</td>
</tr>
HTML;

}

echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>
</small>
HTML;


}else{
	echo '<small>Não possui nenhum registro Cadastrado!</small>';
}

?>

<script type="text/javascript">
	$(document).ready( function () {
    $('#tabela').DataTable({
    		"ordering": false,
			"stateSave": true
    	});
    $('#tabela_filter label input').focus();
} );
</script>


<script type="text/javascript">
	function editar(id, cliente, grupo, item, valor, frequencia, vencimento){
		$('#id').val(id);
		$('#cliente').val(cliente).change();
		$('#grupo').val(grupo).change();	
		$('#frequencia').val(frequencia).change();	
		$('#vencimento').val(vencimento);	
			

		setTimeout(function() {		  
		 	listarItens();
		}, 400)

		setTimeout(function() {		  
		  $('#item').val(item).change();
		}, 700)

		setTimeout(function() {		  
		  $('#valor').val(valor);
		}, 900)
		
		$('#titulo_inserir').text('Editar Registro');
		$('#modalForm').modal('show');

		
	}

	function limparCampos(){
		$('#id').val('');
		$('#nome').val('');
		$('#chave').val('');
		$('#frequencia').val('30').change();	
	}

function baixar(id, valor){

		$('#id_baixar').val(id);
		$('#valor_baixar').val(valor);			
		$('#modalBaixar').modal('show');
	}
</script>

