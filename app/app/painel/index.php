<?php
@session_start();
require_once("../../sistema/conexao.php");
$pag_inicial = 'agenda';
$data_page = 'index';

$id_usuario = @$_SESSION['id'];
$visualizar_usuario = @$_SESSION['visualizar'];

$query = $pdo->query("SELECT * from receber where pago = 'Não' and data_venc = curDate()");
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
  $total_vencendo_msg = @count($res);

    $query = $pdo->query("SELECT * from receber where pago = 'Não' and data_venc < curDate()");
  $res = $query->fetchAll(PDO::FETCH_ASSOC);
  $total_vencidas_msg = @count($res);

if (@$_SESSION['aut_token_portalapp'] != 'portalapp2024') {
  echo "<script>localStorage.setItem('id_usu', '')</script>";
  unset($_SESSION['id'], $_SESSION['nome'], $_SESSION['nivel']);
  $_SESSION['msg'] = "";
  echo '<script>window.location="../"</script>';
  exit();
}


if (@$_SESSION['nivel'] != 'Administrador') {
  require_once("../../sistema/painel/verificar-permissoes.php");
}


if($pag_inicial != 'agenda' and $pag_inicial != 'minhas_comissoes' and $pag_inicial != 'meus_servicos' and $pag_inicial != 'dias' and $pag_inicial != 'servicos_func' and $pag_inicial != 'dias_bloqueio_func' ){
  $pag_inicial = 'agenda';
}



$buscar = @$_POST['buscar'];

$dataInicial = @$_POST['dataInicial'];
$dataFinal = @$_POST['dataFinal'];
$pago = @$_POST['pago'];

$data_hoje = date('Y-m-d');
$data_atual = date('Y-m-d');
$mes_atual = Date('m');
$ano_atual = Date('Y');
$data_inicio_mes = $ano_atual . "-" . $mes_atual . "-01";
$data_inicio_ano = $ano_atual . "-01-01";
$data_mes = $ano_atual."-".$mes_atual."-01";

$data_ontem = date('Y-m-d', strtotime("-1 days", strtotime($data_atual)));
$data_amanha = date('Y-m-d', strtotime("+1 days", strtotime($data_atual)));


if ($mes_atual == '04' || $mes_atual == '06' || $mes_atual == '09' || $mes_atual == '11') {
  $data_final_mes = $ano_atual . '-' . $mes_atual . '-30';
} else if ($mes_atual == '02') {
  $bissexto = date('L', @mktime(0, 0, 0, 1, 1, $ano_atual));
  if ($bissexto == 1) {
    $data_final_mes = $ano_atual . '-' . $mes_atual . '-29';
  } else {
    $data_final_mes = $ano_atual . '-' . $mes_atual . '-28';
  }

} else {
  $data_final_mes = $ano_atual . '-' . $mes_atual . '-31';
}


if (@$_GET['pagina'] != "") {
  $pagina = @$_GET['pagina'];


} else {
  $pagina = $pag_inicial;
}



