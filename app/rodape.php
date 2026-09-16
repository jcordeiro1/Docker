<?php require_once("sistema/conexao.php") ?>

<!-- CSS para Modal por Cima do Menu -->
<style>
.modal-backdrop.show { z-index: 9998 !important; }
#leadModal { z-index: 9999 !important; }
</style>

<footer class="footer_section" style="background:#020202;width:100%;margin-top:0;padding-top:1px;border-top:0;">
  <div class="container">
    <div class="footer_content">
      <div class="row">
        <div class="col-md-5 col-lg-5 footer-col">
          <div class="footer_detail">
            <h4>
              <a href="index">
                <img src="sistema/img/logo.png" width="100px" style="margin-right:5px;">
                <span style="color:#fff;"><?php echo $nome_sistema ?></span>
              </a>
            </h4>
            <p><?php echo $texto_rodape ?></p>
          </div>
        </div>

        <div class="col-md-7 col-lg-4">
          <h4 style="color:#fff;">Links Contatos</h4>
          <div class="contact_nav footer-col">
            <a href="https://goo.gl/maps/6XJLNBjYMdBqLGZHA" target="_blank">
              <i class="fa fa-map-marker" aria-hidden="true"></i>
              <span><?php echo $endereco_sistema ?></span>
            </a>
            <a href="assinatura" target="_blank"><i class="fa fa-user" aria-hidden="true"></i><span>Assinatura protese</span></a>
            <a href="servicos" target="_blank"><i class="fa fa-user" aria-hidden="true"></i><span>Serviços cabeleireiro</span></a>
            <a href="barbearia" target="_blank"><i class="fa fa-user" aria-hidden="true"></i><span>Barbeiro cabeleireiro</span></a>
            <a href="protese" target="_blank"><i class="fa fa-user" aria-hidden="true"></i><span>Protese capilares</span></a>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="footer_form footer-col">
            <h4 style="color:#fff;">SOLICITE AVALIAÇÃO</h4>
            <p style="color:#ccc;"></p>
            <form id="form_cadastro" action="cadastrar.php" method="POST" action="#" data-recaptcha-action="cadastro" >
              <input type="hidden" name="recaptcha_token"  value="">
              <input type="hidden" name="recaptcha_action" value="cadastro">
              <input type="text" name="telefone" id="telefone_rodape" placeholder="WhatsApp" class="form-control mb-2" required />
              <input type="text" name="nome" placeholder="Nome Completo" class="form-control mb-2" required />
              <button type="submit" class="btn btn-theme btn-lg btn-block">Cadastrar</button>
            </form>
            <br>
            <small><div id="mensagem-rodape"></div></small>
          </div>
        </div>

      </div>
    </div>
  </div>

  <section id="bottom-footer" class="mt-4" style="margin-top:0;border-top:0;">
    <div class="container">
      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12 btm-footer-links ocultar-mobile">
          <a style="color:#FFF">&copy; <script>document.write(new Date().getFullYear())</script></a>
          <a href="#" data-toggle="modal" data-target="#privacidade" role="button" style="color:#FFF;">
  <i class="fa fa-lock" aria-hidden="true"></i> Política de privacidade
</a>
          <a href="#" data-toggle="modal" data-target="#modalTermos" role="button" class="text-white">
  <i class="fa fa-file-text-o" aria-hidden="true"></i> Termos de uso
</a>
          <a href="https://play.google.com/store/apps/details?id=app.agendacabeleireiro&pli=1" target="_blank" style="color:#FFF;"><i class="fa fa-play store"> App </i></a>
        </div>

        <div class="col-md-6 col-sm-6 col-xs-12 copyright text-right">
          <span class="cor-branca ocultar-mobile"><?php echo $nome_sistema ?></span>
          <a href="http://api.whatsapp.com/send?1=pt_BR&phone=<?php echo $tel_whatsapp ?>" target="_blank" style="color:#FFF;">
            <?php echo $whatsapp_sistema ?> |
          </a>
          <a title="Ir para o sistema" href="sistema" style="color:#FFF;" target="_blank">
            <i class="fa fa-user" aria-hidden="true"></i>
          </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</footer>

<?php unset($_SESSION['editar_agendamento']); ?>

<!-- jQuery (local) -->
<script src="js/jquery-3.4.1.min.js"></script>

<!-- Popper (CDN com SRI) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

