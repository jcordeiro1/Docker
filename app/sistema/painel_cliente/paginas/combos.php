<?php 
if(!isset($pdo)){
	require_once("../conexao.php");
}

if(!isset($id_usuario)){
	@session_start();
	$id_usuario = @$_SESSION['id'];
}

$query = $pdo->query("SELECT a.*, s.nome as nome_servico, s.combo_qtd_sessoes, s.combo_ativo
	FROM assinaturas a
	INNER JOIN servicos s ON a.item = s.id
	WHERE a.cliente = '$id_usuario'
	AND a.ativo = 'Sim'
	AND (a.cancelado IS NULL OR a.cancelado != 'Sim')
	AND s.combo_ativo = 'Sim'
	ORDER BY a.id DESC");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
?>

<div class="main-page">
	<div class="tables">
		<h3 class="title1">Meus Combos</h3>

		<div class="panel-body widget-shadow">
			<?php if($total_reg > 0){ ?>

				<div class="table-responsive">
					<table class="table table-bordered table-hover" id="tabela-combos">
						<thead>
							<tr>
								<th>Serviço</th>
								<th>Total</th>
								<th>Usadas</th>
								<th>Restantes</th>
								<th>Data</th>
								<th>Vencimento</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>

							<?php 
							for($i=0; $i < $total_reg; $i++){
								$id = $res[$i]['id'];
								$nome_servico = $res[$i]['nome_servico'];
								$total_sessoes = (int)$res[$i]['combo_qtd_sessoes'];
								$data = $res[$i]['data'];
								$vencimento = $res[$i]['vencimento'];
								$ativo = $res[$i]['ativo'];

								$query2 = $pdo->query("SELECT COUNT(*) as total FROM assinaturas_consumos where assinatura = '$id'");
								$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
								$usadas = (int)$res2[0]['total'];
								$restantes = $total_sessoes - $usadas;

								if($restantes < 0){
									$restantes = 0;
								}

								$dataF = implode('/', array_reverse(explode('-', $data)));
								$vencimentoF = implode('/', array_reverse(explode('-', $vencimento)));

								if($ativo == 'Sim' && $restantes > 0){
									$status = 'Ativo';
									$classe_status = 'label label-success';
								}else{
									$status = 'Finalizado';
									$classe_status = 'label label-default';
								}
							?>

							<tr>
								<td><?php echo $nome_servico ?></td>
								<td><?php echo $total_sessoes ?></td>
								<td><?php echo $usadas ?></td>
								<td><b><?php echo $restantes ?></b></td>
								<td><?php echo $dataF ?></td>
								<td><?php echo $vencimentoF ?></td>
								<td><span class="<?php echo $classe_status ?>"><?php echo $status ?></span></td>
							</tr>

							<?php } ?>

						</tbody>
					</table>
				</div>

			<?php }else{ ?>
				<div class="alert alert-info" style="margin-bottom: 0px">
					Você não possui combos cadastrados no momento.
				</div>
			<?php } ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		$('#tabela-combos').DataTable({
			"ordering": false,
			"stateSave": true
		});
	});
</script>