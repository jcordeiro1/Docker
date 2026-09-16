<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ============================================================
   BOOT / SEGURANÇA
   ============================================================ */

// Sessão com flags seguras (antes de qualquer output)
if (session_status() === PHP_SESSION_NONE) {
  $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => $_SERVER['HTTP_HOST'] ?? '',
    'secure'   => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
  @session_start();
}

// Headers de segurança
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
if (!headers_sent()) {
  if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
  }
  $csp = [
    "default-src 'self'",
    "base-uri 'self'",
    "form-action 'self'",
    "frame-ancestors 'self'",
    "upgrade-insecure-requests",
    // imagens
    "img-src 'self' data: https: i.ytimg.com s.ytimg.com",
    // scripts (inclui GA / GTM / CDNs)
    "script-src 'self' 'unsafe-inline' https://www.youtube.com https://www.youtube-nocookie.com https://www.google.com https://www.gstatic.com https://s.ytimg.com https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.googletagmanager.com https://www.google-analytics.com",
    "style-src 'self' 'unsafe-inline'",
    "frame-src https://www.youtube.com https://www.youtube-nocookie.com https://www.google.com",
    // conexões (fetch/XHR)
    "connect-src 'self' https://www.youtube.com https://www.google.com https://www.gstatic.com https://i.ytimg.com https://s.ytimg.com https://*.googlevideo.com https://www.googletagmanager.com https://www.google-analytics.com",
    "media-src 'self' https: blob: https://*.googlevideo.com",
    "font-src 'self' data: https:"
  ];
  header('Content-Security-Policy: ' . implode('; ', $csp));
}

/* ============================================================
   CONEXÃO
   ============================================================ */
$pdo = null;
try {
  require_once $_SERVER['DOCUMENT_ROOT'] . '/sistema/conexao.php';
} catch (Throwable $e) {
  $pdo = null;
}
require_once __DIR__ . '/security.php';

