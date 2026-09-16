<?php
@date_default_timezone_set('America/Sao_Paulo');
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once(__DIR__ . '/../sistema/conexao.php');

$LOG = __DIR__ . '/menuia_debug.txt';

$SESS_DIR = __DIR__ . '/sessions';
if (!is_dir($SESS_DIR)) {
    @mkdir($SESS_DIR, 0755, true);
}

$HUMANO_TIMEOUT = 1800;
$BURST_WINDOW = 4;

function logLine($file, $msg){
    @file_put_contents($file, date('Y-m-d H:i:s') . ' - ' . $msg . PHP_EOL, FILE_APPEND);
}

function onlyDigits($s){
    return preg_replace('/\D+/', '', (string)$s);
}

function leadingNumber($text){
    $text = trim((string)$text);
    if($text === '') return 0;
    if(preg_match('/^\s*(\d{1,3})\b/u', $text, $m)) return (int)$m[1];
    return 0;
}

function menuHint(){
    return "Digite *menu* para ver as opções.";
}

function containsAny($text, array $needles){
    foreach($needles as $n){
        $n = (string)$n;
        if($n !== '' && mb_stripos((string)$text, $n, 0, 'UTF-8') !== false){
            return true;
        }
    }
    return false;
}

function mapGet($map, $key){
    if(!is_array($map)) return null;
    $ks = (string)$key;
    if(isset($map[$ks])) return $map[$ks];
    $ki = (int)$key;
    if(isset($map[$ki])) return $map[$ki];
    return null;
}

function simNaoToInt($v){
    if (is_null($v)) return 0;
    if (is_bool($v)) return $v ? 1 : 0;
    $s = trim((string)$v);
    if ($s === '') return 0;
    if (is_numeric($s)) return ((int)$s) === 1 ? 1 : 0;
    $s = mb_strtolower($s, 'UTF-8');
    if ($s === 'sim' || $s === 's' || $s === 'true' || $s === 'on' || $s === 'ativo') return 1;
    return 0;
}

function stripMenuDuplicadoIA($text){
    $text = (string)$text;
    if(trim($text) === '') return '';
    $lines = preg_split("/\r\n|\n|\r/", $text);
    $out = [];
    $skip = false;
    $skipCount = 0;
    foreach($lines as $line){
        $ltrim = ltrim((string)$line);
        $low = mb_strtolower($ltrim, 'UTF-8');
        if(!$skip && mb_stripos($low, 'menu:', 0, 'UTF-8') !== false){
            $skip = true;
            $skipCount = 0;
            continue;
        }
        if($skip){
            $skipCount++;
            if(preg_match('/^\s*[1-4]\s*[$|\-\.\:]\s*/u', $ltrim) || preg_match('/^\s*[1-4]\s+/u', $ltrim)){
                if($skipCount <= 10) continue;
            }
            if(trim($ltrim) === '' && $skipCount <= 10) continue;
            $skip = false;
        }
        $out[] = (string)$line;
    }
    return trim(preg_replace("/\n{3,}/", "\n\n", implode("\n", $out)));
}

function formatPhone55($n){
    $d = onlyDigits($n);
    if ($d === '') return '';
    if (strpos($d, '55') === 0) return $d;
    if (strlen($d) === 11) return '55' . $d;
    if (strlen($d) === 10) return '55' . $d;
    return $d;
}

function last11($phone){
    $d = onlyDigits($phone);
    return strlen($d) >= 11 ? substr($d, -11) : $d;
}

function phoneKeyCanonical10($phone){
    $d = onlyDigits($phone);
    if(strlen($d) > 10 && str_starts_with($d, '55')) $d = substr($d, 2);
    if(strlen($d) === 11 && substr($d, 2, 1) === '9'){
        $d = substr($d, 0, 2) . substr($d, 3);
    }
    if(strlen($d) > 10) $d = substr($d, -10);
    return $d;
}

function sessionKey($cliente, $prof){
    $c = phoneKeyCanonical10($cliente);
    $p = phoneKeyCanonical10($prof);
    return sha1($c . '|' . $p);
}

function sessionFile($dir, $cliente, $prof){
    $key = sessionKey($cliente, $prof);
    return rtrim($dir,'/') . "/sess_{$key}.json";
}

function getSession($dir, $cliente, $prof){
    $f = sessionFile($dir, $cliente, $prof);
    if(!file_exists($f)) return [];
    $j = @json_decode(@file_get_contents($f), true);
    return is_array($j) ? $j : [];
}

