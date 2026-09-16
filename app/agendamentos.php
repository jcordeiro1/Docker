<?php
session_start();
require_once __DIR__ . "/sistema/conexao.php";

foreach ($sec_paths as $sec_path) {
    if (file_exists($sec_path)) {
        require_once $sec_path;
        break;
    }
}

date_default_timezone_set('America/Sao_Paulo');
$data_atual = date('Y-m-d');

unset($_SESSION['usuario_logado_pagina']);
$_SESSION['usuario_logado_pagina'] = true;

// VERIFICA SE EXISTE DADOS DE EDIÇÃO
$dados = $_SESSION['editar_agendamento'] ?? [];

// NÃO gravar sessão em arquivo público (risco de vazamento)
// file_put_contents('debug_editar.txt', print_r($_SESSION, true));

$id          = isset($dados['id']) ? (int)$dados['id'] : 0;
$servico     = $dados['servico'] ?? '';
$funcionario = $dados['funcionario'] ?? '';
$data        = $dados['data'] ?? $data_atual;
$hora        = $dados['hora'] ?? '';

/// EXCLUI AGENDAMENTO ANTIGO QUANDO ENTRAR EM MODO DE EDIÇÃO
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}

// Guarda telefone na sessão (sanitizado) na primeira vez
if (!isset($_SESSION['telefone'])) {
    $tel_post = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $_SESSION['telefone'] = $tel_post !== null ? $tel_post : '';
}

$tel_agd = $_SESSION['telefone'] ?? '';
$tem_agendamento = false;

if ($tel_agd !== '') {
    $query_agd = $pdo->prepare("SELECT id FROM clientes WHERE telefone = :tel");
    $query_agd->bindValue(":tel", $tel_agd, PDO::PARAM_STR);
    $query_agd->execute();
    $res_cli = $query_agd->fetch(PDO::FETCH_ASSOC);

    if ($res_cli) {
        $id_cliente = (int)$res_cli['id'];

        $query_agd2 = $pdo->prepare("
            SELECT id 
              FROM agendamentos 
             WHERE cliente = :id 
               AND status = 'Agendado'
        ");
        $query_agd2->bindValue(":id", $id_cliente, PDO::PARAM_INT);
        $query_agd2->execute();

        if ($query_agd2->rowCount() > 0) {
            $tem_agendamento = true;
        }
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
<meta charset="utf-8">  
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#0e1720">

<!-- Site Metas -->
<meta name="keywords" content="Cabeleireiro especialista em calvície. Prótese capilar personalizada. Corte de cabelo masculino e feminino. Barbeiro de confiança em Cascavel Paraná. Tratamento para calvície. Designer de barba e cabelo. Penteados para eventos. Salão de beleza em Cascavel Paraná. Consultoria em prótese capilares. Jacy Cordeiro cabeleireiro">
<meta name="description" content="Jacy Cordeiro: cabeleireiro, barbeiro e especialista em prótese capilar com mais de 20 anos de experiência. Transforme seu visual com cortes modernos, tratamento de calvície e próteses capilares personalizadas. Atendimento de confiança e resultados incríveis!" />
<meta name="author" content="Jacy Cordeiro" />
<title><?php echo $nome_sistema ?></title>

<link rel="manifest" href="_manifest.json">
<link rel="apple-touch-icon" sizes="180x180" href="images/favicon_192.png">
<link rel="icon" type="image/png" href="images/favicon_192.png" sizes="32x32">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" type="text/css" href="css/agendamento.css">
<link rel="stylesheet" type="text/css" href="app/css/style.css">  
<link rel="stylesheet" type="text/css" href="app/css/bootstrap.css">

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">  
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;600;700;800&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,900" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<!-- ===== Ajustes visuais para remover espaço e aplicar dark padrão ===== -->
<style>
  :root {
    --bg-dark: #0e1720;          /* fundo base (escuro) */
    --panel-dark: #1b2733;       /* painéis/containers */
    --input-dark: #0f2233;       /* campo de busca */
    --input-border: #254055;
  }

  html, body { height: 100%; }

  /* Remove margens padrão que criam a “faixa” branca */
  body {
    margin: 0;
    background: var(--bg-dark);
  }

  /* Barra de busca fixa no topo, acima de tudo */
  #filtro-servicos-container{
    position: fixed !important;
    top: 0;
    left: 0;
    right: 0;
    padding: 10px 16px;
    z-index: 1090;               /* acima de conteúdo e carousels */
    background: rgba(14,23,32,0.75);
    backdrop-filter: saturate(130%) blur(4px);
  }

  /* Campo de busca escuro (padrão do sistema) */
  #filtro-servicos{
    background-color: var(--input-dark) !important;
    border: 1px solid var(--input-border) !important;
    color: #e8f0fe !important;
  }

  #filtro-servicos::placeholder{
    color:#9bb4c9;
  }

  /* Conteúdo ganha só um pequeno padding para não ficar escondido atrás do buscador */
  #page-content{
    padding-top: 0px;  /* antes estava 72px */
  }

  /* Remove o espaço extra que vinha do inline style (margin-top: 40px) */
  .footer_section{
    background: transparent !important;
    margin-top: 0 !important;
  }

  /* Corrige qualquer espaçamento superior acidental em containers raiz */
  .container,
  .row {
    margin-top: 0 !important;
  }
