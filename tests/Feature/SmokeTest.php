<?php

namespace Tests\Feature;

use App\Models\Periode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function login(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_halaman_publik_dapat_diakses(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_halaman_login_dapat_diakses(): void
    {
        $this->get('/masuk')->assertOk();
    }

    public function test_dashboard_diproteksi(): void
    {
        $this->get('/dashboard')->assertRedirect('/masuk');
    }

    public function test_halaman_pengurus_render_tanpa_periode(): void
    {
        $this->login();
        foreach (['/dashboard', '/peserta', '/iuran', '/santunan', '/laporan', '/pengaturan'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_halaman_pengurus_render_dengan_periode(): void
    {
        $this->login();
        Periode::create(['tahun' => 2025, 'saldo_awal' => 1000000, 'iuran_default' => 5000, 'status' => 'aktif']);

        foreach (['/dashboard', '/peserta', '/iuran', '/santunan', '/laporan', '/pengaturan'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_export_pdf_berjalan(): void
    {
        Periode::create(['tahun' => 2025, 'saldo_awal' => 1000000, 'iuran_default' => 5000, 'status' => 'aktif']);

        $this->get('/publik/2025/surat')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->get('/publik/2025/rincian')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
