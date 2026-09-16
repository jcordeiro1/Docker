<?php require_once("sistema/conexao.php") ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta name="google-site-verification" content="B82YEVl88TdFpPd6yQUg83Pf78QE9BFftNQsPSRgyFw" />
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <!-- SEO básico -->
<title>BarberBot: Agendamento e Gestão para Barbearias</title>
<meta name="description" content="Sistema completo para barbearias: agenda online, confirmações por WhatsApp, pagamentos, relatórios e fidelidade. Organize a equipe e aumente o faturamento.">
<meta name="keywords" content="BarberBot, sistema para barbearia, agendamento barbearia, gestão de barbearia, agenda online, software barbearia, confirmações WhatsApp, pagamentos, relatórios, fidelidade, agenda de barbeiro">
<meta name="robots" content="index,follow">
<link rel="canonical" href="https://barberbot.com.br/">
<meta name="author" content="Jacy Cordeiro">

<!-- Mobile / aparência -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#111827">
<link rel="apple-touch-icon" href="/assets/apple-touch-icon.png">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:locale" content="pt_BR">
<meta property="og:title" content="BarberBot: Agendamento e Gestão para Barbearias">
<meta property="og:description" content="Agenda online, confirmações por WhatsApp, pagamentos e relatórios em um só lugar. Experimente e simplifique sua operação.">
<meta property="og:url" content="https://barberbot.com.br/">
<meta property="og:image" content="https://barberbot.com.br/assets/og-cover.jpg">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="BarberBot: Agendamento e Gestão para Barbearias">
<meta name="twitter:description" content="Agenda online, confirmações por WhatsApp, pagamentos e relatórios em um só lugar.">
<meta name="twitter:image" content="https://barberbot.com.br/assets/og-cover.jpg">

  <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="css/barber.css" rel="stylesheet">
  <script src="js/barber.js" defer></script>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Lotando sua agenda, sem perder a essência  <span class="highlight">Tradicional + Tecnologia</span></h1>
                    <p>Sistema de agendamento pelo WhatsApp, gestão financeira e lembretes automáticos. Menos no-show, mais cortes — aumente o faturamento em até 40% com o BarberBot.</p>
                    <div class="cta-buttons">
                        <a href="https://jc.tec.br/criar-barbearia.php"
   class="btn btn-primary"
   target="_blank" rel="noopener">
  <i class="fas fa-rocket"></i> Começar Teste Grátis
</a>


                        <a href="https://jc.tec.br/agendamentos"
   class="btn btn-secondary"
   target="_blank" rel="noopener">
  <i class="fas fa-play"></i> Ver agendamento
</a>

                    </div>
                    <div class="trust-badges">
                        <div class="trust-badge">
                            <i class="fas fa-check-circle"></i>
                            <span>30 Dias grátis</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Sem cartão</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-star"></i>
                            <span>4.9/5 estrelas</span>
                        </div>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="phone-mockup floating">
                        <div class="phone-frame">
                            <div class="phone-screen">
                                <div class="whatsapp-header">
                                    <i class="fab fa-whatsapp"></i>
                                    <span>Agendamentos BarberBot</span>
                                </div>
                                <div class="chat-messages">
                                    <div class="message received">
                                        <div class="message-bubble">
                                            <p><strong>Cliente:</strong> Bom dia! Gostaria de agendar um horário</p>
                                            <span class="time">09:30</span>
                                        </div>
                                    </div>
                                    <div class="message sent">
                                        <div class="message-bubble">
                                            <p>Olá! Que dia você prefere?</p>
                                            <p>📅 Horários disponíveis hoje:</p>
                                            <p>• 14:00 ✅</p>
                                            <p>• 15:30 ✅</p>
                                            <p>• 17:00 ✅</p>
                                            <span class="time">09:31 ✓✓</span>
                                        </div>
                                    </div>
                                    <div class="message received">
                                        <div class="message-bubble">
                                            <p>Quero às 15:30! 💈</p>
                                            <span class="time">09:32</span>
                                        </div>
                                    </div>
                                    <div class="message sent">
                                        <div class="message-bubble">
                                            <p>✅ Agendamento confirmado!</p>
                                            <p>📅 Hoje às 15:30</p>
                                            <p>💇 Corte + Barba</p>
                                            <p>Te esperamos! 😊</p>
                                            <span class="time">09:32 ✓✓</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Problems Section -->
    <section class="problems" style="margin-top: -60px">
        <div class="container">
            <div class="section-header">
                <h2>Cansado Destes Problemas?</h2>
                <p>Sabemos o quanto é difícil gerenciar uma barbearia sem as ferramentas certas</p>
            </div>
            <div class="problems-grid">
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-phone-slash"></i>
                    </div>
                    <h3>Ligações Perdidas</h3>
                    <p>Clientes ligando fora do horário e você perdendo agendamentos por não conseguir atender todas as ligações.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3>Agenda Bagunçada</h3>
                    <p>Caderninho de papel, mensagens no WhatsApp... Difícil controlar quem agendou e quando.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Sem Controle Financeiro</h3>
                    <p>No final do mês você não sabe exatamente quanto faturou e onde está indo seu dinheiro.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <h3>Clientes Esquecem</h3>
                    <p>Muitos clientes não aparecem porque esqueceram do horário marcado, gerando perda de tempo e dinheiro.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Tempo Desperdiçado</h3>
                    <p>Horas perdidas atendendo ligações, confirmando horários e organizando a agenda manualmente.</p>
                </div>
                <div class="problem-card">
                    <div class="problem-icon">
                        <i class="fas fa-sad-tear"></i>
                    </div>
                    <h3>Clientes Insatisfeitos</h3>
                    <p>Atendimento lento, espera desnecessária e falta de organização afastam seus melhores clientes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features" style="margin-top: -140px">
        <div class="container">
            <div class="section-header">
                <h2>Recursos Que Vão Revolucionar Seu Negócio</h2>
                <p>Tudo que você precisa em um único sistema</p>
            </div>

            <div class="features-grid">
                <div class="feature-image">
                    <div class="phone-feature">
                        <img src="images/barber/agendamento-screen.png" alt="Tela de Agendamento" class="phone-screen-img">
                    </div>
                </div>
                <div class="feature-content">
                    <h3>📅 Agendamento Online 24/7</h3>
                    <p>Seus clientes agendam pelo WhatsApp a qualquer hora, sem precisar ligar ou esperar atendimento. O sistema confirma automaticamente!</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Agenda em tempo real</li>
                        <li><i class="fas fa-check-circle"></i> Confirmação automática via WhatsApp</li>
                        <li><i class="fas fa-check-circle"></i> Lembretes antes do horário</li>
                        <li><i class="fas fa-check-circle"></i> Zero ligações perdidas</li>
                    </ul>