</style>

</head>


<body>
<!-- SPLASH -->
<div id="splash" style="
  position:fixed;
  inset:0;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#0e1720;
  z-index:99999;
">
  <div class="wrap" style="text-align:center;">
    <img class="logo" src="sistema/img/icone.png" alt="Logo" width="412" height="412">
    <div class="title"><?php echo $nome_sistema ?></div>
    <div class="bar"></div>
    <div class="tip">Carregando…</div>
  </div>
</div>

<!-- Filtro flutuante (fixo por cima) -->
<div id="filtro-servicos-container" class="w-100 d-flex justify-content-center">
  <div class="position-relative" style="width: 320px;">
    <input
      type="text"
      id="filtro-servicos"
      class="form-control shadow"
      placeholder="Buscar serviço..."
      autocomplete="off"
      spellcheck="false"
      style="padding-right: 40px; border-radius: 12px;"
    >
    <i class="fas fa-search text-primary" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
  </div>
</div>
<!-- Wrapper do conteúdo para compensar a barra fixa -->
<div id="page-content">

<div class="footer_section" style="background: transparent;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card-header text-center mb-4">
          <p class="text-white-50">Siga as etapas abaixo para agendar seu atendimento</p>
        </div>

        <!-- Indicadores de Etapa -->
        <div class="step-indicator">
          <div class="step active" id="step-1">
            1
            <span class="step-title">Serviço</span>
          </div>
          <div class="step-line"></div>
          <div class="step" id="step-2">
            2
            <span class="step-title">Profissional</span>
          </div>
          <div class="step-line"></div>
          <div class="step" id="step-3">
            3
            <span class="step-title">Data e Hora</span>
          </div>
          <div class="step-line"></div>
          <div class="step" id="step-4">
            4
            <span class="step-title">Seus Dados</span>
          </div>
        </div>

        <form class="form_agendamento" id="form-agenda" method="post">
          <div class="footer_form footer-col">

            <!-- Etapa 1: Escolha do Serviço -->
            <div class="form-step active" id="step-1-content">
              <div class="step-content">
                <h4 class="text-white mb-4 text-center">Escolha o Serviço Desejado</h4>

                <!-- Cards de serviços -->
                <div class="service-cards" id="service-cards-container">
                  <!-- Os cards de serviço serão carregados aqui via JavaScript -->
                  <div class="text-center w-100 p-5">
                    <div class="spinner-border text-info" role="status"></div>
                    <p class="mt-3 text-white-50">Carregando serviços disponíveis...</p>
                  </div>
                </div>

                <!-- Campo oculto para armazenar o serviço selecionado -->
                <select id="servico" name="servico" style="display: none;" required>
                  <option value="">Selecione um Serviço</option>
                  <?php 
                  $query = $pdo->query("SELECT * FROM servicos WHERE ativo = 'Sim' ORDER BY nome ASC");
                  $res = $query->fetchAll(PDO::FETCH_ASSOC);
                  $total_reg = @count($res);                  
                  if($total_reg > 0){
                    for($i=0; $i < $total_reg; $i++){
                      foreach ($res[$i] as $key => $value){}
                      $valor = $res[$i]['valor'];
                      $valorF = number_format($valor, 2, ',', '.');
                      echo '<option value="'.$res[$i]['id'].'" data-foto="'.$res[$i]['foto'].'" data-tempo="'.$res[$i]['tempo'].'">'.$res[$i]['nome'].' - R$ '.$valorF.'</option>';
                    }
                  }
                  ?>
                </select>
              </div>

              <div class="step-buttons">
                <div></div> <!-- Espaço vazio para alinhamento -->
                <button type="button" class="botao-azul next-step" data-step="1">
                  Próximo <i class="fas fa-arrow-right ml-2"></i>
                </button>
              </div>
            </div>

            <!-- Etapa 2: Escolha do Profissional -->
            <div class="form-step" id="step-2-content">
              <div class="step-content">
                <h4 class="text-white mb-4 text-center">Escolha o Profissional</h4>

                <!-- Cards de profissionais -->
                <div class="professional-cards" id="professional-cards-container">
                  <!-- Os cards de profissionais serão carregados aqui via JavaScript -->
                  <div class="text-center w-100 p-5">
                    <div class="spinner-border text-info" role="status"></div>
                    <p class="mt-3 text-white-50">Selecione um serviço primeiro...</p>
                  </div>
                </div>

                <!-- Campo oculto para armazenar o profissional selecionado - sem classe sel2 -->
                <input type="hidden" id="funcionario" name="funcionario" required>
              </div>

              <div class="step-buttons">
                <button type="button" class="botao-azul prev-step" data-step="2">
                  <i class="fas fa-arrow-left mr-2"></i> Anterior
                </button>
                <button type="button" class="botao-azul next-step" data-step="2">
                  Próximo <i class="fas fa-arrow-right ml-2"></i>
                </button>
              </div>
            </div>

            <!-- Etapa 3: Escolha da Data e Hora -->
            <div class="form-step" id="step-3-content">
              <div class="step-content">
                <h4 class="text-white mb-4 text-center">Escolha a Data e Horário</h4>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="data" class="text-white-50 mb-2">Data do Agendamento</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-calendar text-info"></i>
                          </span>
                        </div>
                        <input onchange="mudarFuncionario()" class="inputs_agenda" type="date" name="data" id="data" value="<?php echo $data_atual ?>" required />
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label class="text-white-50 mb-2">Resumo da Reserva</label>
                      <div class="p-3 rounded" style="background: rgba(255,255,255,0.05);">
                        <div class="d-flex align-items-center mb-2">
                          <i class="fas fa-cut text-info mr-2"></i>
                          <span class="text-white" id="resumo-servico">Nenhum serviço selecionado</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                          <i class="fas fa-user text-info mr-2"></i>
                          <span class="text-white" id="resumo-profissional">Nenhum profissional selecionado</span>
                        </div>
                        <div class="d-flex align-items-center">
                          <i class="fas fa-clock text-info mr-2"></i>
                          <span class="text-white" id="resumo-horario">Nenhum horário selecionado</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="text-white-50 mb-2">Horários Disponíveis</label>
                  <div id="listar-horarios" class="p-2 rounded" style="background: rgba(0,0,0,0.2); min-height: 100px;">
                    <div class="text-center p-4 text-white-50">
                      <i class="fas fa-clock fa-2x mb-3"></i>
                      <p>Os horários disponíveis serão exibidos aqui</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="step-buttons">
                <button type="button" class="botao-azul prev-step" data-step="3">
                  <i class="fas fa-arrow-left mr-2"></i> Anterior
                </button>
                <button type="button" class="botao-azul next-step" data-step="3" id="btn-proximo-horario">
                  Próximo <i class="fas fa-arrow-right ml-2"></i>
                </button>
              </div>
            </div>

            <!-- Etapa 4: Dados Pessoais -->
            <div class="form-step" id="step-4-content">
              <div class="step-content">
                <h4 class="text-white mb-4 text-center">Seus Dados</h4>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="telefone" class="text-white-50 mb-2">Telefone com DDD</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-phone text-info"></i>
                          </span>
                        </div>
                        <input onkeyup="buscarNome()" class="inputs_agenda" type="text" name="telefone" id="telefone" placeholder="Seu telefone" required />
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="nome" class="text-white-50 mb-2">Nome Completo</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-user text-info"></i>
                          </span>
                        </div>
                        <input onclick="buscarNome()" class="inputs_agenda" type="text" name="nome" id="nome" placeholder="Seu nome" required />
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="obs" class="text-white-50 mb-2">Observações (opcional)</label>
                      <div class="input-group">
                        <div class="input-group-prepend">
                          <span class="input-group-text bg-transparent border-0">
                            <i class="fas fa-comment text-info"></i>
                          </span>
                        </div>
                        <input maxlength="100" type="text" class="inputs_agenda" name="obs" id="obs" placeholder="Observações caso exista alguma.">
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="p-4 rounded" style="background: rgba(255,255,255,0.05);">
                      <h5 class="text-white mb-3">Resumo do Agendamento</h5>

                      <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                          <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-cut text-white"></i>
                          </div>
                        </div>
                        <div>
                          <div class="text-white-50">Serviço</div>
                          <div class="text-white" id="final-servico">Nenhum serviço selecionado</div>
                        </div>
                      </div>

                      <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                          <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-user text-white"></i>
                          </div>
                        </div>
                        <div>
                          <div class="text-white-50">Profissional</div>
                          <div class="text-white" id="final-profissional">Nenhum profissional selecionado</div>
                        </div>
                      </div>

                      <div class="d-flex align-items-center mb-3">
                        <div class="mr-3">
                          <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-calendar-day text-white"></i>
                          </div>
                        </div>
                        <div>
                          <div class="text-white-50">Data</div>
                          <div class="text-white" id="final-data">Nenhuma data selecionada</div>
                        </div>
                      </div>

                      <div class="d-flex align-items-center">
                        <div class="mr-3">
                          <div class="bg-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fas fa-clock text-white"></i>
                          </div>
                        </div>
                        <div>
                          <div class="text-white-50">Horário</div>
                          <div class="text-white" id="final-horario">Nenhum horário selecionado</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="step-buttons">
                <button type="button" class="botao-azul prev-step" data-step="4">
                  <i class="fas fa-arrow-left mr-2"></i> Anterior
                </button>
                <button onclick="salvar()" class="botao-verde" type="submit" style="width:100%;" id="btn_agendar">
                  <i class="fas fa-calendar-check mr-2"></i>
                  <span id='botao_salvar'>Confirmar Agendamento</span>
                </button>
              </div>
            </div>

            <!-- Mensagem de retorno -->
            <div class="mt-3">
              <div id="mensagem" align="center"></div>
            </div>

            <!-- Campos ocultos -->
            <input type="text" id="data_oculta" style="display: none">
            <?php $ag_editar = @$_SESSION['editar_agendamento']; ?>
            <input type="hidden" name="id_edit" value="<?= @$ag_editar['id'] ?>">
            <input type="hidden" id="hora_rec" name="hora_rec" value="<?= @$ag_editar['hora'] ?>">
            <input type="hidden" id="data_rec" name="data_rec" value="<?= @$ag_editar['data'] ?>">
            <input type="hidden" id="nome_func" name="nome_func">
            <input type="hidden" id="nome_serv" name="nome_serv">
          </div>

          <div class="text-center mt-4">
            <a href="meus-agendamentos.php" class="botao-azul" id='botao_editar' style="display: none;">
              <i class="fas fa-list-alt mr-2"></i>
              Ver Meus Agendamentos
            </a>
          </div>
        </form>

        <div id="listar-cartoes" style="margin-top: 20px;">
          <!-- Cartões serão carregados aqui -->
        </div>

        <div class="text-center mt-4">
          <a href="meus-agendamentos.php" class="botao-azul" id='botao_editar' style="display: none;">
            <i class="fas fa-list-alt mr-2"></i>
            Ver Meus Agendamentos
          </a>
        </div>

      </div>
    </div>
  </div>
