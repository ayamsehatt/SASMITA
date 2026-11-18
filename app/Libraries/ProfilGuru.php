<?php

namespace App\Libraries;

use App\Models\PengaturanModel;
use Throwable;

/**
 * Pengelola profil guru yang disimpan di tabel pengaturan (key-value).
 */
class ProfilGuru
{
    public const KEY_NAMA = 'nama_tampilan';

    /**
     * Normalisasi nama tampilan: buang tag/script, rapikan spasi.
     */
    public static function normalisasi(?string $nama): string
    {
        $nama = (string) $nama;
        $nama = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', ' ', $nama) ?? $nama;
        $nama = strip_tags($nama);
        $nama = preg_replace('/\s+/u', ' ', trim($nama)) ?? trim($nama);

        return $nama;
    }

    /**
     * Validasi nama tampilan. Mengembalikan pesan error atau null jika valid.
     */
    public static function validasi(string $nama): ?string
    {
        $nama   = self::normalisasi($nama);
        $length = mb_strlen($nama);

        if ($length === 0) {
            return 'Nama tampilan wajib diisi.';
        }

        if ($length < 2) {
            return 'Nama tampilan minimal 2 karakter.';
        }

        if ($length > 80) {
            return 'Nama tampilan maksimal 80 karakter.';
        }

        return null;
    }

    /**
     * Ambil nama tampilan dari database, atau kembalikan fallback jika tidak ada/gagal.
     */
    public static function nama(string $fallback = ''): string
    {
        try {
            $model = new PengaturanModel();
            $row   = $model->where('kunci', self::KEY_NAMA)->first();

            if (! is_array($row)) {
                return $fallback;
            }

            $nama = self::normalisasi($row['nilai'] ?? '');

            if ($nama === '' || self::validasi($nama) !== null) {
                return $fallback;
            }

            return $nama;
        } catch (Throwable $e) {
            log_message('warning', 'Gagal membaca nama tampilan: {message}', ['message' => $e->getMessage()]);

            return $fallback;
        }
    }

    /**
     * Simpan nama tampilan ke database (insert/update).
     *
     * @throws Throwable
     */
    public static function simpanNama(string $nama): string
    {
        $nama  = self::normalisasi($nama);
        $error = self::validasi($nama);

        if ($error !== null) {
            throw new \InvalidArgumentException($error);
        }

        $model    = new PengaturanModel();
        $existing = $model->where('kunci', self::KEY_NAMA)->first();

        if (is_array($existing) && isset($existing['id'])) {
            $model->update((int) $existing['id'], ['nilai' => $nama]);
        } else {
            $model->insert([
                'kunci' => self::KEY_NAMA,
                'nilai' => $nama,
            ]);
        }

        return $nama;
    }
}
