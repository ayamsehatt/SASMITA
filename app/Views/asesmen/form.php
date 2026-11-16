<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/asesmen.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];

$oldQuestions = old('questions', [], 'raw');
if (! is_array($oldQuestions)) {
    $oldQuestions = [];
}

$oldData = [
    'title'        => (string) old('title', '', 'raw'),
    'subject'      => (string) old('subject', '', 'raw'),
    'class'        => (string) old('class', '', 'raw'),
    'teacher'      => (string) old('teacher', $teacherName, 'raw'),
    'time'         => (string) old('time', '', 'raw'),
    'instructions' => (string) old('instructions', '', 'raw'),
    'questions'    => $oldQuestions,
];

$hasOld = session('_ci_old_input') !== null;
?>

<div class="back-link-row">
    <a class="back-link" href="<?= site_url('asesmen') ?>">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M19.5 12h-14M10.5 6.5L5 12l5.5 5.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Kembali ke daftar draft
    </a>
</div>

<div class="page-head asesmen-head">
    <div>
        <span class="page-head__eyebrow">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 4.8h7.5L19 9.3v9.9a1.8 1.8 0 0 1-1.8 1.8H7a1.8 1.8 0 0 1-1.8-1.8V6.6A1.8 1.8 0 0 1 7 4.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M14 5v4.5h4.5M9 13h6M9 16.5h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            Asesmen pembelajaran
        </span>
        <h1><?= $isNew ? 'Draft asesmen baru' : 'Edit draft asesmen' ?></h1>
        <p>
            Susun identitas dan soal asesmen Anda. Perubahan hanya tersimpan di browser ini
            setelah tombol <strong>Simpan</strong> ditekan.
        </p>
    </div>

    <div class="asesmen-head__actions">
        <button type="button" class="btn btn--ghost" data-preview>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M2.8 12S6.4 6 12 6s9.2 6 9.2 6-3.6 6-9.2 6-9.2-6-9.2-6z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="2.6" stroke="currentColor" stroke-width="1.7"/>
            </svg>
            Pratinjau
        </button>

        <button type="button" class="btn btn--primary" data-save>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7.2 4.5h8.1L19 8.2v11.3a1.8 1.8 0 0 1-1.8 1.8H7a1.8 1.8 0 0 1-1.8-1.8V6.3A1.8 1.8 0 0 1 7 4.5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                <path d="M8.5 4.8v5h7v-5M8.5 15.5h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            Simpan
        </button>

        <button type="submit" form="asesmen-form" class="btn btn--ghost" data-download>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 4.5v10M7.5 10L12 14.5 16.5 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M4.5 16v2A2.5 2.5 0 0 0 7 20.5h10a2.5 2.5 0 0 0 2.5-2.5v-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <span data-download-label>Unduh PDF</span>
        </button>
    </div>
</div>

<div class="alert alert--error" role="alert" data-missing hidden>
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
        <path d="M12 8v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        <circle cx="12" cy="16.4" r="1" fill="currentColor"/>
    </svg>
    <div>
        <p><strong>Draft tidak ditemukan.</strong> Draft ini mungkin sudah dihapus dari browser ini.</p>
        <p><a href="<?= site_url('asesmen') ?>">Kembali ke daftar draft</a></p>
    </div>
</div>

<?php if ($errors !== []) : ?>
    <div class="alert alert--error" role="alert" data-errors>
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 8v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="16.4" r="1" fill="currentColor"/>
        </svg>
        <div>
            <p><strong>PDF belum bisa dibuat.</strong> Periksa isian berikut:</p>
            <ul>
                <?php foreach ($errors as $error) : ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endif; ?>