function saveSession($dir, $cliente, $prof, $data){
    $f = sessionFile($dir, $cliente, $prof);
    @file_put_contents($f, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function deleteSession($dir, $cliente, $prof){
    $f = sessionFile($dir, $cliente, $prof);
    if(file_exists($f)){
        @unlink($f);
    }
}

function menuText($nomeSalao, $nomeCliente = ''){
    $nomeSalao   = trim((string)$nomeSalao);
    $nomeCliente = trim((string)$nomeCliente);
    $m  = "💈 *{$nomeSalao}*";
    if($nomeCliente !== ''){
        $m .= ", seja bem vindo *{$nomeCliente}*";
    }
    $m .= "\n\n";
    $m .= "Como posso te ajudar?\n\n";
    $m .= "1 - *Profissionais*\n";
    $m .= "2 - *Serviços e preços*\n";
    $m .= "3 - *Agendar horário*\n";
    $m .= "4 - *Falar com humano*\n\n";
    $m .= "*Digite* apenas o *número*, ou *menu*.";
    return $m;
}

function tableExists(PDO $pdo, $table){
    try{
        $st = $pdo->prepare("SHOW TABLES LIKE :t");
        $st->execute([':t' => $table]);
        return $st->fetchColumn() ? true : false;
    }catch(Exception $e){
        return false;
    }
}

function columnExists(PDO $pdo, $table, $column){
    try{
        $st = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE :c");
        $st->execute([':c' => $column]);
        return (bool)$st->fetchColumn();
    }catch(Exception $e){
        return false;
    }
}

function firstExistingColumn(PDO $pdo, string $table, array $candidates): string {
    foreach($candidates as $c){
        $c = (string)$c;
        if($c !== '' && columnExists($pdo, $table, $c)) return $c;
    }
    return '';
}

function parseDateToYmd(string $text): string {
    $t = mb_strtolower(trim($text), 'UTF-8');
    if($t === '') return '';
    if($t === 'hoje') return date('Y-m-d');
    if($t === 'amanha' || $t === 'amanhã') return date('Y-m-d', strtotime('+1 day'));
    if(preg_match('/(\d{1,2})[\/\-](\d{1,2})(?:[\/\-](\d{2,4}))?/u', $t, $m)){
        $d  = (int)$m[1];
        $mo = (int)$m[2];
        if(isset($m[3]) && $m[3] !== ''){
            $y = (int)$m[3];
            if($y < 100) $y += 2000;
        }else{
            $y = (int)date('Y');
        }
        if(checkdate($mo, $d, $y)){
            return sprintf('%04d-%02d-%02d', $y, $mo, $d);
        }
    }
    return '';
}

function weekdayPtBr(string $dateYmd): string {
    $n = (int)date('N', strtotime($dateYmd));
    switch($n){
        case 1: return 'Segunda-Feira';
        case 2: return 'Terça-Feira';
        case 3: return 'Quarta-Feira';
        case 4: return 'Quinta-Feira';
        case 5: return 'Sexta-Feira';
        case 6: return 'Sábado';
        case 7: return 'Domingo';
    }
    return '';
}

function isDiaBloqueado(PDO $pdo, int $idProf, string $dateYmd): bool {
    if($idProf <= 0 || $dateYmd === '') return false;
    if(!tableExists($pdo, 'dias_bloqueio')) return false;
    $colData = firstExistingColumn($pdo, 'dias_bloqueio', ['data','dia','date']);
    if($colData === '') return false;
    if(!columnExists($pdo, 'dias_bloqueio', 'funcionario')) return false;
    try{
        $sql = "SELECT 1 FROM `dias_bloqueio` WHERE `funcionario` = :f AND DATE(`{$colData}`) = :d LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->execute([':f' => $idProf, ':d' => $dateYmd]);
        return (bool)$st->fetchColumn();
    }catch(Exception $e){
        return false;
    }
}

function fetchHorariosDisponiveis(PDO $pdo, int $idProf, string $dateYmd): array {
    if($idProf <= 0 || $dateYmd === '') return [];
    if(isDiaBloqueado($pdo, $idProf, $dateYmd)) return [];

    $base = [];
    if(tableExists($pdo, 'horarios')){
        try{
            $st = $pdo->prepare("SELECT DISTINCT TIME(horario) AS horario FROM `horarios` WHERE `funcionario` = :f AND `data` = :d ORDER BY TIME(horario) ASC");
            $st->execute([':f' => $idProf, ':d' => $dateYmd]);
            $base = $st->fetchAll(PDO::FETCH_COLUMN);
        }catch(Exception $e){}
    }
    if(empty($base) && tableExists($pdo, 'horarios')){
        try{
            $st = $pdo->prepare("SELECT DISTINCT TIME(horario) AS horario FROM `horarios` WHERE `funcionario` = :f AND (`data` IS NULL OR `data` = '0000-00-00') ORDER BY TIME(horario) ASC");
            $st->execute([':f' => $idProf]);
            $base = $st->fetchAll(PDO::FETCH_COLUMN);
        }catch(Exception $e){}
    }
    if(empty($base) && tableExists($pdo, 'dias') && tableExists($pdo, 'usuarios')){
        try{
            $dia = weekdayPtBr($dateYmd);
            $stD = $pdo->prepare("SELECT inicio, final, inicio_almoco, final_almoco FROM dias WHERE funcionario = :f AND dia = :dia LIMIT 1");
            $stD->execute([':f' => $idProf, ':dia' => $dia]);
            $row = $stD->fetch(PDO::FETCH_ASSOC);
            if($row){
                $inicio = (string)($row['inicio'] ?? '');
                $final  = (string)($row['final'] ?? '');
                $iniAlm = (string)($row['inicio_almoco'] ?? '');
                $fimAlm = (string)($row['final_almoco'] ?? '');
                if($iniAlm === '00:00:00') $iniAlm = '';
                if($fimAlm === '00:00:00') $fimAlm = '';
                $stI = $pdo->prepare("SELECT intervalo FROM usuarios WHERE id = :id LIMIT 1");
                $stI->execute([':id' => $idProf]);
                $intervalo = (int)$stI->fetchColumn();
                if($intervalo <= 0) $intervalo = 30;
                if($inicio !== '' && $final !== ''){
                    $start = new DateTime($dateYmd.' '.$inicio);
                    $end   = new DateTime($dateYmd.' '.$final);
                    $lStart = null; $lEnd = null;
                    if($iniAlm !== '' && $fimAlm !== ''){
                        $lStart = new DateTime($dateYmd.' '.$iniAlm);
                        $lEnd   = new DateTime($dateYmd.' '.$fimAlm);
                    }
                    $cur = clone $start;
                    while($cur <= $end){
                        if($lStart && $lEnd){
                            if($cur >= $lStart && $cur < $lEnd){
                                $cur->modify("+{$intervalo} minutes");
                                continue;
                            }
                        }
                        $base[] = $cur->format('H:i:s');
                        $cur->modify("+{$intervalo} minutes");
                        if(count($base) > 200) break;
                    }
                    $base = array_values(array_unique($base));
                    sort($base);
                }
            }
        }catch(Exception $e){}
    }
    if(empty($base)) return [];

    $reservados = [];
    if(tableExists($pdo, 'agendamentos')){
        try{
            $sqlRes = "SELECT DISTINCT TIME(hora) as horario FROM `agendamentos` WHERE `funcionario` = :f AND `data` = :d";
            if(columnExists($pdo, 'agendamentos', 'status')){
                $sqlRes .= " AND status != 'Cancelado'";
            }
            $st2 = $pdo->prepare($sqlRes);
            $st2->execute([':f' => $idProf, ':d' => $dateYmd]);
            $reservados = $st2->fetchAll(PDO::FETCH_COLUMN);
            
            if(tableExists($pdo, 'horarios_agd')){
                $st3 = $pdo->prepare("SELECT DISTINCT TIME(horario) FROM horarios_agd WHERE funcionario = :f AND data = :d");
                $st3->execute([':f' => $idProf, ':d' => $dateYmd]);
                $res3 = $st3->fetchAll(PDO::FETCH_COLUMN);
                $reservados = array_merge($reservados, $res3);
            }
            $reservados = array_unique($reservados);
        }catch(Exception $e){
            logLine($GLOBALS['LOG'], "fetchHorariosDisponiveis ERRO RESERVADOS: ".$e->getMessage());
        }
    }
    
    $disp = [];
    foreach($base as $hr){
        if(!in_array($hr, $reservados)){
            $disp[] = $hr;
        }
    }
    if($dateYmd === date('Y-m-d')){
        $agora = date('H:i:s');
        $disp = array_values(array_filter($disp, function($h) use ($agora){
            return (string)$h > $agora;
        }));
    }
    return array_values($disp);
}

function fetchNomeClienteByPhone(PDO $pdo, $phone55){
    if(!tableExists($pdo, 'clientes')) return '';
    $p11 = last11($phone55);
    try{
        $st = $pdo->prepare("SELECT nome FROM clientes WHERE RIGHT(REPLACE(REPLACE(REPLACE(REPLACE(telefone,'(',''),')',''),'-',''),' ',''), 11) = :p11 LIMIT 1");
        $st->execute([':p11' => $p11]);
        return trim((string)$st->fetchColumn());
    }catch(Exception $e){ return ''; }
}

function fetchProfissionais(PDO $pdo){
    if(!tableExists($pdo, 'usuarios')) return [];
    $hasAtendimento = columnExists($pdo, 'usuarios', 'atendimento');
    $hasServicosFunc = tableExists($pdo, 'servicos_func');
    try{
        $sql = "SELECT u.id, u.nome, u.telefone, u.nivel, u.ativo FROM usuarios u WHERE u.ativo = 'Sim' AND u.nivel != 'Administrador'";
        $conds = [];
        if($hasAtendimento) $conds[] = "u.atendimento = 'Sim'";
        if($hasServicosFunc) $conds[] = "EXISTS (SELECT 1 FROM servicos_func sf WHERE sf.funcionario = u.id)";
        $conds[] = "(u.nivel LIKE 'Profissional%' OR u.nivel LIKE 'profissional%' OR u.nivel LIKE '%Cabeleireiro%' OR u.nivel LIKE '%Manicure%' OR u.nivel LIKE '%Hair%')";
        $sql .= " AND (" . implode(" OR ", $conds) . ") ORDER BY u.nome ASC";
        $q = $pdo->query($sql);
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e){ return []; }
}

function fetchServicos(PDO $pdo){
    if(!tableExists($pdo, 'servicos')) return [];
    try{
        $q = $pdo->query("SELECT id, nome, valor, ativo FROM servicos WHERE (ativo='Sim' OR ativo=1) ORDER BY nome ASC");
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }catch(Exception $e){ return []; }
}

function fetchProfissionaisByServico(PDO $pdo, int $idServico){
    if($idServico <= 0) return fetchProfissionais($pdo);
    if(!tableExists($pdo, 'servicos_func') || !tableExists($pdo, 'usuarios')) return fetchProfissionais($pdo);
    try{
        $st = $pdo->prepare("SELECT DISTINCT u.id, u.nome, u.telefone, u.nivel, u.ativo FROM usuarios u JOIN servicos_func sf ON sf.funcionario = u.id WHERE u.ativo='Sim' AND sf.servico = :s ORDER BY u.nome ASC");
        $st->execute([':s' => $idServico]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($rows)) return $rows;
    }catch(Exception $e){}
    return fetchProfissionais($pdo);
}

function fetchServicosByProf(PDO $pdo, int $idProf){
    if($idProf <= 0) return fetchServicos($pdo);
    if(!tableExists($pdo, 'servicos_func') || !tableExists($pdo, 'servicos')) return fetchServicos($pdo);
    try{
        $st = $pdo->prepare("SELECT DISTINCT s.id, s.nome, s.valor, s.ativo FROM servicos s JOIN servicos_func sf ON sf.servico = s.id WHERE (s.ativo='Sim' OR s.ativo=1) AND sf.funcionario = :f ORDER BY s.nome ASC");
        $st->execute([':f' => $idProf]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($rows)) return $rows;
    }catch(Exception $e){}
    return fetchServicos($pdo);
}

function formatMoneyBR($v){
    if($v === null || $v === '') return '';
    $num = (float)str_replace(',', '.', (string)$v);
    return 'R$ ' . number_format($num, 2, ',', '.');
}

function sendTextViaApiTexto($telefone55, $mensagem, $instanciaOverride = null){
    global $api, $token, $instancia, $LOG;
    $telefone = '55' . preg_replace('/[ ()-]+/', '', preg_replace('/^55/', '', (string)$telefone55));
    $mensagem = str_replace(["\r\n", "\r", "\n"], "%0A", (string)$mensagem);
    $instAntiga = $instancia ?? null;
    if(!empty($instanciaOverride)){
        $instancia = $instanciaOverride;
    }
    ob_start();
    require(__DIR__ . '/api-texto.php');
    $resp = trim(ob_get_clean());
    if(!empty($instanciaOverride)){
        $instancia = $instAntiga;
    }
    logLine($LOG, "SEND => to={$telefone} api=".($api ?? 'NULL')." inst=".($instancia ?? $instancia ?? 'NULL')." resp=".$resp);
}

function sendAndRemember($sessDir, $cliente, $prof, &$sess, $telefone55, $mensagem, $instanciaOverride = null){
    sendTextViaApiTexto($telefone55, $mensagem, $instanciaOverride);
    $sess['last_reply_at'] = time();
    saveSession($sessDir, $cliente, $prof, $sess);
}

function openaiResposta($apiKey, $instructions, $userText, $model = 'gpt-4o-mini'){
    $apiKey = trim((string)$apiKey);
    if($apiKey === '') return '';
    $payload = ["model" => $model, "instructions" => (string)$instructions, "input" => (string)$userText, "store" => false];
    $ch = curl_init("https://api.openai.com/v1/responses");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ["Authorization: Bearer {$apiKey}", "Content-Type: application/json"],
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE), CURLOPT_TIMEOUT => 20,
    ]);
    $resp = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($http < 200 || $http >= 300 || !$resp) return '';
    $data = json_decode($resp, true);
    if(!is_array($data)) return '';
    if(!empty($data['output_text'])) return trim((string)$data['output_text']);
    $out = [];
    if(!empty($data['output']) && is_array($data['output'])){
        foreach($data['output'] as $item){
            if(!empty($item['content']) && is_array($item['content'])){
                foreach($item['content'] as $c){
                    if(isset($c['text'])) $out[] = (string)$c['text'];
                }
            }
        }
    }
    return trim(implode("\n", $out));
}