<button type="button" onclick="openVideo()"
        style="background:#2563eb;color:#fff;border:0;border-radius:10px;padding:12px 18px;cursor:pointer;">
  ▶ Assistir vídeo agendamento online 
</button>
                </div>
            </div>

            <div class="features-grid">
                <div class="feature-content">
                    <h3>💰 Controle Financeiro Completo</h3>
                    <p>Saiba exatamente quanto você fatura por dia, semana e mês. Relatórios detalhados para tomar melhores decisões. Simplifique sua gestão financeira com relatórios de vendas, compras, contas a pagar e a receber, recebimentos vencidos e comissões.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Dashboard com faturamento em tempo real</li>
                        <li><i class="fas fa-check-circle"></i> Controle de comissões</li>
                        <li><i class="fas fa-check-circle"></i> Gestão de despesas</li>
                        <li><i class="fas fa-check-circle"></i> Relatórios personalizados</li>
                    </ul>
                    <button type="button" onclick="openVideoFin()"
        style="background:#2563eb;color:#fff;border:0;border-radius:10px;padding:12px 18px;cursor:pointer;">
  ▶ Assistir vídeo de financeiro
</button>
                </div>
                <div class="feature-image">
                    <div class="phone-feature">
                        <img src="images/barber/financeiro-screen.png" alt="Tela Financeiro" class="phone-screen-img">
                    </div>
                </div>
            </div>

            <div class="features-grid">
                <div class="feature-image">
                    <div class="phone-feature">
                        <img src="images/barber/marketing-screen.png" alt="Tela Marketing WhatsApp" class="phone-screen-img">
                    </div>
                </div>
                <div class="feature-content">
                    <h3>🤖 Marketing Automático via WhatsApp</h3>
                    <p>O sistema envia mensagens automáticas de lembretes, promoções e recupera clientes inativos. Tudo no piloto automático!</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Lembretes automáticos de agendamento</li>
                        <li><i class="fas fa-check-circle"></i> Recuperação de clientes inativos</li>
                        <li><i class="fas fa-check-circle"></i> Promoções segmentadas</li>
                        <li><i class="fas fa-check-circle"></i> Mensagens de aniversário</li>
                    </ul>
                    <button type="button" onclick="openVideoFin2()"
        style="background:#2563eb;color:#fff;border:0;border-radius:10px;padding:12px 18px;cursor:pointer;">
  ▶ Assistir vídeo de mrketing
