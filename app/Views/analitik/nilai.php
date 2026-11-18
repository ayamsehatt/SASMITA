<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/analitik.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-head an-page-head">
    <div>
        <a class="an-back" href="<?= site_url('analitik') ?>">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Kembali ke analitik
        </a>
        <span class="page-head__eyebrow">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 4.5h9l3 3v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M15 4.5v3h3M8 13h8M8 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M8.2 10.2l1.3 1.3 2.5-2.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Kelas <?= esc($className) ?>
        </span>
        <h1>Isi nilai asesmen</h1>
        <p>Buat asesmen baru atau pilih asesmen yang sudah ada, lalu isi nilai siswa. Kosongkan nilai untuk menandai siswa belum dinilai.</p>
    </div>

    <div class="an-head-actions">
        <a class="btn btn--ghost" href="<?= site_url('analitik') ?>">Lihat rekap</a>
    </div>
</div>

<div class="an-nilai-layout">
    <aside class="an-nilai-side">
        <section class="card an-card">
            <div class="card__head">
                <div>
                    <h2>Daftar asesmen</h2>
                    <p>Pilih asesmen untuk diisi atau diperbarui</p>
                </div>
                <span class="pill pill--soft"><?= count($assessments) ?></span>
            </div>
            <div class="card__body">
                <div class="an-assessment-list">
                    <?php if ($assessments === []) : ?>
                        <p class="an-placeholder an-placeholder--small">Belum ada asesmen. Buat asesmen baru di bawah.</p>
                    <?php else : ?>
                        <?php foreach ($assessmentSummaries as $summary) : ?>
                            <div class="an-assessment-item <?= (int) $summary['id'] === (int) $selectedAssessment ? 'is-active' : '' ?>">
                                <a href="<?= site_url('analitik/nilai?asesmen=' . (int) $summary['id']) ?>">
                                    <strong><?= esc($summary['title']) ?></strong>
                                    <span><?= esc($summary['label']) ?> · <?= esc($summary['date']) ?> · <?= esc($summary['count']) ?> dinilai</span>
                                </a>
                                <form method="post" action="<?= site_url('analitik/asesmen/' . (int) $summary['id'] . '/hapus') ?>" onsubmit="return confirm('Hapus asesmen ini beserta seluruh nilainya?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="icon-btn" aria-label="Hapus asesmen <?= esc($summary['title'], 'attr') ?>" title="Hapus asesmen">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4.5 7h15M9.5 7V4.8h5V7M6.5 7l.8 12.2a1.8 1.8 0 0 0 1.8 1.7h5.8a1.8 1.8 0 0 0 1.8-1.7L17.5 7M10 11v5.5M14 11v5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <form class="an-new-assessment" method="post" action="<?= site_url('analitik/asesmen') ?>">
                    <?= csrf_field() ?>
                    <div class="an-new-assessment__title">Buat asesmen baru</div>
                    <div class="an-field-stack">
                        <div class="field">
                            <label for="asesmen-judul">Judul asesmen</label>
                            <input type="text" id="asesmen-judul" name="title" value="<?= old('title') ?>" placeholder="Contoh: Ulangan Harian Bab 2" required>
                        </div>
                        <div class="field">
                            <label for="asesmen-jenis">Jenis</label>
                            <select id="asesmen-jenis" name="type">
                                <option value="formatif" <?= old('type') === 'formatif' ? 'selected' : '' ?>>Formatif</option>
                                <option value="tugas" <?= old('type') === 'tugas' ? 'selected' : '' ?>>Tugas</option>
                                <option value="sumatif" <?= old('type') === 'sumatif' ? 'selected' : '' ?>>Sumatif</option>
                            </select>
                        </div>
                        <div class="field">
                            <label for="asesmen-tanggal">Tanggal</label>
                            <input type="date" id="asesmen-tanggal" name="assessment_date" value="<?= esc(old('assessment_date', date('Y-m-d')), 'attr') ?>" max="<?= esc(date('Y-m-d'), 'attr') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn--soft btn--sm">Tambah asesmen</button>
                </form>
            </div>
        </section>
    </aside>

    <section class="card an-card an-nilai-main">
        <div class="card__head">
            <div>
                <h2>Isi nilai</h2>
                <p>Masukkan nilai 0–100 untuk asesmen yang dipilih</p>
            </div>
            <?php if ($selectedAssessment > 0) : ?>
                <span class="pill pill--soft">KKM <?= esc($kkm) ?></span>
            <?php endif; ?>
        </div>
        <div class="card__body">
            <?php if ($students === []) : ?>
                <p class="an-placeholder">
                    Belum ada siswa. <a href="<?= site_url('analitik#kelola-siswa') ?>">Tambahkan siswa pada panel Kelola siswa</a> sebelum mengisi nilai.
                </p>
            <?php elseif ($selectedAssessment <= 0) : ?>
                <p class="an-placeholder">Belum ada asesmen yang dipilih. Buat atau pilih asesmen di sebelah kiri untuk mulai mengisi nilai.</p>
            <?php else : ?>
                <form class="an-score-form an-score-form--page" method="post" action="<?= site_url('analitik/nilai') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="asesmen_id" value="<?= esc($selectedAssessment, 'attr') ?>">
                    <div class="table-wrap an-table-wrap">
                        <table class="an-table an-table--input">
                            <thead>
                            <tr>
                                <th>NIS</th>
                                <th>Nama siswa</th>
                                <th class="an-cell--num">Nilai (0–100)</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($students as $student) : ?>
                                <?php $studentId = (int) $student['id']; ?>
                                <tr>
                                    <td class="an-cell--muted"><?= esc($student['nis'] !== null && $student['nis'] !== '' ? $student['nis'] : '—') ?></td>
                                    <td><strong><?= esc($student['name']) ?></strong></td>
                                    <td class="an-cell--num">
                                        <input type="number"
                                               name="nilai[<?= esc($studentId, 'attr') ?>]"
                                               min="0" max="100" step="1"
                                               placeholder="—"
                                               value="<?= esc($scoreMap[$studentId] ?? '', 'attr') ?>"
                                               aria-label="Nilai <?= esc($student['name'], 'attr') ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="an-form-foot">
                        <span class="an-form-foot__note">Kosongkan kolom untuk menandai "belum dinilai".</span>
                        <button type="submit" class="btn btn--primary">Simpan nilai</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= $this->endSection() ?>