$query = $pdo->query("SELECT * from usuarios where id = '$id_usuario'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);
if ($linhas > 0) {
  $nome_usuario = $res[0]['nome'];
  $email_usuario = $res[0]['email'];
  $telefone_usuario = $res[0]['telefone'];
  $senha_usuario = $res[0]['senha'];
  $nivel_usuario = $res[0]['nivel'];
  $foto_usuario = $res[0]['foto'];
  $endereco_usuario = $res[0]['endereco'];
  $cpf_usuario = $res[0]['cpf'];
  $atendimento_usuario = $res[0]['atendimento'];
  $intervalo_usuario = $res[0]['intervalo'];
} else {
  echo '<script>window.location="../"</script>';
  exit();
}

require_once("cabecalho.php");
require_once("rodape.php");
require_once("alertas.php");


echo "<script>localStorage.setItem('pagina', '$pagina')</script>";
require_once("paginas/" . $pagina . ".php");
?>







<!--FIM DA PAGINA-->
<script src="js/jquery-3.3.1.min.js"></script>
<script src="js/jquery.validate.min.js"></script>
<script src="js/swiper.min.js"></script>
<script src="js/ajax.js"></script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>



<!-- Mascaras JS -->
<script type="text/javascript" src="js/mascaras.js"></script>

<!-- Ajax para funcionar Mascaras JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

<script type="text/javascript">
  $(document).ready(function () {
    $('.sel_nulo').select2({
    });
    $('.sel2').select2({
      dropdownParent: $('#popupForm')
    });
    $('.sel3').select2({
      dropdownParent: $('#popupForm')
    });
    $('.sel4').select2({
      dropdownParent: $('#popupForm')
    });
    $('.sel5').select2({
      dropdownParent: $('#popupForm')
    });
    $('.sel11').select2({
      dropdownParent: $('#popupForm')
    });

    $('.sel_servicos').select2({
      dropdownParent: $('#menu-share-servico')
    });

  });
</script>

<script>
  function navigateToPage(event, page) {
    event.preventDefault(); // Impede o comportamento padrão do link
    window.location.href = page; // Redireciona para a página especificada

    // Após um pequeno atraso, redireciona para a página correspondente
    setTimeout(function () {
      location.reload(); // Atualiza a página atual
    }, 100); // 100 milissegundos de atraso
  }
</script>




<!-- MODAL PERFIL-->
<div class="offcanvas offcanvas-top rounded-m offcanvas-detached" style="height:98%" id="menu-perfil">
  <div class="content mb-0">
    <div class="d-flex pb-2">
      <div class="align-self-center text-uppercase">
        <span style="margin-left: 150px !important" class="font-14 color-highlight font-700 ">EDITAR PERFIL</span>
      </div>
      <div class="align-self-center ms-auto">
        <button style="border: none; background: transparent; margin-right: 12px" data-bs-dismiss="offcanvas"
          id="btn-fechar-perfil" aria-label="Close" data-bs-dismiss="modal" type="button"><i
            class="bi bi-x-circle-fill color-red-dark font-18 me-n4"></i>
        </button>
      </div>
    </div><br>
    <form id="form-perfil" method="post">

    <div class="form-floating mb-3 position-relative">
        <i class="bi bi-person-fill position-absolute start-0 top-50 translate-middle-y ms-3"></i>
        <input type="text" class="form-control rounded-xs ps-5" id="nome" name="nome" placeholder="" required value="<?php echo $nome_usuario ?>">
        <label class="color-theme ps-5">Seu Nome</label>
        <small class="position-absolute top-50 end-0 translate-middle-y me-3 text-danger"
          style="font-size: 9px;">(Obrigatório)</small>
      </div>

      <div class="form-floating mb-3 position-relative">
        <i class="fa-solid fa-at position-absolute start-0 top-50 translate-middle-y ms-3"></i>
        <input type="email" class="form-control rounded-xs ps-5" id="email" name="email" placeholder="" value="<?php echo $email_usuario ?>" required>
        <label class="color-theme ps-5">Seu E-mail</label>
        <small class="position-absolute top-50 end-0 translate-middle-y me-3 text-danger"
          style="font-size: 9px;">(Obrigatório)</small>
      </div>

<div class="form-floating mb-3 position-relative">
        <i class="fa-solid fa-phone position-absolute start-0 top-50 translate-middle-y ms-3"></i>
        <input type="text" class="form-control rounded-xs ps-5" id="telefone_perfil" name="telefone" placeholder=""
          onkeyup="verificarTelefone('telefone_perfil', this.value)" required value="<?php echo $telefone_usuario ?>">
  <label class="color-theme ps-5">Telefone</label>
  <small class="position-absolute top-50 end-0 translate-middle-y me-3 text-danger"
    style="font-size: 9px;">(Obrigatório)</small>
</div>


<div class="form-floating mb-3 position-relative">
        <i class="fa-solid fa-user position-absolute start-0 top-50 translate-middle-y ms-3"></i>
        <input type="text" class="form-control rounded-xs ps-5" id="cpf_perfil" name="cpf" placeholder=""
           value="<?php echo $cpf_usuario ?>">
  <label class="color-theme ps-5">CPF</label>

</div>



      <div class="form-floating mb-3 position-relative">
        <i class="bi bi-person-fill position-absolute start-0 top-50 translate-middle-y ms-3"></i>
        <input type="password" class="form-control rounded-xs ps-5" id="senha" name="senha" placeholder="" required value="<?php echo @$senha_usuario ?>">
        <label class="color-theme ps-5">Senha</label>
        <small class="position-absolute top-50 end-0 translate-middle-y me-3 text-danger"
          style="font-size: 9px;">(Obrigatório)</small>
      </div>

       <div class="form-floating mb-3 position-relative">
        <i class="bi bi-person-fill position-absolute start-0 top-50 translate-middle-y ms-3"></i>
        <input type="password" class="form-control rounded-xs ps-5" id="conf_senha" name="conf_senha" placeholder="" required>
            <label class="color-theme ps-5">Confirmar Senha</label>
            <small class="position-absolute top-50 end-0 translate-middle-y me-3 text-danger"
    style="font-size: 9px;">(Obrigatório)</small>
          </div>


          <div class="form-floating mb-3">
  <select class="form-select" name="atendimento">
    <option value="Sim" <?php if(@$atendimento_usuario == 'Sim'){?> selected <?php } ?> >Sim</option>
    <option value="Não" <?php if(@$atendimento_usuario == 'Não'){?> selected <?php } ?> >Não</option>
  </select>
  <label>Atendimento</label>
</div>


<div class="form-floating mb-3 position-relative">
        <i class="fa-solid fa-user position-absolute start-0 top-50 translate-middle-y ms-3"></i>
        <input type="number" class="form-control rounded-xs ps-5" name="intervalo" placeholder="Ex: 15, 30 etc"
           value="<?php echo $intervalo_usuario ?>">
  <label class="color-theme ps-5">Intervalo Atendimento (Minutos)</label>

</div>

    
      <div class="row">
        <div class="col-12">
          <div class="form-floating mb-3 position-relative">
            <i class="bi bi-geo-alt position-absolute start-0 top-50 translate-middle-y ms-3"></i>
            <input type="text" class="form-control rounded-xs ps-5"  name="endereco" placeholder="" value="<?php echo @$endereco_usuario ?>">
            <label class="color-theme ps-5">Endereço</label>
          </div>

        </div>
       
      </div>
     



     

      <div class="form_row" align="center" onclick="foto_perfil.click()">
        <img src="../../sistema/painel/img/perfil/<?php echo $foto_usuario ?>" width="100px" id="target_perfil"><br>
        <img src="../images/icone-arquivo.png" width="100px" style="margin-top: -12px">
      </div>
      <input onChange="carregarImgPerfil();" type="file" name="foto" id="foto_perfil" style="display:none">
      <button name="btn_salvar" id="btn_salvar_perfil"
        class="btn btn-full  gradient-highlight rounded-xs text-uppercase font-700 w-100 btn-s mt-4 mb-3"
        type="submit">SALVAR</button>
      <input type="hidden" name="id" value="<?php echo $id_usuario ?>">
    </form>
  </div>
</div>




<style type="text/css">
  .select2-selection__rendered {
    line-height: 40px !important;
    font-size: 12px !important;
    color: #666666 !important;
    margin-top: 3px !important;
  }

  .select2-selection {
    height: 50px !important;
    font-size: 10px !important;
    color: #666666 !important;
    border-radius: 10px !important;
    border: 2px solid #e8e8e8 !important;
  }
</style>


<script>
  $("#form-perfil").submit(function () {
    event.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: "../../sistema/painel/editar-perfil.php",
      type: 'POST',
      data: formData,
      success: function (mensagem) {
        //ARMAZENAR O RETORNO PARA A MSG DE SUCESSO
			$('#toast-message').text(mensagem.trim());
        if (mensagem.trim() == "Editado com Sucesso") {
          //toast(mensagem, 'verde')
          $('#not_salvar').click();
          $('#btn-fechar-perfil').click();
          //location.reload();
          setTimeout(function () {
            location.reload();
          }, 1000); // 2000 milissegundos = 2 segundos
          
        } else {
          //$('#msg-perfil').addClass('text-danger')
          toast(mensagem, 'vermelha')
        }
      },
      cache: false,
      contentType: false,
      processData: false,
    });
  });


