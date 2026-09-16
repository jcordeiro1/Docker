<?php 
@session_start();
if(@$_SESSION['id'] == ""){
	echo "<script>window.location='../index.php'</script>";
	exit();
}

if(@$_SESSION['aut_token_1010'] != "fdsfdsafda8855555"){
	echo "<script>window.location='../index.php'</script>";
	exit();
}
 ?>