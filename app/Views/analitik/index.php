<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/analitik.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
/**
 * Format angka ringkas untuk kartu statistik dan tabel.
 *
 * @param mixed $value
 */
$fmt = static function ($value, int $decimals = 0, string $suffix = ''): string {
    if ($value === null || $value === '') {
        return '—';
    }

    return esc(number_format((float) $value, $decimals, ',', '.') . $suffix);
};

$colors = ['H' => '#0f7b4d', 'S' => '#d97706', 'I' => '#4361ee', 'A' => '#c0264f'];

$segments = [];
$start    = 0.0;

foreach (['H', 'S', 'I', 'A'] as $code) {
    $count  = (int) ($attendanceTotals[$code] ?? 0);
    $share  = $attendanceTotals['total'] > 0 ? ($count / $attendanceTotals['total']) * 100 : 0.0;
    $segments[] = [
        'code'    => $code,
        'label'   => $statusLabels[$code],
        'count'   => $count,
        'percent' => round($share, 1),
        'start'   => $start,
        'end'     => $start + $share,
    ];
    $start += $share;
}

$gradientParts = [];

foreach ($segments as $segment) {
    $gradientParts[] = $colors[$segment['code']] . ' ' . round($segment['start'], 2) . '% ' . round($segment['end'], 2) . '%';
}

$donutStyle = $attendanceTotals['total'] > 0
    ? 'conic-gradient(' . implode(', ', $gradientParts) . ')'
    : '#ece6f8';

$badgeClass = static function (string $tone): string {
    if ($tone === 'ok') {
        return 'pill pill--ready';
    }

    if ($tone === 'warn') {
        return 'pill pill--draft';
    }

    return 'pill pill--soft';
};

$attLast = trim((string) ($attendanceCard['lastDate'] ?? ''));
$attMeta = [
    (int) ($attendanceCard['students'] ?? 0) . ' siswa',
    'hari ini ' . (int) ($attendanceCard['today'] ?? 0) . ' tercatat',
    $attLast !== '' ? 'terakhir ' . $attLast : 'belum ada catatan',
];

$nilaiLast = trim((string) ($nilaiCard['lastTitle'] ?? ''));
$nilaiMeta = [
    (int) ($nilaiCard['assessments'] ?? 0) . ' asesmen',
    (int) ($nilaiCard['scores'] ?? 0) . ' nilai tercatat',
    $nilaiLast !== '' ? 'terakhir ' . $nilaiLast : 'belum ada asesmen',
];
?>

<div class="page-head">
    <div>
        <span class="page-head__eyebrow">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4.5 19.5V13M9.8 19.5V7.5M15.2 19.5v-4.5M20.5 19.5V4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            Rekap kelas <?= esc($className) ?>
        </span>
        <h1>Analitik kehadiran &amp; nilai</h1>
    </div>

    <div class="an-head-actions">
        <a class="btn btn--ghost" href="<?= site_url('analitik/kehadiran') ?>">Input kehadiran</a>
        <a class="btn btn--primary" href="<?= site_url('analitik/nilai') ?>">Input nilai</a>
        <button type="button" class="btn btn--soft" data-print>Cetak rekap</button>
    </div>
</div>

