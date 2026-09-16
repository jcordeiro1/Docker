<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once("../sistema/conexao.php");

$id = $_POST['id'] ?? false;
  header('Content-Type: application/json');

if($id)
{
    $query = "SELECT * FROM agendamentos WHERE id = '$id' and (valor_pago != '0.00' and valor_pago IS NOT NULL)";
    $agenda = $pdo->query($query);
    $agendamentos = $agenda->fetch(PDO::FETCH_ASSOC);
    
    if($agendamentos)
    {
        echo json_encode(['status' => 'pago']);
        exit();
        
    }else
    {
        echo json_encode(['status' => 'aberta']);
        exit();
    }
    
    
}
else{
    echo json_encode(['status' => 'erro']);
    exit();
}

