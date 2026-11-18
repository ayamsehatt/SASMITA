<?php

namespace App\Libraries;

/**
 * Generator materi ajar dummy (bergaya AI) untuk setiap pertemuan RPS.
 *
 * Konten dibuat secara deterministik dari data RPS yang sudah ada di sesi,
 * tanpa memanggil layanan AI dan tanpa disimpan ke basis data.
 */
class MateriGenerator
{
    /**
     * @param array<string, mixed> $rps     Data RPS lengkap dari sesi.
     * @param array<string, mixed> $meeting Baris pertemuan dari $rps['meetings'].
     *
     * @return array<string, mixed>
     */
    public function generate(array $rps, array $meeting): array
    {
        $week      = (int) ($meeting['week'] ?? 0);
        $title     = (string) ($rps['title'] ?? 'Materi Pembelajaran');
        $topic     = (string) ($meeting['topic'] ?? $title);
        $materials = (string) ($meeting['materials'] ?? '');
        $method    = (string) ($meeting['method'] ?? ($rps['method'] ?? 'Diskusi kelompok'));
        $assessment = (string) ($meeting['assessment'] ?? 'Observasi');
        $indicator = (string) ($meeting['indicator'] ?? 'Ketercapaian tujuan pembelajaran');
        $activities = array_values(array_filter(
            array_map('strval', $meeting['activities'] ?? []),
            static fn (string $v): bool => $v !== ''
        ));

        if ($week === 8 || $week === 16) {
            return $this->examContent($week, $title, $topic, $materials, $method, $assessment, $indicator, $activities, $rps);
        }

        return $this->regularContent($week, $title, $topic, $materials, $method, $assessment, $indicator, $activities, $rps);
    }

    /**
     * @param list<string>         $activities
     * @param array<string, mixed> $rps
     *
     * @return array<string, mixed>
     */
    private function regularContent(
        int $week,
        string $title,
        string $topic,
        string $materials,
        string $method,
        string $assessment,
        string $indicator,
        array $activities,
        array $rps
    ): array {
        $minutes = $this->estimatedMinutes($week, $activities);

        return [
            'week'              => $week,
            'topic'             => $topic,
            'method'            => $method,
            'assessment'        => $assessment,
            'estimated_minutes' => $minutes,
            'summary'           => sprintf(
                'Pada pertemuan %d, peserta didik mempelajari "%s" dalam konteks %s. '
                . 'Sesi ini menekankan pemahaman konsep melalui %s, dengan fokus pada materi: %s. '
                . 'Hasil belajar diarahkan agar peserta didik mampu menunjukkan capaian: %s.',
                $week,
                $topic,
                $title,
                $method,
                $materials !== '' ? $materials : $topic,
                rtrim($indicator, '.')
            ),
            'objectives'        => $this->objectives($title, $topic, $indicator),
            'sections'          => $this->sections($title, $topic, $materials, $week),
            'key_terms'         => $this->keyTerms($title, $topic, $materials),
            'activities'        => $this->activitySteps($activities, $method, $minutes),
            'quiz'              => $this->quiz($title, $topic, $materials, $indicator),
            'references'        => $this->references($rps, $topic),
            'generated_at'      => date('c'),
            'is_exam'           => false,
        ];
    }

