<?php
// ─── Configuração ────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // ← altera para o teu utilizador
define('DB_PASS', '');            // ← altera para a tua password
define('DB_NAME', 'portfolio');

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
