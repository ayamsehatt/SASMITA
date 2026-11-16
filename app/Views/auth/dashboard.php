<?php
/**
 * Halaman dasbor sementara setelah login.
 *
 * @var array{email: string, name: string, role: string} $user
 */
$initial = strtoupper(substr($user['name'] ?? 'S', 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Dasbor · SASMITA') ?></title>

    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body class="auth-page">

<div class="form-panel" style="min-height:100vh;min-height:100dvh;justify-content:center">

    <a class="mobile-brand" href="<?= site_url('dashboard') ?>" style="display:flex;max-width:760px">
        <img src="<?= base_url('assets/img/logo-sasmita.svg') ?>" alt="">
        <span>
            <span class="mobile-brand__name">SASMITA</span>
            <span class="mobile-brand__sub">Asisten Guru Berbasis AI</span>
        </span>
    </a>

    <div class="dash">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert--success" role="status" style="margin:0">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M8 12.3l2.6 2.6L16.2 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p><?= esc(session()->getFlashdata('success')) ?></p>
            </div>
        <?php endif; ?>

        <header class="dash__head">
            <div class="dash__user">
                <span class="dash__avatar" aria-hidden="true"><?= esc($initial) ?></span>
                <div>
                    <strong><?= esc($user['name']) ?></strong>
                    <span><?= esc($user['role']) ?> · <?= esc($user['email']) ?></span>
                </div>
            </div>

            <a class="btn-ghost" href="<?= site_url('logout') ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M14 5.5H7.5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2H14M17 8.5l3.5 3.5L17 15.5M10.5 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Keluar
            </a>
        </header>

        <section class="dash__hero">
            <h1>Selamat datang, <?= esc($user['name']) ?> 👋</h1>
            <p>
                Anda berhasil masuk ke SASMITA. Halaman dasbor ini masih berupa
                placeholder — silakan kembangkan menu modul ajar, asesmen, dan
                laporan belajar sesuai kebutuhan aplikasi Anda.
            </p>
        </section>
    </div>
</div>

</body>
</html>
