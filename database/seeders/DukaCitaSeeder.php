<?php

namespace Database\Seeders;

use App\Models\Iuran;
use App\Models\Pengaturan;
use App\Models\Periode;
use App\Models\Peserta;
use App\Models\Santunan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DukaCitaSeeder extends Seeder
{
    public function run(): void
    {
        // ---- Akun pengurus (Ibu) ----
        User::updateOrCreate(
            ['email' => 'pengurus@rt2renijaya.id'],
            ['name' => 'Pengurus Duka Cita', 'password' => Hash::make('dukacita2025')]
        );

        // ---- Pengaturan organisasi & penanda tangan (sesuai surat 2025) ----
        Pengaturan::updateOrCreate(['id' => 1], [
            'nama_organisasi' => 'PENGURUS PKK RT.02/RW.06 RENI JAYA',
            'alamat_baris1' => 'PONDOK BENDA, PAMULANG',
            'alamat_baris2' => 'TANGERANG SELATAN',
            'nama_rt' => 'RT.02/RW.06',
            'nama_perumahan' => 'Perum Reni Jaya',
            'tempat' => 'Reni Jaya',
            'seksi_duka_cita' => 'Ny. Dewi Hairowati',
            'ketua_pkk' => 'Ny. Just Pangau',
            'ketua_rt' => 'Bp. Hasan Basri',
            'iuran_default' => 5000,
        ]);

        // ---- Periode 2025 ----
        $periode = Periode::updateOrCreate(
            ['tahun' => 2025],
            ['saldo_awal' => 19_902_000, 'iuran_default' => 5000, 'status' => 'aktif']
        );

        // Bersihkan data lama periode ini agar seeder idempoten
        $periode->peserta()->delete();
        $periode->santunan()->delete();
        $periode->pengeluaranLain()->delete();
        $periode->iuran()->delete();

        // ---- Roster peserta (dari kartu 2022) ----
        // [nama, blok, nomor, aktif, mulai_bulan, selesai_bulan, status]
        $roster = $this->roster();

        $urut = 1;
        $aktifPeserta = [];
        foreach ($roster as $r) {
            $p = Peserta::create([
                'periode_id' => $periode->id,
                'urutan' => $urut++,
                'nama' => $r['nama'],
                'blok' => $r['blok'],
                'nomor' => $r['nomor'],
                'luar_lingkungan' => false,
                'status' => $r['status'] ?? 'aktif',
                'mulai_bulan' => $r['mulai'] ?? null,
                'selesai_bulan' => $r['selesai'] ?? null,
            ]);
            if (($r['aktif'] ?? true) && ($r['status'] ?? 'aktif') !== 'berhenti') {
                $aktifPeserta[] = $p;
            }
        }

        // ---- Iuran: isi 5.000/bulan sampai total tepat Rp 5.005.000 (sesuai laporan) ----
        $target = 5_005_000;
        $per = 5_000;
        $placed = 0;
        for ($b = 1; $b <= 12 && $placed < $target; $b++) {
            foreach ($aktifPeserta as $p) {
                if ($placed >= $target) {
                    break;
                }
                if ($p->bulanTidakAktif($b)) {
                    continue;
                }
                Iuran::create([
                    'peserta_id' => $p->id,
                    'periode_id' => $periode->id,
                    'bulan' => $b,
                    'nominal' => $per,
                ]);
                $placed += $per;
            }
        }

        // ---- Santunan (10 entri, total Rp 7.000.000) sesuai buku besar ----
        $santunan = [
            ['Kelg. Ibu Winarsih', 500_000, 1],
            ['Kelg. Bpk. Paulus', 500_000, 1],
            ['Kelg. Ibu Endang Lestari', 1_000_000, 3],
            ['Kelg. Ibu Resma', 500_000, 4],
            ['Kelg. Bpk. Wahyu', 500_000, 5],
            ['Kelg. Ibu drg. Ayu', 1_000_000, 5],
            ['Kelg. Ibu Sundaru', 1_000_000, 6],
            ['Kelg. Ibu Ing', 1_000_000, 7],
            ['Kelg. Bpk. Yongki', 500_000, 7],
            ['Kelg. Bpk. Luthfi', 500_000, 7],
        ];
        $u = 1;
        foreach ($santunan as [$nama, $nom, $bln]) {
            Santunan::create([
                'periode_id' => $periode->id,
                'urutan' => $u++,
                'nama_keluarga' => $nama,
                'nominal' => $nom,
                'bulan' => $bln,
                'keterangan' => \App\Support\Format::namaBulan($bln).' 2025',
            ]);
        }
    }

    private function roster(): array
    {
        $L = fn ($nama, $blok, $nomor, $extra = []) => array_merge(['nama' => $nama, 'blok' => $blok, 'nomor' => $nomor], $extra);

        return [
            $L('Danni S', 'L 2', '6'),
            $L('Zumar Qodri', 'L 2', '7'),
            $L('Hendri', 'L 2', '7'),
            $L('Ameng', 'L 2', '8'),
            $L('Retno', 'L 2', '9'),
            $L('Lukito', 'L 2', '9'),
            $L('Robert Riyanto', 'L 2', '10'),
            $L('Ani H / Rettu, Ny', 'L 2', '11'),
            $L('Muslih H. Hamami', 'L 3', '6'),
            $L('Fauzan', 'L 3', '6'),
            $L('Mulyandri', 'L 3', '7'),
            $L('H.A.M Thambas', 'L 3', '8'),
            $L('Arief Handoko', 'L 4', '1'),
            $L('Andi Kusbiantoro', 'L 4', '2'),
            $L('Choirul', 'L 4', '3'),
            $L('Abisatyo Rendiawan', 'L 4', '4'),
            $L('Iwan AM', 'L 4', '5'),
            $L('Kris Murtono', 'L 4', '6'),
            $L('Heriyanto', 'L 4', '7'),
            $L('Robby', 'L 4', '9'),
            $L('B Yohanes', 'L 4', '11'),
            $L('Hendra', 'L 4', '12', ['aktif' => false, 'status' => 'berhenti']),
            $L('Deddy D', 'L 5', '1'),
            $L('Yongki Setiawan', 'L 5', '6'),
            $L('Ny. Ing', 'L 5', '6'),
            $L('Hardjanto', 'L 5', '7'),
            $L('Widagdo', 'M 1', '1'),
            $L('Zakaria', 'M 1', '2'),
            $L('Gandung', 'M 1', '3'),
            $L('Soenadru / C Hanani', 'M 1', '4'),
            $L('Irma Tajudin', 'M 1', '5'),
            $L('Rifatzudin', 'M 1', '6'),
            $L('Rifatzudin', 'M 1', '7'),
            $L('Esly Pardede', 'M 1', '8'),
            $L('Soedarsono', 'M 1', '10'),
            $L('Barito Tedy', 'M 1', '12'),
            $L('Deny Hendarto / Bu Edy', 'M 1', '14'),
            $L('Bu Edy', 'M 1', '14'),
            $L('Ari Wibowo', 'M 1', '15'),
            $L('Pepen Sy / Wahyudiyati', 'M 1', '16'),
            $L('Ritson Sihombing', 'M 1', '17', ['aktif' => false, 'status' => 'berhenti']),
            $L('Totok Mudjiantoro', 'M 1', '18'),
            $L('Bigman / Uut Mardi', 'M 1', '19', ['aktif' => false, 'status' => 'berhenti']),
            $L('Djoko Haryono', 'M 2', '1'),
            $L('Soedarsono', 'M 2', '2'),
            $L('Wahyu D', 'M 2', '3'),
            $L('Sarjan', 'M 2', '4'),
            $L('Erna T', 'M 2', '5'),
            $L('Hasan Basri', 'M 2', '6'),
            $L('Liana', 'M 2', '7'),
            $L('Bernadeta', 'M 2', '8'),
            $L('Bu Tiyem', 'M 2', '8'),
            $L('Amat Faoji', 'M 2', '10'),
            $L('Bu Anjarwati', 'M 2', '10'),
            $L('Endang Haryoko', 'M 2', '12'),
            $L('Kenly', 'M 2', '14'),
            $L('Paulus Yacob', 'M 2', '15'),
            $L('Yuni Tatang', 'M 2', '16', ['aktif' => false, 'status' => 'berhenti']),
            $L('Iwan / Enin', 'M 2', '17'),
            $L('Iwan Maulana', 'M 2', '18'),
            $L('Bu Rahma', 'M 2', '18'),
            $L('Yust Pangau', 'M 2', '19'),
            $L('Syahrudin', 'M 3', '1', ['status' => 'pindah', 'selesai' => 7]),
            $L('Ismail Alatas', 'M 3', '2'),
            $L('Iga Ketut Wardani', 'M 3', '3'),
            $L('Sandra', 'M 3', '4'),
            $L('Alpha', 'M 3', '5'),
            $L('Achmad S', 'M 3', '6'),
            $L('Puspita', 'M 3', '6'),
            $L('Bambang Sutedjo', 'M 3', '7'),
            $L('Erwin Simanjutak', 'M 3', '8'),
            $L('I Made Wijana', 'M 3', '10'),
            $L('Djayanto', 'M 3', '11'),
            $L('Djoko Waspodo', 'M 3', '12'),
            $L('Ariawan', 'M 3', '14', ['aktif' => false, 'status' => 'berhenti']),
            $L('Sutedja Hegar', 'M 3', '15', ['aktif' => false, 'status' => 'berhenti']),
            $L('Sutedja Hegar', 'M 3', '16', ['aktif' => false, 'status' => 'berhenti']),
            $L('Achmad Gunawan', 'M 4', '1'),
            $L('Syarifuddin (Faisal)', 'M 4', '2'),
            $L('Asmawati', 'M 4', '3'),
            $L('Sony', 'M 4', '4'),
            $L('Arifin S', 'M 4', '5'),
            $L('Rina Arifin', 'M 4', '5'),
            $L('Chandra', 'M 4', '6'),
            $L('Anggraini', 'M 4', '7'),
            $L('Yong Song Kin', 'M 4', '8'),
            $L('Dedek Prastowo', 'M 4', '9'),
            $L('Gunawan', 'M 4', '10', ['aktif' => false, 'status' => 'berhenti']),
            $L('Zulfadli', 'M 4', '11'),
            $L('Randi', 'M 4', '12'),
            $L('Lisda S', 'M 4', '14'),
            $L('Ayodya / Bambang Ryadi', 'M 5', '1'),
            $L('M Iqbal', 'M 5', '2', ['aktif' => false, 'status' => 'berhenti']),
            $L('Abu Hanifa Mogot', 'M 5', '3'),
            $L('Karmana', 'M 5', '4'),
            $L('Muwasiq M N', 'M 5', '5'),
            $L('Uswatul Wusqo', 'M 5', '6', ['aktif' => false, 'status' => 'berhenti']),
            $L('Indra Kusumo', 'M 5', '7'),
            $L('Dea Abdul Rahman', 'M 5', '8'),
            $L('Achmad Lutfi', 'M 5', '10'),
            $L('Ardian', 'M 5', '11'),
            $L('Tien Anggriani', 'M 5', '12'),
            $L('Muwasiq M N', 'M 5', '15'),
            $L('Karmana', 'M 5', '16'),
            $L('Dyah Setyati', 'M 5', '17'),
            $L('Ari', 'M 5', '17'),
            $L('Elsyie', 'M 5', '18'),
            $L('Boediarto', 'M 5', '19'),
            $L('Lia Priyanto', 'M 6', '1'),
            $L('Tanti / Reza', 'M 6', '2'),
            $L('Agha', 'M 6', '2'),
            $L('Isdrajat Iryanto', 'M 6', '3'),
            $L('Febri', 'M 6', '3'),
            $L('Mudjianto', 'M 6', '4'),
            $L('Abdul Manaf', 'M 6', '5'),
            $L('Endang Lestari', 'M 6', '7', ['mulai' => 5]),
            $L('Deri Darjat', 'M 6', '8'),
            $L('Rustam Harmayin', 'M 7', '1'),
            $L('Wisnu', 'M 7', '2'),
            $L('Sumarna', 'M 7', '3'),
            $L('Kunto Wiyono', null, null),
            $L('Wandi', null, null),
            $L('Siti Sofyah', null, null),
            $L('Sogir', null, null),
            $L('Bu Bedjo', null, null),
        ];
    }
}
