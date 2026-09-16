<?php 
require_once('../conexao.php');

if($modo_teste == 'Sim'){
    echo 'Esse recurso não é possível alterar no modo de testes!';
    exit();
}

$api = $_POST['api'];
$nome = $_POST['nome_sistema'];
$email = $_POST['email_sistema'];
$whatsapp = $_POST['whatsapp_sistema'];
$fixo = $_POST['telefone_fixo_sistema'];
$endereco = $_POST['endereco_sistema'];
$tipo_rel = $_POST['tipo_rel'];
$instagram = $_POST['instagram_sistema'];
$tipo_comissao = $_POST['tipo_comissao'];
$texto_rodape = $_POST['texto_rodape'];
$texto_sobre = $_POST['texto_sobre'];
$mapa = $_POST['mapa'];
$quantidade_cartoes = $_POST['quantidade_cartoes'];
$texto_fidelidade = $_POST['texto_fidelidade'];
$texto_agendamento = $_POST['texto_agendamento'];
$msg_agendamento = $_POST['msg_agendamento'];
$cnpj_sistema = $_POST['cnpj_sistema'];
$cidade_sistema = $_POST['cidade_sistema'];
$agendamento_dias = $_POST['agendamento_dias'];
$itens_pag = $_POST['itens_pag'];
$token = $_POST['token'];
$minutos_aviso = $_POST['minutos_aviso'];
$instancia = $_POST['instancia'];
$url_video = $_POST['url_video'];
$posicao_video = $_POST['posicao_video'];
$taxa_sistema = $_POST['taxa_sistema'];
$lanc_comissao = $_POST['lanc_comissao'];
$porc_servico = $_POST['porc_servico'];
$pgto_api = $_POST['pgto_api'];
$entrada = $_POST['entrada'];
$opcao_pagar = $_POST['opcao_pagar'];

$barberbot_ativo = $_POST['barberbot_ativo'] ?? 'Não';
$barberbot_boas_vindas = $_POST['barberbot_boas_vindas'] ?? '';
$openai_key = $_POST['openai_key'] ?? '';
$openai_prompt = $_POST['openai_prompt'] ?? '';
$nome_bot = $_POST['nome_bot'] ?? '';

$api_pagamento = $_POST['api_pagamento'];
$public_key_mp = $_POST['public_key_mp'];
$access_token_mp = $_POST['access_token_mp'];
$asaas = $_POST['asaas'];

if($minutos_aviso == ""){
	$minutos_aviso = 0;
}

//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$caminho = '../img/logo.png';
$imagem_temp = @$_FILES['foto-logo']['tmp_name']; 
if(@$_FILES['foto-logo']['name'] != ""){
	$ext = pathinfo(@$_FILES['foto-logo']['name'], PATHINFO_EXTENSION);   
	if($ext == 'png'){ 
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão da imagem para a Logo é somente *PNG';
		exit();
	}
}

$caminho = '../img/favicon.png';
$imagem_temp = @$_FILES['foto-icone']['tmp_name']; 
if(@$_FILES['foto-icone']['name'] != ""){
	$ext = pathinfo(@$_FILES['foto-icone']['name'], PATHINFO_EXTENSION);   
	if($ext == 'png'){ 
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão da imagem para a ícone é somente *ICO';
		exit();
	}
}

$caminho = '../img/logo_rel.jpg';
$imagem_temp = @$_FILES['foto-logo-rel']['tmp_name']; 
if(@$_FILES['foto-logo-rel']['name'] != ""){
	$ext = pathinfo(@$_FILES['foto-logo-rel']['name'], PATHINFO_EXTENSION);   
	if($ext == 'jpg'){ 
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão da imagem para o Relatório é somente *Jpg';
		exit();
	}
}

$query = $pdo->query("SELECT * FROM config");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$img_banner_index = $res[0]['img_banner_index'];
$fundo_login = @$res[0]['fundo_login']; // garante não ficar undefined caso não envie nova imagem

//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = @$_FILES['foto-banner-index']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../images/' .$nome_img;

$imagem_temp = @$_FILES['foto-banner-index']['tmp_name']; 

if(@$_FILES['foto-banner-index']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif'){ 
				
	$img_banner_index = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}

