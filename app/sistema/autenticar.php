<?php 
@session_start();
require_once("conexao.php");


$email = filter_var($_POST['email'], @FILTER_SANITIZE_STRING);
$senha = filter_var($_POST['senha'], @FILTER_SANITIZE_STRING);

$query = $pdo->prepare("SELECT * from usuarios where (email = :email or cpf = :email)");
$query->bindValue(":email", "$email");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);

$total_reg = @count($res);
if($total_reg > 0){
	$ativo = $res[0]['ativo'];

	if(!password_verify($senha, $res[0]['senha_crip'])){
		echo '<script>window.alert("Dados Incorretos!!")</script>'; 
		echo '<script>window.location="index.php"</script>';  
		exit();
	}


	if($ativo == 'Sim'){

		$_SESSION['id'] = $res[0]['id'];
		$_SESSION['nivel'] = $res[0]['nivel'];
		$_SESSION['nivel_usuario'] = $res[0]['nivel']; // ← ESSENCIAL PARA EXIBIÇÃO DO BOTÃO
		$_SESSION['nome'] = $res[0]['nome'];
		$_SESSION['aut_token_1010'] = 'fdsfdsafda8855555';
	
		//ir para o painel
		echo "<script>window.location='painel'</script>";
	}else{
		echo "<script>window.alert('Seu usuário foi desativado, contate o administrador!')</script>";
	echo "<script>window.location='index.php'</script>";
	}
	
}else{
	echo "<script>window.alert('Usuário ou Senha Incorretos!')</script>";
	echo "<script>window.location='index.php'</script>";
}

 ?>