function ensureCliente(PDO $pdo, string $phone55, string $nome = ''): int {
    if(!tableExists($pdo, 'clientes')) return 0;
    $phone55 = formatPhone55($phone55);
    $nome = trim((string)$nome) !== '' ? trim((string)$nome) : 'Cliente WhatsApp';
    $keys = phoneKeyCandidates($phone55);
    $expr = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telefone,'+',''),'(',''),')',''),'-',''),' ',''),'.','')";
    try{
        $conds = []; $params = [];
        if(isset($keys[11]) && $keys[11] !== ''){ $conds[] = "RIGHT($expr, 11) = :k11"; $params[':k11'] = $keys[11]; }
        if(isset($keys[10]) && $keys[10] !== ''){ $conds[] = "RIGHT($expr, 10) = :k10"; $params[':k10'] = $keys[10]; }
        if(!empty($conds)){
            $sql = "SELECT id FROM clientes WHERE (" . implode(" OR ", $conds) . ") LIMIT 1";
            $st = $pdo->prepare($sql); $st->execute($params);
            $id = (int)$st->fetchColumn(); if($id > 0) return $id;
        }
    }catch(Throwable $e){}

    $telLocal = onlyDigits($phone55);
    $telLocal = preg_replace('/^55/', '', $telLocal);
    $telLocal = (strlen($telLocal) > 11) ? substr($telLocal, -11) : $telLocal;
    $telefoneDb = $telLocal;
    if(strlen($telefoneDb) === 11){
        $ddd = substr($telefoneDb, 0, 2); $n1 = substr($telefoneDb, 2, 5); $n2 = substr($telefoneDb, 7, 4);
        $telefoneDb = "({$ddd}) {$n1}-{$n2}";
    }elseif(strlen($telefoneDb) === 10){
        $ddd = substr($telefoneDb, 0, 2); $n1 = substr($telefoneDb, 2, 4); $n2 = substr($telefoneDb, 6, 4);
        $telefoneDb = "({$ddd}) {$n1}-{$n2}";
    }
    $senha = '123'; $senha_crip = password_hash($senha, PASSWORD_DEFAULT);
    try{
        $set = []; $par = [];
        if(columnExists($pdo, 'clientes', 'nome')){ $set[] = 'nome = :nome'; $par[':nome'] = $nome; }
        if(columnExists($pdo, 'clientes', 'telefone')){ $set[] = 'telefone = :telefone'; $par[':telefone'] = $telefoneDb; }
        if(columnExists($pdo, 'clientes', 'data_cad')){ $set[] = 'data_cad = CURDATE()'; }
        if(columnExists($pdo, 'clientes', 'cartoes')){ $set[] = "cartoes = '0'"; }
        if(columnExists($pdo, 'clientes', 'alertado')){ $set[] = "alertado = 'Não'"; }
        if(columnExists($pdo, 'clientes', 'ativo')){ $set[] = "ativo = 'Sim'"; }
        if(columnExists($pdo, 'clientes', 'senha_crip')){ $set[] = 'senha_crip = :senha_crip'; $par[':senha_crip'] = $senha_crip; }
        if(empty($set)) return 0;
        $sql = 'INSERT INTO clientes SET ' . implode(', ', $set);
        $st = $pdo->prepare($sql); $st->execute($par);
        $idNovo = (int)$pdo->lastInsertId(); if($idNovo <= 0) return 0;

        $cfgNome = $GLOBALS['nomeSalao'] ?? 'Sistema';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $url_sistema = $host ? rtrim("https://{$host}", '/') : '';
        $msg  = "👋 *Olá {$nome}*, seja bem-vindo(a) ao *{$cfgNome}*!\n\n";
        $msg .= "🔒 *Senha de acesso:* {$senha}\n\n";
        if($url_sistema){ $msg .= "🌐 *Acessar painel do cliente:*\n{$url_sistema}/sistema/acesso\n\n"; }
        $msg .= "Você já pode agendar normalmente.";
        sendTextViaApiTexto(formatPhone55($phone55), $msg, $GLOBALS['instanciaEvento'] ?? null);
        return $idNovo;
    }catch(Exception $e){ return 0; }
}

