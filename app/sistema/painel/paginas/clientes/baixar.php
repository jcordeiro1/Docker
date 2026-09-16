<?php 
$tabela = 'receber';
require_once("../../../conexao.php");

@session_start();
$id_usuario = @$_SESSION['id'];

$id = $_POST['id'];
$data_baixa = $_POST['data_baixa'] ?? '';
$forma_pgto = $_POST['forma_pgto'] ?? '';
$valor_final = $_POST['valor_final'] ?? '';
$residuo = $_POST['residuo'] ?? '';

$valor_final = str_replace('.', '', $valor_final);
$valor_final = str_replace(',', '.', $valor_final);

$query2 = $pdo->query("SELECT * from receber where id = '$id'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$hash = @$res2[0]['hash'];
$cliente = @$res2[0]['cliente'];
$id_ref = @$res2[0]['id_ref'];
$valor = @$res2[0]['valor'];
$valor = str_replace('.', '', $valor);
$valor = str_replace(',', '.', $valor);

$parcela = @$res2[0]['parcela'];
$nova_parcela = $parcela + 1;
$recorrencia = @$res2[0]['recorrencia'] ?? "Nao";
$data_venc = @$res2[0]['data_venc'];
$dias_frequencia = @$res2[0]['frequencia'];
$descricao = @$res2[0]['descricao'];
$grupo =  @$res2[0]['grupo'];
$tipo =  @$res2[0]['tipo'];
$valor_baixar =  @$res2[0]['valor_baixar'];


if($token != "" and $instancia != ""){
//recuperar o hash e excluir o agendamento das mensagens
require("../../../../ajax/api-excluir.php");

$hash = @$res2[0]['hash2'];
require("../../../../ajax/api-excluir.php");

}

$query3 = $pdo->query("SELECT * FROM cobrancas WHERE id = '$id_ref' ORDER BY id ASC LIMIT 1");
$res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
$parcelas = @$res3[0]['parcelas'];
$cobranca = $res3[0];

$query5 = $pdo->query("SELECT * from receber where id_ref = '$id_ref' and tipo = '$tipo' ");
$resx = $query5->fetchAll(PDO::FETCH_ASSOC);
$cobrancas = $query5->rowCount();



$proximaData = date('Y-m-d', strtotime("+1 month", strtotime($data_venc)));
$subtotal = $valor_baixar - $valor_final;



// Tem residuo
if($valor_baixar > $valor_final && $_POST['residuo'] == "Sim")
{

    //Verificando se tem outra parcela a frente
    if($cobrancas > 1 && $cobrancas > $parcela)
    {
     
        //Adicionando residuo a proxima parcela
        $query2 = $pdo->query("SELECT * from receber where grupo = '$grupo' and data_venc = '$proximaData'");
        $result = $query2->fetchAll(PDO::FETCH_ASSOC);
        $total = $result[0]['valor'] + $subtotal;
        
        $updateQuery = "UPDATE receber SET valor = :total WHERE grupo = :grupo AND data_venc = :data_venc";
        $stmt = $pdo->prepare($updateQuery);
        $stmt->bindParam(':total', $total, PDO::PARAM_STR);
        $stmt->bindParam(':grupo', $grupo, PDO::PARAM_STR);
        $stmt->bindParam(':data_venc', $proximaData, PDO::PARAM_STR);
        $stmt->execute();
        
    }
    else
    {
        
        if($recorrencia != "Sim")
        {
             //Atualizando a quantidades de parcelas
             $updateQuery = "UPDATE cobrancas SET parcelas = :nova_parcela WHERE id = :id_ref";
    
            $stmt = $pdo->prepare($updateQuery);
            $stmt->bindParam(':nova_parcela', $nova_parcela, PDO::PARAM_INT);
            $stmt->bindParam(':id_ref', $id_ref, PDO::PARAM_INT);
            $stmt->execute();
        }
        else
        {
             $subtotal = $cobranca['valor'] + $subtotal;
        }
        
        
        //Criando uma nova parcela para receber o residuo
        $insertQuery = "INSERT INTO receber (pessoa, cliente, id_ref, valor, parcela, recorrencia, data_venc, frequencia, descricao, grupo, tipo, referencia) VALUES (:pessoa, :cliente, :id_ref, :total, :nova_parcela, :recorrencia, :data_venc, :dias_frequencia, :descricao, :grupo, :tipo, :referencia)";

        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindParam(':pessoa', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':id_ref', $id_ref, PDO::PARAM_STR);
        $stmt->bindParam(':total', $subtotal, PDO::PARAM_STR);
        $stmt->bindParam(':nova_parcela', $nova_parcela, PDO::PARAM_INT);
        $stmt->bindParam(':recorrencia', $recorrencia, PDO::PARAM_STR);
        $stmt->bindParam(':data_venc', $proximaData, PDO::PARAM_STR);
        $stmt->bindParam(':dias_frequencia', $dias_frequencia, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':grupo', $grupo, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);  
        $stmt->bindValue(':referencia', 0, PDO::PARAM_INT);    
        $stmt->execute();
    }
}
else
{

    if($recorrencia == "Sim")
    {
        $insertQuery = "INSERT INTO receber (pessoa, cliente, id_ref, valor, parcela, recorrencia, data_venc, frequencia, descricao, grupo, tipo, referencia) VALUES (:pessoa, :cliente, :id_ref, :total, :nova_parcela, :recorrencia, :data_venc, :dias_frequencia, :descricao, :grupo, :tipo, :referencia)";

        $stmt = $pdo->prepare($insertQuery);
        $stmt->bindParam(':pessoa', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':cliente', $cliente, PDO::PARAM_STR);
        $stmt->bindParam(':id_ref', $id_ref, PDO::PARAM_STR);
        $stmt->bindParam(':total', $cobranca['valor'], PDO::PARAM_STR);
        $stmt->bindParam(':nova_parcela', $nova_parcela, PDO::PARAM_INT);
        $stmt->bindParam(':recorrencia', $recorrencia, PDO::PARAM_STR);
        $stmt->bindParam(':data_venc', $proximaData, PDO::PARAM_STR);
        $stmt->bindParam(':dias_frequencia', $dias_frequencia, PDO::PARAM_STR);
        $stmt->bindParam(':descricao', $descricao, PDO::PARAM_STR);
        $stmt->bindParam(':grupo', $grupo, PDO::PARAM_STR);
        $stmt->bindValue(':tipo', $tipo, PDO::PARAM_STR);  
        $stmt->bindValue(':referencia', 0, PDO::PARAM_INT);    
        $stmt->execute();
    }
}



$pdo->query("UPDATE $tabela SET data_pgto = '$data_baixa', pago = 'Sim', pgto = '$forma_pgto', valor = '$valor_final', usuario_baixa = '$id_usuario' WHERE id = '$id' ");  
echo 'Salvo com Sucesso*'.$id.'*'.$parcela.'*'.$parcelas;

?>