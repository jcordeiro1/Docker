<?php 
require_once("../sistema/conexao.php");
$tabela = 'clientes';

$nome = filter_var($_POST['nome'], FILTER_SANITIZE_STRING);
$telefone = filter_var($_POST['telefone'], FILTER_SANITIZE_STRING);

// Validar telefone
$query = $pdo->prepare("SELECT * from $tabela where telefone = :telefone");
$query->bindValue(":telefone", "$telefone");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$senha = '123';
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);
if(@count($res) > 0){
    echo 'Telefone já Cadastrado, você já está cadastrado!!';
    exit();
}

$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, telefone = :telefone, data_cad = curDate(), cartoes = '0', alertado = 'Não', senha_crip = :senha_crip");
$query->bindValue(":nome", "$nome");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":senha_crip", "$senha_crip");
$query->execute();


// --------- ENVIO WHATSAPP -----------

// Formatar telefone para padrão internacional (sem máscara)
$telefone_formatado = '55' . preg_replace('/[ ()-]+/', '', $telefone); 

// Montar mensagem
$mensagem = "👋 *Olá {$nome}*, seja bem-vindo(a) ao *{$nome_sistema}*!\n\n";
$mensagem .= "🔒 *Use seu whatsapp e senha de acesso:* 123\n\n";
$mensagem .= "🌐 *Clique abaixo para acessar seu painel:*\n";
$mensagem .= "{$url_sistema}sistema/acesso";

// Enviar WhatsApp (ajuste o caminho se necessário)
require('ajax/api-texto.php'); // Caminho relativo ao arquivo deste cadastro

echo 'Salvo com Sucesso';
?>
