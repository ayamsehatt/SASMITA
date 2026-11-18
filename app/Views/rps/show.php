<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/materi.css') ?>">
<?= $this->endSection() ?>

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
            <p>Klik topik untuk melihat materi &amp; panduan belajar (dummy AI). Rincian 16 pertemuan, kegiatan, dan penilaian.</p>
        </div>
        <span class="pill pill--soft"><?= esc($item['total_meetings']) ?> pertemuan</span>
    </div>

    <div class="table-wrap">
        <table class="plan-table" data-materi-base="<?= esc(site_url('rps/' . $item['id'] . '/materi'), 'attr') ?>">
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
                    <?php $week = (int) ($meeting['week'] ?? 0); ?>
                    <tr>
                        <td><span class="week"><?= esc($week) ?></span></td>
                        <td>
                            <button type="button"
                                    class="materi-trigger"
                                    data-materi-week="<?= esc($week, 'attr') ?>"
                                    aria-haspopup="dialog"
                                    aria-controls="materi-drawer">
                                <span class="materi-trigger__text">
                                    <span class="topic"><?= esc($meeting['topic']) ?></span>
                                    <span class="materials"><?= esc($meeting['materials']) ?></span>
                                </span>
                                <span class="materi-trigger__icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </button>
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

<?= $this->include('rps/_materi_drawer') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    (function () {
        'use strict';

        document.querySelectorAll('[data-print]').forEach(function (button) {
            button.addEventListener('click', function () {
                window.print();
            });
        });

        var drawer = document.getElementById('materi-drawer');
        var backdrop = document.querySelector('[data-materi-backdrop]');
        var closeBtn = document.querySelector('[data-materi-close]');
        var copyBtn = document.querySelector('[data-materi-copy]');
        var downloadBtn = document.querySelector('[data-materi-download]');
        var body = document.querySelector('[data-materi-body]');
        var titleEl = document.querySelector('[data-materi-title]');
        var metaEl = document.querySelector('[data-materi-meta]');
        var table = document.querySelector('[data-materi-base]');

        if (!drawer || !body || !table) {
            return;
        }

        var baseUrl = table.getAttribute('data-materi-base') || '';
        var lastTrigger = null;
        var currentData = null;
        var currentRequest = 0;

        function setText(el, text) {
            if (!el) return;
            el.textContent = text == null ? '' : String(text);
        }

        function clear(node) {
            while (node.firstChild) {
                node.removeChild(node.firstChild);
            }
        }

        function el(tag, className, text) {
            var node = document.createElement(tag);
            if (className) {
                node.className = className;
            }
            if (text != null) {
                node.textContent = text;
            }
            return node;
        }

        function showSkeleton() {
            clear(body);
            var wrap = el('div', 'materi-skeleton');
            var status = el('div', 'materi-skeleton__status');
            var spark = el('span', 'materi-skeleton__spark');
            spark.setAttribute('aria-hidden', 'true');
            status.appendChild(spark);
            status.appendChild(document.createTextNode('AI sedang menyusun materi...'));
            wrap.appendChild(status);
            var widths = ['92%', '100%', '76%', '100%', '58%', '100%', '84%', '64%'];
            widths.forEach(function (w) {
                var line = el('div', 'materi-skeleton__line');
                line.style.width = w;
                wrap.appendChild(line);
            });
            body.appendChild(wrap);
        }

        function renderError(message) {
            clear(body);
            var box = el('div', 'materi-error');
            box.appendChild(el('strong', null, 'Materi tidak dapat dimuat'));
            box.appendChild(el('p', null, message || 'Terjadi kesalahan. Silakan coba lagi.'));
            body.appendChild(box);
        }

        function renderData(data) {
            clear(body);

            if (data.summary) {
                var summary = el('section', 'materi-section materi-section--summary');
                summary.appendChild(el('h3', 'materi-section__title', 'Ringkasan'));
                summary.appendChild(el('p', 'materi-summary', data.summary));
                body.appendChild(summary);
            }

            if (Array.isArray(data.objectives) && data.objectives.length) {
                var obj = el('section', 'materi-section');
                obj.appendChild(el('h3', 'materi-section__title', 'Tujuan sesi'));
                var ul = el('ul', 'materi-list');
                data.objectives.forEach(function (item) {
                    ul.appendChild(el('li', null, item));
                });
                obj.appendChild(ul);
                body.appendChild(obj);
            }

            if (Array.isArray(data.sections) && data.sections.length) {
                var mat = el('section', 'materi-section');
                mat.appendChild(el('h3', 'materi-section__title', 'Materi inti'));
                data.sections.forEach(function (sec) {
                    var block = el('article', 'materi-block');
                    if (sec.heading) {
                        block.appendChild(el('h4', 'materi-block__heading', sec.heading));
                    }
                    if (Array.isArray(sec.paragraphs)) {
                        sec.paragraphs.forEach(function (p) {
                            if (p) block.appendChild(el('p', null, p));
                        });
                    }
                    if (Array.isArray(sec.points) && sec.points.length) {
                        var pul = el('ul', 'materi-list materi-list--compact');
                        sec.points.forEach(function (pt) {
                            pul.appendChild(el('li', null, pt));
                        });
                        block.appendChild(pul);
                    }
                    mat.appendChild(block);
                });
                body.appendChild(mat);
            }

            if (Array.isArray(data.key_terms) && data.key_terms.length) {
                var kt = el('section', 'materi-section');
                kt.appendChild(el('h3', 'materi-section__title', 'Istilah kunci'));
                var grid = el('div', 'materi-terms');
                data.key_terms.forEach(function (term) {
                    var card = el('div', 'materi-term');
                    card.appendChild(el('strong', 'materi-term__name', term.term || ''));
                    card.appendChild(el('span', 'materi-term__def', term.definition || ''));
                    grid.appendChild(card);
                });
                kt.appendChild(grid);
                body.appendChild(kt);
            }

            if (Array.isArray(data.activities) && data.activities.length) {
                var act = el('section', 'materi-section');
                act.appendChild(el('h3', 'materi-section__title', 'Aktivitas pembelajaran'));
                var ol = el('ol', 'materi-steps');
                data.activities.forEach(function (item) {
                    var li = el('li', 'materi-step');
                    var text = (item && typeof item === 'object') ? (item.step || '') : String(item || '');
                    var mins = (item && typeof item === 'object' && item.minutes != null) ? item.minutes : null;
                    li.appendChild(el('span', 'materi-step__text', text));
                    if (mins != null) {
                        li.appendChild(el('span', 'materi-step__time', mins + ' menit'));
                    }
                    ol.appendChild(li);
                });
                act.appendChild(ol);
                body.appendChild(act);
            }

            if (Array.isArray(data.quiz) && data.quiz.length) {
                var qz = el('section', 'materi-section');
                qz.appendChild(el('h3', 'materi-section__title', 'Kuis pemahaman'));
                data.quiz.forEach(function (item, idx) {
                    var details = document.createElement('details');
                    details.className = 'materi-quiz';
                    var summaryEl = document.createElement('summary');
                    summaryEl.className = 'materi-quiz__q';
                    summaryEl.textContent = (idx + 1) + '. ' + (item.question || '');
                    details.appendChild(summaryEl);
                    var ans = el('div', 'materi-quiz__a', item.answer || '');
                    details.appendChild(ans);
                    qz.appendChild(details);
                });
                body.appendChild(qz);
            }

            if (data.assessment || data.indicator) {
                var as = el('section', 'materi-section');
                as.appendChild(el('h3', 'materi-section__title', 'Asesmen & indikator'));
                if (data.indicator) {
                    var ip = el('p', null);
                    var il = el('strong', null, 'Indikator: ');
                    ip.appendChild(il);
                    ip.appendChild(document.createTextNode(String(data.indicator)));
                    as.appendChild(ip);
                }
                if (data.assessment) {
                    as.appendChild(el('p', null, 'Teknik/instrumen: ' + data.assessment));
                }
                body.appendChild(as);
            }

            if (Array.isArray(data.references) && data.references.length) {
                var ref = el('section', 'materi-section');
                ref.appendChild(el('h3', 'materi-section__title', 'Referensi'));
                var olr = el('ol', 'materi-refs');
                data.references.forEach(function (r) {
                    olr.appendChild(el('li', null, r));
                });
                ref.appendChild(olr);
                body.appendChild(ref);
            }

            var note = el('p', 'materi-note', 'Konten ini dibuat otomatis (dummy AI) dari data RPS dan dapat ditinjau serta disesuaikan oleh guru.');
            body.appendChild(note);
        }

        function openDrawer(trigger) {
            lastTrigger = trigger || null;
            drawer.classList.add('is-open');
            drawer.setAttribute('aria-hidden', 'false');
            if (backdrop) {
                backdrop.hidden = false;
                backdrop.classList.add('is-visible');
            }
            document.body.classList.add('materi-open');
            if (closeBtn) {
                closeBtn.focus();
            }
        }

        function closeDrawer() {
            drawer.classList.remove('is-open');
            drawer.setAttribute('aria-hidden', 'true');
            if (backdrop) {
                backdrop.classList.remove('is-visible');
                backdrop.hidden = true;
            }
            document.body.classList.remove('materi-open');
            if (lastTrigger && typeof lastTrigger.focus === 'function') {
                lastTrigger.focus();
            }
        }

        function loadMateri(week, trigger) {
            var reqId = ++currentRequest;
            currentData = null;
            setText(titleEl, 'Materi pertemuan ' + week);
            setText(metaEl, 'Memuat informasi pertemuan...');
            if (copyBtn) copyBtn.disabled = true;
            if (downloadBtn) downloadBtn.disabled = true;
            showSkeleton();
            openDrawer(trigger);

            fetch(baseUrl + '/' + encodeURIComponent(week), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            }).then(function (res) {
                if (reqId !== currentRequest) return null;
                if (res.status === 401) {
                    window.location.href = '<?= site_url('login') ?>';
                    return null;
                }
                if (!res.ok) {
                    return res.json().catch(function () { return {}; }).then(function (err) {
                        throw new Error(err && err.error ? err.error : 'Gagal memuat materi (HTTP ' + res.status + ').');
                    });
                }
                return res.json();
            }).then(function (data) {
                if (!data || reqId !== currentRequest) return;
                currentData = data;
                setText(titleEl, data.topic || ('Pertemuan ' + week));
                var metaParts = ['Pertemuan ' + (data.week || week)];
                if (data.method) metaParts.push(data.method);
                if (data.estimated_minutes) metaParts.push(data.estimated_minutes + ' menit');
                metaParts.push('Dummy AI');
                setText(metaEl, metaParts.join(' · '));
                renderData(data);
                if (copyBtn) copyBtn.disabled = false;
                if (downloadBtn) downloadBtn.disabled = false;
            }).catch(function (err) {
                if (reqId !== currentRequest) return;
                setText(metaEl, 'Pertemuan ' + week);
                renderError(err && err.message ? err.message : 'Terjadi kesalahan saat memuat materi.');
            });
        }

        function toPlainText(data) {
            if (!data) return '';
            var lines = [];
            var push = function (s) { if (s != null && s !== '') lines.push(String(s)); };
            push((data.topic || 'Materi Pembelajaran') + ' (Pertemuan ' + (data.week || '-') + ')');
            var meta = [];
            if (data.method) meta.push('Metode: ' + data.method);
            if (data.estimated_minutes) meta.push('Estimasi: ' + data.estimated_minutes + ' menit');
            if (meta.length) push(meta.join(' | '));
            push('');
            if (data.summary) { push('RINGKASAN'); push(data.summary); push(''); }
            if (Array.isArray(data.objectives) && data.objectives.length) {
                push('TUJUAN SESI');
                data.objectives.forEach(function (o, i) { push((i + 1) + '. ' + o); });
                push('');
            }
            if (Array.isArray(data.sections) && data.sections.length) {
                push('MATERI INTI');
                data.sections.forEach(function (sec) {
                    if (sec.heading) push('- ' + sec.heading);
                    if (Array.isArray(sec.paragraphs)) sec.paragraphs.forEach(push);
                    if (Array.isArray(sec.points)) sec.points.forEach(function (pt) { push('  • ' + pt); });
                    push('');
                });
            }
            if (Array.isArray(data.key_terms) && data.key_terms.length) {
                push('ISTILAH KUNCI');
                data.key_terms.forEach(function (t) { push('- ' + (t.term || '') + ': ' + (t.definition || '')); });
                push('');
            }
            if (Array.isArray(data.activities) && data.activities.length) {
                push('AKTIVITAS PEMBELAJARAN');
                data.activities.forEach(function (a, i) {
                    var text = (a && typeof a === 'object') ? (a.step || '') : String(a || '');
                    var mins = (a && typeof a === 'object' && a.minutes != null) ? ' (' + a.minutes + ' menit)' : '';
                    push((i + 1) + '. ' + text + mins);
                });
                push('');
            }
            if (Array.isArray(data.quiz) && data.quiz.length) {
                push('KUIS PEMAHAMAN');
                data.quiz.forEach(function (q, i) {
                    push((i + 1) + '. ' + (q.question || ''));
                    push('   Jawaban: ' + (q.answer || ''));
                });
                push('');
            }
            if (data.assessment) { push('ASESMEN: ' + data.assessment); }
            if (data.indicator) { push('INDIKATOR: ' + data.indicator); }
            if (Array.isArray(data.references) && data.references.length) {
                push('');
                push('REFERENSI');
                data.references.forEach(function (r, i) { push((i + 1) + '. ' + r); });
            }
            push('');
            push('[Konten dummy AI – dibuat dari data RPS]');
            return lines.join('\n');
        }

        document.querySelectorAll('[data-materi-week]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var week = btn.getAttribute('data-materi-week');
                if (week) {
                    loadMateri(week, btn);
                }
            });
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', closeDrawer);
        }
        if (backdrop) {
            backdrop.addEventListener('click', closeDrawer);
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && drawer.classList.contains('is-open')) {
                closeDrawer();
            }
        });

        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                if (!currentData) return;
                var text = toPlainText(currentData);
                var done = function (ok) {
                    var original = copyBtn.getAttribute('data-label') || copyBtn.textContent;
                    if (!copyBtn.getAttribute('data-label')) copyBtn.setAttribute('data-label', original);
                    copyBtn.textContent = ok ? 'Tersalin' : 'Gagal menyalin';
                    setTimeout(function () { copyBtn.textContent = copyBtn.getAttribute('data-label') || 'Salin materi'; }, 1600);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(text).then(function () { done(true); }, function () { done(false); });
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = text;
                    ta.setAttribute('readonly', '');
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    try { done(document.execCommand('copy')); } catch (err) { done(false); }
                    document.body.removeChild(ta);
                }
            });
        }

        if (downloadBtn) {
            downloadBtn.addEventListener('click', function () {
                if (!currentData) return;
                var text = toPlainText(currentData);
                var blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var a = document.createElement('a');
                var week = currentData.week || 'materi';
                a.href = url;
                a.download = 'materi-pertemuan-' + week + '.txt';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
            });
        }
    })();
</script>
<?= $this->endSection() ?>