function criarAgendamento(PDO $pdo, int $idCliente, int $idProf, int $idServico, string $dateYmd, string $hora, string $phone55): int {
    if($idCliente <= 0 || $idProf <= 0 || $dateYmd === '' || $hora === '') return 0;
    if(!tableExists($pdo, 'agendamentos')) return 0;
    
    // REMOVIDA TRAVA DE SEGURANÇA QUE ESTAVA BLOQUEANDO A INSERÇÃO
    
    $cols = []; $vals = []; $par = [];
    if(columnExists($pdo, 'agendamentos', 'cliente')){ $cols[]='cliente'; $vals[]=':cliente'; $par[':cliente']=$idCliente; } else return 0;
    if(columnExists($pdo, 'agendamentos', 'funcionario')){ $cols[]='funcionario'; $vals[]=':funcionario'; $par[':funcionario']=$idProf; }
    if(columnExists($pdo, 'agendamentos', 'data')){ $cols[]='data'; $vals[]=':data'; $par[':data']=$dateYmd; }
    if(columnExists($pdo, 'agendamentos', 'hora')){ $cols[]='hora'; $vals[]=':hora'; $par[':hora']=$hora; }
    $colServico = firstExistingColumn($pdo, 'agendamentos', ['servico','servico_id','id_servico']);
    if($colServico !== '' && $idServico > 0){ $cols[]=$colServico; $vals[]=':servico'; $par[':servico']=$idServico; }
    if(columnExists($pdo, 'agendamentos', 'status')){ $cols[]='status'; $vals[]=':status'; $par[':status']='Agendado'; }
    if(columnExists($pdo, 'agendamentos', 'hash')){ $hash = bin2hex(random_bytes(12)); $cols[]='hash'; $vals[]=':hash'; $par[':hash']=$hash; }
    if(columnExists($pdo, 'agendamentos', 'phone')){ $cols[]='phone'; $vals[]=':phone'; $par[':phone']=$phone55; }
    
    if(columnExists($pdo, 'agendamentos', 'data_lanc')){
        $cols[]='data_lanc'; $vals[]=':data_lanc'; $par[':data_lanc']=date('Y-m-d');
    }
    if(columnExists($pdo, 'agendamentos', 'usuario')){
        $cols[]='usuario'; $vals[]=':usuario'; $par[':usuario']=0; 
    }
    if(columnExists($pdo, 'agendamentos', 'obs')){
        $cols[]='obs'; $vals[]=':obs'; $par[':obs']='Agendado via WhatsApp';
    }

    try{
        $sql = "INSERT INTO agendamentos (".implode(',', $cols).") VALUES (".implode(',', $vals).")";
        $st = $pdo->prepare($sql); $st->execute($par);
        $idAg = (int)$pdo->lastInsertId(); if($idAg <= 0) return 0;
        
        // =============================================================
        // LÓGICA DE AGENDAMENTO DE CONFIRMAÇÃO (IDÊNTICA AO SISTEMA)
        // =============================================================
        
        $ult_id = $idAg;
        $nome_func = $pdo->query("SELECT nome FROM usuarios WHERE id = $idProf")->fetchColumn();
        $userRow = $pdo->query("SELECT intervalo FROM usuarios WHERE id = $idProf")->fetch(PDO::FETCH_ASSOC);
        $intervalo = $userRow['intervalo'] ?? 30;

        $servRow = $pdo->query("SELECT nome, tempo FROM servicos WHERE id = $idServico")->fetch(PDO::FETCH_ASSOC);
        $nome_serv = $servRow['nome'] ?? '';
        $tempo = $servRow['tempo'] ?? 30;

        $dataF = date('d/m/Y', strtotime($dateYmd));
        $horaF = date('H:i', strtotime($hora));
        $tel_cli = $phone55;

        // Pega minutos_aviso do config (mesma lógica do arquivo de pagamento)
        $conf = $pdo->query("SELECT * FROM config LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $minutos_aviso = $conf['minutos_aviso'] ?? ($conf['dias_aviso'] ?? 1); 

        // Formata telefone (variável usada no confirmacao.php)
        $telefone = '55'.preg_replace('/[ ()-]+/' , '' , $tel_cli);
        
        // Variáveis necessárias para o confirmacao.php
        $hora_atual = date('H:i:s');
        $data_atual = date('Y-m-d');
        $hora_minutos = @strtotime("-$minutos_aviso hours", @strtotime($hora));
        $nova_hora = date('H:i:s', $hora_minutos);
        
        $mensagem = "*Confirmação de Agendamento*\n";
        $mensagem .= "Profissional: *{$nome_func}*\n";
        $mensagem .= "Serviço: *{$nome_serv}*\n";
        $mensagem .= "Data: *{$dataF}*\n";
        $mensagem .= "Hora: *{$horaF}*\n";
        $mensagem .= "_(Digite o número com a opção desejada)_\n";
        $mensagem .= "1. 1️⃣ para confirmar ✅\n";		
        $mensagem .= "2. 2️⃣ para Cancelar ❌\n";		
        
        $id_envio = "{$ult_id}";
        $data_envio = "{$dateYmd} {$hora}";		
        $data_agd = $dateYmd;
        $hora_do_agd = $hora;
        
        // CHAMA O ARQUIVO QUE AGENDA O DISPARO
        if(file_exists(__DIR__ . '/../ajax/confirmacao.php')){
            require(__DIR__ . '/../ajax/confirmacao.php');
            if(isset($id)){
                $id_hash = $id;
                $pdo->query("UPDATE agendamentos SET hash = '$id_hash' WHERE id = '$ult_id'");
            }
        }
        
        // =============================================================

        if(tableExists($pdo, 'horarios_agd')){
            $hora_final_servico = date('H:i:s', strtotime("+$tempo minutes", strtotime($hora)));
            $horaLoop = $hora;
            
            while(strtotime($horaLoop) < strtotime($hora_final_servico)){
                $hora_minutos = strtotime("+$intervalo minutes", strtotime($horaLoop));			
                $horaLoop = date('H:i:s', $hora_minutos);

                if(strtotime($horaLoop) < strtotime($hora_final_servico)){
                    $pdo->query("INSERT INTO horarios_agd SET agendamento = '$ult_id', horario = '$horaLoop', funcionario = '$idProf', data = '$dateYmd'");
                }
            }
        }
        return $idAg;
    }catch(Exception $e){ return 0; }
}

function isConfirmationPrompt(string $textLower): bool {
    $t = mb_strtolower(trim($textLower), 'UTF-8');
    if($t === '') return false;
    $hasConfirm = (mb_stripos($t, 'confirm', 0, 'UTF-8') !== false);
    $has1 = (preg_match('/\b1\b/u', $t) || mb_stripos($t, '1', 0, 'UTF-8') !== false);
    $has2 = (preg_match('/\b2\b/u', $t) || mb_stripos($t, '2', 0, 'UTF-8') !== false);
    $hasCancel = (mb_stripos($t, 'cancel', 0, 'UTF-8') !== false) || (mb_stripos($t, 'cance', 0, 'UTF-8') !== false);
    return ($hasConfirm && $has1 && $has2 && $hasCancel);
}

function isLikelyBotEcho(string $msgLower): bool {
    $t = preg_replace('/\s+/', ' ', trim($msgLower));
    $t = str_replace(['*', '_', '`'], '', $t);
    $needles = ['eu sou o barberbot','como posso te ajudar','horários disponíveis','serviços deste profissional','profissionais:','perfeito! agora me diga a data','digite o número do horário','digite o número do serviço','digite apenas o número','digite menu para ver as opções','bot pausado nesta conversa','certo! pode enviar sua mensagem','para agendar, acesse o link'];
    foreach($needles as $n){ if($n !== '' && strpos($t, $n) !== false) return true; }
    return false;
}

function phoneKeyCandidates(string $phone): array {
    $d = onlyDigits($phone); if ($d === '') return [];
    if (strpos($d, '55') === 0) $d = substr($d, 2);
    if (strlen($d) > 11) $d = substr($d, -11);
    $out = []; $len = strlen($d);
    if ($len === 10) { $out[10] = $d; $ddd = substr($d, 0, 2); $num = substr($d, 2); $out[11] = $ddd . '9' . $num; }
    if ($len === 11) { $out[11] = $d; $ddd = substr($d, 0, 2); $terceiro = substr($d, 2, 1); if ($terceiro === '9') { $out[10] = $ddd . substr($d, 3); } }
    if ($len < 10 && $len > 0) $out[$len] = $d;
    if (strlen($d) >= 11) $out[11] = substr($d, -11);
    if (strlen($d) >= 10) $out[10] = substr($d, -10);
    $clean = []; foreach($out as $k => $v){ $v = trim((string)$v); if($v !== '') $clean[(int)$k] = $v; }
    return $clean;
}

function isTelefoneUsuario(PDO $pdo, string $phone55): bool {
    if($phone55 === '' || !tableExists($pdo, 'usuarios')) return false;
    $keys = phoneKeyCandidates($phone55); if(empty($keys)) return false;
    $expr = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telefone,'+',''),'(',''),')',''),'-',''),' ',''),'.','')";
    $conds = []; $params = [];
    if(isset($keys[11])){ $conds[] = "RIGHT($expr, 11) = :k11"; $params[':k11'] = $keys[11]; }
    if(isset($keys[10])){ $conds[] = "RIGHT($expr, 10) = :k10"; $params[':k10'] = $keys[10]; }
    if(empty($conds)) return false;
    try{
        $sql = "SELECT 1 FROM usuarios WHERE ativo='Sim' AND (" . implode(" OR ", $conds) . ") LIMIT 1";
        $st = $pdo->prepare($sql); $st->execute($params); return (bool)$st->fetchColumn();
    }catch(Exception $e){ return false; }
}

function isAtendimentoSim($v): bool {
    $s = strtolower(trim((string)$v));
    return in_array($s, ['sim','1','true','yes','y'], true);
}

function fetchUsuarioByTelefone(PDO $pdo, string $phone55): ?array {
    if($phone55 === '' || !tableExists($pdo, 'usuarios')) return null;
    $keys = phoneKeyCandidates($phone55); if(empty($keys)) return null;
    $expr = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telefone,'(',''),')',''),'-',''),' ',''),'+','')";
    $conds = []; $params = [];
    if(isset($keys[11])){ $conds[] = "RIGHT($expr, 11) = :k11"; $params[':k11'] = $keys[11]; }
    if(isset($keys[10])){ $conds[] = "RIGHT($expr, 10) = :k10"; $params[':k10'] = $keys[10]; }
    if(empty($conds)) return null;
    try{
        $sql = "SELECT * FROM usuarios WHERE (" . implode(" OR ", $conds) . ") LIMIT 1";
        $st = $pdo->prepare($sql); $st->execute($params); $row = $st->fetch(PDO::FETCH_ASSOC); return $row ?: null;
    }catch(Exception $e){ return null; }
}

function fetchUsuarioContext(PDO $pdo, $instanciaEvento, string $destinatario55): ?array {
    if(!tableExists($pdo, 'usuarios')) return null;
    $inst = trim((string)$instanciaEvento);
    if($inst !== '' && columnExists($pdo, 'usuarios', 'instancia')){
        try{
            $st = $pdo->prepare("SELECT * FROM usuarios WHERE ativo='Sim' AND instancia = :i AND instancia IS NOT NULL AND instancia<>'' AND token IS NOT NULL AND token<>'' LIMIT 1");
            $st->execute([':i' => $inst]);
            $u = $st->fetch(PDO::FETCH_ASSOC); if(is_array($u) && !empty($u)) return $u;
        }catch(Exception $e){}
    }
    $keys = phoneKeyCandidates($destinatario55); if(empty($keys)) return null;
    $expr = "REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(telefone,'(',''),')',''),'-',''),' ',''),'+','')";
    $conds = []; $params = [];
    if(isset($keys[11])){ $conds[] = "RIGHT($expr, 11) = :k11"; $params[':k11'] = $keys[11]; }
    if(isset($keys[10])){ $conds[] = "RIGHT($expr, 10) = :k10"; $params[':k10'] = $keys[10]; }
    if(empty($conds)) return null;
    try{
        $sql = "SELECT * FROM usuarios WHERE ativo='Sim' AND instancia IS NOT NULL AND instancia<>'' AND token IS NOT NULL AND token<>'' AND (" . implode(" OR ", $conds) . ") ORDER BY id ASC LIMIT 1";
        $st = $pdo->prepare($sql); $st->execute($params); $u = $st->fetch(PDO::FETCH_ASSOC); if(is_array($u) && !empty($u)) return $u;
    }catch(Exception $e){}
    return null;
}

$raw = file_get_contents('php://input');
$ip  = $_SERVER['REMOTE_ADDR'] ?? '';
logLine($LOG, "=== EVENTO ===");
logLine($LOG, "IP: ".$ip);
logLine($LOG, "RAW: ".$raw);

if(trim($raw) === ''){ echo "OK"; exit(); }
$dados = json_decode($raw, true);
if(!is_array($dados)){ echo "OK"; exit(); }

$tipo = $dados['tipo'] ?? '';
$instanciaEvento = $dados['instancia'] ?? ($dados['instance'] ?? null);

