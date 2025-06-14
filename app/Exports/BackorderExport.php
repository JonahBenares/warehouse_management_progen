<?php

namespace App\Exports;

use App\Models\ReceiveItems;
use App\Models\Items;
use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use App\Models\BackorderItems;
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

class BackorderExport implements FromQuery, WithMapping, ShouldAutoSize, WithHeadings, WithEvents, WithTitle, WithCustomStartCell, WithDrawings, WithStrictNullComparison
{
     /**
    * @return \Illuminate\Support\Collection
    */
    protected $from;
    protected $to;
    protected $item;
    protected $pr;
    protected $category;
    protected $subcategory;
    protected $department;
    protected $enduse;
    protected $purpose;
    use Exportable;

   

    public function __construct($from, $to, $item, $pr, $category, $subcategory, $department, $enduse, $purpose) {
        $this->from = $from;
        $this->to = $to;
        $this->item = $item;
        $this->pr = $pr;
        $this->category = $category;
        $this->subcategory = $subcategory;
        $this->department = $department;
        $this->enduse = $enduse;
        $this->purpose = $purpose;
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
        return 'A9';
    }

    public function query()
    {
        $from=$this->from;
        $to=$this->to;
        $item=$this->item;
        $pr=$this->pr;
        $category=$this->category;
        $subcategory=$this->subcategory;
        $department=$this->department;
        $enduse=$this->enduse;
        $purpose=$this->purpose;
        $query = ReceiveItems::query()->with(['receive_head','receive_details','items']);
        $query->whereHas('receive_head', function ($query) {
            $query->where('saved', '1')->where('closed','1');
        });
        if ($from!='null' && $to!='null') {
            $from_date=$from;
            $to_date=$to;
            $query->whereHas('receive_head', function ($query) use ($from_date, $to_date) {
                $query->whereBetween('receive_date', [$from_date, $to_date]);
            });
        }

        if ($item!='0') {
            $query->where('item_id', $item);
        }

        if ($pr!='null') {
            $query->whereHas('receive_details', function ($query) use ($pr) {
                $query->where('pr_no', $pr);
            });    
        }

        if ($category!='0') {
            $query->whereHas('items', function ($query) use ($category) {
                $query->where('item_category_id', $category);
            });
        }

        if ($subcategory!='0') {
            $query->whereHas('items', function ($query) use ($subcategory) {
                $query->where('item_sub_category_id', $subcategory);
            });   
        }

        if ($department!='0') {
            $query->whereHas('receive_details', function ($query) use ($department) {
                $query->where('department_id', $department);
            });    
        }

        if ($enduse!='0') {
            $query->whereHas('receive_details', function ($query) use ($enduse) {
                $query->where('enduse_id', $enduse);
            });    
        }

        if ($purpose!='0') {
            $query->whereHas('receive_details', function ($query) use ($purpose) {
                $query->where('purpose_id', $purpose);
            });    
        }
        return $query;
    }
    
    public function map($ra): array
    {
        $total_bo_qty = BackorderItems::where('receive_items_id','=',$ra->id)->where('item_id', '=', $ra->item_id)->where('variant_id', '=', $ra->variant_id)->sum('bo_quantity');
        $overall_bo = (float)$ra->exp_quantity - ((float)$ra->rec_quantity + (float)$total_bo_qty);
        if($overall_bo != 0){
            return [
                date('F d,Y',strtotime($ra->receive_head->receive_date)),
                $ra->receive_head->po_no,
                $ra->receive_head->dr_no,
                $ra->receive_head->mrecf_no,
                $ra->receive_details->pr_no,
                $ra->items->pn_no,
                $ra->item_description,
                (float) $ra->exp_quantity,
                (float) $ra->rec_quantity,
                (float)$overall_bo,
                $ra->uom,
                $ra->unit_cost,
                $ra->currency,
                $ra->rec_quantity * $ra->unit_cost,
                $ra->supplier_name,
                $ra->receive_details->department_name,
                $ra->receive_details->enduse_name,
                $ra->receive_details->purpose_name,
                '',
                '',
                '',
            ];
        }else{
            return [];
        }
    }

