<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Distribusi {{ $distribusi->nomor_surat }}</title>
    <style>
        @page { margin: 25px 35px; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; }
        table { border-collapse: collapse; width: 100%; }

        .kop-table { width: 620px; margin: 0 auto; }
        .kop-table td { vertical-align: middle; padding: 0; }
        .kop-logo { width: 75px; text-align: center; }
        .kop-logo img { width: 58px; height: 58px; }
        .kop-text { text-align: center; }
        .kop-text .instansi { font-size: 14pt; font-weight: bold; margin: 0; }
        .kop-text .bidang { font-size: 14pt; font-weight: bold; margin: 0; }
        .kop-text .alamat { font-size: 9pt; margin: 0; }
        .kop-line { border-bottom: 3px solid #000; margin-top: 4px; margin-bottom: 2px; }
        .kop-line-thin { border-bottom: 1px solid #000; margin-bottom: 14px; }

        .judul { text-align: center; font-weight: bold; font-size: 16pt; text-decoration: underline; margin: 10px 0 4px; }
        .nomor { text-align: center; margin-bottom: 14px; }

        .info-table td { padding: 3px 0; vertical-align: top; }
        .info-label { width: 130px; }
        .info-colon { width: 12px; }

        .barang-table { margin-top: 14px; }
        .barang-table th, .barang-table td { border: 1px solid #000; padding: 6px 7px; font-size: 12pt; }
        .barang-table th { text-align: center; font-weight: bold; background: #f2f2f2; }
        .barang-table td.center { text-align: center; }
        .barang-table td.kosong { height: 24px; }

        .ttd-table { margin-top: 30px; }
        .ttd-table td { vertical-align: top; padding: 0 10px; text-align: center; width: 50%; }
        .ttd-space { height: 60px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }
        .tanggal-cell { padding-bottom: 0; }

        .kabid-block { text-align: center; margin-top: 30px; }
    </style>
</head>
<body>

    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if(file_exists(public_path('images/asset/Logo-cilacap.png')))
                    <img src="{{ public_path('images/asset/Logo-cilacap.png') }}" alt="Logo Cilacap">
                @endif
            </td>
            <td class="kop-text">
                <p class="instansi">PEMERINTAH KABUPATEN CILACAP</p>
                <p class="bidang">BADAN PENANGGULANGAN BENCANA DAERAH</p>
                <p class="alamat">Jalan Swadaya Nomor 20, Tambakreja, Cilacap Selatan, Cilacap, Jawa Tengah, 53213</p>
                <p class="alamat">Telepon (0282) 533520, 535586 Faksimile (0282) 533520</p>
                <p class="alamat">Laman: www.bpbd.cilacapkab.go.id, Pos-el: bpbdcilacap@gmail.com</p>
            </td>
            <td class="kop-logo">
                @if(file_exists(public_path('images/asset/Logo-bpbd.png')))
                    <img src="{{ public_path('images/asset/Logo-bpbd.png') }}" alt="Logo BPBD">
                @endif
            </td>
        </tr>
    </table>
    <div class="kop-line"></div>
    <div class="kop-line-thin"></div>

    <div class="judul">FORMULIR PENDISTRIBUSIAN</div>
    <div class="nomor">Nomor : {{ $distribusi->nomor_surat }}</div>

    <table class="info-table">
        <tr>
            <td class="info-label">Tujuan</td>
            <td class="info-colon">:</td>
            <td>{{ $distribusi->tujuan ?: '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Nama Posko</td>
            <td class="info-colon">:</td>
            <td>{{ $distribusi->tujuan ?: '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tingkat Posko</td>
            <td class="info-colon">:</td>
            <td>{{ $distribusi->tingkat_posko ?: '-' }}</td>
        </tr>
        <tr>
            <td class="info-label">Lokasi Posko</td>
            <td class="info-colon">:</td>
            <td>{{ $distribusi->tujuan ?: '-' }}</td>
        </tr>
    </table>

    <table class="barang-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">NO.</th>
                <th rowspan="2">NAMA/JENIS BARANG</th>
                <th colspan="2">VOLUME</th>
                <th rowspan="2" style="width: 90px;">SUMBER</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr>
                <th style="width: 60px;">SATUAN</th>
                <th style="width: 60px;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @forelse($distribusi->distribusiDetail as $i => $detail)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                    <td class="center">{{ $detail->barang->satuan ?? '-' }}</td>
                    <td class="center">{{ $detail->jumlah }}</td>
                    <td class="center">{{ $detail->sumber ?: '-' }}</td>
                    <td>{{ $detail->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="center">Tidak ada barang.</td></tr>
            @endforelse
            @php
                $minRows = 7;
                $isiCount = $distribusi->distribusiDetail->count();
                $sisaBaris = max(0, $minRows - $isiCount);
            @endphp
            @for ($i = 0; $i < $sisaBaris; $i++)
                <tr>
                    <td class="center kosong">&nbsp;</td>
                    <td class="kosong">&nbsp;</td>
                    <td class="kosong">&nbsp;</td>
                    <td class="kosong">&nbsp;</td>
                    <td class="kosong">&nbsp;</td>
                    <td class="kosong">&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <table class="ttd-table">
        <tr>
            <td></td>
            <td class="tanggal-cell">Cilacap, {{ $distribusi->tanggal->locale('id')->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="padding-top: 14px;">Yang Menerima</td>
            <td style="padding-top: 14px;">
                Yang Menyerahkan<br>
                Pengurus Barang<br>
                BPBD Kab. Cilacap
            </td>
        </tr>
        <tr>
            <td class="ttd-space"></td>
            <td class="ttd-space"></td>
        </tr>
        <tr>
            <td>( ………………………….. )</td>
            <td>
                <span class="ttd-name">{{ $pengaturan->nama_pengurus_barang ?: '(...........................)' }}</span><br>
                NIP. {{ $pengaturan->nip_pengurus_barang ?: '-' }}
            </td>
        </tr>
    </table>

    <div class="kabid-block">
        Mengetahui,<br>
        Kabid Kedaruratan dan Logistik<br>
        BPBD Kabupaten Cilacap
        <div class="ttd-space"></div>
        <span class="ttd-name">{{ $pengaturan->nama_kabid_kedaruratan_logistik ?: '(...........................)' }}</span><br>
        Penata Tk I<br>
        NIP. {{ $pengaturan->nip_kabid_kedaruratan_logistik ?: '-' }}
    </div>

</body>
</html>