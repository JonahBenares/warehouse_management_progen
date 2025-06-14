<?php

namespace App\Exports;
use App\Models\PRItems;
use App\Models\Items;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class PRoverallExport implements FromQuery, WithMapping, ShouldAutoSize, WithHeadings, WithEvents, WithTitle, WithCustomStartCell, WithDrawings, WithStrictNullComparison
{
     /**
    * @return \Illuminate\Support\Collection
    */
    protected $item;
    protected $pr_no;
    use Exportable;

   

    public function __construct($item, $pr_no) {
        $this->item = $item;
        $this->pr = $pr_no;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/images/default/logo_cenpri.png'));
        $drawing->setHeight(35);
        $drawing->setOffsetY(-6);
        $drawing->setOffsetX(7);
        $drawing->setCoordinates('A2');

        return $drawing;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function query()
    {
        $item=$this->item;
        $pr=$this->pr;
        if($item!='undefined' && $pr!='undefined'){
            $query = PRItems::query()->where('pr_no','=',$pr)->where('item_id','=',$item);

        } else if($item=='undefined' && $pr!='undefined'){
             $query = PRItems::query()->where('pr_no','=',$pr);

        } else if($item!='undefined' && $pr=='undefined' ){
            $query = PRItems::query()->where('item_id','=',$item);
        }
        return $query;
    }
    
    public function map($re): array
    {
        return [
            $re->pr_no,
            Items::where('id','=',$re->item_id)->value('item_description'),
            $re->begbal,
            $re->receive_qty,
            $re->issuance_qty,
            $re->restock_qty,
            $re->transfer_qty,
            $re->damage_qty,
            $re->borrow_deduct,
            $re->replenish_add,
            $re->borrow_add,
            $re->replenish_deduct,
            $re->backorder_qty,
            $re->balance,
        ];
    }

    public function headings(): array
    {
        return [
            'A5'=>'PR',
            'B5'=>'Item',
            'C5'=>'Beg Balance',
            'D5'=>'Receive Qty',
            'E5'=>'Issuance Qty',
            'F5'=>'Restock Qty (+)',
            'G5'=>'Transfer (-)',
            'H5'=>'Damage Qty',
            'I5'=>'Borrowed Qty (-)',
            'J5'=>'Reimbursed Qty (+)',
            'K5'=>'Lent Qty (+)',
            'L5'=>'Replenished Qty (-)',
            'M5'=>'Backorder Qty',
            'N5'=>'Balance',
            'O9'=>'Actual Count',
            'P9'=>'Date',
            'Q9'=>'Notes',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                $event->sheet->getStyle('A5:Q5')->applyFromArray([
                    'font'=> [
                        'bold'=>true
                    ]
                ]);
                for($x=1;$x<=3;$x++){
                    $event->sheet->getStyle('A'.$x)->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ],
                    ]);
                }
                for($x=1;$x<=3;$x++){
                    $event->sheet->getStyle('D'.$x)->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ],
                    ]);
                }
                for($x=1;$x<=3;$x++){
                    $event->sheet->getStyle('Q'.$x)->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ],
                    ]);
                }
                $event->sheet->getStyle('A3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $event->sheet->getStyle('B3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $event->sheet->getStyle('C3:Q3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $totalRows = $event->sheet->getHighestRow();
                for($i=5;$i<=$totalRows;$i++){
                    $event->sheet->getStyle('A'.$i.':Q'.$i)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ]
                    ]);
                    $event->sheet->getStyle('A'.$i)->getAlignment()->setHorizontal('center');
                    $event->sheet->getStyle('C'.$i.':Q'.$i)->applyFromArray([
                       'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);
                }
                for($c=6;$c<=$totalRows;$c++){
                    $event->sheet->getDelegate()->getStyle('A'.$c.':M'.$c)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'cab3f0']
                        ]
                    ]);
                    $event->sheet->getDelegate()->getStyle('N'.$c)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'F7FFBC']
                        ]
                    ]);
                }
                $event->sheet->getDelegate()->getStyle('N5')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F7FFBC']
                    ]
                ]);
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getStyle('A5:Q5')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('B1')->getFont()->setBold(true);
                $event->sheet->setCellValue('B1', 'CENTRAL NEGROS POWER RELIABILITY, INC.');
                $event->sheet->setCellValue('B2', 'Prk. San Jose, Brgy. Calumangan, Bago City');
                $event->sheet->setCellValue('B3', 'Tel. No. 476-7382');
                $event->sheet->setCellValue('H2', 'PR OVERALL REPORT');
                $event->sheet->getStyle('H2:K2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle("H2")->getFont()->setBold(true)->setName('Arial Black');
                $event->sheet->mergeCells('H2:K2');
                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getProtection()->setSort(true);
                $event->sheet->getProtection()->setInsertRows(true);
                $event->sheet->getProtection()->setFormatCells(true);
                $event->sheet->getProtection()->setPassword('Inventory2024!');
            }
        ];
    }

    public function title(): string
    {
        return 'PR Overall Report';
    }
}