<!-- Bootstrap (local) -->
<script src="js/bootstrap.js"></script>

<!-- OwlCarousel (CDN) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- Custom (local) -->
<script src="js/custom.js"></script>

<!-- Evita erro se myMap não estiver definido antes do Google Maps -->
<script>window.myMap = window.myMap || function(){};</script>

<!-- Google Maps (mantém callback), carregamento assíncrono para desempenho -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode(getenv('GOOGLE_MAPS_API_KEY') ?: ''); ?>&callback=myMap"></script>

<!-- Mascaras JS (carregar apenas uma vez) -->
<script src="sistema/painel/js/mascaras.js" type="text/javascript"></script>

<!-- Google Tag Manager (noscript) -->
<noscript>
  <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5BM8JXC" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>

<!-- jQuery Mask (para funcionar mascaras) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Modal Empresa -->
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
        <p class="text-muted"><small>
        Com mais de 25 anos de experiência, Jacy Cordeiro é mais que um cabeleireiro – é um estrategista de beleza!<br> 
        🎨💈 Especializado em cortes modernos, barba de precisão e próteses capilares, Jacy trabalha para resgatar a autoestima e o bem-estar de cada cliente. Cada atendimento é personalizado para realçar sua melhor versão e garantir resultados que vão além do espelho.
        <br><br>
        💇‍♂️ Cabeleireiro & Barbeiro <br>
        ✨ Especialista em Prótese Capilar <br>
        📆 Agendamentos Personalizados <br>
        🔥 Resultados que transformam!<br>
        💪 Se você busca excelência e inovação na beleza, marque um horário e viva a experiência!<br>
        Não se trata apenas de um corte. Trata-se de elevar sua confiança e potencializar sua beleza. Agende agora e experimente o toque de um verdadeiro profissional! 💪
        </small></p>
        <p class="text-muted"><small>
        Jacy Cordeiro - CNPJ: 38.896.614/0001-40.<br>
        Assista o vídeo a seguir.</small>
        </p>
        <iframe width="100%" height="500" src="https://www.youtube.com/embed/fO_s7jlO1tU?si=n3Yntmowyfms6aFm&amp;controls=0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        <p class="text-muted" align="center"><small>
        "Motivação é aquilo que te faz começar. Habito é aquilo que te faz continuar."</small>
        </p>
      </div>
    </div>
  </div>
</div>

<!-- Modal Termos (Bootstrap 4.3.1) -->
<div id="modalTermos" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tituloTermos" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="tituloTermos">Termos de Serviço — Jacy Cabeleireiro</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p><strong>1. Termos</strong></p>
        <p>Ao acessar o site Jacy Cabeleireiro, você concorda em cumprir estes termos de serviço, todas as leis e regulamentos aplicáveis e declara ser responsável pelo cumprimento de todas as leis locais. Se não concordar com algum termo, fica proibido de usar ou acessar este site. Os materiais aqui contidos são protegidos pelas leis de direitos autorais e marcas.</p>

        <p><strong>2. Uso de Licença</strong></p>
        <p>É concedida permissão para baixar temporariamente uma cópia dos materiais (informações ou software) do site Jacy Cabeleireiro somente para visualização pessoal e não comercial. Esta é a concessão de uma licença, não uma transferência de título, e sob esta licença você <u>não pode</u>:</p>
        <ul>
          <li>Modificar ou copiar os materiais;</li>
          <li>Usar os materiais para qualquer finalidade comercial ou exibição pública;</li>
          <li>Tentar descompilar ou fazer engenharia reversa de qualquer software do site;</li>
          <li>Remover direitos autorais ou outras notações de propriedade dos materiais;</li>
          <li>Transferir os materiais para outra pessoa ou “espelhar” em outro servidor.</li>
        </ul>
        <p>Esta licença termina automaticamente se você violar alguma restrição e pode ser rescindida pelo Jacy Cabeleireiro a qualquer momento. Ao encerrar a visualização, apague todos os materiais baixados (eletrônicos ou impressos).</p>

        <p><strong>3. Isenção de responsabilidade</strong></p>
        <p>Os materiais no site são fornecidos “no estado em que se encontram”. O Jacy Cabeleireiro não oferece garantias, expressas ou implícitas, incluindo, sem limitação, garantias de comerciabilidade, adequação a um fim específico ou não violação. Não garante precisão, resultados ou confiabilidade do uso dos materiais, próprios ou de sites vinculados.</p>

        <p><strong>4. Limitações</strong></p>
        <p>Em nenhum caso o Jacy Cabeleireiro ou seus fornecedores serão responsáveis por danos (incluindo perda de dados ou lucros, ou interrupção de negócios) decorrentes do uso ou incapacidade de usar os materiais, mesmo que notificado da possibilidade de tais danos. Algumas jurisdições não permitem tais limitações; elas podem não se aplicar a você.</p>

        <p><strong>5. Precisão dos materiais</strong></p>
        <p>Os materiais exibidos podem conter erros técnicos, tipográficos ou fotográficos. O Jacy Cabeleireiro pode alterar os materiais a qualquer momento, sem aviso, e não se compromete a atualizá-los.</p>

        <p><strong>6. Links</strong></p>
        <p>O Jacy Cabeleireiro não analisou todos os sites vinculados e não é responsável por seu conteúdo. A inclusão de qualquer link não implica endosso. O uso de sites vinculados é por conta e risco do usuário.</p>

        <p><strong>Modificações</strong></p>
        <p>O Jacy Cabeleireiro pode revisar estes termos a qualquer momento, sem aviso. Ao usar este site, você concorda em ficar vinculado à versão vigente.</p>

        <p class="mb-0"><strong>Lei aplicável</strong></p>
        <p>Estes termos são regidos e interpretados de acordo com as leis aplicáveis ao Jacy Cabeleireiro, e você se submete à jurisdição exclusiva dos tribunais competentes.</p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Ok, entendi</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal Privacidade (Bootstrap 4.3.1) -->
