<?php
/**
 * Layout utama ruang kerja SASMITA.
 *
 * @var string $title
 * @var string $active
 */
$user    = session('user') ?? ['name' => 'Guru', 'role' => 'Guru', 'email' => ''];
$initial = strtoupper(substr($user['name'] ?? 'G', 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Ruang kerja guru SASMITA — susun RPS dan materi dengan bantuan AI.">
    <title><?= esc($title ?? 'SASMITA') ?></title>

    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body class="app-body">

<div class="app-shell" data-app-shell>

    <aside class="app-sidebar" data-sidebar>
        <a class="side-brand" href="<?= site_url('rps') ?>">
            <img src="<?= base_url('assets/img/logo-sasmita.svg') ?>" alt="">
            <span>
                <span class="side-brand__name">SASMITA</span>
                <span class="side-brand__sub">Asisten Guru Berbasis AI</span>
            </span>
        </a>

        <nav class="side-nav" aria-label="Menu utama">
            <p class="side-label">Ruang kerja</p>

            <a class="side-link<?= ($active ?? '') === 'rps' ? ' is-active' : '' ?>" href="<?= site_url('rps') ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4.5 5.8A2.3 2.3 0 0 1 6.8 3.5h4.7v17H6.8a2.3 2.3 0 0 0-2.3 2.3V5.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M19.5 5.8a2.3 2.3 0 0 0-2.3-2.3h-4.7v17h4.7a2.3 2.3 0 0 1 2.3 2.3V5.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                </svg>
                <span>RPS &amp; Materi</span>
            </a>

            <a class="side-link<?= ($active ?? '') === 'asesmen' ? ' is-active' : '' ?>" href="<?= site_url('asesmen') ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7 4.8h7.5L19 9.3v9.9a1.8 1.8 0 0 1-1.8 1.8H7a1.8 1.8 0 0 1-1.8-1.8V6.6A1.8 1.8 0 0 1 7 4.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M14 5v4.5h4.5M9 13h6M9 16.5h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                <span>Asesmen</span>
            </a>

            <a class="side-link<?= ($active ?? '') === 'analitik' ? ' is-active' : '' ?>" href="<?= site_url('analitik') ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4.5 19.5V13M9.8 19.5V7.5M15.2 19.5v-4.5M20.5 19.5V4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                <span>Analitik</span>
            </a>

            <p class="side-label side-label--spaced">Akun</p>

            <a class="side-link<?= ($active ?? '') === 'pengaturan' ? ' is-active' : '' ?>" href="<?= site_url('pengaturan') ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M19.4 13.4a7.8 7.8 0 0 0 0-2.8l1.8-1.3-2-3.4-2.1.9a7.7 7.7 0 0 0-2.4-1.4L14.4 3h-4l-.3 2.4a7.7 7.7 0 0 0-2.4 1.4l-2.1-.9-2 3.4 1.8 1.3a7.8 7.8 0 0 0 0 2.8l-1.8 1.3 2 3.4 2.1-.9a7.7 7.7 0 0 0 2.4 1.4l.3 2.4h4l.3-2.4a7.7 7.7 0 0 0 2.4-1.4l2.1.9 2-3.4-1.8-1.3z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                </svg>
                <span>Pengaturan</span>
            </a>
        </nav>

        <div class="side-user">
            <span class="side-user__avatar" aria-hidden="true"><?= esc($initial) ?></span>
            <span class="side-user__meta">
                <strong><?= esc($user['name']) ?></strong>
                <span><?= esc($user['role']) ?></span>
            </span>
            <a class="side-user__logout" href="<?= site_url('logout') ?>" aria-label="Keluar" title="Keluar">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M14 5.5H7.5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2H14M17 8.5l3.5 3.5L17 15.5M10.5 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </aside>

    <div class="app-backdrop" data-nav-backdrop hidden></div>

    <div class="app-main">
        <header class="app-topbar">
            <button type="button" class="nav-toggle" data-nav-toggle aria-label="Buka menu" aria-expanded="false">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                </svg>
            </button>

            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="<?= site_url('rps') ?>">SASMITA</a>
                <span aria-hidden="true">/</span>
                <span><?= esc($crumb ?? 'RPS & Materi') ?></span>
            </nav>

            <div class="topbar-right">
                <span class="demo-pill"><span class="dot"></span> Mode demo</span>
                <span class="topbar-avatar" aria-hidden="true"><?= esc($initial) ?></span>
            </div>
        </header>

        <main class="app-content">
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert--error" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 8v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <circle cx="12" cy="16.4" r="1" fill="currentColor"/>
                    </svg>
                    <p><?= esc(session()->getFlashdata('error')) ?></p>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert--success" role="status">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M8 12.3l2.6 2.6L16.2 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p><?= esc(session()->getFlashdata('success')) ?></p>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>

<script>
    (function () {
        var shell = document.querySelector('[data-app-shell]');
        var toggle = document.querySelector('[data-nav-toggle]');
        var backdrop = document.querySelector('[data-nav-backdrop]');

        function setNav(open) {
            if (!shell) {
                return;
            }
            shell.classList.toggle('nav-open', open);
            if (toggle) {
                toggle.setAttribute('aria-expanded', String(open));
            }
            if (backdrop) {
                backdrop.hidden = !open;
            }
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                setNav(!shell.classList.contains('nav-open'));
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', function () {
                setNav(false);
            });
        }

        document.querySelectorAll('[data-coming-soon]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                window.alert('Menu ini akan tersedia pada pengembangan berikutnya.');
            });
        });
    })();
</script>

<?= $this->renderSection('scripts') ?>

</body>
</html>