<div class="builder-layout">
    <div class="builder-main">
        <form
            id="asesmen-form"
            action="<?= site_url('asesmen/pdf') ?>"
            method="post"
            data-asesmen-form
            data-draft-id="<?= esc($draftId, 'attr') ?>"
            data-is-new="<?= $isNew ? '1' : '0' ?>"
            data-list-url="<?= esc(site_url('asesmen'), 'attr') ?>"
        >
            <input type="hidden" name="draft_id" value="<?= esc($draftId, 'attr') ?>" data-draft-field>

            <section class="panel">
                <div class="panel__head">
                    <div>
                        <h2>Identitas asesmen</h2>
                        <p>Informasi ini tampil di bagian atas PDF.</p>
                    </div>
                    <span class="panel__tag">Langkah 1</span>
                </div>

                <div class="panel__body">
                    <div class="identity-grid">
                        <div class="field identity-grid__wide">
                            <label for="asesmen-title">Judul asesmen <span class="optional">Wajib</span></label>
                            <input
                                type="text"
                                id="asesmen-title"
                                name="title"
                                value="<?= esc($oldData['title']) ?>"
                                maxlength="150"
                                placeholder="Contoh: Asesmen Sumatif Bab 3 — Sistem Pencernaan"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="asesmen-subject">Mata pelajaran <span class="optional">Wajib</span></label>
                            <input
                                type="text"
                                id="asesmen-subject"
                                name="subject"
                                value="<?= esc($oldData['subject']) ?>"
                                maxlength="80"
                                placeholder="Contoh: Ilmu Pengetahuan Alam"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="asesmen-class">Kelas <span class="optional">Wajib</span></label>
                            <input
                                type="text"
                                id="asesmen-class"
                                name="class"
                                value="<?= esc($oldData['class']) ?>"
                                maxlength="40"
                                placeholder="Contoh: VII A"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="asesmen-teacher">Nama guru <span class="optional">Wajib</span></label>
                            <input
                                type="text"
                                id="asesmen-teacher"
                                name="teacher"
                                value="<?= esc($oldData['teacher']) ?>"
                                maxlength="100"
                                placeholder="Nama Anda"
                                required
                            >
                        </div>

                        <div class="field">
                            <label for="asesmen-time">Alokasi waktu <span class="optional">Opsional</span></label>
                            <input
                                type="text"
                                id="asesmen-time"
                                name="time"
                                value="<?= esc($oldData['time']) ?>"
                                maxlength="60"
                                placeholder="Contoh: 60 menit"
                            >
                        </div>
                    </div>

                    <div class="field">
                        <label for="asesmen-instructions">Petunjuk pengerjaan <span class="optional">Opsional</span></label>
                        <textarea
                            id="asesmen-instructions"
                            name="instructions"
                            rows="3"
                            maxlength="2000"
                            placeholder="Contoh: Bacalah setiap soal dengan teliti. Pilih satu jawaban yang paling tepat."
                        ><?= esc($oldData['instructions']) ?></textarea>
                        <p class="hint">Petunjuk akan dicetak tepat di bawah identitas asesmen.</p>
                    </div>
                </div>
            </section>

            <section class="panel panel--questions">
                <div class="panel__head">
                    <div>
                        <h2>Daftar soal</h2>
                        <p>Tambahkan soal, pilih jenisnya, lalu tentukan kunci jawaban.</p>
                    </div>
                    <span class="count-chip"><strong data-count>0</strong> soal</span>
                </div>

                <div class="panel__body">
                    <div class="q-list" data-questions></div>

                    <div class="empty-state" data-empty hidden>
                        <span class="empty-state__icon">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 4.8h7.5L19 9.3v9.9a1.8 1.8 0 0 1-1.8 1.8H7a1.8 1.8 0 0 1-1.8-1.8V6.6A1.8 1.8 0 0 1 7 4.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M14 5v4.5h4.5M9 13h6M9 16.5h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <h3>Belum ada soal</h3>
                        <p>Tambahkan soal pertama Anda. Tidak ada soal contoh — semuanya Anda tulis sendiri.</p>
                        <button type="button" class="btn btn--primary btn--sm" data-add-type="pg">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
                            </svg>
                            Tambah soal
                        </button>
                    </div>

                    <div class="q-add-row">
                        <button type="button" class="btn btn--soft" data-add-type="pg">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
                            </svg>
                            Tambah pilihan ganda
                        </button>
                        <button type="button" class="btn btn--ghost" data-add-type="esai">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
                            </svg>
                            Tambah esai
                        </button>
                    </div>
                </div>
            </section>

            <div class="builder-actions">
                <button type="button" class="btn btn--ghost btn--sm" data-reset>
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4.5 12a7.5 7.5 0 1 0 2.3-5.4M4.5 4.5v4h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Bersihkan isian
                </button>

                <div class="builder-actions__right">
                    <button type="button" class="btn btn--ghost" data-preview>Pratinjau</button>
                    <button type="button" class="btn btn--primary" data-save>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M7.2 4.5h8.1L19 8.2v11.3a1.8 1.8 0 0 1-1.8 1.8H7a1.8 1.8 0 0 1-1.8-1.8V6.3A1.8 1.8 0 0 1 7 4.5z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M8.5 4.8v5h7v-5M8.5 15.5h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        Simpan
                    </button>
                    <button type="submit" class="btn btn--ghost" data-download>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 4.5v10M7.5 10L12 14.5 16.5 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.5 16v2A2.5 2.5 0 0 0 7 20.5h10a2.5 2.5 0 0 0 2.5-2.5v-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                        <span data-download-label>Unduh PDF</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <aside class="builder-aside">
        <section class="summary-card">
            <h2>Ringkasan</h2>

            <div class="summary-stats">
                <div>
                    <strong data-summary-count>0</strong>
                    <span>Soal</span>
                </div>
                <div>
                    <strong data-summary-points>0</strong>
                    <span>Total poin</span>
                </div>
            </div>

            <ul class="summary-list">
                <li><span>Pilihan ganda</span><strong data-summary-pg>0</strong></li>
                <li><span>Esai / uraian</span><strong data-summary-esai>0</strong></li>
                <li><span>Benar / salah</span><strong data-summary-bs>0</strong></li>
                <li><span>Isian singkat</span><strong data-summary-isian>0</strong></li>
            </ul>

            <p class="summary-save" data-save-status>Klik Simpan untuk menyimpan draft ini di browser Anda.</p>
        </section>

        <section class="tip-card">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M9.8 18.5h4.4M10.5 21h3M12 3a6.5 6.5 0 0 0-3.8 11.8c.6.5 1 1.2 1 1.9v.3h5.6v-.3c0-.7.4-1.4 1-1.9A6.5 6.5 0 0 0 12 3z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Sebelum mengunduh
            </h3>
            <p>
                Periksa kembali setiap soal, kunci jawaban, dan poin. Kunci jawaban dicetak
                di halaman terpisah agar PDF soal bisa langsung dibagikan ke siswa.
            </p>
        </section>

        <section class="tip-card">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 6.2v13.3M12 6.2C10 4.6 7.4 4 5 4.4v12c2.4-.4 5 .2 7 1.7 2-1.5 4.6-2.1 7-1.7v-12c-2.4-.4-5 .2-7 1.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                </svg>
                Jenis soal tersedia
            </h3>
            <p>
                Pilihan ganda (2–6 opsi), esai dengan rubrik penilaian, benar/salah, dan
                isian singkat. Semua jenis bisa dicampur dalam satu asesmen.
            </p>
        </section>
    </aside>
