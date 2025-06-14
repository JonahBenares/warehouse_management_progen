<?php

namespace App\Exports;

use App\Models\Items;
use App\Models\Variants;
use App\Models\VariantsBalance;
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

class VariantsExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithEvents, WithTitle, WithCustomStartCell, WithDrawings, WithStrictNullComparison
{
     /**
    * @return \Illuminate\Support\Collection
    */
    protected $item;
    use Exportable;

   

    public function __construct($item) {
        $this->item = $item;
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

    public function collection()
    {
        $item=$this->item;
        if($item!='0'){
            $query1 = VariantsBalance::with(['variants'])->where('item_id','=',$item)->where('variant_id','!=','0')->get();
            $query2 = VariantsBalance::with(['variants'])->where('item_id','=',$item)->where('variant_id', '=', '0')->get();
        }
        return $query1->merge($query2);
    }
    
    public function map($re): array
    {   
        if($re->variants){
            if($re->variants->item_status_id == '0'){
                $status = '';
            } else {
                $status = $re->variants->item_status->status;
            }
        }
        return [
            Items::where('id','=',$re->item_id)->value('item_description'),
            (($re->variants) ? (($re->variants->supplier_name!='') ? $re->variants->supplier_name." | " : '')."".(($re->variants->uom!='') ? $re->variants->uom." | " : '')."".(($re->variants->brand!='') ? $re->variants->brand." | " : '')."".(($re->variants->color!='') ? $re->variants->color." | " : '')."".(($re->variants->size!='') ? $re->variants->size." | " : '')."".(($status!='') ? $status." | " : '')."".(($re->variants->average_cost!='0') ? $re->variants->average_cost." ".$re->variants->currency : '') : ''),
            $re->whstocks_qty,
            $re->composite_qty,
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
            'A5'=>'Item',
            'B5'=>'Variant',
            'C5'=>'WH Stocks Qty',
            'D5'=>'Composite Qty',
            'E5'=>'Receive Qty',
            'F5'=>'Issuance Qty',
            'G5'=>'Restock Qty (+)',
            'H5'=>'Transfer (-)',
            'I5'=>'Damage Qty',
            'J5'=>'Borrowed Qty (-)',
            'K5'=>'Reimbursed Qty (+)',
            'L5'=>'Lent Qty (+)',
            'M5'=>'Replenished Qty (-)',
            'N5'=>'Backorder Qty',
            'O5'=>'Balance',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                $event->sheet->getStyle('A5:O5')->applyFromArray([
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
                    $event->sheet->getStyle('O'.$x)->applyFromArray([
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
                $event->sheet->getStyle('C3:O3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $totalRows = $event->sheet->getHighestRow();
                for($i=5;$i<=$totalRows;$i++){
                    $event->sheet->getStyle('A'.$i.':O'.$i)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ]
                    ]);
                    $event->sheet->getStyle('A'.$i)->getAlignment()->setHorizontal('center');
                    $event->sheet->getStyle('C'.$i.':O'.$i)->applyFromArray([
                       'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);
                }
                for($c=6;$c<=$totalRows;$c++){
                    $event->sheet->getDelegate()->getStyle('O'.$c)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'F7FFBC']
                        ]
                    ]);
                }
                $event->sheet->getDelegate()->getStyle('O5')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F7FFBC']
                    ]
                ]);
                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getProtection()->setSort(true);
                $event->sheet->getProtection()->setInsertRows(true);
                $event->sheet->getProtection()->setFormatCells(true);
                $event->sheet->getProtection()->setPassword('Inventory2024!');
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getStyle('A5:N5')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('B1')->getFont()->setBold(true);
                $event->sheet->setCellValue('B1', 'CENTRAL NEGROS POWER RELIABILITY, INC.');
                $event->sheet->setCellValue('B2', 'Prk. San Jose, Brgy. Calumangan, Bago City');
                $event->sheet->setCellValue('B3', 'Tel. No. 476-7382');
                $event->sheet->setCellValue('H2', 'VARIANTS REPORT');
                $event->sheet->getStyle('H2:K2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle("H2")->getFont()->setBold(true)->setName('Arial Black');
                $event->sheet->mergeCells('H2:K2');
            }
        ];
    }

    public function title(): string
    {
        return 'Variants Report';
    }
}
