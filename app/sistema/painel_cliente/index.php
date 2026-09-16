<?php 
session_start();
require_once("verificar.php");
require_once("../conexao.php");

// Tenta carregar o security.php sem quebrar se estiver em outro diretório
$sec_paths = array(
    __DIR__ . '/security.php',
    __DIR__ . '/../security.php',
    __DIR__ . '/../../security.php',
);

foreach ($sec_paths as $sec_path) {
    if (file_exists($sec_path)) {
        require_once $sec_path;
        break;
    }
}

$pag_inicial = 'home';

$id_usuario = $_SESSION['id'];

$query = $pdo->query("SELECT * from clientes where id = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$nome_usuario = $res[0]['nome'];	
	$cpf_usuario = $res[0]['cpf'];
	$telefone_usuario = $res[0]['telefone'];
	$endereco_usuario = $res[0]['endereco'];	
	$cartoes = $res[0]['cartoes'];	
}

if(@$_GET['pag'] == ""){
	$pag = 'agendamentos';
}else{
	$pag = $_GET['pag'];
}

$data_atual = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');
$data_mes = $ano_atual."-".$mes_atual."-01";
$data_ano = $ano_atual."-01-01";

$partesInicial = explode('-', $data_atual);
$dataDiaInicial = $partesInicial[2];
$dataMesInicial = $partesInicial[1];

$mostrar_menu_combo = false;
$texto_combo_rodape = '';
$total_combos = 0;
$total_sessoes_combo = 0;

try{
	$query_combo = $pdo->query("SELECT a.id, a.item, a.ativo, a.cancelado, s.nome as nome_servico, s.combo_qtd_sessoes, s.combo_ativo
		FROM assinaturas a
		INNER JOIN servicos s ON a.item = s.id
		WHERE a.cliente = '$id_usuario'
		AND a.ativo = 'Sim'
		AND (a.cancelado IS NULL OR a.cancelado != 'Sim')
		AND s.combo_ativo = 'Sim'
		ORDER BY a.id DESC");
	$res_combo = $query_combo->fetchAll(PDO::FETCH_ASSOC);
	$total_combos = @count($res_combo);

	if($total_combos > 0){
		$mostrar_menu_combo = true;

		for($i=0; $i < $total_combos; $i++){
			$id_assinatura = $res_combo[$i]['id'];
			$total_sessoes = (int)$res_combo[$i]['combo_qtd_sessoes'];

			$query_consumo = $pdo->query("SELECT COUNT(*) as total FROM assinaturas_consumos where assinatura = '$id_assinatura'");
			$res_consumo = $query_consumo->fetchAll(PDO::FETCH_ASSOC);
			$usadas = (int)$res_consumo[0]['total'];

			$restantes = $total_sessoes - $usadas;
			if($restantes < 0){
				$restantes = 0;
			}

			$total_sessoes_combo += $restantes;
		}

		if($total_combos == 1){
			$id_assinatura = $res_combo[0]['id'];
			$descricao_combo = $res_combo[0]['nome_servico'];
			$total_sessoes = (int)$res_combo[0]['combo_qtd_sessoes'];

			$query_consumo = $pdo->query("SELECT COUNT(*) as total FROM assinaturas_consumos where assinatura = '$id_assinatura'");
			$res_consumo = $query_consumo->fetchAll(PDO::FETCH_ASSOC);
			$usadas = (int)$res_consumo[0]['total'];

			$sessoes_combo = $total_sessoes - $usadas;
			if($sessoes_combo < 0){
				$sessoes_combo = 0;
			}

			$texto_combo_rodape = $descricao_combo.' - '.$sessoes_combo.' sessões restantes';
		}else{
			$texto_combo_rodape = 'Você possui '.$total_combos.' combos ativos e '.$total_sessoes_combo.' sessões restantes';
		}
	}
}catch(Exception $e){
	$mostrar_menu_combo = false;
	$texto_combo_rodape = '';
}

if($pag == 'combos' and !file_exists('paginas/combos.php')){
	$pag = 'agendamentos';
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<title><?php echo $nome_sistema ?></title>
	<link rel="icon" type="image/png" href="../img/favicon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="" />
	<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>

	<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<link href="css/font-awesome.css" rel="stylesheet"> 
	<link href='css/SidebarNav.min.css' media='all' rel='stylesheet' type='text/css'/>
	<link rel="stylesheet" href="css/monthly.css">

	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/modernizr.custom.js"></script>

	<link href="//fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&amp;subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet">

	<script src="js/Chart.js"></script>
	<script src="js/metisMenu.min.js"></script>
	<script src="js/custom.js"></script>
	<link href="css/custom.css" rel="stylesheet">

	<script src="js/pie-chart.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('#demo-pie-1').pieChart({
				barColor: '#2dde98',
				trackColor: '#eee',
				lineCap: 'round',
				lineWidth: 8,
				onStep: function (from, to, percent) {
					$(this.element).find('.pie-value').text(Math.round(percent) + '%');
				}
			});

			$('#demo-pie-2').pieChart({
				barColor: '#8e43e7',
				trackColor: '#eee',
				lineCap: 'butt',
				lineWidth: 8,
				onStep: function (from, to, percent) {
					$(this.element).find('.pie-value').text(Math.round(percent) + '%');
				}
			});

			$('#demo-pie-3').pieChart({
				barColor: '#ffc168',
				trackColor: '#eee',
				lineCap: 'square',
				lineWidth: 8,
				onStep: function (from, to, percent) {
					$(this.element).find('.pie-value').text(Math.round(percent) + '%');
				}
			});
		});
	</script>

	<link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
 	<script type="text/javascript" src="DataTables/datatables.min.js"></script>
