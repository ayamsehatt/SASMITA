<?php

namespace App\Controllers;

use App\Libraries\RpsGenerator;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Rps extends BaseController
{
    /**
     * @var list<string>
     */
    protected $helpers = ['form', 'url'];

    private const SESSION_KEY = 'rps_list';

    /**
     * Daftar RPS milik guru.
     */
    public function index()
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $this->seedDemo();

        /** @var list<array<string, mixed>> $items */
        $items = session()->get(self::SESSION_KEY) ?? [];

        $meetings   = 0;
        $sources    = 0;
        $indicators = 0;

        foreach ($items as $item) {
            $meetings += (int) ($item['total_meetings'] ?? 0);
            $sources += isset($item['source']) ? 1 : 0;
            $indicators += count($item['indicators'] ?? []);
        }

        return view('rps/index', [
            'title'  => 'RPS & Materi · SASMITA',
            'active' => 'rps',
            'items'  => $items,
            'stats'  => [
                'rps'        => count($items),
                'meetings'   => $meetings,
                'sources'    => $sources,
                'indicators' => $indicators,
            ],
        ]);
    }

    /**
     * Formulir pembuatan RPS.
     */
    public function create()
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        return view('rps/create', [
            'title'  => 'Buat RPS · SASMITA',
            'active' => 'rps',
        ]);
    }

    /**
     * Simpan input guru lalu hasilkan RPS dummy.
     */
    public function store()
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $rules = [
            'title' => [
                'label'  => 'Judul materi',
                'rules'  => 'required|min_length[5]|max_length[180]',
                'errors' => [
                    'required'   => 'Judul materi wajib diisi.',
                    'min_length' => 'Judul materi minimal 5 karakter.',
                    'max_length' => 'Judul materi maksimal 180 karakter.',
                ],
            ],
            'objective' => [
                'label'  => 'Tujuan pembelajaran',
                'rules'  => 'required|min_length[20]',
                'errors' => [
                    'required'   => 'Tujuan pembelajaran wajib diisi.',
                    'min_length' => 'Tuliskan tujuan pembelajaran yang lebih lengkap (minimal 20 karakter).',
                ],
            ],
            'indicators' => [
                'label'  => 'Indikator penilaian',
                'rules'  => 'required|min_length[10]',
                'errors' => [
                    'required'   => 'Indikator penilaian wajib diisi.',
                    'min_length' => 'Tuliskan indikator penilaian yang lebih lengkap (minimal 10 karakter).',
                ],
            ],
            'method' => [
                'label'  => 'Metode pembelajaran',
                'rules'  => 'permit_empty|max_length[140]',
                'errors' => [
                    'max_length' => 'Metode pembelajaran maksimal 140 karakter.',
                ],
            ],
        ];

        // Aturan file hanya dipasang jika guru benar-benar mengunggah berkas.
        // `permit_empty` tidak dipakai karena nilai field file tidak ada di
        // data POST sehingga akan melewati seluruh aturan validasi file.
        $file = $this->request->getFile('source');

        if ($file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['source'] = [
                'label'  => 'Sumber relevan',
                'rules'  => 'uploaded[source]|max_size[source,10240]|ext_in[source,pdf,doc,docx,ppt,pptx,txt,md]',
                'errors' => [
                    'uploaded' => 'Berkas gagal diunggah. Silakan coba lagi.',
                    'max_size' => 'Ukuran file maksimal 10 MB.',
                    'ext_in'   => 'Format file harus PDF, DOC, DOCX, PPT, PPTX, TXT, atau MD.',
                ],
            ];
        }

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $source = $this->storeUpload();

        $generator = new RpsGenerator();
        $plan      = $generator->generate([
            'title'      => (string) $this->request->getPost('title'),
            'objective'  => (string) $this->request->getPost('objective'),
            'indicators' => (string) $this->request->getPost('indicators'),
            'method'     => (string) $this->request->getPost('method'),
        ], $source);

        /** @var list<array<string, mixed>> $items */
        $items = session()->get(self::SESSION_KEY) ?? [];
        array_unshift($items, $plan);
        session()->set(self::SESSION_KEY, array_slice($items, 0, 25));

        return redirect()
            ->to(site_url('rps/' . $plan['id']))
            ->with('success', 'RPS berhasil disusun. Tinjau hasilnya sebelum digunakan.');
    }

    /**
     * Detail RPS hasil generate.
     */
    public function show(string $id)
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $item = $this->find($id);

        if ($item === null) {
            throw PageNotFoundException::forPageNotFound('RPS tidak ditemukan.');
        }

        return view('rps/show', [
            'title'  => $item['title'] . ' · SASMITA',
            'active' => 'rps',
            'item'   => $item,
        ]);
    }

    /**
     * Unduh sumber yang diunggah guru.
     */
    public function downloadSource(string $id)
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $item = $this->find($id);
        $file = $item['source']['stored'] ?? null;

        if ($file === null) {
            return redirect()
                ->to(site_url('rps/' . $id))
                ->with('error', 'File sumber tidak tersedia.');
        }

        $path = WRITEPATH . 'uploads/rps/' . $file;

        if (! is_file($path)) {
            return redirect()
                ->to(site_url('rps/' . $id))
                ->with('error', 'File sumber sudah tidak ada di server.');
        }

        return $this->response->download($path, $item['source']['name'] ?? null);
    }

    /**
     * Hapus RPS dari daftar.
     */
    public function delete(string $id)
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        /** @var list<array<string, mixed>> $items */
        $items = session()->get(self::SESSION_KEY) ?? [];
        $items = array_values(array_filter(
            $items,
            static fn (array $item): bool => ($item['id'] ?? '') !== $id
        ));

        session()->set(self::SESSION_KEY, $items);

        return redirect()
            ->to(site_url('rps'))
            ->with('success', 'RPS berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function find(string $id): ?array
    {
        /** @var list<array<string, mixed>> $items */
        $items = session()->get(self::SESSION_KEY) ?? [];

        foreach ($items as $item) {
            if (($item['id'] ?? '') === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @return array{name: string, size: int, stored: string|null}|null
     */
    private function storeUpload(): ?array
    {
        $file = $this->request->getFile('source');

        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $directory = WRITEPATH . 'uploads/rps';

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $stored = $file->getRandomName();
        $file->move($directory, $stored);

        return [
            'name'   => $file->getClientName(),
            'size'   => $file->getSize(),
            'stored' => $stored,
        ];
    }

    /**
     * Isi daftar dengan dua contoh agar fitur bisa langsung dicoba.
     */
    private function seedDemo(): void
    {
        if (session()->has(self::SESSION_KEY)) {
            return;
        }

        $generator = new RpsGenerator();

        $first = $generator->generate([
            'title'      => 'Berpikir Kritis dalam Pembelajaran IPS',
            'objective'  => "Peserta didik mampu menganalisis permasalahan sosial di lingkungan sekitar.\nPeserta didik mampu menyusun argumen yang didukung bukti.",
            'indicators' => "Ketepatan analisis masalah\nKualitas argumen dan bukti\nKemampuan presentasi kelompok",
            'method'     => 'Problem Based Learning',
        ], [
            'name'   => 'Modul_Berpikir_Kritis.pdf',
            'size'   => 2411724,
            'stored' => null,
        ]);

        $first['status']        = 'Siap';
        $first['created_at']    = date('Y-m-d H:i:s', time() - (2 * DAY));
        $first['created_label'] = $this->dateLabel(time() - (2 * DAY));

        $second = $generator->generate([
            'title'      => 'Literasi Numerasi melalui Data Nyata',
            'objective'  => "Peserta didik mampu membaca dan menafsirkan data dalam bentuk tabel serta diagram.\nPeserta didik mampu mengambil keputusan berdasarkan data.",
            'indicators' => "Ketepatan membaca data\nKemampuan menafsirkan diagram\nKualitas kesimpulan berbasis data",
            'method'     => 'Project Based Learning',
        ]);

        $second['status']        = 'Draft';
        $second['created_at']    = date('Y-m-d H:i:s', time() - (5 * DAY));
        $second['created_label'] = $this->dateLabel(time() - (5 * DAY));

        session()->set(self::SESSION_KEY, [$first, $second]);
    }

    private function dateLabel(int $timestamp): string
    {
        $months = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        return date('j', $timestamp) . ' ' . $months[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
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
