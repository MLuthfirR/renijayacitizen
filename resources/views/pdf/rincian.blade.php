@php use App\Support\Format as F; @endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
    @page { margin: 1.6cm 1.4cm; }
    body { font-family: DejaVu Sans, sans-serif; color: #111; font-size: 10pt; }
    h1 { text-align: center; font-size: 12pt; margin: 0 0 2px; }
    .sub { text-align: center; font-size: 10pt; margin: 0 0 14px; color: #333; }
    table.led { width: 100%; border-collapse: collapse; }
    table.led th, table.led td { border: 1px solid #333; padding: 4px 7px; }
    table.led th { background: #f0f0f0; font-size: 9.5pt; text-align: center; }
    .desc { }
    .kode { text-align: center; width: 42px; }
    .pos { text-align: center; width: 54px; }
    .num { text-align: right; white-space: nowrap; width: 110px; font-variant-numeric: tabular-nums; }
    .ket { width: 92px; font-size: 9pt; }
    .section td { font-weight: bold; background: #fafafa; }
    .total td { font-weight: bold; }
    .grand td { font-weight: bold; border-top: 2px solid #000; }
    .saldo td { font-weight: bold; background: #fff8c5; text-align: center; font-size: 11pt; }
</style>
</head>
<body>
    <h1>Rincian Laporan Iuran Duka Cita {{ $periode->tahun }}</h1>
    <div class="sub">{{ $p->nama_organisasi }}</div>

    <table class="led">
        <thead>
            <tr>
                <th class="kode">Kode</th>
                <th>Deskripsi</th>
                <th class="pos">POS</th>
                <th class="num">Debet</th>
                <th class="num">Kredit</th>
                <th class="ket">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                @if ($row['tipe'] === 'section')
                    <tr class="section">
                        <td class="kode">{{ $row['kode'] }}</td>
                        <td colspan="5">{{ $row['deskripsi'] }}</td>
                    </tr>
                @elseif ($row['tipe'] === 'saldo')
                    <tr class="saldo">
                        <td colspan="3">{{ $row['deskripsi'] }}</td>
                        <td colspan="3">{{ F::rupiah($row['kredit']) }}</td>
                    </tr>
                @elseif ($row['tipe'] === 'grandtotal')
                    <tr class="grand">
                        <td class="kode"></td>
                        <td>{{ $row['deskripsi'] }}</td>
                        <td class="pos"></td>
                        <td class="num">{{ $row['debet'] !== null ? F::rupiah($row['debet']) : '' }}</td>
                        <td class="num">{{ $row['kredit'] !== null ? F::rupiah($row['kredit']) : '' }}</td>
                        <td class="ket"></td>
                    </tr>
                @else
                    <tr class="{{ $row['tipe'] === 'total' ? 'total' : '' }}">
                        <td class="kode">{{ $row['kode'] ?? '' }}</td>
                        <td class="desc">{{ $row['deskripsi'] }}</td>
                        <td class="pos">{{ $row['pos'] ?? '' }}</td>
                        <td class="num">{{ ($row['debet'] ?? null) !== null ? F::rupiah($row['debet']) : '' }}</td>
                        <td class="num">{{ ($row['kredit'] ?? null) !== null ? F::rupiah($row['kredit']) : '' }}</td>
                        <td class="ket">{{ $row['keterangan'] ?? '' }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
