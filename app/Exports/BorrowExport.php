<?php

namespace App\Exports;

use App\Models\BorrowDetails;
use App\Models\ReceiveItems;
use App\Models\Items;
use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use App\Models\RequestHead;
use App\Models\ReceiveHead;
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

class BorrowExport implements FromQuery, WithMapping, ShouldAutoSize, WithHeadings, WithEvents, WithTitle, WithCustomStartCell, WithDrawings, WithStrictNullComparison
{
     /**
    * @return \Illuminate\Support\Collection
    */
    protected $from;
    protected $to;
    protected $item;
    protected $borrowed_from;
    protected $borrowed_by;
    protected $category;
    protected $subcategory;
    protected $department;
    protected $enduse;
    protected $purpose;
    use Exportable;

   

    public function __construct($from, $to, $item, $borrowed_from, $borrowed_by, $category, $subcategory, $department, $enduse, $purpose) {
        $this->from = $from;
        $this->to = $to;
        $this->item = $item;
        $this->borrowed_from = $borrowed_from;
        $this->borrowed_by = $borrowed_by;
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
        $borrowed_from=$this->borrowed_from;
        $borrowed_by=$this->borrowed_by;
        $category=$this->category;
        $subcategory=$this->subcategory;
        $department=$this->department;
        $enduse=$this->enduse;
        $purpose=$this->purpose;
        $query=BorrowDetails::with('borrow_head','items','variants');
        if ($from!='null' && $to!='null') {
            $query->whereHas('borrow_head', function ($query) use ($from, $to) {
                $query->whereBetween('borrow_date', [$from, $to]);
            });
        }

        if ($item!=0) {
            $query->where('item_id', $item);
        }

        if ($borrowed_by!='null') {
            $query->where('borrowed_by', $borrowed_by);  
        }

        if ($borrowed_from!='null') {
            $query->where('borrowed_from', $borrowed_from);  
        }

        if ($category!=0) {
            $query->whereHas('items', function ($query) use ($category) {
                $query->where('item_category_id', $category);
            });    
        }

        if ($subcategory!=0) {
            $query->whereHas('items', function ($query) use ($subcategory) {
                $query->where('item_sub_category_id', $subcategory);
            });
        }

        if ($department!=0) {
            $query->where('department_id', $department);
        }

        if ($enduse!=0) {
            $query->where('enduse_id', $enduse);
        }

        if ($purpose!=0) {
            $query->where('purpose_id', $purpose);
        }
        return $query;
    }
    
    public function map($ad): array
    {
        return [
            date('F d,Y',strtotime($ad->borrow_head->borrow_date)),
            $ad->borrowed_from,
            $ad->borrowed_by,
            $ad->borrow_head->mbr_no,
            $ad->items->pn_no,
            $ad->item_description,
            (float) $ad->quantity,
            $ad->variants->uom,
            (float) $ad->variants->unit_cost,
            $ad->variants->currency,
            (float) $ad->quantity * (float) $ad->variants->unit_cost,
            $ad->variants->supplier_name,
            $ad->department_name,
            $ad->enduse_name,
            $ad->purpose_name,
            '',
            '',
            '',
        ];
    }

    public function headings(): array
    {
        return [
            'A9'=>'Borrow Date',
            'B9'=>'Borrow from',
            'C9'=>'Borrow by',
            'D9'=>'MBR No.',
            'E9'=>'Part No.',
            'F9'=>'Item Description',
            'G9'=>'Total Qty Borrow',
            'H9'=>'Uom',
            'I9'=>'Unit Cost',
            'J9'=>'Currency',
            'K9'=>'Total Cost',
            'L9'=>'Supplier',
            'M9'=>'Department',
            'N9'=>'Enduse',
            'O9'=>'Purpose',
            'P9'=>'Actual Count',
            'Q9'=>'Date',
            'R9'=>'Notes',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                $event->sheet->getStyle('A9:R9')->applyFromArray([
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
                    $event->sheet->getStyle('R'.$x)->applyFromArray([
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
                $event->sheet->getStyle('C3:R3')->applyFromArray([
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
                    $event->sheet->getStyle('A'.$i.':R'.$i)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ]
                    ]);
                    $event->sheet->getStyle('A'.$i.':E'.$i)->applyFromArray([
                       'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);
                    $event->sheet->getStyle('G'.$i.':K'.$i)->applyFromArray([
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
                $event->sheet->getStyle('A9:R9')->getAlignment()->setHorizontal('center');
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
                $event->sheet->setCellValue('H2', 'SUMMARY OF BORROWED MATERIALS');
                $event->sheet->getStyle('H2:K2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle("H2")->getFont()->setBold(true)->setName('Arial Black');
                $event->sheet->mergeCells('H2:K2');
                foreach(range('A','E') as $columnID) {
                    $event->sheet->getDelegate()->getColumnDimension($columnID)
                        ->setAutoSize(false);
                }
                foreach(range('G','K') as $columnID) {
                    $event->sheet->getDelegate()->getColumnDimension($columnID)
                        ->setAutoSize(false);
                }
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(22);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(10);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(10);
                
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
        return 'Borrowed Report';
    }
}
