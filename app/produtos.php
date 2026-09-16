<?php
@session_start();

/* =========================================================
   HEADERS (precisam vir ANTES do output)
   ========================================================= */
if (!headers_sent()) {
  header('Content-Type: text/html; charset=UTF-8');
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: SAMEORIGIN');
  header('Referrer-Policy: strict-origin-when-cross-origin');
  header("Permissions-Policy: geolocation=(), camera=(), microphone=(), payment=()");
  header("Content-Security-Policy: default-src 'self'; img-src 'self' data: https:; media-src 'self'; frame-src 'self' https://www.youtube-nocookie.com https://player.vimeo.com; script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline'; connect-src 'self'; font-src 'self' data:; frame-ancestors 'self';");
}

require_once("cabecalho.php"); // mantém teu header/tema

/* =========================================================
   Helpers
   ========================================================= */
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

/* =========================================================
   Paginação (mesma lógica, com cast seguro)
   ========================================================= */
$itens_por_pagina = 12;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$inicio = ($pagina - 1) * $itens_por_pagina;

/* =========================================================
   Consultas (mesmas, apenas saída higienizada)
   ========================================================= */
$query = $pdo->query("SELECT * FROM produtos WHERE estoque > 0 AND valor_venda > 0 ORDER BY id DESC LIMIT $inicio, $itens_por_pagina");
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$query_total = $pdo->query("SELECT COUNT(*) as total FROM produtos WHERE estoque > 0 AND valor_venda > 0");
$total_reg = (int)($query_total->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
$total_paginas = (int)ceil($total_reg / $itens_por_pagina);
?>

<style>
  /* tema escuro padrão */
  .sub_page .hero_area { min-height: auto; }

  body, .product_section { background:#0b0b0d; color:#fff; }

  .product_section .heading_container h2,
  .product_section .heading_container p { color:#fff; }

  .product_section .row { background:transparent !important; }

  /* card do produto */
  .box{
    background:transparent;
    border:1px solid rgba(255,255,255,.12);
    border-radius:14px;
    overflow:hidden;
    transition:transform .15s ease, border-color .15s ease, box-shadow .15s ease;
  }
  .box:hover{
    transform:translateY(-2px);
    border-color:rgba(255,255,255,.25);
    box-shadow:0 10px 30px rgba(0,0,0,.35);
  }

  .img-box {
    width: 100%;
    height: 200px;
    overflow: hidden;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color:#000;             /* escuro */
    border:1px solid rgba(255,255,255,.08);
    border-radius: 10px;
  }
  .img-box img {
    height: 100%;
    width: auto;
    object-fit: contain;
    display: block;
  }

  .detail-box { padding:12px; text-align:center; }
  .detail-box h5 { color:#fff; margin:0 0 6px; }
  .price { color:#fff; font-weight:700; }

  /* botão comprar (mantém classe existente, só estiliza no dark) */
  .btn.btn-warning.btn-sm{
    background:transparent;
    border:1px solid rgba(255,255,255,.35);
    color:#fff;
    font-weight:700;
    border-radius:10px;
  }
  .btn.btn-warning.btn-sm:hover{
    border-color:#fff;
    color:#fff;
  }

  /* menu dropdown dark */
  .dropdown-menu { background-color: #111 !important; border: none; }
  .dropdown-menu a { color: #fff !important; }
  .dropdown-menu a:hover { background-color: #333 !important; color: #ffd700 !important; }

  /* paginação dark */
  .pagination .page-link{
    background:transparent;
    border:1px solid rgba(255,255,255,.2);
    color:#fff;
  }
  .pagination .page-link:hover{
    background:rgba(255,255,255,.06);
    color:#fff;
    border-color:rgba(255,255,255,.35);
  }
  .pagination .page-item.active .page-link{
    background:#fff;
    color:#000;
    border-color:#fff;
  }
</style>

</div>

<!-- product section -->
<section class="product_section layout_padding">
  <div class="container-fluid">
    <div class="heading_container heading_center">
      <h2>Nossos Produtos</h2>
      <p class="col-lg-8 px-0 mx-auto text-center">
        Confira alguns de nossos produtos, damos desconto caso compre em grande quantidade.
      </p>
    </div>

    <?php if($total_reg > 0): ?>
    <div class="row px-3 pb-5">
      <?php foreach ($res as $idx => $prod):
        $id         = (int)($prod['id'] ?? 0);
        $nome       = (string)($prod['nome'] ?? '');
        $valorNum   = (float)($prod['valor_venda'] ?? 0);
        $valor      = number_format($valorNum, 2, ',', '.');
        $foto       = (string)($prod['foto'] ?? '');
        $descricao  = (string)($prod['descricao'] ?? '');
        $nomeF      = mb_strimwidth($nome, 0, 30, "...", 'UTF-8');

        // caminho imagem + fallback webp (mantém a tua lógica)
        $path = "sistema/painel/img/produtos/" . basename($foto);
        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
        $src  = (is_string($webp) && file_exists($webp)) ? $webp : $path;

        // primeira imagem com fetchpriority; demais lazy
        $imgAttrs = ($idx === 0) ? 'fetchpriority="high" decoding="async"' : 'loading="lazy" decoding="async"';

        // WhatsApp (mantida a lógica, apenas com encode de texto)
        $zapPhone = h($tel_whatsapp ?? '');
        $msg = rawurlencode("Olá, gostaria de saber mais sobre o produto {$nome}");
        $zapHref = "https://api.whatsapp.com/send?1=pt_BR&phone={$zapPhone}&text={$msg}";
      ?>
        <div class="col-sm-6 col-md-3 mb-4">
          <div class="box h-100 d-flex flex-column">
            <div class="img-box mb-3">
              <img
                src="<?= h($src) ?>"
                alt="<?= h($nome) ?>"
                title="<?= h($descricao) ?>"
                width="480" height="320" <?= $imgAttrs ?> class="img-fluid">
            </div>
            <div class="detail-box text-center">
              <h5><?= h($nomeF) ?></h5>
              <h6 class="price">R$ <?= h($valor) ?></h6>
              <a target="_blank" rel="noopener noreferrer"
                 href="<?= $zapHref ?>"
                 class="btn btn-warning btn-sm mt-2">
                Comprar Agora
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-center mt-4">
      <nav aria-label="Paginação de produtos">
        <ul class="pagination">
          <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= ($i === $pagina) ? 'active' : '' ?>">
              <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
    <?php endif; ?>
  </div>
</section>
<!-- product section ends -->

<?php require_once("rodape.php"); ?>
