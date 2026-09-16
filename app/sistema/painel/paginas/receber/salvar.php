<?php 
require_once("../../../conexao.php");
$tabela = 'receber';
@session_start();
$id_usuario = $_SESSION['id'];

$id = $_POST['id'];
$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$valor = str_replace(',', '.', $valor);
$pessoa = $_POST['pessoa'];
$data_venc = $_POST['data_venc'];
$data_pgto = $_POST['data_pgto'];
$forma_pgto = @$_POST['pgto'];
$frequencia = @$_POST['frequencia'];
$servico = @$_POST['servico'];

if($descricao == ""){
	echo 'Insira uma descrição!';
	exit();
}


if($pessoa == ""){
	$pessoa = 0;
}


if($data_pgto != ''){
	$usuario_pgto = $id_usuario;
	$pago = 'Sim';
	$pgto = " ,data_pgto = '$data_pgto'";
}else{
	$usuario_pgto = 0;
	$pago = 'Não';
	$pgto = "";
}


//validar troca da foto
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$foto = $res[0]['foto'];
}else{
	$foto = 'sem-foto.jpg';
}




//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/contas/' .$nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name']; 

if(@$_FILES['foto']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'pdf' or $ext == 'rar' or $ext == 'zip'){ 
	
			//EXCLUO A FOTO ANTERIOR
			if($foto != "sem-foto.jpg"){
				@unlink('../../img/contas/'.$foto);
			}

			$foto = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}


//verificar caixa aberto
$query1 = $pdo->query("SELECT * from caixas where operador = '$id_usuario' and data_fechamento is null order by id desc limit 1");
$res1 = $query1->fetchAll(PDO::FETCH_ASSOC);
if(@count($res1) > 0){
	$id_caixa = @$res1[0]['id'];
}else{
	$id_caixa = 0;
}


if($id == ""){
	$query = $pdo->prepare("INSERT INTO $tabela SET descricao = :descricao, tipo = 'Conta', valor = :valor, data_lanc = curDate(), data_venc = '$data_venc', usuario_lanc = '$id_usuario', usuario_baixa = '$usuario_pgto', foto = '$foto', pessoa = '$pessoa', pago = '$pago' $pgto, caixa = '$id_caixa', hora = curTime(), pgto = '$forma_pgto', hora_alerta = '$hora_random', servico = '$servico', frequencia = '$frequencia'");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET descricao = :descricao, valor = :valor, data_venc = '$data_venc', data_pgto = '$data_pgto', foto = '$foto', pessoa = '$pessoa', caixa = '$id_caixa', hora = curTime(), pgto = '$forma_pgto', servico = '$servico', frequencia = '$frequencia' WHERE id = '$id'");
}

$query->bindValue(":descricao", "$descricao");
$query->bindValue(":valor", "$valor");
$query->execute();




$dias_frequencia = @$_POST['frequencia'];
if($frequencia > 0 and $pago == 'Sim'){
		if($dias_frequencia == 30 || $dias_frequencia == 31){			
			$novo_vencimento = date('Y-m-d', @strtotime("+1 month",@strtotime($data_venc)));
		}else if($dias_frequencia == 90){			
			$novo_vencimento = date('Y-m-d', @strtotime("+3 month",@strtotime($data_venc)));
		}else if($dias_frequencia == 180){ 
			$novo_vencimento = date('Y-m-d', @strtotime("6 month",@strtotime($data_venc)));
		}else if($dias_frequencia == 360 || $dias_frequencia == 365){ 			
			$novo_vencimento = date('Y-m-d', @strtotime("+12 month",@strtotime($data_venc)));

		}else{			
			$novo_vencimento = date('Y-m-d', @strtotime("+$dias_frequencia days",@strtotime($data_venc)));
		}

		$novo_vencimentoF = implode('/', array_reverse(@explode('-', $novo_vencimento)));


		$pdo->query("INSERT INTO receber SET descricao = '$descricao', tipo = 'Conta', valor = '$valor', data_lanc = curDate(), data_venc = '$novo_vencimento', usuario_lanc = '$id_usuario', foto = 'sem-foto.jpg', pessoa = '$pessoa', pago = 'Não', hora = curTime(), frequencia = '$frequencia', hora_alerta = '$hora_random'");
		
		$ultima_conta = $pdo->lastInsertId();

}

echo 'Salvo com Sucesso';
 ?>