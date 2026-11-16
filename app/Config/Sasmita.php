<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Konfigurasi utama aplikasi SASMITA.
 *
 * Untuk sementara autentikasi memakai akun demo di bawah ini agar halaman
 * login bisa langsung dicoba tanpa database. Saat aplikasi sudah terhubung
 * ke database, ganti pengecekan di App\Controllers\Auth::attemptLogin()
 * dengan model User dan password_verify().
 */
class Sasmita extends BaseConfig
{
    public string $appName = 'SASMITA';

    public string $appTagline = 'Asisten Guru Berbasis AI';

    /**
     * Akun demo: email (huruf kecil) => data pengguna.
     *
     * @var array<string, array{password: string, name: string, role: string}>
     */
    public array $users = [
        'guru@sasmita.id' => [
            'password' => 'sasmita123',
            'name'     => 'Ibu Guru',
            'role'     => 'Guru',
        ],
    ];

    /**
     * Lama cookie "Ingat saya" dalam detik (30 hari).
     */
    public int $rememberDuration = 30 * DAY;
}
