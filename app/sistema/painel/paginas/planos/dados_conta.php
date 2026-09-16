<?php
require_once("../../../conexao.php");
$id = $_POST['id'];
$query = $pdo->query("SELECT * FROM receber WHERE id = '$id' LIMIT 1");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
    $telefone = '';
    $descricao = $res[0]['descricao'];
    $data = $res[0]['data_venc'];
    // Busca telefone do cliente
    if(!empty($res[0]['pessoa'])){
        $id_cliente = $res[0]['pessoa'];
        $q = $pdo->query("SELECT telefone FROM clientes WHERE id = '$id_cliente'");
        $r = $q->fetchAll(PDO::FETCH_ASSOC);
        if(@count($r) > 0){
            $telefone = $r[0]['telefone'];
        }
    }
    echo json_encode([
        'telefone' => $telefone,
        'descricao' => $descricao,
        'data' => $data
    ]);
} else {
    echo json_encode([]);
}
?>
