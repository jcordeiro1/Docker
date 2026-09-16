<?php 
@session_start();
$id_cliente = $_SESSION['id'];
require_once("../../../conexao.php");
$tabela = 'receber';
$data_hoje = date('Y-m-d');

$dataInicial = @$_POST['dataInicial'];
$dataFinal = @$_POST['dataFinal'];
$status = '%'.@$_POST['status'].'%';


$total_pago = 0;
$total_a_pagar = 0;

$query = $pdo->query("SELECT * FROM $tabela where pessoa = '$id_cliente' order by id desc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){

echo <<<HTML
	<small>
	<table class="table table-hover" id="tabela">
	<thead> 
	<tr> 
	<th>Descrição</th>	
	<th class="esc">Valor</th> 	
	<th class="esc">Vencimento</th> 	
	<th class="esc">Data PGTO</th> 
	<th class="esc">Arquivo</th>	
	<th>Pagar</th>
	</tr> 
	</thead> 
	<tbody>	
HTML;

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
	
	$valorF = number_format($valor, 2, ',', '.');
	$data_lancF = implode('/', array_reverse(@explode('-', $data_lanc)));
	$data_pgtoF = implode('/', array_reverse(@explode('-', $data_pgto)));
	$data_vencF = implode('/', array_reverse(@explode('-', $data_venc)));
	

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


		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario_baixa'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_usuario_pgto = $res2[0]['nome'];
		}else{
			$nome_usuario_pgto = 'Nenhum!';
		}



		$query2 = $pdo->query("SELECT * FROM usuarios where id = '$usuario_lanc'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_usuario_lanc = $res2[0]['nome'];
		}else{
			$nome_usuario_lanc = 'Sem Referência!';
		}


		if($pago != "Sim"){
			$classe_alerta = 'text-danger';
			$data_pgtoF = 'Pendente';
			$visivel = '';
			$total_a_pagar += $valor;
		}else{
			$classe_alerta = 'verde';
			$visivel = 'ocultar';
			$total_pago += $valor;
		}


			//extensão do arquivo
$ext = pathinfo($foto, PATHINFO_EXTENSION);
if($ext == 'pdf'){
	$tumb_arquivo = 'pdf.png';
}else if($ext == 'rar' || $ext == 'zip'){
	$tumb_arquivo = 'rar.png';
}else{
	$tumb_arquivo = $foto;
}


if($data_venc < $data_hoje and $pago != 'Sim'){
	$classe_debito = 'vermelho-escuro';
}else{
	$classe_debito = '';
}

if($pago == 'Sim'){
	$classe_cor_whats = 'verde';
	$ocultar_baixar = 'ocultar';
}else{
	$classe_cor_whats = 'text-danger';
	$ocultar_baixar = '';
}
		
$whats = '55'.preg_replace('/[ ()-]+/' , '' , $telefone_pessoa);

echo <<<HTML
<tr class="{$classe_debito}">
<td><i class="fa fa-square {$classe_alerta}"></i> {$descricao}</td>
<td class="esc">R$ {$valorF}</td>
<td class="esc">{$data_vencF}</td>
<td class="esc">{$data_pgtoF}</td>

<td><a href="../painel/img/contas/{$foto}" target="_blank"><img src="../painel/img/contas/{$tumb_arquivo}" width="27px" class="mr-2"></a></td>
<td>
		
			<big><a class="{$ocultar_baixar}" href="../../conta/{$id}" target="_blank" title="Efetuar Pagamento"><i class="fa fa-usd verde"></i></a></big>
	
		</td>
</tr>
HTML;

}

$total_pagoF = number_format($total_pago, 2, ',', '.');
$total_a_pagarF = number_format($total_a_pagar, 2, ',', '.');

echo <<<HTML
</tbody>
<small><div align="center" id="mensagem-excluir"></div></small>
</table>

<br>	
<div align="right">Total Pago: <span class="verde">R$ {$total_pagoF}</span> </div>
<div align="right">Total à Pagar: <span class="text-danger">R$ {$total_a_pagarF}</span> </div>

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

