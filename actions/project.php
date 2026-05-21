<?php
session_start();
require '../db.php';

// Proteger: só admins
if (empty($_SESSION['admin'])) {
    header('Location: ../admin/login.php');
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {

    case 'create':
        $stmt = db()->prepare("
            INSERT INTO projects (title, description, tech, url, image_url)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            trim($_POST['title']),
            trim($_POST['description']),
            trim($_POST['tech']),
            trim($_POST['url']       ?? ''),
            trim($_POST['image_url'] ?? ''),
        ]);
        break;

    case 'update':
        $stmt = db()->prepare("
            UPDATE projects
            SET title=?, description=?, tech=?, url=?, image_url=?
            WHERE id=?
        ");
        $stmt->execute([
            trim($_POST['title']),
            trim($_POST['description']),
            trim($_POST['tech']),
            trim($_POST['url']       ?? ''),
            trim($_POST['image_url'] ?? ''),
            (int)$_POST['id'],
        ]);
        break;

    case 'delete':
        $stmt = db()->prepare("DELETE FROM projects WHERE id=?");
        $stmt->execute([(int)$_POST['id']]);
        break;
}

header('Location: ../admin/dashboard.php');
exit;