//validar troca da foto
$query = $pdo->query("SELECT * FROM config");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$img_sobre = $res[0]['imagem_sobre'];

//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = @$_FILES['foto-sobre']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../images/' .$nome_img;

$imagem_temp = @$_FILES['foto-sobre']['tmp_name']; 

if(@$_FILES['foto-sobre']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif'){ 
				
	$img_sobre = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}

$caminho = '../../images/favicon.png';
$imagem_temp = @$_FILES['foto-icone-site']['tmp_name']; 
if(@$_FILES['foto-icone-site']['name'] != ""){
	$ext = pathinfo(@$_FILES['foto-icone-site']['name'], PATHINFO_EXTENSION);   
	if($ext == 'png'){ 
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão da imagem para a ícone é somente *PNG';
		exit();
	}
}

$caminho = '../../images/favicon_192.png';
$imagem_temp = @$_FILES['foto-favicon192']['tmp_name'];

if (!empty(@$_FILES['foto-favicon192']['name'])) {
    $ext = pathinfo($_FILES['foto-favicon192']['name'], PATHINFO_EXTENSION);

    if (strtolower($ext) == 'png') {
        // Carrega a imagem original
        $imagem = imagecreatefrompng($imagem_temp);

        if (!$imagem) {
            echo 'Erro ao ler a imagem PNG enviada.';
            exit();
        }

        // Cria uma nova imagem redimensionada
        $nova_largura = 192;
        $nova_altura = 192;

        $imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);

        // Mantém transparência
        imagealphablending($imagem_redimensionada, false);
        imagesavealpha($imagem_redimensionada, true);

        // Redimensiona
        imagecopyresampled(
            $imagem_redimensionada,
            $imagem,
            0, 0, 0, 0,
            $nova_largura, $nova_altura,
            imagesx($imagem),
            imagesy($imagem)
        );

        // Salva a imagem redimensionada
        imagepng($imagem_redimensionada, $caminho);

        // Libera memória
        imagedestroy($imagem);
        imagedestroy($imagem_redimensionada);

        //echo '✅ Ícone redimensionado e salvo com sucesso!';
    } else {
        //echo '❌ Extensão inválida! A imagem deve ser PNG.';
        //exit();
    }
}

$caminho = '../../images/favicon_512.png';
$imagem_temp = @$_FILES['foto-favicon512']['tmp_name'];

if (!empty(@$_FILES['foto-favicon512']['name'])) {
    $ext = pathinfo($_FILES['foto-favicon512']['name'], PATHINFO_EXTENSION);

    if (strtolower($ext) == 'png') {
        // Carrega a imagem original
        $imagem = imagecreatefrompng($imagem_temp);

        if (!$imagem) {
            echo 'Erro ao ler a imagem PNG enviada.';
            exit();
        }

        // Cria uma nova imagem redimensionada
        $nova_largura = 512;
        $nova_altura = 512;

        $imagem_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);

        // Mantém transparência
        imagealphablending($imagem_redimensionada, false);
        imagesavealpha($imagem_redimensionada, true);

        // Redimensiona
        imagecopyresampled(
            $imagem_redimensionada,
            $imagem,
            0, 0, 0, 0,
            $nova_largura, $nova_altura,
            imagesx($imagem),
            imagesy($imagem)
        );

        // Salva a imagem redimensionada
        imagepng($imagem_redimensionada, $caminho);

        // Libera memória
        imagedestroy($imagem);
        imagedestroy($imagem_redimensionada);

        //echo '✅ Ícone redimensionado e salvo com sucesso!';
    } else {
        //echo '❌ Extensão inválida! A imagem deve ser PNG.';
        exit();
    }
}

//foto fundo login
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['fundo_login']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);
$caminho = '../img/'.$nome_img;
$imagem_temp = @$_FILES['fundo_login']['tmp_name']; 