</div>
</div> <!-- /#page-content -->

<!-- Modal de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">
          <i class="fas fa-trash-alt text-danger mr-2"></i>
          Excluir Agendamento
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px" id="btn-fechar-excluir">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form id="form-excluir">
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <span id="msg-excluir"></span>
          </div>

          <input type="hidden" name="id" id="id_excluir">

          <small><div id="mensagem-excluir" align="center"></div></small>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-2"></i>
            Cancelar
          </button>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash-alt mr-2"></i>
            Confirmar Exclusão
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<button id="botao-instalar" style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); padding: 12px 20px; background: #007bff; color: #fff; border: none; border-radius: 8px; z-index: 9999;">Instalar App</button>

<script src="js/jquery-3.4.1.min.js"></script>
<!-- popper js -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<!-- bootstrap js -->
<script src="js/bootstrap.js"></script>
<!-- owl slider -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<!-- custom js -->
<script src="js/custom.js"></script>
<!-- Google Map -->
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo urlencode(getenv('GOOGLE_MAPS_API_KEY') ?: ''); ?>&callback=myMap"></script>
<!-- End Google Map -->

<!-- Mascaras JS -->
<script type="text/javascript" src="sistema/painel/js/mascaras.js"></script>

<!-- Ajax para funcionar Mascaras JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script> 

