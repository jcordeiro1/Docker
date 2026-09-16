<?php 
require_once("cabecalho.php");
$data_atual = date('Y-m-d');
?>

<!-- ⚡ CSS CRÍTICO INLINE -->
<style type="text/css">
	.sub_page .hero_area {
		min-height: auto;
	}
	
	/* Melhorias de contraste (WCAG AAA) */
	.intro-text a {
		color: #f5f5f5 !important;
	}
	
	.section-title {
		color: #ffffff !important;
	}
	
	/* Otimizar imagens */
	.service-img img {
		display: block;
		max-width: 100%;
		height: auto;
	}
</style>

</div>

<div class="footer_section" style="background: #020202;">
	
	<!-- start Pricing Plan -->
	<section id="pricing" class="pricing section-padding">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="title-wrap text-center"> 					
						<h1 class="section-title" style="font-size: 1.75rem; font-weight: 600;">ESCOLHA UM PLANO E FAÇA PARTE DA ASSINATURA!<br>✂️ Assinatura VIP para Estilo Incomparável!</h1>
						<div class="title-line">
							<span class="short-line"></span>
							<span class="long-line"></span>
						</div>
					</div>
					<div class="intro-text">
						<p style="color: #f5f5f5; font-size: 1.05rem;">Na <?php echo htmlspecialchars($nome_sistema, ENT_QUOTES, 'UTF-8') ?>, acreditamos que estilo e cuidado são inseparáveis. Apresentamos as Assinaturas Exclusivas para quem busca uma experiência premium. <br> Primeiro serviço por assinatura de tratamentos capilares, manutenção de prótese, corte de cabelo, barba, bigode, sobrancelha e manicure-pedicure. Confira...</p>
					</div>
				</div>
			</div>

			<!-- Service Start -->
			<div class="service">
				<div class="container">
					<div class="section-header text-center"></div>
					<div class="row">
						
						<!-- PLANO PRATA -->
						<div class="col-lg-4 col-md-6 mb-4">
							<div class="service-item">
								<div class="service-img">
									<a href="https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c93808487ad3deb0187aecbe3a6007e" 
									   target="_blank" 
									   rel="noopener noreferrer"
									   aria-label="Assinar Plano Prata - Assinatura mensal VIP">
										<img src="images/home/planos-plata.png" 
										     alt="Plano Prata - Assinatura VIP com corte de cabelo, barba e tratamentos mensais" 
										     loading="lazy"
										     decoding="async"
										     width="350"
										     height="450">
									</a>
								</div>
							</div>
						</div>
						
						<!-- PLANO OURO -->
						<div class="col-lg-4 col-md-6 mb-4">
							<div class="service-item">
								<div class="service-img">
									<a href="https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c93808487ad3deb0187aecdd059007f" 
									   target="_blank" 
									   rel="noopener noreferrer"
									   aria-label="Assinar Plano Ouro - Assinatura mensal VIP premium">
										<img src="images/home/planos-ouro.png" 
										     alt="Plano Ouro - Assinatura VIP premium com serviços exclusivos mensais" 
										     loading="lazy"
										     decoding="async"
										     width="350"
										     height="450">
									</a>
								</div> 
							</div>
						</div>
						
						<!-- PLANO DIAMANTE -->
						<div class="col-lg-4 col-md-6 mb-4">
							<div class="service-item">
								<div class="service-img">
									<a href="https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c93808487ad3deb0187aecef6960081" 
									   target="_blank" 
									   rel="noopener noreferrer"
									   aria-label="Assinar Plano Diamante - Assinatura mensal VIP top">
										<img src="images/home/planos-diamante.png" 
										     alt="Plano Diamante - Assinatura VIP top com todos os serviços premium mensais" 
										     loading="lazy"
										     decoding="async"
										     width="350"
										     height="450">
									</a>
								</div> 
							</div>
						</div>
					</div>
				</div>

				<?php 
				$query5 = $pdo->query("SELECT * FROM grupo_assinaturas where ativo = 'Sim' ORDER BY id asc");
				$res5 = $query5->fetchAll(PDO::FETCH_ASSOC);
				$total_reg5 = @count($res5);
				if($total_reg > 0){

					for($i5=0; $i5 < $total_reg5; $i5++){

						$id = $res5[$i5]['id'];
						$nome = $res5[$i5]['nome'];	
						$ativo = $res5[$i5]['ativo'];
						
						if($ativo == 'Sim'){
							$icone = 'fa-check-square';
							$titulo_link = 'Desativar Item';
							$acao = 'Não';
							$classe_linha = '';
						}else{
							$icone = 'fa-square-o';
							$titulo_link = 'Ativar Item';
							$acao = 'Sim';
							$classe_linha = 'text-muted';
						}

						$query22 = $pdo->query("SELECT * FROM itens_assinaturas where grupo = '$id'");
						$res22 = $query22->fetchAll(PDO::FETCH_ASSOC);
						$total_itens = @count($res22);

						if($total_itens == 2){
							$col_md = 'col-md-6';
						}else if($total_itens == 3){
							$col_md = 'col-md-4';
						}else{
							$col_md = 'col-md-3';
						}
				?>
	
				<div class="title-wrap text-center"> 
					<h2 style="color: #ffffff; font-size: 1.6rem; font-weight: 600;" id="barbeiro">
						<?php echo htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>
					</h2>
				</div>

				<!-- Pricing Section Start -->
				<div class="pricing--section pd--100-0-40">
					<div class="pricing--bg-img" data-bg-img="images/home/planos-diamante.png"></div>

					<div class="container">
						<div class="row">

							<?php 
							$query = $pdo->query("SELECT * FROM itens_assinaturas where grupo = '$id' and ativo = 'Sim'");
							$res = $query->fetchAll(PDO::FETCH_ASSOC);
							$total_reg = @count($res);
							if($total_reg > 0){

								for($i=0; $i < $total_reg; $i++){
	
									$id_item = $res[$i]['id'];
									$nome_item = $res[$i]['nome'];	
									$valor = $res[$i]['valor'];	
									$ativo = $res[$i]['ativo'];
									$c1 = $res[$i]['c1'];
									$c2 = $res[$i]['c2'];
									$c3 = $res[$i]['c3'];
									$c4 = $res[$i]['c4'];
									$c5 = $res[$i]['c5'];
									$c6 = $res[$i]['c6'];
									$c7 = $res[$i]['c7'];
									$c8 = $res[$i]['c8'];
									$c9 = $res[$i]['c9'];
									$c10 = $res[$i]['c10'];
									$c11 = $res[$i]['c11'];
									$c12 = $res[$i]['c12'];

									if($ativo == 'Sim'){
										$icone = 'fa-check-square';
										$titulo_link = 'Desativar Item';
										$acao = 'Não';
										$classe_linha = '';
									}else{
										$icone = 'fa-square-o';
										$titulo_link = 'Ativar Item';
										$acao = 'Sim';
										$classe_linha = 'text-muted';
									}
		
									$valorF = number_format($valor, 0, ',', '.');		
							?>

							<div class="<?php echo $col_md ?> col-xs-6 col-xxs-12 mb-4">
								<div class="pricing--item">
									<div class="header">
										<h3 style="color: #ffffff; font-size: 1.4rem;" class="h5">
											<?php echo htmlspecialchars($nome_item, ENT_QUOTES, 'UTF-8') ?>
										</h3>
										<p class="price" style="color: #f5f5f5;">
											R$<strong class="font--semibold"><?php echo $valorF ?></strong>/por mês*
										</p>
									</div>

									<div class="features">
										<ul style="list-style: none; padding: 0; color: #e5e5e5;">
											<?php if($c1 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c1, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c2 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c2, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c3 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c3, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c4 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c4, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c5 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c5, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c6 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c6, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c7 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c7, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c8 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c8, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c9 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c9, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c10 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c10, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c11 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c11, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>
											<?php if($c12 != ""){ ?>
											<li style="padding: 6px 0;"><?php echo htmlspecialchars($c12, ENT_QUOTES, 'UTF-8') ?></li>
											<?php } ?>											
										</ul>
									</div>

									<div class="action">
										<form action="assinar.php" method="POST" target="_blank" rel="noopener noreferrer">
											<input type="hidden" name="id_item" value="<?php echo htmlspecialchars($id_item, ENT_QUOTES, 'UTF-8') ?>">
											<button class="btn btn-custom" 
											        type="submit"
											        aria-label="Assinar plano <?php echo htmlspecialchars($nome_item, ENT_QUOTES, 'UTF-8') ?>"
											        style="min-height: 48px; min-width: 48px; font-size: 1rem; padding: 12px 24px;">
												ASSINAR
											</button>
										</form>
									</div>
								</div>
							</div>				
						
							<?php } } ?>
						</div>
					</div>
				</div>
				<!-- Pricing Section End -->
				<?php } } ?>
			</div>
		</div>
	</section>
  
<!-- contact section -->
<section class="contact_section layout_padding-bottom" style="background:#020202; color:#fff; padding:40px 0 60px; margin-top:0;">
  <div class="container">
    <div class="heading_container text-center">
      <h2 style="color:#ffffff; font-weight:600; margin-bottom:22px; font-size: 1.6rem;">
        Transforme seu visual: Pronto para um visual moderno?
        <br class="d-none d-md-block">Vamos conversar e fazer acontecer!
      </h2>
    </div>

    <div class="row align-items-stretch">
      <!-- Formulário -->
      <div class="col-md-6 mb-4 mb-md-0">
        <div class="form_container">

          <?php
          // Carregar profissionais
          $profissionais = [];
          try {
            $sql = "SELECT id, nome FROM usuarios WHERE ativo = 'Sim' AND (nivel IS NULL OR nivel <> 'Administrador') ORDER BY nome";
            $st = $pdo->query($sql);
            $profissionais = $st ? $st->fetchAll(PDO::FETCH_ASSOC) : [];
          } catch (Exception $e) {
            $profissionais = [];
          }
          ?>

          <!-- FORM ÚNICO (com reCAPTCHA v3) -->
          <form id="form-email" data-recaptcha-action="contato" novalidate>
            <input type="hidden" name="recaptcha_token" value="">
            <input type="hidden" name="recaptcha_action" value="contato">

            <!-- Nome -->
            <div class="mb-3">
              <label for="nome" class="visually-hidden">Nome completo</label>
              <input type="text"
                     id="nome"
                     name="nome"
                     placeholder="Seu Nome"
                     required
                     aria-required="true"
                     autocomplete="name"
                     style="width:100%; background:#1a1a1a; color:#f5f5f5; border:1px solid rgba(255,255,255,.3); height:48px; border-radius:10px; padding:10px 14px; outline:0; font-size:16px;">
            </div>

            <!-- Telefone -->
            <div class="mb-3">
              <label for="telefone" class="visually-hidden">Telefone com DDD</label>
              <input type="tel"
                     id="telefone"
                     name="telefone"
                     placeholder="Seu Telefone"
                     required
                     aria-required="true"
                     autocomplete="tel"
                     style="width:100%; background:#1a1a1a; color:#f5f5f5; border:1px solid rgba(255,255,255,.3); height:48px; border-radius:10px; padding:10px 14px; outline:0; font-size:16px;">
            </div>

            <!-- Profissional -->
            <div class="mb-3">
              <select id="profissional"
                      name="profissional"
                      required
                      aria-required="true"
                      style="width:100%; background:#1a1a1a; color:#f5f5f5; border:1px solid rgba(255,255,255,.3); height:48px; border-radius:10px; padding:10px 12px; outline:0; font-size:16px;">
                <option value="" disabled selected>Selecione o Profissional</option>
                <?php if (!empty($profissionais)): ?>
                  <?php foreach ($profissionais as $p): ?>
                    <option value="<?php echo (int)$p['id']; ?>">
                      <?php echo htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                  <?php endforeach; ?>
                <?php else: ?>
                  <option value="" disabled>Nenhum profissional disponível</option>
                <?php endif; ?>
              </select>
            </div>

            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="visually-hidden">Endereço de e-mail</label>
              <input type="email"
                     id="email"
                     name="email"
                     placeholder="Seu Email"
                     required
                     aria-required="true"
                     autocomplete="email"
                     style="width:100%; background:#1a1a1a; color:#f5f5f5; border:1px solid rgba(255,255,255,.3); height:48px; border-radius:10px; padding:10px 14px; outline:0; font-size:16px;">
            </div>

            <!-- Mensagem -->
            <div style="margin-bottom:14px;">
              <input type="text"
                     name="mensagem"
                     class="message-text"
                     placeholder="Mensagem"
                     required
                     style="width:100%; background:#0f0f10; color:#fff; border:1px solid #2a2a2c; border-radius:8px; padding:12px;">
            </div>

            <!-- Botão -->
            <div class="btn_box">
              <button type="submit"
                      aria-label="Enviar mensagem de contato"
                      style="background:#0d6efd; color:#fff; border:0; border-radius:12px; padding:14px 28px; font-weight:700; letter-spacing:.3px; min-height:48px; min-width:48px; font-size:1rem; cursor:pointer; transition:all 0.3s;">
                Enviar Mensagem
              </button>
            </div>
          </form>

<div id="mensagem" 
     class="mt-3" 
     role="alert" 
     aria-live="polite" 
     style="color:#f5f5f5; min-height:24px;"></div>

<!-- CSS para visually-hidden -->
<style>
.visually-hidden {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}
</style>
					</div>
				</div>

				<!-- Mapa -->
				<div class="col-md-6">
					<div class="map_container"
					     role="region"
					     aria-label="Mapa de localização da barbearia"
					     style="border-radius:14px; overflow:hidden; box-shadow:0 12px 28px rgba(0,0,0,.35); min-height:320px;">
						<?php echo $mapa ?>
					</div>
				</div>
			</div>
		</div>
	</section>

</div>

<div class="mt-0" style="margin-top:0!important;">
	<?php require_once("rodape.php"); ?>
</div>

<!-- CSS Externo Otimizado -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'">
<noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"></noscript>

<!-- JavaScript Otimizado -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>

<style type="text/css">
	.select2-selection__rendered {
		line-height: 45px !important;
		font-size:16px !important;
		color:#000 !important;
	}
	.select2-selection {
		height: 45px !important;
		font-size:16px !important;
		color:#000 !important;
	}
</style>  

<!-- Scripts Otimizados -->
<script>
// Aguardar jQuery carregar
document.addEventListener('DOMContentLoaded', function() {
  // Verifica se jQuery existe
  if (typeof jQuery === 'undefined') {
    console.warn('jQuery não carregado ainda');
    return;
  }

  // Esconder botão de editar se existir
  var botaoEditar = document.getElementById("botao_editar");
  if (botaoEditar) {
    botaoEditar.style.display = "none";
  }

  // Inicializar Select2 se disponível
  if (jQuery.fn.select2) {
    jQuery('.sel2').select2({});
  }

  // Listar funcionários se a função existir
  if (typeof listarFuncionarios === 'function') {
    listarFuncionarios();
  }

  // Otimizar iframe do Google Maps
  var mapContainer = document.querySelector('.map_container');
  if (mapContainer) {
    var iframe = mapContainer.querySelector('iframe');
    if (iframe) {
      var isMobile = window.matchMedia('(max-width: 767px)').matches;
      var height = isMobile ? 320 : 360;
      
      iframe.setAttribute('width', '100%');
      iframe.setAttribute('height', String(height));
      iframe.setAttribute('loading', 'lazy');
      iframe.setAttribute('referrerpolicy', 'no-referrer-when-downgrade');
      iframe.style.width = '100%';
      iframe.style.height = height + 'px';
      iframe.style.border = '0';
    }
  }

  // Envio do formulário
  jQuery('#form-email').on('submit', function(e) {
    e.preventDefault();

    var msgEl = jQuery('#mensagem');
    msgEl.text('Enviando...').css('color', '#f5f5f5');

    var formData = new FormData(this);

    // Validar profissional
    var prof = (formData.get('profissional') || '').trim();
    if (!prof) {
      msgEl.text('Selecione o profissional.').css('color', '#ff6b6b');
      return;
    }

    jQuery.ajax({
      url: 'ajax/enviar-email.php',
      type: 'POST',
      data: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      success: function(response) {
        response = (response || '').trim();
        if (response === 'Enviado com Sucesso') {
          msgEl.css('color', '#7CFC00').text('✅ Enviado com Sucesso!');
          jQuery('#form-email')[0].reset();
        } else {
          msgEl.css('color', '#ff6b6b').text(response || 'Erro ao enviar.');
        }
      },
      error: function(xhr) {
        msgEl.css('color', '#ff6b6b').text('Falha de rede. Tente novamente.');
      },
      cache: false,
      contentType: false,
      processData: false
    });
  });
});

// Função salvar (mantida para compatibilidade)
function salvar() {
  if (typeof jQuery !== 'undefined') {
    jQuery('#id').val('');
  }
}
</script>