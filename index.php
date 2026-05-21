<?php
require 'db.php';

$projects = db()->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
$about    = db()->query("SELECT * FROM about LIMIT 1")->fetch() ?: [
    'name'     => 'O Teu Nome',
    'role'     => 'Desenvolvedor',
    'bio'      => 'Preenche o "Sobre mim" no painel admin.',
    'email'    => '',
    'github'   => '',
    'linkedin' => '',
];

$status  = $_GET['status'] ?? '';
$formMsg = $_GET['msg']    ?? '';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($about['name']) ?> · Portfolio</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=Instrument+Serif:ital@0;1&family=JetBrains+Mono:wght@300;400&display=swap" rel="stylesheet">
<style>
/* ─────────────────────────────────────────────
   RESET & VARS
───────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --bg:        #F2EDE6;
  --bg2:       #EAE3DA;
  --ink:       #1A1410;
  --ink2:      #5C5048;
  --ink3:      #A89F95;
  --red:       #C8391A;
  --red-light: #F4C4B8;
  --cream:     #FAF7F2;
  --border:    rgba(26,20,16,.12);
  --border-dark: rgba(26,20,16,.22);
  --mono:      'JetBrains Mono', monospace;
  --sans:      'Syne', sans-serif;
  --serif:     'Instrument Serif', serif;
}

html { scroll-behavior: smooth; }

body {
  font-family: var(--sans);
  background: var(--bg);
  color: var(--ink);
  overflow-x: hidden;
}

a { color: inherit; text-decoration: none; }

/* ─────────────────────────────────────────────
   CUSTOM CURSOR
───────────────────────────────────────────── */
/* ─────────────────────────────────────────────
   NOISE OVERLAY
───────────────────────────────────────────── */
body::before {
  content: '';
  position: fixed; inset: 0;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='1'/%3E%3C/svg%3E");
  opacity: .025;
  pointer-events: none;
  z-index: 1000;
}

/* ─────────────────────────────────────────────
   NAV
───────────────────────────────────────────── */
nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 500;
  display: flex; align-items: center; justify-content: space-between;
  padding: 1.25rem 3rem;
  background: var(--bg);
  border-bottom: 1px solid var(--border);
}
.nav-logo {
  font-family: var(--sans);
  font-weight: 800;
  font-size: 1.1rem;
  letter-spacing: -.02em;
  color: var(--ink);
  position: relative;
}
.nav-logo::after {
  content: '';
  display: inline-block;
  width: 6px; height: 6px;
  background: var(--red);
  border-radius: 50%;
  margin-left: 3px;
  vertical-align: super;
}
.nav-links {
  display: flex; gap: 2.5rem;
  font-size: .8rem;
  font-weight: 600;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--ink2);
}
.nav-links a {
  position: relative;
  transition: color .2s;
}
.nav-links a::after {
  content: '';
  position: absolute; bottom: -3px; left: 0;
  width: 0; height: 1px;
  background: var(--red);
  transition: width .3s;
}
.nav-links a:hover { color: var(--ink); }
.nav-links a:hover::after { width: 100%; }

/* ─────────────────────────────────────────────
   HERO
───────────────────────────────────────────── */
#hero {
  min-height: 100vh;
  display: grid;
  grid-template-rows: 1fr auto;
  padding: 0 3rem;
  padding-top: 7rem;
  position: relative;
  overflow: hidden;
}

.hero-bg-text {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  font-family: var(--sans);
  font-weight: 800;
  font-size: 22vw;
  color: transparent;
  -webkit-text-stroke: 1px var(--border-dark);
  white-space: nowrap;
  pointer-events: none;
  user-select: none;
  letter-spacing: -.04em;
  opacity: .4;
}

.hero-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  align-items: end;
  gap: 4rem;
  padding-bottom: 5rem;
  margin-top: auto;
}

.hero-left {}

.hero-eyebrow {
  display: flex; align-items: center; gap: .75rem;
  font-family: var(--mono);
  font-size: .7rem;
  color: var(--ink3);
  letter-spacing: .1em;
  margin-bottom: 1.5rem;
}
.hero-eyebrow::before {
  content: '';
  display: block;
  width: 32px; height: 1px;
  background: var(--red);
}