if(@$_FILES['fundo_login']['name'] != ""){
    $ext = pathinfo(@$_FILES['fundo_login']['name'], PATHINFO_EXTENSION);   
    if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'png' || $ext == 'PNG'|| $ext == 'gif' || $ext == 'GIF' || $ext == 'webp' || $ext == 'WEBP'){            
        move_uploaded_file($imagem_temp, $caminho);
        $fundo_login = $nome_img;

        $query = $pdo->query("SELECT * FROM config");
        $res = $query->fetchAll(PDO::FETCH_ASSOC);
        $fundo_login_antigo = @$res[0]['fundo_login'];

        if($fundo_login_antigo != "sem-foto.png"){
            @unlink('../img/'.$fundo_login_antigo);
        } 

    }else{
        echo 'Extensão de Imagem não permitida!';
        exit();
    }
}

$query = $pdo->prepare("UPDATE config SET nome = :nome, email = :email, api = :api, telefone_whatsapp = :whatsapp, telefone_fixo = :telefone_fixo, endereco = :endereco, logo = 'logo.png', icone = 'favicon.png', logo_rel = 'logo_rel.jpg', tipo_rel = '$tipo_rel', instagram = :instagram, tipo_comissao = '$tipo_comissao', texto_rodape = :texto_rodape, img_banner_index = '$img_banner_index', icone_site = 'favicon.png', imagem_sobre = '$img_sobre', texto_sobre = :texto_sobre, mapa = :mapa, quantidade_cartoes = '$quantidade_cartoes', texto_fidelidade = :texto_fidelidade, texto_agendamento = :texto_agendamento, msg_agendamento = :msg_agendamento, cnpj = :cnpj, cidade = :cidade, agendamento_dias = '$agendamento_dias', itens_pag = '$itens_pag', token = :token, minutos_aviso = '$minutos_aviso', instancia = :instancia, url_video = :url_video, posicao_video = :posicao_video, taxa_sistema = :taxa_sistema, lanc_comissao = :lanc_comissao, porc_servico = :porc_servico, pgto_api = :pgto_api, entrada = :entrada, opcao_pagar = :opcao_pagar, fundo_login = :fundo_login, api_pagamento = :api_pagamento, public_key_mp = :public_key_mp, access_token_mp = :access_token_mp, asaas = :asaas, nome_bot = :nome_bot,
barberbot_ativo = :barberbot_ativo, barberbot_boas_vindas = :barberbot_boas_vindas, openai_key = :openai_key, openai_prompt = :openai_prompt
");

$query->bindValue(":api", "$api");
$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->bindValue(":whatsapp", "$whatsapp");
$query->bindValue(":telefone_fixo", "$fixo");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":instagram", "$instagram");
$query->bindValue(":texto_rodape", "$texto_rodape");
$query->bindValue(":texto_sobre", "$texto_sobre");
$query->bindValue(":mapa", "$mapa");
$query->bindValue(":texto_fidelidade", "$texto_fidelidade");
$query->bindValue(":texto_agendamento", "$texto_agendamento");
$query->bindValue(":msg_agendamento", "$msg_agendamento");
$query->bindValue(":cnpj", "$cnpj_sistema");
$query->bindValue(":cidade", "$cidade_sistema");
$query->bindValue(":token", "$token");
$query->bindValue(":instancia", "$instancia");
$query->bindValue(":url_video", "$url_video");
$query->bindValue(":posicao_video", "$posicao_video");
$query->bindValue(":taxa_sistema", "$taxa_sistema");
$query->bindValue(":lanc_comissao", "$lanc_comissao");
$query->bindValue(":porc_servico", "$porc_servico");
$query->bindValue(":pgto_api", "$pgto_api");
$query->bindValue(":entrada", "$entrada");
$query->bindValue(":opcao_pagar", "$opcao_pagar");
$query->bindValue(":fundo_login", "$fundo_login");
$query->bindValue(":api_pagamento", "$api_pagamento");
$query->bindValue(":public_key_mp", "$public_key_mp");
$query->bindValue(":access_token_mp", "$access_token_mp");
$query->bindValue(":asaas", "$asaas");

$query->bindValue(":nome_bot", "$nome_bot");
$query->bindValue(":barberbot_ativo", "$barberbot_ativo");
$query->bindValue(":barberbot_boas_vindas", "$barberbot_boas_vindas");
$query->bindValue(":openai_key", "$openai_key");
$query->bindValue(":openai_prompt", "$openai_prompt");

$query->execute();

echo 'Editado com Sucesso';
?>