</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Grid -->
    <section class="benefits" style="margin-top: -160px">
        <div class="container">
            <div class="section-header">
                <h2>Benefícios Reais Para Seu Negócio</h2>
            </div>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">⚡</div>
                    <h4>Economize 10h/Semana</h4>
                    <p>Pare de perder tempo com ligações e organize sua agenda automaticamente</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">📈</div>
                    <h4>+40% de Faturamento</h4>
                    <p>Mais agendamentos, menos faltas e melhor gestão = mais lucro</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">😊</div>
                    <h4>Clientes Mais Felizes</h4>
                    <p>Atendimento profissional e organizado fideliza seus clientes</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">📱</div>
                    <h4>Tudo no Celular</h4>
                    <p>Gerencie seu negócio de qualquer lugar, a qualquer hora</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🎯</div>
                    <h4>Zero Faltas</h4>
                    <p>Lembretes automáticos reduzem faltas em até 80%</p>
                </div>
                <div class="benefit-card">
                    <div class="benefit-icon">🔒</div>
                    <h4>Dados Seguros</h4>
                    <p>Nunca mais perca informações de clientes em cadernos</p>
                </div>
            </div>
        </div>
    </section>
    
        <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">5000</div>
                    <div class="stat-label">Barbearias Ativas</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">500000</div>
                    <div class="stat-label">Agendamentos/Mês</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">40</div>
                    <div class="stat-label">% Aumento em Lucros</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98</div>
                    <div class="stat-label">% Satisfação</div>
                </div>
            </div>
        </div>
    </section>



    <!-- ===================== CLIENTS ===================== -->
<section class="clients" id="clientes">
  <div class="container">
    <div class="section-header">
      <h2> Resultados reais, projetos que falam por si!</h2>
      <p>Veja o que já entreguei com excelência: soluções práticas, criativas e que geram impacto — tudo feito por Jacy Cordeiro, com foco total no sucesso do cliente.</p>
    </div>

    <!-- Carrossel de Clientes -->
    <div class="clients-carousel">
      <div class="clients-slider" id="clients-slider">

        <!-- setas internas -->
        <button class="clients-arrow prev" id="clients-prev" aria-label="Anterior">‹</button>
        <button class="clients-arrow next" id="clients-next" aria-label="Próximo">›</button>

        <div class="clients-track" id="clients-track">
          <!-- 1 -->
          <div class="client-slide">
            <a href="https://barberbot.com.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/barberbot.png" alt="Cliente 1" loading="lazy">
            </a>
          </div>
          <!-- 2 -->
          <div class="client-slide">
            <a href="https://controleautomacao.com.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/controle.png" alt="Cliente 2" loading="lazy">
            </a>
          </div>
          <!-- 3 -->
          <div class="client-slide">
            <a href="https://biotech.tec.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/biotec.png" alt="Cliente 3" loading="lazy">
            </a>
          </div>
          <!-- 4 -->
          <div class="client-slide">
            <a href="https://brinex.com.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/brinex.png" alt="Cliente 4" loading="lazy">
            </a>
          </div>
          <!-- 5 -->
          <div class="client-slide">
            <a href="https://casadosfogoes.jc.tec.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/casa-fogoes.png" alt="Cliente 5" loading="lazy">
            </a>
          </div>
          <!-- 6 -->
          <div class="client-slide">
            <a href="https://ivanicordeiro.com.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images//barber/ivani.png" alt="Cliente 6" loading="lazy">
            </a>
          </div>
          <!-- 7 -->
          <div class="client-slide">
            <a href="https://jacycabeleireiro.com/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/jacy-cabeleireiro.jpg" alt="Cliente 7" loading="lazy">
            </a>
          </div>
          <!-- 8 -->
          <div class="client-slide">
            <a href="https://jacycordeiro.com.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/jacy-cordeiro.jpg" alt="Cliente 8" loading="lazy">
            </a>
          </div>
        </div>
      </div>

      <!-- Indicadores (opcional) -->
      <div class="carousel-indicators" id="clients-indicators"></div>
    </div>

    <!-- Fallback (sem JS) -->
    <noscript>
      <div class="clients-grid">
        <a href="https://barberbot.com.br/" target="_blank" class="client-logo" rel="noopener">
          <img src="images/barber/barberbot.png" alt="Cliente 1">
        </a>
        <a href="https://controleautomacao.com.br/" target="_blank" class="client-logo" rel="noopener">
          <img src="images/clients/barber/controle.png" alt="Cliente 2">
        </a>
        <a href="https://biotech.tec.br/" target="_blank" class="client-logo" rel="noopener">
          <img src="images/barber/biotec.png" alt="Cliente 3">
        </a>
        <a href="https://brinex.com.br/" target="_blank" class="client-logo" rel="noopener">
          <img src="images//barber/brinex.png" alt="Cliente 4">
        </a>
            <a href="https://casadosfogoes.jc.tec.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/casa-fogoes.png" alt="Cliente 5" loading="lazy">
            </a>
            <a href="https://ivanicordeiro.com.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images//barber/ivani.png" alt="Cliente 6" loading="lazy">
            </a>
            <a href="https://jacycabeleireiro.com/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/jacy-cabeleireiro.jpg" alt="Cliente 7" loading="lazy">
            </a>
        <a href="https://jacycordeiro.com.br/" target="_blank" class="client-logo" rel="noopener">
              <img src="images/barber/jacy-cordeiro.jpg" alt="Cliente 8" loading="lazy">
            </a>
      </div>
    </noscript>
  </div>
