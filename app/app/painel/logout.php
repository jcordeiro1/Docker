<?php 
@session_start();
unset($_SESSION['id'], $_SESSION['nome'], $_SESSION['nivel']);
$_SESSION['msg'] = "";
echo "<script>localStorage.setItem('id_usu', '')</script>";
echo '<script>window.location="../"</script>';

?>