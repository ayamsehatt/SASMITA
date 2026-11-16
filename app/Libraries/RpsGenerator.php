<?php

namespace App\Libraries;

/**
 * Penyusun RPS & materi (dummy AI).
 *
 * Kelas ini menghasilkan struktur RPS 16 pertemuan berdasarkan input guru.
 * Saat ini isinya dibuat dengan aturan template, bukan memanggil layanan AI.
 * Nanti bagian generate() bisa diganti dengan pemanggilan API model bahasa
 * tanpa mengubah tampilan maupun controller.
 */
class RpsGenerator
{
    /**
     * Topik tiap pertemuan. {judul} akan diganti dengan judul materi.
     *
     * @var list<array{topic: string, activities: list<string>, assessment: string}>
     */
    private array $weeks = [
        [
            'topic'      => 'Pengantar & kontrak belajar materi {judul}',
            'activities' => ['Apersepsi dan pemetaan pengetahuan awal', 'Penyampaian tujuan dan alur pembelajaran', 'Kesepakatan aturan serta tugas'],
            'assessment' => 'Tes diagnostik singkat & observasi partisipasi',
        ],
        [
            'topic'      => 'Konsep dasar {judul}',
            'activities' => ['Eksplorasi definisi dan istilah kunci', 'Diskusi kelompok kecil', 'Penyusunan peta konsep'],
            'assessment' => 'Peta konsep & kuis konsep dasar',
        ],
        [
            'topic'      => 'Ruang lingkup dan prinsip {judul}',
            'activities' => ['Ceramah interaktif', 'Analisis contoh nyata', 'Latihan identifikasi prinsip'],
            'assessment' => 'Lembar kerja identifikasi prinsip',
        ],
        [
            'topic'      => 'Analisis kebutuhan dan konteks penerapan',
            'activities' => ['Studi kasus dari lingkungan sekitar', 'Diskusi terarah', 'Presentasi hasil analisis'],
            'assessment' => 'Rubrik analisis kasus',
        ],
        [
            'topic'      => 'Prosedur dan langkah kerja',
            'activities' => ['Demonstrasi langkah kerja', 'Praktik terbimbing', 'Umpan balik per kelompok'],
            'assessment' => 'Unjuk kerja & daftar cek prosedur',
        ],
        [
            'topic'      => 'Studi kasus 1: penerapan {judul}',
            'activities' => ['Pembagian peran dalam kelompok', 'Penyelesaian kasus', 'Diskusi solusi alternatif'],
            'assessment' => 'Laporan singkat kelompok',
        ],
        [
            'topic'      => 'Latihan terbimbing & pendalaman',
            'activities' => ['Latihan mandiri', 'Konsultasi kesulitan belajar', 'Pembahasan kesalahan umum'],
            'assessment' => 'Latihan soal & refleksi belajar',
        ],
        [
            'topic'      => 'Asesmen Tengah Semester',
            'activities' => ['Asesmen sumatif tengah semester', 'Umpan balik hasil asesmen'],
            'assessment' => 'Soal uraian & studi kasus',
        ],
        [
            'topic'      => 'Pendalaman materi dan koneksi antar konsep',
            'activities' => ['Peta ulang konsep', 'Diskusi keterkaitan antar topik', 'Penyusunan pertanyaan esensial'],
            'assessment' => 'Presentasi peta konsep lanjutan',
        ],
        [
            'topic'      => 'Perencanaan proyek mini',
            'activities' => ['Penentuan tema proyek', 'Penyusunan rencana kerja', 'Penetapan pembagian tugas'],
            'assessment' => 'Proposal proyek & rubrik perencanaan',
        ],
        [
            'topic'      => 'Pelaksanaan proyek mini',
            'activities' => ['Pengumpulan data/informasi', 'Bimbingan progres proyek', 'Penyusunan produk'],
            'assessment' => 'Catatan progres & produk antara',
        ],
        [
            'topic'      => 'Presentasi dan saling umpan balik',
            'activities' => ['Presentasi hasil proyek', 'Tanya jawab antar kelompok', 'Umpan balik terstruktur'],
            'assessment' => 'Rubrik presentasi & penilaian sejawat',
        ],
        [
            'topic'      => 'Refleksi dan perbaikan hasil',
            'activities' => ['Refleksi capaian belajar', 'Perbaikan produk berdasarkan masukan', 'Penentuan langkah tindak lanjut'],
            'assessment' => 'Jurnal refleksi',
        ],
        [
            'topic'      => 'Integrasi dan penerapan dalam konteks baru',
            'activities' => ['Transfer konsep ke situasi baru', 'Diskusi tantangan penerapan', 'Studi kasus lanjutan'],
            'assessment' => 'Studi kasus integratif',
        ],
        [
            'topic'      => 'Persiapan asesmen akhir',
            'activities' => ['Rangkuman materi', 'Latihan soal akhir', 'Klinik kesulitan belajar'],
            'assessment' => 'Latihan asesmen akhir',
        ],
        [
            'topic'      => 'Asesmen Akhir Semester',
            'activities' => ['Asesmen sumatif akhir semester', 'Umpan balik dan rencana tindak lanjut'],
            'assessment' => 'Soal uraian, studi kasus, & refleksi akhir',
        ],
    ];

