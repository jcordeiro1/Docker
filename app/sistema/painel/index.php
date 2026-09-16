<?php 
@session_start();
require_once("verificar.php");
require_once("../conexao.php");

$pag_inicial = 'home';

$id_usuario = $_SESSION['id'];

$query = $pdo->query("SELECT * from usuarios where id = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$nome_usuario = $res[0]['nome'];
	$email_usuario = $res[0]['email'];
	$cpf_usuario = $res[0]['cpf'];
	$senha_usuario = $res[0]['senha'];
	$nivel_usuario = $res[0]['nivel'];
	$telefone_usuario = $res[0]['telefone'];
	$endereco_usuario = $res[0]['endereco'];
	$foto_usuario = $res[0]['foto'];
	$atendimento = $res[0]['atendimento'];
	$intervalo_horarios = $res[0]['intervalo'];
}

if(@$_SESSION['nivel'] != 'Administrador'){
	require_once("verificar-permissoes.php");
}

if(@$_GET['pag'] == ""){
	$pag = $pag_inicial;
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
?>

<!DOCTYPE HTML>
<html>
<head>

	<title><?php echo $nome_sistema ?></title>
	<link rel="icon" type="sistema/painel/img/logo.png" href="../img/favicon.png">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="keywords" content="" />
	<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
	<!-- Bootstrap Core CSS -->
	<link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
	<!-- Custom CSS -->
	<link href="css/style.css" rel='stylesheet' type='text/css' />
	<!-- font-awesome icons CSS -->
	<link href="css/font-awesome.css" rel="stylesheet"> 
	<!-- side nav css file -->
	<link href='css/SidebarNav.min.css' media='all' rel='stylesheet' type='text/css'/>
	<!-- //side nav css file -->
	<link rel="stylesheet" href="css/monthly.css">
	<!-- js-->
	<script src="js/jquery-1.11.1.min.js"></script>
	<script src="js/modernizr.custom.js"></script>
	<!--webfonts-->
	<link href="//fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i&amp;subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet">
	<!-- chart -->
	<script src="js/Chart.js"></script>
	<!-- Metis Menu -->
	<script src="js/metisMenu.min.js"></script>
	<script src="js/custom.js"></script>
	<link href="css/custom.css" rel="stylesheet">
	<!--//Metis Menu -->
	<style>
		#chartdiv {
			width: 100%;
			height: 295px;
		}
	</style>
	<!--pie-chart --><!-- index page sales reviews visitors pie chart -->
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
				barColor: '#e32424',
				trackColor: '#eee',
				lineCap: 'square',
				lineWidth: 8,
				onStep: function (from, to, percent) {
					$(this.element).find('.pie-value').text(Math.round(percent) + '%');
				}
			});


		});
		
	</script>
	<!-- //pie-chart --><!-- index page sales reviews visitors pie chart -->


	<link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>
 	<script type="text/javascript" src="DataTables/datatables.min.js"></script>

<!-- Trumbowyg  -->
    <link rel="stylesheet" href="../dist/ui/trumbowyg.min.css">
    <link rel="stylesheet" href="../dist/plugins/emoji/ui/trumbowyg.emoji.min.css">
    <link rel="stylesheet" href="../dist/plugins/colors/ui/trumbowyg.colors.min.css">
	
