<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanStokExcelExporter
{
    private const LABEL_COLS = 4;

    public function download(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($data['namaBulanTahun'], 0, 31));

        $barangList  = $data['barangList'];
        $totalKolom  = self::LABEL_COLS + $barangList->count();
        $lastColLetter = Coordinate::stringFromColumnIndex($totalKolom);

        $row = 1;

        $sheet->setCellValue("A{$row}", 'LAPORAN KEADAAN STOK ' . strtoupper($data['kategoriLabel']) . ' BPBD KABUPATEN CILACAP');
        $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", 'BULAN ' . $data['namaBulanTahun']);
        $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        $headerRow = $row;
        $sheet->setCellValue("A{$headerRow}", 'NO');
        $sheet->setCellValue("B{$headerRow}", 'TANGGAL');
        $sheet->setCellValue("C{$headerRow}", 'TUJUAN');
        $sheet->setCellValue("D{$headerRow}", 'PERIHAL');

        $col = self::LABEL_COLS + 1;
        foreach ($barangList as $b) {
            $letter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$headerRow}", $b->nama_barang);
            $col++;
        }
        $this->styleHeaderRow($sheet, $headerRow, $totalKolom);
        $row++;

        $satuanRow = $row;
        $sheet->setCellValue("A{$satuanRow}", 'SATUAN');
        $sheet->mergeCells("A{$satuanRow}:D{$satuanRow}");
        $col = self::LABEL_COLS + 1;
        foreach ($barangList as $b) {
            $letter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$satuanRow}", $b->satuan);
            $col++;
        }
        $sheet->getStyle("A{$satuanRow}:{$lastColLetter}{$satuanRow}")->getFont()->setBold(true)->setItalic(true);
        $row++;

        $row = $this->tulisBarisRingkasan($sheet, $row, 'SALDO AKHIR ' . $data['namaBulanLalu'], $data['saldoAwal'], $barangList, $lastColLetter);

        $row = $this->tulisBarisRingkasan($sheet, $row, 'PENAMBAHAN', $data['penambahan'], $barangList, $lastColLetter);

        $row = $this->tulisBarisRingkasan($sheet, $row, 'JUMLAH', $data['jumlah'], $barangList, $lastColLetter, true);

        $sheet->setCellValue("A{$row}", 'PENGELUARAN');
        $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0F1F3D');
        $row++;

        foreach ($data['kelompokPengeluaran'] as $kel) {
            $sheet->setCellValue("A{$row}", $kel['label']);
            $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F1F1');
            $row++;

            foreach ($kel['entries'] as $entry) {
                $sheet->setCellValue("A{$row}", $entry['no']);
                $sheet->setCellValue("B{$row}", Carbon::parse($entry['tanggal'])->translatedFormat('d M Y'));
                $sheet->setCellValue("C{$row}", $entry['tujuan'] ?: '-');
                $sheet->setCellValue("D{$row}", $entry['perihal']);

                $col = self::LABEL_COLS + 1;
                foreach ($barangList as $b) {
                    $letter = Coordinate::stringFromColumnIndex($col);
                    $qty = $entry['jumlah'][$b->id] ?? 0;
                    if ($qty > 0) {
                        $sheet->setCellValue("{$letter}{$row}", $qty);
                    }
                    $col++;
                }
                $row++;

                if (!empty($entry['kecamatan'])) {
                    $sheet->setCellValue("C{$row}", $entry['kecamatan']);
                    $sheet->getStyle("C{$row}")->getFont()->setItalic(true)->setSize(9);
                    $row++;
                }
            }
        }

        if (empty($data['kelompokPengeluaran'])) {
            $sheet->setCellValue("A{$row}", 'Tidak ada distribusi pada periode ini.');
            $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
            $row++;
        }

        $row = $this->tulisBarisRingkasan($sheet, $row, 'TOTAL PENGELUARAN', $data['totalPengeluaran'], $barangList, $lastColLetter, true);
        $row++;

        $sheet->setCellValue("A{$row}", 'SALDO AKHIR ' . $data['namaBulanTahun']);
        $sheet->mergeCells("A{$row}:D{$row}");
        $col = self::LABEL_COLS + 1;
        foreach ($barangList as $b) {
            $letter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$row}", $data['saldoAkhir'][$b->id] ?? 0);
            $col++;
        }
        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0F1F3D');
        $row += 2;

        $sheet->setCellValue("A{$row}", 'Dicetak otomatis oleh SIMADANG pada ' . $data['tanggalCetak'] . '.');
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9);
        $row += 3;

        $pengaturan = $data['pengaturan'];
        $sheet->setCellValue("A{$row}", 'Mengetahui,');
        $row++;
        $sheet->setCellValue("A{$row}", 'Kepala Pelaksana');
        $sheet->setCellValue("F{$row}", 'Kepala Bidang Kedaruratan dan Logistik');
        $sheet->setCellValue("K{$row}", 'Pengurus Barang');
        $row++;
        $sheet->setCellValue("A{$row}", 'BPBD Kabupaten Cilacap');
        $sheet->setCellValue("F{$row}", 'BPBD Kabupaten Cilacap');
        $row += 4;
        $sheet->setCellValue("A{$row}", $pengaturan->nama_kepala_pelaksana_bpbd ?: '-');
        $sheet->setCellValue("F{$row}", $pengaturan->nama_kabid_kedaruratan_logistik ?: '-');
        $sheet->setCellValue("K{$row}", $pengaturan->nama_pengurus_barang ?: '-');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("F{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("K{$row}")->getFont()->setBold(true)->setUnderline(true);
        $row++;
        $sheet->setCellValue("A{$row}", 'NIP. ' . ($pengaturan->nip_kepala_pelaksana_bpbd ?: '-'));
        $sheet->setCellValue("F{$row}", 'NIP. ' . ($pengaturan->nip_kabid_kedaruratan_logistik ?: '-'));
        $sheet->setCellValue("K{$row}", 'NIP. ' . ($pengaturan->nip_pengurus_barang ?: '-'));

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(28);
        for ($c = self::LABEL_COLS + 1; $c <= $totalKolom; $c++) {
            $sheet->getColumnDimensionByColumn($c)->setWidth(10);
        }

        $sheet->getStyle("A{$headerRow}:{$lastColLetter}" . ($row))
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $namaFile = 'Laporan-Stok-' . $data['namaBulanTahunSlug'] . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function styleHeaderRow($sheet, int $row, int $totalKolom): void
    {
        $lastCol = Coordinate::stringFromColumnIndex($totalKolom);
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('0F1F3D');
        $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    private function tulisBarisRingkasan($sheet, int $row, string $label, $dataPerBarang, $barangList, string $lastColLetter, bool $tebal = false): int
    {
        $sheet->setCellValue("A{$row}", $label);
        $sheet->mergeCells("A{$row}:D{$row}");

        $col = self::LABEL_COLS + 1;
        foreach ($barangList as $b) {
            $letter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$row}", $dataPerBarang[$b->id] ?? 0);
            $col++;
        }

        if ($tebal) {
            $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F1F1');
        } else {
            $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFont()->setBold(true);
        }

        return $row + 1;
    }
}
