<?php  
require_once("../verificar.php");
require_once("../../conexao.php");
require_once("data_formatada.php");

// Recebe o ID via POST ou GET (flexível para ambos)
$id = @$_POST['id'];
if(!$id && isset($_GET['id'])) $id = $_GET['id']; // fallback se vier por GET

$token_rel = 'A5030'; // ajuste se quiser autenticação por token (igual rel_produtos)

// Usa buffer de saída para capturar o HTML do relatório
ob_start();
include("anotacoes.php"); // Use o arquivo local!
$html = ob_get_clean();

// Carrega DomPDF
require_once '../../dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;

header("Content-Transfer-Encoding: binary");
header("Content-Type: application/pdf"); // correto para PDF

$options = new Options();
$options->set('isRemoteEnabled', TRUE);

$pdf = new Dompdf($options);
$pdf->set_paper('A4', 'portrait');
$pdf->load_html($html);
$pdf->render();

$pdf->stream(
    'anotacoes.pdf',
    array("Attachment" => false)
);
?>

