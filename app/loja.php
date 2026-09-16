<?php
@session_start();
require_once __DIR__ . "/sistema/conexao.php";

// pega slug pela URL /loja/slug
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$seg = array_values(array_filter(explode('/', trim($uri,'/'))));
$slug = (isset($seg[0]) && $seg[0] === 'loja' && !empty($seg[1])) ? $seg[1] : ($_SESSION['tenant_slug'] ?? '');

function bb_digits($v){ return preg_replace('/\D+/', '', (string)$v); }

$w = bb_digits($whatsapp_sistema ?? '');
$wa_link = $w ? ("https://wa.me/55".$w) : "#";
$logo = $logo_sistema ?? 'logo.png';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?=htmlspecialchars($nome_sistema ?? 'Barbearia')?></title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#0b0b0b;color:#fff}
    .wrap{max-width:900px;margin:0 auto;padding:24px}
    .card{border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:18px;background:#0f0f0f}
    .row{display:flex;gap:14px;align-items:center;flex-wrap:wrap}
    img{width:74px;height:74px;border-radius:18px;object-fit:cover;border:1px solid rgba(255,255,255,.12)}
    .btn{display:inline-block;padding:12px 14px;border-radius:12px;background:#fff;color:#000;text-decoration:none;font-weight:800;margin-right:10px;margin-top:10px}
    .muted{opacity:.85}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <div class="row">
        <img src="<?=$url_sistema?>sistema/img/<?=rawurlencode($logo)?>" alt="">
        <div>
          <h2 style="margin:0"><?=htmlspecialchars($nome_sistema ?? '')?></h2>
          <div class="muted"><?=htmlspecialchars($endereco_sistema ?? '')?></div>
        </div>
      </div>

      <div style="margin-top:12px">
        <a class="btn" href="<?=$url_sistema?>agendamentos">Agendar agora</a>
        <a class="btn" href="<?=$url_sistema?>sistema/">Entrar no painel</a>
        <?php if ($w): ?>
          <a class="btn" href="<?=$wa_link?>" target="_blank" rel="noopener">WhatsApp</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
