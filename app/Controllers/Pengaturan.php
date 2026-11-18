<?php

namespace App\Controllers;

use App\Libraries\ProfilGuru;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

class Pengaturan extends BaseController
{
    /**
     * @var list<string>
     */
    protected $helpers = ['form', 'url'];

    /**
     * Halaman pengaturan profil guru.
     */
    public function index()
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $user    = session('user') ?? [];
        $email   = (string) ($user['email'] ?? '');
        $role    = (string) ($user['role'] ?? 'Guru');
        $session = (string) ($user['name'] ?? 'Guru');
        $nama    = ProfilGuru::nama($session);

        return view('pengaturan/index', [
            'title'       => 'Pengaturan · SASMITA',
            'active'      => 'pengaturan',
            'crumb'       => 'Pengaturan',
            'nama'        => $nama,
            'email'       => $email,
            'role'        => $role,
            'initial'     => $this->initial($nama),
            'teacherName' => $nama,
        ]);
    }

    /**
     * Simpan perubahan profil guru.
     */
    public function update(): RedirectResponse
    {
        if ($redirect = $this->guard()) {
            return $redirect;
        }

        $input = (string) $this->request->getPost('nama_tampilan');
        $nama  = ProfilGuru::normalisasi($input);
        $error = ProfilGuru::validasi($nama);

        if ($error !== null) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $error);
        }

        try {
            $saved = ProfilGuru::simpanNama($nama);
        } catch (Throwable $e) {
            log_message('error', 'Gagal menyimpan profil guru: {message}', ['message' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Profil tidak dapat disimpan. Pastikan database tersedia, lalu coba lagi.');
        }

        $user         = session('user') ?? [];
        $user['name'] = $saved;
        session()->set('user', $user);

        return redirect()
            ->to(site_url('pengaturan'))
            ->with('success', 'Profil berhasil diperbarui.');
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

    private function initial(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            return 'G';
        }

        if (function_exists('mb_substr')) {
            return mb_strtoupper(mb_substr($name, 0, 1));
        }

        return strtoupper(substr($name, 0, 1));
    }
}