    /**
     * @param list<string>         $activities
     * @param array<string, mixed> $rps
     *
     * @return array<string, mixed>
     */
    private function examContent(
        int $week,
        string $title,
        string $topic,
        string $materials,
        string $method,
        string $assessment,
        string $indicator,
        array $activities,
        array $rps
    ): array {
        $isMid  = $week === 8;
        $label  = $isMid ? 'Asesmen Tengah Semester (UTS)' : 'Asesmen Akhir Semester (UAS)';
        $scope  = $isMid ? 'pertemuan 1–7' : 'keseluruhan materi semester (pertemuan 1–15)';
        $minutes = $isMid ? 90 : 100;

        return [
            'week'              => $week,
            'topic'             => $topic,
            'method'            => $method,
            'assessment'        => $assessment,
            'estimated_minutes' => $minutes,
            'summary'           => sprintf(
                '%s untuk materi "%s" bertujuan mengukur ketercapaian pembelajaran pada %s. '
                . 'Asesmen mencakup pemahaman konsep, kemampuan analisis, dan penerapan pengetahuan. '
                . 'Guru perlu menyiapkan instrumen, rubrik penilaian, serta umpan balik yang konstruktif bagi peserta didik.',
                $label,
                $title,
                $scope
            ),
            'objectives'        => [
                'Mengukur pemahaman peserta didik terhadap konsep-konsep kunci pada ' . $scope . '.',
                'Menilai kemampuan menganalisis dan menerapkan pengetahuan dalam konteks ' . $title . '.',
                'Memberikan umpan balik yang jelas untuk perbaikan dan tindak lanjut pembelajaran.',
            ],
            'sections'          => [
                [
                    'heading'    => 'Cakupan dan fokus asesmen',
                    'paragraphs' => [
                        sprintf('Asesmen ini mencakup %s dengan penekanan pada indikator: %s.', $scope, rtrim($indicator, '.')),
                        'Soal dan tugas dirancang untuk menilai tingkat pemahaman, bukan sekadar hafalan, sehingga peserta didik perlu menunjukkan proses berpikir dan alasan atas jawaban yang diberikan.',
                    ],
                    'points'     => [
                        'Pemahaman konsep dasar dan istilah kunci.',
                        'Kemampuan menghubungkan konsep dengan contoh nyata.',
                        'Ketepatan analisis dan kualitas argumen dalam jawaban.',
                    ],
                ],
                [
                    'heading'    => 'Panduan pelaksanaan',
                    'paragraphs' => [
                        'Guru menyampaikan tujuan asesmen, alokasi waktu, dan kriteria penilaian sebelum pelaksanaan dimulai. Pastikan suasana kelas kondusif dan instruksi dipahami seluruh peserta didik.',
                        $isMid
                            ? 'Setelah asesmen, lakukan pembahasan singkat soal-soal yang paling banyak keliru untuk memperkuat pemahaman sebelum melanjutkan ke materi berikutnya.'
                            : 'Setelah asesmen, berikan refleksi akhir semester dan rekomendasi tindak lanjut agar peserta didik mengetahui capaian serta area pengembangan mereka.',
                    ],
                    'points'     => $activities !== [] ? $activities : [
                        'Pembukaan dan penjelasan teknis asesmen.',
                        'Pelaksanaan asesmen sesuai instrumen.',
                        'Pengumpulan hasil dan penyampaian umpan balik awal.',
                    ],
                ],
                [
                    'heading'    => 'Kriteria dan tindak lanjut',
                    'paragraphs' => [
                        'Penilaian mengacu pada rubrik yang selaras dengan indikator pembelajaran. Catat pola kesalahan umum untuk menjadi bahan perbaikan pada pertemuan atau semester berikutnya.',
                    ],
                    'points'     => [
                        'Sangat baik: jawaban tepat, alasan jelas, dan contoh relevan.',
                        'Baik: jawaban tepat namun alasan atau contoh perlu diperdalam.',
                        'Perlu bimbingan: terdapat miskonsepsi atau jawaban belum lengkap.',
                    ],
                ],
            ],
            'key_terms'         => [
                [
                    'term'       => $isMid ? 'Asesmen tengah semester' : 'Asesmen akhir semester',
                    'definition' => $isMid
                        ? 'Penilaian tengah periode untuk memotret capaian sementara dan memberi umpan balik perbaikan.'
                        : 'Penilaian akhir periode untuk menentukan ketercapaian tujuan pembelajaran secara keseluruhan.',
                ],
                ['term' => 'Rubrik penilaian', 'definition' => 'Pedoman penilaian yang memuat kriteria dan tingkat capaian untuk menilai hasil belajar secara objektif.'],
                ['term' => 'Umpan balik', 'definition' => 'Informasi spesifik kepada peserta didik mengenai kekuatan dan area yang perlu ditingkatkan.'],
                ['term' => 'Tindak lanjut', 'definition' => 'Rencana perbaikan atau pengayaan berdasarkan hasil asesmen yang diperoleh.'],
            ],
            'activities'        => $this->activitySteps(
                $activities !== [] ? $activities : [
                    'Persiapan instrumen dan penjelasan teknis asesmen',
                    'Pelaksanaan asesmen sesuai waktu yang ditentukan',
                    'Pengumpulan, penilaian, dan penyampaian umpan balik',
                ],
                $method,
                $minutes
            ),
            'quiz'              => [
                [
                    'question' => 'Apa tujuan utama pelaksanaan ' . $label . ' pada materi "' . $title . '"?',
                    'answer'   => 'Untuk mengukur ketercapaian tujuan pembelajaran pada ' . $scope . ' serta memberikan dasar umpan balik dan tindak lanjut bagi peserta didik.',
                ],
                [
                    'question' => 'Mengapa rubrik penilaian penting digunakan dalam asesmen ini?',
                    'answer'   => 'Karena rubrik membuat penilaian lebih objektif, transparan, dan membantu peserta didik memahami kriteria keberhasilan yang diharapkan.',
                ],
                [
                    'question' => 'Langkah apa yang perlu dilakukan guru setelah hasil asesmen diperoleh?',
                    'answer'   => 'Menganalisis hasil, mengidentifikasi pola kesalahan, memberikan umpan balik, dan merencanakan remedial atau pengayaan sesuai kebutuhan.',
                ],
            ],
            'references'        => $this->references($rps, $topic),
            'generated_at'      => date('c'),
            'is_exam'           => true,
        ];
    }