</section>

<!-- ===================== TESTIMONIALS ===================== -->
<section class="testimonials" id="depoimentos" style="margin-top: -80px">
  <div class="container">
    <div class="section-header">
      <h2>O Que Dizem Nossos Clientes</h2>
      <p>Depoimentos reais de quem já transformou seu negócio</p>
    </div>

    <div class="testimonials-container">
      <!-- Slider -->
      <div class="testimonials-slider">
        <div class="testimonials-track" id="testimonials-track">
          <!-- Slide 1 -->
          <div class="testimonial-slide">
            <div class="testimonial-card">
              <div class="quote-icon"><i class="fas fa-quote-right"></i></div>
              <p class="testimonial-text">"Depois do BarberBot meu faturamento aumentou 45%! Agora consigo atender muito mais clientes e não perco mais ligações. Melhor investimento que fiz!"</p>
              <div class="testimonial-author">
                <div class="author-avatar">RC</div>
                <div class="author-info">
                  <h5>Roberto Costa</h5>
                  <p>Barbearia Premium - SP</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="testimonial-slide">
            <div class="testimonial-card">
              <div class="quote-icon"><i class="fas fa-quote-right"></i></div>
              <p class="testimonial-text">"Sistema perfeito! Meus clientes adoram agendar pelo WhatsApp. Economizo mais de 10 horas por semana que antes gastava no telefone."</p>
              <div class="testimonial-author">
                <div class="author-avatar">MS</div>
                <div class="author-info">
                  <h5>Marcos Silva</h5>
                  <p>Barbearia Estilo - RJ</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 3 -->
          <div class="testimonial-slide">
            <div class="testimonial-card">
              <div class="quote-icon"><i class="fas fa-quote-right"></i></div>
              <p class="testimonial-text">"Profissional de verdade! Consigo ver todos os números do meu negócio e tomar decisões muito melhores. Recomendo demais!"</p>
              <div class="testimonial-author">
                <div class="author-avatar">AP</div>
                <div class="author-info">
                  <h5>André Pereira</h5>
                  <p>Salão Masculino - MG</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 4 -->
          <div class="testimonial-slide">
            <div class="testimonial-card">
              <div class="quote-icon"><i class="fas fa-quote-right"></i></div>
              <p class="testimonial-text">"Revolucionou minha barbearia! Agora tenho controle total e meus clientes adoram a facilidade de agendar. Vale cada centavo!"</p>
              <div class="testimonial-author">
                <div class="author-avatar">JM</div>
                <div class="author-info">
                  <h5>João Mendes</h5>
                  <p>Barbearia Moderna - BA</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 5 -->
          <div class="testimonial-slide">
            <div class="testimonial-card">
              <div class="quote-icon"><i class="fas fa-quote-right"></i></div>
              <p class="testimonial-text">"Incrível! Minha agenda sempre cheia e zero faltas. O WhatsApp automático é sensacional. Recomendo para todos!"</p>
              <div class="testimonial-author">
                <div class="author-avatar">PL</div>
                <div class="author-info">
                  <h5>Paulo Lima</h5>
                  <p>Espaço Masculino - RS</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Slide 6 -->
          <div class="testimonial-slide">
            <div class="testimonial-card">
              <div class="quote-icon"><i class="fas fa-quote-right"></i></div>
              <p class="testimonial-text">"Melhor custo-benefício! Sistema completo, fácil de usar e meus clientes adoram. Aumentei 30% o faturamento em 3 meses!"</p>
              <div class="testimonial-author">
                <div class="author-avatar">FS</div>
                <div class="author-info">
                  <h5>Fernando Santos</h5>
                  <p>Barbearia Elite - PR</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- Indicadores -->
      <div class="carousel-indicators" id="indicators"></div>
    </div>
  </div>
