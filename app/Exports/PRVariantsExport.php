<?php

namespace App\Exports;

use App\Models\Items;
use App\Models\Variants;
use App\Models\VariantsBalance;
use App\Models\PIVBalance;
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

class PRVariantsExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithEvents, WithTitle, WithCustomStartCell, WithDrawings, WithStrictNullComparison
{
     /**
    * @return \Illuminate\Support\Collection
    */
    protected $item;
    protected $pr_no;
    use Exportable;

   

    public function __construct($item,$pr_no) {
        $this->item = $item;
        $this->pr_no = $pr_no;
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
        $pr=$this->pr_no;
        if($item!='undefined' && $pr!='undefined'){
            $report = PIVBalance::with(['variants'])->where('pr_no','=',$pr)->where('item_id','=',$item)->where('variant_id','!=','0')->get();
            $report_wh = PIVBalance::with(['variants'])->where('pr_no','=',$pr)->where('item_id','=',$item)->where('variant_id','=','0')->get();
        } else if($item=='undefined' && $pr!='undefined'){
             $report = PIVBalance::with(['variants'])->where('pr_no','=',$pr)->where('variant_id','!=','0')->get();
             $report_wh = PIVBalance::with(['variants'])->where('pr_no','=',$pr)->where('variant_id','=','0')->get();
        } else if($item!='undefined' && $pr=='undefined' ){
            $report = PIVBalance::with(['variants'])->where('item_id','=',$item)->where('variant_id','!=','0')->get();
            $report_wh = PIVBalance::with(['variants'])->where('item_id','=',$item)->where('variant_id','=','0')->get();
        }
        return $report->merge($report_wh);
    }
    
    public function map($re): array
    {   
        if($re->variants){
            if($re->variants->item_status_id == '0'){
                $status = '';
            } else {
                $status = $re->variants->item_status->status;
            }
        }else{
            $status='';
        }
        return [
            $re->pr_no,
            Items::where('id','=',$re->item_id)->value('item_description'),
            (($re->variants) ? (($re->variants->supplier_name!='') ? $re->variants->supplier_name." | " : '')."".(($re->variants->uom!='') ? $re->variants->uom." | " : '')."".(($re->variants->brand!='') ? $re->variants->brand." | " : '')."".(($re->variants->color!='') ? $re->variants->color." | " : '')."".(($re->variants->size!='') ? $re->variants->size." | " : '')."".(($status!='') ? $status." | " : '')."".(($re->variants->average_cost!='0') ? $re->variants->average_cost." ".$re->variants->currency : '') : ''),
            $re->quantity,
        ];
    }

    public function headings(): array
    {
        return [
            'A5'=>'PR',
            'B5'=>'Item',
            'C5'=>'Variant',
            'D5'=>'Balance',
            'E9'=>'Actual Count',
            'F9'=>'Date',
            'G9'=>'Notes',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                $event->sheet->getStyle('A5:G5')->applyFromArray([
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
                    $event->sheet->getStyle('B'.$x)->applyFromArray([
                        'borders' => [
                            'right' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ],
                    ]);
                }
                for($x=1;$x<=3;$x++){
                    $event->sheet->getStyle('G'.$x)->applyFromArray([
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
                $event->sheet->getStyle('C3:G3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $totalRows = $event->sheet->getHighestRow();
                for($i=5;$i<=$totalRows;$i++){
                    $event->sheet->getStyle('A'.$i.':G'.$i)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ]
                    ]);
                    $event->sheet->getStyle('A'.$i)->getAlignment()->setHorizontal('center');
                    $event->sheet->getStyle('D'.$i)->applyFromArray([
                       'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);
                }
                for($c=6;$c<=$totalRows;$c++){
                    $event->sheet->getDelegate()->getStyle('A'.$c.':D'.$c)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'cab3f0']
                        ]
                    ]);
                    $event->sheet->getDelegate()->getStyle('D'.$c)->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['rgb' => 'F7FFBC']
                        ]
                    ]);
                }
                $event->sheet->getDelegate()->getStyle('D5')->applyFromArray([
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
                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getProtection()->setSort(true);
                $event->sheet->getProtection()->setInsertRows(true);
                $event->sheet->getProtection()->setFormatCells(true);
                $event->sheet->getProtection()->setPassword('Inventory2024!');
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(30);
                $event->sheet->getStyle('A5:G5')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('B1')->getFont()->setBold(true);
                $event->sheet->setCellValue('B1', 'CENTRAL NEGROS POWER RELIABILITY, INC.');
                $event->sheet->setCellValue('B2', 'Prk. San Jose, Brgy. Calumangan, Bago City');
                $event->sheet->setCellValue('B3', 'Tel. No. 476-7382');
                $event->sheet->setCellValue('D2', 'PR VARIANTS REPORT');
                $event->sheet->getStyle('D2:F2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle("D2")->getFont()->setBold(true)->setName('Arial Black');
                $event->sheet->mergeCells('D2:F2');
            }
        ];
    }

    public function title(): string
    {
        return 'PR Variants Report';
    }
}
