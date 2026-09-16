<?php 
@session_start();
if(@$_SESSION['id'] == ""){
	echo "<script>window.location='../acesso.php'</script>";
	exit();
}

if(@$_SESSION['aut_token_505052022'] != "fdsfdsafda885574125"){
	echo "<script>window.location='../acesso.php'</script>";
	exit();
}
 ?>