</section>

    <!-- Pricing Section -->
    <section class="pricing" id="pricing">
        <div class="container">
            <h2>Planos Para Todos os Tamanhos</h2>
            
            <div class="pricing-grid">
                <!-- Card 1: Completo Mensal -->
                <div class="pricing-card">
                    <div class="ribbon">15% DESCONTO</div>
                    <div class="plan-name">Completo Mensal</div>
                    <div class="plan-price">R$ 97<span>/mês</span></div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> AGENDAMENTOS ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> PROFISSIONAL ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> DOMÍNIO PERSONALIZADO</li>
                        <li><i class="fas fa-check"></i> CLIENTES ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> CAMPANHA MARKETING </li>
                        <li><i class="fas fa-check"></i> ASSINATURA</li>
                        <li><i class="fas fa-check"></i> LANDING PAGE</li>
                        <li><i class="fas fa-check"></i> API'S PAGA</li>
                    </ul>
                    <button type="button" class="btn btn-primary"
        onclick="window.open('https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c9380848823e75101883076507c04fe','_blank')">
  ÚLTIMOS DIAS DE DESCONTO
</button>
                    <p style="margin-top: 15px; font-size: 0.85rem; color: #64748b;">TESTAR POR 30 DIAS GRÁTIS</p>
                </div>

                <!-- Card 2: Completo Anual -->
                <div class="pricing-card featured">
                    <div class="ribbon">15% DESCONTO</div>
                    <div class="plan-name">Completo Anual</div>
                    <div class="plan-price">R$ 997<span>/anual</span></div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> AGENDAMENTOS ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> PROFISSIONAL ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> DOMÍNIO PERSONALIZADO</li>
                        <li><i class="fas fa-check"></i> CLIENTES ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> CAMPANHA MARKETING </li>
                        <li><i class="fas fa-check"></i> ASSINATURA</li>
                        <li><i class="fas fa-check"></i> LANDING PAGE</li>
                        <li><i class="fas fa-check"></i> API'S PAGA</li>
                    </ul>
                    <button type="button" class="btn btn-primary"
        onclick="window.open('https://mpago.li/1K3CJ3b','_blank')">
  ÚLTIMOS DIAS DE DESCONTO
</button>
                    <p style="margin-top: 15px; font-size: 0.85rem; color: #64748b;">TESTAR POR 30 DIAS GRÁTIS</p>
                </div>
            </div>

            <div class="pricing-grid" style="margin-top: 30px;">
                <!-- Card 3: Ilimitado Anual -->
                <div class="pricing-card">
                    <div class="ribbon">15% DESCONTO</div>
                    <div class="plan-name">Ilimitado Anual</div>
                    <div class="plan-price">R$ 690<span>/anual</span></div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> AGENDAMENTOS ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> PROFISSIONAL ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> CLIENTES ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> LANDING PAGE</li>
                        <li><i class="fas fa-check"></i> SUB-DOMÍNIO</li>
                        <li><i class="fas fa-check"></i> API'S PAGA</li>
                    </ul>
                    <button type="button" class="btn btn-primary"
        onclick="window.open('https://mpago.la/1HnX1B2','_blank')">
  ÚLTIMOS DIAS DE DESCONTO
</button>
                    <p style="margin-top: 15px; font-size: 0.85rem; color: #64748b;">TESTAR POR 30 DIAS GRÁTIS</p>
                </div>

                <!-- Card 4: Ilimitado Mensal -->
                <div class="pricing-card featured">
                    <div class="ribbon">15% DESCONTO</div>
                    <div class="plan-name">Ilimitado Mensal</div>
                    <div class="plan-price">R$ 69<span>/mês</span></div>
                    <ul class="plan-features">
                        <li><i class="fas fa-check"></i> AGENDAMENTOS ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> PROFISSIONAL ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> CLIENTES ILIMITADOS</li>
                        <li><i class="fas fa-check"></i> LANDING PAGE</li>
                        <li><i class="fas fa-check"></i> SUB-DOMÍNIO</li>
                        <li><i class="fas fa-check"></i> API'S PAGA</li>
                    </ul>
                    <button type="button" class="btn btn-primary"
        onclick="window.open('https://www.mercadopago.com.br/subscriptions/checkout?preapproval_plan_id=2c938084896dba32018970f8fc8e01c1','_blank')">
  ÚLTIMOS DIAS DE DESCONTO
</button>

                    <p style="margin-top: 15px; font-size: 0.85rem; color: #64748b;">TESTAR POR 30 DIAS GRÁTIS</p>
                </div>
            </div>
        </div>
    </section>


    <!-- CTA Final -->
    <section class="cta-final">Benefícios Reais Para Seu Negócio
        <div class="container">
            <h2>Pronto Para Transformar Sua Barbearia?</h2>
            <p>Comece seu teste grátis agora. Sem cartão de crédito. Cancele quando quiser.</p>
            <a href="https://jc.tec.br/criar-barbearia.php"
   class="btn btn-primary"
   style="font-size: 1.3rem; padding: 20px 50px;"
   target="_blank" rel="noopener noreferrer">
  <i class="fas fa-rocket"></i> Começar Teste Grátis Por 30 Dias