</script>



<script type="text/javascript">
  function carregarImgPerfil() {
    var target = document.getElementById('target_perfil');
    var file = document.querySelector("#foto_perfil").files[0];

    var reader = new FileReader();

    reader.onloadend = function () {
      target.src = reader.result;
    };

    if (file) {
      reader.readAsDataURL(file);

    } else {
      target.src = "";
    }
  }
</script>


<script>

  function limpa_formulário_cep() {
    //Limpa valores do formulário de cep.
    document.getElementById('endereco').value = ("");
    document.getElementById('bairro').value = ("");
    document.getElementById('cidade').value = ("");
    document.getElementById('estado').value = ("");
    //document.getElementById('ibge').value=("");
  }

  function meu_callback(conteudo) {
    if (!("erro" in conteudo)) {
      //Atualiza os campos com os valores.
      document.getElementById('endereco').value = (conteudo.logradouro);
      document.getElementById('bairro').value = (conteudo.bairro);
      document.getElementById('cidade').value = (conteudo.localidade);
      document.getElementById('estado').value = (conteudo.uf);
      //document.getElementById('ibge').value=(conteudo.ibge);
    } //end if.
    else {
      //CEP não Encontrado.
      limpa_formulário_cep();
      alert("CEP não encontrado.");
    }
  }

  function pesquisacep(valor) {

    //Nova variável "cep" somente com dígitos.
    var cep = valor.replace(/\D/g, '');

    //Verifica se campo cep possui valor informado.
    if (cep != "") {

      //Expressão regular para validar o CEP.
      var validacep = /^[0-9]{8}$/;

      //Valida o formato do CEP.
      if (validacep.test(cep)) {

        //Preenche os campos com "..." enquanto consulta webservice.
        document.getElementById('endereco').value = "...";
        document.getElementById('bairro').value = "...";
        document.getElementById('cidade').value = "...";
        document.getElementById('estado').value = "...";
        //document.getElementById('ibge').value="...";

        //Cria um elemento javascript.
        var script = document.createElement('script');

        //Sincroniza com o callback.
        script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=meu_callback';

        //Insere script no documento e carrega o conteúdo.
        document.body.appendChild(script);

      } //end if.
      else {
        //cep é inválido.
        limpa_formulário_cep();
        alert("Formato de CEP inválido.");
      }
    } //end if.
    else {
      //cep sem valor, limpa formulário.
      limpa_formulário_cep();
    }
  };


</script>

<script>
function atualizarCorStatusIndex() {
    var select = document.getElementById('status_cliente_index');
    var corSelecionada = select.options[select.selectedIndex].getAttribute('data-cor');
    var preview = document.getElementById('preview_cor_status_index');

    if(corSelecionada) {
        preview.style.backgroundColor = corSelecionada;
    } else {
        preview.style.backgroundColor = "#ffffff";
    }
}
</script>



