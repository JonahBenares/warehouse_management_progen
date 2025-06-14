<?php

namespace App\Exports;

use App\Models\ReceiveItems;
use App\Models\IssuanceItems;
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

class IssueExport implements FromQuery, WithMapping, ShouldAutoSize, WithHeadings, WithEvents, WithTitle, WithCustomStartCell, WithDrawings, WithStrictNullComparison
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
        $querydisp=IssuanceItems::query()->with('issuance_head','items','variants');
        if ($from!='null' && $to!='null') {
            $querydisp->whereHas('issuance_head', function ($querydisp) use ($from, $to) {
                $querydisp->whereBetween('issuance_date', [$from, $to]);
            });
        }

        if ($item!=0) {
            $querydisp->where('item_id', $item);
        }

        if ($pr!='null') {
            $querydisp->whereHas('issuance_head', function ($querydisp) use ($pr) {
                $querydisp->where('pr_no', $pr);
            });    
        }

        if ($category!=0) {
            $querydisp->whereHas('items', function ($querydisp) use ($category) {
                $querydisp->where('item_category_id', $category);
            });    
        }

        if ($subcategory!=0) {
            $querydisp->whereHas('items', function ($querydisp) use ($subcategory) {
                $querydisp->where('item_sub_category_id', $subcategory);
            });
        }

        if ($department!=0) {
            $querydisp->whereHas('issuance_head', function ($querydisp) use ($department) {
                $querydisp->where('department_id', $department);
            }); 
        }

        if ($enduse!=0) {
            $querydisp->whereHas('issuance_head', function ($querydisp) use ($enduse) {
                $querydisp->where('enduse_id', $enduse);
            }); 
        }

        if ($purpose!=0) {
            $querydisp->whereHas('issuance_head', function ($querydisp) use ($purpose) {
                $querydisp->where('purpose_id', $purpose);
            }); 
        }
        $iss_list=$querydisp->get();
        $pr_cost=array();
        $wh_cost=array();
        $pr_wo_cost=0;
        $wh_wo_cost=0;
        foreach($iss_list AS $ia){
            $request_type=RequestHead::where('id',$ia->issuance_head->request_head_id)->value('request_type');
            $total_cost=(float) $ia->issued_qty * (float) $ia->unit_cost;
            if($request_type=='With PR'){
                $pr_cost[] = $total_cost;
                if($ia->unit_cost == 0){
                    $pr_wo_cost++;
                }
            } else {
                $wh_cost[] =$total_cost;
                if($ia->unit_cost == 0){
                    $wh_wo_cost++;
                }
            }
        }
        $prcost_sum=array_sum($pr_cost);
        $whcost_sum=array_sum($wh_cost);
        $this->pr_cost=$prcost_sum;
        $this->wh_cost=$whcost_sum;
        $this->pr_wo_cost=$pr_wo_cost;
        $this->wh_wo_cost=$wh_wo_cost;
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
        return 'A11';
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
        $query=IssuanceItems::query()->with('issuance_head','items','variants');
        if ($from!='null' && $to!='null') {
            $query->whereHas('issuance_head', function ($query) use ($from, $to) {
                $query->whereBetween('issuance_date', [$from, $to]);
            });
        }

        if ($item!=0) {
            $query->where('item_id', $item);
        }

        if ($pr!='null') {
            $query->whereHas('issuance_head', function ($query) use ($pr) {
                $query->where('pr_no', $pr);
            });    
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
            $query->whereHas('issuance_head', function ($query) use ($department) {
                $query->where('department_id', $department);
            }); 
        }

        if ($enduse!=0) {
            $query->whereHas('issuance_head', function ($query) use ($enduse) {
                $query->where('enduse_id', $enduse);
            }); 
        }

        if ($purpose!=0) {
            $query->whereHas('issuance_head', function ($query) use ($purpose) {
                $query->where('purpose_id', $purpose);
            }); 
        }
        return $query;
    }
    
    public function map($ia): array
    {
        $uom=($ia->variants) ? $ia->variants->uom : '';
        $supplier_name=($ia->variants) ? $ia->variants->supplier_name : '';
        $pr_no=$ia->issuance_head->pr_no;
        $item_id=$ia->item_id;
        $variant_id=$ia->variant_id;
        $po_no=ReceiveHead::with('receive_items','receive_details')->whereHas('receive_details', function ($rec) use ($pr_no) {
            $rec->where('pr_no', $pr_no);
        })->whereHas('receive_items', function ($reci) use ($item_id,$variant_id) {
            $reci->where('item_id', $item_id)->where('variant_id', $variant_id);
        })->value('po_no');
        return [
            date('F d,Y',strtotime($ia->issuance_head->issuance_date)),
            $po_no,
            $ia->issuance_head->mif_no,
            $ia->issuance_head->pr_no,
            $ia->items->pn_no,
            $ia->item_description,
            (float) $ia->issued_qty,
            $uom,
            (float) $ia->unit_cost,
            $ia->currency,
            (float) $ia->issued_qty * (float) $ia->unit_cost,
            $supplier_name,
            $ia->issuance_head->department_name,
            $ia->issuance_head->enduse_name,
            $ia->issuance_head->purpose_name,
            '',
            '',
            '',
        ];
    }

    public function headings(): array
    {
        return [
            'A9'=>'Issue Date',
            'B9'=>'PO No.',
            'C9'=>'MIF No.',
            'D9'=>'PR No.',
            'E9'=>'Part No.',
            'F9'=>'Item Description',
            'G9'=>'Total Qty Issued',
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
                $event->sheet->getStyle('A11:R11')->applyFromArray([
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
                for($i=11;$i<=$totalRows;$i++){
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
                $event->sheet->getDelegate()->getStyle('B9')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F7FFBC']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle('B10')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F7FFBC']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle('E9')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F7FFBC']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle('E10')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F7FFBC']
                    ]
                ]);
                $event->sheet->getStyle('A9:E9')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $event->sheet->getStyle('A10:E10')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getProtection()->setSort(true);
                $event->sheet->getProtection()->setInsertRows(true);
                $event->sheet->getProtection()->setFormatCells(true);
                $event->sheet->getProtection()->setPassword('Inventory2024!');
                $item_name=Items::where('id',$this->item)->value('item_description');
                $category_name=ItemCategory::where('id',$this->category)->value('category_name');
                $subcategory_name=ItemSubCategory::where('id',$this->subcategory)->value('subcat_name');
                $event->sheet->getStyle('A11:R11')->getAlignment()->setHorizontal('center');
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
                $event->sheet->setCellValue('H2', 'SUMMARY OF ISSUED MATERIALS');
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

                $event->sheet->getStyle('A9')->getFont()->setBold(true);
                $event->sheet->getStyle('A10')->getFont()->setBold(true);
                $event->sheet->getStyle('C9')->getFont()->setBold(true);
                $event->sheet->getStyle('C10')->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A9')->getFont()->setSize(10);
                $event->sheet->getDelegate()->getStyle('A10')->getFont()->setSize(10);
                $event->sheet->getDelegate()->getStyle('C9')->getFont()->setSize(10);
                $event->sheet->getDelegate()->getStyle('C10')->getFont()->setSize(10);
                $event->sheet->setCellValue('A9', 'Total Cost w/ PR: ');
                $event->sheet->setCellValue('B9', $this->pr_cost);
                $event->sheet->setCellValue('A10', 'Total Cost of WH Stocks: ');
                $event->sheet->setCellValue('B10', $this->wh_cost);

                $event->sheet->setCellValue('C9', 'Total Number of Items w/ PR w/o Cost: ');
                $event->sheet->setCellValue('E9', $this->pr_wo_cost);

                $event->sheet->setCellValue('C10', 'Total Number of Items from WH Stocks w/o Cost: ');
                $event->sheet->setCellValue('E10', $this->wh_wo_cost);
                
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
        return 'Issued Report';
    }
}
