<?php
require_once("../conexao.php");

if (!isset($_REQUEST['email']) || !isset($_REQUEST['token'])) {
	header('location: ' . $url_sistema);
	exit;
}

$statement = $pdo->prepare("SELECT * FROM usuarios WHERE email=? AND token=?");
$statement->execute([@$_REQUEST['email'], @$_REQUEST['token']]);
$result = $statement->fetchAll();
$tot = $statement->rowCount();
if ($tot == 0) {
	header('location: ' . $url_sistema);
	exit;
}

$_SESSION['temp_reset_email'] = @$_REQUEST['email'];
$_SESSION['temp_reset_token'] = @$_REQUEST['token'];



?>
<!DOCTYPE HTML>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="viewport"
		content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
	<title><?php echo $nome_sistema ?></title>
	<link rel="stylesheet" type="text/css" href="painel/styles/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="painel/fonts/bootstrap-icons.css">
	<link rel="stylesheet" type="text/css" href="painel/styles/style.css">
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link
		href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Roboto:wght@400;500;700&display=swap"
		rel="stylesheet">
	<link rel="manifest" href="_manifest.json">
	<meta id="theme-check" name="theme-color" content="#FFFFFF">
	<link rel="apple-touch-icon" sizes="180x180" href="painel/app/icons/icon-192x192.png">
	<link rel="icon" type="image/png" href="../img/icone.png" sizes="32x32">
</head>

<body class="theme-light">

	<div id="preloader">
		<div class="spinner-border color-highlight" role="status"></div>
	</div>

	<div id="page">


		<!-- MODAL RESETAR SENHA-->
		<div class="page-content pb-0 mb-0">

			<div class="card card-style m-0 bg-transparent shadow-0 bg-10 rounded-0" data-card-height="cover">
				<div class="card-center">
					<div class="card card-style">
						<div class="content">
							<h2 class="text-center font-800 font-30 mb-2"><br><img src="../img/logo.png" width="70%"></h2><br>


							<form method="post" id="form-recuperar">
								<div class="form-custom form-label form-icon mb-3">
									<i class="bi bi-lock font-12"></i>
									<input type="password" name="senha" value="" id="senha" class="form-control rounded-xs" id="c2"
										placeholder="Digite uma Nova Senha" required />
									<label class="color-theme">Nova Senha</label>
									<span>(Obrigatório)</span>
								</div>

								<div class="form-custom form-label form-icon mb-3">
									<i class="bi bi-lock font-12"></i>
									<input type="password" name="re_senha" value="" id="re_senha" class="form-control rounded-xs" id="c2"
										placeholder="Repita Sua Nova Senha" required />
									<label class="color-theme">Repitir Senha</label>
									<span>(Obrigatório)</span>
								</div>


								<input type="hidden" name="token" id="token" value="">

								<input type="hidden" name="email" id="email" value="<?php echo @$_REQUEST['email'] ?>">



								<button class="btn btn-full gradient-blue rounded-xs text-uppercase font-700 w-100 btn-s mt-4 mb-2"
									type="submit">ALTERAR
									SENHA</button><br>

							</form>

						</div>
					</div>
				</div>
			</div>

		</div>
		<!-- End of Page Content-->


	</div>
	<!--End of Page ID-->




	<script src="painel/scripts/bootstrap.min.js"></script>
	<script src="painel/scripts/custom.js"></script>
</body>


<?php require_once("alertas.php"); ?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


 <script type="text/javascript">
	$("#form-recuperar").submit(function () {

		$('#mensagem-recuperar').text("Alterando!!")

		event.preventDefault();
		var formData = new FormData(this);

		$.ajax({
			url: "alterar-senha.php",
			type: 'POST',
			data: formData,

			success: function (mensagem) {
				//alert(mensagem)
				$('#mensagem-recuperar').text('');
				$('#mensagem-recuperar').removeClass()
				if (mensagem.trim() == "Senha alterada com Sucesso") {
					//$('#btn-fechar-rec').click();					
					$('#senha').val('');
					$('#re_senha').val('');
					$('#btn_sucess_rec_senha').click();
					window.location="index.php";		

				} else {

					$('#mensagem-recuperar').addClass('text-danger')
					$('#btn_senha_incorreta').click();
					
				}


			},

			cache: false,
			contentType: false,
			processData: false,

		});

	});
</script>




<script src="painel/js/jquery-3.3.1.min.js"></script>
<script src="painel/js/jquery.validate.min.js" ></script>
<script src="painel/js/swiper.min.js"></script>
<script src="painel/js/jquery.custom.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>




<script type="text/javascript">
    function toast(mensagem, cor){        
        Toastify({
          text: mensagem,
          duration: 3000,
          destination: "https://github.com/apvarun/toastify-js",
          newWindow: true,
          close: true,
          gravity: "top", // `top` or `bottom`
          position: "center", // `left`, `center` or `right`
          stopOnFocus: true, // Prevents dismissing of toast on hover
          style: {
            background: cor, //verde #24c76a    vermelha #d4483b
          },
          onClick: function(){} // Callback after click
        }).showToast();
    }
</script>