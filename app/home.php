<?php
@session_start();
require_once("cabecalho.php");

// Redirecionos conforme sessão (mesma lógica)
if (!isset($_SESSION['usuario_logado_pagina'])) {
    if ($entrada == 'Login') {
        echo '<script>window.location="sistema/acesso"</script>';
    }
    if ($entrada == 'Agendamento') {
        echo '<script>window.location="agendamento"</script>';
    }
}
?>

<?php
$query = $pdo->query("SELECT * FROM textos_index ORDER BY id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (count($res) > 0) {
?>
<section class="slider_section layout_padding-bottom">
    <div id="customCarousel1" class="carousel slide" data-interval="5000">
        <div class="carousel-inner">
            <?php
            foreach ($res as $i => $item) {
                $ativo = ($i == 0) ? 'active' : '';
            ?>
            <div class="carousel-item <?php echo $ativo; ?>">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-box">
                                <h1><?php echo htmlspecialchars($item['titulo']); ?></h1>
                                <p><?php echo htmlspecialchars($item['descricao']); ?></p>
                                <div class="btn-box">
                                    <a href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo $tel_whatsapp; ?>" target="_blank" rel="noopener" class="btn btn-lg" style="background-color:#007bff; color:white; font-size:1.6rem; padding:12px 34px; border-radius:50px; text-decoration:none; display:inline-block; font-weight:bold; box-shadow:0px 4px 8px rgba(0,0,0,0.2);" onmouseover="this.style.backgroundColor='#0056b3';" onmouseout="this.style.backgroundColor='#007bff';">
                                        Contate-nos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="container">
            <div class="carousel_btn-box">
                <a class="carousel-control-prev" href="#customCarousel1" role="button" data-slide="prev" aria-label="Anterior">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i><span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#customCarousel1" role="button" data-slide="next" aria-label="Próximo">
                    <i class="fa fa-arrow-right" aria-hidden="true"></i><span class="sr-only">Next</span>
                </a>
            </div>
        </div>
    </div>
</section>
<?php } ?>
</div>

<section class="product_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>Nossos Serviços</h2>
            <p class="col-lg-8 px-0">
                <?php
                $query_cat = $pdo->query("SELECT * FROM cat_servicos ORDER BY id ASC");
                $categorias = $query_cat->fetchAll(PDO::FETCH_ASSOC);
                $nomes_categorias = [];
                foreach ($categorias as $cat) {
                    $nomes_categorias[] = $cat['nome'];
                }
                echo implode(' / ', $nomes_categorias);
                ?>
            </p>
        </div>

        <?php
        $query = $pdo->query("SELECT * FROM servicos where ativo = 'Sim' ORDER BY id asc");
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        if (count($res) > 0) {
        ?>
        <div class="product_container">
            <div class="product_owl-carousel owl-carousel owl-theme">
                <?php
                foreach ($res as $i => $item) {
                    $valorF = number_format($item['valor'], 2, ',', '.');
                    $nomeF  = mb_strimwidth($item['nome'], 0, 20, "...");
                ?>
                <div class="item">
                    <div class="box">
                        <div class="img-box">
                            <?php
                            $path = "sistema/painel/img/servicos/" . $item['foto'];
                            $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
                            $src  = file_exists($webp) ? $webp : $path;

                            $loading_attr = ($i == 0) ? 'fetchpriority="high"' : 'loading="lazy"';
                            ?>
                            <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($item['nome']); ?>" title="<?php echo htmlspecialchars($item['nome']); ?>" width="480" height="320" <?php echo $loading_attr; ?> decoding="async" style="width:100%;height:auto;object-fit:cover" />
                        </div>
                        <div class="detail-box">
                            <h4><?php echo htmlspecialchars($nomeF); ?></h4>
                            <h6 class="price"><span class="new_price">R$ <?php echo $valorF; ?></span></h6>
                            <a href="agendamentos">Agendar</a>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>
    </div>
</section>

<?php
$res = $pdo->query("SELECT * FROM foto_admin ORDER BY id DESC LIMIT 12");
$dados = $res->fetchAll(PDO::FETCH_ASSOC);
if (count($dados) > 0) {
?>
<div class="service pt-5 pb-3 bg-white" style="background:#020202;margin-top:0;">
    <div class="container">
        <div class="row">
            <?php foreach ($dados as $d): ?>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="service-item text-center bg-light p-3 rounded">
                    <div class="service-img mb-2">
                        <?php
                        $path = "sistema/painel/img/foto_admin/{$d['imagem']}";
                        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
                        $src  = file_exists($webp) ? $webp : $path;
                        ?>
                        <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($d['titulo']); ?>" title="<?php echo htmlspecialchars($d['titulo']); ?>" width="600" height="250" loading="lazy" decoding="async" class="img-fluid rounded shadow-sm" style="height:250px;object-fit:cover;width:100%;">
                    </div>
                    <a class="btn btn-outline-dark mt-2 fw-bold" href="agendamentos">
                        <?php echo htmlspecialchars($d['titulo']); ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="ver-mais-wrapper text-center mb-3">
            <a href="servicos" class="btn btn-lg" style="background-color:#007bff; color:white; font-size:2rem; padding:14px 40px; border-radius:50px; text-decoration:none; display:inline-block; font-weight:bold; box-shadow:0px 4px 8px rgba(0,0,0,0.2);" onmouseover="this.style.backgroundColor='#0056b3';" onmouseout="this.style.backgroundColor='#007bff';">
                Ver mais Serviços
            </a>
        </div>
    </div>
</div>
<?php } ?>

<section class="about_section" style="background:#020202;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 px-0">
                <div class="img-box">
                    <?php
                    $videoEmCima  = ($url_video !== '' && trim($posicao_video) === 'sobre');
                    $videoEmBaixo = ($url_video !== '' && trim($posicao_video) === 'abaixo');
                    if ($videoEmCima) {
                        echo '<iframe width="100%" height="350" src="' . htmlspecialchars($url_video) . '" title="YouTube video player" frameborder="0" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
                    } else {
                        $imgPath  = "images/{$imagem_sobre}";
                        $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $imgPath);
                    ?>
                    <picture>
                        <?php if (file_exists($webpPath)) : ?>
                        <source type="image/webp" srcset="<?php echo htmlspecialchars($webpPath); ?>">
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Sobre nós" width="1200" height="675" loading="lazy" decoding="async" style="width:100%;height:auto;display:block">
                    </picture>
                    <?php } ?>
                </div>
            </div>
            <div class="col-md-5">
                <div class="detail-box">
                    <div class="heading_container">
                        <h2 class="" style="color:#FFF">Sobre Nós</h2>
                    </div>
                    <p class="detail_p_mt"><?php echo htmlspecialchars($texto_sobre); ?></p>
                    <a href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo htmlspecialchars($tel_whatsapp); ?>" target="_blank" rel="noopener" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold">
                        Mais Informações
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($videoEmBaixo) : ?>
<div>
    <iframe class="video_mobile" width="100%" src="<?php echo htmlspecialchars($url_video); ?>" title="YouTube video player" frameborder="0" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
</div>
<?php endif; ?>

<section class="simulador_section layout_padding" style="background:#111;">
    <div class="container">
        <div class="heading_container heading_center mb-5">
            <h2 style="color:#fff;">Descubra como você pode ficar com prótese capilar</h2>
            <p class="col-lg-9 px-0 mx-auto" style="color:#d7d7d7;">
                Agora você mesmo pode fazer uma simulação rápida e prática do seu novo visual.
                Envie sua foto e veja uma prévia realista da prótese capilar, respeitando a cor natural do seu cabelo.
                Se houver fios grisalhos, o sistema mantém o padrão original, garantindo um resultado mais fiel e natural.
            </p>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="box h-100 text-center p-4" style="background:#fff;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,.12);">
                    <div style="width:72px;height:72px;line-height:72px;margin:0 auto 15px;border-radius:50%;background:#0d6efd;color:#fff;font-size:28px;font-weight:bold;">1</div>
                    <h4 style="font-weight:700;">Envie sua foto</h4>
                    <p style="margin-bottom:0;color:#555;">
                        Envie uma foto frontal do seu rosto para gerar uma prévia personalizada da prótese capilar com base no seu visual atual.
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="box h-100 text-center p-4" style="background:#fff;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,.12);">
                    <div style="width:72px;height:72px;line-height:72px;margin:0 auto 15px;border-radius:50%;background:#0d6efd;color:#fff;font-size:28px;font-weight:bold;">2</div>
                    <h4 style="font-weight:700;">Respeito à cor original</h4>
                    <p style="margin-bottom:0;color:#555;">
                        A simulação respeita a cor natural do seu cabelo, mantendo um resultado mais realista e alinhado ao seu perfil.
                    </p>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="box h-100 text-center p-4" style="background:#fff;border-radius:14px;box-shadow:0 8px 24px rgba(0,0,0,.12);">
                    <div style="width:72px;height:72px;line-height:72px;margin:0 auto 15px;border-radius:50%;background:#0d6efd;color:#fff;font-size:28px;font-weight:bold;">3</div>
                    <h4 style="font-weight:700;">Veja sua prévia</h4>
                    <p style="margin-bottom:0;color:#555;">
                        Visualize uma prévia do seu novo visual antes do atendimento e tenha mais segurança na sua escolha.
                    </p>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-8 mx-auto">
                <div class="text-center p-4" style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:16px;">
                    <h4 style="color:#fff;font-weight:700;">Veja como você pode ficar com prótese capilar</h4>
                    <p style="color:#d7d7d7;margin-bottom:20px;">
                        Envie sua foto e receba uma simulação visual do seu novo cabelo em segundos. A definição final de cor, densidade e modelo será ajustada posteriormente pelo profissional durante a avaliação.
                    </p>

                    <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalSimulacaoCliente" style="font-size:1.3rem; padding:12px 34px; border-radius:50px; text-decoration:none; display:inline-block; font-weight:bold; box-shadow:0px 4px 8px rgba(0,0,0,0.2);">
                        Fazer minha simulação
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$query = $pdo->query("SELECT * FROM produtos where estoque > 0 and valor_venda > 0 ORDER BY id desc limit 8");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
?>
<section class="product_section layout_padding">
    <div class="container-fluid">
        <div class="heading_container heading_center"><h2>Nossos Produtos</h2></div>
        <div class="row">
            <?php
            for ($i = 0; $i < $total_reg; $i++) {
                $id        = $res[$i]['id'];
                $nome      = $res[$i]['nome'];
                $valor     = $res[$i]['valor_venda'];
                $foto      = $res[$i]['foto'];
                $descricao = $res[$i]['descricao'];
                $valorF    = number_format($valor, 2, ',', '.');
                $nomeF     = mb_strimwidth($nome, 0, 23, "...");
            ?>
            <div class="col-sm-6 col-md-3 mb-4">
                <div class="box h-100 d-flex flex-column">
                    <div class="img-box mb-3">
                        <?php
                        $path = "sistema/painel/img/produtos/$foto";
                        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
                        $src  = file_exists($webp) ? $webp : $path;
                        ?>
                        <img
                            src="<?php echo $src; ?>"
                            title="<?php echo htmlspecialchars($descricao); ?>"
                            alt="<?php echo htmlspecialchars($nome); ?>"
                            width="480" height="360"
                            loading="lazy" fetchpriority="low" decoding="async"
                            sizes="(max-width: 576px) 44vw, (max-width: 992px) 22vw, 360px"
                            class="img-fluid"
                        />
                    </div>
                    <div class="detail-box text-center">
                        <h5><?php echo $nomeF; ?></h5>
                        <h6 class="price"><span class="new_price">R$ <?php echo $valorF; ?></span></h6>
                        <a target="_blank" rel="noopener" href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo $tel_whatsapp; ?>&text=Ola, gostaria de saber mais informações sobre o produto <?php echo $nome; ?>">
                            Comprar Agora
                        </a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="ver-mais-wrapper text-center mb-3">
            <a href="produtos" class="btn btn-lg" style="background-color:#007bff; color:white; font-size:2rem; padding:14px 40px; border-radius:50px; text-decoration:none; display:inline-block; font-weight:bold; box-shadow:0px 4px 8px rgba(0,0,0,0.2);" onmouseover="this.style.backgroundColor='#0056b3';" onmouseout="this.style.backgroundColor='#007bff';">
                Ver mais Produtos
            </a>
        </div>
    </div>
</section>
<?php } ?>

<?php
$query = $pdo->query("SELECT * FROM comentarios where ativo = 'Sim' ORDER BY id desc LIMIT 12");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
?>
<section class="client_section layout_padding-bottom">
    <div class="container">
        <div class="heading_container"><h2>Depoimento dos nossos Clientes</h2></div>
        <div class="client_container">
            <div class="carousel-wrap">
                <div class="owl-carousel client_owl-carousel">
                    <?php
                    for ($i = 0; $i < $total_reg; $i++) {
                        $id    = $res[$i]['id'];
                        $nome  = $res[$i]['nome'];
                        $texto = $res[$i]['texto'];
                        $foto  = $res[$i]['foto'];
                    ?>
                    <div class="item">
                        <div class="box">
                            <div class="img-box">
                                <?php
                                $path = "sistema/painel/img/comentarios/$foto";
                                $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);
                                $src  = file_exists($webp) ? $webp : $path;
                                ?>
                                <img
                                    src="<?php echo $src; ?>"
                                    alt="<?php echo htmlspecialchars($nome); ?>"
                                    width="120" height="120"
                                    loading="lazy" fetchpriority="low" decoding="async"
                                    class="img-1"
                                    style="aspect-ratio:1/1;object-fit:cover"
                                />
                            </div>
                            <div class="detail-box">
                                <h5><?php echo $nome; ?></h5>
                                <p><?php echo $texto; ?></p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="btn-box2 text-center mb-3">
            <a href="#" class="btn btn-lg" data-toggle="modal" data-target="#modalComentario" style="background-color:#007bff; color:white; font-size:2rem; padding:14px 40px; border-radius:50px; text-decoration:none; display:inline-block; font-weight:bold; box-shadow:0px 4px 8px rgba(0,0,0,0.2);" onmouseover="this.style.backgroundColor='#0056b3';" onmouseout="this.style.backgroundColor='#007bff';">
                Inserir Depoimento
            </a>
        </div>
    </div>
</section>
<?php } ?>

<div class="modal fade" id="modalSimulacaoCliente" tabindex="-1" role="dialog" aria-labelledby="modalSimulacaoClienteLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalSimulacaoClienteLabel">Simulação de Prótese Capilar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-20px">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="form_simulacao_cliente" method="post" action="javascript:void(0);" enctype="multipart/form-data" onsubmit="return false;">
                <div class="modal-body">

                    <div class="alert alert-info" style="font-size:14px;line-height:22px;">
                        Envie uma foto frontal para gerar sua simulação. A prévia deve respeitar a cor natural do seu cabelo e,
                        se houver fios grisalhos, manter esse padrão para um resultado mais fiel.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Seu Nome *</label>
                                <input type="text" class="form-control" id="nome_simulacao_cliente" name="nome" placeholder="Digite seu nome" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>WhatsApp *</label>
                                <input type="tel" class="form-control" id="telefone_simulacao_cliente" name="telefone" placeholder="(00) 00000-0000" maxlength="15" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Escolha sua foto</label>
                                <input type="file" class="form-control" id="foto_simulacao_cliente" name="foto" accept=".jpg,.jpeg,.png,.webp" onchange="carregarPreviewSimulacaoCliente()" required>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:10px;">
                        <div class="col-md-6">
                            <div class="painel-comparacao-simulacao">
                                <span class="selo-comparacao">Antes</span>
                                <div class="titulo-painel">Imagem Original</div>
                                <div class="box-imagem-simulacao">
                                    <img src="sistema/painel/images/simulacoes/sem-foto.jpg" id="preview_original_simulacao_cliente" alt="">
                                </div>
                                <div class="legenda-simulacao">Foto enviada pelo cliente para comparação visual.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="painel-comparacao-simulacao">
                                <span class="selo-comparacao">Depois</span>
                                <div class="titulo-painel">Imagem Simulada</div>
                                <div class="box-imagem-simulacao">
                                    <img src="sistema/painel/images/simulacoes/sem-foto.jpg" id="preview_resultado_simulacao_cliente" alt="">
                                </div>
                                <div class="legenda-simulacao">Prévia gerada pela IA respeitando a cor original e o padrão grisalho do cliente.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:18px;">
                        <div class="col-md-12">
                            <button type="button" id="btn_simular_cliente" class="btn btn-primary btn-block" style="font-size:18px;font-weight:700;padding:12px;" onclick="enviarSimulacaoCliente()">
                                <i id="icone_btn_simular_cliente" class="fa fa-magic"></i> Simular
                            </button>
                        </div>
                    </div>

                    <div class="row" style="margin-top:12px;">
                        <div class="col-md-12">
                            <small><div id="mensagem_simulacao_cliente" align="center"></div></small>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>

<?php
$res = $pdo->query("SELECT DISTINCT pergunta, resposta FROM faq ORDER BY id DESC");
$faq = $res->fetchAll(PDO::FETCH_ASSOC);
$total = count($faq);
$metade = ceil($total / 2);
$coluna1 = array_slice($faq, 0, $metade);
$coluna2 = array_slice($faq, $metade);
?>
<section class="faq-section text-center">
    <div class="container">
        <h2 class="section-heading mb-5">Encontre respostas para as dúvidas mais comuns dos nossos clientes</h2>
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <?php foreach ($coluna1 as $index => $item): ?>
                        <div class="panel panel-default mb-3">
                            <div class="panel-heading bg-light p-2 rounded shadow-sm">
                                <h5 class="panel-title mb-0">
                                    <a data-toggle="collapse" href="#faq1_<?php echo $index; ?>" class="faq-toggle d-block text-dark text-decoration-none">
                                        <?php echo htmlspecialchars($item['pergunta']); ?>
                                        <span class="toggle-icon float-right">+</span>
                                    </a>
                                </h5>
                            </div>
                            <div id="faq1_<?php echo $index; ?>" class="panel-collapse collapse">
                                <div class="panel-body p-2 text-left bg-white border rounded-bottom"><?php echo nl2br(htmlspecialchars($item['resposta'])); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="col-md-6 mb-4">
                        <?php foreach ($coluna2 as $index => $item): ?>
                        <div class="panel panel-default mb-3">
                            <div class="panel-heading bg-light p-2 rounded shadow-sm">
                                <h5 class="panel-title mb-0">
                                    <a data-toggle="collapse" href="#faq2_<?php echo $index; ?>" class="faq-toggle d-block text-dark text-decoration-none">
                                        <?php echo htmlspecialchars($item['pergunta']); ?>
                                        <span class="toggle-icon float-right">+</span>
                                    </a>
                                </h5>
                            </div>
                            <div id="faq2_<?php echo $index; ?>" class="panel-collapse collapse">
                                <div class="panel-body p-2 text-left bg-white border rounded-bottom"><?php echo nl2br(htmlspecialchars($item['resposta'])); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="contact_section layout_padding-bottom">
    <div class="container">
        <div class="heading_container heading_center">
            <h2>Transforme seu visual: Pronto para um visual moderno?</h2>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form_container">
                    <form id="form-email">
                        <div><input type="text" name="nome" placeholder="Seu Nome" required/></div>
                        <div><input type="text" name="telefone" id="telefone" placeholder="Seu Whatsapp" required/></div>
                        <div><input type="email" name="email" placeholder="Seu Email" required/></div>
                        <div><input type="text" name="mensagem" class="message-text" placeholder="Mensagem" required/></div>
                        <div class="btn_box"><button>Enviar</button></div>
                    </form>
                    <br><div id="mensagem"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="map_container" id="map_container">
                    <template id="map_template"><?php echo $mapa; ?></template>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="mt-4"><?php require_once("rodape.php"); ?></div>

<div class="modal fade" id="modalComentario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Inserir Depoimento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top:-20px">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nome</label>
                                <input type="text" class="form-control" id="nome_cliente" name="nome" placeholder="Nome" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Texto <small>(Até 500 Caracteres)</small></label>
                                <textarea maxlength="500" class="form-control" id="texto_cliente" name="texto" placeholder="Texto Comentário" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Foto</label>
                                <input class="form-control" type="file" name="foto" onChange="carregarImg();" id="foto">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="divImg">
                                <img src="sistema/painel/img/comentarios/sem-foto.jpg" width="80" id="target" alt="">
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="cliente" value="1">
                    <br><small><div id="mensagem-comentario" align="center"></div></small>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Inserir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.slider_section .carousel-inner,
.slider_section .carousel-item {
    min-height: 420px;
}

.client_section .img-1 {
    width: 120px;
    height: 120px;
    object-fit: cover;
    display: block;
}

.service .service-img {
    min-height: 250px;
}

.service .service-img img {
    width: 100%;
    height: 250px;
    object-fit: cover;
    display: block;
}

.product_section,
.service,
.about_section,
.client_section,
.faq-section,
.contact_section {
    content-visibility: auto;
    contain-intrinsic-size: 1px 1000px;
}
</style>

<style>
.cookie-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100vw;
    background: rgba(53,51,62,.92);
    color: #fff;
    z-index: 99999;
    font-family: Arial,sans-serif;
    padding: 18px 0 15px;
    box-shadow: 0 -2px 20px rgba(0,0,0,.2);
    text-align: center;
    transition: .3s;
}
.cookie-banner.hide {
    display: none !important;
}
.cookie-banner .cookie-text {
    font-size: 26px;
    font-weight: 500;
    margin-bottom: 3px;
    display: block;
}
.cookie-banner .cookie-info {
    font-size: 13px;
    margin-bottom: 15px;
    opacity: .88;
    display: block;
}
.cookie-banner .cookie-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}
.cookie-banner button,
.cookie-banner a.termos-btn {
    background: #282736;
    color: #fff;
    border: 1px solid #fff;
    border-radius: 4px;
    padding: 10px 32px;
    font-size: 18px;
    font-weight: bold;
    margin: 0 0 2px 0;
    transition: .18s;
    outline: 0;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,.07);
    text-decoration: none;
    display: inline-block;
}
.cookie-banner button:hover,
.cookie-banner a.termos-btn:hover {
    background: #0d6efd;
    color: #fff;
    border-color: #0d6efd;
}
@media (max-width:600px) {
    .cookie-banner .cookie-text {
        font-size: 16px;
    }
    .cookie-banner button,
    .cookie-banner a.termos-btn {
        font-size: 15px;
        padding: 8px 10px;
    }
}
</style>

