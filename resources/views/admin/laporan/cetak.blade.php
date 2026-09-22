<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan Keadaan Stok {{ strtoupper($kategoriLabel) }} - {{ $namaBulanTahun }}</title>

<style>
    @page { margin: 10px; }
    body { font-family: 'Helvetica', Arial, sans-serif; font-size: 7px; color: #111; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 0.5px solid #333; padding: 2px 3px; text-align: center; vertical-align: middle; }
    .title { font-size: 13px; font-weight: bold; text-align: center; }
    .subtitle { font-size: 11px; font-weight: bold; text-align: center; margin-bottom: 4px; }
    .left { text-align: left; }
    .bold { font-weight: bold; }
    .bg-dark { background: #0f1f3d; color: #fff; }
    .bg-light { background: #f1f1f1; }
    .small { font-size: 6px; color: #444; }
    .no-border { border: none; }
    .signature-table td { border: none; text-align: center; padding: 3px; }
    .signature-name { font-weight: bold; text-decoration: underline; }
</style>
</head>
<body>

    <p class="title">LAPORAN KEADAAN STOK {{ strtoupper($kategoriLabel) }} <br>BPBD KABUPATEN CILACAP</p>
    <p class="subtitle">BULAN {{ $namaBulanTahun }}</p>

    <table>
        <thead>
            <tr class="bg-dark">
                <th style="width:22px;">NO</th>
                <th style="width:55px;">TANGGAL</th>
                <th style="width:90px;">TUJUAN</th>
                <th style="width:100px;">PERIHAL</th>
                @foreach ($barangList as $b)
                    <th style="min-width:32px;">{{ $b->nama_barang }}</th>
                @endforeach
            </tr>
            <tr class="bg-light bold">
                <td colspan="4" class="left">SATUAN</td>
                @foreach ($barangList as $b)
                    <td>{{ $b->satuan }}</td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{-- SALDO AWAL --}}
            <tr class="bold">
                <td colspan="4" class="left">SALDO AKHIR {{ $namaBulanLalu }}</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($saldoAwal[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>

            {{-- PENAMBAHAN --}}
            <tr class="bold">
                <td colspan="4" class="left">PENAMBAHAN</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($penambahan[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>

            {{-- JUMLAH --}}
            <tr class="bold bg-light">
                <td colspan="4" class="left">JUMLAH</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($jumlah[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>

            {{-- PENGELUARAN --}}
            <tr class="bg-dark bold">
                <td colspan="{{ 4 + count($barangList) }}" class="left">PENGELUARAN</td>
            </tr>

            @forelse ($kelompokPengeluaran as $kel)
                <tr class="bold bg-light">
                    <td colspan="{{ 4 + count($barangList) }}" class="left">{{ $kel['label'] }}</td>
                </tr>

                @foreach ($kel['entries'] as $entry)
                    <tr>
                        <td rowspan="2">{{ $entry['no'] }}</td>
                        <td rowspan="2">{{ \Illuminate\Support\Carbon::parse($entry['tanggal'])->translatedFormat('d M Y') }}</td>
                        <td class="left">{{ $entry['tujuan'] ?: '-' }}</td>
                        <td rowspan="2" class="left">{{ $entry['perihal'] }}</td>
                        @foreach ($barangList as $b)
                            <td rowspan="2">{{ ($entry['jumlah'][$b->id] ?? 0) > 0 ? number_format($entry['jumlah'][$b->id], 0, ',', '.') : '' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="left small">{{ $entry['kecamatan'] ?: '' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="{{ 4 + count($barangList) }}">Tidak ada distribusi pada periode ini.</td>
                </tr>
            @endforelse

            {{-- TOTAL PENGELUARAN --}}
            <tr class="bold bg-light">
                <td colspan="4" class="left">TOTAL PENGELUARAN</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($totalPengeluaran[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>

            {{-- SALDO AKHIR --}}
            <tr class="bold bg-dark">
                <td colspan="4" class="left">SALDO AKHIR {{ $namaBulanTahun }}</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($saldoAkhir[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>
        </tbody>
    </table>

    <p class="small" style="margin-top:8px;">Dicetak otomatis oleh SIMADANG pada {{ $tanggalCetak }}.</p>

    <table class="signature-table" style="margin-top:20px; width: 70%; margin-left:auto; margin-right:auto;">
        <tr>
            <td style="width:33%;">Mengetahui,<br>Kepala Pelaksana<br>BPBD Kabupaten Cilacap</td>
            <td style="width:33%;">Kepala Bidang Kedaruratan dan Logistik<br>BPBD Kabupaten Cilacap</td>
            <td style="width:33%;">Pengurus Barang</td>
        </tr>
        <tr><td style="height:40px;"></td><td></td><td></td></tr>
        <tr>
            <td class="signature-name">{{ $pengaturan->nama_kepala_pelaksana_bpbd ?: '-' }}</td>
            <td class="signature-name">{{ $pengaturan->nama_kabid_kedaruratan_logistik ?: '-' }}</td>
            <td class="signature-name">{{ $pengaturan->nama_pengurus_barang ?: '-' }}</td>
        </tr>
        <tr>
            <td>NIP. {{ $pengaturan->nip_kepala_pelaksana_bpbd ?: '-' }}</td>
            <td>NIP. {{ $pengaturan->nip_kabid_kedaruratan_logistik ?: '-' }}</td>
            <td>NIP. {{ $pengaturan->nip_pengurus_barang ?: '-' }}</td>
        </tr>
    </table>

</body>
</html>
