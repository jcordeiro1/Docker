<?php 
require_once("../../../conexao.php");
$tabela = 'pagar';
@session_start();
$id_usuario = $_SESSION['id'];

$id        = $_POST['id'];
$valor     = $_POST['valor'];
// normaliza valor: "1.234,56" -> "1234.56"
$valor = str_replace('.', '', $valor);
$valor = str_replace(',', '.', $valor);

$data_pgto = $_POST['data_pgto'];
$pgto      = $_POST['pgto'];

// verificar caixa aberto
$query1 = $pdo->query("SELECT * FROM caixas WHERE operador = '$id_usuario' AND data_fechamento IS NULL ORDER BY id DESC LIMIT 1");
$res1   = $query1->fetchAll(PDO::FETCH_ASSOC);
if (@count($res1) > 0) {
	$id_caixa = @$res1[0]['id'];
} else {
	$id_caixa = 0;
}

// baixa a comissão
$pdo->query("UPDATE $tabela 
                SET pgto = '$pgto', 
                    valor = '$valor', 
                    pago = 'Sim', 
                    usuario_baixa = '$id_usuario', 
                    data_pgto = '$data_pgto', 
                    caixa = '$id_caixa', 
                    hora = CURTIME() 
              WHERE id = '$id'");

echo 'Baixado com Sucesso';
?>