<script>
let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  document.getElementById('botao-instalar').style.display = 'block';

  document.getElementById('botao-instalar').addEventListener('click', async () => {
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;
    console.log('Resultado da instalação:', outcome);
    deferredPrompt = null;
  });
});

window.addEventListener('appinstalled', () => {
  console.log('✅ App instalado!');
});
</script>

<script type="text/javascript">
$(document).ready(function() {

  const agendamentoEditar = <?= json_encode($_SESSION['editar_agendamento'] ?? []) ?>;

  if (Object.keys(agendamentoEditar).length > 0) {
    $('#servico').val(agendamentoEditar.servico);
    $('#funcionario').val(agendamentoEditar.funcionario);
    $('#data').val(agendamentoEditar.data);
    $('#hora_rec').val(agendamentoEditar.hora);

    setTimeout(() => {
      carregarServicosCards();
      carregarProfissionaisCards();
      mudarFuncionario();
    }, 200);
  }

  var nome_cl = localStorage.nome_cli_del;
  var tel_cl = localStorage.telefone_cli_del;
  $('#telefone').val(tel_cl);
  $('#nome').val(nome_cl);

  // Inicializa o botão de editar como oculto
  $("#botao_editar").hide();

  // Carrega os serviços em cards
  carregarServicosCards();

  // Filtro ao vivo para serviços
  $(document).on('input', '#filtro-servicos', function () {
    var termo = $(this).val().toLowerCase();
    $('.service-card').each(function () {
      var nome = $(this).find('.card-title').text().toLowerCase();
      if (nome.includes(termo)) { $(this).show(); } else { $(this).hide(); }
    });
  });

  // Máscara para telefone
  if($.fn.mask) {
    $('#telefone').mask('(00) 00000-0000');
  }

  // Efeito de destaque nos campos ao focar
  $('.inputs_agenda').focus(function() {
    $(this).css('border-bottom', '2px solid #2ecc71 !important');
  }).blur(function() {
    $(this).css('border-bottom', '2px solid #3498db !important');
  });

  // Navegação entre etapas
  $('.next-step').click(function() {
    var currentStep = parseInt($(this).data('step'));
    var nextStep = currentStep + 1;

    // Validações
    if (currentStep === 1) {
      if ($('#servico').val() === '') {
        if(typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Por favor, selecione um serviço para continuar.', confirmButtonColor: '#3498db' });
        } else { alert('Por favor, selecione um serviço para continuar.'); }
        return;
      }
      carregarProfissionaisCards();
    }

    if (currentStep === 2) {
      if ($('#funcionario').val() === '') {
        if(typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Por favor, selecione um profissional para continuar.', confirmButtonColor: '#3498db' });
        } else { alert('Por favor, selecione um profissional para continuar.'); }
        return;
      }
      mudarFuncionario();
    }

    if (currentStep === 3) {
      var horaSelecionada = $('#hora_rec').val();
      if (!horaSelecionada || horaSelecionada === '') {
        if(typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Por favor, selecione um horário para continuar.', confirmButtonColor: '#3498db' });
        } else { alert('Por favor, selecione um horário para continuar.'); }
        return;
      }
      atualizarResumoFinal();
    }

    // Avança
    $('.form-step').removeClass('active');
    $('#step-' + nextStep + '-content').addClass('active');

    // Indicadores
    $('.step').removeClass('active');
    $('#step-' + nextStep).addClass('active');

    for (var i = 1; i < nextStep; i++) { $('#step-' + i).addClass('completed'); }

    setTimeout(function() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }, 100);
  });

  $('.prev-step').click(function() {
    var currentStep = parseInt($(this).data('step'));
    var prevStep = currentStep - 1;

    $('.form-step').removeClass('active');
    $('#step-' + prevStep + '-content').addClass('active');

    $('.step').removeClass('active');
    $('#step-' + prevStep).addClass('active');

    for (var i = 1; i < prevStep; i++) { $('#step-' + i).addClass('completed'); }
  });

  // Data mínima hoje
  $('#data').change(function() {
    var partes = $(this).val().split('-');
    var selectedDate = new Date(partes[0], partes[1] - 1, partes[2]);
    var today = new Date(); today.setHours(0,0,0,0);

    if (selectedDate < today) {
      if(typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'error', title: 'Data Inválida', text: 'Por favor, selecione uma data igual ou posterior a hoje.', confirmButtonColor: '#3498db' });
      } else { alert('Selecione uma data igual ou maior que hoje!'); }

      var dt = new Date();
      var dia = String(dt.getDate()).padStart(2, '0');
      var mes = String(dt.getMonth() + 1).padStart(2, '0');
      var ano = dt.getFullYear();
      dataAtual = ano + '-' + mes + '-' + dia;
      $('#data').val(dataAtual);
    }

    mudarFuncionario();
    atualizarResumo();
  });
});

