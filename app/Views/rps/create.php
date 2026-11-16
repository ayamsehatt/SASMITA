<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<a class="back-link" href="<?= site_url('rps') ?>">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M19.5 12h-14M11 5.5L4.5 12l6.5 6.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Kembali ke daftar RPS
</a>

<div class="page-head">
    <div>
        <span class="page-head__eyebrow">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
            </svg>
            Buat dengan AI
        </span>
        <h1>RPS &amp; materi baru</h1>
        <p>Isi informasi di bawah ini. Rencana 16 pertemuan, materi ajar, dan instrumen penilaian akan disusun otomatis.</p>
    </div>
</div>

<?php $errors = session('errors') ?? []; ?>

<div class="create-layout">
    <section class="card form-card">
        <div class="card__head">
            <div>
                <h2>Informasi pembelajaran</h2>
                <p>Kolom bertanda wajib harus diisi sebelum generate.</p>
            </div>
            <button type="button" class="btn btn--ghost btn--sm" data-fill-sample>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
                </svg>
                Gunakan contoh
            </button>
        </div>

        <div class="card__body">
            <form action="<?= site_url('rps') ?>" method="post" enctype="multipart/form-data" data-rps-form>

                <div class="form-section">
                    <div class="form-section__head">
                        <span class="step">1</span>
                        <div>
                            <h2>Materi yang diajarkan</h2>
                            <p>Tentukan topik utama dan metode yang biasa Anda gunakan.</p>
                        </div>
                    </div>

                    <div class="field">
                        <label for="title">Judul materi <span class="optional">Wajib</span></label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="<?= esc(old('title')) ?>"
                            placeholder="Contoh: Berpikir Kritis dalam Pembelajaran IPS"
                            maxlength="180"
                            class="<?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                            required
                        >
                        <?php if (isset($errors['title'])) : ?>
                            <p class="error-text"><?= esc($errors['title']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="method">Metode pembelajaran <span class="optional">Opsional</span></label>
                        <input
                            type="text"
                            id="method"
                            name="method"
                            value="<?= esc(old('method')) ?>"
                            placeholder="Contoh: Problem Based Learning, diskusi kelompok"
                            maxlength="140"
                            class="<?= isset($errors['method']) ? 'is-invalid' : '' ?>"
                        >
                        <p class="hint">Jika dikosongkan, SASMITA akan memilih metode yang sesuai secara otomatis.</p>
                        <?php if (isset($errors['method'])) : ?>
                            <p class="error-text"><?= esc($errors['method']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section__head">
                        <span class="step">2</span>
                        <div>
                            <h2>Tujuan &amp; penilaian</h2>
                            <p>Tuliskan satu poin per baris agar mudah dibaca pada hasil RPS.</p>
                        </div>
                    </div>

                    <div class="field">
                        <label for="objective">Tujuan pembelajaran <span class="optional">Wajib</span></label>
                        <textarea
                            id="objective"
                            name="objective"
                            rows="4"
                            placeholder="Peserta didik mampu menganalisis permasalahan sosial di lingkungan sekitar.&#10;Peserta didik mampu menyusun argumen yang didukung bukti."
                            class="<?= isset($errors['objective']) ? 'is-invalid' : '' ?>"
                            required
                        ><?= esc(old('objective')) ?></textarea>
                        <p class="hint">Minimal 20 karakter. Gunakan baris baru untuk memisahkan tujuan.</p>
                        <?php if (isset($errors['objective'])) : ?>
                            <p class="error-text"><?= esc($errors['objective']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label for="indicators">Indikator penilaian <span class="optional">Wajib</span></label>
                        <textarea
                            id="indicators"
                            name="indicators"
                            rows="4"
                            placeholder="Ketepatan analisis masalah&#10;Kualitas argumen dan bukti&#10;Kemampuan presentasi kelompok"
                            class="<?= isset($errors['indicators']) ? 'is-invalid' : '' ?>"
                            required
                        ><?= esc(old('indicators')) ?></textarea>
                        <p class="hint">Indikator ini akan dipetakan ke teknik dan instrumen penilaian.</p>
                        <?php if (isset($errors['indicators'])) : ?>
                            <p class="error-text"><?= esc($errors['indicators']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section__head">
                        <span class="step">3</span>
                        <div>
                            <h2>Sumber relevan</h2>
                            <p>Unggah dokumen pendukung agar referensi RPS lebih kontekstual.</p>
                        </div>
                    </div>

                    <div class="field">
                        <label for="source">Berkas sumber <span class="optional">Opsional</span></label>

                        <label class="dropzone" for="source" data-dropzone>
                            <input
                                type="file"
                                id="source"
                                name="source"
                                accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.md"
                                data-file-input
                            >
                            <span class="dropzone__icon">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 16.5V4.5M7.5 9L12 4.5 16.5 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M4.5 15v3A2.5 2.5 0 0 0 7 20.5h10a2.5 2.5 0 0 0 2.5-2.5v-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <strong>Klik untuk mengunggah atau seret berkas ke sini</strong>
                            <small>PDF, DOC, DOCX, PPT, PPTX, TXT, atau MD · maksimal 10 MB</small>
                            <span class="dropzone__file" data-file-name>
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M7 3.5h7L19 8.5v12H7a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <path d="M13.5 4v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                </svg>
                                <span data-file-label></span>
                            </span>
                        </label>

                        <?php if (isset($errors['source'])) : ?>
                            <p class="error-text"><?= esc($errors['source']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <span class="form-actions__note">Data ini disimpan sebagai demo pada sesi browser Anda.</span>
                    <button type="submit" class="btn btn--primary" data-submit>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true" data-submit-icon>
                            <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
                        </svg>
                        <span data-submit-label>Generate RPS dengan AI</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <aside class="side-panel">
        <section class="ai-panel">
            <div class="ai-panel__head">
                <span class="ai-panel__icon">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
                    </svg>
                </span>
                <h3>Yang akan dihasilkan</h3>
            </div>
            <p>Rencana lengkap siap ditinjau, disunting, lalu digunakan di kelas.</p>

            <ul class="ai-list">
                <li>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Alur 16 pertemuan beserta topik tiap minggu.
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Capaian pembelajaran dan tujuan yang selaras.
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Ringkasan materi ajar per pertemuan.
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Teknik dan instrumen penilaian yang sesuai.
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 12.5l4.5 4.5L19 7.5" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Daftar referensi termasuk sumber unggahan Anda.
                </li>
            </ul>
        </section>

        <section class="tip-card">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M9.8 18.5h4.4M10.5 21h3M12 3a6.5 6.5 0 0 0-3.8 11.8c.6.5 1 1.2 1 1.9v.3h5.6v-.3c0-.7.4-1.4 1-1.9A6.5 6.5 0 0 0 12 3z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Tips menulis tujuan
            </h3>
            <p>Gunakan kata kerja terukur seperti “menganalisis”, “merancang”, atau “menyimpulkan” agar indikator penilaian lebih mudah disusun.</p>
        </section>
    </aside>
</div>

<div class="generating-overlay" data-generating hidden>
    <div class="generating-card">
        <div class="spinner" aria-hidden="true"></div>
        <h3>Menyusun rencana pembelajaran…</h3>
        <p>SASMITA sedang memetakan tujuan, indikator, dan 16 pertemuan untuk Anda.</p>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        var input = document.querySelector('[data-file-input]');
        var dropzone = document.querySelector('[data-dropzone]');
        var fileName = document.querySelector('[data-file-name]');
        var fileLabel = document.querySelector('[data-file-label]');
        var form = document.querySelector('[data-rps-form]');
        var overlay = document.querySelector('[data-generating]');
        var submit = document.querySelector('[data-submit]');
        var submitLabel = document.querySelector('[data-submit-label]');

        if (input) {
            input.addEventListener('change', function () {
                if (! input.files.length) {
                    return;
                }
                if (fileLabel) {
                    fileLabel.textContent = input.files[0].name;
                }
                if (fileName) {
                    fileName.classList.add('is-visible');
                }
            });
        }

        if (dropzone) {
            ['dragenter', 'dragover'].forEach(function (eventName) {
                dropzone.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    dropzone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach(function (eventName) {
                dropzone.addEventListener(eventName, function (event) {
                    dropzone.classList.remove('is-dragover');
                });
            });
        }

        if (form) {
            form.addEventListener('submit', function () {
                if (overlay) {
                    overlay.hidden = false;
                    overlay.classList.add('is-visible');
                }
                if (submit) {
                    submit.disabled = true;
                    submit.classList.add('is-loading');
                }
                if (submitLabel) {
                    submitLabel.textContent = 'Menyusun RPS…';
                }
            });
        }

        var sample = document.querySelector('[data-fill-sample]');
        if (sample) {
            sample.addEventListener('click', function () {
                document.getElementById('title').value = 'Berpikir Kritis dalam Pembelajaran IPS';
                document.getElementById('method').value = 'Problem Based Learning';
                document.getElementById('objective').value = [
                    'Peserta didik mampu menganalisis permasalahan sosial di lingkungan sekitar.',
                    'Peserta didik mampu menyusun argumen yang didukung bukti.',
                    'Peserta didik mampu menyampaikan hasil analisis secara santun.'
                ].join('\n');
                document.getElementById('indicators').value = [
                    'Ketepatan analisis masalah',
                    'Kualitas argumen dan bukti',
                    'Kemampuan presentasi kelompok'
                ].join('\n');
                document.getElementById('title').focus();
            });
        }

        // Kembalikan tombol ke keadaan normal jika halaman dipulihkan
        // dari bfcache setelah pengiriman formulir.
        window.addEventListener('pageshow', function () {
            if (overlay) {
                overlay.classList.remove('is-visible');
                overlay.hidden = true;
            }
            if (submit) {
                submit.disabled = false;
                submit.classList.remove('is-loading');
            }
            if (submitLabel) {
                submitLabel.textContent = 'Generate RPS dengan AI';
            }
        });
    })();
</script>
<?= $this->endSection() ?>
