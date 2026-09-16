<?php  
@session_start();
require_once("conexao.php");
unset($_SESSION['usuario_logado_pagina']);
$_SESSION['usuario_logado_pagina'] = true;
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $nome_sistema ?></title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link rel="stylesheet" type="text/css" href="css/estilo-login.css">
    <link rel="icon" type="image/png" href="img/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css" crossorigin="anonymous">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.6/dist/sweetalert2.min.css">
</head>
<body>
<div class="container ">
    <div class="row vertical-offset-100">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default form-login" style="opacity:0.9; border-radius: 20px">
                <div class="panel-heading" align="center" style="border-top-right-radius: 20px; border-top-left-radius: 20px">
                    <img src="img/logo.png" width="100px">
                </div>
                <p class="recuperar">👥 Acesso Exclusivo ao Painel do Cliente!</p>
                <div class="panel-body">
                    <form accept-charset="UTF-8" role="form" action="autenticar_cliente.php" method="post">
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" placeholder="Seu Telefone" name="telefone" id="telefone" type="text" value="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" placeholder="Senha" name="senha" type="password" value="">
                            </div>

    <!-- reCAPTCHA Aqui -->
    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars(getenv('RECAPTCHA_SITE_KEY') ?: '', ENT_QUOTES, 'UTF-8'); ?>"></div>
    <br>
                            <input class="btn btn-lg btn-primary btn-block" type="submit" value="Login">
                        </fieldset>
                        <p class="recuperar">
                            <a title="Clique para recuperar a senha" href="" data-toggle="modal" data-target="#exampleModal">Recuperar Senha</a>
                        </p>
                        <p class="recuperar">
                            <a title="Gerencie suas tarefas, clientes e muito mais diretamente pelo painel." href="../sistema" target="_blank">🔗 [Acessar Painel do Profissional]</a>
                        </p>
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
          <input placeholder="Digite seu Telefone" class="form-control" type="text" name="telefone" id="telefone2" required>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
$(document).ready(function(){
    // Mascara telefone (brasileiro padrão)
    $('#telefone, #telefone2').mask('(00) 00000-0000');

    // AJAX Recuperar Senha + SweetAlert2
    $('#form-recuperar').submit(function(event){
        event.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: "recuperar-senha_cliente.php",
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(mensagem){
                let msg = mensagem.trim().toLowerCase();
                if(
                    msg === "recuperado com sucesso" ||
                    msg.includes('senha foi enviada para o whatsapp') ||
                    msg.includes('senha foi enviada para o whatsapp ou email')
                ){
                    $('#telefone2').val('');
                    Swal.fire({
                        icon: 'success',
                        title: 'Recuperado com Sucesso!',
                        text: 'Sua senha foi enviada para o WhatsApp ou E-mail.',
                        confirmButtonColor: '#3085d6',
                        timer: 2000,
                        showConfirmButton: false,
                        willClose: () => {
                            try { $('#exampleModal').modal('hide'); } catch(e){}
                            try {
                                var modal = document.getElementById('exampleModal');
                                if(modal && typeof bootstrap !== 'undefined'){
                                    var bsModal = bootstrap.Modal.getInstance(modal);
                                    if(bsModal){ bsModal.hide(); }
                                }
                            }catch(e){}
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('#telefone').focus();
                        }
                    });
                }else{
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
                    text: 'Erro de conexão ou servidor!',
                    confirmButtonColor: '#e32424'
                });
            }
        });
    });
});
</script>
<!-- Mascaras JS extra (caso tenha no seu projeto) -->
<script type="text/javascript" src="painel/js/mascaras.js"></script>
</body>
</html>