    public function headings(): array
    {
        return [
            'A9'=>'Receive Date',
            'B9'=>'PO No.',
            'C9'=>'DR No.',
            'D9'=>'MRIF No.',
            'E9'=>'PR No.',
            'F9'=>'Part No.',
            'G9'=>'Item Description',
            'H9'=>'Expected Qty',
            'I9'=>'Total Qty Receive',
            'J9'=>'BO Qty',
            'K9'=>'Uom',
            'L9'=>'Unit Cost',
            'M9'=>'Currency',
            'N9'=>'Total Cost',
            'O9'=>'Supplier',
            'P9'=>'Department',
            'Q9'=>'Enduse',
            'R9'=>'Purpose',
            'S9'=>'Actual Count',
            'T9'=>'Date',
            'U9'=>'Notes',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                $event->sheet->getStyle('A9:U9')->applyFromArray([
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
                    $event->sheet->getStyle('U'.$x)->applyFromArray([
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
                $event->sheet->getStyle('C3:U3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $event->sheet->getStyle('C5')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ]
                ]);
                $event->sheet->getStyle('E5')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ]
                ]);
                $event->sheet->getStyle('B7')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $event->sheet->getStyle('D7')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $event->sheet->getStyle('F7')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $totalRows = $event->sheet->getHighestRow();
                for($i=9;$i<=$totalRows;$i++){
                    $event->sheet->getStyle('A'.$i.':U'.$i)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ]
                    ]);
                    $event->sheet->getStyle('A'.$i.':F'.$i)->applyFromArray([
                       'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);
                    $event->sheet->getStyle('H'.$i.':N'.$i)->applyFromArray([
                        'alignment' => [
                             'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                         ]
                     ]);
                }

                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getProtection()->setSort(true);
                $event->sheet->getProtection()->setInsertRows(true);
                $event->sheet->getProtection()->setFormatCells(true);
                $event->sheet->getProtection()->setPassword('Inventory2024!');
                
                $item_name=Items::where('id',$this->item)->value('item_description');
                $category_name=ItemCategory::where('id',$this->category)->value('category_name');
                $subcategory_name=ItemSubCategory::where('id',$this->subcategory)->value('subcat_name');
                $event->sheet->getStyle('A9:U9')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('B1')->getFont()->setBold(true);
                $event->sheet->getStyle('I2')->getFont()->setBold(true);
                $event->sheet->getStyle('A5')->getFont()->setBold(true);
                $event->sheet->getStyle('A7')->getFont()->setBold(true);
                $event->sheet->getStyle('B5')->getFont()->setBold(true);
                $event->sheet->getStyle('C7')->getFont()->setBold(true);
                $event->sheet->getStyle('D5')->getFont()->setBold(true);
                $event->sheet->getStyle('E7')->getFont()->setBold(true);
                $event->sheet->getStyle('B5:D5')->getAlignment()->setHorizontal('right');
                $event->sheet->setCellValue('A5', 'Period Covered: ');
                $event->sheet->setCellValue('A7', 'Main Category: ');
                $event->sheet->setCellValue('B7', (($this->category!=0) ? $category_name : ''));
                $event->sheet->setCellValue('B1', 'CENTRAL NEGROS POWER RELIABILITY, INC.');
                $event->sheet->setCellValue('B2', 'Prk. San Jose, Brgy. Calumangan, Bago City');
                $event->sheet->setCellValue('B3', 'Tel. No. 476-7382');
                $event->sheet->setCellValue('B5', 'FROM: ');
                $event->sheet->setCellValue('C5', (($this->from!='null') ? date('F d, Y',strtotime($this->from)) : ''));
                $event->sheet->setCellValue('D5', 'TO: ');
                $event->sheet->setCellValue('E5', (($this->to!='null') ? date('F d, Y',strtotime($this->to)) : ''));
                $event->sheet->setCellValue('C7', 'Subcategory: ');
                $event->sheet->setCellValue('D7', (($this->subcategory!=0) ? $subcategory_name : ''));
                $event->sheet->setCellValue('E7', 'Item Name: ');
                $event->sheet->setCellValue('F7', (($this->item!=0) ? $item_name : ''));
                $event->sheet->setCellValue('H2', 'SUMMARY OF BACKORDER MATERIALS');
                $event->sheet->getStyle('H2:K2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle("H2")->getFont()->setBold(true)->setName('Arial Black');
                $event->sheet->mergeCells('H2:K2');

                foreach(range('A','F') as $columnID) {
                    $event->sheet->getDelegate()->getColumnDimension($columnID)
                        ->setAutoSize(false);
                }
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(20);
                
                $sig=$totalRows + 2;
                $pos=$totalRows + 5;
                $event->sheet->setCellValue('A'.$sig, 'Prepared By: ');
                $event->sheet->setCellValue('A'.$pos, 'Warehouse Personnel');
                $event->sheet->setCellValue('C'.$sig, 'Checked By: ');
                $event->sheet->setCellValue('C'.$pos, 'Warehouse Supervisor');
                $event->sheet->setCellValue('E'.$sig, 'Approved By: ');
                $event->sheet->setCellValue('E'.$pos, 'Plant Director/Plant Manager');
            }
        ];
    }

    public function title(): string
    {
        return 'Backorder Report';
    }
}
