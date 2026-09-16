<?php 
if($token_rel != 'A5030'){
echo '<script>window.location="../../"</script>';
exit();
}

$hoje = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');
$dataInicioMes = $ano_atual."-".$mes_atual."-01";
?>

<!DOCTYPE html>

<html>

<head>



<style>



@import url('https://fonts.cdnfonts.com/css/tw-cen-mt-condensed');

@page { margin: 145px 20px 25px 20px; }

#header { position: fixed; left: 0px; top: -110px; bottom: 100px; right: 0px; height: 35px; text-align: center; padding-bottom: 100px; }

#content {margin-top: 0px;}

#footer { position: fixed; left: 0px; bottom: -60px; right: 0px; height: 80px; }

#footer .page:after {content: counter(page, my-sec-counter);}

body {font-family: 'Tw Cen MT', sans-serif;}



.marca{

	position:fixed;

	left:50;

	top:100;

	width:80%;

	opacity:8%;

}



</style>



</head>

<body>


<div id="header" >



	<div style="border-style: solid; font-size: 10px; height: 50px;">

		<table style="width: 100%; border: 0px solid #ccc;">

			<tr>

				<td style="border: 1px; solid #000; width: 37%; text-align: left;">

					<img style="margin-top: 7px; margin-left: 7px;" id="imag" src="<?php echo $url_sistema ?>sistema/img/logo_rel.jpg" width="80">

				</td>

				
				<td style="width: 1%; text-align: center; font-size: 13px;">

				

				</td>

				<td style="width: 47%; text-align: right; font-size: 9px;padding-right: 10px;">

						<b><big>RELATÓRIO DE PRODUTOS</big></b><br><br> <?php echo @mb_strtoupper($data_hoje) ?>

				</td>

			</tr>		

		</table>

	</div>



<br>





		<table id="cabecalhotabela" style="border-bottom-style: solid; font-size: 8px; margin-bottom:10px; width: 100%; table-layout: fixed;">

			<thead>				

				<tr id="cabeca" style="margin-left: 0px; background-color:#CCC">
					<td style="width:30%">NOME</td>
					<td style="width:25%">CATEGORIA</td>
					<td style="width:15%">VALOR COMPRA</td>
					<td style="width:15%">VALOR VENDA</td>
					<td style="width:15%">ESTOQUE</td>
						
				</tr>

			</thead>

		</table>

</div>



<div id="footer" class="row">

<hr style="margin-bottom: 0;">

	<table style="width:100%;">

		<tr style="width:100%;">

			<td style="width:60%; font-size: 10px; text-align: left;"><?php echo $nome_sistema ?> Telefone: <?php echo $whatsapp_sistema ?></td>

			<td style="width:40%; font-size: 10px; text-align: right;"><p class="page">Página  </p></td>

		</tr>

	</table>

</div>



<div id="content" style="margin-top: 0;">







		<table style="width: 100%; table-layout: fixed; font-size:9px; text-transform: uppercase;">

			<thead>

				<tbody>

					<?php



$estoque_baixo = 0;
		$query = $pdo->query("SELECT * FROM produtos ORDER BY nome asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$linhas = @count($res);

if($linhas > 0){

for($i=0; $i<$linhas; $i++){
$id = $res[$i]['id'];
	$nome = $res[$i]['nome'];	
	$descricao = $res[$i]['descricao'];
	$categoria = $res[$i]['categoria'];
	$valor_compra = $res[$i]['valor_compra'];
	$valor_venda = $res[$i]['valor_venda'];
	$foto = $res[$i]['foto'];
	$estoque = $res[$i]['estoque'];
	$nivel_estoque = $res[$i]['nivel_estoque'];

	$valor_vendaF = number_format($valor_venda, 2, ',', '.');
	$valor_compraF = number_format($valor_compra, 2, ',', '.');


				//extensão do arquivo


	

		$query2 = $pdo->query("SELECT * FROM cat_produtos where id = '$categoria'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$total_reg2 = @count($res2);
		if($total_reg2 > 0){
			$nome_cat = $res2[0]['nome'];
		}else{
			$nome_cat = 'Sem Referência!';
		}


		if($nivel_estoque >= $estoque){
			$alerta_estoque = 'text-danger';
			$estoque_baixo += 1;
		}else{
			$alerta_estoque = '';
		}
		

  	 ?>



  	 

      <tr>

<td style="width:30%"><img src="<?php echo $url_sistema ?>/sistema/painel/img/produtos/<?php echo $foto ?>" width="27px" height="27px">
<?php echo $nome ?></td>
<td style="width:25%"><?php echo $nome_cat ?></td>
<td style="width:15%">R$ <?php echo $valor_compraF ?></td>
<td style="width:15%">R$ <?php echo $valor_vendaF ?></td>
<td style="width:15%"><?php echo $estoque ?></td>
    </tr>



<?php } } ?>

				</tbody>

	

			</thead>

		</table>

	





</div>

<hr>

		<table>

			<thead>

				<tbody>

					<tr>



						<td style="font-size: 10px; width:270px; text-align: right;"></td>	

						<td style="font-size: 10px; width:120px; text-align: right;"></td>
						

								<td style="font-size: 10px; width:160px; text-align: right;"><b>PRODUTOS ESTOQUE BAIXO <span style="color:green"><?php echo $estoque_baixo ?></span></td>

									<td style="font-size: 10px; width:160px; text-align: right;"><b>TOTAL DE PRODUTOS <span style="color:red"><?php echo $total_reg ?></span></td>

						

					</tr>

				</tbody>

			</thead>

		</table>


		



</body>



</html>





