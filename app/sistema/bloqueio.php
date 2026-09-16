<?php
// bloqueio.php
// NÃƒO inclua conexao.php ou header.php aqui para nÃ£o bugar o layout
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="keywords" content="BarberBot, sistema para barbearia, gestão de barbearia, agendamento online, clientes, financeiro, relatórios, painel administrativo, automação WhatsApp">
  <meta name="description" content="BarberBot: sistema completo para barbearias com agendamento, gestão de clientes, financeiro e automação no WhatsApp. Simples, rápido e profissional.">
  <meta name="author" content="BarberBot">
  <meta name="theme-color" content="#000000">

  <link rel="shortcut icon" href="/sistema/img/icone.png" type="image/x-icon">
  <title><?php echo $nome_sistema ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lock-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
            border-top: 5px solid #dc2626; /* Tarja vermelha no topo */
        }

        .icon-area {
            margin-bottom: 20px;
        }

        /* Se usar sua imagem do print, a classe abaixo ajusta ela */
        .img-bloqueio {
            width: 150px;
            height: auto;
        }

        h1 {
            color: #1f2937;
            font-size: 22px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        p {
            color: #6b7280;
            line-height: 1.5;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .btn-contato {
            display: block;
            width: 100%;
            background-color: #2563eb; /* Azul profissional */
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            transition: background 0.3s;
        }

        .btn-contato:hover {
            background-color: #1d4ed8;
        }

        .phone-icon { margin-right: 8px; }
    </style>
</head>
<body>

    <div class="lock-container">
        <div class="icon-area">
            <img src="/sistema/img/logo.webp" alt="Bloqueado" class="img-bloqueio">
        </div>

        <h1>Acesso Suspenso</h1>
        
        <p>
            Sua assinatura expirou. O acesso Ã s funcionalidades do sistema foi temporariamente interrompido.<br>
            Para regularizar, entre em contato com o suporte.
        </p>

        <a href="https://wa.me/5545999580058" class="btn-contato" target="_blank">
            ðŸ“ž Falar com Administrador
        </a>
    </div>

</body>
</html>