</div>

<template id="question-template">
    <article class="q-card" data-question data-type="pg">
        <header class="q-card__head">
            <span class="q-num" data-q-number>1</span>
            <div class="q-card__meta">
                <span class="q-type-chip" data-type-label>Pilihan ganda</span>
                <span class="q-card__hint" data-card-hint>Soal</span>
            </div>
            <div class="q-tools">
                <button type="button" class="q-tool" data-move-up aria-label="Geser soal ke atas" title="Geser ke atas">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 19V5M6.5 10.5L12 5l5.5 5.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button type="button" class="q-tool" data-move-down aria-label="Geser soal ke bawah" title="Geser ke bawah">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5v14M6.5 13.5L12 19l5.5-5.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <button type="button" class="q-tool" data-duplicate aria-label="Duplikat soal" title="Duplikat soal">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <rect x="8.5" y="8.5" width="11" height="11" rx="2" stroke="currentColor" stroke-width="1.7"/>
                        <path d="M15.5 5.5v-1a1 1 0 0 0-1-1h-9a1 1 0 0 0-1 1v9a1 1 0 0 0 1 1h1" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    </svg>
                </button>
                <button type="button" class="q-tool q-tool--danger" data-remove aria-label="Hapus soal" title="Hapus soal">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4.5 7h15M9.5 7V4.8h5V7M6.5 7l.8 12.2a1.8 1.8 0 0 0 1.8 1.7h5.8a1.8 1.8 0 0 0 1.8-1.7L17.5 7M10 11v5.5M14 11v5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </header>

        <div class="q-card__body">
            <div class="q-row">
                <div class="field">
                    <label>Jenis soal</label>
                    <select data-field="type" name="questions[__INDEX__][type]">
                        <option value="pg">Pilihan ganda</option>
                        <option value="esai">Esai / uraian</option>
                        <option value="bs">Benar / salah</option>
                        <option value="isian">Isian singkat</option>
                    </select>
                </div>

                <div class="field field--points">
                    <label>Poin</label>
                    <input
                        type="number"
                        min="1"
                        max="100"
                        step="1"
                        value="1"
                        data-field="points"
                        name="questions[__INDEX__][points]"
                    >
                </div>
            </div>

            <div class="field">
                <label>Teks soal <span class="optional">Wajib</span></label>
                <textarea
                    rows="3"
                    maxlength="2000"
                    data-field="text"
                    name="questions[__INDEX__][text]"
                    placeholder="Tulis pertanyaan di sini…"
                ></textarea>
            </div>

            <div class="q-panel" data-panel="pg">
                <div class="q-panel__head">
                    <span>Opsi jawaban</span>
                    <small>Klik lingkaran di kiri untuk menandai kunci jawaban.</small>
                </div>

                <div class="q-options" data-options></div>

                <div class="q-panel__foot">
                    <button type="button" class="btn btn--ghost btn--sm" data-add-option>
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
                        </svg>
                        Tambah opsi
                    </button>
                    <span class="hint">Minimal 2, maksimal 6 opsi.</span>
                </div>
            </div>

            <div class="q-panel" data-panel="bs" hidden>
                <p class="q-panel__label">Kunci jawaban</p>
                <div class="q-choice">
                    <label>
                        <input type="radio" name="questions[__INDEX__][answer]" value="B">
                        <span>Benar</span>
                    </label>
                    <label>
                        <input type="radio" name="questions[__INDEX__][answer]" value="S">
                        <span>Salah</span>
                    </label>
                </div>
            </div>

            <div class="q-panel" data-panel="isian" hidden>
                <div class="field">
                    <label>Jawaban yang diharapkan</label>
                    <input
                        type="text"
                        maxlength="500"
                        data-field="short-answer"
                        name="questions[__INDEX__][answer]"
                        placeholder="Contoh: fotosintesis"
                    >
                    <p class="hint">Jawaban ini tampil pada halaman kunci jawaban.</p>
                </div>
            </div>

            <div class="q-panel" data-panel="esai" hidden>
                <div class="field">
                    <label>Pedoman penskoran / rubrik <span class="optional">Opsional</span></label>
                    <textarea
                        rows="3"
                        maxlength="2000"
                        data-field="rubric"
                        name="questions[__INDEX__][rubric]"
                        placeholder="Contoh: Skor 3 jika penjelasan lengkap dan tepat; skor 2 jika sebagian tepat; skor 1 jika kurang tepat."
                    ></textarea>
                    <p class="hint">Rubrik dicetak pada halaman kunci jawaban, bukan di lembar soal.</p>
                </div>
            </div>
        </div>
    </article>
</template>

