<?php
date_default_timezone_set('America/Sao_Paulo');

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../sistema/conexao.php');

$tabela = 'manutencoes_protese';
$hoje = date('Y-m-d');

/*
	Ajuste esta rota somente se o seu link real de agendamento for outro.
	Exemplo:
	$rota_agendamento = 'sistema/agendamentos';
*/
$rota_agendamento = 'agendamentos';

$nome_sistema_cron = 'BarberBot';
if (isset($nome_sistema) && $nome_sistema != '') {
	$nome_sistema_cron = $nome_sistema;
}

$url_sistema_cron = '';
if (isset($url_sistema) && $url_sistema != '') {
	$url_sistema_cron = $url_sistema;
}

if ($url_sistema_cron != '' && substr($url_sistema_cron, -1) != '/') {
	$url_sistema_cron .= '/';
}

$arquivo_envio = __DIR__ . '/../ajax/api-texto.php';

if (!file_exists($arquivo_envio)) {
	echo 'Arquivo de envio WhatsApp não encontrado';
	exit();
}

$query = $pdo->prepare("
	SELECT 
		m.id,
		m.cliente,
		m.id_protese,
		m.proxima_manutencao,
		m.alerta_manutencao_enviado,
		c.nome AS nome_cliente,
		c.telefone,
		p.modelo AS nome_protese
	FROM $tabela m
	LEFT JOIN clientes c ON m.cliente = c.id
	LEFT JOIN proteses p ON m.id_protese = p.id
	WHERE m.proxima_manutencao IS NOT NULL
		AND m.proxima_manutencao != '0000-00-00'
		AND m.proxima_manutencao < :hoje
		AND (m.alerta_manutencao_enviado = 0 OR m.alerta_manutencao_enviado IS NULL)
	ORDER BY m.proxima_manutencao ASC
");

$query->bindValue(':hoje', $hoje);
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

if (count($res) == 0) {
	echo 'Nenhuma manutenção vencida para alertar';
	exit();
}

$total_enviados = 0;
$total_ignorados = 0;

for ($i = 0; $i < count($res); $i++) {

	$id = $res[$i]['id'];
	$cliente = $res[$i]['cliente'];
	$id_protese = $res[$i]['id_protese'];

	$nome = isset($res[$i]['nome_cliente']) ? trim($res[$i]['nome_cliente']) : '';
	$nome_protese = isset($res[$i]['nome_protese']) ? trim($res[$i]['nome_protese']) : '';
	$proxima_manutencao = isset($res[$i]['proxima_manutencao']) ? trim($res[$i]['proxima_manutencao']) : '';
	$telefone_cliente = isset($res[$i]['telefone']) ? trim($res[$i]['telefone']) : '';

	if ($telefone_cliente == '') {
		$total_ignorados++;
		continue;
	}

	$telefone = preg_replace('/[^0-9]/', '', $telefone_cliente);

	if ($telefone == '') {
		$total_ignorados++;
		continue;
	}

	if (substr($telefone, 0, 2) != '55') {
		$telefone = '55' . $telefone;
	}

	$data_ts = strtotime($proxima_manutencao);
	if (!$data_ts) {
		$total_ignorados++;
		continue;
	}

	if ($nome == '') {
		$nome = 'cliente';
	}

	if ($nome_protese == '') {
		$nome_protese = 'Prótese capilar';
	}

	$data_proxima_f = date('d/m/Y', $data_ts);

	$link_agendamento = $url_sistema_cron . ltrim($rota_agendamento, '/');
	$link_agendamento .= '?cliente=' . $cliente . '&id_protese=' . $id_protese;

	$mensagem  = "👋 *Olá {$nome}*, tudo bem?\n\n";
	$mensagem .= "🚨 Identificamos que a sua *manutenção de prótese capilar* está vencida.\n\n";
	$mensagem .= "📅 *Data prevista:* {$data_proxima_f}\n";
	$mensagem .= "💇 *Prótese:* {$nome_protese}\n\n";
	$mensagem .= "Para manter o melhor resultado, recomendamos realizar o agendamento da sua próxima manutenção.\n\n";
	$mensagem .= "🔗 *Clique abaixo para agendar:*\n";
	$mensagem .= "{$link_agendamento}\n\n";
	$mensagem .= "Atenciosamente,\n*{$nome_sistema_cron}*";

	include($arquivo_envio);

	$query_update = $pdo->prepare("
		UPDATE $tabela SET 
			alerta_manutencao_enviado = 1,
			data_alerta_manutencao = NOW()
		WHERE id = :id
	");
	$query_update->bindValue(':id', $id);
	$query_update->execute();

	$total_enviados++;
}

echo 'Alertas enviados: ' . $total_enviados . ' | Ignorados: ' . $total_ignorados;
?>