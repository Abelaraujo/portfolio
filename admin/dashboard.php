<?php
session_start();
if (empty($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require '../db.php';

$tab      = $_GET['tab'] ?? 'projects';
$projects = db()->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
$about    = db()->query("SELECT * FROM about LIMIT 1")->fetch() ?: [];
$messages = db()->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
$unread   = db()->query("SELECT COUNT(*) FROM messages WHERE is_read=0")->fetchColumn();

// Marcar mensagens como lidas ao abrir o tab
if ($tab === 'messages') {
    db()->exec("UPDATE messages SET is_read=1");
}

// Projeto a editar (opcional)
$editing = null;
if (!empty($_GET['edit'])) {
    $s = db()->prepare("SELECT * FROM projects WHERE id=?");
    $s->execute([(int)$_GET['edit']]);
    $editing = $s->fetch();
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin · Dashboard</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Segoe UI', sans-serif;
  background: #0d0d0d; color: #ddd;
  min-height: 100vh;
}
a { color: inherit; text-decoration: none; }

/* ── Sidebar ── */
.layout { display: flex; min-height: 100vh; }
.sidebar {
  width: 220px; background: #161616;
  border-right: 1px solid #222;
  padding: 2rem 1rem;
  display: flex; flex-direction: column; gap: .3rem;
  flex-shrink: 0;
}
.sidebar h2 { font-size: .75rem; color: #555; text-transform: uppercase;
              letter-spacing: .1em; margin-bottom: .5rem; padding: 0 .5rem; }
.nav-link {
  display: flex; align-items: center; gap: .6rem;
  padding: .55rem .75rem; border-radius: 8px;
  font-size: .9rem; color: #aaa; transition: .15s;
}
.nav-link:hover, .nav-link.active { background: #222; color: #fff; }
.badge {
  margin-left: auto; background: #ef4444;
  color: #fff; border-radius: 99px;
  padding: .1rem .45rem; font-size: .7rem;
}
.logout { margin-top: auto; }
.logout .nav-link { color: #666; }

/* ── Main ── */
.main { flex: 1; padding: 2rem; max-width: 900px; }
.main h1 { font-size: 1.4rem; margin-bottom: 1.5rem; color: #fff; }

/* ── Cards / Forms ── */
.card {
  background: #1a1a1a; border: 1px solid #262626;
  border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem;
}
.card h3 { margin-bottom: 1rem; font-size: 1rem; color: #fff; }
label { display: block; font-size: .78rem; color: #777; margin-bottom: .25rem; }
input[type=text], input[type=email], input[type=url], textarea {
  width: 100%; padding: .65rem .9rem;
  background: #111; border: 1px solid #2a2a2a;
  border-radius: 8px; color: #eee; font-size: .9rem;
  margin-bottom: .9rem;
}
textarea { min-height: 90px; resize: vertical; font-family: inherit; }
input:focus, textarea:focus { outline: none; border-color: #4f8ef7; }
.row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.btn {
  padding: .55rem 1.2rem; border-radius: 8px;
  border: none; cursor: pointer; font-size: .9rem;
}
.btn-primary { background: #4f8ef7; color: #fff; }
.btn-primary:hover { background: #3a7de0; }
.btn-danger  { background: #3a1a1a; color: #f87171; }
.btn-danger:hover  { background: #4a2222; }
.btn-ghost { background: #222; color: #aaa; }
.btn-ghost:hover { background: #2a2a2a; }
.btn-sm { padding: .35rem .8rem; font-size: .8rem; }

/* ── Table ── */
table { width: 100%; border-collapse: collapse; font-size: .88rem; }
th { text-align: left; padding: .6rem .8rem; color: #555;
     font-size: .75rem; text-transform: uppercase; border-bottom: 1px solid #222; }
td { padding: .7rem .8rem; border-bottom: 1px solid #1e1e1e; vertical-align: top; }
tr:last-child td { border-bottom: none; }
.tech-tag {
  display: inline-block; background: #1e2a3a; color: #7ab3f7;
  border-radius: 4px; padding: .1rem .45rem; font-size: .75rem; margin: .1rem;
}
.msg-new { color: #fff; font-weight: 600; }
.msg-read { color: #555; }
</style>
</head>
<body>
<div class="layout">

  <!-- Sidebar -->
  <nav class="sidebar">
    <h2>Menu</h2>
    <a href="?tab=projects" class="nav-link <?= $tab==='projects' ? 'active' : '' ?>">
      📁 Projetos
    </a>
    <a href="?tab=about" class="nav-link <?= $tab==='about' ? 'active' : '' ?>">
      👤 Sobre mim
    </a>
    <a href="?tab=messages" class="nav-link <?= $tab==='messages' ? 'active' : '' ?>">
      ✉️ Mensagens
      <?php if ($unread > 0): ?>
        <span class="badge"><?= $unread ?></span>
      <?php endif; ?>
    </a>
    <div class="logout">
      <a href="logout.php" class="nav-link">🚪 Sair</a>
    </div>
  </nav>

  <!-- Main -->
  <main class="main">

    <!-- ═══ PROJETOS ═══ -->
    <?php if ($tab === 'projects'): ?>
    <h1><?= $editing ? 'Editar Projeto' : 'Projetos' ?></h1>

    <!-- Formulário criar / editar -->
    <div class="card">
      <h3><?= $editing ? '✏️ Editar' : '➕ Novo Projeto' ?></h3>
      <form method="POST" action="../actions/project.php">
        <input type="hidden" name="action" value="<?= $editing ? 'update' : 'create' ?>">
        <?php if ($editing): ?>
          <input type="hidden" name="id" value="<?= $editing['id'] ?>">
        <?php endif; ?>
        <div class="row">
          <div>
            <label>Título</label>
            <input type="text" name="title" value="<?= htmlspecialchars($editing['title'] ?? '') ?>" required>
          </div>
          <div>
            <label>Tecnologias (separadas por vírgula)</label>
            <input type="text" name="tech" value="<?= htmlspecialchars($editing['tech'] ?? '') ?>" placeholder="PHP, MySQL, HTML">
          </div>
        </div>
        <label>Descrição</label>
        <textarea name="description"><?= htmlspecialchars($editing['description'] ?? '') ?></textarea>
        <div class="row">
          <div>
            <label>URL do projeto</label>
            <input type="url" name="url" value="<?= htmlspecialchars($editing['url'] ?? '') ?>" placeholder="https://...">
          </div>
          <div>
            <label>URL da imagem</label>
            <input type="url" name="image_url" value="<?= htmlspecialchars($editing['image_url'] ?? '') ?>" placeholder="https://...">
          </div>
        </div>
        <button class="btn btn-primary" type="submit"><?= $editing ? 'Guardar' : 'Criar Projeto' ?></button>
        <?php if ($editing): ?>
          <a href="dashboard.php" class="btn btn-ghost" style="margin-left:.5rem">Cancelar</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Lista de projetos -->
    <div class="card">
      <table>
        <thead>
          <tr><th>Título</th><th>Tecnologias</th><th>Ações</th></tr>
        </thead>
        <tbody>
        <?php foreach ($projects as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['title']) ?></td>
            <td>
              <?php foreach (explode(',', $p['tech']) as $t): ?>
                <span class="tech-tag"><?= htmlspecialchars(trim($t)) ?></span>
              <?php endforeach; ?>
            </td>
            <td style="display:flex;gap:.4rem;flex-wrap:wrap">
              <a href="?tab=projects&edit=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">✏️ Editar</a>
              <form method="POST" action="../actions/project.php"
                    onsubmit="return confirm('Apagar projeto?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button class="btn btn-danger btn-sm" type="submit">🗑️ Apagar</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$projects): ?>
          <tr><td colspan="3" style="color:#444;text-align:center">Nenhum projeto ainda.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- ═══ SOBRE MIM ═══ -->
    <?php elseif ($tab === 'about'): ?>
    <h1>Sobre Mim</h1>
    <div class="card">
      <form method="POST" action="../actions/about.php">
        <div class="row">
          <div>
            <label>Nome</label>
            <input type="text" name="name" value="<?= htmlspecialchars($about['name'] ?? '') ?>" required>
          </div>
          <div>
            <label>Cargo / Título</label>
            <input type="text" name="role" value="<?= htmlspecialchars($about['role'] ?? '') ?>" placeholder="Desenvolvedor Full Stack">
          </div>
        </div>
        <label>Bio</label>
        <textarea name="bio"><?= htmlspecialchars($about['bio'] ?? '') ?></textarea>
        <div class="row">
          <div>
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($about['email'] ?? '') ?>">
          </div>
          <div>
            <label>GitHub URL</label>
            <input type="url" name="github" value="<?= htmlspecialchars($about['github'] ?? '') ?>">
          </div>
        </div>
        <label>LinkedIn URL</label>
        <input type="url" name="linkedin" value="<?= htmlspecialchars($about['linkedin'] ?? '') ?>">
        <button class="btn btn-primary" type="submit">💾 Guardar</button>
      </form>
    </div>

    <!-- ═══ MENSAGENS ═══ -->
    <?php elseif ($tab === 'messages'): ?>
    <h1>Mensagens</h1>
    <div class="card">
      <table>
        <thead>
          <tr><th>Nome</th><th>Email</th><th>Mensagem</th><th>Data</th></tr>
        </thead>
        <tbody>
        <?php foreach ($messages as $m): ?>
          <tr class="<?= $m['is_read'] ? 'msg-read' : 'msg-new' ?>">
            <td><?= htmlspecialchars($m['name']) ?></td>
            <td><?= htmlspecialchars($m['email']) ?></td>
            <td style="max-width:300px"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
            <td style="white-space:nowrap;color:#555">
              <?= date('d/m/y H:i', strtotime($m['created_at'])) ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$messages): ?>
          <tr><td colspan="4" style="color:#444;text-align:center">Nenhuma mensagem ainda.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

  </main>
</div>
</body>
</html>
