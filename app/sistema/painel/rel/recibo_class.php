<?php 
include('../../conexao.php');

$id     = (int)($_REQUEST['id'] ?? 0);
$enviar = $_REQUEST['enviar'] ?? '';

if ($id <= 0) {
  die('ID inválido');
}

/**
 * Carrega o HTML do layout (recibo.php)
 * – Esse é o “modelo visual” que você já estilizou.
 */
$url_html = rtrim($url_sistema, '/')."/sistema/painel/rel/recibo.php?id={$id}";
$html = @file_get_contents($url_html);

if ($html === false || $html === '') {
  die('Não foi possível carregar o layout do recibo.');
}

/**
 * Se o sistema estiver configurado para NÃO gerar PDF,
 * apenas exibe o HTML direto no navegador.
 */
if (!isset($tipo_rel) || $tipo_rel !== 'PDF') {
  // Mostra o HTML renderizado (com seu CSS/JS) sem PDF
  header('Content-Type: text/html; charset=UTF-8');
  echo $html;
  exit;
}

/* ===========================
   GERAÇÃO DE PDF COM DOMPDF
   =========================== */
require_once '../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

// headers corretos para PDF (o Dompdf já cuida no stream, mas deixo explícito)
header_remove('Content-Type');
header('Content-Type: application/pdf');

// Opções do Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);  // permite imagens remotas/absolutas
$options->set('defaultFont', 'Helvetica');

$pdf = new Dompdf($options);

/**
 * Papel:
 * - A4 retrato (combina com seu recibo/“modelo Chrome”)
 * - Se você usa bobina térmica, substitua por:
 *   $pdf->set_paper(array(0, 0, 320.28, 250.89));
 */
$pdf->setPaper('A4', 'portrait');

// Carrega e renderiza
$pdf->loadHtml($html);
$pdf->render();

// Salva uma cópia em disco (opcional)
$output  = $pdf->output();
$pasta   = __DIR__ . "/../pdf";
if (!is_dir($pasta)) { @mkdir($pasta, 0775, true); }
$arquivo = $pasta . "/recibo_{$id}.pdf";
@file_put_contents($arquivo, $output);

// Envia o PDF para o navegador (abre o viewer do Chrome com zoom/impressora)
$nomeExibicao = "Recibo_{$id}.pdf";
if (function_exists('ob_get_length')) { while (ob_get_level()) { ob_end_clean(); } }
$pdf->stream($nomeExibicao, ['Attachment' => false]); // false = abrir no navegador

// ======= Envio opcional no WhatsApp (se houver token/instância) =======
$query   = $pdo->query("SELECT * FROM receber WHERE id = '$id' ");
$res     = $query->fetchAll(PDO::FETCH_ASSOC);
$cliente = $res[0]['cliente'] ?? ($res[0]['pessoa'] ?? 0);
$parcela = $res[0]['parcela'] ?? '';

$query   = $pdo->query("SELECT * FROM clientes WHERE id = '$cliente' ");
$resC    = $query->fetchAll(PDO::FETCH_ASSOC);
$telefone= $resC[0]['telefone'] ?? '';

if (!empty($token) && !empty($instancia) && !empty($telefone)) {
  $telefone_envio = '55'.preg_replace('/[ ()-]+/', '', $telefone);
  $mensagem       = 'Recibo_Parcela_'.$parcela;
  $url_envio      = rtrim($url_sistema, '/')."/sistema/painel/pdf/recibo_{$id}.pdf";
  require("../../../ajax/file.php");
}
