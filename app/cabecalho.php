<?php 
require_once("sistema/conexao.php"); 
require_once __DIR__ . '/security.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta name="google-site-verification" content="KMD-KduvUzg4TF8e0Z4Oy9Cwgl25tHKi7d-ISODvCuU" />
    
    <!-- Google tag ads (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-10875176701"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-10875176701');
</script>

  <!-- Google Tag Manager (GTM) -->
  <script>
    (function(w,d,s,l,i){
      w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});
      var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
      j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
      f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-5BM8JXC');
  </script>
  <!-- End GTM -->

  <!-- Google tag (GA4) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-21KPNB78L9"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){ dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'G-21KPNB78L9');
  </script>

  <!-- Metas básicas -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Sistema completo para barbearias: agenda online, confirmações por WhatsApp, pagamentos, relatórios e fidelidade. Organize a equipe e aumente o faturamento.">
<meta name="keywords" content="BarberBot, sistema para barbearia, agendamento barbearia, gestão de barbearia, agenda online, software barbearia, confirmações WhatsApp, pagamentos, relatórios, fidelidade, agenda de barbeiro">
<meta name="robots" content="index,follow">
<link rel="canonical" href="https://barberbot.com.br/">
<meta name="author" content="Jacy Cordeiro">
  <meta name="theme-color" content="#000000">

  <?php
    // canonical (seguro)
    $host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '');
    $req  = $_SERVER['REQUEST_URI'] ?? '/';
    $canonical = $host . rtrim($req, '/');
  ?>
  <link rel="canonical" href="<?php echo htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8'); ?>">

  <link rel="shortcut icon" href="images/<?php echo htmlspecialchars($icone_site ?? '', ENT_QUOTES, 'UTF-8'); ?>" type="image/x-icon">
  <title><?php echo htmlspecialchars($nome_sistema ?? ' Agendamento e Gestão para Barbearias', ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- Conexões adiantadas -->
  <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="dns-prefetch" href="//fonts.googleapis.com">
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>

  <!-- Google Fonts -->
  <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- CSS principal -->
  <link rel="preload" as="style" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="preload" as="style" href="css/style.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="preload" as="style" href="css/responsive.css">
  <link rel="stylesheet" href="css/responsive.css">

  <!-- CSS adicionais -->
  <link rel="stylesheet" href="css/pricing-table.css">
  <link rel="stylesheet" href="css/pricing.css">
  <link rel="preload" as="style" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">

  <!-- Owl Carousel -->
  <link rel="preload" as="style" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

  <!-- Fallback sem JS -->
  <noscript>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/pricing-table.css">
    <link rel="stylesheet" href="css/pricing.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="css/responsive.css">
  </noscript>

  <!-- CSS rápido para evitar CLS -->
  <style>
  
    .hero_area{ position:relative; overflow:hidden; }
    .hero_bg_box img{ display:block; width:100%; height:auto; }
    html,body{ max-width:100%; overflow-x:hidden; }
    .header_section{
      position:fixed; top:0; left:0; right:0; z-index:10000;
      background:rgba(0,0,0,.96);
      backdrop-filter:saturate(120%) blur(2px);
      -webkit-backdrop-filter:saturate(120%) blur(2px);
      transition:box-shadow .2s ease;
    }
    .header_section.header--scrolled{ box-shadow:0 8px 22px rgba(0,0,0,.25); }
    .header_section .dropdown-menu{ z-index:10001; }
    body.has-sticky{ padding-top:var(--header-h, 88px) !important; }
    
    header, .header, nav, .navbar { z-index: 1000 !important; }
.modal-backdrop { z-index: 10040 !important; }
.modal { z-index: 10050 !important; }
.modal-dialog { z-index: 10060 !important; }
body.modal-open { overflow: hidden !important; }
  </style>

  <!-- Google Ads (AW) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10875176701"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){ dataLayer.push(arguments); }
    gtag('js', new Date());
    gtag('config', 'AW-10875176701');
  </script>

  <?php
    // ============== reCAPTCHA v3 (centralizado) ==============
    // carrega chaves e função recaptcha_verify para uso nos endpoints
    @require_once __DIR__ . '/config/recaptcha.php';
    if (defined('RECAPTCHA_SITE_KEY')): ?>
      <script src="https://www.google.com/recaptcha/api.js?render=<?php echo RECAPTCHA_SITE_KEY; ?>" defer></script>
      <script>
        // Injeta token em todo form que tiver data-recaptcha-action
        document.addEventListener('DOMContentLoaded', function(){
          function ensure(form, action){
            let t=form.querySelector('input[name="recaptcha_token"]');
            let a=form.querySelector('input[name="recaptcha_action"]');
            if(!t){ t=document.createElement('input'); t.type='hidden'; t.name='recaptcha_token'; form.appendChild(t); }
            if(!a){ a=document.createElement('input'); a.type='hidden'; a.name='recaptcha_action'; form.appendChild(a); }
            a.value = action || 'generic';
            return t;
          }
          function update(form){
            const action = form.getAttribute('data-recaptcha-action') || 'generic';
            const t = ensure(form, action);
            if(!window.grecaptcha || !grecaptcha.execute) return;
            grecaptcha.ready(function(){
              grecaptcha.execute('<?php echo RECAPTCHA_SITE_KEY; ?>', {action})
                .then(function(token){ t.value = token; })
                .catch(function(){ t.value=''; });
            });
          }
          const forms = document.querySelectorAll('form[data-recaptcha-action]');
          forms.forEach(function(f){ update(f); f.addEventListener('submit', function(){ if(!f.querySelector('input[name="recaptcha_token"]')?.value) update(f); }); });
          setInterval(function(){ forms.forEach(update); }, 110000); // renova token
        });
      </script>
  <?php endif; ?>
  <!-- ============ fim reCAPTCHA v3 ============ -->
</head>

<body class="sub_page">
  <!-- GTM noscript -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5BM8JXC" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

  <div class="hero_area">
    <div class="hero_bg_box">
      <?php
        $img_banner_index = $img_banner_index ?? 'banner.jpg';
        $orig = 'images/' . ltrim($img_banner_index, '/');
        $pi   = pathinfo($orig);
        $webp = ($pi['dirname'] !== '.' ? $pi['dirname'].'/' : '') . ($pi['filename'] ?? 'banner') . '.webp';

        $fsOrig = __DIR__ . '/' . ltrim($orig, '/');
        $fsWebp = __DIR__ . '/' . ltrim($webp, '/');

        $srcBanner = is_file($fsWebp) ? $webp : $orig;

        $w=1280; $h=720;
        $probe = is_file($fsWebp) ? $fsWebp : (is_file($fsOrig) ? $fsOrig : null);
        if ($probe) {
          $dim = @getimagesize($probe);
          if (is_array($dim) && !empty($dim[0]) && !empty($dim[1])) { $w=(int)$dim[0]; $h=(int)$dim[1]; }
        }
      ?>
      <img
        src="<?php echo htmlspecialchars($srcBanner, ENT_QUOTES, 'UTF-8'); ?>"
        alt="Banner — <?php echo htmlspecialchars($nome_sistema ?? 'Jacy Cabeleireiro', ENT_QUOTES, 'UTF-8'); ?>"
        width="<?php echo $w; ?>" height="<?php echo $h; ?>"
        loading="eager" decoding="async" fetchpriority="high">
    </div>

    <!-- header section start -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container ">
          <a class="navbar-brand" href="/">
            <?php
              $logoPngUrl   = '/sistema/img/logo.png';
              $logoWebpUrl  = '/sistema/img/logo.webp';
              $logoWebpFile = $_SERVER['DOCUMENT_ROOT'] . $logoWebpUrl;
            ?>
            <picture>
              <?php if (is_file($logoWebpFile)) : ?>
                <source type="image/webp" srcset="<?php echo $logoWebpUrl; ?>?v=1">
              <?php endif; ?>
              <img src="<?php echo $logoPngUrl; ?>?v=1" width="80" height="40" style="margin-right:3px;height:auto" alt="Logo">
            </picture>
          </a>

          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                  aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Alternar navegação">
            <span class=""></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav">
              <li class="nav-item active">
                <a class="nav-link active" href="agendamentos" target="_blank">
                  <span class="sr-only">(current)</span>📅 Agendamentos
                </a>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="assinatura" target="_blank"> 📝 Assinaturas</a>
              </li>

              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">💼 Serviços</a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="servicos" target="_blank" rel="noopener">Serviços</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="barbearia" target="_blank" rel="noopener">Barbeiro</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="protese" target="_blank" rel="noopener">Protese-capilar</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="corte-cabelo" target="_blank" rel="noopener">Corte-cabelo</a>
                </div>
              </li>

              <li class="nav-item">
                <a class="nav-link" href="produtos" target="_blank">🛍️ Produtos</a>
              </li>

              <li class="nav-item">
                <a title="Acesso exclusivo ao painel do cliente" class="nav-link" href="sistema/acesso" target="_blank" rel="noopener">
                  <i class="fa fa-users" aria-hidden="true"></i> Acessar
                </a>
              </li>

              <li class="nav-item">
                <a title="Acesso exclusivo ao painel do profissional" class="nav-link" href="sistema" target="_blank" rel="noopener">
                  <i class="fa fa-user" aria-hidden="true"></i>
                </a>
              </li>

              <li class="nav-item">
                <a title="Ver Instagram" class="nav-link" href="<?php echo htmlspecialchars($instagram_sistema ?? '#', ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">
                  <i class="fa fa-instagram" aria-hidden="true"></i>
                </a>
              </li>
            </ul>
          </div>

        </nav>
      </div>
    </header>
    <!-- end header section -->

    <!-- Conversão Whatsapp (mantido) -->
    <script>
      gtag('event', 'conversion', {'send_to': 'AW-10875176701/Hr-CCPLCybADEP2N2MEo'});
    </script>
