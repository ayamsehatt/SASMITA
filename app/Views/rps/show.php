<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<a class="back-link" href="<?= site_url('rps') ?>">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M19.5 12h-14M11 5.5L4.5 12l6.5 6.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
    Kembali ke daftar RPS
</a>

<?php
$isReady = ($item['status'] ?? 'Draft') === 'Siap';
$source  = $item['source'] ?? null;
$size    = (int) ($source['size'] ?? 0);
$sizeText = $size >= 1048576
    ? number_format($size / 1048576, 1) . ' MB'
    : number_format($size / 1024) . ' KB';
?>

<div class="detail-head">
    <div class="detail-head__main">
        <div class="detail-tags">
            <span class="pill <?= $isReady ? 'pill--ready' : 'pill--draft' ?>">
                <span class="dot"></span>
                <?= esc($item['status'] ?? 'Draft') ?>
            </span>
            <span class="pill pill--soft"><?= esc($item['code']) ?></span>
            <span class="pill pill--soft">Dummy AI</span>
        </div>

        <h1><?= esc($item['title']) ?></h1>

        <div class="detail-meta">
            <span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="3.5" y="5.5" width="17" height="15" rx="3" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M8 3.5v4M16 3.5v4M3.5 10h17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                Dibuat <?= esc($item['created_label']) ?>
            </span>
            <span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 6.5h16M4 12h16M4 17.5h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
                <?= esc($item['method']) ?>
            </span>
            <span>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 6.2v13.3M12 6.2C10 4.6 7.4 4 5 4.4v12c2.4-.4 5 .2 7 1.7 2-1.5 4.6-2.1 7-1.7v-12c-2.4-.4-5 .2-7 1.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                </svg>
                <?= esc($item['total_meetings']) ?> pertemuan
            </span>
        </div>
    </div>

    <div class="detail-actions">
        <button type="button" class="btn btn--ghost" data-print>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 9.5V4h10v5.5M7 17.5H5.5A1.5 1.5 0 0 1 4 16v-5a1.5 1.5 0 0 1 1.5-1.5h13A1.5 1.5 0 0 1 20 11v5a1.5 1.5 0 0 1-1.5 1.5H17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                <rect x="7" y="14" width="10" height="6.5" rx="1.2" stroke="currentColor" stroke-width="1.7"/>
            </svg>
            Cetak
        </button>
        <a class="btn btn--primary" href="<?= site_url('rps/create') ?>">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
            </svg>
            Buat RPS baru
        </a>
    </div>
</div>

<section class="ai-banner">
    <span class="ai-banner__icon">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
        </svg>
    </span>
    <div>
        <strong>Disusun oleh AI · data dummy</strong>
        <p><?= esc($item['summary']) ?></p>
    </div>
</section>

<div class="info-grid">
    <div class="info-card">
        <span class="info-card__label">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4 6.5h16M4 12h16M4 17.5h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            Metode pembelajaran
        </span>
        <strong><?= esc($item['method']) ?></strong>
        <span class="sub">Metode utama yang dipakai sepanjang semester.</span>
    </div>

    <div class="info-card">
        <span class="info-card__label">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M20 12.5V18a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 18V6a2.5 2.5 0 0 1 2.5-2.5H11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M16 3.5h4.5V8M20.3 3.7l-7.6 7.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Sumber relevan
        </span>

        <?php if ($source !== null) : ?>
            <span class="file-name" title="<?= esc($source['name'], 'attr') ?>">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M7 3.5h7L19 8.5v12H7a2 2 0 0 1-2-2v-13a2 2 0 0 1 2-2z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M13.5 4v5h5" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                </svg>
                <?= esc($source['name']) ?>
            </span>
            <span class="sub"><?= esc($sizeText) ?> · terunggah bersama RPS</span>

            <?php if (! empty($source['stored'])) : ?>
                <a class="btn btn--soft btn--sm" href="<?= site_url('rps/' . $item['id'] . '/sumber') ?>">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 4.5v10M7.5 10L12 14.5 16.5 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.5 16v2A2.5 2.5 0 0 0 7 20.5h10a2.5 2.5 0 0 0 2.5-2.5v-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                    Unduh sumber
                </a>
            <?php endif; ?>
        <?php else : ?>
            <strong>Belum ada lampiran</strong>
            <span class="sub">Referensi tetap disusun dari topik dan tujuan pembelajaran.</span>
        <?php endif; ?>
    </div>

    <div class="info-card">
        <span class="info-card__label">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="3.5" y="5.5" width="17" height="15" rx="3" stroke="currentColor" stroke-width="1.7"/>
                <path d="M8 3.5v4M16 3.5v4M3.5 10h17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            Struktur semester
        </span>
        <strong><?= esc($item['total_meetings']) ?> pertemuan</strong>
        <span class="sub">UTS pada pekan 8 dan UAS pada pekan 16.</span>
    </div>
