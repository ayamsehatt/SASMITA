<?php

use App\Libraries\ProfilGuru;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProfilGuruTest extends CIUnitTestCase
{
    public function testNormalisasiMerapikanSpasiDanTag(): void
    {
        $this->assertSame('Ibu Guru', ProfilGuru::normalisasi("  Ibu   \n  Guru  "));
        $this->assertSame('Ibu Guru', ProfilGuru::normalisasi('<b>Ibu</b> <script>alert(1)</script> Guru'));
        $this->assertSame('', ProfilGuru::normalisasi('   '));
        $this->assertSame('', ProfilGuru::normalisasi(null));
    }

    public function testValidasiMenolakNamaKosong(): void
    {
        $this->assertSame('Nama tampilan wajib diisi.', ProfilGuru::validasi(''));
        $this->assertSame('Nama tampilan wajib diisi.', ProfilGuru::validasi('   '));
    }

    public function testValidasiMenolakNamaTerlaluPendek(): void
    {
        $this->assertSame('Nama tampilan minimal 2 karakter.', ProfilGuru::validasi('A'));
        $this->assertSame('Nama tampilan minimal 2 karakter.', ProfilGuru::validasi(' A '));
    }

    public function testValidasiMenolakNamaTerlaluPanjang(): void
    {
        $nama = str_repeat('a', 81);

        $this->assertSame(81, mb_strlen($nama));
        $this->assertSame('Nama tampilan maksimal 80 karakter.', ProfilGuru::validasi($nama));
    }

    public function testValidasiMenerimaNamaValid(): void
    {
        $this->assertNull(ProfilGuru::validasi('Ibu Guru'));
        $this->assertNull(ProfilGuru::validasi('AB'));
        $this->assertNull(ProfilGuru::validasi(str_repeat('a', 80)));
        $this->assertNull(ProfilGuru::validasi('  Nama   dengan   spasi  '));
    }
}
