<div class="materi-backdrop" data-materi-backdrop hidden></div>

<aside id="materi-drawer"
       class="materi-drawer"
       role="dialog"
       aria-modal="true"
       aria-labelledby="materi-drawer-title"
       aria-hidden="true">
    <header class="materi-drawer__head">
        <div class="materi-drawer__heading">
            <div class="materi-drawer__tags">
                <span class="pill pill--soft">Materi &amp; Panduan</span>
                <span class="pill pill--soft materi-badge-ai">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
                    </svg>
                    Dummy AI
                </span>
            </div>
            <h2 id="materi-drawer-title" class="materi-drawer__title" data-materi-title>Materi pertemuan</h2>
            <p class="materi-drawer__meta" data-materi-meta>Memuat informasi pertemuan...</p>
        </div>
        <button type="button" class="materi-drawer__close" data-materi-close aria-label="Tutup panel materi">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>
    </header>

    <div class="materi-drawer__body" data-materi-body>
        <div class="materi-skeleton">
            <div class="materi-skeleton__status">
                <span class="materi-skeleton__spark" aria-hidden="true"></span>
                AI sedang menyusun materi...
            </div>
            <div class="materi-skeleton__line" style="width:92%"></div>
            <div class="materi-skeleton__line" style="width:100%"></div>
            <div class="materi-skeleton__line" style="width:76%"></div>
            <div class="materi-skeleton__line" style="width:100%"></div>
            <div class="materi-skeleton__line" style="width:58%"></div>
        </div>
    </div>

    <footer class="materi-drawer__foot">
        <span class="materi-drawer__footnote">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 3l1.9 4.8L18.7 9.7l-4.8 1.9L12 16.4l-1.9-4.8L5.3 9.7l4.8-1.9L12 3z" fill="currentColor"/>
            </svg>
            Dihasilkan otomatis (dummy) · tinjau sebelum digunakan
        </span>
        <div class="materi-drawer__actions">
            <button type="button" class="btn btn--ghost btn--sm" data-materi-copy disabled>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="9" y="9" width="11" height="11" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M15 9V6.5A2.5 2.5 0 0 0 12.5 4h-6A2.5 2.5 0 0 0 4 6.5v6A2.5 2.5 0 0 0 6.5 15H9" stroke="currentColor" stroke-width="1.7"/>
                </svg>
                Salin materi
            </button>
            <button type="button" class="btn btn--primary btn--sm" data-materi-download disabled>
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 4.5v10M7.5 10L12 14.5 16.5 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4.5 16v2A2.5 2.5 0 0 0 7 20.5h10a2.5 2.5 0 0 0 2.5-2.5v-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
                Unduh .txt
            </button>
        </div>
    </footer>
</aside>