</head> 
<body class="cbp-spmenu-push">
	<div class="main-content">
		<div class="cbp-spmenu cbp-spmenu-vertical cbp-spmenu-left" id="cbp-spmenu-s1">
			<!--left-fixed -navigation-->
			<aside class="sidebar-left" style="overflow: scroll; height:100%; scrollbar-width: thin;">
				<nav class="navbar navbar-inverse" >
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".collapse" aria-expanded="false" id="showLeftPush2">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<a class="navbar-brand" href="index.php"><img src="img/barbertot.png" width="100px" style="margin-right: 0px"></a>
					</div>
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="sidebar-menu">
							<li class="header">MENU DE NAVEGAÇÃO</li>


							<li class="treeview <?php echo @$home ?>">
								<a href="index.php">
								<i class="fa fa-dashboard"></i> <span>Home</span>
								</a>
							</li>

						
							<li class="treeview">
								<a href="#">
									<i class="fa fa-dashboard"></i>
									<span>Configurações)</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">

									<li class="treeview <?= @$dispositivos ?>"><a href="dispositivos"><i class="fa fa-mobile"></i>Conectar Dispositivo</a></li>
									<li class="<?php echo @$duvidas ?>"><a href="duvidas"><i class="fa fa-question"></i>Dúvidas Frequentes</a></li>	
									<li class="<?php echo @$doc ?>"><a href="https://doc.barberbot.com.br/" target="_blank"><i class="fa fa-question"></i>Documentos</a></li>
								</ul>
							</li>

							<li class="treeview <?php echo @$comanda ?>">
								<a href="comanda">
									<i class="fa fa-file-o"></i> <span>Nova Comanda</span>
								</a>
							</li>


							<li class="treeview <?php echo $menu_pessoas ?>">
								<a href="#">
									<i class="fa fa-users"></i>
									<span>Pessoas</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
									<li class="<?php echo @$usuarios ?>"><a href="usuarios"><i class="fa fa-angle-right"></i>Usuários</a></li>
									<li class="<?php echo @$funcionarios ?>"><a href="funcionarios"><i class="fa fa-angle-right"></i>Funcionários</a></li>
									<li class="<?php echo @$clientes ?>"><a href="clientes"><i class="fa fa-angle-right"></i>Clientes</a></li>
									<li class="<?php echo @$clientes_retorno ?>"><a href="clientes_retorno"><i class="fa fa-angle-right"></i>Clientes Retornos</a></li>
									<li class="<?php echo @$fornecedores ?>"><a href="fornecedores"><i class="fa fa-angle-right"></i>Fornecedores</a></li>

								</ul>
							</li>

							<li class="treeview <?php echo @$menu_cadastros ?>" >
								<a href="#">
									<i class="fa fa-plus"></i>
									<span>Cadastros</span>
									<i class="fa fa-angle-left pull-right"></i></a>
								<ul class="treeview-menu">
									<li class="<?php echo @$servicos ?>"><a href="servicos"><i class="fa fa-angle-right"></i>Serviços</a></li>
									<li class="<?php echo @$cargos ?>"><a href="cargos"><i class="fa fa-angle-right"></i>Cargos</a></li>
									<li class="<?php echo @$cat_servicos ?>"><a href="cat_servicos"><i class="fa fa-angle-right"></i>Categoria Serviços</a></li>
									<li class="<?php echo @$grupos ?>"><a href="grupos"><i class="fa fa-angle-right"></i>Grupo Acessos</a></li>
									<li class="<?php echo @$acessos ?>"><a href="acessos"><i class="fa fa-angle-right"></i>Acessos</a></li>
									<li class="<?php echo @$pgto ?>"><a href="pgto"><i class="fa fa-angle-right"></i>Formas de Pagamento</a></li>
									<li class="<?php echo @$dias_bloqueio ?>"><a href="dias_bloqueio"><i class="fa fa-angle-right"></i>Bloqueio de Dias</a></li>
									<li class="<?php echo @$assinaturas ?>"><a href="assinaturas"><i class="fa fa-angle-right"></i>Assinaturas</a></li>
									<li class="<?php echo @$frequencias ?>"><a href="frequencias"><i class="fa fa-angle-right"></i>Frequências</a></li>
									
								</ul>
							</li>


							<li class="treeview <?php echo $menu_proteses ?>">
								<a href="#">
									<i class="fa fa-user"></i>
									<span>Prótese Capilar</span>
									<i class="fa fa-angle-left pull-right"></i></a>
								<ul class="treeview-menu">
									<li class="<?php echo @$proteses ?>"><a href="proteses"><i class="fa fa-angle-right"></i>Próteses</a></li>
									<li class="<?php echo @$simulador ?>"><a href="simulador"><i class="fa fa-angle-right"></i>Simulador</a></li>
									<li class="<?php echo @$analises ?>"><a href="analises"><i class="fa fa-angle-right"></i>Analises</a></li>
									<li class="<?php echo @$manutencoes ?>"><a href="manutencoes"><i class="fa fa-angle-right"></i>Manutenção</a></li>
								
								</ul>
							</li>

							<li class="treeview <?php echo $menu_produtos ?>">
								<a href="#">
									<i class="fa fa-plus"></i>
									<span>Produtos</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">

									<li class="<?php echo @$produtos ?>"><a href="produtos"><i class="fa fa-angle-right"></i>Produtos</a></li>
									<li class="<?php echo @$cat_produtos ?>"><a href="cat_produtos"><i class="fa fa-angle-right"></i>Categorias</a></li>
									<li class="<?php echo @$estoque ?>"><a href="estoque"><i class="fa fa-angle-right"></i>Estoque Baixo</a></li>
									<li class="<?php echo @$saidas ?>"><a href="saidas"><i class="fa fa-angle-right"></i>Saídas</a></li>
									<li class="<?php echo @$entradas ?>"><a href="entradas"><i class="fa fa-angle-right"></i>Entradas</a></li>
								</ul>
							</li>


							<li class="treeview <?php echo @$menu_financeiro ?>" >
								<a href="#">
									<i class="fa fa-usd"></i>
									<span>Financeiro</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
									<li class="<?php echo @$vendas ?>"><a href="vendas"><i class="fa fa-angle-right"></i>Vendas</a></li>
									<li class="<?php echo @$compras ?>"><a href="compras"><i class="fa fa-angle-right"></i>Compras</a></li>
									<li class="<?php echo @$pagar ?>"><a href="pagar"><i class="fa fa-angle-right"></i>Contas à Pagar</a></li>
									<li class="<?php echo @$receber ?>"><a href="receber"><i class="fa fa-angle-right"></i>Contas à Receber</a></li>
									<li class="<?php echo @$receber_vencidas ?>"><a href="receber_vencidas"><i class="fa fa-angle-right"></i>Recebimentos Vencidos</a></li>	
									<li class="<?php echo @$comissoes ?>"><a href="comissoes"><i class="fa fa-angle-right"></i>Comissões</a></ul>
							</li>
							
							<li class="treeview">
								<a href="#">
									<i class="fa fa-credit-card-alt"></i>
									<span>Assinaturas</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
									<li class="treeview <?php echo @$planos ?>"><a href="planos"><i class="fa fa-angle-right"></i>Planos</a></li>
									<li class="<?php echo @$receber ?>"><a href="receber"><i class="fa fa-angle-right"></i>Receber</a></li>	
									<li class="treeview <?php echo @$cobrancas_pagas ?>"><a href="pagas"><i class="fa fa-angle-right"></i>Cobranças Pagas</a></li>
								</ul>
							</li>
							
							<li class="treeview <?php echo $menu_agendamentos ?>">
								<a href="#">
									<i class="fa fa-calendar-o"></i>
									<span>Agendamento / Serviço</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
									<li class="<?php echo @$agendamentos ?>"><a href="agendamentos"><i class="fa fa-angle-right"></i>Agendamentos</a></li>
									<li class="<?php echo @$servicos_agenda ?>"><a href="servicos_agenda"><i class="fa fa-angle-right"></i>Serviços</a></li>
								</ul>
							</li>

							<li class="treeview <?php echo @$menu_relatorio ?>" >
								<a href="#">
									<i class="fa fa-file-pdf-o"></i>
									<span>Relatórios</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">

									<li class="<?php echo @$rel_produtos ?>"><a href="rel/rel_produtos_class.php" target="_blank"><i class="fa fa-angle-right"></i>Relatório de Produtos</a></li>
									<li class="<?php echo @$rel_entradas ?>"><a href="#" data-toggle="modal" data-target="#RelEntradas"><i class="fa fa-angle-right"></i>Entradas / Ganhos</a></li>
									<li class="<?php echo @$rel_saidas ?>"><a href="#" data-toggle="modal" data-target="#RelSaidas"><i class="fa fa-angle-right"></i>Saídas / Despesas</a></li>
									<li class="<?php echo @$rel_comissoes ?>"><a href="#" data-toggle="modal" data-target="#RelComissoes"><i class="fa fa-angle-right"></i>Relatório de Comissões</a></li>
									<li class="<?php echo @$rel_contas ?>"><a href="#" data-toggle="modal" data-target="#RelCon"><i class="fa fa-angle-right"></i>Relatório de Contas</a></li>
									<li class="<?php echo @$rel_servicos ?>"><a href="#" data-toggle="modal" data-target="#RelServicos"><i class="fa fa-angle-right"></i>Relatório de Serviços</a></li>
									<li class="<?php echo @$rel_aniv ?>"><a href="#" data-toggle="modal" data-target="#RelAniv"><i class="fa fa-angle-right"></i>Relatório de Aniversáriantes</a></li>
									<li class="<?php echo @$rel_lucro ?>"><a href="#" data-toggle="modal" data-target="#RelLucro"><i class="fa fa-angle-right"></i>Demonstrativo de Lucro</a></li>
									<li class="<?php echo @$rel_ina ?>" ><a target="_blank" href="rel/sintetico_inadimplentes_class.php" ><i class="fa fa-angle-right"></i>Relatório de Inadimplentes</a></li>
								</ul>
							</li>
							
							<li class="treeview <?php echo @$marketing ?>" >
								<a href="#">
									<i class="fa fa-whatsapp"></i>
									<span> Marketing</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">
							<li class="treeview <?php echo @$marketing ?>">
								<a href="marketing">
									<i class="fa fa-angle-right"></i> <span>Campanha Marketing</span>
								</a></li>
								    <li class="<?php echo @$grupos_disparos ?>"><a href="grupos_disparos" target="_blank"><i class="fa fa-angle-right"></i>Grupos Disparos</a></li>
								</ul>
							</li>

							<li class="treeview <?php echo @$calendario ?>">
								<a href="calendario">
									<i class="fa fa-calendar-o"></i> <span>Calendário</span>
								</a>
							</li>

							<li class="treeview <?php echo $menu_site ?>" >
								<a href="#">
									<i class="fa fa-globe"></i>
									<span>Dados do Site</span>
									<i class="fa fa-angle-left pull-right"></i>
								</a>
								<ul class="treeview-menu">

									<li class="<?php echo @$avaliacoes ?>"><a href="avaliacoes"><i class="fa fa-angle-right"></i>Avaliações</a></li>
									<li class="<?php echo @$textos_index ?>"><a href="textos_index"><i class="fa fa-angle-right"></i>Textos Index</a></li>
									<li class="<?php echo @$comentarios ?>"><a href="comentarios"><i class="fa fa-angle-right"></i>Comentários</a></li>
									<li class="<?php echo @$faq ?>"><a href="faq"><i class="fa fa-angle-right"></i> Dúvidas Frequentes</a></li>
									<li class="<?php echo @$foto_admin ?>"><a href="foto_admin"><i class="fa fa-angle-right"></i> Fotos home</a></li>
									<li class="<?php echo @$site ?>"><a href="site"><i class="fa fa-angle-right"></i>Portfolio</a></li>
									<li class="<?php echo @$bio ?>"><a href="bio"><i class="fa fa-angle-right"></i>Bio</a></li>
								</ul>
							</li>

							
							<li class="treeview <?php echo @$caixas ?>" >
								<a href="caixas">
									<i class="fa fa-server"></i> <span>Caixas</span>
								</a>
							</li>	

							<li class="treeview <?php echo @$anotacoes ?>">
								<a href="anotacoes">
									<i class="fa fa-sticky-note"></i> <span>Anotações</span>
								</a>
							</li>

							<?php if(@$atendimento == 'Sim'){ ?>
								<li class="treeview">
									<a href="agenda">
										<i class="fa fa-calendar-o"></i> <span>Minha Agenda</span>
									</a>
								</li>
								
								<li class="treeview">
									<a href="meus_servicos">
										<i class="fa fa-server"></i> <span>Meus Serviços</span>
									</a>
								</li>

								<li class="treeview">
									<a href="minhas_comissoes">
										<i class="fa fa-server"></i> <span>Minhas Comissões</span>
									</a>
								</li>				

								<li class="treeview">
									<a href="#">
										<i class="fa fa-usd"></i>
										<span>Meus Horário / Dias</span>
										<i class="fa fa-clock-o pull-right"></i>
									</a>
									<ul class="treeview-menu">

										<li><a href="dias"><i class="fa fa-angle-right"></i>Horários / Dias</a></li>
										<li><a href="servicos_func"><i class="fa fa-angle-right"></i>Lançar Serviços</a></li>									
										<li><a href="dias_bloqueio"><i class="fa fa-angle-right"></i>Bloqueio de Dias</a></li>													
								
								</ul>
							</li>

							<?php } ?>

							<li class="treeview <?php echo @$verificar_pgtos ?>">
								<a href="#" onclick="verificarPg()">
									<i class="fa fa-spinner"></i> <span>Verificar Pagamentos</span>
								</a>
							</li>

						</ul>
					</div>
