@php use App\Support\Format as F; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    * { box-sizing: border-box; }
    @page { margin: 2.2cm 2cm; }
    body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 11.5pt; line-height: 1.55; }
    .header { text-align: center; }
    .header .org { font-weight: bold; font-size: 12.5pt; letter-spacing: .3px; }
    .header .sub { font-size: 11pt; }
    .rule { border: 0; border-top: 2px double #000; margin: 6px 0 16px; }
    table { width: 100%; border-collapse: collapse; }
    .meta td { padding: 1px 0; vertical-align: top; }
    .meta .k { width: 90px; }
    .meta .s { width: 12px; }
    .summary { margin: 6px 0 4px; }
    .summary td { padding: 3px 0; }
    .summary .no { width: 22px; }
    .summary .amt { text-align: right; white-space: nowrap; width: 150px; font-variant-numeric: tabular-nums; }
    .summary .saldo td { border-top: 1px solid #000; font-weight: bold; padding-top: 6px; }
    .sign { margin-top: 30px; width: 100%; }
    .sign td { vertical-align: top; text-align: center; width: 50%; padding-top: 4px; }
    .sign .space { height: 74px; }
    .sign .name { font-weight: bold; text-decoration: underline; }
    .center { text-align: center; }
    p { margin: 9px 0; text-align: justify; }
</style>
</head>
<body>
    <div class="header">
        <div class="org">{{ $p->nama_organisasi }}</div>
        @if ($p->alamat_baris1)<div class="sub">{{ $p->alamat_baris1 }}</div>@endif
        @if ($p->alamat_baris2)<div class="sub">{{ $p->alamat_baris2 }}</div>@endif
    </div>
    <hr class="rule">

    <table class="meta">
        <tr><td class="k">Perihal</td><td class="s">:</td><td>Laporan Keuangan Iuran Duka Cita Tahun {{ $r['tahun'] }}</td></tr>
        <tr><td class="k">Kepada Yth</td><td class="s">:</td><td>Warga {{ $p->nama_rt }} {{ $p->nama_perumahan }}</td></tr>
    </table>

    <p style="margin-top:16px;">Dengan Hormat,</p>
    <p>Bersama ini disampaikan laporan keuangan aktivitas kegiatan PKK {{ $p->nama_rt }} dalam hal Iuran Duka Cita
       untuk periode Januari {{ $r['tahun'] }} s/d {{ F::namaBulan($r['bulan_akhir']) }} {{ $r['tahun'] }} dengan ringkasan rincian sebagai berikut :</p>

    <table class="summary">
        <tr><td class="no">1.</td><td>Saldo awal dana per 1 Januari {{ $r['tahun'] }}</td><td class="amt">{{ F::rupiah($r['saldo_awal']) }},-</td></tr>
        <tr><td class="no">2.</td><td>Penerimaan Iuran periode tahun {{ $r['tahun'] }}</td><td class="amt">{{ F::rupiah($r['total_masuk']) }},-</td></tr>
        <tr><td class="no">3.</td><td>Pengeluaran Santunan Tahun {{ $r['tahun'] }}</td><td class="amt">{{ F::rupiah($r['total_santunan']) }},-</td></tr>
        <tr><td class="no">4.</td><td>Pengeluaran lain-lain Tahun {{ $r['tahun'] }}</td><td class="amt">{{ F::rupiah($r['total_lain']) }},-</td></tr>
        <tr class="saldo"><td></td><td>Saldo Per akhir {{ F::namaBulan($r['bulan_akhir']) }} {{ $r['tahun'] }}</td><td class="amt">{{ F::rupiah($r['saldo_akhir']) }},-</td></tr>
    </table>

    <p>Jumlah peserta yang berpartisipasi dalam kegiatan iuran Duka Cita sebanyak {{ $r['jumlah_peserta'] }} KK,
       @if ($r['jumlah_luar'] > 0){{ $r['jumlah_luar'] }} diantaranya merupakan peserta diluar lingkungan {{ $p->nama_rt }}.@endif
       Adapun detil daftar rincian pemasukan dan pengeluaran terlampir pada surat laporan ini.</p>

    <p>Demikian laporan keuangan ini disampaikan, atas perhatian dan kerjasamanya disampaikan banyak terima kasih.</p>

    <div class="center" style="margin-top:22px;">{{ $p->tempat }}, {{ now()->translatedFormat('d F Y') }}</div>

    <table class="sign">
        <tr>
            <td>Pengurus PKK {{ $p->nama_rt }}<br>Seksi Duka Cita</td>
            <td>Ketua PKK {{ $p->nama_rt }}</td>
        </tr>
        <tr><td class="space"></td><td class="space"></td></tr>
        <tr>
            <td class="name">({{ $p->seksi_duka_cita }})</td>
            <td class="name">({{ $p->ketua_pkk }})</td>
        </tr>
    </table>

    <table class="sign" style="margin-top:16px;">
        <tr><td colspan="2" class="center">Mengetahui,<br>Ketua {{ $p->nama_rt }}<br>{{ $p->nama_perumahan }}</td></tr>
        <tr><td colspan="2" class="space"></td></tr>
        <tr><td colspan="2" class="center name">({{ $p->ketua_rt }})</td></tr>
    </table>
</body>
</html>
