<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
require_once("../sistema/conexao.php");

$data =  date('Y-m-d');
$datePT = date('d/m/Y');
$query = "SELECT * FROM receber WHERE data_venc = '$data' AND pago != 'Sim' AND recorrencia = 'Sim' ";
$receber = $pdo->query($query);
$faturas = $receber->fetchAll(PDO::FETCH_ASSOC);



foreach($faturas as $fatura)
{
    $grupo = $fatura['grupo'];
    $descricao = $fatura['descricao'];
    $valor = $fatura['valor'];
    $usuario = $fatura['pessoa'];
    $idCobranca = $fatura['id'];
    $parcela = $fatura['parcela'];
    $id_ref = $fatura['id_ref'];
    $frequencia = $fatura['frequencia']; 
    $Nparcela = $parcela + 1;
    $usuario_lanc =  $fatura['usuario_lanc'];
    $data_venc = date('Y-m-d', strtotime($data . ' +1 month'));

    
    //Obtendo valor da fatura sem residuos ou calculos
    $query = "SELECT * FROM cobrancas WHERE id = '$id_ref' ";
    $cobrancas = $pdo->query($query);
    $cobrancas = $cobrancas->fetchAll(PDO::FETCH_ASSOC);
    $valorF = $cobrancas[0]['valor'];
    
    //Obtendo dados do cliente
    $query = "SELECT * FROM clientes WHERE id = '$usuario'";
    $cliente  = $pdo->query($query);
    $message .= "*Link de Pagamento:*\n{$url_sistema}pagar/{$idCobranca}";
    
    $nome = $resCliente[0]['nome'];
    $telefone = $resCliente[0]['telefone'] ?? 0;
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    $telefone = strlen($telefone) <= 11 && strlen($telefone)  > 1 ? '55'. $telefone : $telefone;
    
    // Body Mensagem
    $message = "_Seu pagamento vence hoje {$nome}_\n";
    $message .= "Valor: *{$valor}*\n";
    $message .= "Data: *{$datePT}*\n\n";
    $message .= "*Link de Pagamento:*$url_sistemapagar/$idCobranca";
    
    
    //Enviando cobrança Atual
    if($telefone != 0)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://chatbot.menuia.com/api/create-message',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array(
          'appkey' => $token,
          'authkey' => $instancia,
          'to' => $telefone,
          'message' => $message,
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        
    }
    
    
    // Gerando uma nova cobrança para o mês seguinte
    $query = $pdo->prepare("INSERT INTO receber (tipo, valor, data_lanc, data_venc, usuario_lanc, pessoa, cliente, referencia, parcela, descricao, grupo, id_ref, frequencia) VALUES (:tipo, :valor, :data_lanc, :data_venc, :usuario_lanc, :pessoa, :cliente, :referencia, :parcela, :descricao, :grupo, :id_ref, :frequencia)");

    // Bind dos parâmetros
    $query->bindParam(':descricao', $descricao, PDO::PARAM_STR);
    $query->bindParam(':grupo', $grupo, PDO::PARAM_STR);
    $query->bindValue(':tipo', 'Serviço', PDO::PARAM_STR);
    $query->bindParam(':valor', $valorF, PDO::PARAM_INT);
    $query->bindParam(':data_lanc', $data, PDO::PARAM_STR);
    $query->bindParam(':data_venc', $data_venc, PDO::PARAM_STR);
    $query->bindParam(':usuario_lanc', $usuario_lanc, PDO::PARAM_STR);
    $query->bindParam(':pessoa', $usuario, PDO::PARAM_STR);
    $query->bindParam(':cliente', $usuario, PDO::PARAM_STR);
    $query->bindValue(':referencia', '0', PDO::PARAM_INT);
    $query->bindValue(':parcela', $Nparcela, PDO::PARAM_INT);
    $query->bindValue(':id_ref', $id_ref, PDO::PARAM_INT);
    $query->bindValue(':frequencia', $frequencia, PDO::PARAM_INT);
    $result = $query->execute();

    if ($result) {
        echo "Cobrança gerada com sucesso!";
        exit();
    } else {
        echo "Ops! Erro ao gerar cobrança";
        exit();
    }

    
}
    
echo "Nenhuma cobrança para hoje!";
?>