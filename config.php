<?php
// ---- Settings: XAMPP defaults ----
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');            // XAMPP's root password is empty by default
define('DB_NAME', 'student_housing');
define('SITE_NAME', 'Campus Nest');
define('CURRENCY', 'GH₵ ');       // Ghana cedis
define('CONTACT_EMAIL', 'hello@campusnest.example');

session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $db->set_charset('utf8mb4');
} catch (mysqli_sql_exception $ex) {
    http_response_code(500);
    exit('Could not connect to the database. Start MySQL in XAMPP and import database.sql.');
}

// ---- Helpers ----
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money($n) { return CURRENCY . number_format((float)$n, 2); }
function logged_in() { return !empty($_SESSION['user']); }
function redirect($url) { header("Location: $url"); exit; }

function flash($msg = null, $type = 'ok') {
    if ($msg !== null) { $_SESSION['flash'][] = [$type, $msg]; return; }
    $all = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $all;
}
function require_login() {
    if (!logged_in()) { flash('Log in to continue.', 'error'); redirect('login.php'); }
}
function csrf_field() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}
function csrf_ok() {
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
function get_rooms($db) {
    return $db->query("SELECT r.*, (SELECT COUNT(*) FROM bookings b WHERE b.room_id = r.id AND b.status='active') AS taken
                       FROM rooms r ORDER BY r.price")->fetch_all(MYSQLI_ASSOC);
}

// Image folders (relative to site root). Gallery and rooms both work.
function image_search_dirs() {
    return ['assets/img/rooms', 'assets/img/gallery', 'assets/images/rooms', 'assets/images', 'images/rooms', 'images'];
}

function image_file_index() {
    static $index = null;
    if ($index !== null) return $index;
    $index = [];
    $exts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
    foreach (image_search_dirs() as $dir) {
        $abs = __DIR__ . '/' . $dir;
        if (!is_dir($abs)) continue;
        foreach (scandir($abs) as $file) {
            if (!is_file("$abs/$file")) continue;
            $info = pathinfo($file);
            if (!in_array(strtolower($info['extension'] ?? ''), $exts, true)) continue;
            $key = preg_replace('/[\s_]+/', '-', strtolower($info['filename']));
            $index[$key] ??= $dir . '/' . rawurlencode($file);
        }
    }
    return $index;
}

/** Resolve a photo by file name without extension (e.g. 18, 18-1, single-room). */
function image_url($stem) {
    $key = preg_replace('/[\s_]+/', '-', strtolower(trim($stem)));
    return image_file_index()[$key] ?? null;
}

// One image file per room (each stem used only once). Add a line when you upload a new room photo.
function room_photo_stems($code) {
    static $map = [
        'a-101' => ['18-1'],
        'b-110' => ['28-1'],
        'b-215' => ['88'],
        'c-301' => ['58'],
        'c-305' => ['78'],
        'd-401' => ['68'],
        'd-402' => ['98'],
    ];
    return $map[strtolower(trim($code))] ?? [];
}

function room_has_photos($code) {
    return get_room_photos($code) !== [];
}

function get_listable_rooms($db) {
    $out = [];
    foreach (get_rooms($db) as $r) {
        if (room_has_photos($r['code'])) {
            $out[] = $r;
        }
    }
    return $out;
}

// Photos for a room. Prefer room_photo_stems(); also accepts files named after the room code:
//   a-101-1.jpg, a-101-2.jpg ...   (a-101.jpg also works for a single photo)
function get_room_photos($code) {
    $urls = [];
    foreach (room_photo_stems($code) as $stem) {
        $url = image_url($stem);
        if ($url && !in_array($url, $urls, true)) $urls[] = $url;
    }
    if ($urls) return array_slice($urls, 0, 1);

    $base    = strtolower(trim($code));
    $compact = str_replace('-', '', $base);
    $pattern = '/^(?:' . preg_quote($base, '/') . '|' . preg_quote($compact, '/') . ')(?:-(\d+))?$/';
    $found = [];
    foreach (image_search_dirs() as $dir) {
        $abs = __DIR__ . '/' . $dir;
        if (!is_dir($abs)) continue;
        foreach (scandir($abs) as $file) {
            if (!is_file("$abs/$file")) continue;
            $info = pathinfo($file);
            if (!in_array(strtolower($info['extension'] ?? ''), ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'], true)) continue;
            $name = preg_replace('/[\s_]+/', '-', strtolower($info['filename']));
            if (preg_match($pattern, $name, $m)) {
                $found[] = [isset($m[1]) ? (int)$m[1] : 1, $dir . '/' . rawurlencode($file)];
            }
        }
    }
    if ($found) {
        usort($found, fn($a, $b) => $a[0] <=> $b[0] ?: strcmp($a[1], $b[1]));
        return array_slice(array_column($found, 1), 0, 6);
    }
    return [];
}
