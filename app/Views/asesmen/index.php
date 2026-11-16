<?= $this->extend('layouts/app') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/asesmen.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-head asesmen-head">
    <div>
        <span class="page-head__eyebrow">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M7 4.8h7.5L19 9.3v9.9a1.8 1.8 0 0 1-1.8 1.8H7a1.8 1.8 0 0 1-1.8-1.8V6.6A1.8 1.8 0 0 1 7 4.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                <path d="M14 5v4.5h4.5M9 13h6M9 16.5h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            </svg>
            Asesmen pembelajaran
        </span>
        <h1>Draft asesmen</h1>
        <p>
            Setiap kartu berisi satu paket asesmen lengkap: identitas, daftar soal, dan kunci
            jawaban. Buka kartu untuk mengedit, atau buat draft baru untuk mulai menyusun.
        </p>
    </div>

    <div class="asesmen-head__actions">
        <a class="btn btn--primary" href="<?= site_url('asesmen/baru') ?>">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
            </svg>
            Tambah draft soal baru
        </a>
    </div>
</div>

<?php if (! empty($missing)) : ?>
    <div class="alert alert--error" role="alert">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
            <path d="M12 8v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <circle cx="12" cy="16.4" r="1" fill="currentColor"/>
        </svg>
        <p>Draft yang Anda cari tidak ditemukan di browser ini. Silakan pilih draft lain atau buat draft baru.</p>
    </div>
<?php endif; ?>

<div class="draft-toolbar" data-draft-toolbar hidden>
    <div class="draft-toolbar__info">
        <strong data-draft-total>0</strong>
        <span>draft tersimpan di browser ini</span>
    </div>

    <a class="btn btn--ghost btn--sm" href="<?= site_url('asesmen/baru') ?>">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
        </svg>
        Tambah draft
    </a>
</div>

<p class="draft-loading" data-draft-loading>Memuat draft asesmen…</p>

<div class="draft-grid" data-draft-grid hidden></div>

