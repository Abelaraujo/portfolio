<?php
session_start();
require '../db.php';

if (empty($_SESSION['admin'])) {
    header('Location: ../admin/login.php');
    exit;
}

// Verifica se já existe registo
$exists = db()->query("SELECT COUNT(*) FROM about")->fetchColumn();

if ($exists) {
    $stmt = db()->prepare("
        UPDATE about SET name=?, role=?, bio=?, email=?, github=?, linkedin=?
        WHERE id=1
    ");
} else {
    $stmt = db()->prepare("
        INSERT INTO about (name, role, bio, email, github, linkedin)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
}

$stmt->execute([
    trim($_POST['name']),
    trim($_POST['role']),
    trim($_POST['bio']),
    trim($_POST['email']),
    trim($_POST['github']   ?? ''),
    trim($_POST['linkedin'] ?? ''),
]);

header('Location: ../admin/dashboard.php?tab=about');
exit;