<div class="preview-overlay" data-preview-overlay hidden>
    <div class="preview-window" role="dialog" aria-modal="true" aria-labelledby="preview-title">
        <header class="preview-bar">
            <div>
                <span>Pratinjau</span>
                <strong id="preview-title">Tampilan PDF asesmen</strong>
            </div>
            <div class="preview-bar__actions">
                <button type="button" class="btn btn--ghost btn--sm" data-preview-close>Tutup</button>
                <button type="button" class="btn btn--primary btn--sm" data-preview-download>
                    Unduh PDF
                </button>
            </div>
        </header>

        <div class="preview-scroll">
            <div class="preview-paper" data-preview-body></div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.__ASESMEN_OLD__ = <?= json_encode($oldData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
    window.__ASESMEN_HAS_OLD__ = <?= $hasOld ? 'true' : 'false' ?>;
    window.__ASESMEN_TEACHER__ = <?= json_encode($teacherName, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
</script>
<script>
    (function () {
        'use strict';

        var DRAFT_KEY = 'sasmita.asesmen.drafts.v2';
        var LEGACY_KEY = 'sasmita.asesmen.draft.v1';
        var MAX_QUESTIONS = 100;
        var MIN_OPTIONS = 2;
        var MAX_OPTIONS = 6;

        var TYPES = {
            pg: 'Pilihan ganda',
            esai: 'Esai / uraian',
            bs: 'Benar / salah',
            isian: 'Isian singkat'
        };

        var form = document.querySelector('[data-asesmen-form]');
        if (! form) {
            return;
        }

        var list = document.querySelector('[data-questions]');
        var template = document.getElementById('question-template');
        var emptyState = document.querySelector('[data-empty]');
        var saveStatus = document.querySelector('[data-save-status]');
        var previewOverlay = document.querySelector('[data-preview-overlay]');
        var previewBody = document.querySelector('[data-preview-body]');
        var draftField = document.querySelector('[data-draft-field]');
        var defaultTeacher = window.__ASESMEN_TEACHER__ || '';
        var DRAFT_ID = form.getAttribute('data-draft-id') || '';
        var IS_NEW = form.getAttribute('data-is-new') === '1';
        var LIST_URL = form.getAttribute('data-list-url') || '';
        var dirty = false;
        var missingDraft = false;
        var resetDownloadTimer = null;

        function each(selector, scope, callback) {
            var root = scope || document;
            Array.prototype.forEach.call(root.querySelectorAll(selector), callback);
        }

        function field(name) {
            return form.querySelector('[name="' + name + '"]');
        }

        function getCards() {
            return Array.prototype.slice.call(list.querySelectorAll('[data-question]'));
        }

        function esc(value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function nl2br(text) {
            return esc(text).replace(/\r\n|\r|\n/g, '<br>');
        }

        function repeat(markup, count) {
            var output = '';
            for (var i = 0; i < count; i++) {
                output += markup;
            }
            return output;
        }

        function makeOptionRow(text, checked) {
            var row = document.createElement('div');
            row.className = 'q-option';
            row.setAttribute('data-option', '');

            var pick = document.createElement('label');
            pick.className = 'q-option__pick';
            pick.title = 'Tandai sebagai kunci jawaban';

            var radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = 'questions[__INDEX__][answer]';
            radio.value = '0';
            radio.checked = !! checked;

            var letter = document.createElement('span');
            letter.className = 'q-option__letter';
            letter.setAttribute('data-option-letter', '');
            letter.textContent = 'A';

            pick.appendChild(radio);
            pick.appendChild(letter);

            var input = document.createElement('input');
            input.type = 'text';
            input.className = 'q-option__text';
            input.name = 'questions[__INDEX__][options][]';
            input.maxLength = 500;
            input.placeholder = 'Tulis opsi jawaban';
            input.value = text || '';

            var remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'q-option__remove';
            remove.setAttribute('data-remove-option', '');
            remove.setAttribute('aria-label', 'Hapus opsi jawaban');
            remove.title = 'Hapus opsi';
            remove.innerHTML = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/></svg>';

            row.appendChild(pick);
            row.appendChild(input);
            row.appendChild(remove);

            return row;
        }

        function setType(card, type) {
            if (! TYPES[type]) {
                type = 'pg';
            }

            card.setAttribute('data-type', type);
            card.querySelector('[data-field="type"]').value = type;
            card.querySelector('[data-type-label]').textContent = TYPES[type];

            ['pg', 'bs', 'isian', 'esai'].forEach(function (key) {
                var panel = card.querySelector('[data-panel="' + key + '"]');
                var active = key === type;

                panel.hidden = ! active;

                Array.prototype.forEach.call(
                    panel.querySelectorAll('input, textarea, select'),
                    function (input) {
                        input.disabled = ! active;
                    }
                );
            });

            updateOptionTools(card);
        }

        function updateOptionTools(card) {
            var options = card.querySelectorAll('[data-option]');
            var count = options.length;

            Array.prototype.forEach.call(card.querySelectorAll('[data-remove-option]'), function (button) {
                button.disabled = count <= MIN_OPTIONS;
            });

            var addButton = card.querySelector('[data-add-option]');
            if (addButton) {
                addButton.disabled = count >= MAX_OPTIONS;
            }
        }

        function createCard(data) {
            data = data || {};

            var card = template.content.firstElementChild.cloneNode(true);
            var type = TYPES[data.type] ? data.type : 'pg';
            var points = parseInt(data.points, 10);

            if (! points || points < 1) {
                points = 1;
            }
            if (points > 100) {
                points = 100;
            }

            card.querySelector('[data-field="points"]').value = points;
            card.querySelector('[data-field="text"]').value = data.text || '';

            var options = Array.isArray(data.options) ? data.options.slice(0, MAX_OPTIONS) : [];
            if (options.length === 0) {
                options = ['', '', '', ''];
            }

            var optionList = card.querySelector('[data-options]');
            var answer = data.answer == null ? '' : String(data.answer);

            options.forEach(function (option, index) {
                optionList.appendChild(makeOptionRow(option, String(index) === answer));
            });

            Array.prototype.forEach.call(card.querySelectorAll('[data-panel="bs"] input[type="radio"]'), function (radio) {
                radio.checked = answer === radio.value;
            });

            card.querySelector('[data-panel="isian"] input').value = type === 'isian' ? answer : '';
            card.querySelector('[data-panel="esai"] textarea').value = data.rubric || '';

            setType(card, type);

            return card;
        }

        function readCard(card) {
            var type = card.querySelector('[data-field="type"]').value;
            var points = parseInt(card.querySelector('[data-field="points"]').value, 10);

            if (! points || points < 1) {
                points = 1;
            }

            var data = {
                type: type,
                points: points,
                text: card.querySelector('[data-field="text"]').value,
                options: [],
                answer: '',
                rubric: ''
            };

            if (type === 'pg') {
                data.options = Array.prototype.map.call(
                    card.querySelectorAll('[data-option] input[type="text"]'),
                    function (input) {
                        return input.value;
                    }
                );

                var picked = card.querySelector('[data-panel="pg"] input[type="radio"]:checked');
                data.answer = picked ? picked.value : '';
            } else if (type === 'bs') {
                var pickedTruth = card.querySelector('[data-panel="bs"] input[type="radio"]:checked');
                data.answer = pickedTruth ? pickedTruth.value : '';
            } else if (type === 'isian') {
                data.answer = card.querySelector('[data-panel="isian"] input').value;
            } else {
                data.rubric = card.querySelector('[data-panel="esai"] textarea').value;
            }

            return data;
        }

        function readAll() {
            return {
                title: field('title').value,
                subject: field('subject').value,
                class: field('class').value,
                teacher: field('teacher').value,
                time: field('time').value,
                instructions: field('instructions').value,
                questions: getCards().map(readCard)
            };
        }

        // ---- Penyimpanan draft asesmen (localStorage) ----

        function makeDraftId() {
            return 'd' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
        }

        function loadDrafts() {
            var stored = null;

            try {
                stored = JSON.parse(localStorage.getItem(DRAFT_KEY) || '[]');
            } catch (error) {
                stored = null;
            }

            if (! Array.isArray(stored)) {
                return [];
            }

            return stored.filter(function (item) {
                return item && typeof item === 'object' && String(item.id || '') !== '';
            });
        }

        function findDraft(id) {
            if (! id) {
                return null;
            }

            var drafts = loadDrafts();

            for (var i = 0; i < drafts.length; i++) {
                if (String(drafts[i].id) === String(id)) {
                    return drafts[i];
                }
            }

            return null;
        }

        function persistDrafts(drafts) {
            try {
                localStorage.setItem(DRAFT_KEY, JSON.stringify(drafts));
                return true;
            } catch (error) {
                return false;
            }
        }

        function questionHasContent(question) {
            if (! question) {
                return false;
            }

            if (String(question.text || '').trim() !== '') {
                return true;
            }

            if (String(question.answer || '').trim() !== '') {
                return true;
            }

            if (String(question.rubric || '').trim() !== '') {
                return true;
            }

            return (Array.isArray(question.options) ? question.options : []).some(function (option) {
                return String(option || '').trim() !== '';
            });
        }

        function collectQuestions() {
            return getCards().map(readCard).filter(questionHasContent);
        }

        function collectDraft() {
            return {
                title: field('title').value,
                subject: field('subject').value,
                class: field('class').value,
                teacher: field('teacher').value,
                time: field('time').value,
                instructions: field('instructions').value,
                questions: collectQuestions()
            };
        }

        function timeLabel(timestamp) {
            try {
                return new Date(timestamp).toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return '';
            }
        }

        function saveCurrentDraft() {
            var drafts = loadDrafts();
            var data = collectDraft();
            var now = Date.now();
            var index = -1;

            for (var i = 0; i < drafts.length; i++) {
                if (String(drafts[i].id) === String(DRAFT_ID)) {
                    index = i;
                    break;
                }
            }

            if (index === -1) {
                DRAFT_ID = DRAFT_ID || makeDraftId();
                drafts.unshift({
                    id: DRAFT_ID,
                    title: data.title,
                    subject: data.subject,
                    class: data['class'],
                    teacher: data.teacher,
                    time: data.time,
                    instructions: data.instructions,
                    questions: data.questions,
                    createdAt: now,
                    updatedAt: now
                });
            } else {
                drafts[index].title = data.title;
                drafts[index].subject = data.subject;
                drafts[index]['class'] = data['class'];
                drafts[index].teacher = data.teacher;
                drafts[index].time = data.time;
                drafts[index].instructions = data.instructions;
                drafts[index].questions = data.questions;
                drafts[index].updatedAt = now;
            }

            if (! persistDrafts(drafts)) {
                setSaveStatus('Draft gagal disimpan. Penyimpanan browser mungkin penuh atau diblokir.');
                return;
            }

            if (draftField) {
                draftField.value = DRAFT_ID;
            }

            if (IS_NEW) {
                IS_NEW = false;
                form.setAttribute('data-is-new', '0');

                try {
                    window.history.replaceState(null, '', LIST_URL + '/' + encodeURIComponent(DRAFT_ID));
                } catch (error) {
                    // Riwayat browser bisa dibatasi; abaikan.
                }
            }

            dirty = false;
            setSaveStatus('Draft tersimpan pukul ' + timeLabel(now) + '.');
        }

        function markDirty() {
            dirty = true;
            setSaveStatus('Ada perubahan yang belum disimpan. Klik Simpan.');
        }

        function showMissingDraft() {
            var notice = document.querySelector('[data-missing]');

            missingDraft = true;

            if (notice) {
                notice.hidden = false;
            }

            each('[data-save]', document, function (button) {
                button.disabled = true;
            });

            each('[data-download]', document, function (button) {
                button.disabled = true;
            });

            setSaveStatus('Draft tidak ditemukan. Kembali ke daftar draft untuk memilih draft lain.');
        }

        function reindex() {
            var cards = getCards();

            cards.forEach(function (card, index) {
                card.querySelector('[data-q-number]').textContent = String(index + 1);

                Array.prototype.forEach.call(card.querySelectorAll('[name]'), function (input) {
                    var name = input.getAttribute('name') || '';

                    if (name.indexOf('__INDEX__') !== -1) {
                        name = name.replace(/__INDEX__/g, String(index));
                    } else {
                        name = name.replace(/^questions\[\d+\]/, 'questions[' + index + ']');
                    }

                    input.setAttribute('name', name);
                });

                Array.prototype.forEach.call(card.querySelectorAll('[data-option]'), function (row, position) {
                    var letter = row.querySelector('[data-option-letter]');
                    if (letter) {
                        letter.textContent = String.fromCharCode(65 + position);
                    }

                    var radio = row.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.value = String(position);
                    }
                });

                updateOptionTools(card);
            });

            if (emptyState) {
                emptyState.hidden = cards.length !== 0;
            }

            updateSummary();
        }

        function updateSummary() {
            var cards = getCards();
            var totalPoints = 0;
            var counts = { pg: 0, esai: 0, bs: 0, isian: 0 };

            cards.forEach(function (card) {
                var points = parseInt(card.querySelector('[data-field="points"]').value, 10);
                var type = card.querySelector('[data-field="type"]').value;

                if (points > 0) {
                    totalPoints += points;
                }

                if (counts[type] != null) {
                    counts[type]++;
                }
            });

            function setText(selector, value) {
                each(selector, document, function (element) {
                    element.textContent = String(value);
                });
            }

            setText('[data-summary-count]', cards.length);
            setText('[data-summary-points]', totalPoints);
            setText('[data-summary-pg]', counts.pg);
            setText('[data-summary-esai]', counts.esai);
            setText('[data-summary-bs]', counts.bs);
            setText('[data-summary-isian]', counts.isian);
            setText('[data-count]', cards.length);
        }

        function setSaveStatus(text) {
            if (saveStatus) {
                saveStatus.textContent = text;
            }
        }

        function fillForm(data) {
            if (! data) {
                return;
            }

            ['title', 'subject', 'class', 'teacher', 'time', 'instructions'].forEach(function (name) {
                if (data[name] != null) {
                    field(name).value = data[name];
                }
            });

            var questions = Array.isArray(data.questions) ? data.questions : [];
            list.innerHTML = '';

            questions.slice(0, MAX_QUESTIONS).forEach(function (question) {
                list.appendChild(createCard(question));
            });

            if (questions.length === 0) {
                list.appendChild(createCard());
            }

            reindex();
        }

        function addCard(type) {
            if (getCards().length >= MAX_QUESTIONS) {
                window.alert('Maksimal ' + MAX_QUESTIONS + ' soal dalam satu asesmen.');
                return;
            }

            var card = createCard({ type: type || 'pg', points: 1, options: ['', '', '', ''] });
            list.appendChild(card);
            reindex();

            if (emptyState) {
                emptyState.hidden = true;
            }

            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            markDirty();

            setTimeout(function () {
                var input = card.querySelector('[data-field="text"]');
                if (input) {
                    input.focus({ preventScroll: true });
                }
            }, 260);
        }

        function startDownload() {
            if (missingDraft) {
                return;
            }

            each('[data-download]', document, function (button) {
                button.disabled = true;
                button.classList.add('is-loading');
            });

            each('[data-download-label]', document, function (label) {
                label.textContent = 'Menyiapkan PDF…';
            });

            if (resetDownloadTimer) {
                clearTimeout(resetDownloadTimer);
            }

            // Unduhan tidak berpindah halaman, jadi tombol dikembalikan
            // ke keadaan semula setelah beberapa saat.
            resetDownloadTimer = setTimeout(resetDownloadButtons, 8000);
        }

        function resetDownloadButtons() {
            if (missingDraft) {
                return;
            }

            each('[data-download]', document, function (button) {
                button.disabled = false;
                button.classList.remove('is-loading');
            });

            each('[data-download-label]', document, function (label) {
                label.textContent = 'Unduh PDF';
            });
        }

        function answerLabel(question) {
            if (question.type === 'pg') {
                return question.answer === '' ? '—' : String.fromCharCode(65 + parseInt(question.answer, 10));
            }

            if (question.type === 'bs') {
                if (question.answer === 'B') {
                    return 'Benar';
                }
                if (question.answer === 'S') {
                    return 'Salah';
                }
                return '—';
            }

            if (question.type === 'isian') {
                return question.answer || '—';
            }

            return question.rubric ? 'Lihat rubrik' : 'Jawaban terbuka';
        }

        function buildPreview() {
            var data = readAll();
            var title = String(data.title || '').trim() || 'Asesmen tanpa judul';
            var questions = Array.isArray(data.questions) ? data.questions : [];
            var totalPoints = 0;
            var meta = [
                ['Mata pelajaran', data.subject],
                ['Kelas', data.class],
                ['Guru', data.teacher],
                ['Waktu', data.time]
            ].filter(function (item) {
                return String(item[1] || '').trim() !== '';
            });

            questions.forEach(function (question) {
                var points = parseInt(question.points, 10);
                if (points > 0) {
                    totalPoints += points;
                }
            });

            var html = '<div class="pv-doc">';
            html += '<p class="pv-kicker">Asesmen Pembelajaran</p>';
            html += '<h1 class="pv-title">' + esc(title) + '</h1>';

            if (meta.length) {
                html += '<table class="pv-identity"><tbody>';

                for (var i = 0; i < meta.length; i += 2) {
                    html += '<tr>';
                    html += '<th>' + esc(meta[i][0]) + '</th><td>' + esc(meta[i][1]) + '</td>';

                    if (meta[i + 1]) {
                        html += '<th>' + esc(meta[i + 1][0]) + '</th><td>' + esc(meta[i + 1][1]) + '</td>';
                    } else {
                        html += '<th></th><td></td>';
                    }

                    html += '</tr>';
                }

                html += '</tbody></table>';
            }

            html += '<p class="pv-summary">' + questions.length + ' soal &middot; Total ' + totalPoints + ' poin</p>';

            if (String(data.instructions || '').trim() !== '') {
                html += '<div class="pv-instructions"><h2>Petunjuk pengerjaan</h2><p>' + nl2br(data.instructions) + '</p></div>';
            }

            html += '<h2 class="pv-section">Soal</h2>';

            if (questions.length === 0) {
                html += '<p class="pv-empty">Belum ada soal. Tambahkan soal terlebih dahulu.</p>';
            }

            questions.forEach(function (question, index) {
                var points = parseInt(question.points, 10) || 1;

                html += '<div class="pv-question">';
                html += '<div class="pv-question__head">';
                html += '<span class="pv-num">' + (index + 1) + '.</span>';
                html += '<div class="pv-text">' + (String(question.text || '').trim() !== '' ? nl2br(question.text) : '<em>(soal belum diisi)</em>') + '</div>';
                html += '<span class="pv-points">' + points + ' poin</span>';
                html += '</div>';

                if (question.type === 'pg') {
                    html += '<div class="pv-options">';

                    (Array.isArray(question.options) ? question.options : []).forEach(function (option, position) {
                        if (String(option).trim() === '') {
                            return;
                        }

                        html += '<div class="pv-option"><span class="pv-letter">' + String.fromCharCode(65 + position) + '.</span><span>' + esc(option) + '</span></div>';
                    });

                    html += '</div>';
                } else if (question.type === 'bs') {
                    html += '<div class="pv-bs"><span class="pv-box"></span>Benar<span class="pv-box pv-box--gap"></span>Salah</div>';
                } else if (question.type === 'isian') {
                    html += '<div class="pv-answerline">Jawaban: ______________________________________________</div>';
                } else {
                    html += '<div class="pv-lines">' + repeat('<span class="pv-line"></span>', 5) + '</div>';
                }

                html += '</div>';
            });

            html += '<div class="pv-key">';
            html += '<p class="pv-kicker">Kunci jawaban</p>';
            html += '<h2 class="pv-title pv-title--small">Kunci &amp; pedoman penilaian</h2>';

            if (questions.length === 0) {
                html += '<p class="pv-empty">Belum ada kunci jawaban.</p>';
            } else {
                html += '<table class="pv-key-table"><thead><tr><th>No</th><th>Jenis</th><th>Kunci / jawaban</th><th>Poin</th></tr></thead><tbody>';

                questions.forEach(function (question, index) {
                    html += '<tr>';
                    html += '<td class="pv-key-num">' + (index + 1) + '</td>';
                    html += '<td>' + esc(TYPES[question.type] || '') + '</td>';
                    html += '<td class="pv-key-answer">' + esc(answerLabel(question)) + '</td>';
                    html += '<td class="pv-key-points">' + (parseInt(question.points, 10) || 1) + '</td>';
                    html += '</tr>';
                });

                html += '</tbody></table>';
            }

            questions.forEach(function (question, index) {
                if (question.type !== 'esai' || String(question.rubric || '').trim() === '') {
                    return;
                }

                html += '<div class="pv-rubric"><strong>Soal ' + (index + 1) + ' &middot; pedoman penskoran esai</strong><p>' + nl2br(question.rubric) + '</p></div>';
            });

            html += '</div></div>';

            return html;
        }

        function openPreview() {
            if (! previewOverlay || ! previewBody) {
                return;
            }

            previewBody.innerHTML = buildPreview();
            previewOverlay.hidden = false;
            previewOverlay.classList.add('is-visible');
            document.body.classList.add('preview-open');

            var close = previewOverlay.querySelector('[data-preview-close]');
            if (close) {
                close.focus();
            }
        }

        function closePreview() {
            if (! previewOverlay) {
                return;
            }

            previewOverlay.classList.remove('is-visible');
            previewOverlay.hidden = true;
            document.body.classList.remove('preview-open');
        }

        list.addEventListener('click', function (event) {
            var card = event.target.closest('[data-question]');
            if (! card) {
                return;
            }

            var removeOption = event.target.closest('[data-remove-option]');

            if (removeOption) {
                var row = removeOption.closest('[data-option]');
                var rows = card.querySelectorAll('[data-option]');

                if (row && rows.length > MIN_OPTIONS) {
                    row.remove();
                    reindex();
                    markDirty();
                }

                return;
            }

            if (event.target.closest('[data-add-option]')) {
                var optionList = card.querySelector('[data-options]');

                if (optionList.querySelectorAll('[data-option]').length >= MAX_OPTIONS) {
                    return;
                }

                optionList.appendChild(makeOptionRow('', false));
                reindex();
                markDirty();

                var inputs = optionList.querySelectorAll('[data-option] input[type="text"]');
                if (inputs.length) {
                    inputs[inputs.length - 1].focus();
                }

                return;
            }

            if (event.target.closest('[data-remove]')) {
                if (! window.confirm('Hapus soal ini?')) {
                    return;
                }

                card.remove();
                reindex();
                markDirty();
                return;
            }

            if (event.target.closest('[data-duplicate]')) {
                if (getCards().length >= MAX_QUESTIONS) {
                    window.alert('Maksimal ' + MAX_QUESTIONS + ' soal dalam satu asesmen.');
                    return;
                }

                var clone = createCard(readCard(card));
                card.parentNode.insertBefore(clone, card.nextSibling);
                reindex();
                markDirty();

                clone.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            if (event.target.closest('[data-move-up]')) {
                var previous = card.previousElementSibling;

                if (previous && previous.matches('[data-question]')) {
                    list.insertBefore(card, previous);
                    reindex();
                    markDirty();
                }

                return;
            }

            if (event.target.closest('[data-move-down]')) {
                var next = card.nextElementSibling;

                if (next && next.matches('[data-question]')) {
                    list.insertBefore(next, card);
                    reindex();
                    markDirty();
                }
            }
        });

        list.addEventListener('change', function (event) {
            if (event.target.matches('[data-field="type"]')) {
                var card = event.target.closest('[data-question]');
                setType(card, event.target.value);
                reindex();
                markDirty();
            }
        });

        form.addEventListener('input', function (event) {
            if (event.target.closest('[data-question]') && event.target.matches('[data-field="points"]')) {
                updateSummary();
            }

            markDirty();
        });

        form.addEventListener('submit', function (event) {
            if (missingDraft) {
                event.preventDefault();
                return;
            }

            dirty = false;
            startDownload();
        });

        each('[data-add-type]', document, function (button) {
            button.addEventListener('click', function () {
                addCard(button.getAttribute('data-add-type'));
            });
        });

        each('[data-preview]', document, function (button) {
            button.addEventListener('click', openPreview);
        });

        each('[data-save]', document, function (button) {
            button.addEventListener('click', saveCurrentDraft);
        });

        if (previewOverlay) {
            var closeButton = previewOverlay.querySelector('[data-preview-close]');
            if (closeButton) {
                closeButton.addEventListener('click', closePreview);
            }

            var previewDownload = previewOverlay.querySelector('[data-preview-download]');
            if (previewDownload) {
                previewDownload.addEventListener('click', function () {
                    closePreview();
                    startDownload();

                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                });
            }

            previewOverlay.addEventListener('click', function (event) {
                if (event.target === previewOverlay) {
                    closePreview();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && previewOverlay.classList.contains('is-visible')) {
                    closePreview();
                }
            });
        }

        var resetButton = document.querySelector('[data-reset]');
        if (resetButton) {
            resetButton.addEventListener('click', function () {
                if (! window.confirm('Bersihkan semua isian pada halaman ini? Draft yang sudah disimpan baru berubah setelah Anda menekan Simpan.')) {
                    return;
                }

                ['title', 'subject', 'class', 'time', 'instructions'].forEach(function (name) {
                    field(name).value = '';
                });

                field('teacher').value = defaultTeacher;

                var errors = document.querySelector('[data-errors]');
                if (errors) {
                    errors.hidden = true;
                }

                list.innerHTML = '';
                list.appendChild(createCard());
                reindex();
                markDirty();
                setSaveStatus('Isian dibersihkan. Klik Simpan untuk menyimpan perubahan.');
            });
        }

        // ---- Inisialisasi: pulihkan isian sebelumnya atau draft tersimpan ----

        var oldData = window.__ASESMEN_OLD__ || null;
        var restored = false;

        if (window.__ASESMEN_HAS_OLD__ && oldData) {
            fillForm(oldData);
            restored = true;
            dirty = true;
            setSaveStatus('Isian terakhir dipulihkan. Periksa kembali lalu klik Simpan.');
        } else {
            var storedDraft = findDraft(DRAFT_ID);

            if (storedDraft) {
                fillForm(storedDraft);
                restored = true;
                setSaveStatus('Draft tersimpan di browser ini.');
            } else if (! IS_NEW) {
                showMissingDraft();
                list.appendChild(createCard());
                reindex();
                restored = true;
            }
        }

        if (! restored) {
            list.appendChild(createCard());
            reindex();
        }

        window.addEventListener('beforeunload', function (event) {
            if (! dirty) {
                return;
            }

            event.preventDefault();
            event.returnValue = '';
        });

        window.addEventListener('pageshow', function () {
            resetDownloadButtons();
            closePreview();
        });
    })();
</script>
<?= $this->endSection() ?>