<style>
#modalSimulacaoCliente .modal-content {
    border-radius: 18px;
    border: 0;
    overflow: hidden;
    box-shadow: 0 18px 50px rgba(0,0,0,.25);
}
#modalSimulacaoCliente .modal-header {
    background: linear-gradient(90deg,#111 0%,#1f1f1f 100%);
    color: #fff;
    border-bottom: 0;
    padding: 18px 22px;
}
#modalSimulacaoCliente .modal-header .close {
    color: #fff;
    opacity: 1;
    text-shadow: none;
}
#modalSimulacaoCliente .modal-body {
    background: #f7f8fa;
    padding: 22px;
}
#modalSimulacaoCliente .alert {
    border-radius: 12px;
    border: 1px solid #d7e8ff;
    background: #eef6ff;
    color: #2f4f6f;
    margin-bottom: 18px;
}
#modalSimulacaoCliente .form-group label {
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
}
#modalSimulacaoCliente .form-control {
    border-radius: 10px;
    min-height: 46px;
    border: 1px solid #d8dee6;
    box-shadow: none;
}
#modalSimulacaoCliente .form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15);
}
.painel-comparacao-simulacao {
    background: #fff;
    border: 1px solid #e4e7eb;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 6px 20px rgba(0,0,0,.05);
    height: 100%;
}
.painel-comparacao-simulacao .titulo-painel {
    font-size: 15px;
    font-weight: 700;
    color: #2d3436;
    margin-bottom: 12px;
}
.box-imagem-simulacao {
    background: linear-gradient(180deg,#ffffff 0%,#f8f9fb 100%);
    border: 1px solid #dfe4ea;
    border-radius: 14px;
    min-height: 340px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 12px;
    position: relative;
    overflow: hidden;
}
.box-imagem-simulacao img {
    position: relative;
    z-index: 2;
    max-width: 100%;
    max-height: 310px;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    background: #fff;
}
.legenda-simulacao {
    margin-top: 12px;
    font-size: 13px;
    color: #6c757d;
    line-height: 20px;
}
#btn_simular_cliente {
    border-radius: 50px;
    min-height: 52px;
    font-size: 18px;
    font-weight: 700;
    box-shadow: 0 10px 24px rgba(13,110,253,.18);
}
#mensagem_simulacao_cliente {
    font-size: 14px;
    font-weight: 600;
    min-height: 22px;
}
.selo-comparacao {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 700;
    background: #111;
    color: #fff;
    margin-bottom: 10px;
}
@media (max-width:991px) {
    #modalSimulacaoCliente .modal-body {
        padding: 16px;
    }
    .box-imagem-simulacao {
        min-height: 260px;
        margin-bottom: 12px;
    }
    .box-imagem-simulacao img {
        max-height: 230px;
    }
    #btn_simular_cliente {
        font-size: 16px;
    }
}
</style>