    /**
     * @var list<string>
     */
    private array $materialAngles = [
        'Definisi, istilah kunci, dan ruang lingkup materi.',
        'Prinsip dan teori pendukung yang relevan.',
        'Prosedur, langkah kerja, dan contoh penerapan.',
        'Studi kasus kontekstual dan analisis kesalahan umum.',
        'Koneksi dengan materi lain serta penerapan di lapangan.',
    ];

    /**
     * @param array{title: string, objective: string, indicators: string, method?: string} $input
     * @param array{name: string, size: int, stored: string|null}|null                        $source
     *
     * @return array<string, mixed>
     */
    public function generate(array $input, ?array $source = null): array
    {
        $title      = trim($input['title']);
        $objectives = $this->toLines($input['objective']);
        $indicators = $this->toLines($input['indicators']);
        $method     = trim((string) ($input['method'] ?? '')) ?: 'Diskusi kelompok & Problem Based Learning';
        $meetings   = $this->buildMeetings($title, $method, $indicators);
        $references = $this->buildReferences($title, $source);

        return [
            'id'                => bin2hex(random_bytes(8)),
            'code'              => 'RPS-' . date('Ym') . '-' . strtoupper(substr(bin2hex(random_bytes(2)), 0, 4)),
            'title'             => $title,
            'objective'         => $objectives,
            'indicators'        => $indicators,
            'method'            => $method,
            'source'            => $source,
            'status'            => 'Siap',
            'created_at'        => date('Y-m-d H:i:s'),
            'created_label'     => $this->formatDate(),
            'summary'           => $this->buildSummary($title, $objectives, $method),
            'capaian'           => $this->buildCapaian($title),
            'meetings'          => $meetings,
            'material_outline'  => $this->buildMaterialOutline($title),
            'assessment_plan'   => $this->buildAssessmentPlan($indicators),
            'references'        => $references,
            'total_meetings'    => count($meetings),
        ];
    }

    /**
     * @param list<string> $indicators
     *
     * @return list<array<string, mixed>>
     */
    private function buildMeetings(string $title, string $method, array $indicators): array
    {
        $meetings = [];

        foreach ($this->weeks as $index => $week) {
            $isExam = in_array($index + 1, [8, 16], true);
            $topic  = str_replace('{judul}', $title, $week['topic']);

            $meetings[] = [
                'week'       => $index + 1,
                'topic'      => $topic,
                'activities' => $week['activities'],
                'method'     => $isExam ? 'Asesmen tertulis' : $this->variantMethod($method, $index),
                'assessment' => $week['assessment'],
                'materials'  => $this->materialFor($title, $index),
                'indicator'  => $indicators[$index % max(count($indicators), 1)] ?? 'Ketercapaian tujuan pembelajaran',
            ];
        }

        return $meetings;
    }

    private function variantMethod(string $method, int $index): string
    {
        $variants = [
            'Diskusi kelompok',
            'Problem Based Learning',
            'Project Based Learning',
            'Praktik terbimbing',
            'Presentasi & umpan balik',
        ];

        if ($index % 3 === 0) {
            return $variants[$index % count($variants)];
        }

        return $method;
    }