    /**
     * @param list<string> $activities
     */
    private function estimatedMinutes(int $week, array $activities): int
    {
        $base = 2 * 45; // 2 JP
        $extra = min(count($activities), 4) * 5;

        return $base + $extra;
    }

    /**
     * @return list<string>
     */
    private function objectives(string $title, string $topic, string $indicator): array
    {
        $focus = rtrim($indicator, '.');

        return [
            'Peserta didik mampu menjelaskan konsep utama pada topik "' . $topic . '" dalam konteks ' . $title . '.',
            'Peserta didik mampu mengaitkan materi pembelajaran dengan contoh atau situasi nyata yang relevan.',
            'Peserta didik mampu menunjukkan capaian: ' . $focus . '.',
        ];
    }

    /**
     * @return list<array{heading: string, paragraphs: list<string>, points: list<string>}>
     */
    private function sections(string $title, string $topic, string $materials, int $week): array
    {
        $materialText = $materials !== '' ? $materials : $topic;

        return [
            [
                'heading'    => 'Konsep dan konteks',
                'paragraphs' => [
                    sprintf('Topik "%s" merupakan bagian penting dalam pembelajaran %s. Pada tahap ini, guru membantu peserta didik membangun pemahaman awal yang kuat tentang istilah, gagasan utama, dan keterkaitan antar konsep.', $topic, $title),
                    'Pemahaman konsep yang baik akan menjadi dasar bagi peserta didik untuk menganalisis persoalan dan menerapkan pengetahuan pada pertemuan berikutnya.',
                ],
                'points'     => [
                    'Mengidentifikasi istilah dan konsep kunci yang berkaitan dengan ' . $topic . '.',
                    'Menjelaskan hubungan antara konsep yang dipelajari dengan tujuan pembelajaran ' . $title . '.',
                    'Mengaitkan konsep dengan pengalaman atau contoh dari lingkungan sekitar.',
                ],
            ],
            [
                'heading'    => 'Pendalaman materi',
                'paragraphs' => [
                    'Fokus materi pada pertemuan ini: ' . $materialText . '. Guru dapat menyajikan contoh konkret, mengajukan pertanyaan pemantik, dan memfasilitasi diskusi agar peserta didik aktif mengonstruksi pemahaman.',
                    'Dorong peserta didik untuk membandingkan beberapa sudut pandang, menemukan pola, dan menarik kesimpulan berdasarkan bukti atau informasi yang tersedia.',
                ],
                'points'     => [
                    'Uraian langkah atau prosedur yang relevan dengan materi.',
                    'Contoh penerapan dan non-contoh untuk memperjelas konsep.',
                    'Kesalahan umum yang perlu diantisipasi beserta cara memperbaikinya.',
                ],
            ],
            [
                'heading'    => 'Penerapan dan refleksi',
                'paragraphs' => [
                    'Setelah memahami materi, peserta didik diajak menerapkan pengetahuan melalui latihan, studi kasus, atau tugas sederhana. Tahap ini membantu menguatkan pemahaman sekaligus memunculkan pertanyaan lanjutan.',
                    'Akhiri sesi dengan refleksi singkat: apa yang sudah dipahami, apa yang masih membingungkan, dan bagaimana materi ini berguna dalam konteks nyata.',
                ],
                'points'     => [
                    'Latihan terarah yang selaras dengan indikator capaian pertemuan.',
                    'Diskusi hasil kerja untuk memperkaya pemahaman bersama.',
                    'Rencana tindak lanjut atau pengayaan untuk pertemuan ke-' . ($week + 1) . '.',
                ],
            ],
        ];
    }

