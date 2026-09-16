<?php
define('SEC_WAF_MODE', 'detect');
require_once __DIR__ . '/../security.php';

require_once __DIR__ . '/../sistema/conexao.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: text/html; charset=utf-8');

$texto_agendamento = $texto_agendamento ?? 'Selecione';

// entradas
$serv = (int)($_POST['serv'] ?? 0);
$idAg = (int)($_POST['id'] ?? 0);

$funcionarioSel = 0;
$nomeSel = '';
$servicoAg = 0;

/**
 * Se veio ID de agendamento (edição), pega servico/funcionario do agendamento
 */
if ($idAg > 0) {
    $stAg = $pdo->prepare("SELECT servico, funcionario FROM agendamentos WHERE id = :id LIMIT 1");
    $stAg->bindValue(':id', $idAg, PDO::PARAM_INT);
    $stAg->execute();
    $resAg = $stAg->fetch(PDO::FETCH_ASSOC);

    if (is_array($resAg)) {
        $servicoAg = (int)($resAg['servico'] ?? 0);

        // só pré-seleciona se o serviço do agendamento for o mesmo serviço atual
        if ($servicoAg === $serv) {
            $funcionarioSel = (int)($resAg['funcionario'] ?? 0);

            if ($funcionarioSel > 0) {
                $stU = $pdo->prepare("SELECT nome FROM usuarios WHERE id = :id LIMIT 1");
                $stU->bindValue(':id', $funcionarioSel, PDO::PARAM_INT);
                $stU->execute();
                $nomeSel = (string)$stU->fetchColumn();
            }
        }
    }
}

/**
 * Sempre imprime o placeholder primeiro
 */
echo '<option value="">' . htmlspecialchars($texto_agendamento, ENT_QUOTES, 'UTF-8') . '</option>';

/**
 * Se tem profissional selecionado (edição), imprime ele como selected
 */
if ($funcionarioSel > 0 && $nomeSel !== '') {
    echo '<option value="' . (int)$funcionarioSel . '" selected>' .
         htmlspecialchars($nomeSel, ENT_QUOTES, 'UTF-8') .
         '</option>';
}

/**
 * Se não tem serviço válido, para por aqui (não lista profissionais)
 */
if ($serv <= 0) {
    exit;
}

/**
 * Lista profissionais do serviço (ativos e atendimento = Sim), excluindo o selecionado
 */
$sql = "
    SELECT u.id, u.nome, u.foto
    FROM usuarios u
    INNER JOIN servicos_func sf ON sf.funcionario = u.id
    WHERE u.ativo = 'Sim'
      AND u.atendimento = 'Sim'
      AND sf.servico = :serv
";
$params = [':serv' => $serv];

if ($funcionarioSel > 0) {
    $sql .= " AND u.id <> :sel ";
    $params[':sel'] = $funcionarioSel;
}

$sql .= " ORDER BY u.nome ASC ";

$st = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $st->bindValue($k, $v, PDO::PARAM_INT);
}
$st->execute();
$lista = $st->fetchAll(PDO::FETCH_ASSOC);

if ($lista) {
    foreach ($lista as $row) {
        $id   = (int)($row['id'] ?? 0);
        $nome = (string)($row['nome'] ?? '');
        $foto = (string)($row['foto'] ?? '');

        if ($id <= 0 || $nome === '') continue;

        echo '<option value="' . $id . '" data-foto="' . htmlspecialchars($foto, ENT_QUOTES, 'UTF-8') . '">' .
             htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') .
             '</option>';
    }
}
?>

<script>
$(document).ready(function() {
  if ($.fn.select2) {
    $('select').select2({
      templateResult: formatarOpcao,
      templateSelection: formatarOpcao
    });
  }
});

function formatarOpcao(opcao) {
  if (!opcao.id) return opcao.text;

  // aqui era data('imagem') — mas você usa data-foto
  var foto = $(opcao.element).data('foto');
  if (!foto) return opcao.text;

  var $opcao = $(
    '<span><img src="sistema/painel/img/perfil/' + foto + '" width="20" height="20" style="margin-right:5px;border-radius:50%;" /> ' +
    opcao.text + '</span>'
  );

  return $opcao;
}
</script>