.hero-name {
  font-family: var(--sans);
  font-weight: 800;
  font-size: clamp(3rem, 7vw, 5.5rem);
  line-height: .95;
  letter-spacing: -.04em;
  color: var(--ink);
  margin-bottom: .5rem;
}
.hero-name em {
  font-family: var(--serif);
  font-style: italic;
  font-weight: 400;
  color: var(--red);
}

.hero-role {
  font-family: var(--serif);
  font-style: italic;
  font-size: clamp(1.2rem, 2.5vw, 1.8rem);
  color: var(--ink2);
  margin-bottom: 2.5rem;
}

.hero-ctas {
  display: flex; gap: 1rem; align-items: center;
}

.btn-primary {
  display: inline-flex; align-items: center; gap: .6rem;
  padding: .85rem 2rem;
  background: var(--ink);
  color: var(--cream);
  font-family: var(--sans);
  font-size: .85rem;
  font-weight: 700;
  letter-spacing: .05em;
  text-transform: uppercase;
  border-radius: 2px;
  transition: background .2s, transform .15s;
}
.btn-primary:hover { background: var(--red); transform: translateY(-2px); }
.btn-primary svg { width: 16px; height: 16px; }

.btn-outline {
  display: inline-flex; align-items: center; gap: .5rem;
  font-family: var(--sans);
  font-size: .8rem;
  font-weight: 600;
  color: var(--ink2);
  letter-spacing: .1em;
  text-transform: uppercase;
  border-bottom: 1px solid var(--border-dark);
  padding-bottom: 2px;
  transition: color .2s, border-color .2s;
}
.btn-outline:hover { color: var(--red); border-color: var(--red); }

.hero-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 1.5rem;
}

.hero-bio {
  font-family: var(--serif);
  font-style: italic;
  font-size: 1.15rem;
  color: var(--ink2);
  line-height: 1.7;
  max-width: 340px;
  text-align: right;
}

.hero-socials {
  display: flex; gap: 1.25rem;
  font-family: var(--mono);
  font-size: .7rem;
  color: var(--ink3);
  letter-spacing: .08em;
}
.hero-socials a { transition: color .2s; }
.hero-socials a:hover { color: var(--red); }

.hero-bottom {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1.25rem 0;
  border-top: 1px solid var(--border);
  font-family: var(--mono);
  font-size: .68rem;
  color: var(--ink3);
  letter-spacing: .1em;
}
.hero-scroll {
  display: flex; align-items: center; gap: .75rem;
}
.scroll-line {
  width: 40px; height: 1px; background: var(--ink3);
  animation: scrollPulse 2s ease-in-out infinite;
}
@keyframes scrollPulse {
  0%, 100% { width: 40px; opacity: 1; }
  50% { width: 16px; opacity: .4; }
}

/* ─────────────────────────────────────────────
   SECTION COMMONS
───────────────────────────────────────────── */
.section {
  padding: 7rem 3rem;
  max-width: 1200px;
  margin: 0 auto;
}
.section-header {
  display: flex; align-items: baseline; justify-content: space-between;
  border-top: 1px solid var(--border-dark);
  padding-top: 1.5rem;
  margin-bottom: 4rem;
}
.section-num {
  font-family: var(--mono);
  font-size: .65rem;
  color: var(--red);
  letter-spacing: .15em;
}
.section-title {
  font-family: var(--sans);
  font-weight: 800;
  font-size: clamp(1.8rem, 4vw, 3rem);
  letter-spacing: -.03em;
  color: var(--ink);
}
.section-link {
  font-family: var(--mono);
  font-size: .7rem;
  color: var(--ink3);
  letter-spacing: .1em;
  transition: color .2s;
}
.section-link:hover { color: var(--red); }

/* ─────────────────────────────────────────────
   PROJECTS
───────────────────────────────────────────── */
#projetos { background: var(--bg); }

