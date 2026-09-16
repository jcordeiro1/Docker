<?php 
require_once("../sistema/conexao.php");

// Sanitização do ID (evita SQL Injection e mantém padrão de entrada)
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
	echo 'Cancelado com Sucesso';
	exit;
}

// Busca o agendamento com prepared statement (mantém a mesma lógica do fluxo)
$st = $pdo->prepare("SELECT * FROM agendamentos WHERE id = :id LIMIT 1");
$st->execute([':id' => $id]);
$ag = $st->fetch(PDO::FETCH_ASSOC);

if (!$ag) {
	echo 'Cancelado com Sucesso';
	exit;
}

$cliente = $ag['cliente'] ?? '';
$usuario = ($ag['funcionario'] ?? '').'';
$data = $ag['data'] ?? '';
$hora = $ag['hora'] ?? '';
$servico = $ag['servico'] ?? '';
$hash = $ag['hash'] ?? '';

$dataF = $data ? implode('/', array_reverse(explode('-', $data))) : '';
$horaF = $hora ? date("H:i", strtotime($hora)) : '';

// Busca cliente
$nome_cliente = '';
$telefone = '';
$st = $pdo->prepare("SELECT nome, telefone FROM clientes WHERE id = :id LIMIT 1");
$st->execute([':id' => $cliente]);
$cli = $st->fetch(PDO::FETCH_ASSOC);
if($cli){
	$nome_cliente = $cli['nome'] ?? '';
	$telefone = $cli['telefone'] ?? '';
}

/**
 * PONTO DO AJUSTE:
 * Ao excluir/cancelar um agendamento, também cancelar a mensagem agendada (Confirmação) na API.
 * Isso evita o envio de "Confirmação de Agendamento" após o agendamento ter sido removido.
 */
$hash = trim((string)$hash);
if($hash !== ''){
	require('agendar-delete.php');
}

// Exclui do banco (mesma regra, agora com prepared statements)
$st = $pdo->prepare("DELETE FROM agendamentos WHERE id = :id");
$st->execute([':id' => $id]);

$st = $pdo->prepare("DELETE FROM horarios_agd WHERE agendamento = :id");
$st->execute([':id' => $id]);

echo 'Cancelado com Sucesso';

if($not_sistema == 'Sim'){
	$mensagem_not = $nome_cliente;
	$titulo_not = 'Agendamento Cancelado '.$dataF.' - '.$horaF;
	$id_usu = $usuario;
	require('../api/notid.php');
} 



if($msg_agendamento == 'Api'){

	$st = $pdo->prepare("SELECT nome, telefone FROM usuarios WHERE id = :id LIMIT 1");
	$st->execute([':id' => $usuario]);
	$func = $st->fetch(PDO::FETCH_ASSOC);
	$nome_func = $func['nome'] ?? '';
	$tel_func = $func['telefone'] ?? '';

	$st = $pdo->prepare("SELECT nome FROM servicos WHERE id = :id LIMIT 1");
	$st->execute([':id' => $servico]);
	$nome_serv = $st->fetchColumn();
	$nome_serv = $nome_serv ? $nome_serv : '';

	$mensagem = '_Agendamento Cancelado_ %0A';
	$mensagem .= 'Profissional: *'.$nome_func.'* %0A';
	$mensagem .= 'Serviço: *'.$nome_serv.'* %0A';
	$mensagem .= 'Data: *'.$dataF.'* %0A';
	$mensagem .= 'Hora: *'.$horaF.'* %0A';
	$mensagem .= 'Cliente: *'.$nome_cliente.'* %0A';

	$telefone = '55'.preg_replace('/[ ()-]+/' , '' , (string)$telefone);
	require('api-texto.php');

	if($tel_func != $whatsapp_sistema){
		$telefone = '55'.preg_replace('/[ ()-]+/' , '' , (string)$tel_func);
		require('api-texto.php');	
	}
}

?>