<div class="stats-grid an-stats">
    <div class="stat-card">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <circle cx="9" cy="8.5" r="3" stroke="currentColor" stroke-width="1.7"/>
                <path d="M3.5 19c0-3 2.5-5 5.5-5s5.5 2 5.5 5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M16 6.5a3 3 0 0 1 0 5.6M17.5 14.5c1.8.7 3 2.3 3 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
        </span>
        <strong class="stat-card__value"><?= $fmt($kpi['students']) ?></strong>
        <span class="stat-card__label">Total siswa</span>
    </div>

    <div class="stat-card">
        <span class="stat-card__icon stat-card__icon--green">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 19V5.5M5 19h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M8.5 15.5l3-3.4 2.5 2.2 3.5-4.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <strong class="stat-card__value"><?= $fmt($kpi['attendancePercent'], 1, '%') ?></strong>
        <span class="stat-card__label">Kehadiran (H) dari <?= $fmt($kpi['attendanceRecords']) ?> data</span>
    </div>

    <div class="stat-card">
        <span class="stat-card__icon stat-card__icon--pink">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 4.8l8.3 14.4H3.7L12 4.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M12 10v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                <circle cx="12" cy="16.6" r="1" fill="currentColor"/>
            </svg>
        </span>
        <strong class="stat-card__value"><?= $fmt($kpi['attention']) ?></strong>
        <span class="stat-card__label">Siswa perlu perhatian (&lt;75%)</span>
    </div>

    <div class="stat-card">
        <span class="stat-card__icon stat-card__icon--blue">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 5.5h14v13H5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M8.5 15l2.4-3 2.1 1.8 2.7-3.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <strong class="stat-card__value"><?= $fmt($kpi['avgScore'], 1) ?></strong>
        <span class="stat-card__label">Rata-rata seluruh nilai (KKM <?= esc($kkm) ?>)</span>
    </div>
</div>