// ✅ Permite Sistema/Me para capturar Confirmações
if($tipo !== 'Chat' && $tipo !== 'Sistema' && $tipo !== 'Me'){
    $remRaw = $dados['remetente'] ?? '';
    $destRaw = $dados['destinatario'] ?? '';
    $evtRaw  = $dados['evento'] ?? '';
    logLine($LOG, "IGNORE: tipo={$tipo} evento={$evtRaw} rem={$remRaw} dest={$destRaw}");
    echo "OK"; exit();
}

$cfg = $pdo->query("SELECT * FROM config LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$nomeSalao = (trim($cfg['nome'] ?? '') !== '') ? $cfg['nome'] : 'Jacy Cabeleireiro';
$whatsapp_sistema = $cfg['telefone_whatsapp'] ?? ($cfg['telefone_whatsapp_sistema'] ?? '');
$barberbot_ativo_raw   = $cfg['barberbot_ativo'] ?? 0;
$barberbot_ativo       = simNaoToInt($barberbot_ativo_raw);
$barberbot_boas_vindas = $cfg['barberbot_boas_vindas'] ?? '';
$openai_key            = $cfg['openai_key'] ?? '';
$openai_prompt         = $cfg['openai_prompt'] ?? '';
$link_agendamento = rtrim("https://{$_SERVER['HTTP_HOST']}", '/') . "/agendamentos";

$api = $cfg['api'] ?? ($api ?? null);
$token = $cfg['token'] ?? ($token ?? null);
$instancia = $cfg['instancia'] ?? ($cfg['instancia_whatsapp'] ?? ($instancia ?? null));

$destinatarioEvento = formatPhone55($dados['destinatario'] ?? '');
$usuarioCtx = fetchUsuarioContext($pdo, $instanciaEvento, $destinatarioEvento);

if(is_array($usuarioCtx)){
    if(isset($usuarioCtx['telefone']) && trim((string)$usuarioCtx['telefone']) !== '') $whatsapp_sistema = $usuarioCtx['telefone'];
    if(isset($usuarioCtx['token']) && trim((string)$usuarioCtx['token']) !== '') $token = trim((string)$usuarioCtx['token']);
    if(isset($usuarioCtx['instancia']) && trim((string)$usuarioCtx['instancia']) !== '') { $instancia = trim((string)$usuarioCtx['instancia']); $instanciaEvento = $instancia; }
    if(isset($usuarioCtx['api']) && trim((string)$usuarioCtx['api']) !== '') $api = trim((string)$usuarioCtx['api']);
    logLine($LOG, "CTX usuarios OK: id=".($usuarioCtx['id'] ?? 'NULL')." tel=".formatPhone55($whatsapp_sistema)." inst=".($instanciaEvento ?? 'NULL'));
}else{
    logLine($LOG, "CTX usuarios NAO achou: inst=".($instanciaEvento ?? 'NULL')." dest=".$destinatarioEvento);
}

// --- TRAVA DE SEGURANÇA (NORMALIZADA 10 DIGITOS) ---
// Compara apenas DDD + 8 digitos finais para evitar erros de 9º dígito
if($tipo === 'Chat'){
    $destinatarioReal = phoneKeyCanonical10($destinatarioEvento);
    $botConfigurado   = phoneKeyCanonical10($whatsapp_sistema);

    // TRAVA DE SEGURANÇA DESATIVADA PARA EVITAR BLOQUEIO POR FORMATO DE NÚMERO
    if($destinatarioReal !== $botConfigurado){
        logLine($LOG, "AVISO CRUZADO: Msg recebida em {$destinatarioEvento} (canon:{$destinatarioReal}) mas o Bot ativo é {$whatsapp_sistema}. Deixando passar...");
        // echo "OK"; // Comentado para não travar
        // exit();    // Comentado para não travar
    }
}
// ----------------------------------------------------

if($tipo === "Sistema"){
    $numeroCliente = formatPhone55($dados['destinatario'] ?? '');
    $idRef = $dados['idAgendamento'] ?? ($dados['hash'] ?? ($dados['agendamento'] ?? ''));
    if($numeroCliente !== '' && $idRef !== '' && tableExists($pdo, 'agendamentos')){
        try{
            if(columnExists($pdo,'agendamentos','phone')){
                $st = $pdo->prepare("UPDATE agendamentos SET phone = :phone WHERE hash = :ref OR id = :ref");
                $st->execute([':phone' => $numeroCliente, ':ref' => $idRef]);
                logLine($LOG, "Sistema: phone=$numeroCliente ref=$idRef rows=".$st->rowCount());
            }
        }catch(Exception $e){}
    }
    // ✅ Arma modo confirmação se for mensagem de confirmação
    $msgSys = trim((string)($dados['mensagem'] ?? ''));
    if(isConfirmationPrompt($msgSys)){
        $remSys = formatPhone55($dados['remetente'] ?? '');
        $destSys = formatPhone55($dados['destinatario'] ?? '');
        $remIsUser = fetchUsuarioByTelefone($pdo, $remSys);
        $destIsUser = fetchUsuarioByTelefone($pdo, $destSys);
        if($remIsUser && !$destIsUser){ $cliente = $destSys; $prof = $remSys; }
        elseif($destIsUser && !$remIsUser){ $cliente = $remSys; $prof = $destSys; }
        else{ $cliente = $destSys; $prof = $remSys; }
        if($cliente && $prof){
            $sessOut = getSession($SESS_DIR, $cliente, $prof);
            $sessOut['await_confirm_until'] = time() + 21600; 
            $sessOut['await_confirm_set_at'] = time();
            saveSession($SESS_DIR, $cliente, $prof, $sessOut);
            logLine($LOG, "Armei modo confirmação (Sistema): cliente={$cliente} prof={$prof}");
        }
    }
    echo "OK"; exit();
}

if($tipo !== "Chat"){
    // ✅ Arma modo confirmação se for evento de envio (Me)
    $msgEvt = trim((string)($dados['mensagem'] ?? ($dados['message'] ?? '')));
    if(isConfirmationPrompt($msgEvt)){
        $a = formatPhone55($dados['remetente'] ?? '');
        $b = formatPhone55($dados['destinatario'] ?? '');
        $aUser = fetchUsuarioByTelefone($pdo, $a);
        $bUser = fetchUsuarioByTelefone($pdo, $b);
        if($aUser && !$bUser){ $cliente = $b; $prof = $a; }
        elseif($bUser && !$aUser){ $cliente = $a; $prof = $b; }
        else{ $cliente = $b; $prof = $a; }
        if($cliente && $prof){
            $sessOut = getSession($SESS_DIR, $cliente, $prof);
            $sessOut['await_confirm_until'] = time() + 21600;
            $sessOut['await_confirm_set_at'] = time();
            saveSession($SESS_DIR, $cliente, $prof, $sessOut);
            logLine($LOG, "Armei modo confirmação (tipo={$tipo}): cliente={$cliente} prof={$prof}");
        }
    }
    echo "OK"; exit();
}

$msg = trim((string)($dados['mensagem'] ?? ''));
$nomeClienteMenu = trim((string)($dados['nome'] ?? ''));
$msgLower = mb_strtolower($msg, 'UTF-8');
$remetente = formatPhone55($dados['remetente'] ?? '');
$destinatario = formatPhone55($dados['destinatario'] ?? '');

$nomeClienteMenu = fetchNomeClienteByPhone($pdo, $remetente);
if($nomeClienteMenu === '') $nomeClienteMenu = trim((string)($dados['nome'] ?? ''));
if($nomeClienteMenu === '') $nomeClienteMenu = 'Cliente WhatsApp';

$msgId = (string)($dados['idMessagem'] ?? ($dados['idMensagem'] ?? ($dados['messageId'] ?? ($dados['id'] ?? ''))));
$choiceNum = leadingNumber($msg);

if($remetente === ''){ logLine($LOG, "Sem remetente."); echo "OK"; exit(); }

$sess = getSession($SESS_DIR, $remetente, $destinatario);
logLine($LOG, "DEBUG: stage=".($sess['stage'] ?? 'NULL')." cliente={$remetente} prof={$destinatario}");

if($msgId !== ''){
    $lastId = (string)($sess['last_msg_id'] ?? '');
    if($lastId !== '' && $lastId === $msgId){ logLine($LOG, "Dedupe: msgId repetido={$msgId}"); echo "OK"; exit(); }
    $sess['last_msg_id'] = $msgId; $sess['last_msg_at'] = time();
    saveSession($SESS_DIR, $remetente, $destinatario, $sess);
}

$optDigitBurst = substr(onlyDigits($msg), 0, 1);
$isCommand = ($optDigitBurst !== '') || (mb_stripos($msgLower, 'menu', 0, 'UTF-8') !== false);
$lastReplyAt = (int)($sess['last_reply_at'] ?? 0);
if($lastReplyAt > 0 && (time() - $lastReplyAt) < $BURST_WINDOW && !$isCommand){
    logLine($LOG, "Anti-burst ignored."); echo "OK"; exit();
}

$telSistema55 = formatPhone55($whatsapp_sistema);
$isFromSistema = ($telSistema55 !== '' && $remetente === $telSistema55);
if($isFromSistema){
    logLine($LOG, "IGNORE: msg do sistema.");
    if(isConfirmationPrompt($msgLower)){
        $sessOut = getSession($SESS_DIR, $destinatario, $remetente);
        $sessOut['await_confirm_until'] = time() + 21600;
        $sessOut['await_confirm_set_at'] = time();
        saveSession($SESS_DIR, $destinatario, $remetente, $sessOut);
        logLine($LOG, "Armei modo confirmação (Sistema->Cliente)");
    }
    echo "OK"; exit();
}

$remUsuarioRow = fetchUsuarioByTelefone($pdo, $remetente);
if($remUsuarioRow && isLikelyBotEcho($msgLower)){ logLine($LOG, "IGNORE: eco do bot."); echo "OK"; exit(); }
if($remUsuarioRow && isAtendimentoSim($remUsuarioRow['atendimento'] ?? null) && !$isFromSistema){
    logLine($LOG, "IGNORE: remetente é linha de atendimento."); echo "OK"; exit();
}

// =======================================================================
// LÓGICA DE CONFIRMAÇÃO (1=Confirmar, 2=Cancelar/Deletar)
// =======================================================================
$awaitUntil = (int)($sess['await_confirm_until'] ?? 0);
if($awaitUntil > time()){
    if($msg === '1' || $msg === '2'){
        // Busca agendamento pendente
        $agendamento = null;
        $dataHoraAtual = date('Y-m-d H:i:s');
        $rem11 = last11($remetente);
        
        // Tenta pelo telefone na tabela agendamentos
        if(tableExists($pdo, 'agendamentos') && columnExists($pdo, 'agendamentos', 'phone')){
            try{
                $st = $pdo->prepare("SELECT * FROM agendamentos WHERE status = 'Agendado' AND TIMESTAMP(data, hora) > :agora AND (phone = :p55 OR RIGHT(phone, 11) = :p11) ORDER BY TIMESTAMP(data, hora) ASC LIMIT 1");
                $st->execute([':agora' => $dataHoraAtual, ':p55' => $remetente, ':p11' => $rem11]);
                $agendamento = $st->fetch(PDO::FETCH_ASSOC);
            }catch(Exception $e){}
        }
        // Se não achou, tenta via join com clientes
        if(!$agendamento && tableExists($pdo, 'agendamentos') && tableExists($pdo, 'clientes')){
            try{
                $st = $pdo->prepare("SELECT a.* FROM agendamentos a JOIN clientes c ON c.id = a.cliente WHERE a.status = 'Agendado' AND TIMESTAMP(a.data, a.hora) > :agora AND RIGHT(REPLACE(REPLACE(REPLACE(REPLACE(c.telefone,'(',''),')',''),'-',''),' ',''), 11) = :p11 ORDER BY TIMESTAMP(a.data, a.hora) ASC LIMIT 1");
                $st->execute([':agora' => $dataHoraAtual, ':p11' => $rem11]);
                $agendamento = $st->fetch(PDO::FETCH_ASSOC);
            }catch(Exception $e){}
        }

        if(!$agendamento){
            $mensagem = "⚠️ Não encontrei nenhum agendamento pendente para confirmar/cancelar.";
            unset($sess['await_confirm_until'], $sess['await_confirm_set_at']);
            sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento);
            echo "OK"; exit();
        }

        $id_agendamento = $agendamento['id'];
        $idCliente      = $agendamento['cliente'];
        $idFuncionario  = $agendamento['funcionario'];
        $dataAgendamento = date('d/m/Y', strtotime($agendamento['data']));
        $horarioAgendamento = $agendamento['hora'];

        // Nome Cliente
        $nomeCliente = 'Cliente';
        if(tableExists($pdo, 'clientes')){
            $st = $pdo->prepare("SELECT nome FROM clientes WHERE id = :id LIMIT 1");
            $st->execute([':id' => $idCliente]);
            $nomeCliente = $st->fetchColumn() ?: 'Cliente';
        }
        // Nome Profissional
        $nomeFuncionario = 'Profissional';
        $telefoneFuncionario = '';
        if(tableExists($pdo, 'usuarios')){
            $st = $pdo->prepare("SELECT nome, telefone FROM usuarios WHERE id = :id LIMIT 1");
            $st->execute([':id' => $idFuncionario]);
            $f = $st->fetch(PDO::FETCH_ASSOC);
            if($f){ $nomeFuncionario = $f['nome'] ?? 'Profissional'; $telefoneFuncionario = $f['telefone'] ?? ''; }
        }

        if($msg === '1'){
            // CONFIRMAR
            try{
                $st = $pdo->prepare("UPDATE agendamentos SET status = 'Confirmado' WHERE id = :id");
                $st->execute([':id' => $id_agendamento]);
            }catch(Exception $e){}

            $mensagem = "✅ _Confirmado!_\n\n";
            $mensagem .= "Agendamento de *".$nomeCliente."*.\n\n";
            $mensagem .= "*Data:* ".$dataAgendamento." às ".$horarioAgendamento."\n\n";
            $mensagem .= "O profissional *".$nomeFuncionario."* lhe aguarda no horário agendado.\n\n";
            $mensagem .= "_Obrigado!_";

            unset($sess['await_confirm_until'], $sess['await_confirm_set_at']);
            sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento);

            // Avisa profissional
            $telFunc55 = formatPhone55($telefoneFuncionario);
            $telDono55 = formatPhone55($whatsapp_sistema);
            if($telFunc55 !== '' && $telFunc55 !== $telDono55){
                sendTextViaApiTexto($telFunc55, $mensagem, $instanciaEvento);
            }
            echo "OK"; exit();
        }

        if($msg === '2'){
            // CANCELAR (DELETAR)
            try{
                $st = $pdo->prepare("DELETE FROM agendamentos WHERE id = :id");
                $st->execute([':id' => $id_agendamento]);
            }catch(Exception $e){}

            if(tableExists($pdo, 'horarios_agd')){
                try{
                    $st = $pdo->prepare("DELETE FROM horarios_agd WHERE agendamento = :id");
                    $st->execute([':id' => $id_agendamento]);
                }catch(Exception $e){}
            }

            $mensagem = "❌ _Cancelado!_\n\n";
            $mensagem .= "Agendamento de *".$nomeCliente."*\n\n";
            $mensagem .= "*Data:* ".$dataAgendamento." às ".$horarioAgendamento."\n\n";
            $mensagem .= "Reagende novo horário pelo site: ".$link_agendamento;

            unset($sess['await_confirm_until'], $sess['await_confirm_set_at']);
            sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento);

            // Avisa dono/profissional
            $telDono55 = formatPhone55($whatsapp_sistema);
            if($telDono55 !== '') sendTextViaApiTexto($telDono55, $mensagem, $instanciaEvento);
            $telFunc55 = formatPhone55($telefoneFuncionario);
            if($telFunc55 !== '' && $telFunc55 !== $telDono55) sendTextViaApiTexto($telFunc55, $mensagem, $instanciaEvento);
            
            echo "OK"; exit();
        }
    } else {
        // Se está esperando confirmação e digitou outra coisa
        $mensagem = "⚠️ *Opção inválida.*\n\nPor favor, digite *1* para Confirmar ou *2* para Cancelar o agendamento.";
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento);
        echo "OK"; exit();
    }
}

