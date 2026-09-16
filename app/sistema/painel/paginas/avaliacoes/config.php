<?php
@session_start();
require_once("../../../conexao.php");

/*
 Tabela de configs simples:
 CREATE TABLE IF NOT EXISTS config_site (
   id INT AUTO_INCREMENT PRIMARY KEY,
   chave VARCHAR(60) UNIQUE,
   valor TEXT
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

$acao = $_POST['acao'] ?? '';

function getVal($pdo,$k){
  $st=$pdo->prepare("SELECT valor FROM config_site WHERE chave=:k"); $st->execute([':k'=>$k]);
  $r=$st->fetch(PDO::FETCH_ASSOC); return $r? $r['valor'] : '';
}
function setVal($pdo,$k,$v){
  $st=$pdo->prepare("INSERT INTO config_site (chave,valor) VALUES (:k,:v)
                     ON DUPLICATE KEY UPDATE valor=VALUES(valor)");
  $st->execute([':k'=>$k, ':v'=>$v]);
}

if($acao==='get'){
  $out = [
    'google_link'=> getVal($pdo,'google_review_link'),
    'place_id'   => getVal($pdo,'google_place_id'),
    'api_key'    => getVal($pdo,'google_api_key'),
  ];
  echo json_encode($out); exit;
}

if($acao==='set'){
  $link = trim($_POST['google_link'] ?? '');
  $pid  = trim($_POST['place_id'] ?? '');
  $key  = trim($_POST['api_key'] ?? '');
  try{
    setVal($pdo,'google_review_link',$link);
    setVal($pdo,'google_place_id',$pid);
    setVal($pdo,'google_api_key',$key);
    echo 'OK';
  }catch(Exception $e){ echo 'Erro: '.$e->getMessage(); }
  exit;
}

echo 'Ação inválida';
