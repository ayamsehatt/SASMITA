<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/pengaturan.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-head">
    <div>
        <span class="page-head__eyebrow">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.7"/>
                <path d="M19.4 13.4a7.8 7.8 0 0 0 0-2.8l1.8-1.3-2-3.4-2.1.9a7.7 7.7 0 0 0-2.4-1.4L14.4 3h-4l-.3 2.4a7.7 7.7 0 0 0-2.4 1.4l-2.1-.9-2 3.4 1.8 1.3a7.8 7.8 0 0 0 0 2.8l-1.8 1.3 2 3.4 2.1-.9a7.7 7.7 0 0 0 2.4 1.4l.3 2.4h4l.3-2.4a7.7 7.7 0 0 0 2.4-1.4l2.1.9 2-3.4-1.8-1.3z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            </svg>
            Profil guru
        </span>
        <h1>Pengaturan</h1>
        <p>Kelola identitas tampilan Anda di SASMITA.</p>
    </div>
</div>

<div class="settings-grid">
    <section class="card settings-card">
        <div class="card__head">
            <div>
                <h2>Profil tampilan</h2>
                <p>Ubah nama yang ditampilkan di ruang kerja</p>
            </div>
        </div>

        <div class="card__body">
            <div class="profile-hero">
                <span class="profile-hero__avatar" aria-hidden="true" data-profile-avatar><?= esc($initial) ?></span>
                <div class="profile-hero__meta">
                    <strong data-profile-preview><?= esc($nama) ?></strong>
                    <span><?= esc($role !== '' ? $role : 'Guru') ?> · Akun aktif</span>
                </div>
            </div>

            <form action="<?= site_url('pengaturan') ?>" method="post" class="settings-form" novalidate>
                <?= csrf_field() ?>

                <div class="field">
                    <label for="nama_tampilan">Nama tampilan <span class="req">*</span></label>
                    <input type="text"
                           id="nama_tampilan"
                           name="nama_tampilan"
                           value="<?= esc(old('nama_tampilan', $nama), 'attr') ?>"
                           minlength="2"
                           maxlength="80"
                           autocomplete="name"
                           required
                           data-profile-input>
                    <span class="field__hint">2–80 karakter. Nama ini akan menggantikan nama demo pada tampilan aplikasi.</span>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="text"
                           id="role"
                           value="<?= esc($email, 'attr') ?>"
                           readonly
                           class="input-readonly">
                    <span class="field__hint">Email mengikuti akun demo dan tidak dapat diubah dari halaman ini.</span>
                </div>

                <div class="field">
                    <label for="role">Peran</label>
                    <input type="text"
                           id="role"
                           value="<?= esc($role !== '' ? $role : 'Guru', 'attr') ?>"
                           readonly
                           class="input-readonly">
                    <span class="field__hint">Peran ditentukan oleh konfigurasi akun.</span>
                </div>

                <div class="settings-form__actions">
                    <button type="submit" class="btn btn--primary">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M5 12.5l4.2 4.2L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Simpan perubahan
                    </button>
                </div>
            </form>
        </div>
    </section>

    <aside class="settings-side">
        <section class="card settings-card">
            <div class="card__head">
                <div>
                    <h2>Akun demo</h2>
                    <p>Informasi akun yang sedang masuk</p>
                </div>
            </div>
            <div class="card__body">
                <dl class="settings-meta">
                    <div>
                        <dt>Email</dt>
                        <dd><?= esc($email !== '' ? $email : '-') ?></dd>
                    </div>
                    <div>
                        <dt>Peran</dt>
                        <dd><?= esc($role !== '' ? $role : 'Guru') ?></dd>
                    </div>
                    <div>
                        <dt>Status</dt>
                        <dd><span class="pill pill--ready"><span class="dot"></span>Aktif</span></dd>
                    </div>
                    <div>
                        <dt>Penyimpanan</dt>
                        <dd>Tabel <code>pengaturan</code> di database</dd>
                    </div>
                </dl>

                <div class="settings-side__actions">
                    <a href="<?= site_url('logout') ?>" class="btn btn--ghost btn--sm">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M14 5.5H7.5a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2H14M17 8.5l3.5 3.5L17 15.5M10.5 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Keluar
                    </a>
                </div>
            </div>
        </section>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        var input = document.querySelector('[data-profile-input]');
        var preview = document.querySelector('[data-profile-preview]');
        var avatar = document.querySelector('[data-profile-avatar]');

        if (!input) {
            return;
        }

        function firstChar(value) {
            var text = (value || '').trim();
            return text ? text.charAt(0).toUpperCase() : 'G';
        }

        input.addEventListener('input', function () {
            var value = input.value || '';
            if (preview) {
                preview.textContent = value.trim() || 'Guru';
            }
            if (avatar) {
                avatar.textContent = firstChar(value);
            }
        });
    })();
</script>
<?= $this->endSection() ?>
