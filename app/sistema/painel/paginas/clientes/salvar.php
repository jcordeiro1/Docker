<?php 
require_once("../../../conexao.php");
$tabela = 'clientes';

$id = $_POST['id'];
$nome = $_POST['nome'];
$telefone = $_POST['telefone'];
$data_nasc = $_POST['data_nasc'];
$endereco = $_POST['endereco'];
$cartoes = $_POST['cartao'];
$cpf = $_POST['cpf'];

$senha = '123';
$senha_crip = password_hash($senha, PASSWORD_DEFAULT);

//validar telefone
$query = $pdo->query("SELECT * from $tabela where telefone = '$telefone'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0 and $id != $res[0]['id']){
	echo 'Telefone já Cadastrado, escolha outro!!';
	exit();
}

if($id == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, telefone = :telefone, data_cad = curDate(), data_nasc = '$data_nasc', cartoes = '$cartoes', ultimo_servico = 0, endereco = :endereco, alertado = 'Não', cpf = :cpf, senha_crip = '$senha_crip'");
} else {
	$query = $pdo->prepare("UPDATE $tabela SET nome = :nome, telefone = :telefone, data_nasc = '$data_nasc', cartoes = '$cartoes', endereco = :endereco, cpf = :cpf WHERE id = '$id'");
}

$query->bindValue(":nome", "$nome");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":cpf", "$cpf");
$query->execute();

// AQUI VAMOS ENVIAR O WHATSAPP APÓS SALVAR
// Formatar telefone
$telefone = '55' . preg_replace('/[ ()-]+/', '', $telefone); // Remove (), espaço e -

// Montar mensagem
$mensagem = "👋 *Olá {$nome}*, seja bem-vindo(a) ao *{$nome_sistema}*!\n\n";
$mensagem .= "🔒 *Use seu whatsapp e senha de acesso:* 123\n\n";
$mensagem .= "🌐 *Clique abaixo para acessar seu painel:*\n";
$mensagem .= "{$url_sistema}sistema/acesso";

// Agora chama o arquivo de envio
require('../../../../ajax/api-texto.php');

echo 'Salvo com Sucesso';
?>
