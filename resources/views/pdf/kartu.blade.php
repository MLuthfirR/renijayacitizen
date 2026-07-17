@php use App\Support\Format as F; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 1.2cm; }
    body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 8.5pt; }
    h1 { text-align: center; font-size: 12pt; margin: 0; }
    .sub { text-align: center; font-size: 9.5pt; margin: 2px 0 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #333; padding: 2.5px 3px; }
    th { background: #f0f0f0; text-align: center; font-size: 8pt; }
    .no { width: 22px; text-align: center; }
    .nama { width: 150px; }
    .alamat { width: 52px; text-align: center; }
    .bln { width: 24px; text-align: center; }
    .check { color: #05755a; font-weight: bold; }
    .pindah { text-align: center; font-size: 7.5pt; letter-spacing: 1px; }
</style>
</head>
<body>
    <h1>LAPORAN DUKA CITA TAHUN {{ $periode->tahun }}</h1>
    <div class="sub">{{ $p->nama_rt }} {{ $p->nama_perumahan }}</div>

    <table>
        <thead>
            <tr>
                <th class="no">NO</th>
                <th class="nama">NAMA</th>
                <th class="alamat">ALAMAT</th>
                @foreach (range(1, 12) as $b)<th class="bln">{{ F::namaBulanSingkat($b) }}</th>@endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($kartu as $row)
                @php
                    // Jika pindah dengan bulan berhenti, gabungkan sel setelahnya sebagai "PINDAH".
                    $pindahMulai = ($row['status'] === 'pindah' && $row['selesai_bulan']) ? $row['selesai_bulan'] + 1 : null;
                @endphp
                <tr>
                    <td class="no">{{ $row['urutan'] }}</td>
                    <td class="nama">{{ $row['nama'] }}</td>
                    <td class="alamat">{{ $row['alamat'] === '-' ? '' : $row['alamat'] }}</td>
                    @php $b = 1; @endphp
                    @while ($b <= 12)
                        @if ($pindahMulai && $b == $pindahMulai)
                            <td class="pindah" colspan="{{ 12 - $pindahMulai + 1 }}">PINDAH</td>
                            @php $b = 13; @endphp
                        @else
                            <td class="bln">@if ($row['bulan'][$b] === 'lunas')<span class="check">&#10004;</span>@endif</td>
                            @php $b++; @endphp
                        @endif
                    @endwhile
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