    /**
     * @return list<array{term: string, definition: string}>
     */
    private function keyTerms(string $title, string $topic, string $materials): array
    {
        $source = $materials !== '' ? $materials : $topic;

        return [
            [
                'term'       => 'Konsep kunci',
                'definition' => 'Gagasan utama yang menjadi fondasi pemahaman pada topik "' . $topic . '" dalam materi ' . $title . '.',
            ],
            [
                'term'       => 'Konteks pembelajaran',
                'definition' => 'Situasi atau latar nyata tempat pengetahuan diterapkan, sehingga materi menjadi bermakna bagi peserta didik.',
            ],
            [
                'term'       => 'Indikator capaian',
                'definition' => 'Ukuran yang menunjukkan sejauh mana peserta didik telah mencapai tujuan pembelajaran pada pertemuan ini.',
            ],
            [
                'term'       => 'Refleksi belajar',
                'definition' => 'Proses meninjau kembali apa yang telah dipelajari, kesulitan yang ditemui, dan langkah perbaikan ke depan.',
            ],
            [
                'term'       => 'Materi inti',
                'definition' => 'Pokok bahasan utama yang dibahas pada sesi ini: ' . $source . '.',
            ],
        ];
    }

    /**
     * @param list<string> $activities
     *
     * @return list<array{step: string, minutes: int}>
     */
    private function activitySteps(array $activities, string $method, int $totalMinutes): array
    {
        if ($activities === []) {
            $activities = [
                'Pendahuluan dan apersepsi',
                'Kegiatan inti dengan ' . $method,
                'Penutup dan refleksi',
            ];
        }

        $count = count($activities);
        $base  = (int) floor($totalMinutes / $count);
        $rem   = $totalMinutes - ($base * $count);
        $steps = [];

        foreach ($activities as $i => $label) {
            $steps[] = [
                'step'    => $label,
                'minutes' => $base + ($i < $rem ? 1 : 0),
            ];
        }

        return $steps;
    }

    /**
     * @return list<array{question: string, answer: string}>
     */
    private function quiz(string $title, string $topic, string $materials, string $indicator): array
    {
        $materialText = $materials !== '' ? $materials : $topic;

        return [
            [
                'question' => 'Jelaskan gagasan utama yang dipelajari pada topik "' . $topic . '".',
                'answer'   => 'Gagasan utamanya berkaitan dengan ' . $materialText . ' dalam konteks ' . $title . ', termasuk pemahaman konsep dasar dan keterkaitannya dengan tujuan pembelajaran.',
            ],
            [
                'question' => 'Berikan satu contoh penerapan materi pertemuan ini dalam situasi nyata.',
                'answer'   => 'Contoh penerapan dapat dilakukan dengan mengaitkan ' . $topic . ' pada permasalahan atau aktivitas sehari-hari yang relevan, lalu menjelaskan bagaimana konsep yang dipelajari membantu memahami atau menyelesaikannya.',
            ],
            [
                'question' => 'Bagaimana pertemuan ini membantu mencapai indikator: "' . rtrim($indicator, '.') . '"?',
                'answer'   => 'Melalui kegiatan pembelajaran, diskusi, dan latihan yang terarah, peserta didik berlatih menunjukkan kemampuan sesuai indikator tersebut sehingga capaian belajarnya dapat diamati dan dinilai.',
            ],
        ];
    }

    /**
     * @param array<string, mixed> $rps
     *
     * @return list<string>
     */
    private function references(array $rps, string $topic): array
    {
        $refs = [];

        foreach (($rps['references'] ?? []) as $ref) {
            $ref = trim((string) $ref);
            if ($ref !== '') {
                $refs[] = $ref;
            }
        }

        $refs[] = 'Catatan dan bahan ajar guru untuk topik: ' . $topic . '.';
        $refs[] = 'Sumber belajar pendukung yang relevan dan terverifikasi sesuai konteks materi.';

        return array_values(array_unique($refs));
    }
}
