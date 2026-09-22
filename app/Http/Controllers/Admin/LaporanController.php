<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DistribusiDetail;
use App\Models\MutasiBarang;
use App\Models\PengaturanSistem;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        return view('admin.laporan.index', [
            'pageTitle' => 'Laporan Keadaan Stok',
            'bulanIni'  => now()->month,
            'tahunIni'  => now()->year,
        ]);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->rakitData($request);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.cetak', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Stok-' . $data['namaBulanTahunSlug'] . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->rakitData($request);

        return app(\App\Services\LaporanStokExcelExporter::class)->download($data);
    }

    private function rakitData(Request $request): array
    {
        $validated = $request->validate([
            'bulan'    => 'required|integer|min:1|max:12',
            'tahun'    => 'required|integer|min:2000|max:2100',
            'kategori' => 'required|in:logistik_non_permakanan,peralatan',
        ]);

        $bulan    = (int) $validated['bulan'];
        $tahun    = (int) $validated['tahun'];
        $kategori = $validated['kategori'];

        $awalBulan  = Carbon::create($tahun, $bulan, 1)->startOfDay();
        $akhirBulan = $awalBulan->copy()->endOfMonth()->endOfDay();
        $bulanLalu  = $awalBulan->copy()->subMonth();

        $barangList = Barang::where('kategori', $kategori)->orderBy('id')->get();
        $barangIds  = $barangList->pluck('id');

        $stokSaatIni = StokBarang::whereIn('barang_id', $barangIds)
            ->select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('barang_id')->pluck('total', 'barang_id');

        $masukSejakAwalBulan = MutasiBarang::whereIn('barang_id', $barangIds)
            ->where('area', 'masuk')
            ->where('tanggal', '>=', $awalBulan)
            ->select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('barang_id')->pluck('total', 'barang_id');

        $keluarMutasiSejakAwalBulan = MutasiBarang::whereIn('barang_id', $barangIds)
            ->where('area', 'keluar')
            ->where('tanggal', '>=', $awalBulan)
            ->select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('barang_id')->pluck('total', 'barang_id');

        $keluarDistribusiSejakAwalBulan = DistribusiDetail::whereIn('barang_id', $barangIds)
            ->whereHas('suratDistribusi', function ($q) use ($awalBulan) {
                $q->where('tanggal', '>=', $awalBulan);
            })
            ->select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('barang_id')->pluck('total', 'barang_id');

        $saldoAwal = [];
        foreach ($barangIds as $id) {
            $saldoAwal[$id] = (int) ($stokSaatIni[$id] ?? 0)
                - (int) ($masukSejakAwalBulan[$id] ?? 0)
                + (int) ($keluarMutasiSejakAwalBulan[$id] ?? 0)
                + (int) ($keluarDistribusiSejakAwalBulan[$id] ?? 0);
        }

        $penambahan = MutasiBarang::whereIn('barang_id', $barangIds)
            ->where('area', 'masuk')
            ->whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->select('barang_id', DB::raw('SUM(jumlah) as total'))
            ->groupBy('barang_id')->pluck('total', 'barang_id');

        $jumlah = [];
        foreach ($barangIds as $id) {
            $jumlah[$id] = $saldoAwal[$id] + (int) ($penambahan[$id] ?? 0);
        }

        $details = DistribusiDetail::whereIn('barang_id', $barangIds)
            ->whereHas('suratDistribusi', function ($q) use ($awalBulan, $akhirBulan) {
                $q->whereBetween('tanggal', [$awalBulan, $akhirBulan]);
            })
            ->with(['suratDistribusi', 'gudang'])
            ->get();

        $kelompokPengeluaran = [];
        $totalPengeluaran    = array_fill_keys($barangIds->all(), 0);

        $perSurat = $details->groupBy(fn ($d) => $d->gudang_id . '-' . $d->surat_distribusi_id);

        foreach ($perSurat as $rows) {
            $first  = $rows->first();
            $surat  = $first->suratDistribusi;
            $gudang = $first->gudang;

            if (!$surat || !$gudang) {
                continue;
            }

            $gudangId = $gudang->id;

            if (!isset($kelompokPengeluaran[$gudangId])) {
                $kelompokPengeluaran[$gudangId] = [
                    'label'    => $this->labelGudang($gudang->nama_gudang),
                    'entries'  => [],
                    'subtotal' => array_fill_keys($barangIds->all(), 0),
                ];
            }

            $jumlahPerBarang = array_fill_keys($barangIds->all(), 0);
            foreach ($rows as $row) {
                $jumlahPerBarang[$row->barang_id] = ($jumlahPerBarang[$row->barang_id] ?? 0) + $row->jumlah;
                $kelompokPengeluaran[$gudangId]['subtotal'][$row->barang_id]
                    = ($kelompokPengeluaran[$gudangId]['subtotal'][$row->barang_id] ?? 0) + $row->jumlah;
                $totalPengeluaran[$row->barang_id] = ($totalPengeluaran[$row->barang_id] ?? 0) + $row->jumlah;
            }

            $kelompokPengeluaran[$gudangId]['entries'][] = [
                'tanggal'   => $surat->tanggal,
                'tujuan'    => $surat->tujuan,
                'kecamatan' => $surat->kecamatan,
                'perihal'   => $surat->perihal ?: '-',
                'nomor'     => $surat->nomor_surat,
                'jumlah'    => $jumlahPerBarang,
            ];
        }

        foreach ($kelompokPengeluaran as $gudangId => $kel) {
            usort($kelompokPengeluaran[$gudangId]['entries'], fn ($a, $b) => $a['tanggal'] <=> $b['tanggal']);
            foreach ($kelompokPengeluaran[$gudangId]['entries'] as $i => $entry) {
                $kelompokPengeluaran[$gudangId]['entries'][$i]['no'] = $i + 1;
            }
        }
        ksort($kelompokPengeluaran);

        $saldoAkhir = [];
        foreach ($barangIds as $id) {
            $saldoAkhir[$id] = $jumlah[$id] - ($totalPengeluaran[$id] ?? 0);
        }

        return [
            'bulan'                 => $bulan,
            'tahun'                 => $tahun,
            'kategori'              => $kategori,
            'kategoriLabel'         => $kategori === 'peralatan' ? 'Peralatan' : 'Logistik Non Permakanan',
            'namaBulanTahun'        => strtoupper($awalBulan->locale('id')->translatedFormat('F Y')),
            'namaBulanTahunSlug'    => $awalBulan->locale('id')->translatedFormat('F_Y'),
            'namaBulanLalu'         => strtoupper($bulanLalu->locale('id')->translatedFormat('F Y')),
            'tanggalCetak'          => now()->locale('id')->translatedFormat('d F Y'),
            'barangList'            => $barangList,
            'saldoAwal'             => $saldoAwal,
            'penambahan'            => $penambahan,
            'jumlah'                => $jumlah,
            'kelompokPengeluaran'   => $kelompokPengeluaran,
            'totalPengeluaran'      => $totalPengeluaran,
            'saldoAkhir'            => $saldoAkhir,
            'pengaturan'            => PengaturanSistem::current(),
        ];
    }

    private function labelGudang(string $namaGudang): string
    {
        return trim(strtoupper(str_replace('Gudang', '', $namaGudang)));
    }
}