// Função para carregar os serviços em cards
function carregarServicosCards() {
  var html = '';
  $('#servico option').each(function() {
    if ($(this).val() !== '') {
      var id = $(this).val();
      var texto = $(this).text();
      var partes = texto.split(' - R$ ');
      var nome = partes[0];
      var valor = partes.length > 1 ? partes[1] : '0,00';
      var foto = $(this).data('foto');
      var tempo = $(this).data('tempo');

      html += '<div class="service-card" data-id="' + id + '" onclick="selecionarServico(' + id + ', \'' + nome + '\', \'' + valor + '\')">';
      html += '<div class="card-img">';
      html += '<img src="sistema/painel/img/servicos/' + foto + '" alt="' + nome + '" onerror="this.src=\'sistema/painel/img/servicos/sem-foto.jpg\'">';
      html += '</div>';
      html += '<div class="card-body">';
      html += '<h5 class="card-title">' + nome + '</h5>';
      html += '<p class="card-text">'+tempo+' Minutos</p>';
      html += '<div class="card-price">R$ ' + valor + '</div>';
      html += '</div>';
      html += '</div>';
    }
  });

  if (html === '') {
    html = '<div class="text-center w-100 p-5"><i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i><p class="text-white">Nenhum serviço disponível no momento.</p></div>';
  }
  $('#service-cards-container').html(html);
}