<div class="an-chart-grid">
    <section class="card an-card">
        <div class="card__head">
            <div>
                <h2>Komposisi kehadiran</h2>
                <p>Seluruh pertemuan yang sudah dicatat</p>
            </div>
            <span class="pill pill--soft"><?= $fmt($attendanceTotals['total']) ?> data</span>
        </div>
        <div class="card__body an-donut-body">
            <div class="an-donut"
                 style="background: <?= esc($donutStyle, 'attr') ?>"
                 role="img"
                 aria-label="Komposisi kehadiran: <?= esc(implode(', ', array_map(static fn (array $s): string => $s['label'] . ' ' . $s['percent'] . '%', $segments))) ?>">
                <span class="an-donut__center">
                    <strong><?= $fmt($segments[0]['percent'], 1, '%') ?></strong>
                    <span>Hadir</span>
                </span>
            </div>

            <ul class="an-legend">
                <?php foreach ($segments as $segment) : ?>
                    <li>
                        <span class="an-legend__dot" style="background: <?= esc($colors[$segment['code']], 'attr') ?>"></span>
                        <span class="an-legend__label"><?= esc($segment['label']) ?></span>
                        <strong><?= esc($segment['count']) ?></strong>
                        <span class="an-legend__percent"><?= $fmt($segment['percent'], 1, '%') ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="card an-card">
        <div class="card__head">
            <div>
                <h2>Tren kehadiran per pertemuan</h2>
                <p><?= count($trend) ?> pertemuan terakhir</p>
            </div>
            <div class="an-legend an-legend--inline">
                <?php foreach ($statusLabels as $code => $label) : ?>
                    <span><i style="background: <?= esc($colors[$code], 'attr') ?>"></i><?= esc($label) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="card__body">
            <?php if ($trend === []) : ?>
                <p class="an-placeholder">Belum ada kehadiran yang tercatat. Buka halaman <a href="<?= site_url('analitik/kehadiran') ?>">Input kehadiran</a> untuk mencatat dan melihat grafik.</p>
            <?php else : ?>
                <div class="an-trend">
                    <?php foreach ($trend as $day) : ?>
                        <?php
                        $dayLabel = 'Tanggal ' . $day['date'] . ': ';
                        foreach ($statusLabels as $code => $label) {
                            $dayLabel .= $label . ' ' . $day['counts'][$code] . ', ';
                        }
                        ?>
                        <div class="an-trend__item">
                            <span class="an-trend__value"><?= $fmt($day['percent'], 0, '%') ?></span>
                            <div class="an-trend__bar" role="img" aria-label="<?= esc($dayLabel, 'attr') ?>">
                                <?php foreach ($statusLabels as $code => $label) : ?>
                                    <?php if ($day['counts'][$code] > 0) : ?>
                                        <span class="an-seg an-seg--<?= esc(strtolower($code), 'attr') ?>"
                                              style="height: <?= esc(round($day['counts'][$code] / $day['total'] * 100, 2), 'attr') ?>%"></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <span class="an-trend__date"><?= esc($day['label']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="card an-card">
        <div class="card__head">
            <div>
                <h2>Distribusi rata-rata nilai</h2>
                <p>Diurutkan dari rentang tertinggi</p>
            </div>
        </div>
        <div class="card__body">
            <?php foreach ($distribution as $bucket) : ?>
                <div class="an-dist">
                    <span class="an-dist__label"><?= esc($bucket['label']) ?></span>
                    <span class="an-dist__track">
                        <span class="an-dist__fill an-dist__fill--<?= esc($bucket['tone'], 'attr') ?>"
                              style="width: <?= esc($bucket['percent'], 'attr') ?>%"></span>
                    </span>
                    <strong class="an-dist__value"><?= esc($bucket['count']) ?></strong>
                    <span class="an-dist__percent"><?= $fmt($bucket['percent'], 0, '%') ?></span>
                </div>
            <?php endforeach; ?>
            <p class="an-card__note">Rata-rata dihitung dari seluruh asesmen yang sudah dinilai per siswa.</p>
        </div>
    </section>

    <section class="card an-card">
        <div class="card__head">
            <div>
                <h2>Tindakan yang disarankan</h2>
                <p>Dihitung dari data kehadiran dan nilai saat ini</p>
            </div>
        </div>
        <div class="card__body">
            <ul class="an-insights">
                <?php foreach ($insights as $insight) : ?>
                    <li class="an-insight an-insight--<?= esc($insight['tone'], 'attr') ?>">
                        <span class="an-insight__dot"></span>
                        <span><?= esc($insight['text']) ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
</div>

<section class="card an-card an-section">
    <div class="card__head">
        <div>
            <h2>Rekap per siswa</h2>
            <p>Ringkasan kehadiran dan capaian nilai tiap siswa</p>
        </div>
        <span class="pill pill--soft">KKM <?= esc($kkm) ?></span>
    </div>
    <div class="table-wrap">
        <table class="an-table">
            <thead>
            <tr>
                <th>NIS</th>
                <th>Nama siswa</th>
                <th class="an-cell--num">H</th>
                <th class="an-cell--num">S</th>
                <th class="an-cell--num">I</th>
                <th class="an-cell--num">A</th>
                <th class="an-cell--num">Hadir</th>
                <th class="an-cell--num">Rata-rata</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($recap === []) : ?>
                <tr><td colspan="9" class="an-empty-cell">Belum ada siswa. Tambahkan siswa pada panel Kelola siswa.</td></tr>
            <?php else : ?>
                <?php foreach ($recap as $row) : ?>
                    <tr>
                        <td class="an-cell--muted"><?= esc($row['nis'] !== '' ? $row['nis'] : '—') ?></td>
                        <td><strong><?= esc($row['name']) ?></strong></td>
                        <td class="an-cell--num an-count--h"><?= esc($row['counts']['H']) ?></td>
                        <td class="an-cell--num an-count--s"><?= esc($row['counts']['S']) ?></td>
                        <td class="an-cell--num an-count--i"><?= esc($row['counts']['I']) ?></td>
                        <td class="an-cell--num an-count--a"><?= esc($row['counts']['A']) ?></td>
                        <td class="an-cell--num"><?= $fmt($row['present'], 1, '%') ?></td>
                        <td class="an-cell--num"><strong><?= $fmt($row['average'], 1) ?></strong></td>
                        <td><span class="<?= esc($badgeClass($row['tone']), 'attr') ?>"><span class="dot"></span><?= esc($row['label']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="an-section">
    <div class="an-section-head">
        <div>
            <h2>Input data</h2>
            <p>Catat kehadiran per pertemuan atau nilai tiap asesmen pada halaman terpisah.</p>
        </div>
    </div>

    <div class="an-input-cards">
        <a class="card an-action-card an-action-card--attendance" href="<?= site_url('analitik/kehadiran') ?>">
            <span class="an-action-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <rect x="4" y="5.5" width="16" height="14" rx="2.5" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M8 3.5v4M16 3.5v4M4 10h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    <path d="M8.2 14.2l1.8 1.8 3.8-4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div class="an-action-card__body">
                <span class="an-action-card__eyebrow">Kehadiran</span>
                <h3>Input kehadiran</h3>
                <p>Pilih tanggal pertemuan, lalu tandai status hadir, sakit, izin, atau alfa untuk setiap siswa.</p>
                <ul class="an-action-card__meta">
                    <?php foreach ($attMeta as $item) : ?>
                        <li><?= esc($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <span class="an-action-card__arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </a>

        <a class="card an-action-card an-action-card--nilai" href="<?= site_url('analitik/nilai') ?>">
            <span class="an-action-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M6 4.5h9l3 3v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M15 4.5v3h3M8 13h8M8 16.5h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                    <path d="M8.2 10.2l1.3 1.3 2.5-2.8" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div class="an-action-card__body">
                <span class="an-action-card__eyebrow">Nilai</span>
                <h3>Input nilai</h3>
                <p>Buat asesmen baru atau pilih asesmen yang sudah ada, lalu isi nilai siswa pada halaman khusus.</p>
                <ul class="an-action-card__meta">
                    <?php foreach ($nilaiMeta as $item) : ?>
                        <li><?= esc($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <span class="an-action-card__arrow" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
        </a>
    </div>
</section>

<section class="card an-card an-section" id="kelola-siswa">
    <div class="card__head">
        <div>
            <h2>Kelola siswa</h2>
            <p>Tambah atau hapus siswa dalam kelas</p>
        </div>
        <span class="pill pill--soft"><?= esc($kpi['students']) ?> siswa</span>
    </div>
    <div class="card__body">
        <form class="an-field-row an-add-student" method="post" action="<?= site_url('analitik/siswa') ?>">
            <div class="field">
                <label for="siswa-nama">Nama siswa</label>
                <input type="text" id="siswa-nama" name="name" value="<?= old('name') ?>" placeholder="Contoh: Nama siswa baru" required>
            </div>
            <div class="field">
                <label for="siswa-nis">NIS <span class="optional">opsional</span></label>
                <input type="text" id="siswa-nis" name="nis" value="<?= old('nis') ?>" placeholder="Contoh: 2024013">
            </div>
            <div class="field">
                <label for="siswa-kelas">Kelas</label>
                <input type="text" id="siswa-kelas" name="class_name" value="<?= old('class_name', $className) ?>">
            </div>
            <button type="submit" class="btn btn--primary">Tambah siswa</button>
        </form>

        <div class="table-wrap an-table-wrap">
            <table class="an-table">
                <thead>
                <tr>
                    <th>NIS</th>
                    <th>Nama siswa</th>
                    <th>Kelas</th>
                    <th class="an-cell--num">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($students === []) : ?>
                    <tr><td colspan="4" class="an-empty-cell">Belum ada siswa.</td></tr>
                <?php else : ?>
                    <?php foreach ($students as $student) : ?>
                        <tr>
                            <td class="an-cell--muted"><?= esc($student['nis'] !== null && $student['nis'] !== '' ? $student['nis'] : '—') ?></td>
                            <td><strong><?= esc($student['name']) ?></strong></td>
                            <td><?= esc($student['class_name']) ?></td>
                            <td class="an-cell--num">
                                <form method="post" action="<?= site_url('analitik/siswa/' . (int) $student['id'] . '/hapus') ?>" onsubmit="return confirm('Hapus <?= esc($student['name'], 'attr') ?> beserta data kehadiran dan nilainya?');">
                                    <button type="submit" class="icon-btn" aria-label="Hapus <?= esc($student['name'], 'attr') ?>" title="Hapus siswa">
                                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4.5 7h15M9.5 7V4.8h5V7M6.5 7l.8 12.2a1.8 1.8 0 0 0 1.8 1.7h5.8a1.8 1.8 0 0 0 1.8-1.7L17.5 7M10 11v5.5M14 11v5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        var buttons = document.querySelectorAll('[data-print]');

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                window.print();
            });
        });
    })();
</script>
<?= $this->endSection() ?>