.projects-list {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.project-item {
  display: grid;
  grid-template-columns: 3rem 1fr auto;
  align-items: center;
  gap: 2rem;
  padding: 2rem 0;
  border-bottom: 1px solid var(--border);
  cursor: none;
  transition: background .2s;
  position: relative;
  overflow: hidden;
}
.project-item::before {
  content: '';
  position: absolute; inset: 0;
  background: var(--red-light);
  transform: scaleX(0);
  transform-origin: left;
  transition: transform .4s cubic-bezier(.4,0,.2,1);
  z-index: 0;
}
.project-item:hover::before { transform: scaleX(1); }
.project-item > * { position: relative; z-index: 1; }

.project-num {
  font-family: var(--mono);
  font-size: .7rem;
  color: var(--ink3);
  letter-spacing: .1em;
}
.project-info {}
.project-title {
  font-family: var(--sans);
  font-weight: 700;
  font-size: 1.3rem;
  letter-spacing: -.02em;
  color: var(--ink);
  margin-bottom: .35rem;
  transition: color .2s;
}
.project-item:hover .project-title { color: var(--red); }
.project-desc {
  font-family: var(--serif);
  font-style: italic;
  font-size: .95rem;
  color: var(--ink2);
}
.project-meta {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: .75rem;
}
.project-tech {
  display: flex; flex-wrap: wrap; gap: .35rem; justify-content: flex-end;
}
.tech-pill {
  font-family: var(--mono);
  font-size: .65rem;
  color: var(--ink3);
  background: var(--border);
  border: 1px solid var(--border-dark);
  border-radius: 2px;
  padding: .2rem .6rem;
  letter-spacing: .06em;
  transition: background .2s, color .2s;
}
.project-item:hover .tech-pill {
  background: rgba(200,57,26,.12);
  color: var(--red);
  border-color: rgba(200,57,26,.2);
}
.project-arrow {
  font-size: 1.2rem;
  color: var(--ink3);
  transition: transform .2s, color .2s;
}
.project-item:hover .project-arrow { transform: translate(3px, -3px); color: var(--red); }

.project-img-preview {
  position: fixed; top: 50%; right: 5%;
  width: 320px; height: 220px;
  object-fit: cover;
  border-radius: 4px;
  pointer-events: none;
  opacity: 0;
  transform: translateY(-50%) scale(.95);
  transition: opacity .3s, transform .3s;
  z-index: 200;
  box-shadow: 0 24px 60px rgba(0,0,0,.25);
}
.project-item:hover .project-img-preview {
  opacity: 1;
  transform: translateY(-50%) scale(1);
}

.no-projects {
  font-family: var(--serif);
  font-style: italic;
  color: var(--ink3);
  text-align: center;
  padding: 4rem;
  font-size: 1.1rem;
}

/* ─────────────────────────────────────────────
   ABOUT
───────────────────────────────────────────── */
#sobre {
  background: var(--ink);
  color: var(--cream);
  padding: 7rem 3rem;
}
#sobre .section-header {
  border-color: rgba(255,255,255,.12);
}
#sobre .section-num { color: var(--red); }
#sobre .section-title { color: var(--cream); }

.about-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5rem;
  align-items: start;
}
.about-big {
  font-family: var(--sans);
  font-weight: 800;
  font-size: clamp(2rem, 4vw, 3.2rem);
  line-height: 1.1;
  letter-spacing: -.03em;
  color: var(--cream);
  margin-bottom: 2rem;
}
.about-big em {
  font-family: var(--serif);
  font-style: italic;
  color: var(--red);
}
.about-bio {
  font-family: var(--serif);
  font-style: italic;
  font-size: 1.1rem;
  color: rgba(250,247,242,.55);
  line-height: 1.8;
}

