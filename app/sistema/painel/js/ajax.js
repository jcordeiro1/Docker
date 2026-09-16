// Carrega a listagem quando o documento estiver pronto 
$(document).ready(function() {
    listar();

    // Remove qualquer bind prévio e registra o handler do formulário de forma namespaced
    $(document).off('submit.ajaxform', '#form').on('submit.ajaxform', '#form', function (event) {
        event.preventDefault();

        // trava anti-duplo-clique no botão de submit
        var $form = $(this);
        var $btnSubmit = $form.find('button[type=submit], input[type=submit]').first();
        if ($btnSubmit.length) {
            if ($btnSubmit.prop('disabled')) return; // já enviando
            $btnSubmit.prop('disabled', true);
        }

        var formData = new FormData(this);

        // Aborta envio anterior se ainda estiver rodando
        if (window._xhrSalvar && window._xhrSalvar.readyState !== 4) {
            try { window._xhrSalvar.abort(); } catch(e) {}
        }

        window._xhrSalvar = $.ajax({
            url: 'paginas/' + pag + "/salvar.php",
            type: 'POST',
            data: formData,
            success: function (mensagem) {
                $('#mensagem').text('').removeClass();
                if (mensagem && mensagem.trim() === "Salvo com Sucesso") {
                    $('#btn-fechar').click();
                    listar();
                } else {
                    $('#mensagem').addClass('text-danger').text(mensagem || 'Falha ao salvar');
                }
            },
            error: function(xhr){
                var msg = (xhr && xhr.responseText) ? xhr.responseText : 'Erro ao enviar os dados.';
                $('#mensagem').addClass('text-danger').text(msg);
            },
            complete: function () {
                // libera o botão após terminar (com sucesso ou erro)
                if ($btnSubmit.length) $btnSubmit.prop('disabled', false);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
});

// --- Loader simples para a área #listar (não quebra estilos existentes) ---
function _loaderListar() {
    return '' +
      '<div class="p-3">' +
        '<div class="d-flex align-items-center mb-2">' +
          '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>' +
          '<strong>Carregando...</strong>' +
        '</div>' +
        '<div class="bg-light" style="height:10px;border-radius:4px;margin-bottom:8px;"></div>' +
        '<div class="bg-light" style="height:10px;border-radius:4px;margin-bottom:8px;"></div>' +
        '<div class="bg-light" style="height:10px;border-radius:4px;margin-bottom:8px;"></div>' +
      '</div>';
}

// --- Controle simples para evitar múltiplas requisições sobrepostas de listar() ---
var _xhrListar = null;

// ✅ AJUSTE MÍNIMO: volta assinatura compatível com o sistema (p1..p6)
function listar(p1, p2, p3, p4, p5, p6){
    // Coloca loader enquanto carrega
    if ($("#listar").length) {
        $("#listar").html(_loaderListar());
    }

    // Aborta chamada anterior se ainda estiver em andamento (evita sobreposição)
    if (_xhrListar && _xhrListar.readyState !== 4) {
        try { _xhrListar.abort(); } catch(e) {}
    }

    // ✅ AJUSTE MÍNIMO: se veio parâmetro, envia {p1..p6}; senão mantém serialize atual
    var dataToSend;
    if (arguments.length > 0) {
        dataToSend = {p1: p1, p2: p2, p3: p3, p4: p4, p5: p5, p6: p6};
    } else {
        dataToSend = $('#form').length ? $('#form').serialize() : {};
    }

    _xhrListar = $.ajax({
        url: 'paginas/' + pag + "/listar.php",
        method: 'POST',
        data: dataToSend,
        dataType: "html",
        success: function(result){
            $("#listar").html(result || '<div class="alert alert-info">Nenhum dado retornado.</div>');
            $('#mensagem-excluir').text('').removeClass('text-danger');
        },
        error: function(xhr){
            var msg = (xhr && xhr.responseText) ? xhr.responseText : 'Falha ao carregar a lista.';
            $("#listar").html('<div class="alert alert-danger">'+ msg +'</div>');
        }
    });
}

// --- Controle para evitar múltiplas exclusões simultâneas ---
var _xhrExcluir = null;
var _excluindo = false;

function excluir(id){
    // Confirmação antes de excluir (sem mudar a lógica de backend)
    var confirmar = function proceed(){
        if (_excluindo) return; // evita duplo clique
        _excluindo = true;

        // Aborta exclusão anterior se ainda estiver em andamento
        if (_xhrExcluir && _xhrExcluir.readyState !== 4) {
            try { _xhrExcluir.abort(); } catch(e) {}
        }

        _xhrExcluir = $.ajax({
            url: 'paginas/' + pag + "/excluir.php",
            method: 'POST',
            data: {id: id},
            dataType: "text",
            beforeSend: function(){
                // feedback leve em exclusão (opcional; não altera DOM externo)
                if ($('#mensagem-excluir').length) {
                    $('#mensagem-excluir').removeClass('text-danger').text('Excluindo...');
                }
            },
            success: function (mensagem) {
                if (mensagem && mensagem.trim() === "Excluído com Sucesso") {
                    listar();
                } else {
                    if ($('#mensagem-excluir').length) {
                        $('#mensagem-excluir').addClass('text-danger').text(mensagem || 'Erro ao excluir');
                    } else {
                        alert(mensagem || 'Erro ao excluir');
                    }
                }
            },
            error: function(xhr){
                var msg = (xhr && xhr.responseText) ? xhr.responseText : 'Erro ao excluir.';
                if ($('#mensagem-excluir').length) {
                    $('#mensagem-excluir').addClass('text-danger').text(msg);
                } else {
                    alert(msg);
                }
            },
            complete: function(){
                _excluindo = false;
            }
        });
    };

    // Usa SweetAlert2 se estiver carregado; senão, confirm nativo
    if (typeof Swal !== 'undefined' && Swal && typeof Swal.fire === 'function') {
        Swal.fire({
            title: 'Excluir?',
            text: 'Esta ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Excluir',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            focusCancel: true
        }).then(function(result){
            if (result.isConfirmed) confirmar();
        });
    } else {
        if (confirm('Tem certeza que deseja excluir?')) confirmar();
    }
}

function ativar(id, acao){
    $.ajax({
        url: 'paginas/' + pag + "/mudar-status.php",
        method: 'POST',
        data: {id, acao},
        dataType: "text",

        success: function (mensagem) {            
            if (mensagem.trim() == "Alterado com Sucesso") {                
                listar();                
            } else {
                $('#mensagem-excluir').addClass('text-danger')
                $('#mensagem-excluir').text(mensagem)
            }

        },      

    });
}

function inserir(){
    $('#mensagem').text('').removeClass();
    $('#titulo_inserir').text('Inserir Registro');
    $('#modalForm').modal('show');
    // manter chamada a limparCampos() pois outras páginas podem implementá-la
    if (typeof limparCampos === 'function') {
        limparCampos();
    }
}

// ✅ AJUSTE MÍNIMO: mantém compatibilidade com páginas que chamam inserirW()
function inserirW(){
    $('#mensagem').text('').removeClass();
    $('#modalForm').modal('show');
}

// ✅ AJUSTE MÍNIMO: mantém compatibilidade com páginas que chamam conectar()
function conectar(){
    $('#mensagem').text('').removeClass();
    $('#conectar').modal('show');
}

// Máscara de valor (mantida)
function mascara_valor(valor) {
    var valorAlterado = $('#'+valor).val();
    valorAlterado = valorAlterado.replace(/\D/g, ""); // Remove todos os não dígitos
    valorAlterado = valorAlterado.replace(/(\d+)(\d{2})$/, "$1,$2"); // Adiciona a parte de centavos
    valorAlterado = valorAlterado.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1."); // Adiciona pontos a cada três dígitos
    $('#'+valor).val(valorAlterado);
}

// Envio de WhatsApp (mantido; apenas tratado erros/complete já existiam)
function enviarWhats() {
    let mensagem = document.getElementById('mensagem_whatsapp') ? document.getElementById('mensagem_whatsapp').value : '';
    let id = document.getElementById('id_whats') ? document.getElementById('id_whats').value : '';
    let tipo = document.getElementById('tipo_whats') ? document.getElementById('tipo_whats').value : '';

    if (typeof alertWarning === 'function') {
        if (mensagem.trim() === '') {
            alertWarning('Por favor, insira uma mensagem para enviar!');
            return;
        }
    } else {
        if (mensagem.trim() === '') {
            alert('Por favor, insira uma mensagem para enviar!');
            return;
        }
    }

    $('#btn_enviar_whats').hide();
    $('#btn_carregando_whats').show();

    $.ajax({
        url: 'apis/enviar_whatsapp.php',
        method: 'POST',
        data: { id: id, mensagem: mensagem, tipo: tipo },
        success: function (response) {
            $('#btn_enviar_whats').show();
            $('#btn_carregando_whats').hide();

            if (response === 'Mensagem enviada com sucesso!') {
                if (typeof alertSucesso === 'function') alertSucesso(response);
                else alert(response);
                $('#mensagem_whatsapp').val('');
            } else {
                if (typeof alertErro === 'function') alertErro(response);
                else alert(response || 'Erro ao enviar mensagem.');
            }
        },
        error: function (xhr, status, error) {
            $('#btn_enviar_whats').show();
            $('#btn_carregando_whats').hide();
            var msg = 'Erro ao enviar mensagem: ' + (error || (xhr && xhr.responseText) || 'Desconhecido');
            if (typeof alertErro === 'function') alertErro(msg);
            else alert(msg);
        },
        complete: function () {
            $('#mensagem_whatsapp').val(''); // Limpa o campo de mensagem
        }
    });
}
