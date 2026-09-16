// assets/js/app.js

function carregarPag(pagina) {
  const url = 'paginas/' + pagina + '.php';

  $.ajax({
    url: url,
    method: 'GET',
    beforeSend: function () {
      $('#container').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Carregando...</div>');
    },
    success: function (dados) {
      $('#container').html(dados);
      history.pushState(null, null, 'index.php?pag=' + pagina);
    },
    error: function () {
      $('#container').html('<div class="alert alert-danger">Erro ao carregar a página: ' + url + '</div>');
    }
  });
}

// Suporte ao botão "voltar" do navegador
window.onpopstate = function () {
  const pag = new URLSearchParams(window.location.search).get('pag');
  if (pag) carregarPag(pag);
};

// Carregamento inicial (caso URL tenha ?pag=)
$(document).ready(function () {
  const pag = new URLSearchParams(window.location.search).get('pag');
  if (pag) carregarPag(pag);
});