<div id="privacidade" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tituloPrivacidade" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="tituloPrivacidade">Política de Privacidade — Jacy Cabeleireiro</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <p><strong>Política de Privacidade</strong></p>
        <p>
          A sua privacidade é importante para nós. É política do Jacy Cabeleireiro respeitar a sua privacidade em relação
          a qualquer informação sua que possamos coletar no site Jacy Cabeleireiro, e outros sites que possuímos e operamos.
        </p>
        <p>
          Solicitamos informações pessoais apenas quando realmente precisamos delas para lhe fornecer um serviço. Fazemo-lo
          por meios justos e legais, com o seu conhecimento e consentimento. Também informamos por que estamos coletando
          e como será usado.
        </p>
        <p>
          Apenas retemos as informações coletadas pelo tempo necessário para fornecer o serviço solicitado. Quando armazenamos
          dados, protegemos dentro de meios comercialmente aceitáveis ​​para evitar perdas e roubos, bem como acesso, divulgação,
          cópia, uso ou modificação não autorizados.
        </p>
        <p>
          Não compartilhamos informações de identificação pessoal publicamente ou com terceiros, exceto quando exigido por lei.
        </p>
        <p>
          O nosso site pode ter links para sites externos que não são operados por nós. Esteja ciente de que não temos controle
          sobre o conteúdo e práticas desses sites e não podemos aceitar responsabilidade por suas respectivas políticas de privacidade.
        </p>
        <p>
          Você é livre para recusar a nossa solicitação de informações pessoais, entendendo que talvez não possamos fornecer alguns
          dos serviços desejados.
        </p>
        <p>
          O uso continuado de nosso site será considerado como aceitação de nossas práticas em torno de privacidade e informações
          pessoais. Se você tiver alguma dúvida sobre como lidamos com dados do usuário e informações pessoais, entre em contacto
          connosco.
        </p>

        <p><strong>Publicidade (Google AdSense)</strong></p>
        <p>
          O serviço Google AdSense que usamos para veicular publicidade usa um cookie DoubleClick para veicular anúncios mais
          relevantes em toda a Web e limitar o número de vezes que um determinado anúncio é exibido para você. Para mais informações
          sobre o Google AdSense, consulte as FAQs oficiais sobre privacidade do Google AdSense.
        </p>
        <p>
          Utilizamos anúncios para compensar os custos de funcionamento deste site e fornecer financiamento para futuros desenvolvimentos.
          Os cookies de publicidade comportamental usados ​​por este site foram projetados para garantir que você veja os anúncios mais
          relevantes sempre que possível, rastreando anonimamente seus interesses e apresentando conteúdos semelhantes que possam ser do seu interesse.
        </p>
        <p>
          Vários parceiros anunciam em nosso nome e os cookies de rastreamento de afiliados simplesmente nos permitem ver se nossos clientes
          acessaram o site através de um dos sites de nossos parceiros, para que possamos creditá-los adequadamente e, quando aplicável,
          permitir que nossos parceiros afiliados ofereçam qualquer promoção que possa incentivá-lo a realizar uma compra.
        </p>

        <p><strong>Compromisso do Usuário</strong></p>
        <p>O usuário se compromete a fazer uso adequado dos conteúdos e da informação que o Jacy Cabeleireiro oferece no site e, de forma enunciativa porém não limitativa:</p>
        <ul>
          <li>A) Não se envolver em atividades que sejam ilegais ou contrárias à boa-fé e à ordem pública;</li>
          <li>B) Não difundir propaganda ou conteúdo de natureza racista, xenofóbica, jogos de sorte ou azar, qualquer tipo de pornografia ilegal, apologia ao terrorismo ou contra os direitos humanos;</li>
          <li>C) Não causar danos aos sistemas físicos (hardware) e lógicos (software) do Jacy Cabeleireiro, de seus fornecedores ou terceiros, nem introduzir ou disseminar vírus informáticos ou quaisquer outros sistemas capazes de causar os danos mencionados.</li>
        </ul>

        <p><strong>Mais informações</strong></p>
        <p>
          Esperamos que esteja esclarecido e, como mencionado anteriormente, se houver algo que você não tem certeza se precisa ou não,
          geralmente é mais seguro deixar os cookies ativados, caso interaja com um dos recursos que você usa em nosso site.
        </p>

        <p class="mb-0"><small><em>Esta política é efetiva a partir de 2 November 2025 19:22.</em></small></p>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Fechar</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Ok, entendi</button>
      </div>
    </div>
  </div>
