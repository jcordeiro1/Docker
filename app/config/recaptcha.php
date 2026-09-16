<?php
// /config/recaptcha.php

// ✅ CHAVES DO RECAPTCHA v3 (site e secret)
define('RECAPTCHA_SITE_KEY', getenv('RECAPTCHA_SITE_KEY') ?: '');
define('RECAPTCHA_SECRET_KEY', getenv('RECAPTCHA_SECRET_KEY') ?: '');

/**
 * Torna a validação OBRIGATÓRIA (true) ou “soft” (false).
 * Se false, sua aplicação pode optar por não bloquear quando a verificação falhar
 * (ex.: registrar lead mesmo assim e apenas logar a falha).
 */
define('RECAPTCHA_REQUIRED', false);

/** Score mínimo aceito (0.0–1.0). */
define('RECAPTCHA_MIN_SCORE', 0.5);

/**
 * Domínios aceitos. Você pode listar BASE DOMAINS e a checagem aceita
 * o próprio domínio e QUALQUER subdomínio.
 * Ex.: "jacycabeleireiro.com" aceitará "jacycabeleireiro.com" e "agenda.jacycabeleireiro.com".
 */
$RECAPTCHA_ALLOWED_BASE_DOMAINS = [
  'barberbot.com.br',
  'localhost',
];

/** Normaliza hostname/domínios para comparação. */
function rc_norm(string $h): string {
  $h = strtolower(trim($h));
  // remove “https://”, “http://”, barra final e “www.” inicial
  $h = preg_replace('#^https?://#', '', $h);
  $h = rtrim($h, '/');
  if (strpos($h, 'www.') === 0) $h = substr($h, 4);
  return $h;
}

/** Verifica se $host pertence a algum dos domínios base permitidos. */
function rc_host_permitido(string $host, array $baseDomains): bool {
  $host = rc_norm($host);
  if ($host === '') return false;

  foreach ($baseDomains as $base) {
    $base = rc_norm($base);
    if ($host === $base) return true;                   // domínio raiz
    if (substr($host, -strlen('.'.$base)) === '.'.$base) return true; // subdomínio
  }
  return false;
}

/**
 * Verifica o token do reCAPTCHA v3.
 * @param string $token   Token do front-end
 * @param string $action  Ação declarada no front (ex.: 'cadastro'). Se vazio, não é exigida.
 * @return array          ['ok'=>bool, 'score'=>float, 'reason'=>string, 'hostname'=>string, 'action'=>string]
 */
function recaptcha_verify(string $token, string $action = ''): array {
  global $RECAPTCHA_ALLOWED_BASE_DOMAINS;

  $token = trim($token);
  if ($token === '') {
    return ['ok'=>false, 'score'=>0.0, 'reason'=>'token_vazio', 'hostname'=>'', 'action'=>''];
  }

  $post = http_build_query([
    'secret'   => RECAPTCHA_SECRET_KEY,
    'response' => $token,
    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? null,
  ]);

  $resp = null;

  // 1) cURL
  if (function_exists('curl_init')) {
    $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
    curl_setopt_array($ch, [
      CURLOPT_POST           => true,
      CURLOPT_POSTFIELDS     => $post,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT        => 8,
    ]);
    $resp = curl_exec($ch);
    curl_close($ch);
  }

  // 2) Fallback: file_get_contents
  if ($resp === false || $resp === null) {
    $opts = ['http'=>[
      'method'  => 'POST',
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'content' => $post,
      'timeout' => 8,
    ]];
    $ctx  = stream_context_create($opts);
    $resp = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $ctx);
  }

  if (!$resp) {
    return ['ok'=>false, 'score'=>0.0, 'reason'=>'sem_resposta_api', 'hostname'=>'', 'action'=>''];
  }

  $json = json_decode($resp, true);
  if (!is_array($json) || empty($json['success'])) {
    return ['ok'=>false, 'score'=>0.0, 'reason'=>'falha_api', 'hostname'=>'', 'action'=>''];
  }

  $hostname  = (string)($json['hostname'] ?? '');
  $apiAction = (string)($json['action']   ?? '');
  $score     = (float)  ($json['score']    ?? 0);

  // Hostname permitido (raiz ou subdomínios)
  if (!rc_host_permitido($hostname, $RECAPTCHA_ALLOWED_BASE_DOMAINS)) {
    return ['ok'=>false, 'score'=>$score, 'reason'=>'hostname_nao_permitido', 'hostname'=>$hostname, 'action'=>$apiAction];
  }

  // Action: só exige se você informar uma
  if ($action !== '' && $apiAction !== $action) {
    return ['ok'=>false, 'score'=>$score, 'reason'=>'action_diferente', 'hostname'=>$hostname, 'action'=>$apiAction];
  }

  // Score mínimo
  if ($score < RECAPTCHA_MIN_SCORE) {
    return ['ok'=>false, 'score'=>$score, 'reason'=>'score_baixo', 'hostname'=>$hostname, 'action'=>$apiAction];
  }

  return ['ok'=>true, 'score'=>$score, 'reason'=>'ok', 'hostname'=>$hostname, 'action'=>$apiAction];
}
