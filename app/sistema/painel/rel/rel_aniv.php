<?php 
if($token_rel != 'A5030'){
echo '<script>window.location="../../"</script>';
exit();
}

$partesInicial = @explode('-', $dataInicial);
$dataDiaInicial = $partesInicial[2];
$dataMesInicial = $partesInicial[1];

$partesFinal = @explode('-', $dataFinal);
$dataDiaFinal = $partesFinal[2];
$dataMesFinal = $partesFinal[1];

$dataInicialF = implode('/', array_reverse(@explode('-', $dataInicial)));
$dataFinalF = implode('/', array_reverse(@explode('-', $dataFinal)));

if($dataInicial == $dataFinal){
	$texto_apuracao = 'ANIVERSÁRIANTES DO DIA '.$dataInicialF;
}else{
	$texto_apuracao = 'ANIVERSÁRIANTES DE '.$dataInicialF. ' ATÉ '.$dataFinalF;
}



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

						<b><big>RELATÓRIO DE ANIVERSARIANTES</big></b><br> <?php echo @mb_strtoupper($texto_apuracao) ?> <br> <?php echo @mb_strtoupper($data_hoje) ?>

				</td>

			</tr>		

		</table>

	</div>



<br>





		<table id="cabecalhotabela" style="border-bottom-style: solid; font-size: 8px; margin-bottom:10px; width: 100%; table-layout: fixed;">

			<thead>

				

				<tr id="cabeca" style="margin-left: 0px; background-color:#CCC">
					<td style="width:40%">NOME</td>
					<td style="width:15%">TELEFONE</td>
					<td style="width:15%">CADASTRO</td>
					<td style="width:15%">NASCIMENTO</td>
					<td style="width:15%">CARTÕES</td>	

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


$total_entradas = 0;
$query = $pdo->query("SELECT * FROM clientes where month(data_nasc) >= '$dataMesInicial' and day(data_nasc) >= '$dataDiaInicial' and month(data_nasc) <= '$dataMesFinal' and day(data_nasc) <= '$dataDiaFinal' order by data_nasc asc, id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$linhas = @count($res);

if($linhas > 0){

for($i=0; $i<$linhas; $i++){
$id = $res[$i]['id'];
	$nome = $res[$i]['nome'];	
	$data_nasc = $res[$i]['data_nasc'];
	$data_cad = $res[$i]['data_cad'];	
	$telefone = $res[$i]['telefone'];
	$endereco = $res[$i]['endereco'];
	$cartoes = $res[$i]['cartoes'];
	$data_retorno = $res[$i]['data_retorno'];
	$ultimo_servico = $res[$i]['ultimo_servico'];

	$data_cadF = implode('/', array_reverse(@explode('-', $data_cad)));
	$data_nascF = implode('/', array_reverse(@explode('-', $data_nasc)));
	$data_retornoF = implode('/', array_reverse(@explode('-', $data_retorno)));
	
	if($data_nascF == '00/00/0000'){
		$data_nascF = 'Sem Lançamento';
	}
	
  	 ?>



  	 

      <tr>

<td style="width:40%"><?php echo $nome ?></td>

<td style="width:15%"><?php echo $telefone ?></td>

<td style="width:15%"><?php echo $data_cadF ?></td>

<td style="width:15%"><?php echo $data_nascF ?></td>

<td style="width:15%"><?php echo $cartoes ?></td>


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



						<td style="font-size: 10px; width:300px; text-align: right;"></td>



						



						<td style="font-size: 10px; width:70px; text-align: right;"></td>



							<td style="font-size: 10px; width:70px; text-align: right;"></td>





								<td style="font-size: 10px; width:80px; text-align: right;"></td>



									<td style="font-size: 10px; width:200px; text-align: right;"><b>TOTAL DE ANIVERSÁRIANTES: <span style="color:red"><?php echo $total_reg ?></span></td>

						

					</tr>

				</tbody>

			</thead>

		</table>



</body>



</html>





