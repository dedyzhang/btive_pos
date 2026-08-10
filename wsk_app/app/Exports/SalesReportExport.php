<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SalesReportExport implements FromArray, WithEvents
{
    protected $transactions;
    protected $summary;

    // Row trackers for dynamic styling
    private $currentRow = 0;
    private $titleRow = 1;
    private $dateRow = 2;
    private $metricsTitleRow;
    private $metricsHeaderRow;
    private $metricsValueRow;
    private $menuTitleRow;
    private $menuHeaderRow;
    private $menuStartRow;
    private $menuEndRow;
    private $breakdownTitleRow;
    private $breakdownHeaderRow;
    private $breakdownDataStartRow;
    private $breakdownDataEndRow;
    private $profitTitleRow;
    private $profitHeaderRow;
    private $profitDataStartRow;
    private $profitDataEndRow;

    public function __construct($transactions, $summary)
    {
        $this->transactions = $transactions;
        $this->summary = $summary;
    }

    /**
     * Add row to data array and increment row counter
     */
    private function addRow(&$data, $rowData)
    {
        $data[] = $rowData;
        $this->currentRow++;
        return $this->currentRow;
    }

    public function array(): array
    {
        $data = [];
        $this->currentRow = 0;

        // Row 1 & 2: Header Laporan
        $this->titleRow = $this->addRow($data, ['LAPORAN PENJUALAN POS KASIR']);
        $this->dateRow = $this->addRow($data, ['Periode Tanggal: ' . $this->summary['date']]);
        $this->addRow($data, [' ']); // Blank spacer (with space to prevent Laravel Excel from skipping it)

        // Summary Card Labels
        $this->metricsTitleRow = $this->addRow($data, ['RINGKASAN METRIK KEUANGAN']);
        $this->metricsHeaderRow = $this->addRow($data, ['Omzet Bersih', 'Laba Kotor', 'Laba Bersih', 'HPP (Modal)', 'Total Transaksi', 'Total Item Terjual']);
        $this->metricsValueRow = $this->addRow($data, [
            $this->summary['total_revenue'],
            $this->summary['laba_kotor'],
            $this->summary['laba_bersih'],
            $this->summary['total_cost_price'],
            $this->summary['total_transactions'],
            $this->summary['total_items']
        ]);
        $this->addRow($data, [' ']); // Blank spacer

        // Sales By Menus Header
        $this->menuTitleRow = $this->addRow($data, ['RINCIAN PENJUALAN MENU (SALES BY MENUS)']);
        $this->menuHeaderRow = $this->addRow($data, ['Kategori', 'Nama Menu', 'Qty Terjual', 'Harga Jual', 'Estimasi HPP Satuan', 'Total HPP', 'Total Penjualan', 'Keuntungan Bersih']);
        
        // Items Data
        $this->menuStartRow = $this->currentRow + 1;
        foreach ($this->summary['items'] as $item) {
            $this->addRow($data, [
                $item['category_name'],
                $item['name'],
                $item['qty'],
                $item['price'],
                $item['cost_price'],
                $item['cost_price_total'],
                $item['total'],
                $item['profit']
            ]);
        }
        $this->menuEndRow = $this->currentRow;
        
        $this->addRow($data, [' ']); // Blank spacer

        // Header Breakdown Tipe & Metode
        $this->breakdownTitleRow = $this->addRow($data, ['RINGKASAN TIPE PESANAN', '', '', '', '', 'RINGKASAN METODE PEMBAYARAN']);
        $this->breakdownHeaderRow = $this->addRow($data, ['Tipe Pesanan', 'Transaksi', 'Total Penjualan', '', '', 'Metode Pembayaran', 'Transaksi', 'Total Setoran']);

        // Get breakdowns
        $orderTypes = array_values($this->summary['order_types']);
        $paymentMethods = array_values($this->summary['payment_methods']);
        $maxBreakdown = max(count($orderTypes), count($paymentMethods));

        $this->breakdownDataStartRow = $this->currentRow + 1;
        for ($i = 0; $i < $maxBreakdown; $i++) {
            $row = [];
            
            // Order Type side
            if (isset($orderTypes[$i])) {
                $typeKey = array_keys($this->summary['order_types'])[$i];
                $typeName = $typeKey === 'take_away' ? 'Take Away' : 'Dine In';
                $row[] = $typeName;
                $row[] = $orderTypes[$i]['total_transaction'];
                $row[] = $orderTypes[$i]['total'];
            } else {
                $row[] = ''; $row[] = ''; $row[] = '';
            }

            $row[] = ''; // Spacer Column D
            $row[] = ''; // Spacer Column E

            // Payment Method side
            if (isset($paymentMethods[$i])) {
                $methodName = array_keys($this->summary['payment_methods'])[$i] ?: 'CASH';
                $row[] = $methodName;
                $row[] = $paymentMethods[$i]['total_transaction'];
                $row[] = $paymentMethods[$i]['total'];
            } else {
                $row[] = ''; $row[] = ''; $row[] = '';
            }

            $this->addRow($data, $row);
        }
        $this->breakdownDataEndRow = $this->currentRow;
        
        $this->addRow($data, [' ']); // Blank spacer

        // Detailed profitability & Expenses
        $this->profitTitleRow = $this->addRow($data, ['DETAIL PROFITABILITAS & PENGELUARAN BIAYA']);
        $this->profitHeaderRow = $this->addRow($data, ['Kategori Penerimaan', 'Jumlah Rupiah', '', 'Kategori Pengeluaran & Laba', 'Jumlah Rupiah']);
        
        $this->profitDataStartRow = $this->currentRow + 1;
        $this->addRow($data, ['Total Omzet Pendapatan', $this->summary['total_revenue'], '', 'Total HPP (Modal Menu)', $this->summary['total_cost_price']]);
        $this->addRow($data, ['Total Potongan Diskon', $this->summary['total_discount'], '', 'Laba Kotor (Omzet - HPP)', $this->summary['laba_kotor']]);
        $this->addRow($data, ['Total Pungutan Pajak', $this->summary['total_tax'], '', 'Biaya Operasional (50% HPP)', $this->summary['operasional_cost']]);
        $this->addRow($data, ['Total Penerimaan Riil', $this->summary['total_revenue'], '', 'Estimasi Laba Bersih', $this->summary['laba_bersih']]);
        $this->profitDataEndRow = $this->currentRow;

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Enable gridlines
                $sheet->setShowGridLines(true);

                // --- 1. General Fonts Setup ---
                $sheet->getStyle('A1:H200')->getFont()->setName('Segoe UI');

                // --- 2. Title & Date Styling ---
                $sheet->mergeCells("A{$this->titleRow}:H{$this->titleRow}");
                $sheet->getStyle("A{$this->titleRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '1F2937']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                $sheet->mergeCells("A{$this->dateRow}:H{$this->dateRow}");
                $sheet->getStyle("A{$this->dateRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'color' => ['rgb' => '6B7280']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER
                    ]
                ]);

                // --- 3. Ringkasan Metrik Keuangan ---
                $sheet->mergeCells("A{$this->metricsTitleRow}:H{$this->metricsTitleRow}");
                $sheet->getStyle("A{$this->metricsTitleRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);

                // Header Metrics
                $sheet->getStyle("A{$this->metricsHeaderRow}:F{$this->metricsHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F46E5'] // Indigo Brand
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Value Metrics
                $sheet->getStyle("A{$this->metricsValueRow}:F{$this->metricsValueRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '111827']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F3F4F6']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB']
                        ]
                    ]
                ]);

                // Format summary numbers as currency (A:D)
                $sheet->getStyle("A{$this->metricsValueRow}:D{$this->metricsValueRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

                // --- 4. Sales By Menus Section ---
                $sheet->mergeCells("A{$this->menuTitleRow}:H{$this->menuTitleRow}");
                $sheet->getStyle("A{$this->menuTitleRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]
                ]);

                // Table Header Sales By Menus
                $sheet->getStyle("A{$this->menuHeaderRow}:H{$this->menuHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '10B981'] // Green Emerald
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER
                    ]
                ]);

                // Format Items columns
                if ($this->menuEndRow >= $this->menuStartRow) {
                    $sheet->getStyle("A{$this->menuStartRow}:B{$this->menuEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("C{$this->menuStartRow}:C{$this->menuEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D{$this->menuStartRow}:H{$this->menuEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Currency formats
                    $sheet->getStyle("D{$this->menuStartRow}:H{$this->menuEndRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

                    // Item borders
                    $sheet->getStyle("A{$this->menuStartRow}:H{$this->menuEndRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E5E7EB']
                            ]
                        ]
                    ]);

                    // Highlight Profit column
                    $sheet->getStyle("H{$this->menuStartRow}:H{$this->menuEndRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '047857']],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'ECFDF5']
                        ]
                    ]);
                }

                // --- 5. Breakdowns (Order Types & Payment Methods) ---
                // Merge headers
                $sheet->mergeCells("A{$this->breakdownTitleRow}:C{$this->breakdownTitleRow}");
                $sheet->getStyle("A{$this->breakdownTitleRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']]
                ]);

                $sheet->mergeCells("F{$this->breakdownTitleRow}:H{$this->breakdownTitleRow}");
                $sheet->getStyle("F{$this->breakdownTitleRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']]
                ]);

                // Style table headers
                $sheet->getStyle("A{$this->breakdownHeaderRow}:C{$this->breakdownHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '3B82F6'] // Sky blue
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                $sheet->getStyle("F{$this->breakdownHeaderRow}:H{$this->breakdownHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '8B5CF6'] // Purple
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Data styles
                if ($this->breakdownDataEndRow >= $this->breakdownDataStartRow) {
                    $sheet->getStyle("A{$this->breakdownDataStartRow}:A{$this->breakdownDataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("B{$this->breakdownDataStartRow}:B{$this->breakdownDataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$this->breakdownDataStartRow}:C{$this->breakdownDataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("C{$this->breakdownDataStartRow}:C{$this->breakdownDataEndRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

                    $sheet->getStyle("F{$this->breakdownDataStartRow}:F{$this->breakdownDataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("G{$this->breakdownDataStartRow}:G{$this->breakdownDataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H{$this->breakdownDataStartRow}:H{$this->breakdownDataEndRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("H{$this->breakdownDataStartRow}:H{$this->breakdownDataEndRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

                    // Borders
                    $sheet->getStyle("A{$this->breakdownDataStartRow}:C{$this->breakdownDataEndRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E5E7EB']
                            ]
                        ]
                    ]);
                    $sheet->getStyle("F{$this->breakdownDataStartRow}:H{$this->breakdownDataEndRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E5E7EB']
                            ]
                        ]
                    ]);
                }

                // --- 6. Detailed Profitability & Expenses Section ---
                $sheet->mergeCells("A{$this->profitTitleRow}:E{$this->profitTitleRow}");
                $sheet->getStyle("A{$this->profitTitleRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']]
                ]);

                // Table headers
                $sheet->getStyle("A{$this->profitHeaderRow}:E{$this->profitHeaderRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '374151'] // Slate dark
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // Data Merges & Alignment
                for ($row = $this->profitDataStartRow; $row <= $this->profitDataEndRow; $row++) {
                    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("B{$row}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

                    $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
                }

                // Table borders
                $sheet->getStyle("A{$this->profitDataStartRow}:B{$this->profitDataEndRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB']
                        ]
                    ]
                ]);
                $sheet->getStyle("D{$this->profitDataStartRow}:E{$this->profitDataEndRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E5E7EB']
                        ]
                    ]
                ]);

                // Bold titles in profitability columns
                $sheet->getStyle("A{$this->profitDataStartRow}:A{$this->profitDataEndRow}")->getFont()->setBold(true);
                $sheet->getStyle("D{$this->profitDataStartRow}:D{$this->profitDataEndRow}")->getFont()->setBold(true);

                // Highlight Net Profit Row (Last row on Right Table)
                $sheet->getStyle("D{$this->profitDataEndRow}:E{$this->profitDataEndRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1E3A8A']], // Dark blue
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DBEAFE'] // Light blue soft
                    ]
                ]);
                
                // Highlight Real Received Row (Last row on Left Table)
                $sheet->getStyle("A{$this->profitDataEndRow}:B{$this->profitDataEndRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '047857']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'ECFDF5']
                    ]
                ]);

                // --- 7. Column Dimensions ---
                $sheet->getColumnDimension('A')->setWidth(18); // Kategori
                $sheet->getColumnDimension('B')->setWidth(26); // Nama Menu
                $sheet->getColumnDimension('C')->setWidth(14); // Qty
                $sheet->getColumnDimension('D')->setWidth(16); // Harga Jual
                $sheet->getColumnDimension('E')->setWidth(18); // Estimasi HPP Satuan
                $sheet->getColumnDimension('F')->setWidth(16); // Total HPP
                $sheet->getColumnDimension('G')->setWidth(18); // Total Penjualan
                $sheet->getColumnDimension('H')->setWidth(18); // Keuntungan Bersih

                // Row Heights for headers
                $sheet->getRowDimension($this->titleRow)->setRowHeight(28);
                $sheet->getRowDimension($this->metricsHeaderRow)->setRowHeight(22);
                $sheet->getRowDimension($this->metricsValueRow)->setRowHeight(26);
                $sheet->getRowDimension($this->menuHeaderRow)->setRowHeight(22);
                $sheet->getRowDimension($this->breakdownHeaderRow)->setRowHeight(22);
                $sheet->getRowDimension($this->profitHeaderRow)->setRowHeight(22);
            }
        ];
    }
}
