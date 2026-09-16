<?php
require_once("../../../conexao.php");
$tabela = 'servicos';

$id   = isset($_POST['id']) ? trim($_POST['id']) : '';
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';

$valor = isset($_POST['valor']) ? $_POST['valor'] : '';
$valor = str_replace('.', '', $valor);
$valor = str_replace(',', '.', $valor);

$comissao = isset($_POST['comissao']) ? $_POST['comissao'] : '';
$comissao = str_replace(',', '.', $comissao);
$comissao = str_replace('%', '', $comissao);

$tempo = isset($_POST['tempo']) ? $_POST['tempo'] : '';
$dias_retorno = isset($_POST['dias_retorno']) ? $_POST['dias_retorno'] : '';
$categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '0';

$combo_ativo = isset($_POST['combo_ativo']) ? $_POST['combo_ativo'] : 'Não';
$combo_qtd_sessoes = isset($_POST['combo_qtd_sessoes']) ? $_POST['combo_qtd_sessoes'] : 0;
$combo_validade_dias = isset($_POST['combo_validade_dias']) ? $_POST['combo_validade_dias'] : 0;

if($categoria == 0){
	echo 'Cadastre uma Categoria de Serviços para o Serviço';
	exit();
}

if($combo_ativo != 'Sim'){
	$combo_ativo = 'Não';
	$combo_qtd_sessoes = 0;
	$combo_validade_dias = 0;
}else{
	if($combo_qtd_sessoes == '' || $combo_qtd_sessoes <= 0){
		echo 'Informe a quantidade de sessões do combo!';
		exit();
	}

	if($combo_validade_dias == '' || $combo_validade_dias < 0){
		echo 'Informe a validade do combo!';
		exit();
	}
}

try{
	$st = $pdo->prepare("SELECT id FROM $tabela WHERE nome = :nome LIMIT 1");
	$st->bindValue(':nome', $nome);
	$st->execute();
	$achouId = $st->fetchColumn();

	if($achouId && $id != $achouId){
		echo 'Nome já Cadastrado, escolha outro!!';
		exit();
	}
}catch(Exception $e){
	echo 'Erro ao validar o nome!';
	exit();
}

$foto = 'sem-foto.jpg';
if($id !== ''){
	try{
		$st = $pdo->prepare("SELECT foto FROM $tabela WHERE id = :id LIMIT 1");
		$st->bindValue(':id', $id);
		$st->execute();
		$fotoAtual = $st->fetchColumn();

		if($fotoAtual){
			$foto = $fotoAtual;
		}
	}catch(Exception $e){
	}
}

if(isset($_FILES['foto']) && !empty($_FILES['foto']['name'])){
	$nome_img = date('d-m-Y H:i:s') . '-' . $_FILES['foto']['name'];
	$nome_img = preg_replace('/[ :]+/', '-', $nome_img);

	$ext = strtolower(pathinfo($nome_img, PATHINFO_EXTENSION));

	if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif'){

		if($foto != "sem-foto.jpg"){
			@unlink('../../img/servicos/'.$foto);
		}

		$foto = $nome_img;

		$caminho = '../../img/servicos/' . $nome_img;
		$imagem_temp = $_FILES['foto']['tmp_name'];

		if(!@move_uploaded_file($imagem_temp, $caminho)){
			echo 'Erro ao enviar a imagem!';
			exit();
		}

	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}

try{

	if($id == ""){
		$query = $pdo->prepare("
			INSERT INTO $tabela
			SET nome = :nome,
				categoria = :categoria,
				valor = :valor,
				dias_retorno = :dias_retorno,
				ativo = 'Sim',
				foto = :foto,
				comissao = :comissao,
				tempo = :tempo,
				combo_ativo = :combo_ativo,
				combo_qtd_sessoes = :combo_qtd_sessoes,
				combo_validade_dias = :combo_validade_dias
		");
	}else{
		$query = $pdo->prepare("
			UPDATE $tabela
			SET nome = :nome,
				categoria = :categoria,
				valor = :valor,
				dias_retorno = :dias_retorno,
				foto = :foto,
				comissao = :comissao,
				tempo = :tempo,
				combo_ativo = :combo_ativo,
				combo_qtd_sessoes = :combo_qtd_sessoes,
				combo_validade_dias = :combo_validade_dias
			WHERE id = :id
		");
		$query->bindValue(":id", $id);
	}

	$query->bindValue(":nome", $nome);
	$query->bindValue(":categoria", $categoria);
	$query->bindValue(":valor", $valor);
	$query->bindValue(":dias_retorno", $dias_retorno);
	$query->bindValue(":foto", $foto);
	$query->bindValue(":comissao", $comissao);
	$query->bindValue(":tempo", $tempo);
	$query->bindValue(":combo_ativo", $combo_ativo);
	$query->bindValue(":combo_qtd_sessoes", $combo_qtd_sessoes);
	$query->bindValue(":combo_validade_dias", $combo_validade_dias);
	$query->execute();

	echo 'Salvo com Sucesso';

}catch(Exception $e){
	echo 'Erro ao salvar!';
	exit();
}
?>