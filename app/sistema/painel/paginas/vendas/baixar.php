<?php
require_once("../../../conexao.php");
$tabela = 'receber';
@session_start();
$id_usuario = $_SESSION['id'];

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0; // cast seguro

// verificar caixa aberto (mantido)
$query1 = $pdo->query("SELECT * FROM caixas WHERE operador = '$id_usuario' AND data_fechamento IS NULL ORDER BY id DESC LIMIT 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if(@count($res1) > 0){
  $id_caixa = @$res1[0]['id'];
}else{
  $id_caixa = 0;
}

// UPDATE usando prepare (mesma lógica)
$st = $pdo->prepare("UPDATE $tabela 
                        SET pago = 'Sim',
                            usuario_baixa = :usuario_baixa,
                            data_pgto = curDate(),
                            caixa = :caixa,
                            hora = curTime()
                      WHERE id = :id");
$st->execute([
  ':usuario_baixa' => $id_usuario,
  ':caixa'         => $id_caixa,
  ':id'            => $id
]);

echo 'Baixado com Sucesso';