    private function materialFor(string $title, int $index): string
    {
        $angles = [
            'Pengantar ' . $title . ': definisi, istilah kunci, dan tujuan pembelajaran.',
            'Teori dan prinsip pendukung ' . $title . '.',
            'Prosedur kerja dan contoh penerapan ' . $title . '.',
            'Analisis kasus ' . $title . ' dan pembahasan kesalahan umum.',
            'Pendalaman ' . $title . ': koneksi konsep dan penerapan kontekstual.',
        ];

        return $angles[$index % count($angles)];
    }

    /**
     * @param list<string> $objective
     */
    private function buildSummary(string $title, array $objective, string $method): string
    {
        $focus = $objective[0] ?? 'memahami materi secara utuh';

        return sprintf(
            'RPS untuk materi "%s" disusun dalam 16 pertemuan dengan pendekatan %s. '
            . 'Fokus utama pembelajaran adalah %s. Setiap pertemuan dilengkapi kegiatan, '
            . 'materi ajar, dan instrumen penilaian yang dapat langsung ditinjau guru sebelum digunakan.',
            $title,
            $method,
            rtrim($focus, '.')
        );
    }

    /**
     * @return list<string>
     */
    private function buildCapaian(string $title): array
    {
        return [
            'Peserta didik mampu menjelaskan konsep dasar ' . $title . ' secara sistematis.',
            'Peserta didik mampu menganalisis permasalahan yang berkaitan dengan ' . $title . '.',
            'Peserta didik mampu menerapkan ' . $title . ' pada situasi nyata melalui proyek sederhana.',
            'Peserta didik mampu mengomunikasikan hasil belajar dan refleksi secara santun.',
        ];
    }

    /**
     * @return list<string>
     */
    private function buildMaterialOutline(string $title): array
    {
        return array_map(
            static fn (string $angle): string => $angle . ' (' . $title . ')',
            $this->materialAngles
        );
    }

    /**
     * @param list<string> $indicators
     *
     * @return list<array{indicator: string, technique: string, instrument: string}>
     */
    private function buildAssessmentPlan(array $indicators): array
    {
        $techniques  = ['Observasi', 'Unjuk kerja', 'Tes tertulis', 'Penilaian proyek', 'Penilaian diri'];
        $instruments = ['Lembar observasi', 'Rubrik unjuk kerja', 'Soal uraian', 'Rubrik proyek', 'Jurnal refleksi'];
        $plan        = [];

        foreach ($indicators as $index => $indicator) {
            $plan[] = [
                'indicator'  => $indicator,
                'technique'  => $techniques[$index % count($techniques)],
                'instrument' => $instruments[$index % count($instruments)],
            ];
        }

        if ($plan === []) {
            $plan[] = [
                'indicator'  => 'Ketercapaian tujuan pembelajaran',
                'technique'  => 'Observasi',
                'instrument' => 'Lembar observasi',
            ];
        }

        return $plan;
    }

    /**
     * @param array{name: string, size: int, stored: string|null}|null $source
     *
     * @return list<string>
     */
    private function buildReferences(string $title, ?array $source): array
    {
        $references = [];

        if ($source !== null) {
            $references[] = 'Sumber terlampir: ' . $source['name'];
        }

        $references[] = 'Buku ajar dan modul pendukung materi ' . $title . '.';
        $references[] = 'Artikel jurnal pendidikan terkait ' . $title . ' (5 tahun terakhir).';
        $references[] = 'Sumber digital terverifikasi dari perpustakaan sekolah.';

        return $references;
    }

    /**
     * Ubah teks multi-baris menjadi daftar poin yang rapi.
     *
     * @return list<string>
     */
    private function toLines(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];

        $lines = array_map(static function (string $line): string {
            $line = trim($line);
            $line = preg_replace('/^[-*•\d.)\s]+/u', '', $line) ?? $line;

            return trim($line);
        }, $lines);

        return array_values(array_filter($lines, static fn (string $line): bool => $line !== ''));
    }

    private function formatDate(): string
    {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        return date('j') . ' ' . $months[(int) date('n')] . ' ' . date('Y');
    }
}