</div>


<!-- ================= MODAL LEAD (Novo Cliente) 
<div class="modal fade" id="leadModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:#151515; color:#fff; border-radius:16px;">
      <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,.1);">
        <h5 class="modal-title font-weight-bold">🎉 Receba Avaliação Gratuita</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="leadForm" autocomplete="off">
        <div class="modal-body">
          <div class="rounded p-3 mb-3" style="background:#f6f7fb; border:1px solid #e6e8ef;">
            <div class="d-flex">
              <div style="font-size:22px; margin-right:10px;">💬</div>
              <div class="small" style="color:#3b3f4a;">
                <strong>Converse com um especialista sem custo e sem compromisso.</strong><br>
                Em até alguns minutos respondemos no WhatsApp com <em>horários disponíveis</em> e <em>orientação rápida</em>.
                <ul class="mb-0 mt-2 pl-3">
                  <li>Atendimento rápido e discreto</li>
                  <li>Cancelamento a qualquer momento</li>
                  <li>Usamos seus dados apenas para contato do salão</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="form-group">
            
            <input type="text" class="form-control" name="nome" id="leadNome" maxlength="80" required placeholder="Seu nome completo">
          </div>

          <div class="form-group">
            
            <input type="tel" class="form-control" name="telefone" id="leadZap" placeholder="(45) 99999-0000" required>
          </div>

          <small class="form-text text-muted">Não cadastramos números repetidos.</small>
          <div id="leadMsg" class="small mt-2 text-center"></div>
        </div>

        <div class="modal-footer" style="border-top:1px solid rgba(255,255,255,.1);">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary font-weight-bold" id="leadBtn">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</div>================= -->
<!-- ================= FIM MODAL LEAD ================= -->

