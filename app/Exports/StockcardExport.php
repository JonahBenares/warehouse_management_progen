<?php

namespace App\Exports;
use App\Models\Items;
use App\Models\Variants;
use App\Models\VariantsBalance;
use App\Models\PIVBalance;
use App\Models\ItemStatus;
use App\Models\ReceiveHead;
use App\Models\ReceiveItems;
use App\Models\BackorderItems;
use App\Models\RestockDetails;
use App\Models\IssuanceItems;
use App\Models\BorrowDetails;
use App\Models\supplier;
use App\Models\department;
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
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class StockcardExport implements FromCollection, WithMapping, ShouldAutoSize, WithHeadings, WithEvents, WithTitle, WithCustomStartCell, WithDrawings, WithStrictNullComparison
{
      /**
    * @return \Illuminate\Support\Collection
    */
    protected $item;
    protected $supplier;
    protected $department;
    protected $catalog_no;
    protected $brand;
    protected $running_balance;
    use Exportable;

    public function __construct($item, $supplier, $department, $catalog_no, $brand, $running_balance) {
        $item_name=Items::where('id',$item)->value('item_description');
        $supplier_name=supplier::where('id',$supplier)->value('supplier_name');
        $department_name=department::where('id',$department)->value('department_name');
        $this->item = $item;
        $this->supplier = $supplier;
        $this->department = $department;
        $this->catalog_no = $catalog_no;
        $this->brand = $brand;
        $this->running_balance = $running_balance;
        $this->item_name = $item_name;
        $this->supplier_name = $supplier_name;
        $this->department_name = $department_name;
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

    public function collection()
    {
        $item=$this->item;
        $supplier=$this->supplier;
        $department=$this->department;
        $catalog_no=$this->catalog_no;
        $brand=$this->brand;
        $stockcard=array();
        $stockcard1=array();
        $stockcard2=array();
        $stockcard3=array();
        $stockcard4=array();
        $stockcard5=array();
        $stockcard6=array();
        $querywh=VariantsBalance::with('variants','items')->whereHas('items', function ($querywh) {
            $querywh->where('draft','0');
        });
        if ($item!=0) {
            $querywh->where('item_id', $item);
        }

        if ($supplier!=0) {
            $querywh->whereHas('variants', function ($querywh) use ($supplier) {
                $querywh->where('supplier_id', $supplier);
            });   
        }

        if ($catalog_no!='null') {
            $querywh->whereHas('variants', function ($querywh) use ($catalog_no) {
                $querywh->where('catalog_no', $catalog_no);
            });      
        }

        if ($brand!='null') {
            $querywh->whereHas('variants', function ($querywh) use ($brand) {
                $querywh->where('brand', $brand);
            });  
        }

        $beg_list=$querywh->orderBy('id','DESC')->orderBy('created_at','DESC')->get();
        $rec_qty=0;
        foreach($beg_list AS $beg){
            $rec_qty+=$beg->rec_quantity;
            $rec_status=ItemStatus::where('id',$beg->item_status_id)->value('modes');
            if((float) $beg->whstocks_qty!=0){
                $stockcard[]=[
                    'id'=>$beg->id,
                    'date'=>($beg->variants) ?  date('Y-m-d',strtotime($beg->variants->updated_at)) : date('Y-m-d',strtotime($beg->items->created_at)),
                    'pr_no'=>'WH STOCKS',
                    'po_no'=>'',
                    'supplier'=>($beg->variants) ? $beg->variants->supplier_name : '',
                    'catalog_no'=>($beg->variants) ? $beg->variants->catalog_no : '',
                    'brand'=>($beg->variants) ? $beg->variants->brand : '',
                    'department'=>'',
                    'total'=>($beg->variants) ? (float) $beg->whstocks_qty * (float) $beg->variants->unit_cost." ".$beg->variants->currency : 0,
                    'method_disp'=>'Begbal',
                    'quantity_disp'=>(float) $beg->whstocks_qty,
                    'remarks'=>'',
                    'date_created'=> $beg->created_at,
                    'method'=>'Begbal',
                    'quantity'=> (float) $beg->whstocks_qty,
                ];
            }
        }

        $queryrec=ReceiveItems::with('receive_head','receive_details')->whereHas('receive_head', function ($queryrec) {
            $queryrec->where('saved','1')->where('draft','0');
        });
        if ($item!=0) {
            $queryrec->where('item_id', $item);
        }

        if ($supplier!=0) {
            $queryrec->where('supplier_id', $supplier);    
        }

        if ($department!=0) {
            $queryrec->whereHas('receive_details', function ($queryrec) use ($department) {
                $queryrec->where('department_id', $department);
            });    
        }

        if ($catalog_no!='null') {
            $queryrec->where('catalog_no', $catalog_no);    
        }

        if ($brand!='null') {
            $queryrec->where('brand', $brand);    
        }

        $rec_list=$queryrec->orderBy('id','DESC')->orderBy('created_at','DESC')->get();
        $rec_qty=0;
        foreach($rec_list AS $ia){
            $rec_qty+=$ia->rec_quantity;
            $rec_status=ItemStatus::where('id',$ia->item_status_id)->value('modes');
            $stockcard[]=[
                'id'=>$ia->id,
                'date'=>$ia->receive_head->receive_date,
                'pr_no'=>$ia->receive_details->pr_no,
                'po_no'=>$ia->receive_head->po_no,
                'supplier'=>$ia->supplier_name,
                'catalog_no'=>$ia->catalog_no,
                'brand'=>$ia->brand,
                'department'=>$ia->receive_details->department_name,
                'total'=>(float) $ia->rec_quantity * (float) $ia->unit_cost." ".$ia->currency,
                'method_disp'=>($rec_status=='add') ? 'Receive' : 'Receive-Damage',
                'quantity_disp'=>(float) $ia->rec_quantity,
                'remarks'=>$ia->receive_head->remarks,
                'date_created'=>$ia->receive_head->updated_at,
                'method'=>($rec_status=='add') ? 'Receive' : 'Receive-Damage',
                'quantity'=>($rec_status=='add') ? (float) $ia->rec_quantity : 0,
            ];
        }

        $queryback=BackorderItems::with('backorder_head','backorder_details')->whereHas('backorder_head', function ($queryback) {
            $queryback->where('saved','1')->where('draft','0')->where('closed','0')->orderBy('updated_at','DESC');
        });
        if ($item!=0) {
            $queryback->where('item_id', $item);
        }

        if ($supplier!=0) {
            $queryback->where('supplier_id', $supplier);   
        }

        if ($department!=0) {
            $queryback->whereHas('backorder_details', function ($queryback) use ($department) {
                $queryback->where('department_id', $department);
            });    
        }

        if ($catalog_no!='null') {
            $queryback->where('catalog_no', $catalog_no);      
        }

        if ($brand!='null') {
            $queryback->where('brand', $brand);     
        }

        $back_list=$queryback->orderBy('id','DESC')->get();
        $boqty=0;
        foreach($back_list AS $bc){
            $boqty+=$bc->bo_quantity + $rec_qty;
            $pr_balance=VariantsBalance::where('item_id',$bc->item_id)->where('variant_id',$bc->variant_id)->value('balance');
            $stockcard[]=[
                'id'=>$bc->id,
                'date'=>$bc->backorder_head->backorder_date,
                'pr_no'=>$bc->backorder_details->pr_no,
                'po_no'=>$bc->backorder_head->po_no,
                'supplier'=>$bc->supplier_name,
                'catalog_no'=>$bc->catalog_no,
                'brand'=>$bc->brand,
                'department'=>$bc->backorder_details->department_name,
                'total'=>(float) $bc->bo_quantity * (float) $bc->unit_cost." ".$bc->currency,
                'method_disp'=>'Backorder',
                'quantity_disp'=>(float) $bc->bo_quantity,
                'remarks'=>$bc->remarks,
                'date_created'=>$bc->backorder_head->updated_at,
                'method'=>'Backorder',
                'quantity'=>(float) $bc->bo_quantity,
            ];
        }

        $queryres=RestockDetails::with('variants','restock_head')->whereHas('restock_head', function ($queryres) {
            $queryres->where('saved','1');
        });
        if ($item!=0) {
            $queryres->where('item_id', $item);
        }

        if ($supplier!=0) {
            $queryres->whereHas('variants', function ($queryres) use ($supplier) {
                $queryres->where('supplier_id', $supplier);
            });    
        }

        if ($department!=0) {
            $queryres->whereHas('restock_head', function ($queryres) use ($department) {
                $queryres->where('department_id', $department);
            });    
        }

        if ($catalog_no!='null') {
            $queryres->whereHas('variants', function ($queryres) use ($catalog_no) {
                $queryres->where('catalog_no', $catalog_no);
            });     
        }

        if ($brand!='null') {
            $queryres->whereHas('variants', function ($queryres) use ($brand) {
                $queryres->where('brand', $brand);
            });    
        }

        $res_list=$queryres->orderBy('id','DESC')->orderBy('created_at','DESC')->get();
        $restock_qty=0;
        foreach($res_list AS $rs){
            $restock_qty+=$rs->quantity;
            $pr_balance=VariantsBalance::where('item_id',$rs->item_id)->where('variant_id',$rs->variant_id)->value('balance');
            $item_id=$rs->item_id;
            $variant_id=$rs->variant_id;
            $pr_no=$rs->restock_head->source_pr;
            $receive_items_id=$rs->receive_items_id;
            $po_no=ReceiveHead::with('receive_items','receive_details')->whereHas('receive_details', function ($rec) use ($pr_no) {
                $rec->where('pr_no', $pr_no);
            })->whereHas('receive_items', function ($reci) use ($receive_items_id) {
                $reci->where('id', $receive_items_id);
            })->value('po_no');
            $unit_cost=Variants::where('item_id', $rs->item_id)->where('id', $rs->variant_id)->value('unit_cost');
            $currency=Variants::where('item_id', $rs->item_id)->where('id', $rs->variant_id)->value('currency');
            $res_status=ItemStatus::where('id',$rs->item_status_id)->value('modes');
            $stockcard[]=[
                'id'=>$rs->id,
                'date'=>$rs->restock_head->date,
                'pr_no'=>$rs->restock_head->source_pr,
                'po_no'=>$po_no,
                'supplier'=>$rs->variants->supplier_name,
                'catalog_no'=>$rs->variants->catalog_no,
                'brand'=>$rs->variants->brand,
                'department'=>$rs->restock_head->department,
                'total'=>(float) $rs->quantity * (float) $unit_cost." ".$currency,
                'method_disp'=>($res_status=='add') ? (($rs->identifier=='Not Issued') ? 'Restock (Not Issued)' : 'Restock (Issued)') : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? 'Restock (Not Issued) Damage' : 'Restock (Issued) Damage'),
                'quantity_disp'=>($rs->identifier=='Not Issued' && $res_status=='deduct') ? '-'.(float) $rs->quantity : (float) $rs->quantity,
                'remarks'=>$rs->remarks,
                'date_created'=>$rs->restock_head->updated_at,
                'method'=>($res_status=='add') ? 'Restock' : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? 'Restock-Damaged' : 'Restock-Damage'),
                'quantity'=>($res_status=='add') ? ($rs->identifier=='Not Issued') ? 0 : (float) $rs->quantity : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? (float) $rs->quantity : 0)
            ];
        }

        $queryiss=IssuanceItems::with('variants','issuance_head')->whereHas('issuance_head', function ($queryiss) {
            $queryiss->where('saved','0');
        });
        if ($item!=0) {
            $queryiss->where('item_id', $item);
        }

        if ($supplier!=0) {
            $queryiss->whereHas('variants', function ($queryiss) use ($supplier) {
                $queryiss->where('supplier_id', $supplier);
            });    
        }

        if ($department!=0) {
            $queryiss->whereHas('issuance_head', function ($queryiss) use ($department) {
                $queryiss->where('department_id', $department);
            });    
        }

        if ($catalog_no!='null') {
            $queryiss->whereHas('variants', function ($queryiss) use ($catalog_no) {
                $queryiss->where('catalog_no', $catalog_no);
            });     
        }

        if ($brand!='null') {
            $queryiss->whereHas('variants', function ($queryiss) use ($brand) {
                $queryiss->where('brand', $brand);
            });    
        }

        $iss_list=$queryiss->orderBy('id','DESC')->get();
        $issuance_qty=0;
        foreach($iss_list AS $is){
            $issuance_qty-=($rec_qty+$boqty)-$is->issued_qty;
            $pr_balance=VariantsBalance::where('item_id',$is->item_id)->where('variant_id',$is->variant_id)->value('balance');
            $item_id=$is->item_id;
            $variant_id=$is->variant_id;
            $pr_no=$is->issuance_head->pr_no;
            $po_no=ReceiveHead::with('receive_items','receive_details')->whereHas('receive_details', function ($rec) use ($pr_no) {
                $rec->where('pr_no', $pr_no);
            })->whereHas('receive_items', function ($reci) use ($item_id,$variant_id) {
                $reci->where('item_id', $item_id)->where('variant_id', $variant_id);
            })->value('po_no');
            $stockcard[]=[
                'id'=>$is->id,
                'date'=>$is->issuance_head->issuance_date,
                'pr_no'=>$is->issuance_head->pr_no,
                'po_no'=>$po_no,
                'supplier'=>$is->variants->supplier_name,
                'catalog_no'=>$is->variants->catalog_no,
                'brand'=>$is->variants->brand,
                'department'=>$is->issuance_head->department_name,
                'total'=>(float) $is->issued_qty * (float) $is->unit_cost." ".$is->currency,
                'method_disp'=>'Issuance',
                'quantity_disp'=>"-".(float) $is->issued_qty,
                'remarks'=>$is->issuance_head->remarks,
                'date_created'=>$is->issuance_head->issuance_date." ".$is->issuance_head->issuance_time,
                'method'=>'Issuance',
                'quantity'=>(float) $is->issued_qty,
            ];
        }

        $querybor=BorrowDetails::with('variants','borrow_head');
        if ($item!=0) {
            $querybor->where('item_id', $item);
        }

        if ($supplier!=0) {
            $querybor->whereHas('variants', function ($querybor) use ($supplier) {
                $querybor->where('supplier_id', $supplier);
            });    
        }

        if ($department!=0) {
            $querybor->where('department_id', $department); 
        }

        if ($catalog_no!='null') {
            $querybor->whereHas('variants', function ($querybor) use ($catalog_no) {
                $querybor->where('catalog_no', $catalog_no);
            });     
        }

        if ($brand!='null') {
            $querybor->whereHas('variants', function ($querybor) use ($brand) {
                $querybor->where('brand', $brand);
            });    
        }

        $bor_list=$querybor->orderBy('id','DESC')->orderBy('created_at','DESC')->get();
        $borqty=0;
        foreach($bor_list AS $bor){
            $borqty-=$bor->quantity;
            $pr_balance=VariantsBalance::where('item_id',$bor->item_id)->where('variant_id',$bor->variant_id)->value('balance');
            $item_id=$bor->item_id;
            $variant_id=$bor->variant_id;
            $pr_no=$bor->borrowed_from;
            $po_no=ReceiveHead::with('receive_items','receive_details')->whereHas('receive_details', function ($rec) use ($pr_no) {
                $rec->where('pr_no', $pr_no);
            })->whereHas('receive_items', function ($reci) use ($item_id,$variant_id) {
                $reci->where('item_id', $item_id)->where('variant_id', $variant_id);
            })->value('po_no');
            $unit_cost=ReceiveItems::with('receive_details')->where('item_id', $bor->item_id)->where('variant_id', $bor->variant_id)->whereHas('receive_details', function ($reci) use ($pr_no) {
                $reci->where('pr_no', $pr_no);
            })->value('unit_cost');
            $currency=ReceiveItems::with('receive_details')->where('item_id', $bor->item_id)->where('variant_id', $bor->variant_id)->whereHas('receive_details', function ($reci) use ($pr_no) {
                $reci->where('pr_no', $pr_no);
            })->value('currency');
            $stockcard[]=[
                'id'=>$bor->id,
                'date'=>$bor->borrow_head->borrow_date,
                'pr_no'=>$bor->borrowed_from,
                'po_no'=>$po_no,
                'supplier'=>$bor->variants->supplier_name,
                'catalog_no'=>$bor->variants->catalog_no,
                'brand'=>$bor->variants->brand,
                'department'=>$bor->department_name,
                'total'=>(float) $bor->quantity * (float) $unit_cost." ".$currency,
                'method_disp'=>'Borrow',
                'quantity_disp'=>"-".(float) $bor->quantity,
                'remarks'=>$bor->remarks,
                'date_created'=>$bor->updated_at,
                'method'=>'Borrow',
                'quantity'=>(float) $bor->quantity,
            ];
        }

        // $a = array($stockcard1, $stockcard2, $stockcard3, $stockcard4, $stockcard5, $stockcard6);
        // $result = array_reduce($a, 'array_merge', array());
        // $arr=array_merge($stockcard1,$stockcard2, $stockcard3, $stockcard4, $stockcard5, $stockcard6);
        // collect($arr)->sortByDesc('date_created');
        $display=array();
        $stock=collect($stockcard)->sort(function($a, $b){
            return strtotime($a['date_created']) - strtotime($b['date_created']);
        })->sortBy('id')->sortBy('date_created');
        // usort($stockcard, function($a, $b) {
        //     return strtotime($a['date_created']) - strtotime($b['date_created']);
        // });
        $quantitysum=0;
        foreach($stock AS $r){
            // collect($stockcard)->sort(function($a, $b){
            //     return strtotime($a['date_created']) - strtotime($b['date_created']);
            // });
            if($r['method']=='Begbal' || $r['method']=='Receive' || $r['method']=='Restock' || $r['method']=='Backorder'){
                $quantitysum += (float) $r['quantity'];
            }else if($r['method']=='Issuance' || $r['method']=='Borrow' || $r['method']=='Restock-Damaged'){
                $quantitysum -= (float) $r['quantity'];
            }
            $display[]=[
                'date'=>$r['date'],
                'pr_no'=>$r['pr_no'],
                'po_no'=>$r['po_no'],
                'supplier'=>$r['supplier'],
                'catalog_no'=>$r['catalog_no'],
                'brand'=>$r['brand'],
                'department'=>$r['department'],
                'total'=>$r['total'],
                'method_disp'=>$r['method_disp'],
                'quantity_disp'=>number_format($r['quantity_disp'],2),
                'remarks'=>$r['remarks'],
                'running_balance'=>number_format((float)$quantitysum,2),
                'date_created'=>$r['date_created'],
                'id'=>$r['id'],
            ];
        }
        // $sort= collect($display)->sort(function($a, $b){
        //     return strtotime($b['date_created']) - strtotime($['date_created']);
        // })->sortByDesc('id');

        return collect($display)->sort(function($a, $b){
            return strtotime($a['date_created']) - strtotime($b['date_created']);
        })->sortByDesc('id')->sortByDesc('date_created');
        
        // return  collect($arr)->sort(function($a, $b){
        //     return strtotime($b['date_created']) - strtotime($a['date_created']);
        //  });
    }
    
    public function map($display): array
    {   
        return [
            $display
        ];
    }

    public function headings(): array
    {
        return [
            'A6'=>'Date',
            'B6'=>'PR Number',
            'C6'=>'PO Number',
            'D6'=>'Supplier',
            'E6'=>'Catalog #',
            'F6'=>'Brand',
            'G6'=>'Department',
            'H6'=>'TUC',
            'I6'=>'Method',
            'J6'=>'Qty',
            'K6'=>'Notes',
            'L6'=>'Running Balance',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) { 
                $event->sheet->getStyle('A9:L9')->applyFromArray([
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
                    $event->sheet->getStyle('L'.$x)->applyFromArray([
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
                $event->sheet->getStyle('C3:L3')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $totalRows = $event->sheet->getHighestRow();
                for($i=9;$i<=$totalRows;$i++){
                    $event->sheet->getStyle('A'.$i.':L'.$i)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ]
                        ]
                    ]);
                    $event->sheet->getStyle('A'.$i)->getAlignment()->setHorizontal('center');
                    $event->sheet->getStyle('L'.$i)->applyFromArray([
                       'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ]
                    ]);
                }
                for($c=10;$c<=$totalRows;$c++){
                    $event->sheet->getDelegate()->getStyle('M'.$c.':N'.$c)->applyFromArray([
                        'font' => [
                            'color' => ['argb' => 'FFFFFF'],
                        ],
                    ]);
                    // $event->sheet->getDelegate()->getStyle('L'.$c)->applyFromArray([
                    //     'fill' => [
                    //         'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    //         'color' => ['rgb' => 'F7FFBC']
                    //     ]
                    // ]);
                    $event->sheet->getParent()->getActiveSheet()->getProtection()->setSheet(true);
                    // lock all cells then unlock the cell
                    $event->sheet->getParent()->getActiveSheet()->getStyle('A'.$c.':L'.$c)->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);
                    $event->sheet->getStyle('B'.$c.':C'.$c)->getAlignment()->setHorizontal('center');
                    $event->sheet->getStyle('H'.$c.':J'.$c)->getAlignment()->setHorizontal('center');
                }
                $event->sheet->getDelegate()->getStyle('L9')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F7FFBC']
                    ]
                ]);

                $event->sheet->getDelegate()->getStyle('A7')->applyFromArray([
                    'font' => [
                        'color' => ['argb' => '2563EB'],
                    ],
                ]);

                $event->sheet->getDelegate()->getStyle('L7:L8')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => '2563EB']
                    ],
                    'font' => [
                        'color' => ['argb' => 'FFFFFF'],
                    ],
                ]);

                $event->sheet->getStyle('B4')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);

                $event->sheet->getStyle('B5')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);

                $event->sheet->getStyle('B6')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);

                $event->sheet->getStyle('E4')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);

                $event->sheet->getStyle('E5')->applyFromArray([
                    'borders' => [
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ],
                ]);
                $event->sheet->getProtection()->setSheet(true);
                $event->sheet->getProtection()->setSort(true);
                $event->sheet->getProtection()->setInsertRows(true);
                $event->sheet->getProtection()->setFormatCells(true);
                $event->sheet->getProtection()->setPassword('Inventory2024!');
                $event->sheet->getDelegate()->getColumnDimension('A')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('B')->setAutoSize(false);
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(30);
                $event->sheet->getStyle('A9:L9')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle('B1')->getFont()->setBold(true);
                $event->sheet->setCellValue('B1', 'CENTRAL NEGROS POWER RELIABILITY, INC.');
                $event->sheet->setCellValue('B2', 'Prk. San Jose, Brgy. Calumangan, Bago City');
                $event->sheet->setCellValue('B3', 'Tel. No. 476-7382');
                $event->sheet->setCellValue('G2', 'STOCKCARD REPORT');
                $event->sheet->getStyle('G2:I2')->getAlignment()->setHorizontal('center');
                $event->sheet->getStyle("G2")->getFont()->setBold(true)->setName('Arial Black');
                $event->sheet->mergeCells('G2:I2');

                $event->sheet->getStyle('A4')->getFont()->setBold(true);
                $event->sheet->getStyle('A4')->getAlignment()->setHorizontal('left');
                $event->sheet->setCellValue('A4', 'Item:');
                $event->sheet->setCellValue('B4', $this->item_name);

                $event->sheet->getStyle('A5')->getFont()->setBold(true);
                $event->sheet->getStyle('A5')->getAlignment()->setHorizontal('left');
                $event->sheet->setCellValue('A5', 'Department:');
                $event->sheet->setCellValue('B5', $this->department_name);

                $event->sheet->getStyle('A6')->getFont()->setBold(true);
                $event->sheet->getStyle('A6')->getAlignment()->setHorizontal('left');
                $event->sheet->setCellValue('A6', 'Brand:');
                $event->sheet->setCellValue('B6', ($this->brand!='null') ? $this->brand : '');

                $event->sheet->getStyle('D4')->getFont()->setBold(true);
                $event->sheet->getStyle('D4')->getAlignment()->setHorizontal('left');
                $event->sheet->setCellValue('D4', 'Supplier:');
                $event->sheet->setCellValue('E4', $this->supplier_name);

                $event->sheet->getStyle('D5')->getFont()->setBold(true);
                $event->sheet->getStyle('D5')->getAlignment()->setHorizontal('left');
                $event->sheet->setCellValue('D5', 'Catalog No.:');
                $event->sheet->setCellValue('E5', ($this->catalog_no!='null') ? $this->catalog_no : '');
                
                $event->sheet->getDelegate()->getStyle('A7')->getFont()->setSize(20);
                $event->sheet->getStyle('A7')->getFont()->setBold(true);
                $event->sheet->getStyle('A7')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('A7')->getAlignment()->setVertical('center');
                $event->sheet->setCellValue('A7', $this->item_name);
                $event->sheet->mergeCells('A7:A8');

                $event->sheet->getDelegate()->getStyle('L7')->getFont()->setSize(14);
                $event->sheet->getStyle('L7')->getFont()->setBold(true);
                $event->sheet->getStyle('L7')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('L7')->getAlignment()->setVertical('center');
                $event->sheet->setCellValue('L7', 'Running Balance: '.$this->running_balance);
                $event->sheet->mergeCells('L7:L8');
            }
        ];
    }

    public function title(): string
    {
        return 'Stockcard Report';
    }
}
