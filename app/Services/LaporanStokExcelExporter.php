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

    private const ABU_ABU = 'D9D9D9';
    private const FONT    = 'Arial';

    public function download(array $data): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName(self::FONT)->setSize(11);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($data['namaBulanTahun'], 0, 31));
        $sheet->setShowGridlines(false);

        $barangList     = $data['barangList'];
        $totalKolom     = self::LABEL_COLS + $barangList->count();
        $lastColLetter  = Coordinate::stringFromColumnIndex($totalKolom);
        $itemColStart   = self::LABEL_COLS + 1;
        $itemColLetter0 = Coordinate::stringFromColumnIndex($itemColStart);

        $row = 1;

        $sheet->setCellValue("A{$row}", 'LAPORAN KEADAAN STOK ' . strtoupper($data['kategoriLabel']));
        $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", 'BPBD KABUPATEN CILACAP');
        $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", 'BULAN ' . $data['namaBulanTahun']);
        $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row += 2;

        $headerRow = $row;

        $sheet->setCellValue("A{$headerRow}", 'NO');
        $sheet->setCellValue("B{$headerRow}", 'TANGGAL');
        $sheet->setCellValue("C{$headerRow}", 'TUJUAN');
        $sheet->setCellValue("D{$headerRow}", 'PERIHAL');
        $sheet->getStyle("A{$headerRow}:D{$headerRow}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$headerRow}:D{$headerRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $col = $itemColStart;
        foreach ($barangList as $b) {
            $letter = Coordinate::stringFromColumnIndex($col);
            $cell   = "{$letter}{$headerRow}";

            $sheet->setCellValue($cell, $b->nama_barang);
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle($cell)->getAlignment()
                ->setTextRotation(90)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setWrapText(true);
            $sheet->getColumnDimensionByColumn($col)->setWidth(4.6);
            $col++;
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(125);
        $row++;

        $satuanRow = $row;
        $sheet->setCellValue("A{$satuanRow}", 'SATUAN');
        $sheet->mergeCells("A{$satuanRow}:D{$satuanRow}");
        $col = $itemColStart;
        foreach ($barangList as $b) {
            $letter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$satuanRow}", $b->satuan);
            $col++;
        }
        $sheet->getStyle("A{$satuanRow}:{$lastColLetter}{$satuanRow}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A{$satuanRow}:{$lastColLetter}{$satuanRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $row++;

        $row = $this->barisRingkasan($sheet, $row, 'SALDO AKHIR ' . $data['namaBulanLalu'], $data['saldoAwal'], $barangList, $itemColStart, $lastColLetter, false);
        $row = $this->barisRingkasan($sheet, $row, 'PENAMBAHAN', $data['penambahan'], $barangList, $itemColStart, $lastColLetter, false);
        $row = $this->barisRingkasan($sheet, $row, 'JUMLAH', $data['jumlah'], $barangList, $itemColStart, $lastColLetter, true);

        $sheet->setCellValue("A{$row}", 'PENGELUARAN');
        $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
        $row++;

        foreach ($data['kelompokPengeluaran'] as $kel) {
            $sheet->setCellValue("A{$row}", $kel['label']);
            $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("A{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::ABU_ABU);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            $row++;

            if (empty($kel['entries'])) {
                continue;
            }

            foreach ($kel['entries'] as $entry) {
                $r1 = $row;
                $r2 = $row + 1;

                $sheet->setCellValue("A{$r1}", $entry['no']);
                $sheet->setCellValue("B{$r1}", Carbon::parse($entry['tanggal'])->translatedFormat('d M Y'));
                $sheet->setCellValue("C{$r1}", $entry['tujuan'] ?: '-');
                $sheet->setCellValue("D{$r1}", $entry['perihal']);

                $sheet->mergeCells("A{$r1}:A{$r2}");
                $sheet->mergeCells("B{$r1}:B{$r2}");
                $sheet->mergeCells("D{$r1}:D{$r2}");

                $col = $itemColStart;
                foreach ($barangList as $b) {
                    $letter = Coordinate::stringFromColumnIndex($col);
                    $qty    = $entry['jumlah'][$b->id] ?? 0;
                    if ($qty > 0) {
                        $sheet->setCellValue("{$letter}{$r1}", $qty);
                    }
                    $sheet->mergeCells("{$letter}{$r1}:{$letter}{$r2}");
                    $col++;
                }

                if (!empty($entry['kecamatan'])) {
                    $kec = preg_replace('/^kec\.?\s*/i', '', $entry['kecamatan']);
                    $sheet->setCellValue("C{$r2}", 'Kec. ' . $kec);
                }

                $sheet->getStyle("A{$r1}:{$lastColLetter}{$r2}")->getFont()->setSize(10);
                $sheet->getStyle("A{$r1}:{$lastColLetter}{$r2}")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $sheet->getStyle("C{$r1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("C{$r2}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("D{$r1}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $row += 2;
            }
        }

        if (empty($data['kelompokPengeluaran'])) {
            $sheet->setCellValue("A{$row}", 'Tidak ada distribusi pada periode ini.');
            $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setItalic(true);
            $row++;
        }

        $row = $this->barisRingkasan($sheet, $row, 'TOTAL PENGELUARAN', $data['totalPengeluaran'], $barangList, $itemColStart, $lastColLetter, false);

        $sheet->setCellValue("A{$row}", 'SALDO AKHIR ' . $data['namaBulanTahun']);
        $sheet->mergeCells("A{$row}:D{$row}");
        $col = $itemColStart;
        foreach ($barangList as $b) {
            $letter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$row}", $data['saldoAkhir'][$b->id] ?? 0);
            $col++;
        }
        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::ABU_ABU);
        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("{$itemColLetter0}{$row}:{$lastColLetter}{$row}")->getNumberFormat()->setFormatCode('#,##0');

        $gridEndRow = $row;
        $row += 2;

        $sheet->getStyle("A{$headerRow}:{$lastColLetter}{$gridEndRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $sheet->setCellValue("A{$row}", 'Dicetak otomatis oleh SIMADANG pada ' . $data['tanggalCetak'] . '.');
        $sheet->getStyle("A{$row}")->getFont()->setItalic(true)->setSize(9);
        $row += 2;

        $pengaturan = $data['pengaturan'];
        $c1 = 'A';
        $c2 = Coordinate::stringFromColumnIndex((int) floor($totalKolom / 3) + 1);
        $c3 = Coordinate::stringFromColumnIndex((int) floor(($totalKolom * 2) / 3) + 1);

        $sheet->setCellValue("{$c1}{$row}", 'Mengetahui,');
        $row++;

        $sheet->setCellValue("{$c1}{$row}", 'Kepala Pelaksana');
        $sheet->setCellValue("{$c2}{$row}", 'Kepala Bidang Kedaruratan dan Logistik');
        $sheet->setCellValue("{$c3}{$row}", 'Pengurus Barang');
        $row++;

        $sheet->setCellValue("{$c1}{$row}", 'BPBD Kabupaten Cilacap');
        $sheet->setCellValue("{$c2}{$row}", 'BPBD Kabupaten Cilacap');
        $sheet->setCellValue("{$c3}{$row}", 'BPBD Kabupaten Cilacap');
        $row += 4;

        $sheet->setCellValue("{$c1}{$row}", $pengaturan->nama_kepala_pelaksana_bpbd ?: '-');
        $sheet->setCellValue("{$c2}{$row}", $pengaturan->nama_kabid_kedaruratan_logistik ?: '-');
        $sheet->setCellValue("{$c3}{$row}", $pengaturan->nama_pengurus_barang ?: '-');
        foreach ([$c1, $c2, $c3] as $c) {
            $sheet->getStyle("{$c}{$row}")->getFont()->setBold(true)->setUnderline(true);
        }
        $row++;

        $sheet->setCellValue("{$c1}{$row}", 'NIP. ' . ($pengaturan->nip_kepala_pelaksana_bpbd ?: '-'));
        $sheet->setCellValue("{$c2}{$row}", 'NIP. ' . ($pengaturan->nip_kabid_kedaruratan_logistik ?: '-'));
        $sheet->setCellValue("{$c3}{$row}", 'NIP. ' . ($pengaturan->nip_pengurus_barang ?: '-'));

        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(11);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(20);

        $namaFile = 'Laporan-Stok-' . $data['namaBulanTahunSlug'] . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function barisRingkasan($sheet, int $row, string $label, $dataPerBarang, $barangList, int $itemColStart, string $lastColLetter, bool $abuAbu): int
    {
        $sheet->setCellValue("A{$row}", $label);
        $sheet->mergeCells("A{$row}:D{$row}");

        $col = $itemColStart;
        foreach ($barangList as $b) {
            $letter = Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue("{$letter}{$row}", $dataPerBarang[$b->id] ?? 0);
            $col++;
        }

        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        if ($abuAbu) {
            $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(self::ABU_ABU);
        }

        $itemColLetter = Coordinate::stringFromColumnIndex($itemColStart);
        $sheet->getStyle("{$itemColLetter}{$row}:{$lastColLetter}{$row}")->getNumberFormat()->setFormatCode('#,##0');

        return $row + 1;
    }
}
