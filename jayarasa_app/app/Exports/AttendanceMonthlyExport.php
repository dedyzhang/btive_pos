<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AttendanceMonthlyExport implements FromArray, WithEvents
{
    protected $users;
    protected $dates;
    protected $attendanceMap;
    protected $lateTime;
    protected $startDate;
    protected $endDate;

    public function __construct($users, $dates, $attendanceMap, $lateTime, $startDate, $endDate)
    {
        $this->users = $users;
        $this->dates = $dates;
        $this->attendanceMap = $attendanceMap;
        $this->lateTime = $lateTime;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function array(): array
    {
        $data = [];

        // Row 1 (Header 1)
        $row1 = ['Nama Karyawan', 'Role'];
        foreach ($this->dates as $dateStr) {
            $formattedDate = date('d/m', strtotime($dateStr));
            $row1[] = $formattedDate;
            $row1[] = ''; // Empty cell for merged Clock Out
        }
        $row1[] = 'Total Hadir';
        $row1[] = 'Total Terlambat';
        $row1[] = 'Total Alpa';
        $data[] = $row1;

        // Row 2 (Header 2)
        $row2 = ['', ''];
        foreach ($this->dates as $dateStr) {
            $row2[] = 'Masuk';
            $row2[] = 'Pulang';
        }
        $row2[] = '';
        $row2[] = '';
        $row2[] = '';
        $data[] = $row2;

        // User rows
        foreach ($this->users as $user) {
            $row = [$user->name, $user->role];
            $totalHadir = 0;
            $totalTerlambat = 0;
            $totalAlpa = 0;

            foreach ($this->dates as $dateStr) {
                if (isset($this->attendanceMap[$user->uuid][$dateStr])) {
                    $att = $this->attendanceMap[$user->uuid][$dateStr];
                    $totalHadir++;
                    
                    $clockInFormatted = substr($att->clock_in, 0, 5);
                    if ($att->clock_in > $this->lateTime) {
                        $totalTerlambat++;
                        $row[] = $clockInFormatted . ' (T)';
                    } else {
                        $row[] = $clockInFormatted;
                    }

                    $row[] = $att->clock_out ? substr($att->clock_out, 0, 5) : '-';
                } else {
                    $totalAlpa++;
                    $row[] = '-';
                    $row[] = '-';
                }
            }

            $row[] = $totalHadir;
            $row[] = $totalTerlambat;
            $row[] = $totalAlpa;
            $data[] = $row;
        }

        // Legends
        $data[] = [];
        $data[] = ['Keterangan:'];
        $data[] = ['HH:MM', 'Absen Tepat Waktu (Jam Masuk / Pulang)'];
        $data[] = ['HH:MM (T)', 'Absen Terlambat (Melewati Jam ' . substr($this->lateTime, 0, 5) . ')'];
        $data[] = ['-', 'Alpa / Tidak Absen'];

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $numDates = count($this->dates);
                $totalColsCount = 2 + 2 * $numDates + 3;

                // 1. Merge "Nama Karyawan" (A1:A2) and "Role" (B1:B2)
                $sheet->mergeCells('A1:A2');
                $sheet->mergeCells('B1:B2');

                // 2. Merge Date Headers (Row 1: Col 3 to Col 3+2*numDates-1)
                for ($k = 0; $k < $numDates; $k++) {
                    $startColIndex = 3 + 2 * $k;
                    $endColIndex = 4 + 2 * $k;
                    
                    $startColLetter = Coordinate::stringFromColumnIndex($startColIndex);
                    $endColLetter = Coordinate::stringFromColumnIndex($endColIndex);
                    
                    $sheet->mergeCells("{$startColLetter}1:{$endColLetter}1");
                }

                // 3. Merge Summary Headers (Row 1 to 2)
                $colHadir = Coordinate::stringFromColumnIndex(3 + 2 * $numDates);
                $colTerlambat = Coordinate::stringFromColumnIndex(4 + 2 * $numDates);
                $colAlpa = Coordinate::stringFromColumnIndex(5 + 2 * $numDates);

                $sheet->mergeCells("{$colHadir}1:{$colHadir}2");
                $sheet->mergeCells("{$colTerlambat}1:{$colTerlambat}2");
                $sheet->mergeCells("{$colAlpa}1:{$colAlpa}2");

                // 4. Premium Styling
                $lastColLetter = Coordinate::stringFromColumnIndex($totalColsCount);
                $headerRange = "A1:{$lastColLetter}2";
                
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 10,
                        'name' => 'Segoe UI'
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5'] // Modern Indigo
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'C7D2FE']
                        ]
                    ]
                ]);

                // Data block styling
                $totalUsers = count($this->users);
                $dataRowsCount = 2 + $totalUsers;
                $dataRange = "A3:{$lastColLetter}{$dataRowsCount}";
                
                $sheet->getStyle($dataRange)->applyFromArray([
                    'font' => [
                        'size' => 9.5,
                        'name' => 'Segoe UI'
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB']
                        ]
                    ]
                ]);

                // Left align employee name & role
                $sheet->getStyle("A3:B{$dataRowsCount}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Summary columns grey background
                $summaryRange = "{$colHadir}3:{$colAlpa}{$dataRowsCount}";
                $sheet->getStyle($summaryRange)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ],
                    'font' => [
                        'bold' => true
                    ]
                ]);

                // Highlight late clock-in cells with a soft red background
                for ($row = 3; $row <= $dataRowsCount; $row++) {
                    for ($k = 0; $k < $numDates; $k++) {
                        $colInIndex = 3 + 2 * $k;
                        $colInLetter = Coordinate::stringFromColumnIndex($colInIndex);
                        $cellValue = $sheet->getCell("{$colInLetter}{$row}")->getValue();
                        
                        if ($cellValue && str_contains((string)$cellValue, '(T)')) {
                            $sheet->getStyle("{$colInLetter}{$row}")->applyFromArray([
                                'font' => [
                                    'color' => ['rgb' => 'DC2626'],
                                    'bold' => true
                                ],
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'startColor' => ['rgb' => 'FEF2F2']
                                ]
                            ]);
                        }
                    }
                }

                // Set custom column widths
                $sheet->getColumnDimension('A')->setWidth(24);
                $sheet->getColumnDimension('B')->setWidth(15);
                for ($col = 3; $col <= $totalColsCount; $col++) {
                    $colLetter = Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($colLetter)->setWidth(9);
                }
                
                // Align legend to left
                $legendRowStart = $dataRowsCount + 2;
                $sheet->getStyle("A{$legendRowStart}:A" . ($legendRowStart + 4))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle("A{$legendRowStart}")->getFont()->setBold(true);
            }
        ];
    }
}
