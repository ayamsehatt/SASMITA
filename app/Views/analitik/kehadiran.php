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
                <rect x="4" y="5.5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.7"/>
                <path d="M8 3.5v4M16 3.5v4M4 10h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M8.2 14.2l1.8 1.8 3.8-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Kelas <?= esc($className) ?>
        </span>
        <h1>Catat kehadiran</h1>
        <p>Pilih tanggal pertemuan, lalu tentukan status hadir, sakit, izin, atau alfa untuk setiap siswa. Data pada tanggal yang sama akan diperbarui.</p>
    </div>

    <div class="an-head-actions">
        <a class="btn btn--ghost" href="<?= site_url('analitik') ?>">Lihat rekap</a>
    </div>
</div>

<section class="card an-card an-section">
    <div class="card__head">
        <div>
            <h2>Input kehadiran</h2>
            <p>Pilih tanggal, lalu tentukan status setiap siswa</p>
        </div>
        <span class="pill pill--soft"><?= count($students) ?> siswa</span>
    </div>
    <div class="card__body">
        <form class="an-toolbar" method="get" action="<?= site_url('analitik/kehadiran') ?>">
            <div class="field an-toolbar__field">
                <label for="tanggal-kehadiran">Tanggal pertemuan</label>
                <input type="date" id="tanggal-kehadiran" name="tanggal" value="<?= esc($selectedDate, 'attr') ?>" max="<?= esc(date('Y-m-d'), 'attr') ?>" required>
            </div>
            <button type="submit" class="btn btn--ghost btn--sm">Tampilkan</button>
        </form>

        <?php if ($students === []) : ?>
            <p class="an-placeholder">
                Belum ada siswa. <a href="<?= site_url('analitik#kelola-siswa') ?>">Tambahkan siswa pada panel Kelola siswa</a> untuk mulai mencatat kehadiran.
            </p>
        <?php else : ?>
            <form method="post" action="<?= site_url('analitik/kehadiran') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="tanggal" value="<?= esc($selectedDate, 'attr') ?>">
                <div class="table-wrap an-table-wrap">
                    <table class="an-table an-table--input">
                        <thead>
                        <tr>
                            <th>NIS</th>
                            <th>Nama siswa</th>
                            <th>Status kehadiran</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($students as $student) : ?>
                            <?php $studentId = (int) $student['id']; ?>
                            <tr>
                                <td class="an-cell--muted"><?= esc($student['nis'] !== null && $student['nis'] !== '' ? $student['nis'] : '—') ?></td>
                                <td><strong><?= esc($student['name']) ?></strong></td>
                                <td>
                                    <select name="kehadiran[<?= esc($studentId, 'attr') ?>]" aria-label="Kehadiran <?= esc($student['name'], 'attr') ?>">
                                        <?php foreach ($statusLabels as $code => $label) : ?>
                                            <option value="<?= esc($code, 'attr') ?>" <?= ($attendanceMap[$studentId] ?? 'H') === $code ? 'selected' : '' ?>>
                                                <?= esc($label) ?> (<?= esc($code) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="an-form-foot">
                    <span class="an-form-foot__note">Data pada tanggal yang sama akan diperbarui.</span>
                    <button type="submit" class="btn btn--primary">Simpan kehadiran</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>