// Função para selecionar um serviço
function selecionarServico(id, nome, valor) {  
  $('.service-card').removeClass('selected');
  $('.service-card[data-id="' + id + '"]').addClass('selected');
  $('#servico').val(id);
  $('#nome_serv').val(nome + ' - R$ ' + valor);
  $('#resumo-servico').text(nome + ' - R$ ' + valor);
  $('#final-servico').text(nome + ' - R$ ' + valor);
  mudarServico();
  irParaProximaEtapa(1)
}

// Carregar profissionais
function carregarProfissionaisCards() {
  $("#professional-cards-container").html('<div class="text-center w-100 p-5"><div class="spinner-border text-info" role="status"></div><p class="mt-3 text-white-50">Carregando profissionais...</p></div>');
  var serv = $("#servico").val();

  $.ajax({
    url: "ajax/listar-funcionarios.php",
    method: 'POST',
    data: {serv},
    dataType: "text",
    success: function(result) {
      var tempDiv = document.createElement('div');
      tempDiv.innerHTML = result;
      var options = tempDiv.querySelectorAll('option');
      var html = '';
      options.forEach(function(option) {
        if (option.value !== '') {
          var id = option.value;
          var nome = option.textContent;
          var foto = option.dataset.foto;
          html += '<div class="professional-card" data-id="' + id + '" onclick="selecionarProfissional(' + id + ', \'' + nome + '\')">';
          html += '<div class="card-img">';
          html += '<img src="sistema/painel/img/perfil/' + foto + '" alt="' + nome + '" onerror="this.src=\'sistema/painel/img/perfil/sem-foto.jpg\'">';
          html += '</div>';
          html += '<div class="card-body">';
          html += '<h5 class="card-title-func">' + nome + '</h5>';
          html += '<div class="card-specialty">Profissional</div>';
          html += '<div class="card-rating mt-2">';
          for (var j = 0; j < 5; j++) { html += '<i class="fas fa-star"></i>'; }
          html += '</div></div></div>';
        }
      });
      if (html === '') {
        html = '<div class="text-center w-100 p-5"><i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i><p class="text-white">Nenhum profissional disponível para este serviço.</p></div>';
      }
      $('#professional-cards-container').html(html);
    },
    error: function() {
      $('#professional-cards-container').html('<div class="alert alert-danger">Erro ao carregar profissionais. Tente novamente.</div>');
    }
  });
}

// Selecionar profissional
function selecionarProfissional(id, nome) {
  $('.professional-card').removeClass('selected');
  $('.professional-card[data-id="' + id + '"]').addClass('selected');
  $('#funcionario').val(id);
  $('#nome_func').val(nome);
  $('#resumo-profissional').text(nome);
  $('#final-profissional').text(nome);
  listarFuncionario();
  irParaProximaEtapa(2)
}

// Resumo (mantido)
function atualizarResumo() {
  var servico = $('#nome_serv').val() || 'Nenhum serviço selecionado';
  var profissional = $('#nome_func').val() || 'Nenhum profissional selecionado';
  var data = $('#data').val();
  var horario = $('#hora_rec').val() || 'Nenhum horário selecionado';
  if (data) {
    var dataObj = new Date(data);
    var dia = String(dataObj.getDate()).padStart(2, '0');
    var mes = String(dataObj.getMonth() + 1).padStart(2, '0');
    var ano = dataObj.getFullYear();
    var dataFormatada = dia + '/' + mes + '/' + ano;
    $('#resumo-data').text(dataFormatada);
    $('#data_rec').val(dataFormatada);
  } else {
    $('#resumo-data').text('Nenhuma data selecionada');
  }
  $('#resumo-servico').text(servico);
  $('#resumo-profissional').text(profissional);
  $('#resumo-horario').text(horario);
}