<div class="cookie-banner hide" id="cookieBanner">
    <span class="cookie-text">Este website utiliza cookies</span>
    <span class="cookie-info">Nós utilizamos cookies para garantir que você tenha a melhor experiência em nosso site.</span>
    <div class="cookie-buttons">
        <button id="aceitarCookies">Permitir Cookies</button>
        <button id="recusarCookies">Recusar Cookies</button>
        <a href="#privacidade" data-toggle="modal" class="termos-btn">Termos de Uso</a>
    </div>
</div>

<script defer>
document.addEventListener('DOMContentLoaded', function(){
    var $ = window.jQuery || window.$;

    if (!localStorage.cookieChoice) {
        document.getElementById('cookieBanner').classList.remove('hide');
    }

    var aceita = document.getElementById('aceitarCookies');
    var recusa = document.getElementById('recusarCookies');

    if (aceita) {
        aceita.onclick = function() {
            localStorage.cookieChoice = "accept";
            document.getElementById('cookieBanner').classList.add('hide');
        };
    }

    if (recusa) {
        recusa.onclick = function() {
            localStorage.cookieChoice = "deny";
            document.getElementById('cookieBanner').classList.add('hide');
        };
    }

    if ($) {
        $('#form-email').on('submit', function(e){
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'ajax/enviar-email.php',
                type: 'POST',
                data: formData,
                success: function(mensagem){
                    $('#mensagem').text('').removeClass();
                    if (mensagem.trim() == "Enviado com Sucesso") {
                        $('#mensagem').addClass('text-success').text(mensagem);
                    } else {
                        $('#mensagem').addClass('text-danger').text(mensagem);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        $('#form').on('submit', function(e){
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'sistema/painel/paginas/comentarios/salvar.php',
                type: 'POST',
                data: formData,
                success: function(mensagem){
                    $('#mensagem-comentario').text('').removeClass();
                    if (mensagem.trim() == "Salvo com Sucesso") {
                        $('#mensagem-comentario').addClass('text-success').text('Comentário Enviado para Aprovação!');
                        $('#nome_cliente').val('');
                        $('#texto_cliente').val('');
                    } else {
                        $('#mensagem-comentario').addClass('text-danger').text(mensagem);
                    }
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        $('.panel-collapse').on('show.bs.collapse', function(){
            $(this).prev('.panel-heading').find('.toggle-icon').text('–');
        });

        $('.panel-collapse').on('hide.bs.collapse', function(){
            $(this).prev('.panel-heading').find('.toggle-icon').text('+');
        });
    }

    window.carregarImg = function(){
        var target = document.getElementById('target');
        var file = document.querySelector("#foto").files[0];
        var reader = new FileReader();

        reader.onloadend = function(){
            target.src = reader.result;
        };

        if (file) {
            reader.readAsDataURL(file);
        } else {
            target.src = "";
        }
    };
});
</script>

<script>
(function(){
    var cont = document.getElementById('map_container');
    if (!cont) {
        return;
    }

    function mount(){
        if (cont.dataset.mounted) {
            return;
        }

        var tpl = document.getElementById('map_template');
        if (!tpl) {
            return;
        }

        cont.innerHTML = tpl.innerHTML.replace('<iframe','<iframe loading="lazy" referrerpolicy="strict-origin-when-cross-origin" ');
        cont.dataset.mounted = '1';
    }

    if ('IntersectionObserver' in window) {
        new IntersectionObserver(function(es,obs){
            if (es[0].isIntersecting) {
                mount();
                obs.disconnect();
            }
        }, {rootMargin:'600px 0px'}).observe(cont);
    } else {
        mount();
    }
})();
</script>

<script>
(function(){
    var urlSimulacaoCliente = 'ajax/gerar_ia_cliente.php';
    var urlCadastroCliente = 'cadastrar.php';
    var imagemPadrao = 'sistema/painel/images/simulacoes/sem-foto.jpg';
    var pastaSimulacoes = 'sistema/painel/images/simulacoes/';

    function normalizarUrlImagem(url){
        if (!url) {
            return '';
        }

        url = String(url).trim();

        if (!url) {
            return '';
        }

        if (/^https?:\/\//i.test(url)) {
            return url;
        }

        if (url.charAt(0) === '/') {
            return window.location.origin + url;
        }

        return window.location.origin + '/' + url.replace(/^\.?\//, '');
    }

    function montarUrlSimulacao(caminho){
        if (!caminho) {
            return '';
        }

        caminho = String(caminho).trim();

        if (!caminho) {
            return '';
        }

        if (/^https?:\/\//i.test(caminho)) {
            return caminho;
        }

        if (caminho.indexOf('sistema/painel/images/simulacoes/') !== -1) {
            return normalizarUrlImagem(caminho);
        }

        if (caminho.charAt(0) === '/') {
            return normalizarUrlImagem(caminho);
        }

        return normalizarUrlImagem(pastaSimulacoes + caminho.replace(/^\.?\//, ''));
    }

    function adicionarCacheNaImagem(url){
        if (!url) {
            return '';
        }

        return url + (url.indexOf('?') !== -1 ? '&v=' : '?v=') + Date.now();
    }

    function retornoFoiSucesso(status){
        if (!status) {
            return false;
        }

        status = String(status).toLowerCase().trim();
        return status === 'success' || status === 'sucesso';
    }

    window.carregarPreviewSimulacaoCliente = function(){
        var campo = document.getElementById('foto_simulacao_cliente');
        var imgOriginal = document.getElementById('preview_original_simulacao_cliente');
        var imgResultado = document.getElementById('preview_resultado_simulacao_cliente');

        if (!campo || !imgOriginal || !imgResultado) {
            return;
        }

        if (!campo.files || !campo.files[0]) {
            imgOriginal.src = adicionarCacheNaImagem(imagemPadrao);
            imgResultado.src = adicionarCacheNaImagem(imagemPadrao);
            return;
        }

        var arquivo = campo.files[0];
        var reader = new FileReader();

        reader.onload = function(e){
            imgOriginal.src = e.target.result;
            imgResultado.src = adicionarCacheNaImagem(imagemPadrao);
        };

        reader.onerror = function(){
            imgOriginal.src = adicionarCacheNaImagem(imagemPadrao);
            imgResultado.src = adicionarCacheNaImagem(imagemPadrao);
        };

        reader.readAsDataURL(arquivo);
    };

    window.enviarSimulacaoCliente = function(){
        var form = document.getElementById('form_simulacao_cliente');
        var campo = document.getElementById('foto_simulacao_cliente');
        var nomeCampo = document.getElementById('nome_simulacao_cliente');
        var telefoneCampo = document.getElementById('telefone_simulacao_cliente');
        var btn = document.getElementById('btn_simular_cliente');
        var icone = document.getElementById('icone_btn_simular_cliente');
        var msg = document.getElementById('mensagem_simulacao_cliente');
        var imgOriginal = document.getElementById('preview_original_simulacao_cliente');
        var imgResultado = document.getElementById('preview_resultado_simulacao_cliente');

        if (!form || !campo || !btn || !icone || !msg || !imgOriginal || !imgResultado) {
            return;
        }

        var nome = nomeCampo ? nomeCampo.value.trim() : '';
        var telefone = telefoneCampo ? telefoneCampo.value.trim() : '';

        if (!nome || !telefone) {
            msg.className = 'text-danger';
            msg.innerText = 'Preencha nome e WhatsApp';
            return;
        }

        if (!campo.files || !campo.files[0]) {
            msg.className = 'text-danger';
            msg.innerText = 'Selecione uma imagem para simular';
            return;
        }

        var formData = new FormData(form);

        btn.disabled = true;
        icone.className = 'fa fa-spinner fa-spin';
        msg.className = 'text-info';
        msg.innerText = 'Gerando simulação, aguarde...';

        fetch(urlSimulacaoCliente, {
            method: 'POST',
            body: formData
        })
        .then(async function(response){
            var texto = await response.text();
            var dados = null;

            try {
                dados = JSON.parse(texto);
            } catch (e) {
                throw new Error(texto || 'Resposta inválida do servidor');
            }

            if (!response.ok || !dados || !retornoFoiSucesso(dados.status)) {
                throw new Error((dados && dados.mensagem) ? dados.mensagem : 'Erro ao processar a simulação');
            }

            var urlOriginal = '';
            var urlSimulada = '';

            if (dados.url_original) {
                urlOriginal = normalizarUrlImagem(dados.url_original);
            } else if (dados.foto_original) {
                urlOriginal = montarUrlSimulacao(dados.foto_original);
            }

            if (dados.url_simulada) {
                urlSimulada = normalizarUrlImagem(dados.url_simulada);
            } else if (dados.imagem_simulada) {
                urlSimulada = montarUrlSimulacao(dados.imagem_simulada);
            }

            if (!urlSimulada) {
                throw new Error('A imagem simulada não foi retornada');
            }

            if (urlOriginal) {
                imgOriginal.src = adicionarCacheNaImagem(urlOriginal);
            }

            imgResultado.src = adicionarCacheNaImagem(urlSimulada);

            fetch(urlCadastroCliente, {
                method: 'POST',
                body: new FormData(form)
            }).catch(function(){
            });

            msg.className = 'text-success';
            msg.innerText = 'Simulação gerada com sucesso';
        })
        .catch(function(erro){
            imgResultado.src = adicionarCacheNaImagem(imagemPadrao);
            msg.className = 'text-danger';
            msg.innerText = erro.message ? erro.message : 'Erro ao processar a simulação';
        })
        .finally(function(){
            btn.disabled = false;
            icone.className = 'fa fa-magic';
        });
    };

    if (window.jQuery && window.jQuery.fn && window.jQuery.fn.mask) {
        var $ = window.jQuery;

        var SPMaskBehavior = function(val){
            return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
        };

        var spOptions = {
            onKeyPress: function(val, e, field, options){
                field.mask(SPMaskBehavior.apply({}, arguments), options);
            }
        };

        $('#telefone').mask(SPMaskBehavior, spOptions);
        $('#telefone_simulacao_cliente').mask(SPMaskBehavior, spOptions);
    }

    if (window.jQuery) {
        $('#modalSimulacaoCliente').on('hidden.bs.modal', function(){
            var form = document.getElementById('form_simulacao_cliente');
            var imgOriginal = document.getElementById('preview_original_simulacao_cliente');
            var imgResultado = document.getElementById('preview_resultado_simulacao_cliente');
            var msg = document.getElementById('mensagem_simulacao_cliente');
            var btn = document.getElementById('btn_simular_cliente');
            var icone = document.getElementById('icone_btn_simular_cliente');

            if (form) {
                form.reset();
            }

            if (imgOriginal) {
                imgOriginal.src = adicionarCacheNaImagem(imagemPadrao);
            }

            if (imgResultado) {
                imgResultado.src = adicionarCacheNaImagem(imagemPadrao);
            }

            if (msg) {
                msg.innerText = '';
                msg.className = '';
            }

            if (btn) {
                btn.disabled = false;
            }

            if (icone) {
                icone.className = 'fa fa-magic';
            }
        });
    }
})();
</script>