<!-- /.navbar-collapse -->
				</nav>
			</aside>
		</div>
<!--left-fixed -navigation-->
		
		<!-- header-starts -->
		<div class="sticky-header header-section ">
			<div class="header-left">
<!--toggle button start-->
				<button id="showLeftPush" data-toggle="collapse" data-target=".collapse"><i class="fa fa-bars"></i></button>
<!--toggle button end-->
				<div class="profile_details_left"><!--notifications of menu start -->
					<ul class="nofitications-dropdown">


						<?php if($atendimento == 'Sim'){ 

													//totalizando agendamentos dia usuario
							$query = $pdo->query("SELECT * FROM agendamentos where data = curDate() and funcionario = '$id_usuario' and status = 'Agendado'");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							$total_agendamentos_hoje_usuario_pendentes = @count($res);

							?>
							<li class="dropdown head-dpdn">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-bell"></i><span class="badge text-danger"><?php echo $total_agendamentos_hoje_usuario_pendentes ?></span></a>
								<ul class="dropdown-menu">
									<li>
										<div class="notification_header" align="center">
											<h3><?php echo $total_agendamentos_hoje_usuario_pendentes ?> Agendamento Pendente Hoje</h3>
										</div>
									</li>

									<?php 
									for($i=0; $i < @count($res); $i++){
										foreach ($res[$i] as $key => $value){}
											$id = $res[$i]['id'];								
										$cliente = $res[$i]['cliente'];
										$hora = $res[$i]['hora'];
										$servico = $res[$i]['servico'];
										$horaF = date("H:i", strtotime($hora));


										$query2 = $pdo->query("SELECT * FROM servicos where id = '$servico'");
										$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
										if(@count($res2) > 0){
											$nome_serv = $res2[0]['nome'];
											$valor_serv = $res2[0]['valor'];
										}else{
											$nome_serv = 'Não Lançado';
											$valor_serv = '';
										}


										$query2 = $pdo->query("SELECT * FROM clientes where id = '$cliente'");
										$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
										if(@count($res2) > 0){
											$nome_cliente = $res2[0]['nome'];
										}else{
											$nome_cliente = 'Sem Cliente';
										}
										?>
										<li>									
											<div class="notification_desc">
												<p><b><?php echo $horaF ?> </b> - <?php echo $nome_cliente ?> / <?php echo $nome_serv ?></p>
												<p><span></span></p>
											</div>
											<div class="clearfix"></div>	
										</li>
										<?php 
									}
									?>
									
									
									
									<li>
										<div class="notification_bottom" style="background: #ffe8e6">
											<a href="agenda">Ver Agendamentos</a>
										</div> 
									</li>
								</ul>
							</li>	
						<?php } ?>



						<?php if(@$rel_aniv == ''){ 

						//totalizando aniversariantes do dia
							$query = $pdo->query("SELECT * FROM clientes where month(data_nasc) = '$dataMesInicial' and day(data_nasc) = '$dataDiaInicial' order by data_nasc asc, id asc");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							$total_aniversariantes_hoje = @count($res);

							?>
							<li class="dropdown head-dpdn">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-birthday-cake" style="color:#FFF"></i><span class="badge" style="background: #2b6b39"><?php echo $total_aniversariantes_hoje ?></span></a>
								<ul class="dropdown-menu">
									<li>
										<div class="notification_header" align="center">
											<h3><?php echo $total_aniversariantes_hoje ?> Aniversariando Hoje</h3>
										</div>
									</li>

									<?php 
									for($i=0; $i < @count($res); $i++){
										foreach ($res[$i] as $key => $value){}
											
											$nome = $res[$i]['nome'];	
										$telefone = $res[$i]['telefone'];														

										?>
										<li>									
											<div class="notification_desc">
												<p><b><?php echo $nome ?> </b> - <?php echo $telefone ?> </p>
												<p><span></span></p>
											</div>
											<div class="clearfix"></div>	
										</li>
										<?php 
									}
									?>
									
									
									
									<li>
										<div class="notification_bottom" style="background: #d9ffe1">
											<a href="#" data-toggle="modal" data-target="#RelAniv">Relatório Aniversáriantes</a>
										</div> 
									</li>
								</ul>
							</li>	
						<?php } ?>


						<?php if(@$clientes_retorno == ''){ 

						//totalizando aniversariantes do dia
							$query = $pdo->query("SELECT * FROM clientes where alertado != 'Sim' and data_retorno < curDate() ORDER BY data_retorno asc");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							$total_clientes_retorno = @count($res);

							?>
							<li class="dropdown head-dpdn">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-users" style="color:#FFF"></i><span class="badge" style="background: #c93504"><?php echo $total_clientes_retorno ?></span></a>
								<ul class="dropdown-menu">
									<li>
										<div class="notification_header" align="center">
											<h3><?php echo $total_clientes_retorno ?> Cliente com Retorno Pendente</h3>
										</div>
									</li>

									<?php 
									for($i=0; $i < @count($res); $i++){
										foreach ($res[$i] as $key => $value){}
											
											$nome = $res[$i]['nome'];	
										$telefone = $res[$i]['telefone'];														

										?>
										<li>									
											<div class="notification_desc">
												<p><b><?php echo $nome ?> </b> - <?php echo $telefone ?> </p>
												<p><span></span></p>
											</div>
											<div class="clearfix"></div>	
										</li>
										<?php 
									}
									?>
									
									
									
									<li>
										<div class="notification_bottom" style="background: #ffcdbd">
											<a href="clientes_retorno">Ver Clientes</a>
										</div> 
									</li>
								</ul>
							</li>	
						<?php } ?>


						<?php if(@$comentarios == ''){ 

						//totalizando aniversariantes do dia
							$query = $pdo->query("SELECT * FROM comentarios where ativo != 'Sim'");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							$total_comentarios = @count($res);

							?>
							<li class="dropdown head-dpdn">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-comment" style="color:#FFF"></i><span class="badge" style="background: #22168a"><?php echo $total_comentarios ?></span></a>
								<ul class="dropdown-menu">
									<li>
										<div class="notification_header" align="center">
											<h3><?php echo $total_comentarios ?> Depoimentos Pendente</h3>
										</div>
									</li>

									<?php 
									for($i=0; $i < @count($res); $i++){
										foreach ($res[$i] as $key => $value){}
											
											$nome = $res[$i]['nome'];
										

										?>
										<li>									
											<div class="notification_desc">
												<p><b>Cliente: <?php echo $nome ?> </b> </p>
												<p><span></span></p>
											</div>
											<div class="clearfix"></div>	
										</li>
										<?php 
									}
									?>
									
									
									
									<li>
										<div class="notification_bottom" style="background: #d8d4fc">
											<a href="comentarios">Ver Depoimentos</a>
										</div> 
									</li>
								</ul>
							</li>	
						<?php } ?>						


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
									<span class="prfil-img"><img src="img/perfil/<?php echo $foto_usuario ?>" alt="" width="50" height="50"> </span> 
									<div class="user-name esc">
										<p><?php echo $nome_usuario ?></p>
										<span><?php echo $nivel_usuario ?></span>
									</div>
									<i class="fa fa-angle-down lnr"></i>
									<i class="fa fa-angle-up lnr"></i>
									<div class="clearfix"></div>	
								</div>	
							</a>
							<ul class="dropdown-menu drp-mnu">
								<?php if(@$configuracoes == ''){ ?>
									<li> <a href="configuracoes" ><i class="fa fa-cog"></i> Configurações</a> </li> 	
								<?php } ?>

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
		<!-- //header-ends -->



		<!-- main content start-->
		<div id="page-wrapper">
			<?php require_once("paginas/".$pag.'.php') ?>
		</div>



