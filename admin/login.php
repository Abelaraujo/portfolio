<?php
session_start();
if (!empty($_SESSION['admin'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require '../db.php';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin'] = $admin['username'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Utilizador ou password incorretos.';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin · Login</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', sans-serif;
    background: #0f0f0f;
    color: #eee;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .card {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    padding: 2.5rem;
    width: 100%;
    max-width: 380px;
  }
  h1 { font-size: 1.4rem; margin-bottom: 1.5rem; color: #fff; }
  label { display: block; font-size: .8rem; color: #888; margin-bottom: .3rem; }
  input {
    width: 100%; padding: .7rem 1rem;
    background: #111; border: 1px solid #333;
    border-radius: 8px; color: #fff; font-size: .95rem;
    margin-bottom: 1rem;
  }
  input:focus { outline: none; border-color: #4f8ef7; }
  button {
    width: 100%; padding: .75rem;
    background: #4f8ef7; color: #fff;
    border: none; border-radius: 8px;
    font-size: 1rem; cursor: pointer;
  }
  button:hover { background: #3a7de0; }
  .error {
    background: #3a1a1a; color: #f87171;
    border-radius: 8px; padding: .7rem 1rem;
    margin-bottom: 1rem; font-size: .9rem;
  }
</style>
</head>
<body>
<div class="card">
  <h1>🔒 Painel Admin</h1>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST">
    <label>Utilizador</label>
    <input type="text" name="username" required autofocus>
    <label>Password</label>
    <input type="password" name="password" required>
    <button type="submit">Entrar</button>
  </form>
</div>
</body>
</html>
