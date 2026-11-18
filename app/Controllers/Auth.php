<?php

namespace App\Controllers;

use App\Libraries\ProfilGuru;
use Config\Sasmita;

class Auth extends BaseController
{
    /**
     * Helper yang dipakai di view (old(), site_url(), base_url()).
     *
     * @var list<string>
     */
    protected $helpers = ['form', 'url'];

    private const REMEMBER_COOKIE = 'sasmita_email';

    /**
     * Menampilkan halaman login.
     */
    public function index()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to(site_url('rps'));
        }

        return view('auth/login', [
            'title'           => 'Masuk · SASMITA',
            'rememberedEmail' => (string) ($this->request->getCookie(self::REMEMBER_COOKIE) ?? ''),
        ]);
    }

    /**
     * Memproses formulir login.
     */
    public function attemptLogin()
    {
        $rules = [
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email belum benar.',
                ],
            ],
            'password' => [
                'label'  => 'Kata sandi',
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'Kata sandi wajib diisi.',
                    'min_length' => 'Kata sandi minimal 6 karakter.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        /** @var Sasmita $config */
        $config   = config(Sasmita::class);
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');
        $user     = $config->users[$email] ?? null;

        if ($user === null || ! hash_equals($user['password'], $password)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Email atau kata sandi salah. Silakan coba lagi.');
        }

        $displayName = ProfilGuru::nama((string) $user['name']);

        // Cegah session fixation setelah login berhasil.
        session()->regenerate();

        session()->set([
            'isLoggedIn' => true,
            'user'       => [
                'email' => $email,
                'name'  => $displayName,
                'role'  => $user['role'],
            ],
        ]);

        $remember = (bool) $this->request->getPost('remember');

        if ($remember) {
            $this->response->setCookie(self::REMEMBER_COOKIE, $email, $config->rememberDuration);
        } else {
            $this->response->deleteCookie(self::REMEMBER_COOKIE);
        }

        return redirect()
            ->to(site_url('rps'))
            ->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    /**
     * Alias dasbor: langsung menuju ruang kerja RPS.
     */
    public function dashboard()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()
                ->to(site_url('login'))
                ->with('error', 'Silakan masuk terlebih dahulu.');
        }

        return redirect()->to(site_url('rps'));
    }

    /**
     * Keluar dari aplikasi.
     */
    public function logout()
    {
        session()->destroy();
        $this->response->deleteCookie(self::REMEMBER_COOKIE);

        return redirect()
            ->to(site_url('login'))
            ->with('success', 'Anda telah keluar dari SASMITA.');
    }
}
