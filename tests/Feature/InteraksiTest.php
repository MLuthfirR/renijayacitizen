<?php

namespace Tests\Feature;

use App\Livewire\Auth\Login;
use App\Livewire\Iuran\Index as IuranIndex;
use App\Livewire\Pengaturan\Index as PengaturanIndex;
use App\Livewire\Peserta\Index as PesertaIndex;
use App\Livewire\Santunan\Index as SantunanIndex;
use App\Models\Iuran;
use App\Models\Periode;
use App\Models\Peserta;
use App\Models\Santunan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class InteraksiTest extends TestCase
{
    use RefreshDatabase;

    private function siapkan(): array
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $periode = Periode::create(['tahun' => 2025, 'saldo_awal' => 1_000_000, 'iuran_default' => 5000, 'status' => 'aktif']);
        session(['periode_id' => $periode->id]);
        return [$user, $periode];
    }

    public function test_toggle_iuran_membuat_dan_menghapus_pembayaran(): void
    {
        [, $periode] = $this->siapkan();
        $peserta = Peserta::create(['periode_id' => $periode->id, 'urutan' => 1, 'nama' => 'Budi']);

        Livewire::test(IuranIndex::class)
            ->call('toggle', $peserta->id, 3);

        $this->assertDatabaseHas('iuran', ['peserta_id' => $peserta->id, 'bulan' => 3, 'nominal' => 5000]);

        // Toggle lagi → terhapus
        Livewire::test(IuranIndex::class)->call('toggle', $peserta->id, 3);
        $this->assertDatabaseMissing('iuran', ['peserta_id' => $peserta->id, 'bulan' => 3]);
    }

    public function test_isi_kolom_menandai_semua_peserta_aktif(): void
    {
        [, $periode] = $this->siapkan();
        Peserta::create(['periode_id' => $periode->id, 'urutan' => 1, 'nama' => 'A', 'status' => 'aktif']);
        Peserta::create(['periode_id' => $periode->id, 'urutan' => 2, 'nama' => 'B', 'status' => 'aktif']);

        Livewire::test(IuranIndex::class)->call('isiKolom', 1);

        $this->assertEquals(2, Iuran::where('bulan', 1)->count());
        $this->assertEquals(10000, Iuran::where('bulan', 1)->sum('nominal'));
    }

    public function test_tambah_peserta(): void
    {
        [, $periode] = $this->siapkan();

        Livewire::test(PesertaIndex::class)
            ->call('tambah')
            ->set('nama', 'Ibu Sari')
            ->set('blok', 'M 2')
            ->set('nomor', '19')
            ->set('urutan', 5)
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('peserta', ['nama' => 'Ibu Sari', 'blok' => 'M 2', 'periode_id' => $periode->id]);
    }

    public function test_tambah_santunan(): void
    {
        [, $periode] = $this->siapkan();

        Livewire::test(SantunanIndex::class)
            ->call('tambah', 'santunan')
            ->set('judul', 'Kelg. Bpk. Test')
            ->set('nominal', 1_000_000)
            ->set('bulan', 4)
            ->call('simpan')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('santunan', ['nama_keluarga' => 'Kelg. Bpk. Test', 'nominal' => 1_000_000]);
        $this->assertEquals(1_000_000, $periode->fresh()->totalSantunan());
    }

    public function test_buat_periode_baru_menyalin_peserta(): void
    {
        [, $periode] = $this->siapkan();
        Peserta::create(['periode_id' => $periode->id, 'urutan' => 1, 'nama' => 'Warga Lama', 'status' => 'aktif']);

        Livewire::test(PengaturanIndex::class)
            ->call('bukaModalPeriode')
            ->set('tahunBaru', 2026)
            ->set('saldoAwalBaru', 500000)
            ->set('iuranBaru', 5000)
            ->set('salinPeserta', true)
            ->set('salinDari', $periode->id)
            ->call('simpanPeriode')
            ->assertHasNoErrors();

        $baru = Periode::where('tahun', 2026)->first();
        $this->assertNotNull($baru);
        $this->assertDatabaseHas('peserta', ['periode_id' => $baru->id, 'nama' => 'Warga Lama']);
    }

    public function test_login_berhasil_dan_gagal(): void
    {
        $user = User::factory()->create(['email' => 'ibu@rt2.id', 'password' => Hash::make('rahasia123')]);

        Livewire::test(Login::class)
            ->set('email', 'ibu@rt2.id')
            ->set('password', 'salah')
            ->call('login')
            ->assertHasErrors('email');

        Livewire::test(Login::class)
            ->set('email', 'ibu@rt2.id')
            ->set('password', 'rahasia123')
            ->call('login')
            ->assertRedirect('/dashboard');
    }
}
