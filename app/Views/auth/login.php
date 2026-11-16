<?php
/**
 * Halaman login SASMITA.
 *
 * Variabel yang tersedia:
 * - $rememberedEmail (string) email dari cookie "ingat saya"
 *
 * @var string $rememberedEmail
 */
$errors          = session('errors') ?? [];
$rememberedEmail = $rememberedEmail ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Masuk ke akun SASMITA, asisten guru berbasis AI.">
    <title><?= esc($title ?? 'Masuk · SASMITA') ?></title>

    <link rel="icon" href="<?= base_url('favicon.ico') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body class="auth-page">

<div class="auth-shell">

    <!-- Panel brand -->
    <aside class="brand-panel" aria-label="Tentang SASMITA">
        <span class="brand-panel__blob brand-panel__blob--one"></span>
        <span class="brand-panel__blob brand-panel__blob--two"></span>
        <span class="brand-panel__blob brand-panel__blob--three"></span>

        <div class="brand-panel__inner">
            <a class="brand-mark" href="<?= site_url('/') ?>">
                <img src="<?= base_url('assets/img/logo-sasmita.svg') ?>" alt="">
                <span class="brand-mark__text">
                    <span class="brand-mark__name">SASMITA</span>
                    <span class="brand-mark__sub">Asisten Guru Berbasis AI</span>
                </span>
            </a>

            <div class="brand-copy">
                <span class="brand-copy__eyebrow">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
                    </svg>
                    Ruang kerja guru cerdas
                </span>

                <h1>Mengajar lebih ringan,<br>berkat <span>bantuan AI.</span></h1>

                <p>
                    SASMITA membantu Anda menyusun modul ajar, membuat asesmen,
                    dan menganalisis perkembangan siswa dalam satu tempat.
                </p>

                <ul class="feature-list">
                    <li>
                        <span class="tick">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Susun modul ajar dan bahan belajar secara otomatis.
                    </li>
                    <li>
                        <span class="tick">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Buat asesmen adaptif sesuai kebutuhan kelas.
                    </li>
                    <li>
                        <span class="tick">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Pantau hasil belajar dengan analisis yang mudah dibaca.
                    </li>
                </ul>
            </div>

            <p class="brand-panel__foot">© <?= date('Y') ?> SASMITA · Asisten Guru Berbasis AI</p>
        </div>
    </aside>

    <!-- Panel formulir -->
    <main class="form-panel">

        <a class="mobile-brand" href="<?= site_url('/') ?>">
            <img src="<?= base_url('assets/img/logo-sasmita.svg') ?>" alt="">
            <span>
                <span class="mobile-brand__name">SASMITA</span>
                <span class="mobile-brand__sub">Asisten Guru Berbasis AI</span>
            </span>
        </a>

        <section class="login-card" aria-labelledby="login-title">

            <header class="login-card__head">
                <span class="eyebrow-pill">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
                    </svg>
                    Selamat datang
                </span>
                <h1 id="login-title">Masuk ke akun Anda</h1>
                <p>Lanjutkan pekerjaan mengajar Anda di SASMITA.</p>
            </header>

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

            <?php if ($errors !== []) : ?>
                <div class="alert alert--error" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M12 8v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <circle cx="12" cy="16.4" r="1" fill="currentColor"/>
                    </svg>
                    <div>
                        <p>Periksa kembali data Anda:</p>
                        <ul>
                            <?php foreach ($errors as $message) : ?>
                                <li><?= esc($message) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('login') ?>" method="post" class="login-form" novalidate>

                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap<?= isset($errors['email']) ? ' is-invalid' : '' ?>">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3.2" y="5.4" width="17.6" height="13.2" rx="3" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M4.4 7.4l6.4 4.9a2 2 0 0 0 2.4 0l6.4-4.9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= esc(old('email', $rememberedEmail)) ?>"
                            placeholder="nama@sasmita.id"
                            autocomplete="email"
                            inputmode="email"
                            required
                            autofocus
                        >
                    </div>
                    <?php if (isset($errors['email'])) : ?>
                        <p class="field-error"><?= esc($errors['email']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="field">
                    <label for="password">Kata sandi</label>
                    <div class="input-wrap input-wrap--password<?= isset($errors['password']) ? ' is-invalid' : '' ?>">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="4.5" y="10" width="15" height="10" rx="3" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8 10V8a4 4 0 0 1 8 0v2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            <circle cx="12" cy="15" r="1.4" fill="currentColor"/>
                        </svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan kata sandi"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-password" data-toggle-password aria-label="Tampilkan kata sandi" aria-pressed="false">
                            <svg class="icon-eye" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M2.8 12S6.2 5.8 12 5.8 21.2 12 21.2 12 17.8 18.2 12 18.2 2.8 12 2.8 12z" stroke="currentColor" stroke-width="1.7"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                            </svg>
                            <svg class="icon-eye-off" viewBox="0 0 24 24" fill="none" aria-hidden="true" hidden>
                                <path d="M4 4l16 16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M9.9 5.3A9.7 9.7 0 0 1 12 5.1c5.8 0 9.2 6.2 9.2 6.2a16 16 0 0 1-3 3.8M6.2 7.4A16 16 0 0 0 2.8 11.3S6.2 17.5 12 17.5c1.2 0 2.3-.2 3.3-.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M10.1 10.2a2.8 2.8 0 0 0 3.8 3.9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])) : ?>
                        <p class="field-error"><?= esc($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <label class="checkbox" for="remember">
                        <input type="checkbox" id="remember" name="remember" value="1" <?= old('remember', $rememberedEmail !== '') ? 'checked' : '' ?>>
                        <span>Ingat saya</span>
                    </label>
                    <a class="link" href="#" data-forgot-password>Lupa kata sandi?</a>
                </div>

                <button type="submit" class="btn-primary">
                    Masuk
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4.5 12h14M13 6.5l5.5 5.5L13 17.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </form>

            <div class="demo-box">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="8.5" cy="8.5" r="3.2" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M2.8 19.4a5.7 5.7 0 0 1 11.4 0M15.4 5.6a3.2 3.2 0 0 1 0 5.8M17.6 14.4a5.7 5.7 0 0 1 3.6 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                <div>
                    <strong>Akun demo</strong>
                    <span>guru@sasmita.id · sasmita123</span>
                </div>
            </div>

            <p class="signup-note">
                Belum punya akun? <a href="#">Hubungi admin sekolah</a>
            </p>
        </section>

        <p class="form-foot">© <?= date('Y') ?> SASMITA. Seluruh hak cipta dilindungi.</p>
    </main>
</div>

<script>
    (function () {
        var toggle = document.querySelector('[data-toggle-password]');
        var password = document.getElementById('password');

        if (toggle && password) {
            toggle.addEventListener('click', function () {
                var isHidden = password.type === 'password';
                password.type = isHidden ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', String(isHidden));
                toggle.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
                toggle.querySelector('.icon-eye').hidden = isHidden;
                toggle.querySelector('.icon-eye-off').hidden = ! isHidden;
                password.focus({ preventScroll: true });
            });
        }

        var forgot = document.querySelector('[data-forgot-password]');
        if (forgot) {
            forgot.addEventListener('click', function (event) {
                event.preventDefault();
                window.alert('Silakan hubungi admin sekolah untuk mengatur ulang kata sandi Anda.');
            });
        }
    })();
</script>

</body>
</html>