// Resumo final (mantido)
function atualizarResumoFinal() {
  var servico = $('#nome_serv').val() || 'Nenhum serviço selecionado';
  var profissional = $('#nome_func').val() || 'Nenhum profissional selecionado';
  var data = $('#data').val();
  var horario = $('#hora_rec').val() || 'Nenhum horário selecionado';
  if (data) {
    var partes = data.split('-');
    var ano = parseInt(partes[0], 10);
    var mes = parseInt(partes[1], 10) - 1;
    var dia = parseInt(partes[2], 10);
    var dataObj = new Date(ano, mes, dia);
    var diaStr = String(dataObj.getDate()).padStart(2, '0');
    var mesStr = String(dataObj.getMonth() + 1).padStart(2, '0');
    var anoStr = dataObj.getFullYear();
    var dataFormatada = diaStr + '/' + mesStr + '/' + anoStr;
    $('#final-data').text(dataFormatada);
    $('#data_rec').val(dataFormatada);
  } else {
    $('#final-data').text('Nenhuma data selecionada');
  }
  $('#final-servico').text(servico);
  $('#final-profissional').text(profissional);
  $('#final-horario').text(horario);
}

function mudarFuncionario(){
  var funcionario = $('#funcionario').val();
  var data = $('#data').val();    
  var hora = $('#hora_rec').val();
  $("#listar-horarios").html('<div class="text-center p-3"><div class="spinner-border text-info" role="status"></div><p class="mt-2 text-white-50">Carregando horários...</p></div>');
  listarHorarios(funcionario, data, hora);
  listarFuncionario();  
}

// Listar horários (sem mudar lógica)
function listarHorarios(funcionario, data, hora){  
  var servico = $("#servico").val();
  $.ajax({
    url: "ajax/listar-horarios.php",
    method: 'POST',
    data: {funcionario, data, hora, servico},
    dataType: "text",
    success:function(result){
      if(result.trim() === '000'){
        if(typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Selecione uma data igual ou maior que hoje!', confirmButtonColor: '#3498db' });
        } else { alert('Selecione uma data igual ou maior que hoje!'); }
        var dt = new Date();
        var dia = String(dt.getDate()).padStart(2, '0');
        var mes = String(dt.getMonth() + 1).padStart(2, '0');
        var ano = dt.getFullYear();
        dataAtual = ano + '-' + mes + '-' + dia;
        $('#data').val(dataAtual);
        return;
      } else {
        $("#listar-horarios").html(result);
        // Mantém sua lógica de clique (só garante bind)
        $('.btn-horario').off('click').on('click', function() {
          $('.btn-horario').removeClass('btn-info').addClass('btn-outline-info');
          $(this).removeClass('btn-outline-info').addClass('btn-info');
          var horarioSelecionado = $(this).text().trim();
          $('#hora_rec').val(horarioSelecionado);
          $('#resumo-horario').text(horarioSelecionado);
          $('#final-horario').text(horarioSelecionado);
        });
        if(hora && hora !== '') {
          $('.btn-horario').each(function() {
            if($(this).text().trim() === hora) { $(this).click(); }
          });
        }
      }
    },
    error: function() {
      $("#listar-horarios").html('<div class="alert alert-danger">Erro ao carregar horários. Tente novamente.</div>');
    }
  });
}

function buscarNome(){
  var tel = $('#telefone').val();  
  if(tel.length < 8) return;
  listarCartoes(tel);  
  $.ajax({
    url: "ajax/listar-nome.php",
    method: 'POST',
    data: {tel},
    dataType: "text",
    success:function(result){
      var split = result.split("*");
      console.log(split[3]);
      if(split[2] != "" && split[2] != undefined){
        $("#funcionario").val(parseInt(split[2]));
        $('.professional-card').removeClass('selected');
        $('.professional-card[data-id="' + parseInt(split[2]) + '"]').addClass('selected');
      }
      if(split[5] != "" && split[5] != undefined){
        $("#servico").val(parseInt(split[5]));
        $('.service-card').removeClass('selected');
        $('.service-card[data-id="' + parseInt(split[5]) + '"]').addClass('selected');
        $("#botao_editar").show();          
        $("#botao_salvar").text('Novo Agendamento');
      }else{
        $("#botao_editar").hide(); 
      }
      $("#nome").val(split[0]);
      $("#msg-excluir").text('Deseja Realmente excluir esse agendamento feito para o dia ' + split[7] + ' às ' + split[4]);
      mudarFuncionario();
    }
  });  
}

function salvar(){ $('#id').val(''); }

function listarCartoes(tel){
  $.ajax({
    url: "ajax/listar-cartoes.php",
    method: 'POST',
    data: {tel},
    dataType: "text",
    success:function(result){ $("#listar-cartoes").html(result); }
  });
}

