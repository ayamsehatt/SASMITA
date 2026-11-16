<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<div class="page-head">
    <div>
        <span class="page-head__eyebrow">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
            </svg>
            Ruang kerja guru
        </span>
        <h1>Rencana Pembelajaran &amp; Materi</h1>
        <p>
            Masukkan judul materi, tujuan, dan indikator penilaian. SASMITA akan
            menyusun RPS 16 pertemuan beserta materi dan instrumen penilaiannya.
        </p>
    </div>

    <a class="btn btn--primary" href="<?= site_url('rps/create') ?>">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
        </svg>
        Buat RPS baru
    </a>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-card__icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M4.5 5.8A2.3 2.3 0 0 1 6.8 3.5h4.7v17H6.8a2.3 2.3 0 0 0-2.3 2.3V5.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M19.5 5.8a2.3 2.3 0 0 0-2.3-2.3h-4.7v17h4.7a2.3 2.3 0 0 1 2.3 2.3V5.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            </svg>
        </span>
        <strong class="stat-card__value"><?= esc($stats['rps']) ?></strong>
        <span class="stat-card__label">RPS tersusun</span>
    </div>

    <div class="stat-card">
        <span class="stat-card__icon stat-card__icon--pink">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="3.5" y="5.5" width="17" height="15" rx="3" stroke="currentColor" stroke-width="1.7"/>
                <path d="M8 3.5v4M16 3.5v4M3.5 10h17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
        </span>
        <strong class="stat-card__value"><?= esc($stats['meetings']) ?></strong>
        <span class="stat-card__label">Pertemuan dirancang</span>
    </div>

    <div class="stat-card">
        <span class="stat-card__icon stat-card__icon--blue">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 6.2v13.3M12 6.2C10 4.6 7.4 4 5 4.4v12c2.4-.4 5 .2 7 1.7 2-1.5 4.6-2.1 7-1.7v-12c-2.4-.4-5 .2-7 1.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            </svg>
        </span>
        <strong class="stat-card__value"><?= esc($stats['sources']) ?></strong>
        <span class="stat-card__label">Sumber terlampir</span>
    </div>

    <div class="stat-card">
        <span class="stat-card__icon stat-card__icon--green">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M5 19V5.5M5 19h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                <path d="M8.5 15.5l3-3.4 2.5 2.2 3.5-4.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <strong class="stat-card__value"><?= esc($stats['indicators']) ?></strong>
        <span class="stat-card__label">Indikator penilaian</span>
    </div>
</div>

<div class="list-head">
    <h2>RPS terbaru</h2>

    <div class="filters" role="group" aria-label="Filter status RPS">
        <button type="button" class="filter-chip is-active" data-filter="all">Semua</button>
        <button type="button" class="filter-chip" data-filter="Siap">Siap</button>
        <button type="button" class="filter-chip" data-filter="Draft">Draft</button>
    </div>
</div>

<?php if ($items === []) : ?>
    <div class="empty-state">
        <span class="empty-state__icon">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 6.2v13.3M12 6.2C10 4.6 7.4 4 5 4.4v12c2.4-.4 5 .2 7 1.7 2-1.5 4.6-2.1 7-1.7v-12c-2.4-.4-5 .2-7 1.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M17 2.8l.9 2.2 2.2.9-2.2.9L17 9l-.9-2.2-2.2-.9 2.2-.9L17 2.8z" fill="currentColor"/>
            </svg>
        </span>
        <h3>Belum ada RPS</h3>
        <p>Mulai dengan memasukkan judul materi dan tujuan pembelajaran. Rencana lengkap akan disusun otomatis.</p>
        <a class="btn btn--primary" href="<?= site_url('rps/create') ?>">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
            </svg>
            Buat RPS pertama
        </a>
    </div>
<?php else : ?>
    <div class="rps-grid" data-rps-grid>
        <?php foreach ($items as $item) : ?>
            <?php
            $isReady  = ($item['status'] ?? 'Draft') === 'Siap';
            $objective = $item['objective'][0] ?? '';
            $source   = $item['source'] ?? null;
            ?>
            <article class="rps-card" data-status="<?= esc($item['status'] ?? 'Draft', 'attr') ?>">
                <div class="rps-card__top">
                    <span class="rps-card__code"><?= esc($item['code']) ?></span>
                    <span class="pill <?= $isReady ? 'pill--ready' : 'pill--draft' ?>">
                        <span class="dot"></span>
                        <?= esc($item['status'] ?? 'Draft') ?>
                    </span>
                </div>

                <h3>
                    <a href="<?= site_url('rps/' . $item['id']) ?>"><?= esc($item['title']) ?></a>
                </h3>

                <p class="rps-card__desc"><?= esc($objective) ?></p>

                <div class="rps-card__meta">
                    <span class="chip">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 6.5h16M4 12h16M4 17.5h10" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        <span><?= esc($item['method']) ?></span>
                    </span>
                    <span class="chip">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <rect x="3.5" y="5.5" width="17" height="15" rx="3" stroke="currentColor" stroke-width="1.7"/>
                            <path d="M8 3.5v4M16 3.5v4M3.5 10h17" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                        </svg>
                        <span><?= esc($item['total_meetings']) ?> pertemuan</span>
                    </span>
                    <?php if ($source !== null) : ?>
                        <span class="chip">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M20 12.5V18a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 18V6a2.5 2.5 0 0 1 2.5-2.5H11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M16 3.5h4.5V8M20.3 3.7l-7.6 7.6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span><?= esc($source['name']) ?></span>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="rps-card__foot">
                    <span class="rps-card__date">Dibuat <?= esc($item['created_label']) ?></span>
                    <div class="rps-card__actions">
                        <a class="link-arrow" href="<?= site_url('rps/' . $item['id']) ?>">
                            Buka
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4.5 12h14M13 6.5l5.5 5.5L13 17.5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <form action="<?= site_url('rps/' . $item['id'] . '/hapus') ?>" method="post" onsubmit="return confirm('Hapus RPS ini dari daftar?');">
                            <button type="submit" class="icon-btn" aria-label="Hapus RPS <?= esc($item['title'], 'attr') ?>" title="Hapus RPS">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4.5 7h15M9.5 7V4.8h5V7M6.5 7l.8 12.2a1.8 1.8 0 0 0 1.8 1.7h5.8a1.8 1.8 0 0 0 1.8-1.7L17.5 7M10 11v5.5M14 11v5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <p class="empty-state" data-filter-empty hidden>Belum ada RPS dengan status tersebut.</p>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        var chips = document.querySelectorAll('[data-filter]');
        var cards = document.querySelectorAll('[data-status]');
        var empty = document.querySelector('[data-filter-empty]');

        if (! chips.length) {
            return;
        }

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                var value = chip.getAttribute('data-filter');
                var visible = 0;

                chips.forEach(function (item) {
                    item.classList.toggle('is-active', item === chip);
                });

                cards.forEach(function (card) {
                    var show = value === 'all' || card.getAttribute('data-status') === value;
                    card.hidden = ! show;
                    if (show) {
                        visible++;
                    }
                });

                if (empty) {
                    empty.hidden = visible !== 0;
                }
            });
        });
    })();
</script>
<?= $this->endSection() ?>
