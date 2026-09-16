<?php
// Dados de conexão: em Docker vêm do ambiente; os fallbacks mantêm compatibilidade local.
$servidor = getenv('DB_HOST') ?: 'localhost';
$porta = getenv('DB_PORT') ?: '3306';
$banco = getenv('DB_NAME') ?: 'barber';
$usuario = getenv('DB_USER') ?: 'root';
$senha = getenv('DB_PASSWORD') ?: '';

$modo_teste = 'Não';

$appUrl = trim((string)(getenv('APP_URL') ?: ''));
if ($appUrl !== '') {
	$url_sistema = rtrim($appUrl, '/') . '/';
} else {
	$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
	$url_sistema = $scheme . '://' . $host . '/';
}

date_default_timezone_set('America/Sao_Paulo');

try {
	$pdo = new PDO("mysql:dbname=$banco;host=$servidor;port=$porta;charset=utf8mb4", "$usuario", "$senha");
} catch (Exception $e) {
	echo 'Não conectado ao Banco de Dados! <br><br>' .$e;
}

//variaveis para os disparos de notificações
$hora_rand = rand(8, 10);
$minutos_rand = rand(0, 59);
if($hora_rand < 10){
	$hora_rand = '0'.$hora_rand;
}
if($minutos_rand < 10){
	$minutos_rand = '0'.$minutos_rand;
}	

$hora_random = $hora_rand.':'.$minutos_rand.':00';

//VARIAVEIS DO SISTEMA
$nome_sistema = getenv('APP_NAME') ?: 'BarberBot Docker Demo';
$email_sistema = getenv('APP_CONTACT_EMAIL') ?: 'contato@example.test';
$whatsapp_sistema = getenv('APP_CONTACT_WHATSAPP') ?: '';
$not_sistema = 'Sim';

$query = $pdo->query("SELECT * from config ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);

if($total_reg == 0){
	$pdo->query("INSERT INTO config SET 
		nome = '$nome_sistema', 
		email = '$email_sistema', 
		telefone_whatsapp = '$whatsapp_sistema', 
		logo = 'logo.png', 
		icone = 'favicon.ico', 
		logo_rel = 'logo_rel.jpg', 
		tipo_rel = 'pdf', 
		tipo_comissao = 'Porcentagem', 
		texto_rodape = 'Edite este texto nas configurações do painel administrador', 
		img_banner_index = 'hero-bg.jpg', 
		quantidade_cartoes = 10, 
		texto_agendamento = 'Selecionar Prestador de Serviço', 
		msg_agendamento = 'Sim', 
		agendamento_dias = '30', 
		itens_pag = '10', 
		minutos_aviso = '0', 
		porc_servico = '0', 
		pgto_api = 'Sim', 
		api = 'menuia',
		nome_bot = 'Barberbot',
		barberbot_ativo = 'Não',
		barberbot_boas_vindas = '',
		openai_key = '',
		openai_prompt = ''
	");
}else{

	$nome_sistema = $res[0]['nome'];
	$email_sistema = $res[0]['email'];
	$whatsapp_sistema = $res[0]['telefone_whatsapp'];
	$tipo_rel = $res[0]['tipo_rel'];
	$telefone_fixo_sistema = $res[0]['telefone_fixo'];
	$endereco_sistema = $res[0]['endereco'];
	$logo_rel = $res[0]['logo_rel'];
	$logo_sistema = $res[0]['logo'];
	$icone_sistema = $res[0]['icone'];
	$instagram_sistema = $res[0]['instagram'];
	$tipo_comissao = $res[0]['tipo_comissao'];
	$texto_rodape = $res[0]['texto_rodape'];
	$img_banner_index = $res[0]['img_banner_index'];
	$icone_site = $res[0]['icone_site'];
	$texto_sobre = $res[0]['texto_sobre'];
	$imagem_sobre = $res[0]['imagem_sobre'];
	$mapa = $res[0]['mapa'];
	$quantidade_cartoes = $res[0]['quantidade_cartoes'];
	$texto_fidelidade = $res[0]['texto_fidelidade'];
	$texto_agendamento = $res[0]['texto_agendamento'];
	$msg_agendamento = $res[0]['msg_agendamento'];
	$cnpj_sistema = $res[0]['cnpj'];
	$cidade_sistema = $res[0]['cidade'];
	$agendamento_dias = $res[0]['agendamento_dias'];
	$itens_pag = $res[0]['itens_pag'];
	$minutos_aviso = $res[0]['minutos_aviso'];
	$antAgendamento = $res[0]['minutos_aviso'];
	$token = $res[0]['token'];
	$instancia = $res[0]['instancia'];
	$url_video = $res[0]['url_video'];
	$posicao_video = $res[0]['posicao_video'];
	$taxa_sistema = $res[0]['taxa_sistema'];
	$lanc_comissao = $res[0]['lanc_comissao'];
	$ativo_sistema = $res[0]['ativo'];
	$porc_servico = $res[0]['porc_servico'];
	$pgto_api = $res[0]['pgto_api'];
	$api = $res[0]['api'];
	$entrada = $res[0]['entrada'];
	$fundo_login = $res[0]['fundo_login'];
	$opcao_pagar = $res[0]['opcao_pagar'];
	$api_pagamento = $res[0]['api_pagamento'];
	$public_key_mp = $res[0]['public_key_mp'];
	$access_token_mp = $res[0]['access_token_mp'];
	$asaas = $res[0]['asaas'];

	// BarberBot / OpenAI (novos campos)
	$nome_bot = $res[0]['nome_bot'] ?? 'Barberbot';
	$barberbot_ativo = $res[0]['barberbot_ativo'] ?? 'Não';
	$barberbot_boas_vindas = $res[0]['barberbot_boas_vindas'] ?? '';
	$openai_key = $res[0]['openai_key'] ?? '';
	$openai_prompt = $res[0]['openai_prompt'] ?? '';

	if($fundo_login == ""){
		$fundo_login = 'sem-foto.png';
	}

	// Novas variaveis
    $emailMenuia = $res[0]['email_menuia'] ?? '';
    $planoMenuia = $res[0]['plano_menuia'] ?? '';
    $validadeMenuia = $res[0]['validade_menuia'] ?? '';
    $senhaMenuia = $res[0]['senha_menuia'] ?? '';

    $token_whatsapp = $res[0]['token'];
	$instancia_whatsapp = $res[0]['instancia'];
    
    // Fim das variaveis Menuia

	$horas_confirmacaoF = $minutos_aviso.':00:00';

	$tel_whatsapp = '55'.preg_replace('/[ ()-]+/' , '' , $whatsapp_sistema);
	
    if ($ativo_sistema != 'Sim' && $ativo_sistema != '') {
        
        if (strpos($_SERVER['PHP_SELF'], 'bloqueio.php') === false) {
            
            echo "<script>window.location.href='/sistema/bloqueio.php';</script>";
            exit;
        }
    }
}
?>