</head> 
<body class="cbp-spmenu-push">
	<div class="main-content">
		<div class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="cbp-spmenu-s1">
			<aside class="sidebar-left">
				<nav class="navbar navbar-inverse">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".collapse" aria-expanded="false" id="showLeftPush2">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<h1><a class="navbar-brand" href="index.php"><span class="fa fa-area-chart"></span> Cliente<span class="dashboard_text"></span></a></h1>
					</div>
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="sidebar-menu">
							<li class="header">MENU DE NAVEGAÇÃO</li>

							<li class="treeview <?php echo @$agendamentos ?>">
								<a href="index.php">
									<i class="fa fa-calendar-o"></i> <span>Agendamentos</span>
								</a>
							</li>

							<?php if($mostrar_menu_combo){ ?>
							<li class="treeview <?php echo @$combos ?>">
								<a href="combos">
									<i class="fa fa-clone"></i> <span>Meus Combos</span>
								</a>
							</li>
							<?php } ?>

							<li class="treeview <?php echo @$planos ?>">
								<a href="planos">
									<i class="fa fa-credit-card-alt"></i> <span>Planos / Assinaturas</span>
								</a>
							</li>

							<li class="treeview ">
								<a href="receber">
									<i class="fa fa-usd"></i> <span>Meus Pagamentos</span>
								</a>
							</li>

						</ul>
					</div>
				</nav>
			</aside>
		</div>
		
		<div class="sticky-header header-section ">
			<div class="header-left">
				<button id="showLeftPush" data-toggle="collapse" data-target=".collapse"><i class="fa fa-bars"></i></button>
				<div class="profile_details_left">
					<ul class="nofitications-dropdown">
					</ul>
					<div class="clearfix"> </div>
				</div>
			</div>

			<div class="header-right">
				<div class="profile_details">		
					<ul>
						<li class="dropdown profile_details_drop">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
								<div class="profile_img">	
									<span class="prfil-img"><img src="img/perfil/sem-foto.jpg" alt="" width="50" height="50"> </span> 
									<div class="user-name esc">
										<p><?php echo $nome_usuario ?></p>
										<span>Cliente</span>
									</div>
									<i class="fa fa-angle-down lnr"></i>
									<i class="fa fa-angle-up lnr"></i>
									<div class="clearfix"></div>	
								</div>	
							</a>
							<ul class="dropdown-menu drp-mnu">								
								<li> <a href="" data-toggle="modal" data-target="#modalPerfil"><i class="fa fa-suitcase"></i> Editar Perfil</a> </li> 
								<li> <a href="logout.php"><i class="fa fa-sign-out"></i> Sair</a> </li>
							</ul>
						</li>
					</ul>
				</div>
				<div class="clearfix"> </div>				
			</div>
			<div class="clearfix"> </div>	
		</div>

		<div id="page-wrapper">
			<?php require_once("paginas/".$pag.'.php') ?>
		</div>

		<div class="footer" >
			<div class="row">
				<?php 
				for($i=1; $i<=$quantidade_cartoes; $i++){ 
					if($cartoes >= $i){
						$valor = 0;
						$opacity = 1;
					}else{
						$valor = 1;
						$opacity = 0.4;		
					}
				?>
				<div style="display:inline-block;" align="center" >
					<img src="../../images/favicon.png" width="35px" style="filter: grayscale(<?php echo $valor ?>); filter: opacity(<?php echo $opacity ?>)">
				</div>
				<?php } ?>
			</div>

			<div align="center"><small><small>Você possui <?php echo $cartoes ?> de <?php echo $quantidade_cartoes ?> cartões Fidelidade</small></small></div>

			<?php if($texto_combo_rodape != ''){ ?>
			<div align="center"><small><small><?php echo $texto_combo_rodape ?></small></small></div>
			<?php } ?>
		</div>
	</div>

	<script src="js/classie.js"></script>
	<script>
		var menuLeft = document.getElementById( 'cbp-spmenu-s1' ),
			showLeftPush = document.getElementById( 'showLeftPush' ),
			body = document.body;
			
		showLeftPush.onclick = function() {
			classie.toggle( this, 'active' );
			classie.toggle( body, 'cbp-spmenu-push-toright' );
			classie.toggle( menuLeft, 'cbp-spmenu-open' );
			disableOther( 'showLeftPush' );
		};
		
		function disableOther( button ) {
			if( button !== 'showLeftPush' ) {
				classie.toggle( showLeftPush, 'disabled' );
			}
		}

		showLeftPush2 = document.getElementById( 'showLeftPush2' ),
		
		showLeftPush2.onclick = function() {
			classie.toggle( this, 'active' );
			classie.toggle( body, 'cbp-spmenu-push-toright' );
			classie.toggle( menuLeft, 'cbp-spmenu-open' );
			disableOther2( 'showLeftPush2' );
		};

		function disableOther2( button ) {
			if( button !== 'showLeftPush2' ) {
				classie.toggle( showLeftPush2, 'disabled' );
			}
		}
	</script>

	<script src="js/jquery.nicescroll.js"></script>
	<script src="js/scripts.js"></script>
	
	<script src='js/SidebarNav.min.js' type='text/javascript'></script>
	<script>
		$('.sidebar-menu').SidebarNav()
	</script>
	<script src="js/bootstrap.js"> </script>
</body>
</html>