<div class="empty-state" data-draft-empty hidden>
    <span class="empty-state__icon">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M7 4.8h7.5L19 9.3v9.9a1.8 1.8 0 0 1-1.8 1.8H7a1.8 1.8 0 0 1-1.8-1.8V6.6A1.8 1.8 0 0 1 7 4.8z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M14 5v4.5h4.5M9 13h6M9 16.5h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
        </svg>
    </span>
    <h3>Belum ada draft asesmen</h3>
    <p>
        Mulai dari draft pertama Anda. Isi identitas asesmen dan soal, lalu tekan Simpan.
        Draft akan tersimpan di browser ini dan bisa dibuka lagi kapan saja.
    </p>
    <a class="btn btn--primary" href="<?= site_url('asesmen/baru') ?>">
        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.1" stroke-linecap="round"/>
        </svg>
        Tambah draft soal baru
    </a>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.__ASESMEN_URLS__ = {
        list: <?= json_encode(site_url('asesmen'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>,
        pdf: <?= json_encode(site_url('asesmen/pdf'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>
    };
</script>
<script>
    (function () {
        'use strict';

        var DRAFT_KEY = 'sasmita.asesmen.drafts.v2';
        var LEGACY_KEY = 'sasmita.asesmen.draft.v1';
        var urls = window.__ASESMEN_URLS__ || {};
        var grid = document.querySelector('[data-draft-grid]');
        var empty = document.querySelector('[data-draft-empty]');
        var toolbar = document.querySelector('[data-draft-toolbar]');
        var loading = document.querySelector('[data-draft-loading]');
        var totalLabel = document.querySelector('[data-draft-total]');

        if (! grid) {
            return;
        }

        function each(selector, scope, callback) {
            var root = scope || document;
            Array.prototype.forEach.call(root.querySelectorAll(selector), callback);
        }

        function readDrafts() {
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

        function writeDrafts(drafts) {
            try {
                localStorage.setItem(DRAFT_KEY, JSON.stringify(drafts));
                return true;
            } catch (error) {
                return false;
            }
        }

        function draftHasContent(draft) {
            if (! draft) {
                return false;
            }

            if (['title', 'subject', 'class', 'time', 'instructions'].some(function (key) {
                return String(draft[key] || '').trim() !== '';
            })) {
                return true;
            }

            return (Array.isArray(draft.questions) ? draft.questions : []).some(function (question) {
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
            });
        }

        function migrateLegacy() {
            var drafts = readDrafts();

            if (drafts.length > 0) {
                return drafts;
            }

            var stored = null;

            try {
                stored = JSON.parse(localStorage.getItem(LEGACY_KEY) || 'null');
            } catch (error) {
                stored = null;
            }

            if (! stored || typeof stored !== 'object' || ! draftHasContent(stored)) {
                try {
                    localStorage.removeItem(LEGACY_KEY);
                } catch (error) {
                    // abaikan
                }

                return drafts;
            }

            var now = Date.now();
            var draft = {
                id: 'd' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8),
                title: String(stored.title || ''),
                subject: String(stored.subject || ''),
                class: String(stored.class || ''),
                teacher: String(stored.teacher || ''),
                time: String(stored.time || ''),
                instructions: String(stored.instructions || ''),
                questions: Array.isArray(stored.questions) ? stored.questions : [],
                createdAt: now,
                updatedAt: now
            };

            if (writeDrafts([draft])) {
                try {
                    localStorage.removeItem(LEGACY_KEY);
                } catch (error) {
                    // abaikan
                }

                return [draft];
            }

            return drafts;
        }

        function draftQuestions(draft) {
            return (Array.isArray(draft.questions) ? draft.questions : []).filter(function (question) {
                return question && typeof question === 'object';
            });
        }

        function totalPoints(questions) {
            var total = 0;

            questions.forEach(function (question) {
                var points = parseInt(question.points, 10);

                if (points > 0) {
                    total += points;
                }
            });

            return total;
        }

        function firstQuestionText(questions) {
            for (var i = 0; i < questions.length; i++) {
                var text = String(questions[i].text || '').trim();

                if (text !== '') {
                    return text;
                }
            }

            return '';
        }

        function formatDate(timestamp) {
            var date = new Date(parseInt(timestamp, 10) || 0);

            if (isNaN(date.getTime())) {
                return 'baru saja';
            }

            try {
                return date.toLocaleString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (error) {
                return date.toLocaleString();
            }
        }

        function editUrl(id) {
            return String(urls.list || '') + '/' + encodeURIComponent(String(id));
        }

        function appendField(form, name, value) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value == null ? '' : String(value);
            form.appendChild(input);
        }

        function downloadDraft(draft) {
            var form = document.createElement('form');
            form.method = 'post';
            form.action = urls.pdf || '';
            form.style.display = 'none';

            appendField(form, 'draft_id', draft.id);
            appendField(form, 'title', draft.title);
            appendField(form, 'subject', draft.subject);
            appendField(form, 'class', draft['class']);
            appendField(form, 'teacher', draft.teacher);
            appendField(form, 'time', draft.time);
            appendField(form, 'instructions', draft.instructions);

            draftQuestions(draft).forEach(function (question, index) {
                var prefix = 'questions[' + index + ']';

                appendField(form, prefix + '[type]', question.type);
                appendField(form, prefix + '[text]', question.text);
                appendField(form, prefix + '[points]', question.points);
                appendField(form, prefix + '[answer]', question.answer);
                appendField(form, prefix + '[rubric]', question.rubric);

                (Array.isArray(question.options) ? question.options : []).forEach(function (option) {
                    appendField(form, prefix + '[options][]', option);
                });
            });

            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }

        function removeDraft(id) {
            var drafts = readDrafts().filter(function (draft) {
                return String(draft.id) !== String(id);
            });

            if (! writeDrafts(drafts)) {
                window.alert('Draft gagal dihapus dari penyimpanan browser ini.');
                return;
            }

            render();
        }

        function makeElement(tag, className, text) {
            var element = document.createElement(tag);

            if (className) {
                element.className = className;
            }

            if (text != null) {
                element.textContent = text;
            }

            return element;
        }

        function makeCard(draft) {
            var questions = draftQuestions(draft);
            var points = totalPoints(questions);
            var article = makeElement('article', 'draft-card');
            article.setAttribute('data-draft-id', draft.id);

            var top = makeElement('div', 'draft-card__top');
            top.appendChild(makeElement('span', 'draft-card__badge', 'Draft asesmen'));
            top.appendChild(makeElement('span', 'draft-card__date', 'Diperbarui ' + formatDate(draft.updatedAt)));
            article.appendChild(top);

            var title = makeElement('h3');
            var link = makeElement('a', null, String(draft.title || '').trim() || 'Draf tanpa judul');
            link.href = editUrl(draft.id);
            title.appendChild(link);
            article.appendChild(title);

            var subject = String(draft.subject || '').trim();
            var className = String(draft['class'] || '').trim();
            var identity = [subject, className].filter(function (part) {
                return part !== '';
            }).join(' · ');

            article.appendChild(makeElement(
                'p',
                'draft-card__meta' + (identity === '' ? ' is-empty' : ''),
                identity === '' ? 'Identitas asesmen belum diisi' : identity
            ));

            var stats = makeElement('div', 'draft-card__stats');

            var countStat = makeElement('span', 'draft-stat');
            countStat.appendChild(makeElement('strong', null, String(questions.length)));
            countStat.appendChild(document.createTextNode(' '));
            countStat.appendChild(makeElement('span', null, 'soal'));
            stats.appendChild(countStat);

            var pointStat = makeElement('span', 'draft-stat');
            pointStat.appendChild(makeElement('strong', null, String(points)));
            pointStat.appendChild(document.createTextNode(' '));
            pointStat.appendChild(makeElement('span', null, 'poin'));
            stats.appendChild(pointStat);

            if (String(draft.teacher || '').trim() !== '') {
                var teacherStat = makeElement('span', 'draft-stat');
                teacherStat.appendChild(makeElement('span', null, String(draft.teacher).trim()));
                stats.appendChild(teacherStat);
            }

            article.appendChild(stats);

            var previewText = firstQuestionText(questions);
            article.appendChild(makeElement(
                'p',
                'draft-card__preview' + (previewText === '' ? ' is-empty' : ''),
                previewText === '' ? 'Belum ada soal' : previewText
            ));

            var foot = makeElement('div', 'draft-card__foot');

            var open = makeElement('a', 'link-arrow', 'Buka / Edit');
            open.href = editUrl(draft.id);
            open.setAttribute('aria-label', 'Buka atau edit draft ' + (String(draft.title || '').trim() || 'tanpa judul'));
            foot.appendChild(open);

            var actions = makeElement('div', 'draft-card__actions');

            var download = makeElement('button', 'draft-action', 'Unduh PDF');
            download.type = 'button';
            download.setAttribute('data-download', '');
            actions.appendChild(download);

            var remove = makeElement('button', 'icon-btn', null);
            remove.type = 'button';
            remove.setAttribute('data-delete', '');
            remove.setAttribute('aria-label', 'Hapus draft');
            remove.title = 'Hapus draft';
            remove.innerHTML = '<svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4.5 7h15M9.5 7V4.8h5V7M6.5 7l.8 12.2a1.8 1.8 0 0 0 1.8 1.7h5.8a1.8 1.8 0 0 0 1.8-1.7L17.5 7M10 11v5.5M14 11v5.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            actions.appendChild(remove);

            foot.appendChild(actions);
            article.appendChild(foot);

            article.querySelector('[data-download]').addEventListener('click', function () {
                downloadDraft(draft);
            });

            article.querySelector('[data-delete]').addEventListener('click', function () {
                if (window.confirm('Hapus draft "' + (String(draft.title || '').trim() || 'Draf tanpa judul') + '" dari browser ini?')) {
                    removeDraft(draft.id);
                }
            });

            return article;
        }

        function render() {
            var drafts = readDrafts().slice().sort(function (a, b) {
                return (parseInt(b.updatedAt, 10) || 0) - (parseInt(a.updatedAt, 10) || 0);
            });

            grid.innerHTML = '';

            drafts.forEach(function (draft) {
                grid.appendChild(makeCard(draft));
            });

            grid.hidden = drafts.length === 0;
            empty.hidden = drafts.length !== 0;
            toolbar.hidden = drafts.length === 0;

            if (totalLabel) {
                totalLabel.textContent = String(drafts.length);
            }
        }

        migrateLegacy();
        render();

        if (loading) {
            loading.hidden = true;
        }
    })();
</script>
<?= $this->endSection() ?>