.about-details {}
.detail-block {
  padding: 1.5rem 0;
  border-bottom: 1px solid rgba(255,255,255,.08);
  display: flex; align-items: flex-start; gap: 1.25rem;
}
.detail-icon {
  width: 36px; height: 36px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 6px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1rem;
  flex-shrink: 0;
}
.detail-content {}
.detail-label {
  font-family: var(--mono);
  font-size: .65rem;
  color: rgba(250,247,242,.35);
  letter-spacing: .12em;
  text-transform: uppercase;
  margin-bottom: .25rem;
}
.detail-value {
  font-family: var(--sans);
  font-size: .95rem;
  color: var(--cream);
}
.detail-value a { color: #7DAEFF; }
.detail-value a:hover { color: var(--red-light); }

/* ─────────────────────────────────────────────
   CONTACT
───────────────────────────────────────────── */
#contacto { background: var(--bg2); }

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 5rem;
  align-items: start;
}
.contact-left {}
.contact-big {
  font-family: var(--sans);
  font-weight: 800;
  font-size: clamp(2rem, 4vw, 3.5rem);
  letter-spacing: -.03em;
  color: var(--ink);
  line-height: 1;
  margin-bottom: 1.5rem;
}
.contact-big em {
  font-family: var(--serif);
  font-style: italic;
  color: var(--red);
}
.contact-sub {
  font-family: var(--serif);
  font-style: italic;
  color: var(--ink2);
  font-size: 1.05rem;
  line-height: 1.7;
}
.contact-right {}

