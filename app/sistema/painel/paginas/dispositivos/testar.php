<?php
// Bridge para localizar e executar o /ajax/teste_whatsapp.php, sem mudar a lógica.
// Versão compatível com PHP < 7.4 (sem arrow functions e sem array unpacking).

@session_start();
header('Content-Type: text/html; charset=utf-8');

// ---------- Helpers ----------
function candidato($base, $rel) {
    if (!$base) return null;
    return rtrim($base, '/').'/'.ltrim($rel, '/');
}

function add_candidato(&$arr, $path) {
    if ($path && !in_array($path, $arr, true)) {
        $arr[] = $path;
    }
}
// -----------------------------

$docRoot   = rtrim(isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '', '/'); // raiz do domínio/subdomínio
$dirAtual  = __DIR__;                                                                       // .../sistema/painel/paginas/dispositivos
$publicHtml = $docRoot ? dirname($docRoot) . '/public_html' : null;

// Subir vários níveis a partir do diretório atual
$ups = array();
for ($n = 1; $n <= 8; $n++) {
    $ups[] = dirname($dirAtual, $n);
}

// Monte a lista de candidatos (ordem da mais provável para menos provável)
$candidatos = array();

// 1) Direto na raiz do domínio/subdomínio
add_candidato($candidatos, candidato($docRoot,    'ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($docRoot,    'sistema/ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($docRoot,    'sistema/painel/ajax/teste_whatsapp.php'));

// 2) Em public_html “acima” (cenário comum de cPanel)
add_candidato($candidatos, candidato($publicHtml, 'ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($publicHtml, 'sistema/ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($publicHtml, 'sistema/painel/ajax/teste_whatsapp.php'));

// 3) Relativos a partir do diretório atual
add_candidato($candidatos, candidato($dirAtual,   '../../ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($dirAtual,   '../../../ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($dirAtual,   '../../../../ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($dirAtual,   '../../sistema/ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($dirAtual,   '../../../sistema/ajax/teste_whatsapp.php'));
add_candidato($candidatos, candidato($dirAtual,   '../../../../sistema/ajax/teste_whatsapp.php'));

// 4) Varredura agressiva subindo níveis (ajax/ e sistema/ajax/)
foreach ($ups as $u) { add_candidato($candidatos, candidato($u, 'ajax/teste_whatsapp.php')); }
foreach ($ups as $u) { add_candidato($candidatos, candidato($u, 'sistema/ajax/teste_whatsapp.php')); }

// Procura o primeiro arquivo existente
$alvo = null;
foreach ($candidatos as $c) {
    if (is_file($c)) { $alvo = $c; break; }
}

if (!$alvo) {
    http_response_code(404);
    echo 'teste_whatsapp.php não encontrado nos caminhos padrão.';
    exit;
}

// Executa o script alvo preservando includes relativos dele
$cwd = getcwd();
chdir(dirname($alvo));

ob_start();
include $alvo; // mantém o retorno/HTML/JSON do script original
$saida = ob_get_clean();

chdir($cwd);

echo $saida;