<!-- ============= CONTROLE DO MODAL - BOOTSTRAP 4.3.1 
<script>
(function() {
  'use strict';
  
  // Constantes
  var MODAL_ID = 'leadModal';
  var KEY_LAST = 'leadPopupLast';
  var KEY_DONE = 'clienteCadastrado';
  var HOURS_LOCK = 12;
  
  // Debug ativo
  var DEBUG = true;
  
  function log(msg) {
    if (DEBUG) console.log('[LEAD MODAL] ' + msg);
  }
  
  function now() {
    return Date.now();
  }
  
  // Verifica se pode mostrar o modal
  function canShow() {
    log('Verificando se pode mostrar modal...');
    
    // Já cadastrado?
    if (localStorage.getItem(KEY_DONE) === 'true') {
      log('❌ Cliente já cadastrado');
      return false;
    }
    
    // Forçar exibição (para teste)
    var params = new URLSearchParams(window.location.search);
    if (params.get('showLead') === '1') {
      log('✅ Forçando exibição (parâmetro showLead=1)');
      return true;
    }
    
    // É bot?
    var ua = navigator.userAgent || '';
    if (/bot|crawler|spider|lighthouse|pagespeed/i.test(ua)) {
      log('❌ Detectado como bot');
      return false;
    }
    
    // Verificar tempo desde última exibição
    var last = parseInt(localStorage.getItem(KEY_LAST) || '0', 10);
    var diff = now() - last;
    var hoursPassed = diff / (3600 * 1000);
    
    if (diff > (HOURS_LOCK * 3600 * 1000)) {
      log('✅ Pode mostrar (última exibição há ' + hoursPassed.toFixed(1) + ' horas)');
      return true;
    } else {
      log('❌ Bloqueado (última exibição há ' + hoursPassed.toFixed(1) + ' horas, mínimo ' + HOURS_LOCK + 'h)');
      return false;
    }
  }
  
  // Abre o modal usando Bootstrap 4
  function openModal() {
    log('Tentando abrir modal...');
    
    // Verifica se jQuery está carregado
    if (typeof jQuery === 'undefined') {
      log('❌ ERRO: jQuery não está carregado!');
      return;
    }
    
    // Verifica se Bootstrap modal está disponível
    if (typeof jQuery.fn.modal === 'undefined') {
      log('❌ ERRO: Bootstrap modal não está disponível!');
      return;
    }
    
    var $modal = jQuery('#' + MODAL_ID);
    
    if ($modal.length === 0) {
      log('❌ ERRO: Modal #' + MODAL_ID + ' não encontrado no DOM!');
      return;
    }
    
    log('✅ Modal encontrado, abrindo...');
    
    // Abre o modal
    $modal.modal({
      backdrop: true,
      keyboard: true,
      show: true
    });
    
    log('✅ Modal aberto com sucesso!');
    
    // Foco no campo nome após abrir
    setTimeout(function() {
      var input = document.getElementById('leadNome');
      if (input) {
        input.focus();
        log('✅ Foco no campo nome');
      }
    }, 500);
  }
  
  // Fecha o modal
  function closeModal() {
    log('Fechando modal...');
    
    // Salva timestamp
    localStorage.setItem(KEY_LAST, String(now()));
    log('⏱️ Timestamp salvo: ' + KEY_LAST);
    
    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.modal !== 'undefined') {
      jQuery('#' + MODAL_ID).modal('hide');
      log('✅ Modal fechado');
    }
    
    // Limpa backdrop após fechar
    setTimeout(cleanBackdrops, 300);
  }
  
  // Remove backdrops fantasmas
  function cleanBackdrops() {
    log('Limpando backdrops...');
    jQuery('.modal-backdrop').each(function() {
      jQuery(this).remove();
    });
    jQuery('body').removeClass('modal-open').css({
      overflow: '',
      paddingRight: ''
    });
    log('✅ Backdrops limpos');
  }
  
  // Marca cliente como cadastrado
  function marcarCadastrado() {
    localStorage.setItem(KEY_DONE, 'true');
    log('✅ Cliente marcado como cadastrado');
  }
  
  // Expõe funções globalmente
  window.showLeadModal = openModal;
  window.hideLeadModal = closeModal;
  window.marcarClienteCadastrado = marcarCadastrado;
  
  log('✅ Funções globais expostas');
  
  // Aguarda DOM e jQuery estarem prontos
  jQuery(document).ready(function() {
    log('DOM e jQuery prontos');
    
    // Configura eventos de fechar
    var $modal = jQuery('#' + MODAL_ID);
    
    // Evento ao fechar
    $modal.on('hidden.bs.modal', function() {
      log('Evento hidden.bs.modal disparado');
      cleanBackdrops();
    });
    
    // Botões de fechar
    $modal.find('[data-dismiss="modal"],.close').on('click', function() {
      log('Botão fechar clicado');
      closeModal();
    });
    
    // Verifica se pode mostrar
    if (!canShow()) {
      log('❌ Modal não será exibido (condições não atendidas)');
      return;
    }
    
    log('✅ Modal será exibido após interação do usuário');
    
    // Arma abertura após interação
    var armed = false;
    
    function arm() {
      if (armed) return;
      armed = true;
      log('🎯 Interação detectada, agendando abertura...');
      
      setTimeout(function() {
        log('⏰ Timeout atingido, abrindo modal agora!');
        openModal();
      }, 2000); // 2 segundos após primeira interação
      
      // Remove listeners após armar
      jQuery(window).off('scroll.leadmodal click.leadmodal keydown.leadmodal touchstart.leadmodal');
    }
    
    // Escuta eventos de interação
    jQuery(window).on('scroll.leadmodal click.leadmodal keydown.leadmodal touchstart.leadmodal', arm);
    
    // Fallback: abre após 10 segundos se não houver interação
    setTimeout(function() {
      if (!armed && document.visibilityState === 'visible') {
        log('⏰ Fallback: abrindo modal após 10s sem interação');
        arm();
      }
    }, 10000);
  });
  
})();
</script>============= -->

