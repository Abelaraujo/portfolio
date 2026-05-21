<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$message = trim($_POST['message'] ?? '');

// Validação básica
$errors = [];
if (empty($name))              $errors[] = 'Nome é obrigatório.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
if (strlen($message) < 10)    $errors[] = 'Mensagem demasiado curta.';

if ($errors) {
    // Redireciona com erro codificado na URL
    $msg = urlencode(implode(' ', $errors));
    header("Location: ../index.php?status=error&msg=$msg#contacto");
    exit;
}

$stmt = db()->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
$stmt->execute([$name, $email, $message]);

header('Location: ../index.php?status=ok#contacto');
exit;
