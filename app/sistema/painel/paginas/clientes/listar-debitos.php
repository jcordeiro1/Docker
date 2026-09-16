<?php 
require_once("../../../conexao.php");
$tabela = 'receber';
$data_atual = date('Y-m-d');

$id = $_POST['id'];
$id_cli = $_POST['id'];

// pegar a pagina atual
if(@$_POST['pagina'] == ""){
    @$_POST['pagina'] = 0;
}


$query = $pdo->query("SELECT * FROM $tabela where pessoa = '$id' and pago != 'Sim' ORDER BY data_venc asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){

echo <<<HTML
	<small>
	<table class="table table-hover">
	<thead> 
	<tr> 
	<th class="">Descrição Conta</th>
	<th>Data Vencimento</th>	
	<th class="">Valor</th>
	<th class="">Dar Baixa</th>	
	</tr> 
	</thead> 
	<tbody>	
HTML;
$total = 0;
$total_pagar = 0;
$total_vencido = 0;
$totalF = 0;
$total_pagarF = 0;
$total_vencidoF = 0;
for($i=0; $i < $total_reg; $i++){
	foreach ($res[$i] as $key => $value){}
	$id = $res[$i]['id'];	
	$descricao = $res[$i]['descricao'];
	$tipo = $res[$i]['tipo'];
	$valor = $res[$i]['valor'];
	$data_lanc = $res[$i]['data_lanc'];
	$data_pgto = $res[$i]['data_pgto'];
	$data_venc = $res[$i]['data_venc'];
	$usuario_lanc = $res[$i]['usuario_lanc'];
	$usuario_baixa = $res[$i]['usuario_baixa'];
	$foto = $res[$i]['foto'];
	$pessoa = $res[$i]['pessoa'];
	$pago = $res[$i]['pago'];
	$pgto = $res[$i]['pgto'];

		$query2 = $pdo->query("SELECT * FROM clientes where id = '$pessoa'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_pessoa = $res2[0]['nome'];
			$telefone_pessoa = $res2[0]['telefone'];
			$classe_whats = '';
		}else{
			$nome_pessoa = 'Nenhum!';
			$telefone_pessoa = '';
			$classe_whats = 'ocultar';
		}
		
	//
	$valorF = number_format($valor, 2, ',', '.');
	$data_lancF = implode('/', array_reverse(explode('-', $data_lanc)));
	$data_pgtoF = implode('/', array_reverse(explode('-', $data_pgto)));
	$data_vencF = implode('/', array_reverse(explode('-', $data_venc)));

	$total += $valor;
	$totalF = number_format($total, 2, ',', '.');

	$classe_data = '';
	if(strtotime($data_venc) < strtotime($data_atual)){
		$classe_data = 'text-danger';
		$total_vencido += $valor;
	}else{
		$total_pagar += $valor;
	}
	
	$total_pagarF = number_format($total_pagar, 2, ',', '.');
	$total_vencidoF = number_format($total_vencido, 2, ',', '.');
	//$valor = number_format($valor, 2, ',', '.');
	
	
	
echo <<<HTML
<tr class="">
<td>{$descricao}</td>
<td class="{$classe_data}">{$data_vencF}</td>
<td class="text-danger">R$ {$valor}</td>
<td>
	<big><a class="" href="#" onclick="baixar('{$id}', '{$valor}', '{$descricao}', '{$pgto}', '{$pessoa}')" title="Baixar Conta"><i class="fa fa-check-square verde"></i></a></big>

		
</td>
</tr>
HTML;

}

echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir-baixar"></div></small>
</table>
<div align="right"><span style="margin-right: 20px; ">Total Vencido <span style="color:red">R$ {$total_vencidoF}</span></span>
<span style="margin-right: 20px; ">Total à Vencer <span style="color:blue">R$ {$total_pagarF}</span></span>
<span >Total Pagar <span style="color:green">R$ {$totalF}</span></span>
</div>
</small>

HTML;

}else{
	echo '<small>Este Cliente não possui pagamento pendente!</small>';
}

?>
<script type="text/javascript">
	function cobrar(id, valor, data, telefone, descricao){

	var instancia = "<?=$instancia?>";
	var token = "<?=$token?>";
	

	if(instancia.trim() == "" || token.trim() == ""){
		alert('Insira um Token e Instancia Whatsapp nas configurações');
		return;
	}
	$.ajax({
	        url: 'paginas/receber/gerar_cobranca.php',
	        method: 'POST',
	        data: {id, valor, data, telefone, descricao},
	        dataType: "html",

	        success:function(result){	
	        //alert(result) 
	        	alert('Cobrança Efetuada!');           	
	           	
	        }
	    });
}
</script>


<script type="text/javascript">
	function baixar(id, valor, descricao, pgto, cliente){

		$('#id_baixar').val(id);
		$('#valor_baixar').val(valor);	
		mascara_valor('valor_baixar');		
		$('#titulo_baixar').text(descricao);
		$('#pgto_baixar').val(pgto).change();
		
		$('#cliente_baixar').val(cliente);	

		$('#modalBaixar').modal('show');
	}
</script>




