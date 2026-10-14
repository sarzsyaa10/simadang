<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Laporan Keadaan Stok {{ strtoupper($kategoriLabel) }} - {{ $namaBulanTahun }}</title>

<style>
    @page { margin: 12px; }
    body { font-family: 'Arial', 'Helvetica', sans-serif; font-size: 7px; color: #111; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 0.5px solid #666; padding: 2px 3px; text-align: center; vertical-align: middle; }
    .title { font-size: 14px; font-weight: bold; text-align: center; margin: 0; }
    .subtitle { font-size: 12px; font-weight: bold; text-align: center; margin: 2px 0 6px; }
    .left { text-align: left; }
    .general { text-align: left; }
    .bold { font-weight: bold; }
    .bg-abu { background: #D9D9D9; }
    .small { font-size: 6px; color: #444; }
    .item-name { font-size: 6.5px; line-height: 1.15; word-break: break-word; }
    .label-col { width: 55px; }
    .signature-table td { border: none; text-align: center; padding: 3px; }
    .signature-name { font-weight: bold; text-decoration: underline; }
</style>
</head>
<body>

    <p class="title">LAPORAN KEADAAN STOK {{ strtoupper($kategoriLabel) }}<br>BPBD KABUPATEN CILACAP</p>
    <p class="subtitle">BULAN {{ $namaBulanTahun }}</p>

    <table>
        <thead>
            {{-- Header nama barang -- berdiri sendiri, kolom label masih kosong --}}
            <tr class="bold">
                <th style="width:16px;"></th>
                <th style="width:40px;"></th>
                <th style="width:70px;"></th>
                <th style="width:80px;"></th>
                @foreach ($barangList as $b)
                    <th style="min-width:28px;" class="item-name">{{ $b->nama_barang }}</th>
                @endforeach
            </tr>
            <tr class="small">
                <td colspan="4"></td>
                @foreach ($barangList as $b)
                    <td>{{ $b->satuan }}</td>
                @endforeach
            </tr>
            <tr class="small">
                <td colspan="4"></td>
                @foreach ($barangList as $i => $b)
                    <td>{{ $i + 1 }}</td>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{-- SALDO AWAL --}}
            <tr class="bold">
                <td colspan="2"></td>
                <td colspan="2">SALDO AKHIR {{ $namaBulanLalu }}</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($saldoAwal[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>

            {{-- PENAMBAHAN --}}
            <tr class="bold">
                <td colspan="2"></td>
                <td colspan="2">PENAMBAHAN</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($penambahan[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>

            {{-- JUMLAH --}}
            <tr class="bold bg-abu">
                <td colspan="2"></td>
                <td colspan="2">JUMLAH</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($jumlah[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>

            {{-- Banner PENGELUARAN --}}
            <tr class="bold">
                <td colspan="{{ 4 + count($barangList) }}">PENGELUARAN</td>
            </tr>

            {{-- Header baru khusus transaksi --}}
            <tr class="bold">
                <td>NO</td>
                <td>TANGGAL</td>
                <td>TUJUAN</td>
                <td>PERIHAL</td>
                @foreach ($barangList as $b)
                    <td class="item-name">{{ $b->nama_barang }}</td>
                @endforeach
            </tr>

            @forelse ($kelompokPengeluaran as $kel)
                <tr class="bold bg-abu">
                    <td colspan="{{ 4 + count($barangList) }}">{{ $kel['label'] }}</td>
                </tr>

                @foreach ($kel['entries'] as $entry)
                    <tr>
                        <td rowspan="2">{{ $entry['no'] }}</td>
                        <td rowspan="2">{{ \Illuminate\Support\Carbon::parse($entry['tanggal'])->translatedFormat('d M Y') }}</td>
                        <td class="general">{{ $entry['tujuan'] ?: '-' }}</td>
                        <td rowspan="2" class="left">{{ $entry['perihal'] }}</td>
                        @foreach ($barangList as $b)
                            <td rowspan="2">{{ ($entry['jumlah'][$b->id] ?? 0) > 0 ? number_format($entry['jumlah'][$b->id], 0, ',', '.') : '' }}</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td class="general small">{{ $entry['kecamatan'] ? 'Kec. ' . preg_replace('/^kec\.?\s*/i', '', $entry['kecamatan']) : '' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="{{ 4 + count($barangList) }}">Tidak ada distribusi pada periode ini.</td>
                </tr>
            @endforelse

            {{-- TOTAL PENGELUARAN --}}
            <tr class="bold">
                <td colspan="2"></td>
                <td colspan="2">TOTAL PENGELUARAN</td>
                @foreach ($barangList as $b)
                    <td>{{ number_format($totalPengeluaran[$b->id] ?? 0, 0, ',', '.') }}</td>
                @endforeach
            </tr>

            {{-- SALDO AKHIR --}}
            <tr class="bold bg-abu">
                <td colspan="2"></td>
                <td colspan="2">SALDO AKHIR {{ $namaBulanTahun }}</td>
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
            <td style="width:33%;">Pengurus Barang<br>BPBD Kabupaten Cilacap</td>
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
