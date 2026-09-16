<?php
@session_start();

if (!headers_sent()) {
  header('Content-Type: text/html; charset=UTF-8');
}
ini_set('default_charset', 'UTF-8');

require_once("conexao.php");

//INSERIR UM USU09RIO ADMINISTRADOR CASO N01O EXISTA
$demoAdminName = getenv('DEMO_ADMIN_NAME') ?: 'Administrador Demo';
$demoAdminEmail = getenv('DEMO_ADMIN_EMAIL') ?: 'admin@example.test';
$demoAdminPassword = getenv('DEMO_ADMIN_PASSWORD') ?: 'demo123';
$senha_crip = password_hash($demoAdminPassword, PASSWORD_DEFAULT);

$query = $pdo->query("SELECT * from usuarios where nivel = 'Administrador'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg == 0){
	$stmtAdmin = $pdo->prepare("INSERT INTO usuarios SET nome = :nome, email = :email, senha = '', senha_crip = :senha_crip, nivel = 'Administrador', data = curDate(), ativo = 'Sim', foto = 'sem-foto.jpg', atendimento = 'Sim', visualizar = 'Sim'");
	$stmtAdmin->execute([
		':nome' => $demoAdminName,
		':email' => $demoAdminEmail,
		':senha_crip' => $senha_crip,
	]);
}

$query = $pdo->query("SELECT * from cargos");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg == 0){
	$pdo->query("INSERT INTO cargos SET nome = 'Administrador'");
}

//APAGAR AGENDAMENTOS ANTERIORES
$data_atual = date('Y-m-d');
$data_anterior = date('Y-m-d', strtotime("-$agendamento_dias days",strtotime($data_atual)));
$query = $pdo->query("SELECT * FROM agendamentos WHERE data < '$data_anterior'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
 for($i=0; $i < $total_reg; $i++){
    foreach ($res[$i] as $key => $value){}
        $id = $res[$i]['id'];
    	$pdo->query("DELETE FROM agendamentos WHERE id = '$id'");
 }
}

$bodyStyle = '';
if($fundo_login != "" and $fundo_login != "sem-foto.png"){
  $bodyStyle = "background: url('img/{$fundo_login}') no-repeat center center fixed; background-size: cover;";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $nome_sistema ?></title>

	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link rel="stylesheet" type="text/css" href="css/estilo-login.css">
	<link rel="icon" type="image/png" href="img/favicon.ico">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.min.css">

  <style>
    /* Link IP - discreto e profissional */
    .ip-helper{
      margin-top: 12px;
      padding-top: 10px;
      border-top: 1px solid rgba(0,0,0,.08);
      text-align: center;
    }
    .ip-helper .ip-pill{
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 12px;
      border-radius: 999px;
      border: 1px solid rgba(0,0,0,.15);
      background: rgba(255,255,255,.65);
      color: #333;
      font-weight: 700;
      font-size: 12px;
      line-height: 1;
      text-decoration: none;
      transition: .15s;
    }
    .ip-helper .ip-pill:hover{
      background: #fff;
      text-decoration: none;
      border-color: rgba(0,0,0,.22);
    }
    .ip-helper small{
      display:block;
      margin-top: 6px;
      color:#666;
      font-size: 11px;
      line-height: 1.35;
    }
  </style>
</head>

<body style="<?php echo $bodyStyle; ?>">

<div class="container ">
  <div class="row vertical-offset-100">
    <div class="col-md-4 col-md-offset-4">

      <div class="panel panel-default form-login" style="opacity:0.92; border-radius: 20px">
        <div class="panel-heading" align="center" style="border-top-right-radius: 20px; border-top-left-radius: 20px">
          <img src="img/logo.png" width="100px">
        </div>

        <p class="recuperar">Acesso ao Painel do Profissional</p>

        <div class="panel-body">
          <form accept-charset="UTF-8" role="form" action="autenticar.php" method="post">
            <fieldset>
              <div class="form-group">
                <input class="form-control" placeholder="E-mail ou CPF" name="email" type="text" value="">
              </div>

              <div class="form-group">
                <input class="form-control" placeholder="Senha" name="senha" type="password" value="">
              </div>

              <!-- reCAPTCHA -->
              <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(getenv('RECAPTCHA_SITE_KEY') ?: '', ENT_QUOTES, 'UTF-8'); ?>"></div>
              <br>

              <input class="btn btn-lg btn-primary btn-block" type="submit" value="Login">
            </fieldset>

            <p class="recuperar">
              <a title="Clique para recuperar a senha" href="" data-toggle="modal" data-target="#exampleModal">Recuperar Senha</a>
            </p>

            <p class="recuperar">
              <a title="Clique e tenha controle total dos seus agendamentos" href="acesso" target="_blank">[Acessar Painel do Cliente]</a>
            </p>

            <!-- IP (bem embaixo e discreto) -->
            <div class="ip-helper">
              <a class="ip-pill" href="/meu-ip.php" target="_blank" rel="noopener">
                &#128274; Ver meu IP (libera&ccedil;&atilde;o de acesso)
              </a>
              <small>Abra, copie o IP e envie para o suporte liberar o acesso.</small>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Modal Recuperar Senha -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content" style="width:400px">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Recuperar Senha</h5>
        <button id="btn-fechar-rec" type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form method="post" id="form-recuperar">
        <div class="modal-body">
          <input placeholder="Digite seu email" class="form-control" type="email" name="email" id="email-recuperar" required>
          <br>
          <small><div id="mensagem-recuperar" align="center"></div></small>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Recuperar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- SCRIPTS -->
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function(){
  $('#form-recuperar').submit(function(event){
    event.preventDefault();
    var formData = new FormData(this);

    $.ajax({
      url: "recuperar-senha.php",
      type: 'POST',
      data: formData,
      cache: false,
      contentType: false,
      processData: false,

      success: function(mensagem){
        let msg = (mensagem || '').trim().toLowerCase();

        if(
          msg === "recuperado com sucesso" ||
          msg.includes('senha foi enviada para o whatsapp') ||
          msg.includes('senha foi enviada para o whatsapp ou email')
        ){
          $('#email-recuperar').val('');

          Swal.fire({
            icon: 'success',
            title: 'Recuperado com Sucesso!',
            text: 'Sua senha foi enviada para o WhatsApp ou E-mail.',
            confirmButtonColor: '#3085d6',
            timer: 2000,
            showConfirmButton: false,
            willClose: () => {
              try { $('#exampleModal').modal('hide'); } catch(e){}
              $('.modal-backdrop').remove();
              $('body').removeClass('modal-open');
              $('[name=email]').focus();
            }
          });

        } else {
          Swal.fire({
            icon: 'error',
            title: 'Erro!',
            text: mensagem,
            confirmButtonColor: '#e32424'
          });
        }
      },

      error: function(){
        Swal.fire({
          icon: 'error',
          title: 'Erro!',
          text: 'Erro de conex\u00e3o ou servidor!',
          confirmButtonColor: '#e32424'
        });
      }
    });
  });
});
</script>

<script type="text/javascript" src="painel/js/mascaras.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
  document.addEventListener('contextmenu', function(event) {
    event.preventDefault();
  });

  document.addEventListener('keydown', function(event) {
    if (event.ctrlKey && event.key === 'u') {
      event.preventDefault();
      alert("A visualiza\u00e7\u00e3o do c\u00f3digo-fonte est\u00e1 desabilitada!");
    }
    if (event.key === 'F12') {
      event.preventDefault();
      alert("A visualiza\u00e7\u00e3o do c\u00f3digo-fonte est\u00e1 desabilitada!");
    }
  });
</script>

</body>
</html>