.alert {
  padding: .85rem 1.2rem;
  border-radius: 2px;
  margin-bottom: 1.25rem;
  font-family: var(--mono);
  font-size: .78rem;
  letter-spacing: .05em;
}
.alert-ok { background: #d4edda; color: #1a6b35; border-left: 3px solid #1a6b35; }
.alert-error { background: #fde8e4; color: var(--red); border-left: 3px solid var(--red); }

.form-group { margin-bottom: 1.25rem; }
.form-group label {
  display: block;
  font-family: var(--mono);
  font-size: .65rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--ink3);
  margin-bottom: .4rem;
}
.form-group input,
.form-group textarea {
  width: 100%;
  padding: .8rem 0;
  background: transparent;
  border: none;
  border-bottom: 1px solid var(--border-dark);
  color: var(--ink);
  font-family: var(--sans);
  font-size: 1rem;
  font-weight: 500;
  transition: border-color .2s;
  outline: none;
}
.form-group input::placeholder,
.form-group textarea::placeholder { color: var(--ink3); font-weight: 400; }
.form-group input:focus,
.form-group textarea:focus { border-color: var(--red); }
.form-group textarea { min-height: 120px; resize: vertical; }

.form-submit {
  margin-top: 2rem;
}

/* ─────────────────────────────────────────────
   FOOTER
───────────────────────────────────────────── */
footer {
  background: var(--ink);
  padding: 2rem 3rem;
  display: flex; align-items: center; justify-content: space-between;
}
.footer-logo {
  font-family: var(--sans);
  font-weight: 800;
  font-size: .9rem;
  color: var(--cream);
  opacity: .5;
}
.footer-copy {
  font-family: var(--mono);
  font-size: .65rem;
  color: rgba(250,247,242,.35);
  letter-spacing: .1em;
}
.footer-admin {
  font-family: var(--mono);
  font-size: .65rem;
  color: rgba(250,247,242,.25);
  letter-spacing: .1em;
  transition: color .2s;
}
.footer-admin:hover { color: var(--red-light); }

/* ─────────────────────────────────────────────
   RESPONSIVE
───────────────────────────────────────────── */
@media (max-width: 768px) {
  nav { padding: 1rem 1.5rem; }
  .nav-links { gap: 1.5rem; }
  #hero { padding: 0 1.5rem; padding-top: 5rem; }
  .hero-content { grid-template-columns: 1fr; gap: 2rem; }
  .hero-right { align-items: flex-start; }
  .hero-bio { text-align: left; }
  .hero-bg-text { font-size: 35vw; }
  .section { padding: 5rem 1.5rem; }
  .about-grid, .contact-grid { grid-template-columns: 1fr; gap: 3rem; }
  #sobre { padding: 5rem 1.5rem; }
  .project-item { grid-template-columns: 2rem 1fr; }
  .project-meta { display: none; }
  footer { padding: 1.5rem; flex-direction: column; gap: 1rem; text-align: center; }
}

/* ─────────────────────────────────────────────
   ANIMATIONS
───────────────────────────────────────────── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(24px); }
  to   { opacity: 1; transform: translateY(0); }
}
.hero-eyebrow  { animation: fadeUp .6s ease both; animation-delay: .1s; }
.hero-name     { animation: fadeUp .6s ease both; animation-delay: .2s; }
.hero-role     { animation: fadeUp .6s ease both; animation-delay: .3s; }
.hero-ctas     { animation: fadeUp .6s ease both; animation-delay: .4s; }
.hero-right    { animation: fadeUp .6s ease both; animation-delay: .5s; }
.hero-bottom   { animation: fadeUp .6s ease both; animation-delay: .6s; }
</style>
</head>
<body>

<!-- Nav -->
<nav>
  <span class="nav-logo"><?= htmlspecialchars($about['name']) ?></span>
  <div class="nav-links">
    <a href="#projetos">Projetos</a>
    <a href="#sobre">Sobre</a>
    <a href="#contacto">Contacto</a>
  </div>
</nav>

<!-- Hero -->
<section id="hero">
  <div class="hero-bg-text" aria-hidden="true">PORTO</div>

  <div class="hero-content">
    <div class="hero-left">
      <div class="hero-eyebrow">Disponível para novos projetos</div>
      <h1 class="hero-name">
        <?php $nameParts = explode(' ', $about['name'], 2); ?>
        <?= htmlspecialchars($nameParts[0]) ?><br>
        <em><?= htmlspecialchars($nameParts[1] ?? '') ?></em>
      </h1>
      <p class="hero-role"><?= htmlspecialchars($about['role']) ?></p>
      <div class="hero-ctas">
        <a href="#projetos" class="btn-primary">
          Ver Projetos
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
        <a href="#contacto" class="btn-outline">Contactar</a>
      </div>
    </div>
    <div class="hero-right">
      <p class="hero-bio"><?= htmlspecialchars($about['bio']) ?></p>
      <div class="hero-socials">
        <?php if ($about['github']): ?>
          <a href="<?= htmlspecialchars($about['github']) ?>" target="_blank" rel="noopener">GitHub↗</a>
        <?php endif; ?>
        <?php if ($about['linkedin']): ?>
          <a href="<?= htmlspecialchars($about['linkedin']) ?>" target="_blank" rel="noopener">LinkedIn↗</a>
        <?php endif; ?>
        <?php if ($about['email']): ?>
          <a href="mailto:<?= htmlspecialchars($about['email']) ?>"><?= htmlspecialchars($about['email']) ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="hero-bottom">
    <span><?= date('Y') ?> · Portfolio</span>
    <div class="hero-scroll">
      <div class="scroll-line"></div>
      <span>scroll</span>
    </div>
    <span>PHP · MySQL</span>
  </div>
</section>

<!-- Projetos -->
<div class="section" id="projetos">
  <div class="section-header">
    <span class="section-num">01 — Trabalho</span>
    <h2 class="section-title">Projetos</h2>
  </div>

  <?php if ($projects): ?>
  <div class="projects-list">
    <?php foreach ($projects as $i => $p): ?>
    <div class="project-item" data-img="<?= htmlspecialchars($p['image_url'] ?? '') ?>">
      <span class="project-num"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
      <div class="project-info">
        <h3 class="project-title"><?= htmlspecialchars($p['title']) ?></h3>
        <p class="project-desc"><?= htmlspecialchars($p['description']) ?></p>
        <?php if ($p['image_url']): ?>
          <img src="<?= htmlspecialchars($p['image_url']) ?>"
               alt="<?= htmlspecialchars($p['title']) ?>"
               class="project-img-preview">
        <?php endif; ?>
      </div>
      <div class="project-meta">
        <div class="project-tech">
          <?php foreach (explode(',', $p['tech']) as $t): ?>
            <span class="tech-pill"><?= htmlspecialchars(trim($t)) ?></span>
          <?php endforeach; ?>
        </div>
        <?php if ($p['url']): ?>
          <a href="<?= htmlspecialchars($p['url']) ?>" target="_blank" rel="noopener"
             class="project-arrow">↗</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <p class="no-projects">Nenhum projeto ainda. Adiciona no painel admin.</p>
  <?php endif; ?>
</div>

<!-- Sobre -->
<section id="sobre">
  <div class="section" style="padding-top:0;padding-bottom:0">
    <div class="section-header">
      <span class="section-num">02 — Sobre</span>
      <h2 class="section-title" style="color:var(--cream)">Sobre Mim</h2>
    </div>
    <div class="about-grid">
      <div>
        <p class="about-big">
          Criando experiências<br><em>digitais</em> que<br>importam.
        </p>
        <p class="about-bio"><?= nl2br(htmlspecialchars($about['bio'])) ?></p>
      </div>
      <div class="about-details">
        <?php if ($about['email']): ?>
        <div class="detail-block">
          <div class="detail-icon">✉</div>
          <div class="detail-content">
            <div class="detail-label">Email</div>
            <div class="detail-value">
              <a href="mailto:<?= htmlspecialchars($about['email']) ?>"><?= htmlspecialchars($about['email']) ?></a>
            </div>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($about['github']): ?>
        <div class="detail-block">
          <div class="detail-icon">⌥</div>
          <div class="detail-content">
            <div class="detail-label">GitHub</div>
            <div class="detail-value">
              <a href="<?= htmlspecialchars($about['github']) ?>" target="_blank" rel="noopener">Ver perfil ↗</a>
            </div>
          </div>
        </div>
        <?php endif; ?>
        <?php if ($about['linkedin']): ?>
        <div class="detail-block">
          <div class="detail-icon">◈</div>
          <div class="detail-content">
            <div class="detail-label">LinkedIn</div>
            <div class="detail-value">
              <a href="<?= htmlspecialchars($about['linkedin']) ?>" target="_blank" rel="noopener">Ver perfil ↗</a>
            </div>
          </div>
        </div>
        <?php endif; ?>
        <div class="detail-block">
          <div class="detail-icon">◉</div>
          <div class="detail-content">
            <div class="detail-label">Cargo</div>
            <div class="detail-value"><?= htmlspecialchars($about['role']) ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Contacto -->
<section id="contacto">
  <div class="section">
    <div class="section-header">
      <span class="section-num">03 — Contacto</span>
      <h2 class="section-title">Fala Comigo</h2>
    </div>
    <div class="contact-grid">
      <div class="contact-left">
        <p class="contact-big">Vamos<br>trabalhar<br><em>juntos.</em></p>
        <p class="contact-sub">
          Tens um projeto em mente? Envia uma mensagem e respondo brevemente.
        </p>
      </div>
      <div class="contact-right">
        <?php if ($status === 'ok'): ?>
          <div class="alert alert-ok">✓ Mensagem enviada! Entrarei em contacto em breve.</div>
        <?php elseif ($status === 'error'): ?>
          <div class="alert alert-error">✕ <?= htmlspecialchars(urldecode($formMsg)) ?></div>
        <?php endif; ?>

        <form method="POST" action="actions/contact.php">
          <div class="form-group">
            <label>Nome</label>
            <input type="text" name="name" required placeholder="O teu nome">
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="teu@email.com">
          </div>
          <div class="form-group">
            <label>Mensagem</label>
            <textarea name="message" required placeholder="Descreve o teu projeto..."></textarea>
          </div>
          <div class="form-submit">
            <button type="submit" class="btn-primary" style="border:none;cursor:none">
              Enviar Mensagem
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer>
  <span class="footer-logo"><?= htmlspecialchars($about['name']) ?></span>
  <span class="footer-copy">© <?= date('Y') ?> · Todos os direitos reservados</span>
  <a href="admin/login.php" class="footer-admin">Admin ↗</a>
</footer>

<script>
// Scroll reveal
const obs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.style.opacity = '1';
      e.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: .15 });

document.querySelectorAll('.project-item, .detail-block, .contact-left, .contact-right, .about-grid > *').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = 'opacity .6s ease, transform .6s ease';
  obs.observe(el);
});
</script>
</body>
</html>