<!--footer-->
		<div class="footer">
			<p> <a href="https://barberbot.com.br/" target="_blank">BarberBot</a></p>		
		</div>
		<!--//footer-->
	</div>



	<!-- Classie --><!-- for toggle left push menu script -->
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
	<!-- //Classie --><!-- //for toggle left push menu script -->


	<!--scrolling js-->
	<script src="js/jquery.nicescroll.js"></script>
	<script src="js/scripts.js"></script>
	<!--//scrolling js-->
	
	<!-- side nav js -->
	<script src='js/SidebarNav.min.js' type='text/javascript'></script>
	<script>
		$('.sidebar-menu').SidebarNav()
	</script>
	<!-- //side nav js -->
	
	
	
	<!-- Bootstrap Core JavaScript -->
	<script src="js/bootstrap.js"> </script>
	<!-- //Bootstrap Core JavaScript -->
	
</body>
</html>


<!-- SweetAlert JS -->
<script src="js/sweetalert2.all.min.js"></script>
<script src="js/sweetalert1.min.css"></script>
<script src="js/alertas.js"></script>


<!-- Mascaras JS -->
<script type="text/javascript" src="js/mascaras.js"></script>

<!-- Ajax para funcionar Mascaras JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script> 




<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style type="text/css">
	.select2-selection__rendered {
		line-height: 36px !important;
		font-size:16px !important;
		color:#666666 !important;

	}

	.select2-selection {
		height: 36px !important;
		font-size:16px !important;
		color:#666666 !important;

	}
</style>  


<!-- Modal Perfil-->
<div class="modal fade" id="modalPerfil" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Editar Perfil</h4>
				<button id="btn-fechar-perfil" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true" >&times;</span>
				</button>
			</div>
			<form method="post" id="form-perfil">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail1">Nome</label>
								<input type="text" class="form-control" id="nome-perfil" name="nome" placeholder="Nome" value="<?php echo $nome_usuario ?>" required>    
							</div> 	
						</div>
						<div class="col-md-6">

							<div class="form-group">
								<label for="exampleInputEmail1">Email</label>
								<input type="email" class="form-control" id="email-perfil" name="email" placeholder="Email" value="<?php echo $email_usuario ?>" required>    
							</div> 	
						</div>
					</div>


					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="exampleInputEmail1">Telefone</label>
								<input type="text" class="form-control" id="telefone-perfil" name="telefone" placeholder="Telefone" value="<?php echo $telefone_usuario ?>" >    
							</div> 	
						</div>
						<div class="col-md-6">
							
							<div class="form-group">
								<label for="exampleInputEmail1">CPF</label>
								<input type="text" class="form-control" id="cpf-perfil" name="cpf" placeholder="CPF" value="<?php echo $cpf_usuario ?>">    
							</div> 	
						</div>
					</div>


					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="exampleInputEmail1">Senha</label>
								<input type="password" class="form-control" id="senha-perfil" name="senha" placeholder="Senha" value="<?php echo $senha_usuario ?>" required>    
							</div> 	
						</div>
						<div class="col-md-4">
							
							<div class="form-group">
								<label for="exampleInputEmail1">Confirmar Senha</label>
								<input type="password" class="form-control" id="conf-senha-perfil" name="conf_senha" placeholder="Confirmar Senha" required>    
							</div> 	
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label for="exampleInputEmail1">Atendimento</label>
								<select class="form-control" name="atendimento" id="atendimento-perfil">
									<option <?php if($atendimento == 'Sim'){ ?> selected <?php } ?> value="Sim">Sim</option>
									<option <?php if($atendimento == 'Não'){ ?> selected <?php } ?> value="Não">Não</option>
								</select>  
							</div> 	
						</div>

					</div>


					<div class="row">
						<div class="col-md-8">
							<div class="form-group">
								<label for="exampleInputEmail1">Endereço</label>
								<input type="text" class="form-control" id="endereco-perfil" name="endereco" placeholder="Rua X Número 1 Bairro xxx" value="<?php echo $endereco_usuario ?>" >    
							</div> 	
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label for="exampleInputEmail1">Intervalo Minutos</label>
								<input type="number" class="form-control" id="intervalo_perfil" name="intervalo" placeholder="Intervalo Horários" value="<?php echo $intervalo_horarios ?>" required>    
							</div> 	
						</div>
						
					</div>





					<div class="row">
						<div class="col-md-8">						
							<div class="form-group"> 
								<label>Foto</label> 
								<input class="form-control" type="file" name="foto" onChange="carregarImgPerfil();" id="foto-usu">
							</div>						
						</div>
						<div class="col-md-4">
							<div id="divImg">
								<img src="img/perfil/<?php echo $foto_usuario ?>"  width="80px" id="target-usu">									
							</div>
						</div>

					</div>


					
					<input type="hidden" name="id" value="<?php echo $id_usuario ?>">

					<br>
					<small><div id="mensagem-perfil" align="center"></div></small>
				</div>
				<div class="modal-footer">      
					<button type="submit" class="btn btn-primary">Editar Perfil</button>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Modal Config-->
