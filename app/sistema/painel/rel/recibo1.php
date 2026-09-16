<?php 
include('../../conexao.php');
$id = $_GET['id'];

$query = $pdo->query("SELECT * from receber where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_par = $res[0]['id'];
$valor = $res[0]['valor'];
$parcela = $res[0]['parcela'];
$data_venc = $res[0]['data_venc'];
$data_pgto = $res[0]['data_pgto'];
$pago = $res[0]['pago'];
$descricao = $res[0]['referencia'];
$cliente = $res[0]['cliente'];
$ref_pix = $res[0]['ref_pix'];
$id_ref = $res[0]['id_ref'];
$forma_pgto = $res[0]['pgto'];
$hora = @$res[0]['hora'];

$valorF = number_format($valor, 2, ',', '.');

$data_vencF = implode('/', array_reverse(@explode('-', $data_venc)));
$data_pgtoF = implode('/', array_reverse(@explode('-', $data_pgto)));

$query = $pdo->query("SELECT * from cobrancas where id = '$id_ref'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
@$parcelas_do_imp = $res[0]['parcelas'];
@$id_emp = $res[0]['id'];

$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
if(@count($res2) > 0){
	$nome_cliente = $res2[0]['nome'];	
	$tel_cliente = $res2[0]['telefone'];		
}


if ($parcela == $parcelas_do_imp) {
$texto_pag = 'QUITAÇÃO DE EMPRESTIMO';


$valor_total_pago_emp = 0;

$query = $pdo->query("SELECT * from receber where id_ref = '$id_emp'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);

for($i=0; $i<$linhas; $i++){
	$valor = $res[$i]['valor'];

	$valor_total_pago_emp += $valor;
}
}else{
$texto_pag = 'PAGAMENTO';
}





?>


<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<?php if(@$impressao_automatica == 'Sim'){ ?>
<script type="text/javascript">
	$(document).ready(function() {    		
		//window.print();
		//window.close(); 
	} );
</script>
<?php } ?>





<style type="text/css">
	*{
	margin:0px;

	/*Espaçamento da margem da esquerda e da Direita*/
	padding:0px;
	background-color:#ffffff;
	
	font-color:#000;	
	font-family: TimesNewRoman, Geneva, sans-serif; 

}



body{
	margin:10px;
}
.text {
	&-center { text-align: center; }
}
.ttu { text-transform: uppercase;
	font-weight: bold;
	font-size: 1.2em;
 }

.printer-ticket {
	display: table !important;
	width: 100%;

	/*largura do Campos que vai os textos*/
	max-width: 400px;
	font-weight: light;
	line-height: 1.3em;

	/*Espaçamento da margem da esquerda e da Direita*/
	padding: 0px;
	font-family: TimesNewRoman, Geneva, sans-serif; 

	/*tamanho da Fonte do Texto*/
	font-size: 14px; 
	font-color:#000;
	
	
	}
	
	th { 
		font-weight: inherit;

		/*Espaçamento entre as uma linha para outra*/
		padding:5px;
		text-align: center;

		/*largura dos tracinhos entre as linhas*/
		border-bottom: 1px dashed #000000;
	}


	

	
	
		
	.cor{
		color:#000000;
	}
	
	
	

	/*margem Superior entre as Linhas*/
	.margem-superior{
		padding-top:5px;
	}
	
	
}
</style>



<table class="printer-ticket">

	<tr>
		<th class="ttu" class="title" colspan="3"><?php echo $nome_sistema ?></th>

	</tr>
	<tr style="font-size: 10px">
		<th colspan="3">
			<?php echo $endereco_sistema ?> <br />
			Contato: <?php echo $telefone_fixo_sistema ?>  <?php if($cnpj_sistema != ""){ ?> / CNPJ <?php echo  $cnpj_sistema  ?><?php } ?>
		</th>
	</tr>

	<tr >
		<th colspan="3">Cliente <?php echo $nome_cliente ?> Tel: <?php echo $tel_cliente ?>			
					
			
		</th>
	</tr>
	
	<tr>
		<th class= margem-superior" colspan="3">
			<b>RECIBO DE PAGAMENTO</b>  
			
		</th>
	</tr>

	
	<tbody>
		
			<tr>
				
					<td colspan="2" width="70%"> Parcela <?php echo $parcela ?> 
					</td>				

				<td align="right">R$ <?php echo $valorF ;?></td>
			</tr>

		


				
	</tbody>
	

		<tfoot>


		<tr>

			<th class="ttu"  colspan="3" class="cor">
			<!-- _ _	_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ -->
			</th>
		</tr>	
		
			
		
		</tr>

			<tr>
			<td colspan="2">Total Pago</td>
			<td align="right"><b>R$ <?php echo $valorF ?></b></td>
		</tr>
		
			<?php if($forma_pgto != ""){ ?>
		<tr>
			<td colspan="2">Forma de Pagamento</td>
			<td align="right"><?php echo $forma_pgto ?></td>
		</tr>
		<?php } ?>


		

		<tr >
		<th colspan="3">
		</th>
		</tr>
	
		


	</tfoot>
</table>

<div style="font-size: 11px; margin-top: 15px" align="center">Data Pagamento:<b><?php echo $data_pgtoF ?></b>  /  Hora:<b><?php echo $hora ?></b></div>


<br><br>
