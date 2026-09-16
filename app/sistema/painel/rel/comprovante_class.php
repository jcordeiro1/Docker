<?php
include('../../conexao.php');

$id = (int)($_REQUEST['id'] ?? 0);
if ($id <= 0) { die('ID inválido'); }

// carrega o HTML do layout
$url_html = rtrim($url_sistema, '/')."/sistema/painel/rel/comprovante.php?id={$id}";
$html = @file_get_contents($url_html);
if ($html === false || $html === '') {
  die('Não foi possível carregar o layout do comprovante.');
}

// se o sistema não estiver em modo PDF, só exibe o HTML
if (!isset($tipo_rel) || $tipo_rel !== 'PDF') {
  header('Content-Type: text/html; charset=UTF-8');
  echo $html;
  exit;
}

require_once '../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$pdf = new Dompdf($options);

// A4 retrato (combina com o layout que você quer)
$pdf->setPaper('A4', 'portrait');

$pdf->loadHtml($html);
$pdf->render();

// salva uma cópia (opcional)
$output   = $pdf->output();
$pasta    = __DIR__ . "/../pdf";
if (!is_dir($pasta)) { @mkdir($pasta, 0775, true); }
$arquivo  = $pasta . "/comprovante_{$id}.pdf";
@file_put_contents($arquivo, $output);

// abre no viewer do navegador (zoom/imprimir)
$nomeExibicao = "Comprovante_{$id}.pdf";
if (function_exists('ob_get_length')) { while (ob_get_level()) { ob_end_clean(); } }
$pdf->stream($nomeExibicao, ['Attachment' => false]);

// (opcional) enviar por WhatsApp se você usa isso
/*
$cli = $pdo->query("SELECT pessoa FROM receber WHERE id = '$id'")->fetch(PDO::FETCH_ASSOC);
$cliente = $cli['pessoa'] ?? 0;
if (!empty($token) && !empty($instancia) && $cliente) {
  $c = $pdo->query("SELECT telefone FROM clientes WHERE id = '$cliente'")->fetch(PDO::FETCH_ASSOC);
  $telefone_envio = '55'.preg_replace('/[ ()-]+/', '', ($c['telefone'] ?? ''));
  if ($telefone_envio) {
    $mensagem  = 'Comprovante';
    $url_envio = rtrim($url_sistema, '/')."/sistema/painel/pdf/comprovante_{$id}.pdf";
    require("../../../ajax/file.php");
  }
}
*/