<div class="modal fade" id="modalConfig" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Editar Configurações</h4>
				<button id="btn-fechar-config" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true" >&times;</span>
				</button>
			</div>
			
		</div>
	</div>
</div>


<!-- Modal Rel Entradas / Ganhos -->
<div class="modal fade" id="RelEntradas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Relatório de Ganhos
					<small>(
						<a href="#" onclick="datas('1980-01-01', 'tudo-Ent', 'Ent')">
							<span style="color:#000" id="tudo-Ent">Tudo</span>
						</a> / 
						<a href="#" onclick="datas('<?php echo $data_atual ?>', 'hoje-Ent', 'Ent')">
							<span id="hoje-Ent">Hoje</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_mes ?>', 'mes-Ent', 'Ent')">
							<span style="color:#000" id="mes-Ent">Mês</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_ano ?>', 'ano-Ent', 'Ent')">
							<span style="color:#000" id="ano-Ent">Ano</span>
						</a> 
					)</small>



				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="rel/rel_entradas_class.php" target="_blank">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6">						
							<div class="form-group"> 
								<label>Data Inicial</label> 
								<input type="date" class="form-control" name="dataInicial" id="dataInicialRel-Ent" value="<?php echo date('Y-m-d') ?>" required> 
							</div>						
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label>Data Final</label> 
								<input type="date" class="form-control" name="dataFinal" id="dataFinalRel-Ent" value="<?php echo date('Y-m-d') ?>" required> 
							</div>
						</div>

						<div class="col-md-6">						
							<div class="form-group"> 
								<label>Entradas / Ganhos</label> 
								<select class="form-control sel13" name="filtro" style="width:100%;">
									<option value="">Todas</option>
									<option value="Venda">Vendas</option>
									<option value="Serviço">Serviços</option>
									<option value="Conta">Demais Ganhos</option>
									
								</select> 
							</div>						
						</div>


						<div class="col-md-6">						
							<div class="form-group"> 
								<label>Selecionar Cliente</label> 
								<select class="form-control selcli" name="cliente" style="width:100%;" > 
									<option value="">Todos</option>
									<?php 
									$query = $pdo->query("SELECT * FROM clientes");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if($total_reg > 0){
										for($i=0; $i < $total_reg; $i++){
											foreach ($res[$i] as $key => $value){}
												echo '<option value="'.$res[$i]['id'].'">'.$res[$i]['nome'].'</option>';
										}
									}
									?>


								</select>    
							</div>						
						</div>


					</div>


					

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Gerar Relatório</button>
				</div>
			</form>

		</div>
	</div>
</div>


<!-- Modal Rel Saidas / Despesas -->
<div class="modal fade" id="RelSaidas" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Relatório de Saídas
					<small>(
						<a href="#" onclick="datas('1980-01-01', 'tudo-Saida', 'Saida')">
							<span style="color:#000" id="tudo-Saida">Tudo</span>
						</a> / 
						<a href="#" onclick="datas('<?php echo $data_atual ?>', 'hoje-Saida', 'Saida')">
							<span id="hoje-Saida">Hoje</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_mes ?>', 'mes-Saida', 'Saida')">
							<span style="color:#000" id="mes-Saida">Mês</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_ano ?>', 'ano-Saida', 'Saida')">
							<span style="color:#000" id="ano-Saida">Ano</span>
						</a> 
					)</small>



				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="rel/rel_saidas_class.php" target="_blank">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-4">						
							<div class="form-group"> 
								<label>Data Inicial</label> 
								<input type="date" class="form-control" name="dataInicial" id="dataInicialRel-Saida" value="<?php echo date('Y-m-d') ?>" required> 
							</div>						
						</div>
						<div class="col-md-4">
							<div class="form-group"> 
								<label>Data Final</label> 
								<input type="date" class="form-control" name="dataFinal" id="dataFinalRel-Saida" value="<?php echo date('Y-m-d') ?>" required> 
							</div>
						</div>

						<div class="col-md-4">						
							<div class="form-group"> 
								<label>Saídas / Despesas</label> 
								<select class="form-control sel13" name="filtro" style="width:100%;">
									<option value="">Todas</option>
									<option value="Conta">Despesas</option>
									<option value="Comissão">Comissões</option>
									<option value="Compra">Compras</option>
									
								</select> 
							</div>						
						</div>

					</div>


					

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Gerar Relatório</button>
				</div>
			</form>

		</div>
	</div>
</div>


<!-- Modal Rel Comissoes -->
<div class="modal fade" id="RelComissoes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Relatório de Comissões
					<small>(
						<a href="#" onclick="datas('1980-01-01', 'tudo-Com', 'Com')">
							<span style="color:#000" id="tudo-Com">Tudo</span>
						</a> / 
						<a href="#" onclick="datas('<?php echo $data_atual ?>', 'hoje-Com', 'Com')">
							<span id="hoje-Com">Hoje</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_mes ?>', 'mes-Com', 'Com')">
							<span style="color:#000" id="mes-Com">Mês</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_ano ?>', 'ano-Com', 'Com')">
							<span style="color:#000" id="ano-Com">Ano</span>
						</a> 
					)</small>



				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="rel/rel_comissoes_class.php" target="_blank">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-4">						
							<div class="form-group"> 
								<label>Data Inicial</label> 
								<input type="date" class="form-control" name="dataInicial" id="dataInicialRel-Com" value="<?php echo date('Y-m-d') ?>" required> 
							</div>						
						</div>
						<div class="col-md-4">
							<div class="form-group"> 
								<label>Data Final</label> 
								<input type="date" class="form-control" name="dataFinal" id="dataFinalRel-Com" value="<?php echo date('Y-m-d') ?>" required> 
							</div>
						</div>

						<div class="col-md-4">						
							<div class="form-group"> 
								<label>Pago</label> 
								<select class="form-control " name="pago" style="width:100%;">
									<option value="">Todas</option>
									<option value="Sim">Somente Pagas</option>
									<option value="Não">Pendentes</option>
									
								</select> 
							</div>						
						</div>

					</div>

					<div class="row">
						<div class="col-md-12">						
							<div class="form-group"> 
								<label>Funcionário</label> 
								<select class="form-control sel15" name="funcionario" style="width:100%;">
									<option value="">Todos</option>
									<?php 
									$query = $pdo->query("SELECT * FROM usuarios where atendimento = 'Sim' ORDER BY id desc");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if($total_reg > 0){
										for($i=0; $i < $total_reg; $i++){
											foreach ($res[$i] as $key => $value){}
												echo '<option value="'.$res[$i]['id'].'">'.$res[$i]['nome'].'</option>';
										}
									}?>
									
								</select> 
							</div>						
						</div>	
					</div>


					

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Gerar Relatório</button>
				</div>
			</form>

		</div>
	</div>