function listarFuncionario(){  
  var func = $("#funcionario").val();
  $.ajax({
    url: "ajax/listar-funcionario.php",
    method: 'POST',
    data: {func},
    dataType: "text",
    success:function(result){
      $("#nome_func").val(result);
      $('#resumo-profissional').text(result);
      $('#final-profissional').text(result);
    }
  });
}

function mudarServico(){
  listarFuncionarios();  
  var serv = $("#servico").val();
  $.ajax({
    url: "ajax/listar-servico.php",
    method: 'POST',
    data: {serv},
    dataType: "text",
    success:function(result){
      $("#nome_serv").val(result);
      $('#resumo-servico').text(result);
      $('#final-servico').text(result);
    }
  });
}

function listarFuncionarios(){  
  var serv = $("#servico").val();
  $.ajax({
    url: "ajax/listar-funcionarios.php",
    method: 'POST',
    data: {serv},
    dataType: "text",
    success:function(result){
      var tempDiv = document.createElement('div');
      tempDiv.innerHTML = result;
      var options = tempDiv.querySelectorAll('option');
      $('#funcionario').val('');
    }
  });
}

// Submissão
$("#form-agenda").submit(function () {
  event.preventDefault();
  var nome = $('#nome').val();
  var telefone = $('#telefone').val();
  localStorage.setItem('nome_cli_del', nome);
  localStorage.setItem('telefone_cli_del', telefone);
  $('#btn_agendar').prop('disabled', true);
  $('#mensagem').html('<div class="text-center"><div class="spinner-border text-light" role="status"></div><p class="mt-2 text-white">Processando agendamento...</p></div>');
  var formData = new FormData(this);
  $.ajax({
    url: "ajax/agendar_temp.php",
    type: 'POST',
    data: formData,
    success: function (mensagem) {
      var msg = mensagem.split('*');
      var id_agd = msg[1];
      $('#mensagem').text('');
      $('#mensagem').removeClass();
      if (msg[0].trim() == "Pré Agendado") {                    
        $('#mensagem').html('<div class="alert alert-success">Agendamento realizado com sucesso!</div>');
        buscarNome();
        if(typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: 'Agendamento Realizado!',
            text: 'Você será redirecionado para a página de pagamento.',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
          }).then(() => {
            window.location="pagamento/"+id_agd+"/100";
          });
        } else {
          $('#mensagem').text(msg[0]);
          window.location="pagamento/"+id_agd+"/100";
        }
      } else {
        $('#mensagem').html('<div class="alert alert-danger">' + msg[0] + '</div>');
      }
      $('#btn_agendar').prop('disabled', false);
    },
    error: function() {
      $('#mensagem').html('<div class="alert alert-danger">Erro ao processar agendamento. Tente novamente.</div>');
      $('#btn_agendar').prop('disabled', false);
    },
    cache: false,
    contentType: false,
    processData: false,
  });
});

function irParaProximaEtapa(etapaAtual) {
  $('.next-step[data-step="' + etapaAtual + '"]').click();
}
</script>

<script>
// Registra o SW
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
  .then(reg => console.log('✅ SW registrado:', reg.scope))
  .catch(err => console.error('❌ Erro ao registrar SW:', err));
}

// Gerencia instalação
let deferredPrompt2;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt2 = e;
  const btn = document.getElementById('botao-instalar');
  btn.style.display = 'block';

  btn.addEventListener('click', () => {
    btn.style.display = 'none';
    deferredPrompt2.prompt();
    deferredPrompt2.userChoice.then((result) => {
      console.log('Resultado da instalação:', result.outcome);
      deferredPrompt2 = null;
    });
  });
});

window.addEventListener('appinstalled', () => {
  console.log('✅ App instalado!');
});
</script>

<script>
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('sw.js')
  .then(reg => { console.log('✅ Service Worker registrado:', reg.scope); })
  .catch(err => { console.error('❌ Erro ao registrar Service Worker:', err); });
}
</script>

<script>
  // Mostrar botão ao rolar
  window.onscroll = function () {
    const btn = document.getElementById("btnTopo");
    if (!btn) return;
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
      btn.style.display = "flex";
    } else {
      btn.style.display = "none";
    }
  };
  // Subir ao topo
  var bt = document.getElementById("btnTopo");
  if (bt) {
    bt.onclick = function () { window.scrollTo({ top: 0, behavior: 'smooth' }); };
  }
</script>

<script>
  window.addEventListener('load', function () {
    var splash = document.getElementById('splash');
    var app    = document.getElementById('app');

    if (splash) {
      splash.style.display = 'none';
    }
    if (app) {
      app.style.display = 'block';
    }
  });
</script>

<script src="app/js/custom.js"></script>
</body>
</html>