/* ============================================================
   HELPERS
   ============================================================ */
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function d($s) { return preg_replace('/\D+/', '', (string)$s); }
function money_br($v) {
  if ($v === null || $v === '') return '';
  if (is_string($v) && preg_match('/^\d+(?:[.,]\d+)?$/', $v)) { $v = str_replace(',', '.', $v); }
  return 'R$ ' . number_format((float)$v, 2, ',', '.');
}
function table_exists(?PDO $pdo, $t) {
  if (!$pdo) return false;
  try { $st = $pdo->prepare('SHOW TABLES LIKE :t'); $st->execute([':t'=>$t]); return (bool)$st->fetch(PDO::FETCH_NUM); }
  catch (Throwable $e) { return false; }
}
function col_exists(PDO $pdo, $t, $c) {
  try { $st=$pdo->prepare("SHOW COLUMNS FROM `$t` LIKE :c"); $st->execute([':c'=>$c]); return (bool)$st->fetch(PDO::FETCH_ASSOC); }
  catch (Throwable $e) { return false; }
}
function coalesce_expr(PDO $pdo, $t, $cands, $alias) {
  $cands = (array)$cands;
  $ok = [];
  foreach ($cands as $c) {
    if (col_exists($pdo, $t, $c)) { $ok[] = $c; }
  }
  if (!empty($ok)) {
    $parts = [];
    foreach ($ok as $c) {
      $c = str_replace('`','',$c);
      $parts[] = '`'.$c.'`';
    }
    $cols = implode(',', $parts);
    return "COALESCE($cols) AS $alias";
  }
  return "NULL AS $alias";
}
function canonical_link($u) {
  $u = trim((string)$u); if ($u==='' || $u==='#') return '';
  if (preg_match('#^(https?:)?//#i', $u)) return $u;
  if ($u[0] === '/') return $u;
  if (preg_match('/^[\w.-]+\.[a-z]{2,}(\/.*)?$/i', $u)) return 'https://' . $u;
  return '/' . ltrim($u, '/');
}
function yt_id(string $urlOrId): string {
  $u = trim($urlOrId);
  if ($u === '') return '';
  if (preg_match('~^(?:https?://)?(?:www\.)?(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $u, $m)) {
    return $m[1];
  }
  if (preg_match('~^[A-Za-z0-9_-]{6,}$~', $u)) return $u;
  return '';
}
function fix_path(string $raw): string {
  $raw = trim($raw);
  if ($raw === '' || $raw === '#') return '';
  if (preg_match('#^(?:https?:)?//#i', $raw) || strncmp($raw,'data:',5)===0) return $raw;
  if ($raw[0] === '/') return $raw;
  $cands = [];
  if (preg_match('#^(paginas/|site/|uploads_)#i', $raw)) {
    $cands[] = '/sistema/painel/'.$raw;
    $cands[] = '/'.$raw;
  }
  if (defined('UP')) $cands[] = rtrim(UP,'/').'/'.ltrim($raw,'/');
  $cands[] = '/sistema/painel/paginas/site/'.ltrim($raw,'/');
  foreach ($cands as $u) {
    $abs = $_SERVER['DOCUMENT_ROOT'].$u;
    if (is_file($abs)) return $u;
  }
  return $cands[0] ?? '/'.$raw;
}
function resolve_media_src(string $raw): array {
  $u = fix_path($raw);
  $path = parse_url($u, PHP_URL_PATH) ?? $u;
  $isVideo = preg_match('/\.(mp4|webm|ogg)$/i', $path);
  return $isVideo ? ['img'=>'','video'=>$u] : ['img'=>$u,'video'=>''];
}

/* ============================================================
   SETTINGS
   ============================================================ */
function loadSettings(?PDO $pdo) {
  $out = [
    'site'       => 'https://jacycabeleireiro.com/',
    'nome'       => 'Jacy Cabeleireiro',
    'descricao'  => 'Cortes, prótese capilar, mechas e escovas com técnica e acabamento profissional.',
    'whatsapp'   => '5545999882100',
    'telefone'   => '(45) 99988-2100',
    'email'      => 'jacycordeiro1@gmail.com',
    'endereco'   => 'Rio Grande do Sul, 2151 - Cascavel/PR',
    'uploads'    => '/sistema/painel/paginas/site/uploads_banners',
    'logo_png'   => '/sistema/img/logo.png',
    'logo_webp'  => '/sistema/img/logo.webp',
    'agenda_url' => 'https://jacycabeleireiro.com/agendamentos',
  ];
  if (!$pdo) return $out;
  $tabs = ['site_config','configuracoes','empresa','institucional','config_site','settings','config'];
  foreach ($tabs as $t) {
    if (!table_exists($pdo, $t)) continue;
    try {
      $nome    = coalesce_expr($pdo,$t,['nome','titulo','razao_social','nome_fantasia'],'nome');
      $desc    = coalesce_expr($pdo,$t,['descricao','descricao_site','sobre','texto'],'descricao');
      $wpp     = coalesce_expr($pdo,$t,['whatsapp','wpp','telefone2'],'whatsapp');
      $tel     = coalesce_expr($pdo,$t,['telefone','fone'],'telefone');
      $mail    = coalesce_expr($pdo,$t,['email','mail','contato'],'email');
      $end     = coalesce_expr($pdo,$t,['endereco','endereço','address'],'endereco');
      $logo    = coalesce_expr($pdo,$t,['logo_webp','logo','logo_png'],'logo');
      $uploads = coalesce_expr($pdo,$t,['uploads_base','uploads','upload_dir'],'uploads');
      $agenda  = coalesce_expr($pdo,$t,['agenda','agenda_url','link_agenda'],'agenda_url');
      $site    = coalesce_expr($pdo,$t,['site','url','dominio'],'site');
      $sql = "SELECT $nome,$desc,$wpp,$tel,$mail,$end,$logo,$uploads,$agenda,$site FROM `$t` LIMIT 1";
      $r   = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
      if ($r) { foreach ($r as $k=>$v) { if ($v!==null && $v!=='') $out[$k]=$v; } break; }
    } catch (Throwable $e) {}
  }
  return $out;
}
$SET = loadSettings($pdo);
$WPP = 'https://wa.me/' . (strpos($SET['whatsapp'],'55')===0 ? $SET['whatsapp'] : '55'.d($SET['whatsapp']));
if (!defined('UP')) define('UP', rtrim($SET['uploads'] ?? '/sistema/public/uploads/site/', '/') . '/');

/* ============================================================
   SLIDES (topo edge-to-edge)
   ============================================================ */
function loadSlides(?PDO $pdo) {
  $fallback = [[
    'img'   => UP.'banner-1.webp',
    'video' => '',
    'titulo'=> 'Prótese Capilar Masculino',
    'texto' => 'Técnica discreta e com acabamento profissional.',
    'link'  => '/agendamentos'
  ]];
  if (!$pdo) return $fallback;
  $table = table_exists($pdo,'site_banners_data') ? 'site_banners_data'
         : (table_exists($pdo,'site_banners')     ? 'site_banners'
         : (table_exists($pdo,'arquivos')         ? 'arquivos' : null));
  if (!$table) return $fallback;
  try {
    $isData = ($table==='site_banners_data');
    $midia  = $isData
      ? coalesce_expr($pdo,$table,['arquivo','src','caminho','midia'],'src')
      : coalesce_expr($pdo,$table,['arquivo','src','caminho','link','url'],'src');
    $titulo = coalesce_expr($pdo,$table,['titulo','nome','legenda'],'titulo');
    $texto  = coalesce_expr($pdo,$table,['texto','descricao','conteudo','caption'],'texto');
    $cta    = coalesce_expr($pdo,$table,['btn_url','cta_url','link_url','href','url','link','arquivo_url'],'cta');
    $ordem  = col_exists($pdo,$table,'ordem') ? 'ordem' : (col_exists($pdo,$table,'posicao')?'posicao':'id');
    $wheres = [];
    if (col_exists($pdo,$table,'ativo'))    $wheres[]='COALESCE(ativo,1)=1';
    if (col_exists($pdo,$table,'registro')) $wheres[]="registro IN ('home','site','index','banner_home')";
    $sqlWhere = $wheres ? (' WHERE ' . implode(' AND ', $wheres)) : '';
    $rows = $pdo->query("SELECT $titulo,$texto,$midia,$cta FROM `$table` $sqlWhere ORDER BY $ordem ASC LIMIT 12")
                ->fetchAll(PDO::FETCH_ASSOC);
    $out  = [];
    foreach ($rows as $r) {
      $src = trim((string)($r['src'] ?? ''));
      if ($src === '') continue;
      if (!preg_match('#^(?:https?:)?//#i', $src) && strpos($src, '/') === false) {
        $src = rtrim(UP, '/').'/'.$src;
      }
      $media = resolve_media_src($src);
      $out[] = [
        'img'   => $media['img'],
        'video' => $media['video'],
        'titulo'=> trim((string)($r['titulo'] ?? '')),
        'texto' => trim((string)($r['texto']  ?? '')),
        'link'  => canonical_link((string)($r['cta'] ?? '')),
      ];
    }
    return $out ?: $fallback;
  } catch (Throwable $e) { return $fallback; }
}
$slides = loadSlides($pdo);
$LCP    = (!empty($slides[0]['img'])) ? $slides[0]['img'] : '';

/* ============================================================
   HERO (Agenda Seletiva, ESQ)
   ============================================================ */
function loadHeroCarousel(?PDO $pdo): array {
  $fallback = [
    ['id'=>'108YeuHhaFQ','titulo'=>'','subtitulo'=>'','thumb'=>'https://img.youtube.com/vi/108YeuHhaFQ/hqdefault.jpg'],
    ['id'=>'26ZB20BR0E4','titulo'=>'','subtitulo'=>'','thumb'=>'https://img.youtube.com/vi/26ZB20BR0E4/hqdefault.jpg'],
  ];
  if (!$pdo || !table_exists($pdo,'site_carrossel')) return $fallback;
  try {
    $sql = "SELECT id, titulo, subtitulo, video_url, thumb, ordem, ativo
              FROM site_carrossel
             WHERE COALESCE(ativo,1)=1
          ORDER BY COALESCE(ordem,0), id";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $out = [];
    foreach ($rows as $r) {
      $vid = yt_id((string)$r['video_url']);
      if ($vid === '') continue;
      $thumb = trim((string)($r['thumb'] ?? ''));
      if ($thumb !== '') {
        if (!preg_match('#^https?://#i', $thumb)) {
          if (preg_match('#^/?paginas/#', $thumb)) {
            $thumb = '/sistema/painel/' . ltrim($thumb, '/');
          } else {
            $thumb = '/' . ltrim($thumb, '/');
          }
        }
      } else {
        $thumb = "https://img.youtube.com/vi/$vid/hqdefault.jpg";
      }
      $out[] = [
        'id'        => $vid,
        'thumb'     => $thumb,
        'titulo'    => trim((string)($r['titulo'] ?? '')),
        'subtitulo' => trim((string)($r['subtitulo'] ?? '')),
      ];
    }
    return $out ?: $fallback;
  } catch (Throwable $e) {
    return $fallback;
  }
}
$HERO = loadHeroCarousel($pdo);

/* ============================================================
   SERVIÇOS
   ============================================================ */
function loadServices(?PDO $pdo): array {
  if (!$pdo) return [];
  $cands = [
    ['t'=>'servicos','title'=>['titulo','nome'],'desc'=>['descricao','texto','resumo'],'preco'=>['preco','valor'],'img'=>['capa','img','imagem','thumb'],'link'=>['link','url','slug'],'ativo'=>['ativo','status']],
    ['t'=>'produtos_servicos','title'=>['titulo','nome'],'desc'=>['descricao','texto'],'preco'=>['preco','valor'],'img'=>['imagem','capa'],'link'=>['url','slug'],'ativo'=>['ativo','status']],
    ['t'=>'servicos_site','title'=>['titulo','nome'],'desc'=>['descricao','texto'],'preco'=>['preco','valor'],'img'=>['imagem','capa'],'link'=>['url','slug'],'ativo'=>['ativo','status']],
  ];
  foreach ($cands as $c) {
    if (!table_exists($pdo, $c['t'])) continue;
    try {
      $title = coalesce_expr($pdo, $c['t'], $c['title'], 'titulo');
      $desc  = coalesce_expr($pdo, $c['t'], $c['desc'],  'descricao');
      $preco = coalesce_expr($pdo, $c['t'], $c['preco'], 'preco');
      $img   = coalesce_expr($pdo, $c['t'], $c['img'],   'img');
      $link  = coalesce_expr($pdo, $c['t'], $c['link'],  'link');
      $ativoCol = null;
      foreach ($c['ativo'] as $ac) {
        if (col_exists($pdo, $c['t'], $ac)) { $ativoCol = $ac; break; }
      }
      $where = $ativoCol ? "WHERE COALESCE($ativoCol,1)=1" : "";
      $sql  = "SELECT $title,$desc,$preco,$img,$link FROM `{$c['t']}` $where ORDER BY 1 LIMIT 12";
      $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
      if (!$rows) continue;
      $out = [];
      foreach ($rows as $r) {
        $imgSrc = trim((string)($r['img'] ?? ''));
        if ($imgSrc === '') continue;
        $path = parse_url($imgSrc, PHP_URL_PATH) ?: '';
        $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $isImg  = in_array($ext, ['jpg','jpeg','png','webp','gif'], true);
        $isVideo= in_array($ext, ['mp4','webm','ogg'], true);
        if (!$isImg && !$isVideo) continue;
        if (!preg_match('#^https?://#i', $imgSrc) && ($imgSrc[0] ?? '') !== '/') {
          if (preg_match('#^/?paginas/#', $imgSrc)) {
            $imgSrc = '/sistema/painel/' . ltrim($imgSrc, '/');
          } else {
            $imgSrc = '/' . ltrim($imgSrc, '/');
          }
        }
        $out[] = [
          'img'      => $isImg ? $imgSrc : '',
          'video'    => $isVideo ? $imgSrc : '',
          'titulo'   => trim((string)($r['titulo'] ?? '')),
          'descricao'=> trim((string)($r['descricao'] ?? '')),
          'preco'    => trim((string)($r['preco'] ?? '')),
          'link'     => canonical_link((string)($r['link'] ?? '')),
        ];
      }
      return $out;
    } catch (Throwable $e) {}
  }
  return [];
}
$servicos = loadServices($pdo);

/* ============================================================
   GALERIA
   ============================================================ */
if (!function_exists('galeria_resolve_img')) {
  function galeria_resolve_img(string $nome): string {
    $nome = trim($nome);
    if ($nome === '') return '';
    $maybe = fix_path($nome);
    $absMaybe = $_SERVER['DOCUMENT_ROOT'].(parse_url($maybe, PHP_URL_PATH) ?? $maybe);
    if (is_file($absMaybe)) return (parse_url($maybe, PHP_URL_PATH) ?? $maybe);
    $nome = basename($nome);
    $roots = [
      $_SERVER['DOCUMENT_ROOT']."/sistema/painel/img/foto_admin/$nome",
      $_SERVER['DOCUMENT_ROOT']."/sistema/painel/img/fotos/$nome",
      $_SERVER['DOCUMENT_ROOT']."/sistema/painel/img/galeria/$nome",
      $_SERVER['DOCUMENT_ROOT']."/sistema/public/uploads/site/$nome",
      $_SERVER['DOCUMENT_ROOT']."/sistema/img/$nome",
    ];
    foreach ($roots as $abs) {
      if (is_file($abs)) return str_replace($_SERVER['DOCUMENT_ROOT'], '', $abs);
    }
    return "/sistema/painel/img/foto_admin/sem-foto.jpg";
  }
}
if (!function_exists('galeria_load_rows')) {
  function galeria_load_rows(?PDO $pdo): array {
    if (!$pdo) return [];
    try {
      $sql  = "SELECT id, titulo, imagem FROM foto_admin ORDER BY data_cad DESC, id DESC";
      $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
      $out  = [];
      foreach ($rows as $r) {
        $src = galeria_resolve_img((string)($r['imagem'] ?? ''));
        if ($src !== '') {
          $out[] = ['titulo' => trim((string)($r['titulo'] ?? '')), 'src' => $src];
        }
      }
      return $out;
    } catch (Throwable $e) { return []; }
  }
}
$GALERIA_ROWS = galeria_load_rows($pdo);

/* ============================================================
   HTML
   ============================================================ */
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <!-- Google tag (gtag.js) – async não-bloqueante -->
  <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
  <link rel="preconnect" href="https://www.google-analytics.com" crossorigin>
  <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10875176701"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'AW-10875176701');
  </script>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?=h($SET['nome'])?> — Beleza &amp; Prótese Capilar</title>
  <meta name="author" content="Jacy Cordeiro">
  <meta name="theme-color" content="#000000">
  <link rel="shortcut icon" href="images/<?=h($icone_site ?? '')?>" type="image/x-icon">
  <meta name="robots" content="index,follow,max-image-preview:large">
  <link rel="canonical" href="<?=h(rtrim($SET['site'],'/').'/')?>">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?=h($SET['nome'])?>">
  <meta property="og:title" content="<?=h($SET['nome'].' — Beleza & Prótese Capilar')?>">
  <meta property="og:description" content="<?=h($SET['descricao'])?>">
  <meta property="og:url" content="<?=h(rtrim($SET['site'],'/').'/')?>">
  <meta property="og:image" content="<?=h($SET['logo_png'])?>">
  <meta name="twitter:card" content="summary_large_image">

  <!-- Preconnect críticos -->
  <link rel="preconnect" href="https://code.jquery.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="dns-prefetch" href="//code.jquery.com">
  <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
  <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

  <?php if ($LCP): 
    $isExt = preg_match('#^https?://#i',$LCP);
  ?>
    <link rel="preload" as="image" href="<?=h($LCP)?>" fetchpriority="high" <?= $isExt ? 'crossorigin' : ''?>>
  <?php endif; ?>

  <!-- jQuery necessário para plugins: carregado com defer -->
  <link rel="modulepreload" href="https://code.jquery.com/jquery-3.7.1.min.js" crossorigin>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer integrity="sha256-3fpZawG8KX9D9wQpJqYqU3H6G+jw/adJzi10BGSA7Ms=" crossorigin="anonymous"></script>

  <!-- reCAPTCHA v3 (chave exposta por arquivo local) -->
  <?php
    $___recaptcha_file = $_SERVER['DOCUMENT_ROOT'].'/config/recaptcha.php';
    if (is_file($___recaptcha_file)) {
      require_once $___recaptcha_file;
    }
    $___site_key = defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '';
  ?>
  <script>window.RECAPTCHA_SITE_KEY="<?=h($___site_key)?>";</script>

  <style><?php /* CSS crítico minificado */ ?>
:root{--bg:#0b0b0d;--bg2:#131319;--card:#1a1c23;--text:#e9e9ee;--muted:#b0b2b8;--brand:#2d86ff;--shadow:0 10px 30px rgba(0,0,0,.3)}html,body{margin:0;background:var(--bg);color:var(--text);font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Helvetica Neue',Arial,'Noto Sans',sans-serif;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}a{color:#fff;text-decoration:none}a:hover{opacity:.9}.container{max-width:1200px;margin:0 auto;padding:0 16px}.btn{display:inline-block;padding:10px 16px;border-radius:12px;font-weight:800;cursor:pointer}.btn-brand{background:var(--brand)}.btn-outline{outline:2px solid rgba(255,255,255,.25);background:transparent}.hero-viewport{width:100vw;max-width:100vw;margin:0;padding:0}.carousel{position:relative;overflow:hidden;width:100vw;max-width:100vw;border-radius:0}.slides{display:flex;flex-wrap:nowrap;transition:transform .5s ease;will-change:transform}.slide{min-width:100vw;flex:0 0 100vw;position:relative;aspect-ratio:16/9;background:#000}.slide img,.slide video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block}.caption{position:absolute;left:min(6vw,64px);bottom:min(8vh,80px);max-width:min(560px,86vw);padding:16px 18px;border-radius:12px;background:linear-gradient(90deg,rgba(0,0,0,.55),rgba(0,0,0,.15));backdrop-filter:blur(2px)}.caption h3{margin:0 0 4px;font-size:clamp(18px,2.5vw,28px)}.caption p{margin:0 0 10px;color:#e7e7e7}.nav{position:absolute;inset:0;display:flex;justify-content:space-between;align-items:center;pointer-events:none}.nav button{pointer-events:auto;border:0;background:rgba(0,0,0,.35);color:#fff;width:44px;height:44px;border-radius:999px;margin:0 12px;font-size:22px;cursor:pointer}.dots{position:absolute;left:0;right:0;bottom:12px;display:flex;justify-content:center;gap:6px}.dots button.active{background:#fff}.dots button{width:10px;height:10px;border-radius:999px;border:0;background:rgba(255,255,255,.45);cursor:pointer}@media(max-width:800px){.slide{aspect-ratio:3/4}.caption{left:16px;right:16px;bottom:16px}}.section{padding:28px 0}.cards{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}.card{background:var(--card);border:1px solid rgba(255,255,255,.07);border-radius:14px;overflow:hidden;display:flex;flex-direction:column}.card img{width:100%;height:180px;object-fit:cover;display:block;background:#000}.card .p{padding:12px;display:flex;flex-direction:column;gap:6px}.price{font-weight:800;color:#fff}.muted{color:var(--muted)}.foot{border-top:1px solid rgba(255,255,255,.07);padding:18px 0;background:var(--bg2);color:#c9c9cf}.foot a{text-decoration:underline;color:#c9c9cf}@media(max-width:1000px){.cards{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.cards{grid-template-columns:1fr}}.modal{display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.7);align-items:center;justify-content:center}.modal.show{display:flex}.modal-dialog{width:95%;max-width:900px;margin:0}.modal-content{background:#0f0f10;color:#e6e6e6;border-radius:10px;overflow:hidden;max-height:90vh;display:flex;flex-direction:column}.modal-header,.modal-footer{padding:12px 16px;border:0}.modal-header{border-bottom:1px solid #2a2a2c}.modal-footer{border-top:1px solid #2a2a2c}.modal-body{padding:16px;overflow:auto}.close{border:0;background:transparent;color:#e6e6e6;font-size:26px;line-height:1;cursor:pointer}.grecaptcha-badge{z-index:2147483647}
  </style>
<style>
  /* Container e grid independentes do Bootstrap */
  .xp-wrap{max-width:1280px;margin:0 auto;padding:0 16px}
  .xp-grid{display:grid;grid-template-columns:1.1fr 1fr;gap:32px;align-items:center}
  @media (max-width: 980px){.xp-grid{grid-template-columns:1fr}}

  /* Coluna esquerda (1999 + visitas) */
  .xp-left-year{position:relative;text-align:center}
  .xp-year{font-size:min(22vw,200px);line-height:.85;font-weight:900;letter-spacing:2px;background:linear-gradient(180deg,#fff 0%,#b9c5ff 60%,#8aa2ff 100%);-webkit-background-clip:text;background-clip:text;color:transparent;text-shadow:0 10px 40px rgba(0,0,0,.25)}
  .xp-subtitle{font-size:28px;font-weight:800;color:#cfe1ff}
  .xp-underline{width:120px;height:4px;background:linear-gradient(90deg,#2d86ff,#3ddc97);border-radius:6px;margin:10px auto 18px}

  .xp-visitas{background:rgba(255,255,255,0.05);border:2px solid rgba(66,133,244,0.3);border-radius:15px;padding:20px;display:flex;align-items:center;justify-content:center;gap:14px}
  .xp-vis-num{font-size:2.4rem;font-weight:800;color:#4285f4;line-height:1}
  .xp-vis-label{font-size:.9rem;color:#bbb;text-transform:uppercase;letter-spacing:1px;font-weight:600}

  /* Coluna direita (título, texto e cards) */
  .xp-title{color:#fff;font-size:clamp(28px,4.4vw,56px);line-height:1.05;font-weight:900;margin:0 0 10px}
  .xp-title .blue{color:#8ab4ff}
  .xp-desc{color:#d7dbff;line-height:1.75;margin:0 0 22px;max-width:60ch}

  /* Cards */
  .exp-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:26px}
  @media (max-width:900px){.exp-stats{grid-template-columns:repeat(2,1fr)}}
  @media (max-width:520px){.exp-stats{grid-template-columns:1fr}}
  .exp-card{background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.10);border-radius:12px;padding:18px;text-align:center}
  .exp-num{font-size:2.4rem;font-weight:800;line-height:1;margin-bottom:6px}
  .exp-label{font-size:.9rem;letter-spacing:1px;text-transform:uppercase;color:#bbb;font-weight:600}
  .exp-num--blue{color:#4285f4}.exp-num--green{color:#34a853}.exp-num--gold{color:#fbbc04}

  .xp-badges{display:flex;gap:12px;flex-wrap:wrap;margin-top:12px}
  .xp-badge{display:inline-flex;align-items:center;gap:10px;padding:10px 18px;border-radius:999px;font-weight:800;border:1px solid}
  .xp-badge--cert{background:#0e1935;border-color:rgba(141,176,255,.25);color:#cfe1ff}
  .xp-badge--qual{background:#0f1d15;border-color:rgba(83,214,155,.25);color:#bff3dd}
  .xp-badge .dot{display:inline-grid;place-items:center;width:22px;height:22px;border-radius:50%}
  .xp-badge--cert .dot{background:#2d86ff;color:#fff}
  .xp-badge--qual .dot{background:#3ddc97;color:#0d2a1f}

  /* Box visitas compacto por padrão */
  .experience-section{--vis-w:200px;--vis-pad:12px 14px;--vis-num:1.8rem;--vis-icon:1.6rem;--vis-bw:1px;--under-w:90px;--under-h:3px;--under-m:8px auto 14px}
  .experience-section .xp-visitas{max-width:var(--vis-w);margin:10px auto 0;padding:var(--vis-pad);border-width:var(--vis-bw);border-radius:12px}
  .experience-section .xp-visitas > div:first-child{font-size:var(--vis-icon)!important;line-height:1}
  .experience-section .xp-visitas .xp-vis-num{font-size:var(--vis-num)}
  .experience-section .xp-left-year .xp-underline{width:var(--under-w);height:var(--under-h);margin:var(--under-m)}
  .experience-section.visitas-normal{--vis-w:100%;--vis-pad:20px;--vis-num:2.4rem;--vis-icon:2.2rem;--vis-bw:2px;--under-w:120px;--under-h:4px;--under-m:10px auto 18px}
</style>

</head>
<body>

<header id="siteHeader" style="position:fixed;top:0;left:0;right:0;background:#0d0d0d;color:#fff;z-index:9999">
  <div style="max-width:1280px;margin:0 auto;display:flex;align-items:center;gap:14px;padding:10px 16px">
    <?php
      $logoPng  = '/sistema/img/logo.png';
      $logoWebp = '/sistema/img/logo.webp';
      $hasWebp  = is_file($_SERVER['DOCUMENT_ROOT'].$logoWebp);
    ?>
    <a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:#fff">
      <picture>
        <?php if($hasWebp):?><source type="image/webp" srcset="<?=$logoWebp?>?v=1" sizes="42px"><?php endif;?>
        <img src="<?=$logoPng?>?v=1" alt="Logo" width="42" height="42" style="display:block">
      </picture>
      <strong style="font-weight:700;white-space:nowrap">Jacy Cabeleireiro</strong>
    </a>
    <nav style="margin-left:auto;display:flex;align-items:center;gap:22px;flex-wrap:wrap">
      <a href="/agendamentos" style="color:#fff;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:6px">📅 AGENDAMENTOS</a>
      <a href="/assinatura" style="color:#fff;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:6px">📝 ASSINATURAS</a>
      <details style="position:relative">
        <summary style="list-style:none;cursor:pointer;display:flex;align-items:center;gap:6px;font-weight:700">💼 SERVIÇOS <span style="opacity:.7">▾</span></summary>
        <div style="position:absolute;top:calc(100% + 8px);left:0;background:#fff;color:#111;min-width:200px;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,.25);overflow:hidden">
          <a href="#" style="display:block;padding:10px 14px;text-decoration:none;color:#111;border-bottom:1px solid #eee">Serviços</a>
          <a href="#" style="display:block;padding:10px 14px;text-decoration:none;color:#111;border-bottom:1px solid #eee">Barbeiro</a>
          <a href="/protese" style="display:block;padding:10px 14px;text-decoration:none;color:#111;border-bottom:1px solid #eee">Protese-capilar</a>
          <a href="/corte-cabelo" style="display:block;padding:10px 14px;text-decoration:none;color:#111">Corte-cabelo</a>
        </div>
      </details>
      <a href="#" style="color:#fff;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:6px">🛍️ PRODUTOS</a>
      <a href="/sistema/acesso" target="_blank" rel="noopener" style="color:#fff;text-decoration:none;font-weight:700;display:flex;align-items:center;gap:6px">👥 ACESSAR</a>
      <a href="/sistema" target="_blank" rel="noopener" title="Painel" style="color:#fff;text-decoration:none;font-weight:700">👤</a>
      <a href="<?=h($instagram_sistema??'#')?>" target="_blank" rel="noopener" title="Instagram" style="color:#fff;text-decoration:none;font-weight:700">📷</a>
    </nav>
  </div>
</header>

<main>
  <section class="hero-viewport" aria-label="Destaques">
    <div class="carousel" id="hero" aria-roledescription="carousel">
      <div class="slides" id="slides">
        <?php foreach($slides as $i=>$s): $isFirst=($i===0);?>
          <div class="slide" data-index="<?=$i?>" aria-label="Slide <?=$i+1?>">
            <?php if(!empty($s['video'])):?>
              <video <?=$isFirst?'preload="metadata" autoplay':'preload="none"'?> muted playsinline loop width="1280" height="720">
                <source src="<?=h($s['video'])?>" type="video/mp4">
              </video>
            <?php else:?>
              <img src="<?=h($s['img'])?>" alt="<?=h($s['titulo']?:'Banner')?>" width="1280" height="720" decoding="async" sizes="100vw" <?=$isFirst?'loading="eager" fetchpriority="high"':'loading="lazy"'?>>
            <?php endif;?>
            <?php if(($s['titulo']??'')||($s['texto']??'')||($s['link']??'')):?>
              <div class="caption">
                <?php if($s['titulo']):?><h3><?=h($s['titulo'])?></h3><?php endif;?>
                <?php if($s['texto']):?><p><?=h($s['texto'])?></p><?php endif;?>
                <div style="display:flex;gap:8px;flex-wrap:wrap">
                  <a class="btn btn-brand" href="<?=h($SET['agenda_url'])?>">Agendar</a>
                  <?php if(!empty($s['link'])):?>
                    <a class="btn btn-outline" href="<?=h($s['link'])?>" target="_blank" rel="noopener">Saiba mais</a>
                  <?php endif;?>
                </div>
              </div>
            <?php endif;?>
          </div>
        <?php endforeach;?>
      </div>
      <div class="nav" aria-hidden="false">
        <button type="button" id="prev" aria-label="Anterior">‹</button>
        <button type="button" id="next" aria-label="Próximo">›</button>
      </div>
      <div class="dots" id="dots" role="tablist" aria-label="Indicadores"></div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <?php if($servicos):?>
        <div class="cards">
          <?php foreach($servicos as $s):?>
            <article class="card">
              <img src="<?=h($s['img']?:(UP.'placeholder.webp'))?>" width="600" height="360" alt="<?=h($s['titulo'])?>" loading="lazy" decoding="async" sizes="(max-width: 600px) 100vw, 33vw">
              <div class="p">
                <strong><?=h($s['titulo'])?></strong>
                <?php if(!empty($s['descricao'])):?>
                  <div class="muted"><?=h(mb_strimwidth(strip_tags($s['descricao']),0,160,'…','UTF-8'))?></div>
                <?php endif;?>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-top:4px">
                  <?php if($s['preco']!==''):?><span class="price"><?=h(money_br($s['preco']))?></span><?php endif;?>
                  <span class="muted">·</span>
                  <a class="btn btn-brand" href="<?=h($SET['agenda_url'])?>">Agendar</a>
                  <a class="btn btn-outline" href="<?=h($WPP)?>" target="_blank" rel="noopener">WhatsApp</a>
                  <?php if(!empty($s['link'])):?>
                    <a class="btn btn-outline" href="<?=h($s['link'])?>" target="_blank" rel="noopener">Detalhes</a>
                  <?php endif;?>
                </div>
              </div>
            </article>
          <?php endforeach;?>
        </div>
      <?php else:?>
        <p class="muted"></p>
      <?php endif;?>
    </div>
  </section>

  <section id="agenda-seletiva" style="background:radial-gradient(1200px 600px at 70% -10%,#0d1f17 0%,#070b09 60%,#050807 100%);color:#fff">
    <div style="max-width:1200px;margin:0 auto;padding:60px 20px;display:grid;grid-template-columns:1.1fr 1fr;gap:48px;align-items:center">
      <div>
        <div id="heroSlider" style="position:relative;border-radius:18px;overflow:hidden;background:#000;box-shadow:0 20px 60px rgba(0,0,0,.35)">
          <div id="heroTrack" style="display:flex;transition:transform .5s ease">
            <?php foreach($HERO as $h):?>
              <div class="hero-slide" data-video="<?=h($h['id'])?>" style="min-width:100%;position:relative;aspect-ratio:16/9;background:#000">
                <img alt="<?=h($h['titulo']?:'Vídeo')?>" loading="lazy" width="640" height="360" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.9" src="<?=h($h['thumb'])?>" onerror="this.onerror=null;this.src='https://img.youtube.com/vi/<?=h($h['id'])?>/hqdefault.jpg'">
                <button class="play-btn" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:72px;height:72px;border-radius:50%;border:0;background:rgba(255,255,255,.16);backdrop-filter:blur(6px);cursor:pointer;display:grid;place-items:center;box-shadow:0 10px 30px rgba(0,0,0,.35)">
                  <svg width="28" height="28" viewBox="0 0 24 24" fill="#fff" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                  <span class="sr" style="position:absolute;left:-9999px">Reproduzir vídeo</span>
                </button>
              </div>
            <?php endforeach;?>
          </div>
          <button id="heroPrev" aria-label="Anterior" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:42px;height:42px;border-radius:50%;border:0;background:rgba(255,255,255,.14);color:#fff;cursor:pointer;display:grid;place-items:center">‹</button>
          <button id="heroNext" aria-label="Próximo" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);width:42px;height:42px;border-radius:50%;border:0;background:rgba(255,255,255,.14);color:#fff;cursor:pointer;display:grid;place-items:center">›</button>
          <div id="heroDots" style="position:absolute;left:0;right:0;bottom:12px;display:flex;gap:8px;justify-content:center">
            <?php for($i=0,$n=count($HERO);$i<$n;$i++):?>
              <button data-i="<?=$i?>" style="width:8px;height:8px;border-radius:50%;border:0;cursor:pointer;<?=$i===0?'background:#ffffffaa':'background:#ffffff55'?>"></button>
            <?php endfor;?>
          </div>
        </div>
      </div>
      <div>
        <h2 style="font-size:48px;line-height:1.1;margin:0 0 20px 0;font-weight:800;letter-spacing:.5px">Próteses Leves<br>e Confortáveis</h2>
        <p style="color:#cfe6dc;line-height:1.7;margin:0 0 28px 0;max-width:46ch">Por isso, agende agora e garanta seu horário com o profissional que mais combina com você. O melhor investimento é aquele que investe em você.</p>
        <div style="display:flex;flex-direction:column;gap:16px;max-width:560px">
          <a id="ctaWhats" href="#" target="_blank" rel="noopener" style="display:block;text-align:center;background:#0e6f52;color:#fff;padding:18px 24px;border-radius:32px;text-decoration:none;font-weight:700;letter-spacing:.3px;box-shadow:0 6px 24px rgba(19,170,120,.25)">AGENDAR PELO WHATSAPP</a>
          <a id="ctaApp" href="#" target="_blank" rel="noopener" style="display:block;text-align:center;background:transparent;color:#d7efe6;padding:18px 24px;border-radius:32px;text-decoration:none;font-weight:700;letter-spacing:.3px;border:2px solid rgba(215,239,230,.35)">AGENDAR PELO APP-BARBERBOT</a>
        </div>
      </div>
    </div>
  </section>

  <?php if(!empty($GALERIA_ROWS)):?>
  <section id="galeria-banco" style="padding:40px 0;background:#0b0b0d;color:#fff">
    <div style="max-width:1200px;margin:0 auto;padding:0 16px">
      <h2 style="margin:0 0 22px;font-size:28px;font-weight:800;letter-spacing:.3px"></h2>
      <div id="galeriaWrap" style="position:relative">
        <button id="galPrev" aria-label="Anterior" style="position:absolute;left:-8px;top:45%;transform:translateY(-50%);width:44px;height:44px;border-radius:50%;border:0;cursor:pointer;background:rgba(255,255,255,.15);color:#fff;font-size:24px;z-index:2">‹</button>
        <button id="galNext" aria-label="Próximo" style="position:absolute;right:-8px;top:45%;transform:translateY(-50%);width:44px;height:44px;border-radius:50%;border:0;cursor:pointer;background:rgba(255,255,255,.15);color:#fff;font-size:24px;z-index:2">›</button>
        <div style="overflow:hidden">
          <div id="galTrack" style="display:flex;gap:22px;transition:transform .4s ease;will-change:transform">
            <?php foreach($GALERIA_ROWS as $g):?>
              <figure class="galItem" style="margin:0;min-width:25%;display:flex;flex-direction:column;align-items:center">
                <div style="width:100%;aspect-ratio:1/1.02;position:relative;border-radius:12px;overflow:hidden;background:#000">
                  <img src="<?=h($g['src'])?>" alt="<?=h($g['titulo']?:'Foto')?>" width="400" height="408" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:block" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='/sistema/painel/img/foto_admin/sem-foto.jpg'">
                </div>
                <?php if(!empty($g['titulo'])):?>
                  <figcaption style="margin-top:12px;font-weight:700;text-align:center"><?=h($g['titulo'])?></figcaption>
                <?php endif;?>
              </figure>
            <?php endforeach;?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif;?>
  
    <section class="experience-section" style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 100%); color:#e9e9ee; padding:56px 0 48px; border-top:1px solid #2a2a2a; border-bottom:1px solid #2a2a2a; position:relative; overflow:hidden">
    <div class="xp-wrap">
      <div class="xp-grid">
        <!-- ESQUERDA: 1999 + visitas -->
        <div class="xp-left-year">
          <div class="xp-year">1999</div>
          <div class="xp-subtitle">Início da Jornada</div>
          <div class="xp-underline"></div>

          <div class="xp-visitas">
            <div style="font-size:2.2rem">👁️</div>
            <div style="text-align:left">
              <div class="xp-vis-num"><span class="counter-1999" data-target="0">0</span></div>
              <div class="xp-vis-label">Visitas</div>
            </div>
          </div>
        </div>

        <!-- DIREITA: título, texto e cards -->
        <div>
          <h2 class="xp-title">Mais de 25 Anos de <span class="blue">Excelência</span></h2>
          <p class="xp-desc">
            Desde 1999, dedicamos nossa paixão e expertise para transformar a vida de milhares de clientes.
            Com mais de duas décadas de experiência, somos referência em <strong>cuidado capilar</strong> e
            <strong>estética masculina</strong>.
          </p>

          <!-- Mini Stats (grid) -->
          <div class="exp-stats">
            <div class="exp-card">
              <div class="exp-num exp-num--blue">25+</div>
              <div class="exp-label">Anos</div>
            </div>
            <div class="exp-card">
              <div class="exp-num exp-num--green">15k+</div>
              <div class="exp-label">Clientes</div>
            </div>
            <div class="exp-card">
              <div class="exp-num exp-num--gold">4.6</div>
              <div class="exp-label">Avaliação</div>
            </div>
          </div>

          <!-- Badges -->
          <div class="xp-badges">
            <span class="xp-badge xp-badge--cert"><span class="dot">✔</span>Profissionais Certificados</span>
            <span class="xp-badge xp-badge--qual"><span class="dot">🏆</span>Tradição & Qualidade</span>
          </div>
        </div>

      </div>
    </div>

    <!-- brilho sutil de fundo -->
    <div style="position:absolute;inset:auto -10% -40% -10%;height:300px;background:radial-gradient(60% 60% at 50% 0%,rgba(45,134,255,.25),transparent 60%)"></div>
  </section>

  <section style="background:#0b0b0d;color:#fff;padding:36px 0">
    <div style="max-width:1280px;margin:0 auto;padding:0 16px;display:flex;flex-wrap:wrap;gap:24px;align-items:center">
      <div style="flex:1 1 520px;min-width:280px">
        <div style="border-radius:14px;overflow:hidden;background:#000">
          <?php
          $url_video=isset($url_video)?(string)$url_video:'';
          $posicao_video=isset($posicao_video)?(string)$posicao_video:'';
          $imagem_sobre=isset($imagem_sobre)?basename((string)$imagem_sobre):'sobre.jpg';
          $texto_sobre=isset($texto_sobre)?(string)$texto_sobre:'';
          $videoEmCima=($url_video!=='' && trim($posicao_video)==='sobre');
          $videoEmBaixo=($url_video!=='' && trim($posicao_video)==='abaixo');
          if($videoEmCima){
            echo '<div style="position:relative;width:100%;padding-top:56.25%"><iframe src="'.h($url_video).'" title="Vídeo institucional" frameborder="0" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" style="position:absolute;inset:0;width:100%;height:100%;display:block;border:0"></iframe></div>';
          }else{
            $imgPath="images/{$imagem_sobre}";
            $webpPath=preg_replace('/\.(jpe?g|png)$/i','.webp',$imgPath);
          ?>
          <picture>
            <?php if($webpPath && file_exists($webpPath)):?>
              <source type="image/webp" srcset="<?=h($webpPath)?>">
            <?php endif;?>
            <img src="<?=h($imgPath)?>" alt="Sobre nós" width="1200" height="675" loading="lazy" decoding="async" style="width:100%;height:auto;display:block">
          </picture>
          <?php }?>
        </div>
      </div>
      <div style="flex:1 1 520px;min-width:280px;display:flex;align-items:center">
        <div style="width:100%">
          <h4 style="margin:0 0 14px;font-size:42px;line-height:1.15;font-weight:800;letter-spacing:.4px">Sobre Nós</h4>
          <p style="margin:0 0 22px;line-height:1.75;font-size:16px;color:#EDEDED"><?=nl2br(h($texto_sobre))?></p>
          <a href="#empresa" data-toggle="modal" data-target="#empresa" aria-controls="empresa" aria-label="Abrir informações da empresa" style="display:inline-block;background:#0ea5e9;border:0;color:#fff;padding:14px 22px;border-radius:999px;font-weight:800;letter-spacing:.3px;text-decoration:none;box-shadow:0 8px 26px rgba(14,165,233,.35)">Mais Informações</a>
        </div>
      </div>
    </div>
  </section>

  <?php if(!empty($videoEmBaixo)):?>
    <div style="background:#0b0b0d;padding:12px 0">
      <div style="max-width:1280px;margin:0 auto;padding:0 16px">
        <div style="position:relative;width:100%;padding-top:56.25%;border-radius:14px;overflow:hidden;background:#000">
          <iframe class="video_mobile" src="<?=h($url_video)?>" title="Vídeo institucional" frameborder="0" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen referrerpolicy="strict-origin-when-cross-origin" style="position:absolute;inset:0;width:100%;height:100%;display:block;border:0"></iframe>
        </div>
      </div>
    </div>
  <?php endif;?>

  <?php
  try{
    $resDep=$pdo?$pdo->query("SELECT nome, texto, foto FROM comentarios WHERE ativo='Sim' ORDER BY id DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC):[];
  }catch(Throwable $e){$resDep=[];}
  if($resDep && count($resDep)>0):
  ?>
  <section id="depoimentos" style="background:#0b0b0d;color:#fff;padding:46px 0">
    <div style="max-width:1280px;margin:0 auto;padding:0 16px">
      <h2 style="margin:0 0 22px;font-size:36px;font-weight:800">Depoimento dos nossos Clientes</h2>
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
        <button id="depPrev" aria-label="Anterior" style="border:0;background:#1e1f24;color:#fff;width:42px;height:42px;border-radius:50%;cursor:pointer">‹</button>
        <div id="depDots" style="display:flex;gap:6px;flex:1"></div>
        <button id="depNext" aria-label="Próximo" style="border:0;background:#1e1f24;color:#fff;width:42px;height:42px;border-radius:50%;cursor:pointer">›</button>
      </div>
      <div style="overflow:hidden">
        <div id="depTrack" style="display:flex;gap:28px;transition:transform .45s ease;will-change:transform">
          <?php foreach($resDep as $c):
            $nome=trim($c['nome']??'');
            $texto=trim($c['texto']??'');
            $foto=basename($c['foto']??'');
            $path="sistema/painel/img/comentarios/".($foto?:'sem-foto.jpg');
            $webp=preg_replace('/\.(jpe?g|png)$/i','.webp',$path);
            $src=(is_file($_SERVER['DOCUMENT_ROOT'].'/'.$webp)?$webp:$path);
          ?>
          <article class="depItem" style="min-width:100%">
            <div style="background:#17181d;border:1px solid #2a2c33;border-radius:18px;padding:26px;box-shadow:0 8px 26px rgba(0,0,0,.35)">
              <div style="display:flex;align-items:center;gap:18px;margin-bottom:18px">
                <img src="<?=h($src)?>" alt="<?=h($nome?:'Cliente')?>" width="96" height="96" loading="lazy" decoding="async" style="width:96px;height:96px;object-fit:cover;border-radius:50%;border:3px solid #2a2c33;background:#000">
                <div>
                  <strong style="font-size:18px;display:block;margin-bottom:4px"><?=h($nome?:'Cliente')?></strong>
                  <span style="opacity:.7;font-size:14px">Avaliação verificada</span>
                </div>
              </div>
              <div style="background:#111317;border:1px solid #2a2c33;border-radius:12px;padding:22px;text-align:center">
                <h4 style="margin:0 0 8px;font-weight:800"><?=h($nome?:'Cliente')?></h4>
                <p style="margin:0;line-height:1.75;color:#dfe2e7"><?=nl2br(h($texto))?></p>
              </div>
            </div>
          </article>
          <?php endforeach;?>
        </div>
      </div>
      <div style="text-align:center;margin-top:22px">
        <a href="#" data-toggle="modal" data-target="#modalComentario" style="display:inline-block;background:#0ea5e9;color:#fff;padding:14px 26px;border-radius:999px;font-weight:800;text-decoration:none;box-shadow:0 8px 26px rgba(14,165,233,.35)">Inserir Depoimento</a>
      </div>
    </div>
  </section>
  <?php endif;?>

</main>

<?php
if(!function_exists('h2')){function h2($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}}
if(!function_exists('digits')){function digits($s){return preg_replace('/\D+/','',(string)$s);}}
$FOOT=[
  'nome'=>$nome_sistema??'Jacy Cabeleireiro',
  'endereco'=>$endereco??'',
  'texto_rodape'=>$texto_rodape??'',
  'instagram'=>$instagram_sistema??'',
  'wpp_raw'=>$tel_whatsapp??'45 99988-2100',
  'agenda_url'=>'/agendamentos',
  'logo_png'=>'/sistema/img/logo.png',
  'logo_webp'=>'/sistema/img/logo.webp',
];
try{
  if(isset($pdo) && $pdo instanceof PDO){
    $row=$pdo->query("SELECT nome, endereco, telefone_whatsapp, instagram, texto_rodape FROM config LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    if($row){
      if(!empty($row['nome']))$FOOT['nome']=$row['nome'];
      if(!empty($row['endereco']))$FOOT['endereco']=$row['endereco'];
      if(!empty($row['instagram']))$FOOT['instagram']=$row['instagram'];
      if(!empty($row['texto_rodape']))$FOOT['texto_rodape']=$row['texto_rodape'];
      if(!empty($row['telefone_whatsapp']))$FOOT['wpp_raw']=$row['telefone_whatsapp'];
    }
  }
}catch(Throwable $e){}
$wppDigits=digits($FOOT['wpp_raw']);
if($wppDigits!=='' && strpos($wppDigits,'55')!==0){$wppDigits='55'.$wppDigits;}
$WPP_LINK=$wppDigits?('https://wa.me/'.$wppDigits):'https://wa.me/5545999882100';
$webpFs=$_SERVER['DOCUMENT_ROOT'].$FOOT['logo_webp'];
$logoTag=(is_file($webpFs))?'<picture><source type="image/webp" srcset="'.h($FOOT['logo_webp']).'?v=1"><img src="'.h($FOOT['logo_png']).'?v=1" alt="Logo" width="46" height="22" style="height:auto"></picture>':'<img src="'.h($FOOT['logo_png']).'?v=1" alt="Logo" width="46" height="22" style="height:auto">';
?>

<footer style="background:#0b0b0b;color:#fff">
  <div class="container" style="max-width:1280px;margin:0 auto;padding:26px 16px">
    <div style="display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start">
      <div style="flex:1 1 300px;min-width:280px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px"><?=$logoTag?><h3 style="margin:0;font-size:22px;letter-spacing:.5px"><?=h($FOOT['nome'])?></h3></div>
        <?php if($FOOT['texto_rodape']!==''):?>
          <p style="margin:0;color:#d9d9d9;line-height:1.55;font-size:14px"><?=nl2br(h($FOOT['texto_rodape']))?></p>
        <?php endif;?>
      </div>
      <div style="flex:1 1 260px;min-width:240px">
        <h3 style="margin:0 0 10px 0;font-size:22px">Links contatos</h3>
        <ul style="list-style:none;padding:0;margin:0;color:#dcdcdc;font-size:15px;line-height:1.9">
          <?php if($FOOT['endereco']!==''):?><li>📍 <?=h($FOOT['endereco'])?></li><?php endif;?>
          <li>✍️ <a href="/assinatura" style="color:#fff;text-decoration:none">Assinatura</a></li>
          <li>✂️ <a href="#" style="color:#fff;text-decoration:none" target="_blank" rel="noopener">Serviços</a></li>
          <li>👔 <a href="#" style="color:#fff;text-decoration:none" target="_blank" rel="noopener">Barbeiro</a></li>
          <li>💇‍♀️ <a href="/protese" style="color:#fff;text-decoration:none" target="_blank" rel="noopener">Protese</a></li>
        </ul>
      </div>
      <div style="flex:1 1 320px;min-width:260px">
        <h3 style="margin:0 0 10px 0;font-size:22px">Cadastre-se</h3>
        <p style="margin:0 0 10px 0;color:#dcdcdc;font-size:14px">Solicite uma avaliação gratuita</p>
        <form id="form_cadastro" method="post" action="#" data-recaptcha-action="cadastro" style="max-width:340px">
          <input type="hidden" name="recaptcha_token" value="">
          <input type="hidden" name="recaptcha_action" value="cadastro">
          <div style="margin-bottom:10px"><input type="tel" name="telefone" id="telefone_rodape" placeholder="WhatsApp" style="width:100%;padding:12px 14px;border:0;background:#eaecef;border-radius:6px;font-size:15px"></div>
          <div style="margin-bottom:10px"><input type="text" name="nome" placeholder="Nome Completo" style="width:100%;padding:12px 14px;border:0;background:#eaecef;border-radius:6px;font-size:15px"></div>
          <button type="submit" style="width:100%;padding:12px 14px;border:0;border-radius:6px;background:#0d6efd;color:#fff;font-weight:700;cursor:pointer">Cadastrar</button>
        </form>
        <br><small><div id="mensagem-rodape"></div></small>
      </div>
    </div>
  </div>
  <div style="background:#1f2229;color:#e6e6e6">
    <div class="container" style="max-width:1280px;margin:0 auto;padding:10px 16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center">
      <small>© <?=date('Y')?> <?=h($FOOT['nome'])?></small>
      <a data-toggle="modal" data-target="#privacidade" href="#privacidade">Política de privacidade</a>
      <a data-toggle="modal" data-target="#modalTermos" href="#modalTermos">Termos de uso</a>
      <small style="margin-left:auto"><?=h($FOOT['nome'])?> <?=h($FOOT['wpp_raw'])?> | <a href="<?=h($WPP_LINK)?>" target="_blank" rel="noopener" style="color:#e6e6e6;text-decoration:none">📱 WhatsApp</a></small>
    </div>
  </div>
</footer>

<!-- ======= Modais ======= -->
<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="modalComentarioLabel" aria-hidden="true">
  <div class="modal-dialog" role="document" style="max-width:760px">
    <div class="modal-content" style="background:#0f0f10;color:#eaeef3;border:1px solid #2a2a2c;border-radius:12px;overflow:hidden">
      <div class="modal-header" style="border:0;padding:14px 16px">
        <h5 class="modal-title" id="modalComentarioLabel" style="margin:0;font-weight:800;letter-spacing:.3px">Inserir Depoimento</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar" style="border:0;background:transparent;color:#eaeef3;font-size:28px;line-height:1;opacity:.9;cursor:pointer"><span aria-hidden="true">&times;</span></button>
      </div>
      <form id="form">
        <div class="modal-body" style="padding:10px 16px 6px">
          <div style="margin-bottom:12px">
            <label for="nome_cliente" style="display:block;margin:0 0 6px;font-weight:700">Nome</label>
            <input type="text" id="nome_cliente" name="nome" placeholder="Seu nome completo" required class="form-control" style="width:100%;background:#eaecef;border:0;border-radius:8px;box-shadow:none;padding:12px 14px;outline:none;color:#111">
          </div>
          <div style="margin-bottom:16px">
            <label for="texto_cliente" style="display:block;margin:0 0 6px;font-weight:700">Texto <small style="opacity:.8;font-weight:400">(até 500 caracteres)</small></label>
            <textarea id="texto_cliente" name="texto" maxlength="500" rows="4" required placeholder="Seu depoimento" class="form-control" style="width:100%;background:#eaecef;border:0;border-radius:8px;box-shadow:none;padding:12px 14px;outline:none;color:#111"></textarea>
          </div>
          <div style="display:flex;align-items:flex-end;gap:16px;flex-wrap:wrap">
            <div style="flex:1 1 360px">
              <label for="foto" style="display:block;margin:0 0 6px;font-weight:700">Foto</label>
              <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png,.webp" class="form-control" onchange="carregarImg()" style="width:100%;background:#eaecef;border:0;border-radius:8px;box-shadow:none;padding:10px 12px;outline:none;color:#111">
              <div style="margin-top:6px;font-size:12px;opacity:.8">Opcional: JPG/PNG/WebP (até 2MB)</div>
            </div>
            <div id="divImg" style="flex:0 0 110px;margin-left:auto;display:grid;place-items:center">
              <img id="target" alt="Pré-visualização" src="sistema/painel/img/comentarios/sem-foto.jpg" width="96" height="96" style="display:block;width:96px;height:96px;object-fit:cover;border-radius:50%;border:2px solid #2a2a2c;background:#000">
            </div>
          </div>
          <input type="hidden" name="id" id="id">
          <input type="hidden" name="cliente" value="1">
          <div id="mensagem-comentario" style="margin-top:12px;text-align:center;font-size:13px;min-height:18px"></div>
        </div>
        <div class="modal-footer" style="border-top:1px solid #24262b;padding:12px 16px;display:flex;gap:10px;justify-content:flex-end">
          <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background:transparent;color:#eaeef3;border:1px solid #333;border-radius:10px;padding:10px 18px;font-weight:700;cursor:pointer">Cancelar</button>
          <button type="submit" class="btn btn-primary" style="background:#0d6efd;border:0;border-radius:10px;padding:10px 18px;font-weight:700;cursor:pointer">Inserir</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="empresa" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="">
        <div class="modal-header">
          <h1 class="modal-title"><small>JACY CABELEIREIRO</small></h1>
          <button type="submit" class="close" name="fecharModal">&times;</button>
        </div>
      </form>
      <div class="modal-body">
        <p class="text-muted"><small>Com mais de 25 anos de experiência, Jacy Cordeiro é mais que um cabeleireiro – é um estrategista de beleza!<br>🎨💈 Especializado em cortes modernos, barba de precisão e próteses capilares, Jacy trabalha para resgatar a autoestima e o bem-estar de cada cliente. Cada atendimento é personalizado para realçar sua melhor versão e garantir resultados que vão além do espelho.<br><br>💇‍♂️ Cabeleireiro & Barbeiro<br>✨ Especialista em Prótese Capilar<br>📆 Agendamentos Personalizados<br>🔥 Resultados que transformam!<br>💪 Se você busca excelência e inovação na beleza, marque um horário e viva a experiência!<br>Não se trata apenas de um corte. Trata-se de elevar sua confiança e potencializar sua beleza. Agende agora e experimente o toque de um verdadeiro profissional! 💪</small></p>
        <p class="text-muted"><small>Jacy Cordeiro - CNPJ: 38.896.614/0001-40.<br>Assista o vídeo a seguir.</small></p>
        <iframe width="100%" height="500" src="https://www.youtube.com/embed/fO_s7jlO1tU?si=n3Yntmowyfms6aFm&amp;controls=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe>
        <p class="text-muted" align="center"><small>"Motivação é aquilo que te faz começar. Habito é aquilo que te faz continuar."</small></p>
      </div>
    </div>
  </div>
</div>

<div id="modalTermos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalTermosLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" id="modalTermosLabel"><small>Termos de Uso</small></h1>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="col-lg-12"><span class="text-muted"><small><i>
          <br>
          <h2><span style="color:#444">1. Termos</span></h2>
          <p><span style="color:#444">Ao acessar ao site <a href="https://agenda.jacycabeleireiro.com/">Jacy Cabeleireiro</a>, concorda em cumprir estes termos de serviço, todas as leis e regulamentos aplicáveis ​​e concorda que é responsável pelo cumprimento de todas as leis locais aplicáveis. Se você não concordar com algum desses termos, está proibido de usar ou acessar este site. Os materiais contidos neste site são protegidos pelas leis de direitos autorais e marcas comerciais aplicáveis.</span></p>
          <h2><span style="color:#444">2. Uso de Licença</span></h2>
          <p><span style="color:#444">É concedida permissão para baixar temporariamente uma cópia dos materiais (informações ou software) no site Jacy Cabeleireiro , apenas para visualização transitória pessoal e não comercial. Esta é a concessão de uma licença, não uma transferência de título e, sob esta licença, você não pode:&nbsp;</span></p>
          <ol>
            <li><span style="color:#444">modificar ou copiar os materiais;&nbsp;</span></li>
            <li><span style="color:#444">usar os materiais para qualquer finalidade comercial ou para exibição pública (comercial ou não comercial);&nbsp;</span></li>
            <li><span style="color:#444">tentar descompilar ou fazer engenharia reversa de qualquer software contido no site Jacy Cabeleireiro;&nbsp;</span></li>
            <li><span style="color:#444">remover quaisquer direitos autorais ou outras notações de propriedade dos materiais; ou&nbsp;</span></li>
            <li><span style="color:#444">transferir os materiais para outra pessoa ou 'espelhe' os materiais em qualquer outro servidor.</span></li>
          </ol>
          <p><span style="color:#444">Esta licença será automaticamente rescindida se você violar alguma dessas restrições e poderá ser rescindida por Jacy Cabeleireiro a qualquer momento. Ao encerrar a visualização desses materiais ou após o término desta licença, você deve apagar todos os materiais baixados em sua posse, seja em formato eletrónico ou impresso.</span></p>
          <h2><span style="color:#444">3. Isenção de responsabilidade</span></h2>
          <ol>
            <li><span style="color:#444">Os materiais no site da Jacy Cabeleireiro são fornecidos 'como estão'. Jacy Cabeleireiro não oferece garantias, expressas ou implícitas, e, por este meio, isenta e nega todas as outras garantias, incluindo, sem limitação, garantias implícitas ou condições de comercialização, adequação a um fim específico ou não violação de propriedade intelectual ou outra violação de direitos.</span></li>
            <li><span style="color:#444">Além disso, o Jacy Cabeleireiro não garante ou faz qualquer representação relativa à precisão, aos resultados prováveis ​​ou à confiabilidade do uso dos materiais em seu site ou de outra forma relacionado a esses materiais ou em sites vinculados a este site.</span></li>
          </ol>
          <h2><span style="color:#444">4. Limitações</span></h2>
          <p><span style="color:#444">Em nenhum caso o Jacy Cabeleireiro ou seus fornecedores serão responsáveis ​​por quaisquer danos (incluindo, sem limitação, danos por perda de dados ou lucro ou devido a interrupção dos negócios) decorrentes do uso ou da incapacidade de usar os materiais em Jacy Cabeleireiro, mesmo que Jacy Cabeleireiro ou um representante autorizado da Jacy Cabeleireiro tenha sido notificado oralmente ou por escrito da possibilidade de tais danos. Como algumas jurisdições não permitem limitações em garantias implícitas, ou limitações de responsabilidade por danos consequentes ou incidentais, essas limitações podem não se aplicar a você.</span></p>
          <h2><span style="color:#444">5. Precisão dos materiais</span></h2>
          <p><span style="color:#444">Os materiais exibidos no site da Jacy Cabeleireiro podem incluir erros técnicos, tipográficos ou fotográficos. Jacy Cabeleireiro não garante que qualquer material em seu site seja preciso, completo ou atual. Jacy Cabeleireiro pode fazer alterações nos materiais contidos em seu site a qualquer momento, sem aviso prévio. No entanto, Jacy Cabeleireiro não se compromete a atualizar os materiais.</span></p>
          <h2><span style="color:#444">6. Links</span></h2>
          <p><span style="color:#444">O Jacy Cabeleireiro não analisou todos os sites vinculados ao seu site e não é responsável pelo conteúdo de nenhum site vinculado. A inclusão de qualquer link não implica endosso por Jacy Cabeleireiro do site. O uso de qualquer site vinculado é por conta e risco do usuário.</span></p>
          <h3><span style="color:#444">Modificações</span></h3>
          <p><span style="color:#444">O Jacy Cabeleireiro pode revisar estes termos de serviço do site a qualquer momento, sem aviso prévio. Ao usar este site, você concorda em ficar vinculado à versão atual desses termos de serviço.</span></p>
          <h3><span style="color:#444">Lei aplicável</span></h3>
          <p><span style="color:#444">Estes termos e condições são regidos e interpretados de acordo com as leis do Jacy Cabeleireiro e você se submete irrevogavelmente à jurisdição exclusiva dos tribunais naquele estado ou localidade.</span></p>
        </i></small></span></div>
      </div>
      <div class="modal-footer">
        <small><span class="text-muted ml-4">WhatsApp <a class="text-muted" href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo $tel_whatsapp ?>" target="_blank" rel="noopener"><?php echo $whatsapp_sistema ?></a></span></small>
      </div>
    </div>
  </div>
</div>

<div id="privacidade" class="modal fade" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="background:#0f0f10;color:#e6e6e6">
      <div class="modal-header" style="border-bottom:1px solid #2a2a2c">
        <h5 class="modal-title" style="color:#fff;margin:0">Política de privacidade</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar" style="color:#e6e6e6;opacity:1"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" style="line-height:1.6">
        <p>A sua privacidade é importante para nós. É política do <strong>Jacy Cabeleireiro</strong> respeitar a sua privacidade em relação a qualquer informação sua que possamos coletar no site <a href="https://agenda.jacycabeleireiro.com/" target="_blank" rel="noopener" style="color:#e6e6e6;text-decoration:underline">Jacy Cabeleireiro</a> e outros sites que possuímos e operamos.</p>
        <p>Solicitamos informações pessoais apenas quando realmente precisamos delas para lhe fornecer um serviço. Fazemos isso por meios justos e legais, com o seu conhecimento e consentimento. Também informamos por que estamos coletando e como será usado.</p>
        <p>Apenas retemos as informações coletadas pelo tempo necessário para fornecer o serviço solicitado. Quando armazenamos dados, protegemos dentro de meios comercialmente aceitáveis para evitar perdas, roubos, acesso, divulgação, cópia, uso ou modificação não autorizados.</p>
        <p>Não compartilhamos informações de identificação pessoal publicamente ou com terceiros, exceto quando exigido por lei.</p>
        <p>Nosso site pode conter links para sites externos que não são operados por nós. Não temos controle sobre o conteúdo e práticas desses sites e não podemos aceitar responsabilidade por suas respectivas <a href="https://politicaprivacidade.com/" target="_blank" rel="noopener" style="color:#e6e6e6;text-decoration:underline">políticas de privacidade</a>.</p>
        <p>Você é livre para recusar nossa solicitação de informações pessoais, entendendo que talvez não possamos fornecer alguns dos serviços desejados.</p>
        <p>O uso continuado de nosso site será considerado como aceitação de nossas práticas em torno de privacidade e informações pessoais. Se você tiver alguma dúvida sobre como lidamos com dados do usuário e informações pessoais, entre em contato conosco.</p>
        <h6 style="color:#fff;margin-top:18px">Cookies e publicidade</h6>
        <ul style="padding-left:18px;margin:0">
          <li>Podemos usar o serviço Google AdSense, que utiliza o cookie DoubleClick para veicular anúncios mais relevantes e limitar o número de vezes que um anúncio é exibido.</li>
          <li>Usamos anúncios para compensar custos de operação do site e financiar melhorias.</li>
          <li>Parceiros afiliados podem usar cookies de rastreamento para contabilizar indicações.</li>
        </ul>
        <h6 style="color:#fff;margin-top:18px">Compromisso do usuário</h6>
        <ul style="padding-left:18px;margin:0">
          <li>Não se envolver em atividades ilegais ou contrárias à boa-fé e à ordem pública.</li>
          <li>Não difundir conteúdo discriminatório, ilegal, violento ou que viole direitos humanos.</li>
          <li>Não causar danos aos sistemas do site, nem introduzir vírus ou softwares maliciosos.</li>
        </ul>
        <h6 style="color:#fff;margin-top:18px">Mais informações</h6>
        <p>Se restarem dúvidas, é geralmente mais seguro manter os cookies ativados caso interaja com algum recurso que você usa em nosso site.</p>
        <p style="opacity:.85">Esta política é efetiva a partir de <strong>28/10/2024 14:33</strong>.</p>
        <p style="opacity:.85">reCAPTCHA v3 aplicado para proteção de formulários.</p>
      </div>
      <div class="modal-footer" style="border-top:1px solid #2a2a2c">
        <small>WhatsApp: <a href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo $tel_whatsapp ?>" target="_blank" rel="noopener" style="color:#e6e6e6;text-decoration:underline"><?php echo $whatsapp_sistema ?></a></small>
      </div>
    </div>
  </div>
</div>

<div id="ytModal" style="position:fixed;inset:0;background:rgba(0,0,0,.7);display:none;align-items:center;justify-content:center;z-index:99999">
  <div style="position:relative;width:96vw;max-width:980px;background:#000;border-radius:14px;overflow:hidden">
    <button id="ytClose" aria-label="Fechar" style="position:absolute;top:10px;right:10px;border:0;background:rgba(255,255,255,.15);color:#fff;padding:6px 10px;border-radius:6px;cursor:pointer;z-index:1">✕</button>
    <div style="position:relative;width:100%;padding-top:56.25%"><div id="ytPlayer" style="position:absolute;inset:0"></div></div>
  </div>
</div>

<!-- reCAPTCHA carregado sob demanda -->
<script defer>
if(window.RECAPTCHA_SITE_KEY){
  const s=document.createElement('script');
  s.src='https://www.google.com/recaptcha/api.js?render='+window.RECAPTCHA_SITE_KEY;
  s.async=true;
  document.head.appendChild(s);
}
</script>

<!-- ======= Scripts principais (não-blocking / sem alterar lógica) ======= -->
<script>
(function(){
  document.querySelectorAll('[data-toggle="modal"]').forEach(function(a){
    a.addEventListener('click',function(e){
      e.preventDefault();
      var sel=a.getAttribute('data-target')||a.getAttribute('href');
      if(!sel)return;
      if(window.jQuery && typeof jQuery.fn.modal==='function'){
        jQuery(sel).modal('show');
        return;
      }
      var m=document.querySelector(sel);
      if(!m)return;
      m.classList.add('show');
      document.body.style.overflow='hidden';
      if(history.replaceState)history.replaceState(null,'',location.pathname+location.search);
    },{passive:false});
  });
  function closeFallback(m){m.classList.remove('show');document.body.style.overflow='';}
  document.addEventListener('click',function(ev){
    var btn=ev.target.closest('[data-dismiss="modal"], .close');
    if(btn){var m=btn.closest('.modal');if(m)closeFallback(m);}
    var modal=ev.target.closest('.modal');
    if(modal && ev.target===modal)closeFallback(modal);
  });
  document.addEventListener('keydown',function(ev){
    if(ev.key==='Escape'){
      document.querySelectorAll('.modal.show').forEach(closeFallback);
    }
  });
})();

const LINK_WHATSAPP='https://wa.me/5545999882100';
const LINK_APP='https://jacycabeleireiro.com/agendamentos';
var btnW=document.getElementById('ctaWhats');if(btnW)btnW.href=LINK_WHATSAPP;
var btnA=document.getElementById('ctaApp');if(btnA)btnA.href=LINK_APP;

const track=document.getElementById('heroTrack');
const slidesV=track?Array.from(track.querySelectorAll('.hero-slide')):[];
const dotsV=Array.from(document.querySelectorAll('#heroDots button'));
let heroIndex=0;
function heroGo(i){
  if(!track)return;
  heroIndex=(i+slidesV.length)%slidesV.length;
  track.style.transform='translateX('+(heroIndex*-100)+'%)';
  dotsV.forEach((d,idx)=>d.style.background=idx===heroIndex?'#ffffffaa':'#ffffff55');
}
var prevH=document.getElementById('heroPrev');if(prevH)prevH.onclick=()=>heroGo(heroIndex-1);
var nextH=document.getElementById('heroNext');if(nextH)nextH.onclick=()=>heroGo(heroIndex+1);
dotsV.forEach(d=>d.onclick=()=>heroGo(parseInt(d.dataset.i,10)));
let heroTimer=setInterval(()=>heroGo(heroIndex+1),4000);

let __ytApiRequested=false;
function ensureYTApiLoaded(cb){
  if(window.YT && window.YT.Player){cb();return;}
  if(!__ytApiRequested){
    __ytApiRequested=true;
    var s=document.createElement('script');
    s.src='https://www.youtube.com/iframe_api';
    s.async=true;
    document.head.appendChild(s);
  }
  (function wait(){
    if(window.YT && window.YT.Player)cb();
    else setTimeout(wait,80);
  })();
}

let ytPlayer;
const ytModal=document.getElementById('ytModal');
const ytClose=document.getElementById('ytClose');
function onYouTubeIframeAPIReady(){}

function openVideo(videoId){
  if(!ytModal)return;
  ytModal.style.display='flex';
  document.body.style.overflow='hidden';
  clearInterval(heroTimer);
  ensureYTApiLoaded(function(){
    if(!ytPlayer){
      ytPlayer=new YT.Player('ytPlayer',{
        videoId:videoId,
        playerVars:{autoplay:1,rel:0,controls:1,modestbranding:1,playsinline:1},
        events:{
          onReady:(e)=>{const ifr=e.target.getIframe();ifr.style.position='absolute';ifr.style.inset='0';ifr.style.width='100%';ifr.style.height='100%';},
          onStateChange:(e)=>{if(e.data===YT.PlayerState.ENDED)closeVideo();}
        }
      });
    }else{
      ytPlayer.loadVideoById(videoId);
    }
  });
}
function closeVideo(){
  if(!ytModal)return;
  ytModal.style.display='none';
  document.body.style.overflow='';
  if(ytPlayer && ytPlayer.stopVideo)ytPlayer.stopVideo();
  heroTimer=setInterval(()=>heroGo(heroIndex+1),4000);
}
if(ytClose)ytClose.onclick=closeVideo;
if(ytModal)ytModal.addEventListener('click',(ev)=>{if(ev.target===ytModal)closeVideo();});
document.addEventListener('keydown',(e)=>{if(e.key==='Escape')closeVideo();});
slidesV.forEach(slide=>{
  const id=slide.dataset.video;
  var p=slide.querySelector('.play-btn');
  if(p)p.addEventListener('click',()=>openVideo(id));
  slide.addEventListener('dblclick',()=>openVideo(id));
});

(function(){
  const root=document.getElementById('hero');if(!root)return;
  const slides=root.querySelector('#slides');
  const items=Array.from(root.querySelectorAll('.slide'));
  const dotsEl=root.querySelector('#dots');
  const btnPrev=root.querySelector('#prev');
  const btnNext=root.querySelector('#next');
  let idx=0,timer=null,hover=false;
  const duration=5000;
  const reduced=window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  items.forEach((_,i)=>{
    const b=document.createElement('button');
    b.type='button';b.setAttribute('aria-label','Ir para o slide '+(i+1));
    b.addEventListener('click',()=>go(i),{passive:true});
    dotsEl.appendChild(b);
  });
  function mark(){dotsEl.querySelectorAll('button').forEach((b,i)=>b.classList.toggle('active',i===idx));}
  function go(i){
    idx=(i+items.length)%items.length;
    slides.style.transform='translateX('+(-100*idx)+'%)';
    mark();
  }
  function next(){go(idx+1);}
  function prev(){go(idx-1);}
  function loop(){clearTimeout(timer);if(hover||reduced)return;timer=setTimeout(()=>{next();loop();},duration);}
  if(btnPrev)btnPrev.addEventListener('click',prev,{passive:true});
  if(btnNext)btnNext.addEventListener('click',next,{passive:true});
  root.addEventListener('pointerenter',()=>{hover=true;clearTimeout(timer);},{passive:true});
  root.addEventListener('pointerleave',()=>{hover=false;loop();},{passive:true});
  let sx=0,dx=0;
  root.addEventListener('touchstart',e=>{sx=e.touches[0].clientX;},{passive:true});
  root.addEventListener('touchmove',e=>{dx=e.touches[0].clientX-sx;},{passive:true});
  root.addEventListener('touchend',()=>{if(Math.abs(dx)>40){dx<0?next():prev();}dx=0;},{passive:true});
  const boot=()=>{go(0);loop();};
  if('requestIdleCallback' in window)requestIdleCallback(boot);else window.addEventListener('load',boot,{once:true});
})();

(function(){
  const wrap=document.getElementById('galeriaWrap');if(!wrap)return;
  const track=document.getElementById('galTrack');
  const items=Array.from(track.querySelectorAll('.galItem'));
  const prev=document.getElementById('galPrev');
  const next=document.getElementById('galNext');
  let idx=0;
  function perView(){
    const w=window.innerWidth;
    if(w<520)return 1;
    if(w<768)return 2;
    if(w<1024)return 3;
    return 4;
  }
  function applyLayout(){
    const pv=perView();
    const w=(100/pv)+'%';
    items.forEach(el=>{el.style.minWidth=w;});
    clamp();go(idx,true);
  }
  function clamp(){
    const pv=perView();
    const max=Math.max(0,items.length-pv);
    if(idx>max)idx=max;
    if(idx<0)idx=0;
    if(prev)prev.disabled=(idx===0);
    if(next)next.disabled=(idx===max);
  }
  function go(i,noAnim){
    idx=i;clamp();
    if(noAnim)track.style.transition='none';
    const pv=perView();
    const shift=(100/pv)*idx;
    track.style.transform='translateX('+(-shift)+'%)';
    if(noAnim)requestAnimationFrame(()=>track.style.transition='.4s ease');
  }
  if(prev)prev.addEventListener('click',()=>go(idx-1));
  if(next)next.addEventListener('click',()=>go(idx+1));
  let sx=0,dx=0;
  track.addEventListener('touchstart',e=>{sx=e.touches[0].clientX;},{passive:true});
  track.addEventListener('touchmove',e=>{dx=e.touches[0].clientX-sx;},{passive:true});
  track.addEventListener('touchend',()=>{if(Math.abs(dx)>40){dx<0?go(idx+1):go(idx-1);}dx=0;},{passive:true});
  window.addEventListener('resize',applyLayout);
  applyLayout();
  let autoTimer=setInterval(()=>{
    const pv=perView();
    const max=Math.max(0,items.length-pv);
    if(idx>=max)go(0);else go(idx+1);
  },4000);
  if(wrap){
    wrap.addEventListener('pointerenter',()=>{clearInterval(autoTimer);},{passive:true});
    wrap.addEventListener('pointerleave',()=>{
      autoTimer=setInterval(()=>{
        const pv=perView();
        const max=Math.max(0,items.length-pv);
        if(idx>=max)go(0);else go(idx+1);
      },4000);
    },{passive:true});
  }
})();

(function(){
  var track=document.getElementById('depTrack');
  if(!track)return;
  var items=Array.from(track.querySelectorAll('.depItem'));
  var prev=document.getElementById('depPrev');
  var next=document.getElementById('depNext');
  var dotsEl=document.getElementById('depDots');
  function perView(){
    return(window.innerWidth>=900)?2:1;
  }
  var dots=[];
  function layout(){
    var pv=perView();
    var w=(100/pv)+'%';
    items.forEach(function(el){el.style.minWidth=w;});
    dotsEl.innerHTML='';
    dots=[];
    var pages=Math.max(1,Math.ceil(items.length/pv));
    for(var i=0;i<pages;i++){
      var b=document.createElement('button');
      b.type='button';
      b.style.cssText='width:10px;height:10px;border-radius:999px;border:0;background:#3a3c44;cursor:pointer;';
      (function(i){b.addEventListener('click',function(){go(i);});})(i);
      dotsEl.appendChild(b);
      dots.push(b);
    }
    clamp();go(index,true);
  }
  var index=0;
  function clamp(){
    var pv=perView();
    var pages=Math.max(1,Math.ceil(items.length/pv));
    if(index<0)index=0;
    if(index>pages-1)index=pages-1;
    dots.forEach(function(d,i){d.style.background=(i===index?'#ffffff':'#3a3c44');});
    if(prev)prev.disabled=(index===0);
    if(next)next.disabled=(index===pages-1);
  }
  function go(i,noAnim){
    index=i;clamp();
    if(noAnim)track.style.transition='none';
    var shift=(100*index);
    track.style.transform='translateX('+(-shift)+'%)';
    if(noAnim)requestAnimationFrame(function(){track.style.transition='.45s ease';});
  }
  if(prev)prev.addEventListener('click',function(){go(index-1);});
  if(next)next.addEventListener('click',function(){go(index+1);});
  var sx=0,dx=0;
  track.addEventListener('touchstart',function(e){sx=e.touches[0].clientX;},{passive:true});
  track.addEventListener('touchmove',function(e){dx=e.touches[0].clientX-sx;},{passive:true});
  track.addEventListener('touchend',function(){
    if(Math.abs(dx)>40){dx<0?go(index+1):go(index-1);}
    dx=0;
  },{passive:true});
  window.addEventListener('resize',layout,{passive:true});
  layout();
  var t=setInterval(function(){
    var pv=perView();
    var pages=Math.max(1,Math.ceil(items.length/pv));
    go((index+1)%pages);
  },6000);
})();

(function(){
  var h=document.getElementById('siteHeader');
  function pad(){if(!h)return;document.body.style.paddingTop=h.getBoundingClientRect().height+'px';}
  pad();window.addEventListener('resize',pad,{passive:true});
  document.addEventListener('click',function(ev){
    document.querySelectorAll('header details[open]').forEach(function(d){
      if(!d.contains(ev.target))d.removeAttribute('open');
    });
  },{passive:true});
})();

function carregarImg(){
  var input=document.getElementById('foto');
  var img=document.getElementById('target');
  var msg=document.getElementById('mensagem-comentario');
  if(!input||!input.files||!input.files[0])return;
  var file=input.files[0];
  var okTypes=['image/jpeg','image/jpg','image/png','image/webp'];
  if(okTypes.indexOf(file.type)===-1){
    msg.textContent='Arquivo inválido. Use JPG, PNG ou WebP.';
    msg.style.color='#ff8080';
    input.value='';
    img.src='sistema/painel/img/comentarios/sem-foto.jpg';
    return;
  }
  if(file.size>2*1024*1024){
    msg.textContent='Arquivo muito grande (máx. 2MB).';
    msg.style.color='#ff8080';
    input.value='';
    img.src='sistema/painel/img/comentarios/sem-foto.jpg';
    return;
  }
  var reader=new FileReader();
  reader.onload=function(e){
    img.src=e.target.result;
    msg.textContent='';
  };
  reader.readAsDataURL(file);
}
</script>

<script>
(function(){
  var ENDPOINT='/cadastrar.php';
  var tel=document.getElementById('telefone_rodape');
  if(tel){
    tel.addEventListener('input',function(e){
      var d=e.target.value.replace(/\D/g,'').slice(0,11);
      var out=d;
      if(d.length>2)out='('+d.slice(0,2)+') '+d.slice(2);
      if(d.length>=11)out='('+d.slice(0,2)+') '+d.slice(2,7)+'-'+d.slice(7);
      else if(d.length>=7)out='('+d.slice(0,2)+') '+d.slice(2,6)+'-'+d.slice(6);
      e.target.value=out;
    });
  }
  var form=document.getElementById('form_cadastro');
  if(!form)return;
  form.addEventListener('submit',function(e){
    e.preventDefault();
    var action=(form.getAttribute('data-recaptcha-action')||'cadastro').trim();
    var tokEl=form.querySelector('input[name="recaptcha_token"]');
    var actEl=form.querySelector('input[name="recaptcha_action"]');
    if(actEl)actEl.value=action;
    function prosseguirEnvio(){
      var nome=(form.querySelector('[name="nome"]').value||'').trim();
      var fone=(form.querySelector('[name="telefone"]').value||'').replace(/\D/g,'');
      if(!nome || fone.length<10){
        if(window.Swal)Swal.fire({icon:'error',title:'❌ Dados inválidos',text:'Preencha corretamente nome e telefone.',confirmButtonColor:'#DAA520'});
        else alert('Preencha corretamente nome e telefone.');
        return;
      }
      var xhr=new XMLHttpRequest();
      xhr.open('POST',ENDPOINT,true);
      xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
      xhr.onreadystatechange=function(){
        if(xhr.readyState!==4)return;
        var ok=(xhr.status>=200 && xhr.status<300);
        var resp=(xhr.responseText||'').replace(/\s+/g,' ').trim();
        if(!ok){
          if(window.Swal)Swal.fire({icon:'error',title:'❌ Erro ao enviar',text:'HTTP '+xhr.status,confirmButtonColor:'#DAA520'});
          else alert('Erro HTTP '+xhr.status);
          return;
        }
        if(resp.indexOf('Cadastrado com Sucesso')===0){
          if(window.Swal)Swal.fire({icon:'success',title:'✅ Sucesso!',text:'Cadastro realizado com sucesso.',confirmButtonColor:'#DAA520'})
            .then(function(){window.location.href='/sistema/acesso';});
          else{alert('Cadastro realizado com sucesso.');window.location.href='/sistema/acesso';}
        }else if(resp.indexOf('Você já está Cadastrado')===0){
          if(window.Swal)Swal.fire({icon:'warning',title:'⚠️ Já cadastrado',text:'Você já tem cadastro.',confirmButtonColor:'#DAA520'})
            .then(function(){window.location.href='/sistema/acesso';});
          else{alert('Você já tem cadastro.');window.location.href='/sistema/acesso';}
        }else if(resp.indexOf('Preencha nome e telefone')===0){
          if(window.Swal)Swal.fire({icon:'error',title:'❌ Dados inválidos',text:'Preencha corretamente nome e telefone.',confirmButtonColor:'#DAA520'});
          else alert('Preencha corretamente nome e telefone.');
        }else{
          if(window.Swal)Swal.fire({icon:'error',title:'❌ Erro inesperado',html:'Retorno:<br><pre style="white-space:pre-wrap">'+resp+'</pre>',confirmButtonColor:'#DAA520'});
          else alert('Erro: '+resp);
        }
      };
      xhr.onerror=function(){
        if(window.Swal)Swal.fire({icon:'error',title:'❌ Falha de rede',text:'Não foi possível enviar os dados.',confirmButtonColor:'#DAA520'});
        else alert('Falha de rede.');
      };
      xhr.send(new FormData(form));
    }
    if(window.grecaptcha && window.RECAPTCHA_SITE_KEY){
      grecaptcha.ready(function(){
        grecaptcha.execute(window.RECAPTCHA_SITE_KEY,{action:action}).then(function(token){
          if(tokEl)tokEl.value=token;
          prosseguirEnvio();
        }).catch(function(){
          prosseguirEnvio();
        });
      });
    }else{
      prosseguirEnvio();
    }
  });
})();
</script>

<!-- ======= Structured Data ======= -->
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"HairSalon",
  "name":<?=json_encode($SET['nome'],JSON_UNESCAPED_UNICODE)?>,
  "url":<?=json_encode($SET['site'],JSON_UNESCAPED_SLASHES)?>,
  "description":<?=json_encode($SET['descricao'],JSON_UNESCAPED_UNICODE)?>,
  "telephone":<?=json_encode($SET['telefone'],JSON_UNESCAPED_UNICODE)?>,
  "address":{"@type":"PostalAddress","streetAddress":<?=json_encode($SET['endereco'],JSON_UNESCAPED_UNICODE)?>},
  "image":<?=json_encode($SET['logo_png'],JSON_UNESCAPED_SLASHES)?>
}
</script>

<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"HairSalon",
  "name":<?=json_encode($SET['nome'],JSON_UNESCAPED_UNICODE)?>,
  "url":<?=json_encode(rtrim($SET['site'],'/').'/',JSON_UNESCAPED_SLASHES)?>,
  "description":<?=json_encode($SET['descricao'],JSON_UNESCAPED_UNICODE)?>,
  "telephone":<?=json_encode($SET['telefone'],JSON_UNESCAPED_UNICODE)?>,
  "image":<?=json_encode($SET['logo_png'],JSON_UNESCAPED_SLASHES)?>,
  "address":{
    "@type":"PostalAddress",
    "streetAddress":"Rua Rio Grande do Sul, 2151",
    "addressLocality":"Cascavel",
    "addressRegion":"PR",
    "postalCode":"85801-050",
    "addressCountry":"BR"
  },
  "geo":{
    "@type":"GeoCoordinates",
    "latitude":-24.957842,
    "longitude":-53.458627
  },
  "areaServed":"Cascavel e região oeste do Paraná",
  "priceRange":"R$R$",
  "openingHoursSpecification":[
    {"@type":"OpeningHoursSpecification","dayOfWeek":["Monday","Tuesday","Wednesday","Thursday","Friday"],"opens":"09:00","closes":"19:00"},
    {"@type":"OpeningHoursSpecification","dayOfWeek":"Saturday","opens":"09:00","closes":"17:00"}
  ],
  "sameAs":[
    <?=json_encode($instagram_sistema??"https://www.instagram.com/jacycabeleireiro",JSON_UNESCAPED_SLASHES)?>,
    "https://maps.app.goo.gl/PGDcFaCRaq2pmMVa9"
  ]
}
</script>

<!-- ======= CONTADOR 1999 ======= -->
<script>
document.addEventListener('DOMContentLoaded', function () {
  const el = document.querySelector('.counter-1999');
  const sec = document.querySelector('.experience-section');
  if (!el) return;

  fetch('/contador-visitas.php', { cache: 'no-store' })
    .then(r => r.ok ? r.json() : Promise.reject(r.status))
    .then(data => {
      const total = Number(data && data.visitas) || 0;
      const finalFmt = (data && data.formatado) ? String(data.formatado) : total.toLocaleString('pt-BR');
      startCounter(total, finalFmt);
    })
    .catch(() => {
      startCounter(5280, (5280).toLocaleString('pt-BR'));
    });

  function startCounter(target, finalFmt) {
    el.textContent = '0';
    el.setAttribute('data-target', String(target));
    const run = () => animateTo(target, finalFmt);

    if ('IntersectionObserver' in window && sec) {
      const io = new IntersectionObserver((entries) => {
        if (entries.some(e => e.isIntersecting) && !el.classList.contains('animated')) {
          el.classList.add('animated');
          run();
          io.disconnect();
        }
      }, { threshold: 0.3 });
      io.observe(sec);
    } else {
      run();
    }
  }

  function animateTo(target, finalFmt) {
    if (!target || target <= 0) { el.textContent = '0'; return; }
    let current = 0;
    const duration = 2400;
    const steps = 100;
    const increment = Math.max(1, Math.ceil(target / steps));
    const tick = Math.max(16, Math.floor(duration / Math.ceil(target / increment)));

    const t = setInterval(() => {
      current += increment;
      if (current >= target) {
        clearInterval(t);
        el.textContent = finalFmt;
        return;
      }
      el.textContent = current.toLocaleString('pt-BR');
    }, tick);
  }
});
</script>

<!-- Scripts locais/terceiros com defer -->
<script src="sistema/painel/js/mascaras.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

</body>
</html>