</a>

            <p style="margin-top: 20px; font-size: 0.9rem;">✅ Configuração em 5 minutos • ✅ Suporte em português • ✅ Sem compromisso</p>
        </div>
    </section>

<!-- ===================== FOOTER (modelo do print) ===================== -->
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <!-- Coluna 1: Marca + descrição -->
      <div class="footer-brand">
  <div class="brand-row">
    <img src="/sistema/img/logo.png" alt="Software" width="120" height="auto">
    <h3 class="brand-title">BarberBot software</h3>
  </div>
        <p class="brand-text">Gerenciar uma barbearia pode ser desafiador, mas e se você pudesse simplificar tudo, aumentar seus lucros e encantar seus clientes? Apresentamos o BarberBot Software, a solução completa pensada para o sucesso do seu negócio.
        </p>
      </div>

      <!-- Coluna 2: Links de contato -->
      <div class="footer-links-col">
        <h4 class="footer-col-title">LINKS CONTATOS</h4>
        <ul class="footer-links-list">
          <li>
            <i class="fa-solid fa-location-dot"></i>
            Rio Grande do Sul, 2151
          </li>
          <li>
            <a href="/assinatura">
              <i class="fa-regular fa-id-card"></i>
              Assinatura
            </a>
          </li>
          <li>
            <a href="/servicos">
              <i class="fa-solid fa-list-check"></i>
              Serviços
            </a>
          </li>
          <li>
            <a href="/barbearia">
              <i class="fa-solid fa-user"></i>
              Barbeiro
            </a>
          </li>
          <li>
            <a href="/protese-capilar">
              <i class="fa-solid fa-user-shield"></i>
              Prótese
            </a>
          </li>
        </ul>
      </div>

<!-- Coluna 3: Form lead -->
<div class="footer-form">
  <h4 class="footer-col-title">CADASTRE-SE</h4>
  <p class="muted">Solicite uma avaliação gratuita</p>

  <form id="footerLead" action="/cadastrar.php" method="post" autocomplete="off" novalidate>
    <input type="hidden" name="redirect" value="1">

    <div class="mb-2">
      <input
        type="tel"
        class="footer-input"
        name="whatsapp"
        id="footerLeadZap"
        placeholder="(45) 99999-0000"
        inputmode="numeric"
        maxlength="16"
        pattern="\(?\d{2}\)?\s?\d{4,5}-?\d{4}"
        title="Informe um WhatsApp válido no formato (DD) 99999-9999"
        required>
    </div>

    <div class="mb-2">
      <input
        type="text"
        class="footer-input"
        name="nome"
        placeholder="Nome Completo"
        maxlength="80"
        required>
    </div>

    <button type="submit" class="btn btn-primary footer-btn">Cadastrar</button>
  </form>
  </div>
  </div>
  </div>

  <!-- Barra inferior -->
  <div class="footer-bottom">
    <div class="container bottom-row">
      <div class="bottom-left">
        © 2025
        <a href="/politica-de-privacidade">Política de privacidade</a>
        <span class="dot">•</span>
        <a href="/termos-de-uso">Termos de uso</a>
        <span class="dot">•</span>
        <a href="/app">App</a>
      </div>

      <div class="bottom-right">
        BarberBot software
         <span class="dot">•</span>
        (45) 99958–0058
         <span class="dot">•</span>
          <a title="Ir para o sistema" href="sistema" style="color:#FFF;" target="_blank">
            <i class="fa fa-user" aria-hidden="true"></i>
      </div>
    </div>
  </div>
</footer>

<!-- Modal agenda-->
<div id="ytModal"
     style="position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;align-items:center;justify-content:center;z-index:99999;">
  <!-- caixa do vídeo responsiva: 96% da tela, máx 900px, 16:9 -->
  <div style="position:relative;width:96vw;max-width:900px;background:#000;border-radius:12px;overflow:hidden;">
    <button id="closeYt" aria-label="Fechar"
            style="position:absolute;top:8px;right:8px;border:0;background:rgba(255,255,255,.15);color:#fff;padding:6px 10px;border-radius:6px;cursor:pointer;z-index:1;">
      ✕
    </button>
    <!-- wrapper 16:9 -->
    <div style="position:relative;width:100%;padding-top:56.25%;">
      <div id="ytPlayer" style="position:absolute;inset:0;"></div>
    </div>
  </div>