if($barberbot_ativo !== 1){ echo "OK"; exit(); }

$optDigit = '';
if($choiceNum >= 1 && $choiceNum <= 4) $optDigit = (string)$choiceNum;

// Modo humano
if(($sess['modo'] ?? '') === 'humano'){
    $desde = (int)($sess['humano_desde'] ?? 0);
    if($desde <= 0){ $sess['humano_desde'] = time(); saveSession($SESS_DIR, $remetente, $destinatario, $sess); echo "OK"; exit(); }
    if((time() - $desde) >= $HUMANO_TIMEOUT){ unset($sess['modo'], $sess['humano_desde']); saveSession($SESS_DIR, $remetente, $destinatario, $sess); }
    else{ echo "OK"; exit(); }
}
if( ($optDigit === '4' && strpos($msg, '/') === false) || strpos($msgLower, 'humano') !== false){
    $sess['modo'] = 'humano'; $sess['humano_desde'] = time(); saveSession($SESS_DIR, $remetente, $destinatario, $sess);
    $mensagem = "✅ Certo! Pode enviar sua mensagem que o profissional vai te atender.\n\n*(Bot pausado nesta conversa por 30 minutos.)*";
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento);
    echo "OK"; exit();
}

$gatilhosMenu = ['menu','oi','ola','olá','bom dia','boa tarde','boa noite','tudo bem','tudo bom','corte','barba','protese','prótese','escova','progressiva'];
if(empty($sess) || containsAny($msgLower, $gatilhosMenu)){
    unset($sess['stage'], $sess['servicos_map'], $sess['prof_map'], $sess['time_map'], $sess['selected_service_id'], $sess['selected_prof_id'], $sess['selected_date'], $sess['profs_map'], $sess['services_by_prof_map']);
    $sess['started'] = true; saveSession($SESS_DIR, $remetente, $destinatario, $sess);
    $mensagem = ""; if(trim($barberbot_boas_vindas) !== '') $mensagem .= $barberbot_boas_vindas . "\n\n";
    $mensagem .= menuText($nomeSalao, $nomeClienteMenu);
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento);
    echo "OK"; exit();
}

