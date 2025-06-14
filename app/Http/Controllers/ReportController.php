<?php

namespace App\Http\Controllers;
use App\Models\PRItems;
use App\Models\Items;
use App\Models\Variants;
use App\Models\VariantsBalance;
use App\Models\ItemStatus;
use App\Models\ReceiveHead;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\IssuanceHead;
use App\Models\IssuanceItems;
use App\Models\RestockHead;
use App\Models\RestockDetails;
use App\Models\BackorderHead;
use App\Models\BackorderDetails;
use App\Models\BackorderItems;
use App\Models\BorrowHead;
use App\Models\BorrowDetails;
use App\Models\PIVBalance;
use App\Models\Supplier;
use App\Models\Department;
use App\Models\RequestHead;
use App\Exports\ReceiveExport;
use App\Exports\RestockExport;
use App\Exports\IssueExport;
use App\Exports\BorrowExport;
use App\Exports\BackorderExport;
use App\Exports\PRoverallExport;
use App\Exports\VariantsExport;
use App\Exports\PRVariantsExport;
use App\Exports\StockcardExport;
use App\Exports\StockcardItemsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function filter_pr_overall(Request $request){

        $item=$request->get('item');
        $pr=$request->get('pr');
   
        if($item!='undefined' && $pr!='undefined'){
            $report = PRItems::where('pr_no','=',$pr)->where('item_id','=',$item)->get();

        } else if($item=='undefined' && $pr!='undefined'){
             $report = PRItems::where('pr_no','=',$pr)->get();

        } else if($item!='undefined' && $pr=='undefined' ){
            $report = PRItems::where('item_id','=',$item)->get();
        }
        $formItems = array();
       
        foreach($report AS $re){
            
            $formItems[]=[
               'pr_no'=>$re->pr_no,
               'item'=>Items::where('id','=',$re->item_id)->value('item_description'),
               'begbal'=>$re->begbal,
               'receive_qty'=>$re->receive_qty,
               'issuance_qty'=>$re->issuance_qty,
               'restock_qty'=>$re->restock_qty,
               'transfer_qty'=>$re->transfer_qty,
               'damage_qty'=>$re->damage_qty,
               'borrow_deduct'=>$re->borrow_deduct,
               'replenish_add'=>$re->replenish_add,
               'borrow_add'=>$re->borrow_add,
               'replenish_deduct'=>$re->replenish_deduct,
               'backorder_qty'=>$re->backorder_qty,
               'balance'=>$re->balance,
              
           ];
        }
       return response()->json($formItems);
      
    }

    public function export_proverall($item, $pr_no){
        return Excel::download(new PRoverallExport($item, $pr_no), 'PR Overall Export.xlsx');
    }

    public function filter_item(Request $request){

        $item=$request->get('item');
        
        $report = VariantsBalance::with(['variants'])->where('item_id','=',$item)->where('variant_id','!=','0')->get();
   
        foreach($report AS $re){
            
            if($re->variants->item_status_id == '0'){
                $status = '';
            } else {
                $status = $re->variants->item_status->status;
            }
            $formItems[]=[
                'item'=>Items::where('id','=',$re->variants->item_id)->value('item_description'),
                'variant'=>$re->variants,
                'item_status'=>$status,
                'begbal'=>$re->whstocks_qty,
                'composite_qty'=>$re->composite_qty,
                'receive_qty'=>$re->receive_qty,
                'issuance_qty'=>$re->issuance_qty,
                'restock_qty'=>$re->restock_qty,
                'transfer_qty'=>$re->transfer_qty,
                'damage_qty'=>$re->damage_qty,
                'borrow_deduct'=>$re->borrow_deduct,
                'replenish_add'=>$re->replenish_add,
                'borrow_add'=>$re->borrow_add,
                'replenish_deduct'=>$re->replenish_deduct,
                'backorder_qty'=>$re->backorder_qty,
                'balance'=>$re->balance,
               
            ];

        }

        $whstocks = VariantsBalance::where('item_id','=',$item)->where('variant_id','=','0')->get();

        foreach($whstocks AS $wh){
            
            $formItems[]=[
                'item'=>Items::where('id','=',$wh->item_id)->value('item_description'),
                'variant'=>'Beginning Balance',
                'begbal'=>$wh->whstocks_qty,
                'composite_qty'=>$wh->composite_qty,
                'receive_qty'=>$wh->receive_qty,
                'issuance_qty'=>$wh->issuance_qty,
                'restock_qty'=>$wh->restock_qty,
                'transfer_qty'=>$wh->transfer_qty,
                'damage_qty'=>$wh->damage_qty,
                'borrow_deduct'=>$wh->borrow_deduct,
                'replenish_add'=>$wh->replenish_add,
                'borrow_add'=>$wh->borrow_add,
                'replenish_deduct'=>$wh->replenish_deduct,
                'backorder_qty'=>$wh->backorder_qty,
                'balance'=>$wh->balance,
               
            ];

        }


        return response()->json($formItems);
      
    }

    public function export_variants($item){
        return Excel::download(new VariantsExport($item), 'Variants Export.xlsx');
    }

    public function filter_pr_variants(Request $request){

        $item=$request->get('item');
        $pr=$request->get('pr');
   
        if($item!='undefined' && $pr!='undefined'){
            $report = PIVBalance::with(['variants'])->where('pr_no','=',$pr)->where('item_id','=',$item)->where('variant_id','!=','0')->get();
            $report_wh = PIVBalance::where('pr_no','=',$pr)->where('item_id','=',$item)->where('variant_id','=','0')->get();
        } else if($item=='undefined' && $pr!='undefined'){
             $report = PIVBalance::with(['variants'])->where('pr_no','=',$pr)->where('variant_id','!=','0')->get();
             $report_wh = PIVBalance::where('pr_no','=',$pr)->where('variant_id','=','0')->get();
        } else if($item!='undefined' && $pr=='undefined' ){
            $report = PIVBalance::with(['variants'])->where('item_id','=',$item)->where('variant_id','!=','0')->get();
            $report_wh = PIVBalance::where('item_id','=',$item)->where('variant_id','=','0')->get();
        }
        
        foreach($report AS $re){
          
            if($re->variants->item_status_id == '0'){
                $status = '';
            } else {
                $status = $re->variants->item_status->status;
            }
            $formItems[]=[
               'pr_no'=>$re->pr_no,
               'item'=>Items::where('id','=',$re->item_id)->value('item_description'),
               'variant'=>$re->variants,
               'item_status'=>$status,
               'balance'=>$re->quantity,
              
           ];
        }

        foreach($report_wh AS $rh){
            
            $formItems[]=[
               'pr_no'=>$rh->pr_no,
               'item'=>Items::where('id','=',$rh->item_id)->value('item_description'),
               'variant'=>'Beginning Balance', 
            //    'item_status'=>$re->variants->item_status->status,
               'item_status'=>'',
               'balance'=>$rh->quantity,
              
           ];
        }
       return response()->json($formItems);
      
    }

    public function export_prvariants($item,$pr_no){
        return Excel::download(new PRVariantsExport($item,$pr_no), 'PR Variants Export.xlsx');
    }

    public function all_receive_transactions(Request $request){

    
       // $query = ReceiveHead::query();
       // $return = '';
       //$query = ReceiveItems::query();
       $close= 1;
    //    $query = ReceiveItems::with(['receive_head'], function ($query) use ($close) {
    //          $query->where('closed', $close);
    //         });

    //$query = ReceiveItems::with(['receive_head'])->with(['receive_details']);
   // $query = ReceiveItems::with(['receive_head'])->with(['receive_details']);
    //$query = ReceiveHead::with(['receive_details','receive_details.receive_items']);
    $query = ReceiveItems::with(['receive_head','receive_details','items']);
    $query->whereHas('receive_head', function ($query) {
        $query->where('saved', '1')->where('closed','1');
    });
        if ($request->get('from_date') && $request->get('to_date')) {
           
            $from_date=$request->get('from_date');
            $to_date=$request->get('to_date');
        
            $query->whereHas('receive_head', function ($query) use ($from_date, $to_date) {
                $query->whereBetween('receive_date', [$from_date, $to_date]);
            });
          // $query->whereBetween('receive_date', [$from_date, $to_date]);
       
        }

        if ($request->get('item')) {
            $item = $request->get('item');
            $query->where('item_id', $item);
        }

        if ($request->get('pr_no')) {
            $pr_no=$request->get('pr_no');
            $query->whereHas('receive_details', function ($query) use ($pr_no) {
                $query->where('pr_no', $pr_no);
            });    

       // $query->where('pr_no', $pr_no);
        }

        if ($request->get('category') ) {
           
            $category=$request->get('category');
        
            $query->whereHas('items', function ($query) use ($category) {
                $query->where('item_category_id', $category);
            });

            //$query->where('item_category_id', $category);
                  
        }

        if ($request->get('subcategory')) {
           
            $subcategory=$request->get('subcategory');
        
            $query->whereHas('items', function ($query) use ($subcategory) {
                $query->where('item_sub_category_id', $subcategory);
            });

           // $query->where('item_sub_category_id', $subcategory);
                  
        }

        if ($request->get('department')) {
            $department=$request->get('department');
            $query->whereHas('receive_details', function ($query) use ($department) {
                $query->where('department_id', $department);
            });    
            // $query->where('department_id', $department);
        }

        if ($request->get('enduse')) {
           
            $enduse=$request->get('enduse');
        
            $query->whereHas('receive_details', function ($query) use ($enduse) {
                $query->where('enduse_id', $enduse);
            });    

            // $query->where('enduse_id', $enduse);
        }

        if ($request->get('purpose')) {
            $purpose=$request->get('purpose');
            $query->whereHas('receive_details', function ($query) use ($purpose) {
                $query->where('purpose_id', $purpose);
            });    
            // $query->where('purpose_id', $purpose);
        }

        $rec_list=$query->get();
        $receivearray=array();
        foreach($rec_list AS $rc){
            $receivearray[]=[
                date('F d,Y',strtotime($rc->receive_head->receive_date)),
                $rc->receive_head->po_no,
                $rc->receive_head->dr_no,
                $rc->receive_head->mrecf_no,
                $rc->receive_head->waybill_no,
                $rc->receive_details->pr_no,
                $rc->items->pn_no,
                $rc->item_description,
                (float) $rc->rec_quantity,
                $rc->uom,
                (float) $rc->unit_cost,
                $rc->currency,
                (float) $rc->rec_quantity * (float) $rc->unit_cost,
                $rc->supplier_name,
                $rc->receive_details->department_name,
                $rc->receive_details->enduse_name,
                $rc->receive_details->purpose_name,
            ];
        }
        
        return response()->json([
            'receivearray'=>$receivearray,
        ],200);
        
        // return $query->get();
    }

    public function export_received($from, $to, $item, $pr, $category, $subcategory, $department, $enduse, $purpose){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new ReceiveExport($from, $to, $item, $pr, $category, $subcategory, $department, $enduse, $purpose), 'Received Export.xlsx');
        // return $department;
    }

    public function all_issued_transactions(Request $request){
        $query=IssuanceItems::with('issuance_head','items','variants');
        if ($request->get('from_date') && $request->get('to_date')) {
            $from_date=$request->get('from_date');
            $to_date=$request->get('to_date');
            $query->whereHas('issuance_head', function ($query) use ($from_date, $to_date) {
                $query->whereBetween('issuance_date', [$from_date, $to_date]);
            });
        }

        if ($request->get('item')) {
            $item = $request->get('item');
            $query->where('item_id', $item);
        }

        if ($request->get('pr_no')) {
            $pr_no=$request->get('pr_no');
            $query->whereHas('issuance_head', function ($query) use ($pr_no) {
                $query->where('pr_no', $pr_no);
            });    
        }

        if ($request->get('category') ) {
            $category=$request->get('category');
            $query->whereHas('items', function ($query) use ($category) {
                $query->where('item_category_id', $category);
            });    
        }

        if ($request->get('subcategory')) {
            $subcategory=$request->get('subcategory');
            $query->whereHas('items', function ($query) use ($subcategory) {
                $query->where('item_sub_category_id', $subcategory);
            });
        }

        if ($request->get('department')) {
            $department=$request->get('department'); 
            $query->whereHas('issuance_head', function ($query) use ($department) {
                $query->where('department_id', $department);
            }); 
        }

        if ($request->get('enduse')) {
            $enduse=$request->get('enduse');
            $query->whereHas('issuance_head', function ($query) use ($enduse) {
                $query->where('enduse_id', $enduse);
            }); 
        }

        if ($request->get('purpose')) {
            $purpose=$request->get('purpose');
            $query->whereHas('issuance_head', function ($query) use ($purpose) {
                $query->where('purpose_id', $purpose);
            }); 
        }

        $iss_list=$query->get();
        $issuearray=array();
        $pr_cost=array();
        $wh_cost=array();
        $pr_wo_cost=0;
        $wh_wo_cost=0;
        foreach($iss_list AS $ia){
            $uom=($ia->variants) ? $ia->variants->uom : '';
            $supplier_name=($ia->variants) ? $ia->variants->supplier_name : '';
            $request_type=RequestHead::where('id',$ia->issuance_head->request_head_id)->value('request_type');
            $total_cost=(float) $ia->issued_qty * (float) $ia->unit_cost;
            $pr_no=$ia->issuance_head->pr_no;
            $item_id=$ia->item_id;
            $variant_id=$ia->variant_id;
            $po_no=ReceiveHead::with('receive_items','receive_details')->whereHas('receive_details', function ($rec) use ($pr_no) {
                $rec->where('pr_no', $pr_no);
            })->whereHas('receive_items', function ($reci) use ($item_id,$variant_id) {
                $reci->where('item_id', $item_id)->where('variant_id', $variant_id);
            })->value('po_no');
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
            $issuearray[]=[
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
                $total_cost,
                $supplier_name,
                $ia->issuance_head->department_name,
                $ia->issuance_head->enduse_name,
                $ia->issuance_head->purpose_name,
            ];
        }
        $prcost_sum=array_sum($pr_cost);
        $whcost_sum=array_sum($wh_cost);
        return response()->json([
            'issuearray'=>$issuearray,
            'pr_cost'=>$prcost_sum,
            'wh_cost'=>$whcost_sum,
            'pr_wo_cost'=>$pr_wo_cost,
            'wh_wo_cost'=>$wh_wo_cost,
        ],200);

        // return $query->get();
    }

    public function export_issued($from, $to, $item, $pr, $category, $subcategory, $department, $enduse, $purpose){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new IssueExport($from, $to, $item, $pr, $category, $subcategory, $department, $enduse, $purpose), 'Issued Export.xlsx');
        // return $department;
    }

    public function all_restock_transactions(Request $request){
        $query=RestockDetails::with('restock_head','items','variants');
        if ($request->get('from_date') && $request->get('to_date')) {
            $from_date=$request->get('from_date');
            $to_date=$request->get('to_date');
            $query->whereHas('restock_head', function ($query) use ($from_date, $to_date) {
                $query->whereBetween('date', [$from_date, $to_date]);
            });
        }

        if ($request->get('item')) {
            $item = $request->get('item');
            $query->where('item_id', $item);
        }

        if ($request->get('source_pr')) {
            $source_pr=$request->get('source_pr');
            $query->whereHas('restock_head', function ($query) use ($source_pr) {
                $query->where('source_pr', $source_pr);
            });    
        }

        if ($request->get('destination_pr')) {
            $destination_pr=$request->get('destination_pr');
            $query->whereHas('restock_head', function ($query) use ($destination_pr) {
                $query->where('destination', $destination_pr);
            });    
        }

        if ($request->get('category') ) {
            $category=$request->get('category');
            $query->whereHas('items', function ($query) use ($category) {
                $query->where('item_category_id', $category);
            });    
        }

        if ($request->get('subcategory')) {
            $subcategory=$request->get('subcategory');
            $query->whereHas('items', function ($query) use ($subcategory) {
                $query->where('item_sub_category_id', $subcategory);
            });
        }

        if ($request->get('department')) {
            $department=$request->get('department'); 
            $query->whereHas('restock_head', function ($query) use ($department) {
                $query->where('department_id', $department);
            }); 
        }

        if ($request->get('enduse')) {
            $enduse=$request->get('enduse');
            $query->whereHas('restock_head', function ($query) use ($enduse) {
                $query->where('enduse_id', $enduse);
            }); 
        }

        if ($request->get('purpose')) {
            $purpose=$request->get('purpose');
            $query->whereHas('restock_head', function ($query) use ($purpose) {
                $query->where('purpose_id', $purpose);
            }); 
        }

        $res_list=$query->get();
        $restockarray=array();
        foreach($res_list AS $rl){
            $uom=($rl->variants) ? $rl->variants->uom : '';
            $unit_cost=($rl->variants) ? $rl->variants->unit_cost : 0;
            $currency=($rl->variants) ? $rl->variants->currency : '';
            $supplier_name=($rl->variants) ? $rl->variants->supplier_name : '';
            $restockarray[]=[
                date('F d,Y',strtotime($rl->restock_head->date)),
                $rl->restock_head->source_pr,
                $rl->restock_head->destination,
                $rl->restock_head->mrs_no,
                $rl->items->pn_no,
                $rl->item_description,
                (float) $rl->quantity,
                $uom,
                (float) $unit_cost,
                $currency,
                (float) $rl->quantity * (float) $unit_cost,
                $supplier_name,
                $rl->restock_head->department,
                $rl->restock_head->enduse,
                $rl->restock_head->purpose,
                $rl->reason,
            ];
        }
        
        return response()->json([
            'restockarray'=>$restockarray,
        ],200);
        // return $query->get();
    }

    public function export_restocked($from, $to, $item, $pr, $destination, $category, $subcategory, $department, $enduse, $purpose){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new RestockExport($from, $to, $item, $pr, $destination, $category, $subcategory, $department, $enduse, $purpose), 'Restock Export.xlsx');
        // return $department;
    }

    public function all_backorder_transactions(Request $request){
        $query = ReceiveItems::with(['receive_head','receive_details','items']);
        $query->whereHas('receive_head', function ($query) {
            $query->where('saved', '1')->where('closed','1');
        });
        if ($request->get('from_date') && $request->get('to_date')) {
            $from_date=$request->get('from_date');
            $to_date=$request->get('to_date');
            $query->whereHas('receive_head', function ($query) use ($from_date, $to_date) {
                $query->whereBetween('receive_date', [$from_date, $to_date]);
            });
        }

        if ($request->get('item')) {
            $item = $request->get('item');
            $query->where('item_id', $item);
        }

        if ($request->get('pr_no')) {
            $pr_no=$request->get('pr_no');
            $query->whereHas('receive_details', function ($query) use ($pr_no) {
                $query->where('pr_no', $pr_no);
            });
        }

        if ($request->get('category') ) {
            $category=$request->get('category');
            $query->whereHas('items', function ($query) use ($category) {
                $query->where('item_category_id', $category);
            });    
        }

        if ($request->get('subcategory')) {
            $subcategory=$request->get('subcategory');
            $query->whereHas('items', function ($query) use ($subcategory) {
                $query->where('item_sub_category_id', $subcategory);
            });      
        }

        if ($request->get('department')) {
            $department=$request->get('department');
            $query->whereHas('receive_details', function ($query) use ($department) {
                $query->where('department_id', $department);
            });
        }

        if ($request->get('enduse')) {
            $enduse=$request->get('enduse');
            $query->whereHas('receive_details', function ($query) use ($enduse) {
                $query->where('enduse_id', $enduse);
            });
        }

        if ($request->get('purpose')) {
            $purpose=$request->get('purpose');
            $query->whereHas('receive_details', function ($query) use ($purpose) {
                $query->where('purpose_id', $purpose);
            });
        }

        $receive_all=$query->get();
        $backorder_data=[];
        foreach($receive_all AS $ra){
            $total_bo_qty = BackorderItems::where('receive_items_id','=',$ra->id)->where('item_id', '=', $ra->item_id)->where('variant_id', '=', $ra->variant_id)->sum('bo_quantity');
            $overall_bo = (float)$ra->exp_quantity - ((float)$ra->rec_quantity + (float)$total_bo_qty);
            if($overall_bo != 0){
                $backorder_data[] = [
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
                    // 'receive_date'=>$ra->receive_head->receive_date,
                    // 'po_no'=>$ra->receive_head->po_no,
                    // 'dr_no'=>$ra->receive_head->dr_no,
                    // 'mrecf_no'=>$ra->receive_head->mrecf_no,
                    // 'pr_no'=>$ra->receive_details->pr_no,
                    // 'pn_no'=>$ra->items->pn_no,
                    // 'item_description'=>$ra->item_description,
                    // 'exp_quantity'=>$ra->exp_quantity,
                    // 'rec_quantity'=>$ra->rec_quantity,
                    // 'bo_qty'=>$overall_bo,
                    // 'uom'=>$ra->uom,
                    // 'unit_cost'=>$ra->unit_cost,
                    // 'supplier_name'=>$ra->supplier_name,
                    // 'department_name'=>$ra->receive_details->department_name,
                    // 'enduse_name'=>$ra->receive_details->enduse_name,
                    // 'purpose_name'=>$ra->receive_details->purpose_name,
                ];
            } 

        }
        return response()->json([
            'backorder_data'=>$backorder_data,
        ],200);    
    }

    public function export_backorder($from, $to, $item, $pr, $category, $subcategory, $department, $enduse, $purpose){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new BackorderExport($from, $to, $item, $pr, $category, $subcategory, $department, $enduse, $purpose), 'Backorder Export.xlsx');
        // return $department;
    }

    public function all_borrowed_transactions(Request $request){
        $query=BorrowDetails::with('borrow_head','items','variants');
        if ($request->get('from_date') && $request->get('to_date')) {
            $from_date=$request->get('from_date');
            $to_date=$request->get('to_date');
            $query->whereHas('borrow_head', function ($query) use ($from_date, $to_date) {
                $query->whereBetween('borrow_date', [$from_date, $to_date]);
            });
        }

        if ($request->get('item')) {
            $item = $request->get('item');
            $query->where('item_id', $item);
        }

        if ($request->get('borrowed_by')) {
            $borrowed_by=$request->get('borrowed_by');
            $query->where('borrowed_by', $borrowed_by);  
        }

        if ($request->get('borrowed_from')) {
            $borrowed_from=$request->get('borrowed_from');
            $query->where('borrowed_from', $borrowed_from);  
        }

        if ($request->get('category') ) {
            $category=$request->get('category');
            $query->whereHas('items', function ($query) use ($category) {
                $query->where('item_category_id', $category);
            });    
        }

        if ($request->get('subcategory')) {
            $subcategory=$request->get('subcategory');
            $query->whereHas('items', function ($query) use ($subcategory) {
                $query->where('item_sub_category_id', $subcategory);
            });
        }

        if ($request->get('department')) {
            $department=$request->get('department'); 
            $query->where('department_id', $department);
        }

        if ($request->get('enduse')) {
            $enduse=$request->get('enduse');
            $query->where('enduse_id', $enduse);
        }

        if ($request->get('purpose')) {
            $purpose=$request->get('purpose');
            $query->where('purpose_id', $purpose);
        }
        $all_data=$query->get();
        $borrow_data=array();
        foreach($all_data AS $ad){
            // $borrowed_from=$ad->borrowed_from;
            // $uom=ReceiveItems::with('receive_details')->where('item_id',$ad->item_id)->where('variant_id',$ad->variant_id)->whereHas('receive_details', function ($query) use ($borrowed_from) {
            //     $query->where('pr_no', $borrowed_from);
            // })->value('uom');
            // $unit_cost=ReceiveItems::with('receive_details')->where('item_id',$ad->item_id)->where('variant_id',$ad->variant_id)->whereHas('receive_details', function ($query) use ($borrowed_from) {
            //     $query->where('pr_no', $borrowed_from);
            // })->value('unit_cost');
            $borrow_data[] = [
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
            ];
            // $borrow_data[] = [
            //     'borrow_date'=>$ad->borrow_head->borrow_date,
            //     'mbr_no'=>$ad->borrow_head->mbr_no,
            //     'borrowed_from'=>$ad->borrowed_from,
            //     'borrowed_by'=>$ad->borrowed_by,
            //     'pn_no'=>$ad->items->pn_no,
            //     'item_description'=>$ad->item_description,
            //     'quantity'=>$ad->quantity,
            //     'uom'=>$uom,
            //     'unit_cost'=>$unit_cost,
            //     'supplier_name'=>$ad->items->supplier_name,
            //     'department_name'=>$ad->department_name,
            //     'enduse_name'=>$ad->enduse_name,
            //     'purpose_name'=>$ad->purpose_name,
            // ];
        }
        //return $query->get();
        return response()->json([
            'borrow_data'=>$borrow_data,
        ],200);   
    }

    public function export_borrowed($from, $to, $item, $borrowed_from, $borrowed_by, $category, $subcategory, $department, $enduse, $purpose){
        return Excel::download(new BorrowExport($from, $to, $item, $borrowed_from, $borrowed_by, $category, $subcategory, $department, $enduse, $purpose), 'Borrowed Export.xlsx');
    }

    public function all_stockcard_transactions(Request $request){
        $stockcard=array();
        $balance=array();
        $querywh=VariantsBalance::with('variants','items')->whereHas('items', function ($querywh) {
            $querywh->where('draft','0');
        });
        if ($request->get('item')) {
            $item = $request->get('item');
            $querywh->where('item_id', $item);
        }

        if ($request->get('supplier')) {
            $supplier=$request->get('supplier');
            $querywh->whereHas('variants', function ($querywh) use ($supplier) {
                $querywh->where('supplier_id', $supplier);
            });   
        }

        if ($request->get('catalog')) {
            $catalog=$request->get('catalog');
            $querywh->whereHas('variants', function ($querywh) use ($catalog) {
                $querywh->where('catalog_no', $catalog);
            });      
        }

        if ($request->get('brand')) {
            $brand=$request->get('brand');
            $querywh->whereHas('variants', function ($querywh) use ($brand) {
                $querywh->where('brand', $brand);
            });  
        }

        $beg_list=$querywh->get();
        $rec_qty=0;
        foreach($beg_list AS $beg){
            $rec_qty+=$beg->rec_quantity;
            $rec_status=ItemStatus::where('id',$beg->item_status_id)->value('modes');
            if((float) $beg->whstocks_qty!=0){
                $stockcard[]=[
                    ($beg->variants) ?  date('Y-m-d',strtotime($beg->variants->updated_at)) : date('Y-m-d',strtotime($beg->items->created_at)),
                    'WH STOCKS',
                    '',
                    ($beg->variants) ? $beg->variants->supplier_name : '',
                    ($beg->variants) ? $beg->variants->catalog_no : '',
                    ($beg->variants) ? $beg->variants->brand : '',
                    '',
                    ($beg->variants) ? (float) $beg->whstocks_qty * (float) $beg->variants->unit_cost." ".$beg->variants->currency : 0,
                    'Begbal',
                    (float) $beg->whstocks_qty,
                    '',
                    '',
                    'date_created'=>$beg->items->created_at,
                    'method'=>'Begbal',
                    'quantity'=> (float) $beg->whstocks_qty,
                ];
                $balance[]=[
                    'date_created'=>$beg->items->created_at,
                    'method'=>'Begbal',
                    'quantity'=>$beg->whstocks_qty
                ];
            }
        }

        $queryrec=ReceiveItems::with('receive_head','receive_details')->whereHas('receive_head', function ($queryrec) {
            $queryrec->where('saved','1')->where('draft','0');
        });
        if ($request->get('item')) {
            $item = $request->get('item');
            $queryrec->where('item_id', $item);
        }

        if ($request->get('supplier')) {
            $supplier=$request->get('supplier');
            $queryrec->where('supplier_id', $supplier);    
        }

        if ($request->get('department') ) {
            $department=$request->get('department');
            $queryrec->whereHas('receive_details', function ($queryrec) use ($department) {
                $queryrec->where('department_id', $department);
            });    
        }

        if ($request->get('catalog')) {
            $catalog=$request->get('catalog');
            $queryrec->where('catalog_no', $catalog);    
        }

        if ($request->get('brand')) {
            $brand=$request->get('brand');
            $queryrec->where('brand', $brand);    
        }

        $rec_list=$queryrec->get();
        $rec_qty=0;
        foreach($rec_list AS $ia){
            $rec_qty+=$ia->rec_quantity;
            $rec_status=ItemStatus::where('id',$ia->item_status_id)->value('modes');
            $stockcard[]=[
                $ia->receive_head->receive_date,
                $ia->receive_details->pr_no,
                $ia->receive_head->po_no,
                $ia->supplier_name,
                $ia->catalog_no,
                $ia->brand,
                $ia->receive_details->department_name,
                (float) $ia->rec_quantity * (float) $ia->unit_cost." ".$ia->currency,
                ($rec_status=='add') ? 'Receive' : 'Receive (Damage)',
                (float) $ia->rec_quantity,
                $ia->receive_head->remarks,
                '',
                'date_created'=>$ia->receive_head->updated_at,
                'method'=>($rec_status=='add') ? 'Receive' : 'Receive (Damage)',
                'quantity'=> ($rec_status=='add') ? $ia->rec_quantity : 0,
            ];
            $balance[]=[
                'date_created'=>$ia->receive_head->updated_at,
                'method'=>($rec_status=='add') ? 'Receive' : 'Receive (Damage)',
                'quantity'=>$ia->rec_quantity
            ];
        }

        $queryback=BackorderItems::with('backorder_head','backorder_details')->whereHas('backorder_head', function ($queryback) {
            $queryback->where('saved','1')->where('draft','0')->where('closed','0');
        });
        if ($request->get('item')) {
            $item = $request->get('item');
            $queryback->where('item_id', $item);
        }

        if ($request->get('supplier')) {
            $supplier=$request->get('supplier');
            $queryback->where('supplier_id', $supplier);   
        }

        if ($request->get('department') ) {
            $department=$request->get('department');
            $queryback->whereHas('backorder_details', function ($queryback) use ($department) {
                $queryback->where('department_id', $department);
            });    
        }

        if ($request->get('catalog')) {
            $catalog=$request->get('catalog');
            $queryback->where('catalog_no', $catalog);      
        }

        if ($request->get('brand')) {
            $brand=$request->get('brand');
            $queryback->where('brand', $brand);     
        }

        $back_list=$queryback->get();
        $boqty=0;
        foreach($back_list AS $bc){
            $boqty+=$bc->bo_quantity + $rec_qty;
            $pr_balance=VariantsBalance::where('item_id',$bc->item_id)->where('variant_id',$bc->variant_id)->value('balance');
            $stockcard[]=[
                $bc->backorder_head->backorder_date,
                $bc->backorder_details->pr_no,
                $bc->backorder_head->po_no,
                $bc->supplier_name,
                $bc->catalog_no,
                $bc->brand,
                $bc->backorder_details->department_name,
                (float) $bc->bo_quantity * (float) $bc->unit_cost." ".$bc->currency,
                'Backorder',
                (float) $bc->bo_quantity,
                $bc->remarks,
                '',
                'date_created'=>$bc->backorder_head->updated_at,
                'method'=>'Backorder',
                'quantity'=>$bc->bo_quantity,
            ];
            $balance[]=[
                'date_created'=>$bc->backorder_head->updated_at,
                'method'=>'Backorder',
                'quantity'=>$bc->bo_quantity
            ];
        }

        $queryres=RestockDetails::with('variants','restock_head')->whereHas('restock_head', function ($queryres) {
            $queryres->where('saved','1');
        });
        if ($request->get('item')) {
            $item = $request->get('item');
            $queryres->where('item_id', $item);
        }

        if ($request->get('supplier')) {
            $supplier=$request->get('supplier');
            $queryres->whereHas('variants', function ($queryres) use ($supplier) {
                $queryres->where('supplier_id', $supplier);
            });    
        }

        if ($request->get('department') ) {
            $department=$request->get('department');
            $queryres->whereHas('restock_head', function ($queryres) use ($department) {
                $queryres->where('department_id', $department);
            });    
        }

        if ($request->get('catalog')) {
            $catalog=$request->get('catalog');
            $queryres->whereHas('variants', function ($queryres) use ($catalog) {
                $queryres->where('catalog_no', $catalog);
            });     
        }

        if ($request->get('brand')) {
            $brand=$request->get('brand');
            $queryres->whereHas('variants', function ($queryres) use ($brand) {
                $queryres->where('brand', $brand);
            });    
        }

        $res_list=$queryres->get();
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
            // $unit_cost=ReceiveItems::with('receive_details')->where('item_id', $rs->item_id)->where('variant_id', $rs->variant_id)->whereHas('receive_details', function ($reci) use ($pr_no) {
            //     $reci->where('pr_no', $pr_no);
            // })->value('unit_cost');
            $unit_cost=Variants::where('item_id', $rs->item_id)->where('id', $rs->variant_id)->value('unit_cost');
            $currency=Variants::where('item_id', $rs->item_id)->where('id', $rs->variant_id)->value('currency');
            $res_status=ItemStatus::where('id',$rs->item_status_id)->value('modes');
            $stockcard[]=[
                $rs->restock_head->date,
                $rs->restock_head->source_pr,
                $po_no,
                $rs->variants->supplier_name,
                $rs->variants->catalog_no,
                $rs->variants->brand,
                $rs->restock_head->department,
                (float) $rs->quantity * (float) $unit_cost." ".$currency,
                ($res_status=='add') ? (($rs->identifier=='Not Issued') ? 'Restock (Not Issued)' : 'Restock (Issued)') : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? 'Restock (Not Issued) Damage' : 'Restock (Issued) Damage'),
                ($rs->identifier=='Not Issued' && $res_status=='deduct') ? '-'.(float) $rs->quantity : (float) $rs->quantity,
                $rs->remarks,
                '',
                'date_created'=>$rs->restock_head->updated_at,
                'method'=>($res_status=='add') ? 'Restock' : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? 'Restock-Damaged' : 'Restock-Damage'),
                'quantity'=>($res_status=='add') ? ($rs->identifier=='Not Issued') ? 0 : $rs->quantity : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? $rs->quantity : 0)

                // 'quantity'=>($res_status=='add') ? $rs->quantity : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? $rs->quantity : 0)
                // 'method'=>($res_status=='add') ? 'Restock' : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? 'Restock-Damaged' : 'Restock-Damage'),
                // 'quantity'=> ($rs->identifier=='Not Issued') ? $rs->quantity : 0,
            ];
            $balance[]=[
                'date_created'=>$rs->restock_head->updated_at,
                'method'=>'Restock',
                'quantity'=>($rs->identifier=='Not Issued') ? $rs->quantity : 0
            ];
        }

        $queryiss=IssuanceItems::with('variants','issuance_head')->whereHas('issuance_head', function ($queryiss) {
            $queryiss->where('saved','0');
        });
        if ($request->get('item')) {
            $item = $request->get('item');
            $queryiss->where('item_id', $item);
        }

        if ($request->get('supplier')) {
            $supplier=$request->get('supplier');
            $queryiss->whereHas('variants', function ($queryiss) use ($supplier) {
                $queryiss->where('supplier_id', $supplier);
            });    
        }

        if ($request->get('department') ) {
            $department=$request->get('department');
            $queryiss->whereHas('issuance_head', function ($queryiss) use ($department) {
                $queryiss->where('department_id', $department);
            });    
        }

        if ($request->get('catalog')) {
            $catalog=$request->get('catalog');
            $queryiss->whereHas('variants', function ($queryiss) use ($catalog) {
                $queryiss->where('catalog_no', $catalog);
            });     
        }

        if ($request->get('brand')) {
            $brand=$request->get('brand');
            $queryiss->whereHas('variants', function ($queryiss) use ($brand) {
                $queryiss->where('brand', $brand);
            });    
        }

        $iss_list=$queryiss->get();
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
                $is->issuance_head->issuance_date,
                $is->issuance_head->pr_no,
                $po_no,
                $is->variants->supplier_name,
                $is->variants->catalog_no,
                $is->variants->brand,
                $is->issuance_head->department_name,
                (float) $is->issued_qty * (float) $is->unit_cost." ".$is->currency,
                'Issuance',
                "-".(float) $is->issued_qty,
                $is->issuance_head->remarks,
                '',
                'date_created'=>$is->issuance_head->issuance_date." ".$is->issuance_head->issuance_time,
                'method'=>'Issuance',
                'quantity'=>$is->issued_qty,
            ];
            $balance[]=[
                'date_created'=>$is->issuance_head->issuance_date." ".$is->issuance_head->issuance_time,
                'method'=>'Issuance',
                'quantity'=>$is->issued_qty
            ];
        }

        $querybor=BorrowDetails::with('variants','borrow_head');
        if ($request->get('item')) {
            $item = $request->get('item');
            $querybor->where('item_id', $item);
        }

        if ($request->get('supplier')) {
            $supplier=$request->get('supplier');
            $querybor->whereHas('variants', function ($querybor) use ($supplier) {
                $querybor->where('supplier_id', $supplier);
            });    
        }

        if ($request->get('department') ) {
            $department=$request->get('department');
            $querybor->where('department_id', $department); 
        }

        if ($request->get('catalog')) {
            $catalog=$request->get('catalog');
            $querybor->whereHas('variants', function ($querybor) use ($catalog) {
                $querybor->where('catalog_no', $catalog);
            });     
        }

        if ($request->get('brand')) {
            $brand=$request->get('brand');
            $querybor->whereHas('variants', function ($querybor) use ($brand) {
                $querybor->where('brand', $brand);
            });    
        }

        $bor_list=$querybor->get();
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
                $bor->borrow_head->borrow_date,
                $bor->borrowed_from,
                $po_no,
                $bor->variants->supplier_name,
                $bor->variants->catalog_no,
                $bor->variants->brand,
                $bor->department_name,
                (float) $bor->quantity * (float) $unit_cost." ".$currency,
                'Borrow',
                "-".(float) $bor->quantity,
                $bor->remarks,
                '',
                'date_created'=>$bor->updated_at,
                'method'=>'Borrow',
                'quantity'=>$bor->quantity,
            ];
            $balance[]=[
                'date_created'=>$bor->updated_at,
                'method'=>'Borrow',
                'quantity'=>$bor->quantity
            ];
        }
        
        return response()->json([
            'stockcard'=>$stockcard,
            'balance'=>$balance,
        ],200);

        // return $query->get();
    }

    public function export_stockcard($item, $supplier, $department, $catalog_no, $brand, $running_balance){
        return Excel::download(new StockcardExport($item, $supplier, $department, $catalog_no, $brand, $running_balance), 'Stockcard Export.xlsx');
    }

    public function stockcard_item($variant_id,$item_id,$supplier_id,$catalog_no,$brand,$department_id){
        $stockcard=array();
        $balance=array();
        $querywh=VariantsBalance::with('variants','items')->whereHas('items', function ($querywh) {
            $querywh->where('draft','0');
        });
        if ($variant_id!=0) {
            $querywh->where('variant_id', $variant_id);
        }

        if ($item_id!=0) {
            $querywh->where('item_id', $item_id);
        }

        if ($supplier_id!=0) {
            $querywh->whereHas('variants', function ($querywh) use ($supplier_id) {
                $querywh->where('supplier_id', $supplier_id);
            });   
        }

        if ($catalog_no!='null') {
            $querywh->whereHas('variants', function ($querywh) use ($catalog_no) {
                $querywh->where('catalog_no', $catalog_no);
            });      
        }

        if ($brand!='null') {
            // $brand=$request->get('brand');
            $querywh->whereHas('variants', function ($querywh) use ($brand) {
                $querywh->where('brand', $brand);
            });  
        }

        $beg_list=$querywh->get();
        $rec_qty=0;
        foreach($beg_list AS $beg){
            $rec_qty+=$beg->rec_quantity;
            $rec_status=ItemStatus::where('id',$beg->item_status_id)->value('modes');
            if((float) $beg->whstocks_qty!=0){
                $stockcard[]=[
                    ($beg->variants) ?  date('Y-m-d',strtotime($beg->variants->updated_at)) : date('Y-m-d',strtotime($beg->items->created_at)),
                    'WH STOCKS',
                    '',
                    ($beg->variants) ? $beg->variants->supplier_name : '',
                    ($beg->variants) ? $beg->variants->catalog_no : '',
                    ($beg->variants) ? $beg->variants->brand : '',
                    '',
                    ($beg->variants) ? (float) $beg->whstocks_qty * (float) $beg->variants->unit_cost." ".$beg->variants->currency : 0,
                    'Begbal',
                    (float) $beg->whstocks_qty,
                    '',
                    '',
                    'date_created'=>$beg->items->created_at,
                    'method'=>'Begbal',
                    'quantity'=> (float) $beg->whstocks_qty,
                ];
                $balance[]=[
                    'date_created'=>$beg->items->created_at,
                    'method'=>'Begbal',
                    'quantity'=>$beg->whstocks_qty
                ];
            }
        }

        $queryrec=ReceiveItems::with('receive_head','receive_details')->whereHas('receive_head', function ($queryrec) {
            $queryrec->where('saved','1')->where('draft','0');
        });
        if ($variant_id!=0) {
            $queryrec->where('variant_id', $variant_id);
        }

        if ($item_id!=0) {
            $queryrec->where('item_id', $item_id);
        }

        if ($supplier_id!=0) {
            $queryrec->where('supplier_id', $supplier_id);    
        }

        if ($department_id!=0) {
            $queryrec->whereHas('receive_details', function ($queryrec) use ($department_id) {
                $queryrec->where('department_id', $department_id);
            });    
        }

        if ($catalog_no!='null') {
            $queryrec->where('catalog_no', $catalog_no);    
        }

        if ($brand!='null') {
            // $brand=$request->get('brand');
            $queryrec->where('brand', $brand);    
        }

        $rec_list=$queryrec->get();
        $rec_qty=0;
        foreach($rec_list AS $ia){
            $rec_qty+=$ia->rec_quantity;
            $rec_status=ItemStatus::where('id',$ia->item_status_id)->value('modes');
            $stockcard[]=[
                $ia->receive_head->receive_date,
                $ia->receive_details->pr_no,
                $ia->receive_head->po_no,
                $ia->supplier_name,
                $ia->catalog_no,
                $ia->brand,
                $ia->receive_details->department_name,
                (float) $ia->rec_quantity * (float) $ia->unit_cost." ".$ia->currency,
                ($rec_status=='add') ? 'Receive' : 'Receive-Damage',
                (float) $ia->rec_quantity,
                $ia->receive_head->remarks,
                '',
                'date_created'=>$ia->receive_head->updated_at,
                'method'=>($rec_status=='add') ? 'Receive' : 'Receive-Damage',
                'quantity'=> ($rec_status=='add') ? $ia->rec_quantity : 0,
            ];
            $balance[]=[
                'date_created'=>$ia->receive_head->updated_at,
                'method'=>($rec_status=='add') ? 'Receive' : 'Receive-Damage',
                'quantity'=>$ia->rec_quantity
            ];
        }

        $queryback=BackorderItems::with('backorder_head','backorder_details')->whereHas('backorder_head', function ($queryback) {
            $queryback->where('saved','1')->where('draft','0')->where('closed','0');
        });
        if ($variant_id!=0) {
            $queryback->where('variant_id', $variant_id);
        }

        if ($item_id!=0) {
            $queryback->where('item_id', $item_id);
        }

        if ($supplier_id!=0) {
            $queryback->where('supplier_id', $supplier_id);   
        }

        if ($department_id!=0) {
            $queryback->whereHas('backorder_details', function ($queryback) use ($department_id) {
                $queryback->where('department_id', $department_id);
            });    
        }

        if ($catalog_no!='null') {
            $queryback->where('catalog_no', $catalog_no);      
        }

        if ($brand!='null') {
            // $brand=$request->get('brand');
            $queryback->where('brand', $brand);     
        }

        $back_list=$queryback->get();
        $boqty=0;
        foreach($back_list AS $bc){
            $boqty+=$bc->bo_quantity + $rec_qty;
            $pr_balance=VariantsBalance::where('item_id',$bc->item_id)->where('variant_id',$bc->variant_id)->value('balance');
            $stockcard[]=[
                $bc->backorder_head->backorder_date,
                $bc->backorder_details->pr_no,
                $bc->backorder_head->po_no,
                $bc->supplier_name,
                $bc->catalog_no,
                $bc->brand,
                $bc->backorder_details->department_name,
                (float) $bc->bo_quantity * (float) $bc->unit_cost." ".$bc->currency,
                'Backorder',
                (float) $bc->bo_quantity,
                $bc->remarks,
                '',
                'date_created'=>$bc->backorder_head->updated_at,
                'method'=>'Backorder',
                'quantity'=>$bc->bo_quantity,
            ];
            $balance[]=[
                'date_created'=>$bc->backorder_head->updated_at,
                'method'=>'Backorder',
                'quantity'=>$bc->bo_quantity
            ];
        }

        $queryres=RestockDetails::with('variants','restock_head')->whereHas('restock_head', function ($queryres) {
            $queryres->where('saved','1');
        });
        if ($variant_id!=0) {
            $queryres->where('variant_id', $variant_id);
        }

        if ($item_id!=0) {
            $queryres->where('item_id', $item_id);
        }

        if ($supplier_id!=0) {
            $queryres->whereHas('variants', function ($queryres) use ($supplier_id) {
                $queryres->where('supplier_id', $supplier_id);
            });    
        }

        if ($department_id!=0) {
            $queryres->whereHas('restock_head', function ($queryres) use ($department_id) {
                $queryres->where('department_id', $department_id);
            });    
        }

        if ($catalog_no!='null') {
            $queryres->whereHas('variants', function ($queryres) use ($catalog_no) {
                $queryres->where('catalog_no', $catalog_no);
            });     
        }

        if ($brand!='null') {
            // $brand=$request->get('brand');
            $queryres->whereHas('variants', function ($queryres) use ($brand) {
                $queryres->where('brand', $brand);
            });    
        }

        $res_list=$queryres->get();
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
            // $unit_cost=ReceiveItems::with('receive_details')->where('item_id', $rs->item_id)->where('variant_id', $rs->variant_id)->whereHas('receive_details', function ($reci) use ($pr_no) {
            //     $reci->where('pr_no', $pr_no);
            // })->value('unit_cost');
            $unit_cost=Variants::where('item_id', $rs->item_id)->where('id', $rs->variant_id)->value('unit_cost');
            $currency=Variants::where('item_id', $rs->item_id)->where('id', $rs->variant_id)->value('currency');
            $res_status=ItemStatus::where('id',$rs->item_status_id)->value('modes');
            $stockcard[]=[
                $rs->restock_head->date,
                $rs->restock_head->source_pr,
                $po_no,
                $rs->variants->supplier_name,
                $rs->variants->catalog_no,
                $rs->variants->brand,
                $rs->restock_head->department,
                (float) $rs->quantity * (float) $unit_cost." ".$currency,
                ($res_status=='add') ? (($rs->identifier=='Not Issued') ? 'Restock (Not Issued)' : 'Restock (Issued)')  : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? 'Restock (Not Issued) Damage' : 'Restock (Issued) Damage'),
                ($rs->identifier=='Not Issued' && $res_status=='deduct') ? '-'.(float) $rs->quantity : (float) $rs->quantity,
                $rs->remarks,
                '',
                'date_created'=>$rs->restock_head->updated_at,
                'method'=>($res_status=='add') ? 'Restock' : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? 'Restock-Damaged' : 'Restock-Damage'),
                'quantity'=>($res_status=='add') ? ($rs->identifier=='Not Issued') ? 0 : $rs->quantity : (($rs->identifier=='Not Issued' && $res_status=='deduct') ? $rs->quantity : 0)
            ];
            $balance[]=[
                'date_created'=>$rs->restock_head->updated_at,
                'method'=>'Restock',
                'quantity'=>($rs->identifier=='Not Issued') ? $rs->quantity : 0
            ];
        }

        $queryiss=IssuanceItems::with('variants','issuance_head')->whereHas('issuance_head', function ($queryiss) {
            $queryiss->where('saved','0');
        });
        if ($variant_id!=0) {
            $queryiss->where('variant_id', $variant_id);
        }

        if ($item_id!=0) {
            $queryiss->where('item_id', $item_id);
        }

        if ($supplier_id!=0) {
            $queryiss->whereHas('variants', function ($queryiss) use ($supplier_id) {
                $queryiss->where('supplier_id', $supplier_id);
            });    
        }

        if ($department_id!=0) {
            $queryiss->whereHas('issuance_head', function ($queryiss) use ($department_id) {
                $queryiss->where('department_id', $department_id);
            });    
        }

        if ($catalog_no!='null') {
            $queryiss->whereHas('variants', function ($queryiss) use ($catalog_no) {
                $queryiss->where('catalog_no', $catalog_no);
            });     
        }

        if ($brand!='null') {
            // $brand=$request->get('brand');
            $queryiss->whereHas('variants', function ($queryiss) use ($brand) {
                $queryiss->where('brand', $brand);
            });    
        }

        $iss_list=$queryiss->get();
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
                $is->issuance_head->issuance_date,
                $is->issuance_head->pr_no,
                $po_no,
                $is->variants->supplier_name,
                $is->variants->catalog_no,
                $is->variants->brand,
                $is->issuance_head->department_name,
                (float) $is->issued_qty * (float) $is->unit_cost." ".$is->currency,
                'Issuance',
                "-".(float) $is->issued_qty,
                $is->issuance_head->remarks,
                '',
                'date_created'=>$is->issuance_head->issuance_date." ".$is->issuance_head->issuance_time,
                'method'=>'Issuance',
                'quantity'=>$is->issued_qty,
            ];
            $balance[]=[
                'date_created'=>$is->issuance_head->issuance_date." ".$is->issuance_head->issuance_time,
                'method'=>'Issuance',
                'quantity'=>$is->issued_qty
            ];
        }

        $querybor=BorrowDetails::with('variants','borrow_head');
        if ($variant_id!=0) {
            $querybor->where('variant_id', $variant_id);
        }

        if ($item_id!=0) {
            $querybor->where('item_id', $item_id);
        }

        if ($supplier_id!=0) {
            $querybor->whereHas('variants', function ($querybor) use ($supplier_id) {
                $querybor->where('supplier_id', $supplier_id);
            });    
        }

        if ($department_id!=0) {
            $querybor->where('department_id', $department_id); 
        }

        if ($catalog_no!='null') {
            $querybor->whereHas('variants', function ($querybor) use ($catalog_no) {
                $querybor->where('catalog_no', $catalog_no);
            });     
        }

        if ($brand!='null') {
            // $brand=$request->get('brand');
            $querybor->whereHas('variants', function ($querybor) use ($brand) {
                $querybor->where('brand', $brand);
            });    
        }

        $bor_list=$querybor->get();
        $borqty=0;
        foreach($bor_list AS $bor){
            $borqty-=$bor->quantity;
            $pr_balance=VariantsBalance::where('item_id',$bor->item_id)->where('variant_id',$bor->variant_id)->value('balance');
            $item_id=$bor->item_id;
            $variant_id1=$bor->variant_id;
            $pr_no=$bor->borrowed_from;
            $po_no=ReceiveHead::with('receive_items','receive_details')->whereHas('receive_details', function ($rec) use ($pr_no) {
                $rec->where('pr_no', $pr_no);
            })->whereHas('receive_items', function ($reci) use ($item_id,$variant_id1) {
                $reci->where('item_id', $item_id)->where('variant_id', $variant_id1);
            })->value('po_no');
            $unit_cost=ReceiveItems::with('receive_details')->where('item_id', $bor->item_id)->where('variant_id', $bor->variant_id)->whereHas('receive_details', function ($reci) use ($pr_no) {
                $reci->where('pr_no', $pr_no);
            })->value('unit_cost');
            $currency=ReceiveItems::with('receive_details')->where('item_id', $bor->item_id)->where('variant_id', $bor->variant_id)->whereHas('receive_details', function ($reci) use ($pr_no) {
                $reci->where('pr_no', $pr_no);
            })->value('currency');
            $stockcard[]=[
                $bor->borrow_head->borrow_date,
                $bor->borrowed_from,
                $po_no,
                $bor->variants->supplier_name,
                $bor->variants->catalog_no,
                $bor->variants->brand,
                $bor->department_name,
                (float) $bor->quantity * (float) $unit_cost." ".$currency,
                'Borrow',
                "-".(float) $bor->quantity,
                $bor->remarks,
                '',
                'date_created'=>$bor->updated_at,
                'method'=>'Borrow',
                'quantity'=>$bor->quantity,
            ];
            $balance[]=[
                'date_created'=>$bor->updated_at,
                'method'=>'Borrow',
                'quantity'=>$bor->quantity
            ];
        }
        
        return response()->json([
            'stockcard'=>$stockcard,
            'balance'=>$balance,
        ],200);

        // return $query->get();
    }

    public function export_stockcarditems($variant_id, $item, $supplier, $catalog_no, $brand, $department, $running_balance){
        return Excel::download(new StockcardItemsExport($variant_id, $item, $supplier, $catalog_no, $brand, $department, $running_balance), 'Stockcard Export.xlsx');
    }

    // public function filter_stockcard(Request $request){
    //     $item=$request->get('item');
    //     $department=$request->get('department');
    //     $supplier=$request->get('supplier');
    //     $brandid=$request->get('brand');
    //     $catalog=$request->get('catalog');

    //     if($item != 'undefined' && $department != 'undefined' && $supplier != 'undefined' && $brand != 'undefined' && $catalog != 'undefined'){
    //         $variant_id = Variants::where('item_id', '=', $item)->where('supplier_id', '=', $supplier)->where('brand', '=', $brand)->where('catalog_no', '=', $catalog)->get();
    //             $var_id=array();
    //             foreach($variant_id AS $vi){
    //                 $var_id[]=$vi->id;
    //             }
    //             // $variant = Variants::whereIn('id', $var_id)->get();

    //         $get_receive = ReceiveItems::with('receive_head', 'receive_details')->where('item_id','=', $item)->whereIn('variant_id', $var_id);
    //         $get_receive->whereHas('receive_head', function ($get_receive) {
    //             $get_receive->where('closed', '=', '1');
    //             $get_receive->where('draft', '=', '0');
    //             $get_receive->where('cancelled', '=', '0');
    //         });
    //         $get_receive->whereHas('receive_details', function ($get_receive) {
    //             $get_receive->where('department_id', '=', $department);
    //         });
            
    //         $receive_stockcard = $get_receive->get();

    //         // $issuance_stockcard = ;
    //         // $backorder_stockcard = ;
    //         // $borrow_stockcard = ;
    //         // $restock_stockcard = ;
    //     } else if($item != 'undefined' && $department == 'undefined' && $supplier != 'undefined' && $brand != 'undefined' && $catalog != 'undefined'){
    //         $variant_id = Variants::where('item_id', '=', $item)->where('supplier_id', '=', $supplier)->where('brand', '=', $brand)->where('catalog_no', '=', $catalog)->get();
    //         $var_id=array();
    //         foreach($variant_id AS $vi){
    //             $var_id[]=$vi->id;
    //         }
    //         $variant = Variants::whereIn('id', $var_id)->get();

    //         // $issuance_stockcard = ;
    //         // $backorder_stockcard = ;
    //         // $borrow_stockcard = ;
    //         // $restock_stockcard = ;
    //     } else if($item != 'undefined' && $department == 'undefined' && $supplier == 'undefined' && $brand != 'undefined' && $catalog != 'undefined'){
    //         $variant_id = Variants::where('item_id', '=', $item)->where('brand', '=', $brand)->where('catalog_no', '=', $catalog)->get();
    //         $var_id=array();
    //         foreach($variant_id AS $vi){
    //             $var_id[]=$vi->id;
    //         }
    //         $variant = Variants::whereIn('id', $var_id)->get();

    //         // $issuance_stockcard = ;
    //         // $backorder_stockcard = ;
    //         // $borrow_stockcard = ;
    //         // $restock_stockcard = ;
    //     } else if($item != 'undefined' && $department == 'undefined' && $supplier == 'undefined' && $brand == 'undefined' && $catalog != 'undefined'){
    //         $variant_id = Variants::where('item_id', '=', $item)->where('catalog_no', '=', $catalog)->get();
    //         $var_id=array();
    //         foreach($variant_id AS $vi){
    //             $var_id[]=$vi->id;
    //         }
    //         $variant = Variants::whereIn('id', $var_id)->get();

    //         // $issuance_stockcard = ;
    //         // $backorder_stockcard = ;
    //         // $borrow_stockcard = ;
    //         // $restock_stockcard = ;
    //     } else if($item != 'undefined' && $department == 'undefined' && $supplier == 'undefined' && $brand == 'undefined' && $catalog == 'undefined'){
    //         $variant_id = Variants::where('item_id', '=', $item)->get();
    //         // $variant_id = Variants::where('item_id', '=', $item)->where('supplier_id', '=', $supplier)->where('brand', '=', $brand)->where('catalog_no', '=', $catalog)->get();
    //             $var_id=array();
    //             foreach($variant_id AS $vi){
    //                 $var_id[]=$vi->id;
    //             }
    //             $variant = Variants::whereIn('id', $var_id)->get();

    //         // $issuance_stockcard = ;
    //         // $backorder_stockcard = ;
    //         // $borrow_stockcard = ;
    //         // $restock_stockcard = ;
    //     }
        
        
      
    //         foreach($receive_stockcard AS $res){
    //                 $ItemData[]=[
    //                 'date'=>$res->receive_head->receive_date,
    //                 'pr_no'=>$res->receive_details->pr_no,
    //                 'po_no'=>$res->receive_head->po_no,
    //                 'supplier_name'=>$res->supplier_name,
    //                 'catalog_no'=>$res->catalog_no,
    //                 'brand'=>$res->brand,
    //                 'department'=>$res->receive_details->$department_name,
    //                 'method'=>'Receive',
    //                 'notes'=>$res->receive_head->remarks,
    //                 'total_cost'=>$total_cost,
    //                 'quantity'=>$res->rec_quantity,
                    
    //             ];
    //         }

    //     //     foreach($issuance_stockcard AS $iss){
    //     //             $ItemData[]=[
    //     //             'date'=>$iss->issuance_date,
    //     //             'pr_no'=>$pr_no,
    //     //             'po_no'=>$po_no,
    //     //             'supplier_name'=>$supplier_name,
    //     //             'catalog_no'=>$catalog_no,
    //     //             'brand'=>$brand_name,
    //     //             'department'=>$department_name,
    //     //             'method'=>'Issuance',
    //     //             'notes'=>$iss->remarks,
    //     //             'total_cost'=>$total_cost,
    //     //             'quantity'=>$iss->issued_qty,
                    
    //     //         ];
    //     //     }
        
    //     //     foreach($backorder_stockcard AS $bas){
    //     //             $ItemData[]=[
    //     //             'date'=>$bas->backorder_date,
    //     //             'pr_no'=>$pr_no,
    //     //             'po_no'=>$po_no,
    //     //             'supplier_name'=>$supplier_name,
    //     //             'catalog_no'=>$catalog_no,
    //     //             'brand'=>$brand_name,
    //     //             'department'=>$department_name,
    //     //             'method'=>'Backorder',
    //     //             'notes'=>$bas->remarks,
    //     //             'total_cost'=>$total_cost,
    //     //             'quantity'=>$bas->bo_quantity,
                    
    //     //         ];
    //     //     }

    //     //     foreach($borrow_stockcard AS $bos){
    //     //             $ItemData[]=[
    //     //             'date'=>$bos->borrow_date,
    //     //             'pr_no'=>$pr_no,
    //     //             'po_no'=>$po_no,
    //     //             'supplier_name'=>$supplier_name,
    //     //             'catalog_no'=>$catalog_no,
    //     //             'brand'=>$brand_name,
    //     //             'department'=>$department_name,
    //     //             'method'=>'Borrow',
    //     //             'notes'=>$bos->remarks,
    //     //             'total_cost'=>$total_cost,
    //     //             'quantity'=>$bos->quantity,
                    
    //     //         ];
    //     //     }

    //     //     foreach($restock_stockcard AS $rts){
    //     //             $ItemData[]=[
    //     //             'date'=>$rts->borrow_date,
    //     //             'pr_no'=>$pr_no,
    //     //             'po_no'=>$po_no,
    //     //             'supplier_name'=>$supplier_name,
    //     //             'catalog_no'=>$catalog_no,
    //     //             'brand'=>$brand_name,
    //     //             'department'=>$department_name,
    //     //             'method'=>'Restock',
    //     //             'notes'=>$rts->remarks,
    //     //             'total_cost'=>$total_cost,
    //     //             'quantity'=>$rts->quantity,
                    
    //     //         ];
    //     //     }

    //     //     $stockcard = $ItemData->sortBy('date');
    //     //     // $sorted = $collection->sortByDesc('date');

    //     //     $stockcard->values()->all();


    //     return response()->json($ItemData);
    // }
}