<!-- ============= MÁSCARA DE WHATSAPP (Modal Lead) ============= -->
<script>
jQuery(document).ready(function($) {
  var $zap = $('#leadZap');
  if ($zap.length === 0) return;
  
  $zap.attr({
    'inputmode': 'numeric',
    'autocomplete': 'tel'
  });
  
  function onlyDigits(v) {
    return (v || '').replace(/\D+/g, '').slice(0, 11);
  }
  
  function formatBR(d) {
    if (d.length <= 2) return d;
    if (d.length <= 6) return '(' + d.slice(0, 2) + ') ' + d.slice(2);
    if (d.length <= 10) return '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
    return '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
  }
  
  $zap.on('input', function() {
    var raw = $(this).val();
    var digits = onlyDigits(raw);
    $(this).val(formatBR(digits));
  });
  
  $zap.on('paste', function(e) {
    e.preventDefault();
    var clip = (e.originalEvent.clipboardData || window.clipboardData).getData('text') || '';
    $(this).val(formatBR(onlyDigits(clip)));
  });
});
</script>

<!-- ============= ENVIO AJAX - MARCA CLIENTE COMO CADASTRADO ============= -->
<script>
jQuery(document).ready(function($) {
  var $form = $('#leadForm');
  if ($form.length === 0) return;
  
  var $btn = $('#leadBtn');
  
  function onlyDigits(v) {
    return (v || '').replace(/\D+/g, '');
  }
  
  $form.on('submit', function(e) {
    e.preventDefault();
    
    var nome = $('#leadNome').val().trim();
    var fone = onlyDigits($('#leadZap').val());
    
    if (!nome || fone.length < 10) {
      if (window.Swal) {
        Swal.fire({
          icon: 'error',
          title: '❌ Dados inválidos',
          text: 'Preencha corretamente nome e telefone.',
          confirmButtonColor: '#0d6efd'
        });
      } else {
        alert('Preencha corretamente nome e telefone.');
      }
      return;
    }
    
    var oldText = $btn.text();
    $btn.prop('disabled', true).text('Enviando...');
    
    $.ajax({
      url: '/cadastrar.php',
      method: 'POST',
      data: $form.serialize(),
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(resp) {
        var texto = (resp || '').replace(/\s+/g, ' ').trim();
        
        // Sucesso
        if (texto.indexOf('Cadastrado com Sucesso') === 0 || texto.indexOf('Salvo com Sucesso') === 0) {
          if (typeof window.marcarClienteCadastrado === 'function') {
            window.marcarClienteCadastrado();
          }
          
          if (typeof window.hideLeadModal === 'function') {
            window.hideLeadModal();
          }
          
          if (window.Swal) {
            Swal.fire({
              icon: 'success',
              title: '✅ Sucesso!',
              text: 'Cadastro realizado com sucesso.',
              confirmButtonColor: '#0d6efd'
            }).then(function() {
              window.location.href = '/sistema/acesso';
            });
          } else {
            window.location.href = '/sistema/acesso';
          }
          return;
        }
        
        // Já cadastrado
        if (texto.indexOf('Você já está Cadastrado') === 0) {
          if (typeof window.marcarClienteCadastrado === 'function') {
            window.marcarClienteCadastrado();
          }
          
          if (typeof window.hideLeadModal === 'function') {
            window.hideLeadModal();
          }
          
          if (window.Swal) {
            Swal.fire({
              icon: 'warning',
              title: '⚠️ Já cadastrado',
              text: 'Este WhatsApp já possui cadastro.',
              confirmButtonColor: '#0d6efd'
            }).then(function() {
              window.location.href = '/sistema/acesso';
            });
          } else {
            window.location.href = '/sistema/acesso';
          }
          return;
        }
        
        // Erro de preenchimento
        if (texto.indexOf('Preencha nome e telefone') === 0) {
          if (window.Swal) {
            Swal.fire({
              icon: 'error',
              title: '❌ Dados inválidos',
              text: 'Preencha corretamente nome e telefone.',
              confirmButtonColor: '#0d6efd'
            });
          } else {
            alert('Preencha corretamente nome e telefone.');
          }
          return;
        }
        
        // Erro inesperado
        if (window.Swal) {
          Swal.fire({
            icon: 'error',
            title: '❌ Erro inesperado',
            html: 'Retorno:<br><pre style="white-space:pre-wrap">' + texto + '</pre>',
            confirmButtonColor: '#0d6efd'
          });
        } else {
          alert('Erro: ' + texto);
        }
      },
      error: function() {
        if (window.Swal) {
          Swal.fire({
            icon: 'error',
            title: '❌ Falha de rede',
            text: 'Não foi possível enviar os dados.',
            confirmButtonColor: '#0d6efd'
          });
        } else {
          alert('Falha de rede.');
        }
      },
      complete: function() {
        $btn.prop('disabled', false).text(oldText);
      }
    });
  });
});
</script>