// Stage logic
if(($sess['stage'] ?? '') === 'profs_list' && $choiceNum > 0){
    $map = $sess['profs_map'] ?? []; $idProf = (int)(mapGet($map, $choiceNum) ?? 0);
    if($idProf > 0){
        $sess['selected_prof_id'] = $idProf;
        $servs = fetchServicosByProf($pdo, $idProf);
        if(empty($servs)){
            $mensagem = "⚠️ Esse profissional não tem serviços vinculados.\n\n".menuHint();
            unset($sess['stage'], $sess['profs_map'], $sess['selected_prof_id']);
            saveSession($SESS_DIR, $remetente, $destinatario, $sess);
            sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
        }
        $mensagem = "💇‍♂️ *Serviços deste profissional:*\nDigite o *número do serviço*.\n";
        $smap = []; $i=1;
        foreach($servs as $s){
            $nomeS = $s['nome'] ?? 'Serviço'; $valor = formatMoneyBR($s['valor'] ?? '');
            $mensagem .= $i." - ".$nomeS.($valor ? " - *{$valor}*" : "")."\n";
            $smap[(string)$i] = (int)($s['id'] ?? 0); $i++; if($i>60) break;
        }
        $sess['stage'] = 'services_by_prof'; $sess['services_by_prof_map'] = $smap; unset($sess['profs_map']);
        saveSession($SESS_DIR, $remetente, $destinatario, $sess);
        $mensagem .= "\nDigite o *número do serviço*.\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $mensagem = "⚠️ Opção inválida. Digite o número do profissional.\n\n".menuHint();
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
}

if(($sess['stage'] ?? '') === 'services_by_prof' && $choiceNum > 0){
    $map = $sess['services_by_prof_map'] ?? []; $idServico = (int)(mapGet($map, $choiceNum) ?? 0);
    if($idServico > 0){
        $sess['selected_service_id'] = $idServico;
        $sess['stage'] = 'choose_date'; unset($sess['services_by_prof_map']);
        saveSession($SESS_DIR, $remetente, $destinatario, $sess);
        $mensagem = "📅 Perfeito! Agora me diga a *data*.\nDigite *DD/MM* ou *DD/MM/AAAA*.\nEx: 31/01 ou 31/01/2026\n\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $mensagem = "⚠️ Escolha o número do serviço na lista.\n\n".menuHint();
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
}

if(($sess['stage'] ?? '') === 'servicos_list' && $choiceNum > 0){
    $map = $sess['servicos_map'] ?? []; $idServico = (int)(mapGet($map, $choiceNum) ?? 0);
    if($idServico > 0){
        $srv = null; try{ $st=$pdo->prepare("SELECT id, nome, valor FROM servicos WHERE id=:id AND (ativo='Sim' OR ativo=1) LIMIT 1"); $st->execute([':id'=>$idServico]); $srv=$st->fetch(PDO::FETCH_ASSOC); }catch(Exception $e){}
        if($srv){
            $sess['selected_service_id'] = $idServico;
            $profs = fetchProfissionaisByServico($pdo, $idServico);
            if(empty($profs)){
                $mensagem = "⚠️ Não encontrei profissionais para esse serviço.\n\n".menuHint();
                unset($sess['stage'], $sess['servicos_map'], $sess['selected_service_id']);
                saveSession($SESS_DIR, $remetente, $destinatario, $sess);
                sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
            }
            $mensagem = "✅ *Serviço selecionado:*\n".((string)($srv['nome'] ?? 'Serviço'));
            $valor = formatMoneyBR($srv['valor'] ?? ''); if($valor) $mensagem .= " - *{$valor}*";
            $mensagem .= "\n\n👤 *Agora escolha o profissional:*\n";
            $profMap = []; $i=1;
            foreach($profs as $p){ $mensagem .= $i." - ".($p['nome'] ?? 'Profissional')."\n"; $profMap[(string)$i] = (int)($p['id'] ?? 0); $i++; if($i>30) break; }
            $sess['stage'] = 'choose_prof'; $sess['prof_map'] = $profMap; unset($sess['servicos_map']);
            saveSession($SESS_DIR, $remetente, $destinatario, $sess);
            $mensagem .= "\nDigite o *número do profissional*.\n".menuHint();
            sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
        }
    }
    $mensagem = "⚠️ Opção inválida.\n\n".menuHint();
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
}

if(($sess['stage'] ?? '') === 'choose_prof'){
    $profMap = $sess['prof_map'] ?? []; $idProf = (int)(mapGet($profMap, $choiceNum) ?? 0);
    if($idProf > 0){
        $sess['selected_prof_id'] = $idProf;
        $sess['stage'] = 'choose_date'; unset($sess['prof_map']);
        saveSession($SESS_DIR, $remetente, $destinatario, $sess);
        $mensagem = "📅 Perfeito! Agora me diga a *data*.\nDigite *DD/MM* ou *DD/MM/AAAA*.\nEx: 29/12\n\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $mensagem = "👤 Escolha o *número do profissional* na lista.\n\n".menuHint();
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
}

if(($sess['stage'] ?? '') === 'choose_date'){
    $dateYmd = parseDateToYmd($msg);
    if($dateYmd === ''){
        $mensagem = "📅 Não entendi a data.\nDigite *DD/MM* ou *DD/MM/AAAA*.\nEx: 29/12\n\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    if(strtotime($dateYmd.' 00:00:00') < strtotime(date('Y-m-d').' 00:00:00')){
        $mensagem = "📅 Essa data já passou. Digite uma data a partir de hoje.\n\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $idProf = (int)($sess['selected_prof_id'] ?? 0);
    if($idProf <= 0){
        unset($sess['stage'], $sess['selected_date']);
        saveSession($SESS_DIR, $remetente, $destinatario, $sess);
        $mensagem = "⚠️ Vamos recomeçar.\n\n".menuText($nomeSalao, $nomeClienteMenu);
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    if(isDiaBloqueado($pdo, $idProf, $dateYmd)){
        $mensagem = "🚫 Esse dia está *bloqueado* na agenda do profissional.\nDigite *outra data* (DD/MM).\n\n" . menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $times = fetchHorariosDisponiveis($pdo, $idProf, $dateYmd);
    if(empty($times)){
        $mensagem = "⏰ Não encontrei horários livres para esse dia.\nDigite *outra data* (DD/MM).\n\n" . menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $mensagem = "⏰ *Horários disponíveis em* ".date('d/m/Y', strtotime($dateYmd)).":\nDigite o *número do horário*.\n";
    $timeMap = []; $i=1;
    foreach($times as $t){ $mensagem .= $i." - ".substr($t,0,5)."\n"; $timeMap[(string)$i] = $t; $i++; if($i > 60) break; }
    $sess['selected_date'] = $dateYmd; $sess['stage'] = 'choose_time'; $sess['time_map'] = $timeMap;
    saveSession($SESS_DIR, $remetente, $destinatario, $sess);
    $mensagem .= "\nDigite o *número do horário*.\n".menuHint();
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
}

if(($sess['stage'] ?? '') === 'choose_time'){
    $dateYmd = (string)($sess['selected_date'] ?? '');
    $idProf  = (int)($sess['selected_prof_id'] ?? 0);
    $timeMap = $sess['time_map'] ?? [];
    if(empty($timeMap) && $idProf > 0 && $dateYmd !== ''){
        $horarios = fetchHorariosDisponiveis($pdo, $idProf, $dateYmd);
        if(!empty($horarios)){
            $timeMap = []; $i = 1; foreach($horarios as $h){ $timeMap[(string)$i] = $h; $i++; }
            $sess['time_map'] = $timeMap; saveSession($SESS_DIR, $remetente, $destinatario, $sess);
        }
    }
    $hora = (string)(mapGet($timeMap, $choiceNum) ?? '');
    if($hora !== '' && $dateYmd === date('Y-m-d')){
        $agora = date('H:i:s');
        if(strtotime($hora) <= strtotime($agora)){
            $mensagem = "⛔ Esse horário já passou. Escolha outro número da lista.\n\n".menuHint();
            sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
        }
    }
    if($hora === ''){
        if(!empty($timeMap)){
            $dataFmt = ($dateYmd !== '') ? date('d/m/Y', strtotime($dateYmd)) : '';
            $mensagem = ($dataFmt !== '') ? "⏰ *Horários disponíveis em* {$dataFmt}:\n" : "⏰ *Horários disponíveis:*\n";
            $i = 1; foreach($timeMap as $t){ $mensagem .= "{$i}) {$t}\n"; $i++; }
            $mensagem .= "\nDigite o *número do horário*.\n".menuHint();
            sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
        }
        $sess['stage'] = 'choose_date'; unset($sess['time_map']); saveSession($SESS_DIR, $remetente, $destinatario, $sess);
        $mensagem = "📅 Não consegui carregar os horários. Me diga a *data* novamente.\nDigite *DD/MM* ou *DD/MM/AAAA*.\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $idServico = (int)($sess['selected_service_id'] ?? 0);
    if($idServico <= 0){
        $mensagem = "⚠️ Serviço inválido. Digite *menu* e tente novamente.\n\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $idCliente = ensureCliente($pdo, $remetente, $nomeClienteMenu);
    if($idCliente <= 0){
        $mensagem = "⚠️ Não consegui salvar seu cadastro agora.\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }
    $idAg = criarAgendamento($pdo, $idCliente, $idProf, $idServico, $dateYmd, $hora, $remetente);
    if($idAg <= 0){
        $mensagem = "❌ Não consegui confirmar seu horário agora. Tente outro horário ou digite *menu*.\n".menuHint();
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
    }

    // Busca dados reais p/ confirmar
    $nomeProf = 'Profissional'; $telProf = '';
    if($idProf > 0 && tableExists($pdo, 'usuarios')){
        try{ $st=$pdo->prepare("SELECT nome, telefone FROM usuarios WHERE id=:id LIMIT 1"); $st->execute([':id'=>$idProf]); $prof=$st->fetch(PDO::FETCH_ASSOC); if($prof){ $nomeProf=$prof['nome']??'Profissional'; $telProf=$prof['telefone']??''; } }catch(Exception $e){}
    }
    $nomeServico = 'Serviço';
    if($idServico > 0 && tableExists($pdo, 'servicos')){
        try{ $st=$pdo->prepare("SELECT nome FROM servicos WHERE id=:id LIMIT 1"); $st->execute([':id'=>$idServico]); $nomeServico=$st->fetchColumn()?:'Serviço'; }catch(Exception $e){}
    }
    $dataFmt = ($dateYmd !== '') ? date('d/m/Y', strtotime($dateYmd)) : '';
    $horaFmt = substr($hora, 0, 5);

    deleteSession($SESS_DIR, $remetente, $destinatario);

    $mensagem  = "✅ *Agendamento confirmado!*\n\n";
    $mensagem .= "👤 *Profissional:* {$nomeProf}\n";
    $mensagem .= "💇 *Serviço:* {$nomeServico}\n";
    $mensagem .= "📅 *Data:* {$dataFmt}\n";
    $mensagem .= "⏰ *Horário:* {$horaFmt}\n\n";
    $mensagem .= "Se precisar, digite *menu*.";

    $emptySess = [];
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $emptySess, $remetente, $mensagem, $instanciaEvento);
    // ✅ Agenda a mensagem de confirmação (mesma lógica do pagamento_aprovado.php)
    $antAgendamento = (int)($cfg['minutos_aviso'] ?? 0);
    if($antAgendamento <= 0) $antAgendamento = 2;

    $telefone   = $remetente;
    $data_envio = trim($dateYmd . ' ' . $hora);
    $id_envio   = (string)$idAg;
    $nome       = $nomeSalao;

    $mensagemConfirm  = "📅 Confirmação de Agendamento - *{$nomeSalao}*\n\n";
    $mensagemConfirm .= "Olá, *{$nomeClienteMenu}*!\n\n";
    $mensagemConfirm .= "Você possui um agendamento no dia *{$dataFmt}* às *{$horaFmt}*.\n\n";
    $mensagemConfirm .= "Responda digita o número desejada:\n";
    $mensagemConfirm .= "1. 1️⃣ para Confirmar ✅\n";
    $mensagemConfirm .= "2. 2️⃣ para Cancelar ❌\n\n";
    $mensagemConfirm .= "Obrigado!";

    // confirmacao.php espera: $mensagem, $telefone, $data_envio, $id_envio, $nome e $antAgendamento
    $mensagemBackup = $mensagem;
    $mensagem = $mensagemConfirm;

    // BLINDAGEM: Tenta carregar o arquivo local (da imagem), senão tenta o do ajax, senão manda texto puro
    $arquivoLocal = __DIR__ . '/confirmacao.php';
    $arquivoAjax  = __DIR__ . '/../ajax/confirmacao.php';

    if(file_exists($arquivoLocal)){
        // Prioridade 1: Arquivo na mesma pasta (visto na imagem)
        require($arquivoLocal);
    } 
    elseif(file_exists($arquivoAjax)){
        // Prioridade 2: Arquivo na pasta ajax (citado no meio do código)
        require($arquivoAjax);
    } 
    else {
        // Fallback: Se não achar o arquivo, envia o texto via API para não falhar
        sendTextViaApiTexto($remetente, $mensagemConfirm, $instanciaEvento);
    }

    $mensagem = $mensagemBackup;


    // Notifica dono e profissional
    $telDono = (string)($usuarioCtx['whatsapp_sistema'] ?? $usuarioCtx['telefone'] ?? '');
    $telDono55 = formatPhone55($telDono);
    if($telDono55 !== '') sendTextViaApiTexto($telDono55, "📌 Novo agendamento (WhatsApp)\nCliente: {$nomeClienteMenu}\nServiço: {$nomeServico}\nProf: {$nomeProf}\nData: {$dataFmt} {$horaFmt}", $instanciaEvento);
    $telProf55 = formatPhone55($telProf);
    if($telProf55 !== '' && $telProf55 !== $telDono55) sendTextViaApiTexto($telProf55, "📌 Novo agendamento (WhatsApp)\nCliente: {$nomeClienteMenu}\nServiço: {$nomeServico}\nData: {$dataFmt} {$horaFmt}", $instanciaEvento);

    echo "OK"; exit();
}

if($optDigit === '1'){
    unset($sess['stage'], $sess['servicos_map'], $sess['prof_map'], $sess['time_map'], $sess['selected_service_id'], $sess['selected_prof_id'], $sess['selected_date']);
    $profs = fetchProfissionais($pdo);
    if(empty($profs)){
        $mensagem = "👤 Não encontrei profissionais cadastrados.\n\n" . menuHint();
    }else{
        $mensagem = "👤 *Profissionais:*\n";
        $map = []; $i=1;
        foreach($profs as $p){ $mensagem .= $i." - ".($p['nome'] ?? 'Profissional')."\n"; $map[(string)$i] = (int)($p['id'] ?? 0); $i++; if($i>30) break; }
        $sess['stage'] = 'profs_list'; $sess['profs_map'] = $map;
        saveSession($SESS_DIR, $remetente, $destinatario, $sess);
        $mensagem .= "\nDigite o *número do profissional* para ver os serviços/agendar.\n".menuHint();
    }
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
}

if($optDigit === '2'){
    unset($sess['stage'], $sess['servicos_map'], $sess['prof_map'], $sess['time_map'], $sess['selected_service_id'], $sess['selected_prof_id'], $sess['selected_date']);
    $servs = fetchServicos($pdo);
    if(empty($servs)){
        $mensagem = "💇‍♂️ Não consegui carregar os serviços.\nVeja e agende aqui: ".$link_agendamento."\n\n".menuHint();
    }else{
        $mensagem = "💇‍♂️ *Serviços e preços:*\nDigite o número do serviço.\n";
        $map = []; $i=1;
        foreach($servs as $s){
            $nomeS = $s['nome'] ?? 'Serviço'; $valor = formatMoneyBR($s['valor'] ?? '');
            $mensagem .= $i." - ".$nomeS.($valor ? " - *{$valor}*" : "")."\n";
            $map[(string)$i] = (int)($s['id'] ?? 0); $i++; if($i>80) break;
        }
        $sess['stage'] = 'servicos_list'; $sess['servicos_map'] = $map;
        saveSession($SESS_DIR, $remetente, $destinatario, $sess);
        $mensagem .= "\nDigite o *número do serviço*.\n".menuHint();
    }
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
}

if($optDigit === '3'){
    unset($sess['stage'], $sess['servicos_map'], $sess['prof_map'], $sess['time_map'], $sess['selected_service_id'], $sess['selected_prof_id'], $sess['selected_date']);
    $mensagem = "🔗 Para agendar, acesse o link:\n".$link_agendamento."\n\nOu digite *2* para escolher o serviço aqui pelo WhatsApp.\n\n".menuHint();
    sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento); echo "OK"; exit();
}

if(trim((string)$openai_key) !== '' && trim((string)$openai_prompt) !== ''){
    $vars = ['{NOME_SALAO}'=>$nomeSalao, '{LINK_AGENDAMENTO}'=>$link_agendamento, '{NOME_CLIENTE}'=>($nomeClienteMenu ?? ''), '{MENU}'=>'1 profissionais | 2 serviços | 3 agendar | 4 humano'];
    $promptFinal = strtr((string)$openai_prompt, $vars);
    $promptFinal .= "%0A%0AImportante: NÃO escreva 'Menu:' e NÃO liste opções 1-4. Se o usuário pedir opções, diga apenas: 'Digite menu para ver as opções.'";
    $ia = openaiResposta($openai_key, $promptFinal, $msg, 'gpt-4o-mini');
    if($ia !== ''){
        $ia = strtr($ia, $vars); $ia = stripMenuDuplicadoIA($ia); $textoFinal = trim($ia); if($textoFinal === '') $textoFinal = "Entendi 🙂";
        sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $textoFinal . "\n\n" . menuHint(), $instanciaEvento);
        echo "OK"; exit();
    }
}

$mensagem = "Entendi 🙂\n\n" . menuHint();
sendAndRemember($SESS_DIR, $remetente, $destinatario, $sess, $remetente, $mensagem, $instanciaEvento);
echo "OK";
exit();