</div>
<!-- Modal (Financeiro) -->
<div id="ytModalFin"
     style="position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;align-items:center;justify-content:center;z-index:99999;">
  <!-- caixa do vídeo responsiva: 96% da tela, máx 900px, 16:9 -->
  <div style="position:relative;width:96vw;max-width:900px;background:#000;border-radius:12px;overflow:hidden;">
    <button id="closeYtFin" aria-label="Fechar"
            style="position:absolute;top:8px;right:8px;border:0;background:rgba(255,255,255,.15);color:#fff;padding:6px 10px;border-radius:6px;cursor:pointer;z-index:1;">
      ✕
    </button>
    <!-- wrapper 16:9 -->
    <div style="position:relative;width:100%;padding-top:56.25%;">
      <div id="ytPlayerFin" style="position:absolute;inset:0;"></div>
    </div>
  </div>
</div>

<!-- Modal Marketing -->
<div id="ytModalFin2"
     style="position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;align-items:center;justify-content:center;z-index:99999;">
  <div style="position:relative;width:96vw;max-width:900px;background:#000;border-radius:12px;overflow:hidden;">
    <button id="closeYtFin2" aria-label="Fechar"
            style="position:absolute;top:8px;right:8px;border:0;background:rgba(255,255,255,.15);color:#fff;padding:6px 10px;border-radius:6px;cursor:pointer;z-index:1;">
      ✕
    </button>
    <div style="position:relative;width:100%;padding-top:56.25%;">
      <div id="ytPlayerFin2" style="position:absolute;inset:0;"></div>
    </div>
  </div>
