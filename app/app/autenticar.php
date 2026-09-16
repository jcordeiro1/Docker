<?php
@session_set_cookie_params(['httponly' => true]);
@session_start();
@session_regenerate_id(true);
require_once("../sistema/conexao.php");

$id_usu = filter_var(@$_POST['id'], @FILTER_SANITIZE_STRING);
$pagina = filter_var(@$_POST['pagina'], @FILTER_SANITIZE_STRING);
if($id_usu != ""){

	$query = $pdo->prepare("SELECT * from usuarios where id = :id");
	$query->bindValue(":id", "$id_usu");
	$query->execute();
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$linhas = @count($res);
	if($linhas > 0){

	$_SESSION['nome'] = $res[0]['nome'];
	$_SESSION['id'] = $res[0]['id'];
	$_SESSION['nivel'] = $res[0]['nivel'];
	$_SESSION['aut_token_portalapp'] = 'portalapp2024';

	if($res[0]['atendimento'] != 'Sim'){
		$_SESSION['msg'] = 'Esse aplicativo são para os profissionais que possuem atendimento!'; 
		echo '<script>window.location="index.php"</script>';  
	}

	if($pagina == ""){
		echo '<script>window.location="painel"</script>';  
	}else{
		echo '<script>window.location="painel/'.$pagina.'"</script>';  
	}
	
	}else{
		echo "<script>localStorage.setItem('id_usu', '')</script>";
		echo '<script>window.location="index.php"</script>';
	}
}

$usuario = filter_var(@$_POST['usuario'], @FILTER_SANITIZE_STRING);
$senha = filter_var(@$_POST['senha'], @FILTER_SANITIZE_STRING);
$salvar = filter_var(@$_POST['salvar'], @FILTER_SANITIZE_STRING);



$query = $pdo->prepare("SELECT * from usuarios where email = :email order by id asc limit 1");
$query->bindValue(":email", "$usuario");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$linhas = @count($res);

if($linhas > 0){

		if(!password_verify($senha, $res[0]['senha_crip'])){
		$_SESSION['msg'] = 'Dados Incorretos!';
		echo '<script>window.location="index.php"</script>';    
		exit();
	}

	if($res[0]['ativo'] != 'Sim'){
		$_SESSION['msg'] = 'Seu Acesso foi desativado!'; 
		echo '<script>window.location="index.php"</script>';  
	}


	if($res[0]['atendimento'] != 'Sim'){
		echo '<script>alert("Esse aplicativo são para os profissionais que possuem atendimento!")</script>'; 
		echo '<script>window.location="index.php"</script>';  
	}

	$_SESSION['nome'] = $res[0]['nome'];
	$_SESSION['id'] = $res[0]['id'];
	$_SESSION['nivel'] = $res[0]['nivel'];
	$_SESSION['visualizar'] = $res[0]['visualizar'];
	$_SESSION['aut_token_portalapp'] = 'portalapp2024';
	$id = $res[0]['id'];

	if($salvar == 'Sim'){
		echo "<script>localStorage.setItem('email_usu', '$usuario')</script>";
		echo "<script>localStorage.setItem('senha_usu', '$senha')</script>";
		echo "<script>localStorage.setItem('id_usu', '$id')</script>";
	}else{
		echo "<script>localStorage.setItem('email_usu', '')</script>";
		echo "<script>localStorage.setItem('senha_usu', '')</script>";
		echo "<script>localStorage.setItem('id_usu', '')</script>";
	}

	

	echo '<script>window.location="painel"</script>';

}else{
	$_SESSION['msg'] = 'Dados Incorretos!';  
	echo '<script>window.location="index.php"</script>';  
}


 ?>