<!-- ============= SINCRONIZAÇÃO HEADER SPACER ============= -->
<script>
jQuery(document).ready(function($) {
  function syncSpacer() {
    var $header = $('.header_section');
    var $spacer = $('#header-spacer');
    if ($header.length && $spacer.length) {
      $spacer.css('height', $header.outerHeight() + 'px');
    }
  }
  
  function onScroll() {
    var $header = $('.header_section');
    if ($header.length) {
      if ($(window).scrollTop() > 8) {
        $header.addClass('header--scrolled');
      } else {
        $header.removeClass('header--scrolled');
      }
    }
  }
  
  $(window).on('load resize orientationchange', syncSpacer);
  $(window).on('scroll', onScroll);
  $(document).on('shown.bs.collapse hidden.bs.collapse', syncSpacer);
});
</script>

<!-- ============= FORMULÁRIO RODAPÉ - AJAX ============= -->
<script>
jQuery(document).ready(function($) {
  var $tel = $('#telefone_rodape');
  
  if ($tel.length) {
    $tel.on('input', function() {
      var d = $(this).val().replace(/\D/g, '').slice(0, 11);
      var out = d;
      
      if (d.length > 2) {
        out = '(' + d.slice(0, 2) + ') ' + d.slice(2);
      }
      if (d.length >= 11) {
        out = '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
      } else if (d.length >= 7) {
        out = '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
      }
      
      $(this).val(out);
    });
  }
  
  var $form = $('#form_cadastro');
  if ($form.length === 0) return;
  
  $form.on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
      url: '/cadastrar.php',
      method: 'POST',
      data: $form.serialize(),
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      },
      success: function(resp) {
        var texto = (resp || '').replace(/\s+/g, ' ').trim();
        
        if (texto.indexOf('Cadastrado com Sucesso') === 0) {
          Swal.fire({
            icon: 'success',
            title: '✅ Sucesso!',
            text: 'Cadastro realizado com sucesso.',
            confirmButtonColor: '#DAA520'
          }).then(function() {
            window.location.href = '/sistema/acesso';
          });
        } else if (texto.indexOf('Você já está Cadastrado') === 0) {
          Swal.fire({
            icon: 'warning',
            title: '⚠️ Já cadastrado',
            text: 'Você já tem cadastro.',
            confirmButtonColor: '#DAA520'
          }).then(function() {
            window.location.href = '/sistema/acesso';
          });
        } else if (texto.indexOf('Preencha nome e telefone') === 0) {
          Swal.fire({
            icon: 'error',
            title: '❌ Dados inválidos',
            text: 'Preencha corretamente nome e telefone.',
            confirmButtonColor: '#DAA520'
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: '❌ Erro inesperado',
            html: 'Retorno:<br><pre style="white-space:pre-wrap">' + texto + '</pre>',
            confirmButtonColor: '#DAA520'
          });
        }
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: '❌ Falha de rede',
          text: 'Não foi possível enviar os dados.',
          confirmButtonColor: '#DAA520'
        });
      }
    });
  });
});
</script>

</body>
</html>