<?php
/**
 * Corre este ficheiro UMA VEZ no browser para criar o admin.
 * Depois apaga-o do servidor!
 *   http://localhost/portfolio/setup_admin.php
 */
require 'db.php';

$username = 'admin';       // ← muda se quiseres
$password = 'admin123';    // ← muda para uma password segura

$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = db()->prepare("
    INSERT INTO admins (username, password_hash) VALUES (?, ?)
    ON DUPLICATE KEY UPDATE password_hash = ?
");
$stmt->execute([$username, $hash, $hash]);

echo "<h2>Admin criado com sucesso!</h2>";
echo "<p>Utilizador: <strong>$username</strong></p>";
echo "<p>Apaga este ficheiro agora: <code>setup_admin.php</code></p>";
