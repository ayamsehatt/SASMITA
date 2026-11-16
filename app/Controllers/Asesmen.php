<?php

namespace App\Controllers;

use App\Libraries\AsesmenPdf;
use CodeIgniter\HTTP\RedirectResponse;

class Asesmen extends BaseController
{
    /**
     * Helper yang dipakai di view (old(), site_url(), base_url()).
     *
     * @var list<string>
     */
    protected $helpers = ['form', 'url'];

    private const TYPES = [
        'pg'    => 'Pilihan ganda',
        'esai'  => 'Esai / uraian',
        'bs'    => 'Benar / salah',
        'isian' => 'Isian singkat',
    ];

    /**
     * Daftar draft asesmen. Kartu draft disusun di sisi browser dari localStorage.
     */
    public function index()
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $user = session('user') ?? [];

        return view('asesmen/index', [
            'title'       => 'Draft Asesmen · SASMITA',
            'active'      => 'asesmen',
            'crumb'       => 'Asesmen',
            'teacherName' => (string) ($user['name'] ?? ''),
            'missing'     => $this->request->getGet('missing') === '1',
        ]);
    }

    /**
     * Builder untuk draft asesmen baru.
     */
    public function newDraft()
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $user = session('user') ?? [];

        return view('asesmen/form', [
            'title'       => 'Draft Baru · SASMITA',
            'active'      => 'asesmen',
            'crumb'       => 'Asesmen / Draft baru',
            'teacherName' => (string) ($user['name'] ?? ''),
            'draftId'     => '',
            'isNew'       => true,
        ]);
    }

    /**
     * Builder untuk membuka dan mengedit draft asesmen yang sudah ada.
     */
    public function edit($id = null)
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $id = trim((string) $id);

        if ($id === '' || ! preg_match('/^[A-Za-z0-9_-]{1,64}$/', $id)) {
            return redirect()->to(site_url('asesmen?missing=1'));
        }

        $user = session('user') ?? [];

        return view('asesmen/form', [
            'title'       => 'Edit Draft · SASMITA',
            'active'      => 'asesmen',
            'crumb'       => 'Asesmen / Edit draft',
            'teacherName' => (string) ($user['name'] ?? ''),
            'draftId'     => $id,
            'isNew'       => false,
        ]);
    }

    /**
     * Validasi input guru lalu kembalikan berkas PDF asesmen.
     */
    public function pdf()
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $data   = $this->collect();
        $errors = $this->validateAsesmen($data);

        if ($errors !== []) {
            $draftId = trim((string) $this->request->getPost('draft_id'));
            $target  = preg_match('/^[A-Za-z0-9_-]{1,64}$/', $draftId)
                ? 'asesmen/' . $draftId
                : 'asesmen/baru';

            return redirect()
                ->to(site_url($target))
                ->withInput()
                ->with('errors', $errors);
        }

        $asesmen = $this->buildForTemplate($data);
        $pdf     = new AsesmenPdf();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $pdf->filename($asesmen) . '"')
            ->setBody($pdf->render($asesmen));
    }

    /**
     * Ambil data mentah dari formulir dan rapikan bentuknya.
     *
     * @return array<string, mixed>
     */
    private function collect(): array
    {
        $rawQuestions = $this->request->getPost('questions');
        $questions    = [];

        if (is_array($rawQuestions)) {
            foreach (array_values($rawQuestions) as $raw) {
                if (! is_array($raw)) {
                    continue;
                }

                $type     = (string) ($raw['type'] ?? 'pg');
                $text     = trim((string) ($raw['text'] ?? ''));
                $points   = trim((string) ($raw['points'] ?? '1'));
                $answer   = $raw['answer'] ?? '';
                $answer   = is_array($answer) ? '' : trim((string) $answer);
                $rubric   = trim((string) ($raw['rubric'] ?? ''));
                $options  = $this->normalizeOptions($raw['options'] ?? []);

                // Kartu yang benar-benar kosong diabaikan agar tidak menghalangi
                // guru yang menambah baris soal lalu mengosongkannya kembali.
                if ($text === '' && $answer === '' && $rubric === '' && $options === []) {
                    continue;
                }

                $questions[] = [
                    'type'    => $type,
                    'text'    => $text,
                    'points'  => $points,
                    'options' => $options,
                    'answer'  => $answer,
                    'rubric'  => $rubric,
                ];
            }
        }

        return [
            'title'        => trim((string) $this->request->getPost('title')),
            'subject'      => trim((string) $this->request->getPost('subject')),
            'class'        => trim((string) $this->request->getPost('class')),
            'teacher'      => trim((string) $this->request->getPost('teacher')),
            'time'         => trim((string) $this->request->getPost('time')),
            'instructions' => trim((string) $this->request->getPost('instructions')),
            'questions'    => $questions,
        ];
    }

    /**
     * Buang opsi kosong di bagian akhir, tetapi pertahankan yang ada di tengah
     * agar bisa dilaporkan sebagai kesalahan.
     *
     * @param mixed $raw
     *
     * @return list<string>
     */
    private function normalizeOptions($raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        $options = [];

        foreach ($raw as $option) {
            $options[] = trim((string) $option);
        }

        while ($options !== [] && end($options) === '') {
            array_pop($options);
        }

        return $options;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<string>
     */
    private function validateAsesmen(array $data): array
    {
        $errors = [];

        $title   = (string) $data['title'];
        $subject = (string) $data['subject'];
        $class   = (string) $data['class'];
        $teacher = (string) $data['teacher'];
        $time    = (string) $data['time'];
        $notes   = (string) $data['instructions'];

        if ($title === '') {
            $errors[] = 'Judul asesmen wajib diisi.';
        } elseif (mb_strlen($title) < 3 || mb_strlen($title) > 150) {
            $errors[] = 'Judul asesmen harus 3–150 karakter.';
        }

        if ($subject === '') {
            $errors[] = 'Mata pelajaran wajib diisi.';
        } elseif (mb_strlen($subject) < 2 || mb_strlen($subject) > 80) {
            $errors[] = 'Mata pelajaran harus 2–80 karakter.';
        }

        if ($class === '') {
            $errors[] = 'Kelas wajib diisi.';
        } elseif (mb_strlen($class) > 40) {
            $errors[] = 'Kelas maksimal 40 karakter.';
        }

        if ($teacher === '') {
            $errors[] = 'Nama guru wajib diisi.';
        } elseif (mb_strlen($teacher) < 2 || mb_strlen($teacher) > 100) {
            $errors[] = 'Nama guru harus 2–100 karakter.';
        }

        if (mb_strlen($time) > 60) {
            $errors[] = 'Alokasi waktu maksimal 60 karakter.';
        }

        if (mb_strlen($notes) > 2000) {
            $errors[] = 'Petunjuk pengerjaan maksimal 2.000 karakter.';
        }

        /** @var list<array<string, mixed>> $questions */
        $questions = $data['questions'];

        if ($questions === []) {
            $errors[] = 'Tambahkan minimal satu soal sebelum mengunduh PDF.';
        } elseif (count($questions) > 100) {
            $errors[] = 'Maksimal 100 soal dalam satu asesmen.';
        }

        foreach ($questions as $index => $question) {
            $number = $index + 1;
            $type   = (string) $question['type'];
            $text   = (string) $question['text'];
            $points = (string) $question['points'];

            if (! isset(self::TYPES[$type])) {
                $errors[] = "Soal {$number}: jenis soal tidak dikenali.";
                continue;
            }

            if ($text === '') {
                $errors[] = "Soal {$number}: teks soal wajib diisi.";
            } elseif (mb_strlen($text) < 3 || mb_strlen($text) > 2000) {
                $errors[] = "Soal {$number}: teks soal harus 3–2.000 karakter.";
            }

            $pointValue = filter_var($points, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1, 'max_range' => 100],
            ]);

            if ($pointValue === false) {
                $errors[] = "Soal {$number}: poin harus berupa angka 1–100.";
            }

            if ($type === 'pg') {
                /** @var list<string> $options */
                $options = $question['options'];
                $answer  = (string) $question['answer'];

                if (count($options) < 2) {
                    $errors[] = "Soal {$number}: pilihan ganda membutuhkan minimal 2 opsi jawaban.";
                } elseif (count($options) > 6) {
                    $errors[] = "Soal {$number}: pilihan ganda maksimal 6 opsi jawaban.";
                } elseif (in_array('', $options, true)) {
                    $errors[] = "Soal {$number}: semua opsi jawaban harus diisi.";
                }

                if (! ctype_digit($answer) || ! isset($options[(int) $answer])) {
                    $errors[] = "Soal {$number}: pilih kunci jawaban yang benar.";
                }
            } elseif ($type === 'bs') {
                if (! in_array((string) $question['answer'], ['B', 'S'], true)) {
                    $errors[] = "Soal {$number}: tentukan kunci Benar atau Salah.";
                }
            } elseif ($type === 'isian') {
                if ((string) $question['answer'] === '') {
                    $errors[] = "Soal {$number}: isi jawaban yang diharapkan.";
                }
            }
        }

        return $errors;
    }

    /**
     * Siapkan data final untuk template PDF.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function buildForTemplate(array $data): array
    {
        $questions  = [];
        $totalPoint = 0;

        /** @var list<array<string, mixed>> $rawQuestions */
        $rawQuestions = $data['questions'];

        foreach ($rawQuestions as $index => $question) {
            $type   = (string) $question['type'];
            $points = (int) $question['points'];
            $answer = (string) $question['answer'];

            $item = [
                'number' => $index + 1,
                'type'   => $type,
                'label'  => self::TYPES[$type],
                'text'   => (string) $question['text'],
                'points' => $points,
                'rubric' => (string) $question['rubric'],
                'options' => [],
                'answer'  => '',
            ];

            if ($type === 'pg') {
                $letters = [];

                foreach ($question['options'] as $position => $option) {
                    $letters[] = [
                        'letter' => chr(65 + (int) $position),
                        'text'   => (string) $option,
                    ];
                }

                $item['options'] = $letters;
                $item['answer']  = chr(65 + (int) $answer);
            } elseif ($type === 'bs') {
                $item['answer'] = $answer === 'B' ? 'Benar' : 'Salah';
            } elseif ($type === 'isian') {
                $item['answer'] = $answer;
            }

            $totalPoint += $points;
            $questions[] = $item;
        }

        return [
            'title'        => (string) $data['title'],
            'subject'      => (string) $data['subject'],
            'class'        => (string) $data['class'],
            'teacher'      => (string) $data['teacher'],
            'time'         => (string) $data['time'],
            'instructions' => (string) $data['instructions'],
            'questions'    => $questions,
            'total_points' => $totalPoint,
            'total_items'  => count($questions),
            'generated_at' => $this->dateLabel(),
        ];
    }

    private function dateLabel(): string
    {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        return date('j') . ' ' . $months[(int) date('n')] . ' ' . date('Y');
    }

    private function guard(): ?RedirectResponse
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()
                ->to(site_url('login'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        return null;
    }
}