</div>


<!-- Modal Rel Contas -->
<div class="modal fade" id="RelCon" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Relatório de Contas
					<small>(
						<a href="#" onclick="datas('1980-01-01', 'tudo-Con', 'Con')">
							<span style="color:#000" id="tudo-Con">Tudo</span>
						</a> / 
						<a href="#" onclick="datas('<?php echo $data_atual ?>', 'hoje-Con', 'Con')">
							<span id="hoje-Con">Hoje</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_mes ?>', 'mes-Con', 'Con')">
							<span style="color:#000" id="mes-Con">Mês</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_ano ?>', 'ano-Con', 'Con')">
							<span style="color:#000" id="ano-Con">Ano</span>
						</a> 
					)</small>



				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="rel/rel_contas_class.php" target="_blank">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-4">						
							<div class="form-group"> 
								<label>Data Inicial</label> 
								<input type="date" class="form-control" name="dataInicial" id="dataInicialRel-Con" value="<?php echo date('Y-m-d') ?>" required> 
							</div>						
						</div>
						<div class="col-md-4">
							<div class="form-group"> 
								<label>Data Final</label> 
								<input type="date" class="form-control" name="dataFinal" id="dataFinalRel-Con" value="<?php echo date('Y-m-d') ?>" required> 
							</div>
						</div>

						<div class="col-md-4">						
							<div class="form-group"> 
								<label>Pago</label> 
								<select class="form-control" name="pago" style="width:100%;">
									<option value="">Todas</option>
									<option value="Sim">Somente Pagas</option>
									<option value="Não">Pendentes</option>
									
								</select> 
							</div>						
						</div>

					</div>



					<div class="row">
						<div class="col-md-6">						
							<div class="form-group"> 
								<label>Pagar / Receber</label> 
								<select class="form-control sel13" name="tabela" style="width:100%;">
									<option value="pagar">Contas à Pagar</option>
									<option value="receber">Contas à Receber</option>
									
								</select> 
							</div>						
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label>Consultar Por</label> 
								<select class="form-control sel13" name="busca" style="width:100%;">
									<option value="data_venc">Data de Vencimento</option>
									<option value="data_pgto">Data de Pagamento</option>
									
								</select>
							</div>
						</div>

						

					</div>


					

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Gerar Relatório</button>
				</div>
			</form>

		</div>
	</div>
</div>


<!-- Modal Rel Lucro -->
<div class="modal fade" id="RelLucro" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Demonstrativo de Lucro
					<small>(
						<a href="#" onclick="datas('1980-01-01', 'tudo-Lucro', 'Lucro')">
							<span style="color:#000" id="tudo-Lucro">Tudo</span>
						</a> / 
						<a href="#" onclick="datas('<?php echo $data_atual ?>', 'hoje-Lucro', 'Lucro')">
							<span id="hoje-Lucro">Hoje</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_mes ?>', 'mes-Lucro', 'Lucro')">
							<span style="color:#000" id="mes-Lucro">Mês</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_ano ?>', 'ano-Lucro', 'Lucro')">
							<span style="color:#000" id="ano-Lucro">Ano</span>
						</a> 
					)</small>



				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="rel/rel_lucro_class.php" target="_blank">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-4">						
							<div class="form-group"> 
								<label>Data Inicial</label> 
								<input type="date" class="form-control" name="dataInicial" id="dataInicialRel-Lucro" value="<?php echo date('Y-m-d') ?>" required> 
							</div>						
						</div>
						<div class="col-md-4">
							<div class="form-group"> 
								<label>Data Final</label> 
								<input type="date" class="form-control" name="dataFinal" id="dataFinalRel-Lucro" value="<?php echo date('Y-m-d') ?>" required> 
							</div>
						</div>						

					</div>


					

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Gerar Relatório</button>
				</div>
			</form>

		</div>
	</div>
</div>


<!-- Modal Rel Anivesariantes -->
<div class="modal fade" id="RelAniv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Relatório de Aniversáriantes
					<small>(
						<a href="#" onclick="datas('1980-01-01', 'tudo-Aniv', 'Aniv')">
							<span style="color:#000" id="tudo-Aniv">Tudo</span>
						</a> / 
						<a href="#" onclick="datas('<?php echo $data_atual ?>', 'hoje-Aniv', 'Aniv')">
							<span id="hoje-Aniv">Hoje</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_mes ?>', 'mes-Aniv', 'Aniv')">
							<span style="color:#000" id="mes-Aniv">Mês</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_ano ?>', 'ano-Aniv', 'Aniv')">
							<span style="color:#000" id="ano-Aniv">Ano</span>
						</a> 
					)</small>



				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="rel/rel_aniv_class.php" target="_blank">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-4">						
							<div class="form-group"> 
								<label>Data Inicial</label> 
								<input type="date" class="form-control" name="dataInicial" id="dataInicialRel-Aniv" value="<?php echo date('Y-m-d') ?>" required> 
							</div>						
						</div>
						<div class="col-md-4">
							<div class="form-group"> 
								<label>Data Final</label> 
								<input type="date" class="form-control" name="dataFinal" id="dataFinalRel-Aniv" value="<?php echo date('Y-m-d') ?>" required> 
							</div>
						</div>						

					</div>


					

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Gerar Relatório</button>
				</div>
			</form>

		</div>
	</div>
</div>



<!-- Modal Rel Entradas / Ganhos -->
<div class="modal fade" id="RelServicos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Relatório de Serviços
					<small>(
						<a href="#" onclick="datas('1980-01-01', 'tudo-Ser', 'Ser')">
							<span style="color:#000" id="tudo-Ser">Tudo</span>
						</a> / 
						<a href="#" onclick="datas('<?php echo $data_atual ?>', 'hoje-Ser', 'Ser')">
							<span id="hoje-Ser">Hoje</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_mes ?>', 'mes-Ser', 'Ser')">
							<span style="color:#000" id="mes-Ser">Mês</span>
						</a> /
						<a href="#" onclick="datas('<?php echo $data_ano ?>', 'ano-Ser', 'Ser')">
							<span style="color:#000" id="ano-Ser">Ano</span>
						</a> 
					)</small>



				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" action="rel/rel_servicos_class.php" target="_blank">
				<div class="modal-body">

					<div class="row">
						<div class="col-md-6">						
							<div class="form-group"> 
								<label>Data Inicial</label> 
								<input type="date" class="form-control" name="dataInicial" id="dataInicialRel-Ser" value="<?php echo date('Y-m-d') ?>" required> 
							</div>						
						</div>
						<div class="col-md-6">
							<div class="form-group"> 
								<label>Data Final</label> 
								<input type="date" class="form-control" name="dataFinal" id="dataFinalRel-Ser" value="<?php echo date('Y-m-d') ?>" required> 
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">						
							<div class="form-group"> 
								<label>Forma de Pagamento</label> 
								<select class="form-control" name="pgto" style="width:100%;" > 
									<option value="">Selecionar Pagamento</option>
									<?php 
									$query = $pdo->query("SELECT * FROM formas_pgto");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if($total_reg > 0){
										for($i=0; $i < $total_reg; $i++){
											foreach ($res[$i] as $key => $value){}
												echo '<option value="'.$res[$i]['nome'].'">'.$res[$i]['nome'].'</option>';
										}
									}
									?>


								</select>    
							</div>						
						</div>


						<div class="col-md-6">						
							<div class="form-group"> 
								<label>Selecionar Serviço</label> 
								<select class="form-control" name="servico" style="width:100%;" > 
									<option value="">Selecionar Serviço</option>
									<?php 
									$query = $pdo->query("SELECT * FROM servicos");
									$res = $query->fetchAll(PDO::FETCH_ASSOC);
									$total_reg = @count($res);
									if($total_reg > 0){
										for($i=0; $i < $total_reg; $i++){
											foreach ($res[$i] as $key => $value){}
												echo '<option value="'.$res[$i]['id'].'">'.$res[$i]['nome'].'</option>';
										}
									}
									?>


								</select>    
							</div>						
						</div>

					</div>


					

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Gerar Relatório</button>
				</div>
			</form>

		</div>
	</div>
