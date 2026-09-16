<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function _strip_www($h){ return strtolower(preg_replace('/^www\./i','',trim(preg_replace('/:\d+$/','',$h)))); }
function _env_load($f){ $o=[]; if(!is_readable($f))return $o; foreach(file($f, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) as $ln){
  $ln=trim($ln); if($ln===''||$ln[0]==='#')continue; [$k,$v]=array_pad(explode('=',$ln,2),2,''); $o[$k]=trim($v); } return $o; }
$env = _env_load(dirname(__DIR__).'/.env');
$dom = _strip_www($env['LICENCA_DOMINIO'] ?? '');
$exp = $env['LICENCA_EXPIRA'] ?? '';
$grc = (int)($env['LIC_GRACE_DAYS'] ?? 0);
$host= _strip_www($_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? '');
$hoje = new DateTimeImmutable('today');
$expDt = ($exp ? DateTimeImmutable::createFromFormat('Y-m-d',$exp) : null);
$dias = $expDt ? (int)$hoje->diff($expDt)->format('%r%a') : null;
header('Content-Type: text/plain; charset=UTF-8');
echo "Host atual........: $host\n";
echo "Domínio licenciado: $dom\n";
echo "Expira em.........: ".($exp?:'vitalícia')."\n";
echo "Carência (dias)...: $grc\n";
echo "Diferença (dias)..: ".($dias===null?'—':$dias)."\n";
$ok_dom  = ($dom!=='' && $host === $dom);
$ok_data = ($dias===null) ? true : ($dias > -$grc);
echo "Válido agora?.....: ".(($ok_dom && $ok_data)?'SIM':'NÃO')."\n";