</div>
<!-- API do agenda -->
<script src="https://www.youtube.com/iframe_api"></script>
<script>
  let ytPlayer;
  const ytModal = document.getElementById('ytModal');
  const closeBtn = document.getElementById('closeYt');

  // Carrega player no div #ytPlayer
  function onYouTubeIframeAPIReady() {
    ytPlayer = new YT.Player('ytPlayer', {
      videoId: 'JJUjGGbgg1Q',                // seu vídeo
      playerVars: {
        autoplay: 0,
        rel: 0,
        controls: 1,                         // 0 se quiser sem controles
        modestbranding: 1,
        playsinline: 1                       // iOS abre inline
      },
      events: {
        'onReady': (e) => {
          // força o iframe a ocupar 100% (inline)
          const ifr = e.target.getIframe();
          ifr.style.position = 'absolute';
          ifr.style.top = '0';
          ifr.style.left = '0';
          ifr.style.width = '100%';
          ifr.style.height = '100%';
        },
        'onStateChange': (e) => {
          // fecha quando termina
          if (e.data === YT.PlayerState.ENDED) closeModal();
        }
      }
    });
  }

  function openVideo() {
    ytModal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    if (ytPlayer && ytPlayer.playVideo) ytPlayer.playVideo();
  }

  function closeModal() {
    ytModal.style.display = 'none';
    document.body.style.overflow = '';
    if (ytPlayer && ytPlayer.stopVideo) ytPlayer.stopVideo();
  }

  closeBtn.onclick = closeModal;
  ytModal.addEventListener('click', (ev) => { if (ev.target === ytModal) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
</script>

<!-- API de financeiro -->
<script src="https://www.youtube.com/iframe_api"></script>
<script>
  // Config deste vídeo
  const VIDEO_ID_FIN = '26ZB20BR0E4';

  let ytPlayerFin;
  const ytModalFin = document.getElementById('ytModalFin');
  const closeBtnFin = document.getElementById('closeYtFin');

  // cria o player quando a API estiver pronta (sem depender do callback global)
  function createFinPlayer(onReadyCb){
    if (ytPlayerFin) { if (onReadyCb) onReadyCb(); return; }
    if (window.YT && YT.Player){
      ytPlayerFin = new YT.Player('ytPlayerFin', {
        videoId: VIDEO_ID_FIN,
        playerVars: {
          autoplay: 0,
          rel: 0,
          controls: 1,          // troque para 0 se quiser sem controles
          modestbranding: 1,
          playsinline: 1
        },
        events: {
          onReady: (e) => {
            // força o iframe a ocupar 100% da área
            const ifr = e.target.getIframe();
            ifr.style.position = 'absolute';
            ifr.style.top = '0';
            ifr.style.left = '0';
            ifr.style.width = '100%';
            ifr.style.height = '100%';
            if (onReadyCb) onReadyCb();
          },
          onStateChange: (e) => {
            // fecha quando o vídeo termina
            if (e.data === YT.PlayerState.ENDED) closeModalFin();
          }
        }
      });
    } else {
      // espera a API carregar
      setTimeout(() => createFinPlayer(onReadyCb), 100);
    }
  }

  function openVideoFin() {
    ytModalFin.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    createFinPlayer(() => {
      // garante que sempre carregue o vídeo correto
      try {
        ytPlayerFin.loadVideoById(VIDEO_ID_FIN);
      } catch(_) { /* no-op */ }
      if (ytPlayerFin && ytPlayerFin.playVideo) ytPlayerFin.playVideo();
    });
  }

  function closeModalFin() {
    ytModalFin.style.display = 'none';
    document.body.style.overflow = '';
    if (ytPlayerFin && ytPlayerFin.stopVideo) ytPlayerFin.stopVideo();
  }

  // fechar: botão, overlay, ESC
  closeBtnFin.onclick = closeModalFin;
  ytModalFin.addEventListener('click', (ev) => { if (ev.target === ytModalFin) closeModalFin(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModalFin(); });
</script>

<!-- API de Marketing-->
<!-- <script src="https://www.youtube.com/iframe_api"></script> -->
<script>
  // Vídeo deste bloco (troque aqui se quiser outro)
  const VIDEO_ID_FIN2 = 'oSQMhpK31o8';

  let ytPlayerFin2;
  const ytModalFin2 = document.getElementById('ytModalFin2');
  const closeBtnFin2 = document.getElementById('closeYtFin2');

  function createFin2Player(onReadyCb){
    if (ytPlayerFin2) { if (onReadyCb) onReadyCb(); return; }
    if (window.YT && YT.Player){
      ytPlayerFin2 = new YT.Player('ytPlayerFin2', {
        videoId: VIDEO_ID_FIN2,
        playerVars: {
          autoplay: 0,
          rel: 0,
          controls: 1,
          modestbranding: 1,
          playsinline: 1
        },
        events: {
          onReady: (e) => {
            const ifr = e.target.getIframe();
            ifr.style.position = 'absolute';
            ifr.style.top = '0';
            ifr.style.left = '0';
            ifr.style.width = '100%';
            ifr.style.height = '100%';
            if (onReadyCb) onReadyCb();
          },
          onStateChange: (e) => {
            if (e.data === YT.PlayerState.ENDED) closeModalFin2();
          }
        }
      });
    } else {
      setTimeout(() => createFin2Player(onReadyCb), 100);
    }
  }

  function openVideoFin2() {
    ytModalFin2.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    createFin2Player(() => {
      try { ytPlayerFin2.loadVideoById(VIDEO_ID_FIN2); } catch(_) {}
      if (ytPlayerFin2 && ytPlayerFin2.playVideo) ytPlayerFin2.playVideo();
    });
  }

  function closeModalFin2() {
    ytModalFin2.style.display = 'none';
    document.body.style.overflow = '';
    if (ytPlayerFin2 && ytPlayerFin2.stopVideo) ytPlayerFin2.stopVideo();
  }

  closeBtnFin2.onclick = closeModalFin2;
  ytModalFin2.addEventListener('click', (ev) => { if (ev.target === ytModalFin2) closeModalFin2(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModalFin2(); });
</script>
 <!-- Structured Data (Schema.org) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "BarberBot",
  "applicationCategory": "BusinessApplication",
  "operatingSystem": "Web",
  "url": "https://barberbot.com.br/",
  "description": "Sistema de agendamento e gestão para barbearias com agenda online, confirmações por WhatsApp, pagamentos, relatórios e programa de fidelidade.",
  "brand": { "@type": "Brand", "name": "BarberBot" }
}
</script>

<script>
(function(){
  const form  = document.getElementById('footerLead');
  const input = document.getElementById('footerLeadZap');

  if(!input || !form) return;

  function onlyDigits(s){ return (s||'').replace(/\D+/g,''); }

  function maskBRPhone(v){
    const d = onlyDigits(v).slice(0, 11); // até 11 dígitos (DD + 9)
    if(d.length <= 10){
      // (99) 9999-9999
      const p1 = d.slice(0,2);
      const p2 = d.slice(2,6);
      const p3 = d.slice(6,10);
      return (p1 ? `(${p1}` : '')
           + (p1.length === 2 ? ') ' : '')
           + p2
           + (p3 ? `-${p3}` : '');
    }else{
      // (99) 99999-9999
      const p1 = d.slice(0,2);
      const p2 = d.slice(2,7);
      const p3 = d.slice(7,11);
      return (p1 ? `(${p1}` : '')
           + (p1.length === 2 ? ') ' : '')
           + p2
           + (p3 ? `-${p3}` : '');
    }
  }

  function applyMask(){
    const masked = maskBRPhone(input.value);
    input.value = masked;
  }

  // Aplica máscara em tempo real
  ['input','blur','change','paste'].forEach(ev => {
    input.addEventListener(ev, applyMask, { passive: true });
  });

  // Validação mínima no submit (10 ou 11 dígitos)
  form.addEventListener('submit', function(e){
    const digits = onlyDigits(input.value);
    if(digits.length < 10){
      e.preventDefault();
      input.setCustomValidity('Informe um WhatsApp válido com DDD.');
      input.reportValidity();
      input.focus();
    }else{
      input.setCustomValidity('');
    }
  });
})();
</script>
</body>
</html>