</div>

<div class="detail-grid">
    <section class="card">
        <div class="card__head">
            <h2>Capaian pembelajaran</h2>
        </div>
        <div class="card__body">
            <ul class="bullet-list">
                <?php foreach ($item['capaian'] as $capaian) : ?>
                    <li><?= esc($capaian) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="card">
        <div class="card__head">
            <h2>Tujuan pembelajaran</h2>
        </div>
        <div class="card__body">
            <?php if (($item['objective'] ?? []) !== []) : ?>
                <ul class="bullet-list">
                    <?php foreach ($item['objective'] as $objective) : ?>
                        <li><?= esc($objective) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="sub" style="margin:0;color:var(--muted);font-size:.84rem">Belum ada tujuan pembelajaran.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<section class="card detail-block">
    <div class="card__head">
        <div>
            <h2>Alur pembelajaran</h2>
            <p>Rincian 16 pertemuan, kegiatan, materi, dan penilaian.</p>
        </div>
        <span class="pill pill--soft"><?= esc($item['total_meetings']) ?> pertemuan</span>
    </div>

    <div class="table-wrap">
        <table class="plan-table">
            <thead>
                <tr>
                    <th scope="col">Pekan</th>
                    <th scope="col">Topik &amp; materi</th>
                    <th scope="col">Kegiatan pembelajaran</th>
                    <th scope="col">Metode</th>
                    <th scope="col">Penilaian</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($item['meetings'] as $meeting) : ?>
                    <tr>
                        <td><span class="week"><?= esc($meeting['week']) ?></span></td>
                        <td>
                            <span class="topic"><?= esc($meeting['topic']) ?></span>
                            <span class="materials"><?= esc($meeting['materials']) ?></span>
                        </td>
                        <td>
                            <ul class="activities">
                                <?php foreach ($meeting['activities'] as $activity) : ?>
                                    <li><?= esc($activity) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </td>
                        <td><span class="method-tag"><?= esc($meeting['method']) ?></span></td>
                        <td><?= esc($meeting['assessment']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="detail-grid">
    <section class="card">
        <div class="card__head">
            <div>
                <h2>Rencana penilaian</h2>
                <p>Indikator yang dipetakan ke teknik dan instrumen.</p>
            </div>
        </div>
        <div class="card__body" style="padding-top:6px">
            <table class="assessment-table">
                <thead>
                    <tr>
                        <th scope="col">Indikator</th>
                        <th scope="col">Teknik</th>
                        <th scope="col">Instrumen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($item['assessment_plan'] as $assessment) : ?>
                        <tr>
                            <td><?= esc($assessment['indicator']) ?></td>
                            <td><?= esc($assessment['technique']) ?></td>
                            <td><?= esc($assessment['instrument']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <div class="card__head">
            <h2>Referensi</h2>
        </div>
        <div class="card__body">
            <ol class="number-list">
                <?php foreach ($item['references'] as $reference) : ?>
                    <li><?= esc($reference) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>
</div>

<section class="card detail-block">
    <div class="card__head">
        <div>
            <h2>Ringkasan materi ajar</h2>
            <p>Pokok bahasan utama yang bisa Anda kembangkan menjadi bahan ajar.</p>
        </div>
    </div>
    <div class="card__body">
        <ul class="bullet-list">
            <?php foreach ($item['material_outline'] as $outline) : ?>
                <li><?= esc($outline) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<section class="danger-zone no-print">
    <div>
        <strong>Hapus RPS ini?</strong>
        <span>Tindakan ini hanya menghapus data pada sesi demo browser Anda.</span>
    </div>
    <form action="<?= site_url('rps/' . $item['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus RPS ini dari daftar?');">
        <button type="submit" class="btn btn--danger btn--sm">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4.5 7h15M9.5 7V4.8h5V7M6.5 7l.8 12.2a1.8 1.8 0 0 0 1.8 1.7h5.8a1.8 1.8 0 0 0 1.8-1.7L17.5 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            Hapus RPS
        </button>
    </form>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.querySelectorAll('[data-print]').forEach(function (button) {
        button.addEventListener('click', function () {
            window.print();
        });
    });
</script>
<?= $this->endSection() ?>
