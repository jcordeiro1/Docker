<?php
$cfg = @include __DIR__ . '/config/marketplace.php';
if (!is_array($cfg) || empty($cfg['master'])) {
  http_response_code(500);
  exit('Config do marketplace ausente.');
}

try {
  $pdo = new PDO(
    "mysql:dbname={$cfg['master']['db']};host={$cfg['master']['host']};charset=utf8mb4",
    $cfg['master']['user'],
    $cfg['master']['pass'] ?? '',
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (Exception $e) {
  http_response_code(500);
  exit('Erro ao conectar no master.');
}

$q = trim($_GET['q'] ?? '');
$sql = "SELECT slug, nome, cidade, estado, logo, whatsapp FROM tenants WHERE ativo = 1";
$par = [];
if ($q !== '') {
  $sql .= " AND (nome LIKE :q OR cidade LIKE :q OR slug LIKE :q)";
  $par[':q'] = '%'.$q.'%';
}
$sql .= " ORDER BY nome ASC LIMIT 80";
$st = $pdo->prepare($sql);
$st->execute($par);
$tenants = $st->fetchAll();

function bb_digits($v){ return preg_replace('/\D+/', '', (string)$v); }
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Marketplace - Barbearias</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#0b0b0b;color:#fff}
    .wrap{max-width:1100px;margin:0 auto;padding:24px}
    .top{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap}
    input{padding:12px 14px;border-radius:10px;border:1px solid rgba(255,255,255,.2);background:#111;color:#fff;min-width:260px}
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:14px;margin-top:18px}
    .card{border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:14px;background:#0f0f0f}
    .card img{width:52px;height:52px;border-radius:12px;object-fit:cover;border:1px solid rgba(255,255,255,.12)}
    .row{display:flex;gap:12px;align-items:center}
    .nome{font-weight:700}
    .meta{opacity:.85;font-size:13px;margin-top:2px}
    .btn{display:inline-block;margin-top:10px;padding:10px 12px;border-radius:10px;background:#fff;color:#000;text-decoration:none;font-weight:700}
    .pill{display:inline-block;font-size:12px;padding:4px 8px;border-radius:999px;border:1px solid rgba(255,255,255,.14);opacity:.85;margin-top:6px}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="top">
      <h2 style="margin:0">Barbearias</h2>
      <form method="get" style="margin:0">
        <input name="q" value="<?=htmlspecialchars($q)?>" placeholder="Buscar por nome, cidade ou slug">
      </form>
    </div>

    <div class="grid">
      <?php foreach ($tenants as $t):
        $slug = $t['slug'];
        $logo = $t['logo'] ?: 'logo.png';
        $local = trim(($t['cidade'] ?? '') . (empty($t['estado']) ? '' : ' / '.$t['estado']));
        $logo_url = "/loja/{$slug}/sistema/img/".rawurlencode($logo);
        $w = bb_digits($t['whatsapp'] ?? '');
      ?>
      <div class="card">
        <div class="row">
          <img src="<?=$logo_url?>" alt="">
          <div>
            <div class="nome"><?=htmlspecialchars($t['nome'])?></div>
            <div class="meta"><?=htmlspecialchars($local ?: $slug)?></div>
            <?php if ($w): ?><div class="pill">WhatsApp: <?=$w?></div><?php endif; ?>
          </div>
        </div>
        <a class="btn" href="/loja/<?=rawurlencode($slug)?>/">Ver e agendar</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