</div>



<!-- Modal Verificar pgtos pendentes -->
<div class="modal fade" id="modalVerificar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="exampleModalLabel">Verificando Pagamentos</h4>
				<button id="btn-fechar-pgtos" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -25px">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			
			<div class="modal-body">	
				<div id="verificar_pagamentos">
					<div align="center" id="loading_img"><img src="images/loading.gif"></div>
					<div align="center" id="textos_verificar" style="display:none"></div>
				</div>

				
			</div>						
	
			
		</div>
	</div>
</div>


<!-- Modal Dúvida Frequente -->
<div class="modal" id="duvidas" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document"> 
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title">Configuração (Vídeo passo a passo)</h2>
        <button type="button" class="close" style="font-size: 1.5rem; margin-top: -30px;" data-dismiss="modal" aria-label="Fechar"> 
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body"><div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
    <h4 class="font-weight-bold">01 - Configuração Home</h4>
<p class="text-muted">Aprenda a configurar sua página inicial do BarberBot de forma simples e rápida. Este tutorial irá guiá-lo em cada etapa do processo. Confira o vídeo para um passo a passo visual!
<a href="https://drive.google.com/file/d/1CUvOhkdW6vwv6_n2pcRJSnNkP4iOH6hS/view?usp=drive_link" class="btn btn-outline-primary" target="_blank">Assistir ao vídeo</a></p></p>
</div>

        <div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
            <h4 class="font-weight-bold">02 - Categoria Cargo</h4>
<p class="text-muted">Entenda como gerir as categorias de cargos dentro do BarberBot. Esta seção é fundamental para organizar sua equipe e atribuir funções adequadas. Confira o vídeo para um tutorial completo e aprenda a otimizar sua gestão!
<a href="https://drive.google.com/file/d/1U-pv6BcN3prheXw3pueeqwtrcUaM4NPT/view?usp=drive_link" class="btn btn-outline-primary" target="_blank">Assistir ao vídeo</a></p>
        </div>

        <div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
            <h4 class="font-weight-bold">03 - Cadastrar Usuário</h4>
<p class="text-muted">Aprenda a cadastrar novos usuários no BarberBot de forma rápida e eficiente. Nesta seção, você descobrirá como gerenciar as informações dos usuários e garantir que sua equipe esteja pronta para oferecer o melhor atendimento. Não perca o vídeo para um guia passo a passo!
<a href="https://drive.google.com/file/d/1xj58MaR4DSJTYpNcw8ZATIZPlE4eaxp5/view?usp=drive_link" class="btn btn-outline-primary" target="_blank">Assistir ao vídeo</a></p>
        </div>

        <div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
            <h4 class="font-weight-bold">04 - Cadastro de Serviço</h4>
<p class="text-muted">Descubra como cadastrar novos serviços no BarberBot de maneira prática e eficiente. Nesta seção, você aprenderá a definir detalhes importantes sobre cada serviço oferecido, otimizando seu fluxo de trabalho. Confira o vídeo para um tutorial passo a passo e maximize seu atendimento!
<a href="https://drive.google.com/file/d/1DbpWs6lqkdditNg3mQ36tisU9fJNJ3_4/view?usp=drive_link" class="btn btn-outline-primary" target="_blank">Assistir ao Vídeo</a></p>
        </div>

        <div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
            <h4 class="font-weight-bold">05 - Produto: Entrada e Saída</h4>
<p class="text-muted">Aprenda a gerenciar a entrada e saída de produtos no BarberBot de forma eficiente. Nesta seção, você descobrirá como manter seu estoque atualizado e acompanhar o desempenho dos produtos oferecidos em seu estabelecimento. Confira o vídeo para um guia prático e maximize sua gestão de inventário!
<a href="https://drive.google.com/file/d/1i60pXhZ0m0TldLTt4py0J_-nyl8_Kcqo/view?usp=drive_link" class="btn btn-outline-primary" target="_blank">Assistir ao Vídeo</a></p>
</div>

        <div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
            <h4 class="font-weight-bold">06 - Cadastro de Venda</h4>
<p class="text-muted">Descubra como cadastrar vendas no BarberBot de forma simples e eficaz. Nesta seção, você aprenderá a registrar transações, monitorar o desempenho das vendas e gerar relatórios para uma melhor gestão financeira. Não perca o vídeo que oferece um guia passo a passo para aproveitar ao máximo essa funcionalidade!
<a href="https://drive.google.com/file/d/1wd3szq-cbkCoGYt-cxUL0uMxmxKoWgj6/view?usp=drive_link" class="btn btn-outline-primary" target="_blank">Assistir ao Vídeo</a></p>
</div>

        <div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
        <h4 class="font-weight-bold">07 - Horário de Almoço</h4>
<p class="text-muted">Aprenda a gerenciar o horário de almoço de sua equipe no BarberBot com eficiência. Nesta seção, você descobrirá como programar pausas e garantir que os serviços continuem a fluir suavemente, sem comprometer o atendimento ao cliente. Confira o vídeo para um tutorial prático e melhore a gestão do seu tempo!
<a href="https://drive.google.com/file/d/1nyHx9fvW5tPjp_-QXEoTnlcfj6dPj2Gv/view?usp=drive_linko" class="btn btn-outline-primary" target="_blank">Assistir ao Vídeo</a></p>
</div>

        <div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
        <h4 class="font-weight-bold">08 - Criar Agenda Profissional</h4>
