<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../sistema/conexao.php");
$dados = json_decode(file_get_contents('php://input'), true);

$tipo = $dados['tipo'];

function formatarTelefone($telefone) {
    $telefone = preg_replace('/[ ()-]+/', '', $telefone);

    // Verifica se o número começa com '55'
    if (strpos($telefone, '55') !== 0) {
        $telefone = '55' . $telefone;
    }

    return $telefone;
}

if($tipo == "Sistema")
{
    $numeroCliente = $dados['destinatario'];
    $id = $dados['idAgendamento'];
    $pdo->query("UPDATE agendamentos SET phone = '$numeroCliente' where hash = '$id'"); // 01:20
}

else if($tipo == "Chat")
{
    $resposta = $dados['mensagem'];
    $numeroCliente = $dados['remetente'];
 
    $dataHoraAtual = date('Y-m-d H:i:s');

    // Montando a consulta SQL com a condição desejada
    $query = "SELECT * FROM agendamentos WHERE phone = '$numeroCliente' AND status = 'Agendado' AND CONCAT(data, ' ', hora) > '$dataHoraAtual' ORDER BY CONCAT(data, ' ', hora) ASC LIMIT 1";
        $agendamento = $pdo->query($query);
    $resAgendamento = $agendamento->fetchAll(PDO::FETCH_ASSOC); 
    $agendamento = $resAgendamento[0];
    $id_agendamento = $resAgendamento[0]['id'];
    
    
    // Dados Agendamento
    $idCliente = $resAgendamento[0]['cliente'];
    $idFuncionario = $resAgendamento[0]['funcionario'];
    $dataAgendamento = date('d/m/Y', strtotime($resAgendamento[0]['data'])); // data Formatada para padrao 00/00/0000
    $horarioAgendamento = $resAgendamento[0]['hora'];
    
    // Buscando infos cliente
    $query = "SELECT * FROM clientes where id = '$idCliente'";
    $cliente = $pdo->query($query);
    $resCliente = $cliente->fetchAll(PDO::FETCH_ASSOC);
    $cliente = $resCliente[0];
    $nomeCliente = $cliente['nome'];
    
    
    // Buscando infos funcionario
    $query = "SELECT * FROM usuarios where id = '$idFuncionario'";
    $funcionario = $pdo->query($query);
    $resFuncionario = $funcionario->fetchAll(PDO::FETCH_ASSOC);
    $funcionario = $resFuncionario[0];
    $nomeFuncionario = $funcionario['nome'];
    $telefoneFuncionario = $funcionario['telefone'];
    
    if($agendamento)
    {
      
        if($resposta == 1)
        {
           $pdo->query("UPDATE agendamentos SET status = 'Confirmado' where id = '$id_agendamento'");
           $mensagem = "✅ _Confirmado!_\n\n";
           $mensagem .= "Agendamento de *".$nomeCliente."*.\n\n";
           $mensagem .= "*Data:* ".$dataAgendamento." às ".$horarioAgendamento."\n\n";
           $mensagem .="O profissional *".$nomeFuncionario."* lhe aguarda no horário agendado.\n\n";
           $mensagem .="_Obrigado!_";
           
            //Notifica o cliente
            $telefone = formatarTelefone($numeroCliente) ;
            
            require('api-texto.php');
            
            //Notifica o Dono da plataforma
            //$telefone = '55'.preg_replace('/[ ()-]+/' , '' , $whatsapp_sistema);
            //require('api-texto.php');
            
            //Notifica o funcionario se ele nao for o dono
            if($telefoneFuncionario != $whatsapp_sistema)
            {
            	$telefone =  formatarTelefone($telefoneFuncionario);
            	require('api-texto.php');	
            }
            
            return "Confirmado!";
        } 
        else if($resposta == 2)
        {
            $pdo->query("DELETE FROM agendamentos where id = '$id_agendamento'");
            $pdo->query("DELETE FROM horarios_agd where agendamento = '$id_agendamento'");
            $mensagem = "❌ _Cancelado!_\n\n";
           $mensagem .= "Agendamento de *".$nomeCliente."*\n\n";
           $mensagem .= "*Data:* ".$dataAgendamento." às ".$horarioAgendamento."\n\n";
           $mensagem .=" Reagende novo horário pelo site: ".$url_sistema;
           
            //Notifica o cliente
            $telefone = formatarTelefone($numeroCliente);
            require('api-texto.php');
            
            //Notifica o Dono da plataforma
            $telefone = formatarTelefone($whatsapp_sistema);
            require('api-texto.php');
            
            //Notifica o funcionario se ele nao for o dono
            if($telefoneFuncionario != $whatsapp_sistema)
            {
            	$telefone = formatarTelefone($telefoneFuncionario);
            	require('api-texto.php');	
            }
            return "Cancelado!";
        }
        else
        {
            $mensagem = "Desculpe! responda apenas com: \n\n ✔ *1* Para confirmar.\n ❌ *2* para cancelar. ";
            
            //Notifica o cliente
            $telefone = formatarTelefone($numeroCliente);
            require('api-texto.php');
        }
    
    
    
    
    }
}

?>