<p class="text-muted">Descubra como criar e gerenciar a agenda profissional no BarberBot para otimizar o atendimento da sua equipe. Nesta seção, você aprenderá a configurar horários, agendar compromissos e garantir que seus profissionais estejam sempre disponíveis para atender seus clientes. Confira o vídeo para um guia prático e maximize a eficiência da sua barbearia!
<a href="https://drive.google.com/file/d/1KMvgUuG5PXUNalmQSSiT_-s2FUly93VR/view?usp=drive_link" class="btn btn-outline-primary" target="_blank">Assistir ao Vídeo</a></p>
</div>

       <div class="linha" style="border-bottom: 1px solid #ddd; padding: 10px 0;">
    <h4 class="font-weight-bold">09 - Relatório</h4>
    <p class="text-muted">
        Aprenda a gerar e analisar relatórios detalhados sobre as atividades do BarberBot, diretamente na sua página inicial. Nesta seção, você descobrirá como interpretar os dados, identificar tendências e otimizar o desempenho do seu negócio. 
        Não perca o vídeo que oferece um passo a passo fácil para aproveitar ao máximo essa funcionalidade!
    
    <a href="https://drive.google.com/file/d/111dL79PT74T4g5Qh_lp2m4PMWJUjHSUr/view?usp=drive_link" class="btn btn-outline-primary">Assistir ao Vídeo</a></p><h4 class="font-weight-bold">10 - Anotações</h4>
<p class="text-muted">
    Descubra como fazer e gerenciar anotações detalhadas no BarberBot para cada um de seus clientes. Nesta seção, você aprenderá a registrar informações importantes, acompanhar preferências e garantir um atendimento mais personalizado e eficiente.
    Não perca o vídeo que oferece um passo a passo fácil para aproveitar ao máximo essa funcionalidade!
    
    <a href="https://drive.google.com/file/d/1R9FxCyaUVhWZ67lSdsA0-Rr_CLxiTMK1/view?usp=drive_link" class="btn btn-outline-primary">Assistir ao Vídeo</a>
</p>
</div>

<div class="linha" style="padding: 10px 0;">
    <p class="text-muted">
        Obrigado por confiar no BarberBot! Sua escolha é extremamente valiosa para nós. Estamos comprometidos em oferecer a melhor experiência e suporte para a gestão da sua barbearia. 
        Se tiver alguma dúvida ou sugestão, não hesite em entrar em contato!
    </p>
    <p class="text-muted">
        Esperamos que você aproveite todas as funcionalidades que o BarberBot oferece para otimizar a gestão do seu negócio. Sua satisfação é a nossa prioridade!
    </p>     
       

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
	$(document).ready(function() {		
		$('.sel15').select2({	
			dropdownParent: $('#RelComissoes')		
		});

		$('.selcli').select2({	
			dropdownParent: $('#RelEntradas')		
		});
	});
</script>


 <script type="text/javascript">
	$("#form-perfil").submit(function () {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: "editar-perfil.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {
				$('#mensagem-perfil').text('');
				$('#mensagem-perfil').removeClass()
				if (mensagem.trim() == "Editado com Sucesso") {

					$('#btn-fechar-perfil').click();
					location.reload();			
					
				} else {

					$('#mensagem-perfil').addClass('text-danger')
					$('#mensagem-perfil').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>


<script type="text/javascript">
	function carregarImgPerfil() {
    var target = document.getElementById('target-usu');
    var file = document.querySelector("#foto-usu").files[0];
    
        var reader = new FileReader();

        reader.onloadend = function () {
            target.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            target.src = "";
        }
    }
</script>


 <script type="text/javascript">
	$("#form-config").submit(function () {

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: "editar-config.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {
				$('#mensagem-config').text('');
				$('#mensagem-config').removeClass()
				if (mensagem.trim() == "Editado com Sucesso") {

					$('#btn-fechar-config').click();
					location.reload();			
					
				} else {

					$('#mensagem-config').addClass('text-danger')
					$('#mensagem-config').text(mensagem)
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>


<script type="text/javascript">
	function carregarImgLogo() {
    var target = document.getElementById('target-logo');
    var file = document.querySelector("#foto-logo").files[0];
    
        var reader = new FileReader();

        reader.onloadend = function () {
            target.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            target.src = "";
        }
    }
</script>


<script type="text/javascript">
	function carregarImgLogoRel() {
    var target = document.getElementById('target-logo-rel');
    var file = document.querySelector("#foto-logo-rel").files[0];
    
        var reader = new FileReader();

        reader.onloadend = function () {
            target.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            target.src = "";
        }
    }
</script>


<script type="text/javascript">
	function carregarImgIcone() {
    var target = document.getElementById('target-icone');
    var file = document.querySelector("#foto-icone").files[0];
    
        var reader = new FileReader();

        reader.onloadend = function () {
            target.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            target.src = "";
        }
    }
</script>


<script type="text/javascript">
	function carregarImgIconeSite() {
    var target = document.getElementById('target-icone-site');
    var file = document.querySelector("#foto-icone-site").files[0];
    
        var reader = new FileReader();

        reader.onloadend = function () {
            target.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            target.src = "";
        }
    }
</script>


<script type="text/javascript">
	function carregarImgBannerIndex() {
    var target = document.getElementById('target-banner-index');
    var file = document.querySelector("#foto-banner-index").files[0];
    
        var reader = new FileReader();

        reader.onloadend = function () {
            target.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            target.src = "";
        }
    }
</script>


<script type="text/javascript">
	function carregarImgSobre() {
    var target = document.getElementById('target-sobre');
    var file = document.querySelector("#foto-sobre").files[0];
    
        var reader = new FileReader();

        reader.onloadend = function () {
            target.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);

        } else {
            target.src = "";
        }
    }
</script>


<script type="text/javascript">
		function datas(data, id, campo){		

			var data_atual = "<?=$data_atual?>";
			var separarData = data_atual.split("-");
			var mes = separarData[1];
			var ano = separarData[0];

			var separarId = id.split("-");

			if(separarId[0] == 'tudo'){
				data_atual = '2100-12-31';
			}

			if(separarId[0] == 'ano'){
				data_atual = ano + '-12-31';
			}

			if(separarId[0] == 'mes'){
				if(mes == 1 || mes == 3 || mes == 5 || mes == 7 || mes == 8 || mes == 10 || mes == 12){
					data_atual = ano + '-'+ mes + '-31';
				}else if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
					data_atual = ano + '-'+ mes + '-30';
				}else{
					data_atual = ano + '-'+ mes + '-28';
				}

			}

			$('#dataInicialRel-'+campo).val(data);
			$('#dataFinalRel-'+campo).val(data_atual);

			document.getElementById('hoje-'+campo).style.color = "#000";
			document.getElementById('mes-'+campo).style.color = "#000";
			document.getElementById(id).style.color = "blue";	
			document.getElementById('tudo-'+campo).style.color = "#000";
			document.getElementById('ano-'+campo).style.color = "#000";
			document.getElementById(id).style.color = "blue";		
		}
</script>



<script type="text/javascript">
	function verificarPg(){
		$('#modalVerificar').modal('show');
		$('#loading_img').show();
		$('#textos_verificar').hide();

		$.ajax({
        url: 'verificar_pgtos.php',
        method: 'POST',
        data: {},
        dataType: "html",

        success:function(mensagem){
            $('#loading_img').hide();
            $('#textos_verificar').html(mensagem);
            $('#textos_verificar').show();
        }

	});

	}
</script>

<!-- Ajax para carregamento dinâmico -->
<script type="text/javascript">
  function carregarPag(pagina) {
    $.ajax({
      url: 'paginas/' + pagina + '.php',
      method: 'GET',
      success: function(dados) {
        $('#container').html(dados);
        history.pushState(null, null, pagina);
      },
      error: function() {
        $('#container').html('<div class="alert alert-danger">Erro ao carregar a página.</div>');
      }
    });
  }
</script>

