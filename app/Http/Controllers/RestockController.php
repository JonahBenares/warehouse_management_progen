<?php

namespace App\Http\Controllers;

use App\Models\RestockHead;
use App\Models\RestockDetails;
use App\Models\RestockReason;
use App\Models\MRS;
use App\Models\User;
use App\Models\Items;
use App\Models\IssuanceHead;
use App\Models\IssuanceItems;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\PRItems;
use App\Models\Variants;
use App\Models\VariantsBalance;
use App\Models\PIVBalance;
use App\Models\ItemStatus;
use Illuminate\Http\Request;
use App\Http\Requests\RestockRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Config;

class RestockController extends Controller
{
    public function get_all_restocks(Request $request){
        $filter=$request->get('filter');
        $restockitems = RestockHead::query()->when($request->get('filter'), function ($query, $filter) {
            $query->where('mrs_no', 'LIKE', '%' . $filter . '%')
            ->orWhere('date', 'LIKE', '%' . $filter . '%')
            ->orWhere('time', 'LIKE', '%' . $filter . '%')
            ->orWhere('source_pr', 'LIKE', '%' . $filter . '%')
            ->orWhere('destination', 'LIKE', '%' . $filter . '%')
            ->orWhere('purpose', 'LIKE', '%' . $filter . '%');
        })->paginate(10);
        // ->join('receive_details', 'receive_details.pr_no', '=', 'restock_head.source_pr')->paginate(10);
        return response()->json($restockitems);
    }

    public function create_restock_head(Request $request){
        $curr_year = date('Y');
        $curr_mo = date('m');
        if(MRS::where('year', '=', $curr_year)->exists()) {
            $mrec = MRS::where('year', '=', $curr_year)->max('series') + 1;
            $max_value = str_pad($mrec,4,"0",STR_PAD_LEFT);;
        } else {
            $max_value = '0001';
        }

        $mrs_no = 'MRS-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
        $id = Auth::id();
        $formData=[
            'mrs_no'=>$mrs_no,
            'date'=>date("Y-m-d"),
            'time'=>date("H:i:s"),
            'source_pr'=>'',
            'destination'=>'',
            'user_id'=>$id 
        ];
        return response()->json($formData);
    }

    public function get_pr_details($pr_no){
        $purpose_id = ($pr_no!='WH Stocks') ? ReceiveDetails::where('pr_no',$pr_no)->value('purpose_id') : '';
        $enduse_id = ($pr_no!='WH Stocks') ? ReceiveDetails::where('pr_no',$pr_no)->value('enduse_id') : '';
        $department_id = ($pr_no!='WH Stocks') ? ReceiveDetails::where('pr_no',$pr_no)->value('department_id') : '';
        $purpose = ($pr_no!='WH Stocks') ? ReceiveDetails::where('pr_no',$pr_no)->value('purpose_name') : '';
        $enduser = ($pr_no!='WH Stocks') ? ReceiveDetails::where('pr_no',$pr_no)->value('enduse_name') : '';
        $department = ($pr_no!='WH Stocks') ? ReceiveDetails::where('pr_no',$pr_no)->value('department_name') : '';

        // $issued_items = IssuanceItems::with('issuance_head')->whereRelation('issuance_head', 'pr_no', '=', $pr_no)->wherecolumn('pr_qty','=','issued_qty')->get();
        // $issued_items = PRItems::where('pr_no',$pr_no)->wherecolumn('receive_qty','=','issuance_qty')
        //         ->wherecolumn('issuance_qty','!=','restock_qty')
        //         ->wherecolumn('issuance_qty','!=','transfer_qty')
        //         ->get();
        // $count=$issued_items->count();

        return response()->json([
            'purpose_id'=>$purpose_id,
            'enduse_id'=>$enduse_id,
            'department_id'=>$department_id,
            'purpose'=>$purpose,
            'enduser'=>$enduser,
            'department'=>$department,
            // 'count'=>$count
        ],200);
    }

    public function add_restock_head(RestockRequest $request){

        $validated = $request->validated();
        $validated['department_id']=$request->department_id ?? 0;
        // $validated['source_pr']=$request->source_pr ?? '';
        $validated['department']=$request->department ?? '';
        $validated['enduse_id']=$request->enduse_id ?? 0;
        $validated['enduse']=$request->enduser ?? '';
        $validated['purpose_id']=$request->purpose_id ?? 0;
        $validated['purpose']=$request->purpose ?? '';
        $validated['user_id']=$request->user_id;
        $restock_id = RestockHead::insertGetId($validated);
        $mrs = $request->mrs_no;
        $ser = explode("-",$mrs);
        $series = $ser[3];
        MRS::create([
            'year' => date("Y"),
            'series'=>$series
        ]);

        return $restock_id."-".$request->source_pr;
    }

    function unique_multidim_array($array, $key) {

        $temp_array = array();
        $i = 0;
        $key_array = array();
    
        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
    
    public function get_restock_details($id,$source_pr){
        $all_iss_items=array();
        $all_iss_items_whs=array();
        $all_no_iss_items=array();
        $head = RestockHead::find($id);

        $all_issued_items = IssuanceItems::with('issuance_head','variants')->whereRelation('issuance_head', 'pr_no', '=', $source_pr)->get();
        $all_not_issued_items = ReceiveItems::with('receive_details','variants')->whereRelation('receive_details', 'pr_no', '=', $source_pr)->get();
        
        foreach($all_issued_items AS $aii){
                $receive_items_id = ReceiveItems::with('receive_details')->whereRelation('receive_details', 'pr_no', '=', $source_pr)->where('item_id',$aii->item_id)->where('variant_id',$aii->variant_id)->value('id');
                $pr_balance=VariantsBalance::where('item_id',$aii->item_id)->where('variant_id',$aii->variant_id)->value('balance');
                $receive_qty=VariantsBalance::where('item_id',$aii->item_id)->where('variant_id',$aii->variant_id)->value('receive_qty');
                $issued_qty=VariantsBalance::where('item_id',$aii->item_id)->where('variant_id',$aii->variant_id)->value('issuance_qty');
                //$restock_qty = RestockDetails::where('receive_items_id','=',$receive_items_id)->where('identifier','=','Issued')->sum('quantity');
                //$restock_qty = RestockDetails::where('receive_items_id','=',$receive_items_id)->sum('quantity');
                $restock_qty =  RestockDetails::where('receive_items_id', '=', $receive_items_id)
                ->where(function ($query) {
                    $query->where('identifier', '=', 'Issued')
                          ->orWhere('identifier', '=', 'Incomplete Issuance');
                })
                ->sum('quantity');
                $mif_no = IssuanceHead::where('id','=',$aii->issuance_head_id)->value('mif_no');
                $remaining_qty = $aii->issued_qty - $restock_qty;
            //if($aii->pr_qty == $aii->issued_qty && $issued_qty != 0 && $remaining_qty != 0){
                if($aii->issued_qty > 0&& $issued_qty != 0 && $remaining_qty != 0){
                $all_iss_items[]=[
                    'receive_items_id'=>$receive_items_id,
                    'mif_no'=>$mif_no,
                    'item_id'=>$aii->item_id,
                    'variant_id'=>$aii->variant_id,
                    'issued_qty'=>$aii->issued_qty,
                    'restocked_qty'=>$restock_qty,
                    'remaining_qty'=>$remaining_qty,
                    'supplier_name'=>$aii->variants->supplier_name,
                    'item_description'=>$aii->item_description,
                    'brand'=>$aii->variants->brand,
                    'catalog_no'=>$aii->variants->catalog_no,
                    'serial_no'=>$aii->variants->serial_no,
                    'uom'=>$aii->variants->uom,
                    'color'=>$aii->variants->color,
                    'size'=>$aii->variants->size,
                    'item_status_id'=>$aii->variants->item_status_id,
                ];
            }

           // $restock_qty_in = RestockDetails::where('receive_items_id','=',$receive_items_id)->where('identifier','=','Incomplete Issuance')->sum('quantity');
           // $restock_qty_in = RestockDetails::where('receive_items_id','=',$receive_items_id)->sum('quantity');
           $restock_qty_in =  RestockDetails::where('receive_items_id', '=', $receive_items_id)
           ->where(function ($query) {
               $query->where('identifier', '=', 'Issued')
                     ->orWhere('identifier', '=', 'Incomplete Issuance');
           })
           ->sum('quantity');
            $remaining_qty_in = $aii->issued_qty - $restock_qty_in;
            // if($receive_qty != 0 && $issued_qty != 0 && $aii->pr_qty != $aii->issued_qty && $remaining_qty_in != 0){
            if($remaining_qty_in != 0){
                $all_iss_items_whs[]=[
                    'receive_items_id'=>$receive_items_id,
                    'mif_no'=>$mif_no,
                    'item_id'=>$aii->item_id,
                    'variant_id'=>$aii->variant_id,
                    'issued_qty'=>$aii->issued_qty,
                    'restocked_qty'=>$restock_qty_in,
                    'remaining_qty'=>$remaining_qty_in,
                    'supplier_name'=>$aii->variants->supplier_name,
                    'item_description'=>$aii->item_description,
                    'brand'=>$aii->variants->brand,
                    'catalog_no'=>$aii->variants->catalog_no,
                    'serial_no'=>$aii->variants->serial_no,
                    'uom'=>$aii->variants->uom,
                    'color'=>$aii->variants->color,
                    'size'=>$aii->variants->size,
                    'item_status_id'=>$aii->variants->item_status_id,

                ];
            }
        }

            foreach($all_not_issued_items AS $anii){
                // $remaining_qty=PRItems::where('pr_no',$source_pr)->where('item_id',$anii->item_id)->value('balance');
                // $remaining_qty = VariantsBalance::where('item_id',$anii->item_id)->where('variant_id',$anii->variant_id)->value('balance');
                $pr_balance=VariantsBalance::where('item_id',$anii->item_id)->where('variant_id',$anii->variant_id)->value('balance');
                $receive_qty=VariantsBalance::where('item_id',$anii->item_id)->where('variant_id',$anii->variant_id)->value('receive_qty');
                $backorder_qty=VariantsBalance::where('item_id',$anii->item_id)->where('variant_id',$anii->variant_id)->value('backorder_qty');
                $issued_qty=VariantsBalance::where('item_id',$anii->item_id)->where('variant_id',$anii->variant_id)->value('issuance_qty');
                $restock_qty = RestockDetails::where('receive_items_id','=',$anii->id)->where('identifier','=','Not Issued')->sum('quantity');
                // if($issued_qty != 0){
                //     $remaining_qty = ($receive_qty - $issued_qty) - $restock_qty;
                // }else{
                //     $remaining_qty = $receive_qty - $restock_qty;
                // }



                if($backorder_qty != 0){
                    $total_receive = $receive_qty + $backorder_qty;
                }else{
                    $total_receive = VariantsBalance::where('item_id',$anii->item_id)->where('variant_id',$anii->variant_id)->value('receive_qty');
                }

                $remaining_qty = ($total_receive - $issued_qty) - $restock_qty;

            if($total_receive != $issued_qty && $remaining_qty != 0){
                $all_no_iss_items[]=[
                    'receive_items_id'=>$anii->id,
                    'mif_no'=>'Excess',
                    'item_id'=>$anii->item_id,
                    'variant_id'=>$anii->variant_id,
                    'issued_qty'=>$issued_qty,
                    'restocked_qty'=>$restock_qty,
                    'remaining_qty'=>$remaining_qty,
                    'supplier_name'=>$anii->variants->supplier_name,
                    'item_description'=>$anii->item_description,
                    'brand'=>$anii->variants->brand,
                    'catalog_no'=>$anii->variants->catalog_no,
                    'serial_no'=>$anii->variants->serial_no,
                    'uom'=>$anii->variants->uom,
                    'color'=>$anii->variants->color,
                    'size'=>$anii->variants->size,
                    'item_status_id'=>$anii->variants->item_status_id,

                ];
            } 
        }
        
        $all_issitems = $this->unique_multidim_array($all_iss_items, 'item_id','variant_id');
        $all_iss_itemswhs = $this->unique_multidim_array($all_iss_items_whs, 'item_id','variant_id');
        $all_no_issitems = $this->unique_multidim_array($all_no_iss_items, 'item_id','variant_id');
        return response()->json([
            'head'=>$head,
            'all_iss_items'=>$all_issitems,
            'all_iss_items_whs'=>$all_iss_itemswhs,
            'all_no_iss_items'=>$all_no_issitems,
        ],200);
    }

    public function save_restock(Request $request){
        $restock_head_id = $request->restock_head_id;
        $pr_no = $request->source_pr;
        $restock_issued = $request->input('restock_issued');
        foreach(json_decode($restock_issued) as $ri){
            if($ri->restock_qty != 0 && $ri->restock_qty != ''){
                $item_status = ItemStatus::where('id',$ri->item_status_id)->value('status');
                $item_mode = ItemStatus::where('id',$ri->item_status_id)->value('modes');
                $reason=RestockReason::where('id',$ri->reason_id)->value('reason');

                $pr_qty = IssuanceItems::with('issuance_head')->whereRelation('issuance_head', 'pr_no', '=', $pr_no)->where('variant_id','=',$ri->variant_id)->where('item_id', '=', $ri->item_id)->value('pr_qty');
                $issued_qty = IssuanceItems::with('issuance_head')->whereRelation('issuance_head', 'pr_no', '=', $pr_no)->where('variant_id','=',$ri->variant_id)->where('item_id', '=', $ri->item_id)->value('issued_qty');

                if($pr_qty != $issued_qty){
                    $identifier = 'Incomplete Issuance';
                }else{
                    $identifier = 'Issued';
                }

                if(Variants::where('id','=',$ri->variant_id)->where('item_id', '=', $ri->item_id)->where('item_status_id', '=', $ri->item_status_id)->exists()){
                    $restockdata['restock_head_id'] = $request->restock_head_id;
                    $restockdata['item_id'] = $ri->item_id;
                    $restockdata['item_description'] = $ri->item_desc ?? '';
                    $restockdata['variant_id'] = $ri->variant_id;
                    $restockdata['receive_items_id'] = $ri->receive_items_id;
                    $restockdata['quantity'] =  $ri->restock_qty;
                    $restockdata['reason_id'] = $ri->reason_id;
                    $restockdata['reason'] = $reason ?? '';
                    $restockdata['remarks'] = $ri->remarks ?? '';
                    $restockdata['item_status_id'] = $ri->item_status_id;
                    $restockdata['item_status'] = $item_status ?? '';
                    $restockdata['identifier'] = $identifier;
                    $restockdata['mif_no'] = $ri->mif_no;
                    $restock_save=RestockDetails::create($restockdata);

                    $update_balance = Items::find($ri->item_id);
                    $update_balance->running_balance = $update_balance->running_balance + $ri->restock_qty;
                    $update_balance->save();

                    $updatevariant = Variants::find($ri->variant_id);
                    $updatevariant->quantity = $updatevariant->quantity + $ri->restock_qty;
                    $updatevariant->save();

                    $pritems_id = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $ri->item_id)->value('id');
                    $update = PRItems::find($pritems_id);
                    $update->restock_qty = $update->restock_qty + $ri->restock_qty;
                    $update->balance = $update->balance + $ri->restock_qty;
                    $update->save();

                    $varbal_id = VariantsBalance::where('variant_id','=',$ri->variant_id)->where('item_id', '=', $ri->item_id)->value('id');
                    $updatevar = VariantsBalance::find($varbal_id);
                    $updatevar->restock_qty = $updatevar->restock_qty + $ri->restock_qty;
                    $updatevar->balance = $updatevar->balance + $ri->restock_qty;
                    $updatevar->save();

                    $piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $ri->item_id)->where('variant_id','=',$ri->variant_id)->value('id');
                    $updatePIV = PIVBalance::find($piv_id);
                    $updatePIV->quantity = $updatePIV->quantity + $ri->restock_qty;
                    $updatePIV->save();
                }else{
                    $variants_restock = Variants::where('id','=',$ri->variant_id)->get();
                        foreach(json_decode($variants_restock) as $vr){
                            if(Variants::where('supplier_id','=',$vr->supplier_id)
                            ->where('item_id', '=', $vr->item_id)
                            ->where('brand', '=', $vr->brand)
                            ->where('item_status_id', '=', $ri->item_status_id)
                            ->where('expiration', '=', $vr->expiration)
                            ->where('uom', '=', $vr->uom)
                            ->where('color', '=', $vr->color)
                            ->where('size', '=', $vr->size)
                            ->where('average_cost','=',$vr->average_cost)
                            ->exists()){
    
                                $variantid = Variants::where('supplier_id','=',$vr->supplier_id)
                                    ->where('item_id', '=', $vr->item_id)
                                    ->where('brand', '=', $vr->brand)
                                    ->where('item_status_id', '=', $ri->item_status_id)
                                    ->where('expiration', '=', $vr->expiration)
                                    ->where('uom', '=', $vr->uom)
                                    ->where('color', '=', $vr->color)
                                    ->where('size', '=', $vr->size)
                                    ->where('average_cost','=',$vr->average_cost)
                                    ->value('id');
                            }else{
                                $var_data['item_id'] = $vr->item_id;
                                $var_data['supplier_id'] = $vr->supplier_id;
                                $var_data['supplier_name'] = $vr->supplier_name ;
                                $var_data['catalog_no'] = $vr->catalog_no;
                                $var_data['brand'] = $vr->brand;
                                $var_data['color'] = $vr->color;
                                $var_data['size'] = $vr->size;
                                $var_data['barcode'] = $vr->barcode;
                                $var_data['expiration'] = $vr->expiration;
                                $var_data['serial_no'] = $vr->serial_no;
                                $var_data['uom'] = $vr->uom;
                                $var_data['unit_cost'] = $vr->unit_cost;
                                $var_data['shipping_cost'] = $vr->shipping_cost;
                                $var_data['average_cost'] = $vr->unit_cost + $vr->shipping_cost;
                                $var_data['item_status_id'] = $ri->item_status_id;
                                $variantid = Variants::insertGetId($var_data);
                            }

                            $restockdata['restock_head_id'] = $request->restock_head_id;
                            $restockdata['item_id'] = $ri->item_id;
                            $restockdata['item_description'] = $ri->item_desc ?? '';
                            $restockdata['variant_id'] = $variantid;
                            $restockdata['receive_items_id'] = $ri->receive_items_id;
                            $restockdata['quantity'] =  $ri->restock_qty;
                            $restockdata['reason_id'] = $ri->reason_id;
                            $restockdata['reason'] = $reason ?? '';
                            $restockdata['remarks'] = $ri->remarks ?? '';
                            $restockdata['item_status_id'] = $ri->item_status_id;
                            $restockdata['item_status'] = $item_status ?? '';
                            $restockdata['identifier'] = 'Issued';
                            $restockdata['mif_no'] = $ri->mif_no;
                            $restock_save=RestockDetails::create($restockdata);

                            if($item_mode == 'add'){
                                $update_balance = Items::find($ri->item_id);
                                $update_balance->running_balance = $update_balance->running_balance + $ri->restock_qty;
                                $update_balance->save();

                                $pritems_id = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $ri->item_id)->value('id');
                                $update = PRItems::find($pritems_id);
                                $update->restock_qty = $update->restock_qty + $ri->restock_qty;
                                $update->balance = $update->balance + $ri->restock_qty;
                                $update->save();

                                $varbal_id = VariantsBalance::where('variant_id','=',$variantid)->where('item_id', '=', $ri->item_id)->value('id');
                                $updatevar = VariantsBalance::find($varbal_id);
                                $updatevar->restock_qty = $updatevar->restock_qty + $ri->restock_qty;
                                $updatevar->balance = $updatevar->balance + $ri->restock_qty;
                                $updatevar->save();

                                $piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $ri->item_id)->where('variant_id','=',$variantid)->value('id');
                                $updatePIV = PIVBalance::find($piv_id);
                                $updatePIV->quantity = $updatePIV->quantity + $ri->restock_qty;
                                $updatePIV->save();
                            }else{
                                $pritems_id = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $ri->item_id)->value('id');
                                $update = PRItems::find($pritems_id);
                                $update->damage_qty = $update->damage_qty + $ri->restock_qty;
                                $update->save();

                                if(VariantsBalance::where('variant_id','=',$variantid)->where('item_id', '=', $ri->item_id)->exists()){
                                    $var_bal_id = VariantsBalance::where('variant_id','=',$variantid)->where('item_id', '=', $ri->item_id)->value('id');
                                    $vardata = VariantsBalance::find($var_bal_id);
                                    $vardata->damage_qty = $vardata->damage_qty + $ri->restock_qty;
                                    $vardata->save();
                                }else{
                                    $vardata['variant_id']=$variantid;
                                    $vardata['item_id']=$ri->item_id;
                                    $vardata['damage_qty']=$ri->restock_qty;
                                    VariantsBalance::create($vardata);
                                }

                                if(!PIVBalance::where('pr_no','=',$pr_no)->where('variant_id','=',$variantid)->where('item_id', '=', $ri->item_id)->exists()){
                                    $pivdata['pr_no']=strtoupper($pr_no);
                                    $pivdata['variant_id']=$variantid;
                                    $pivdata['item_id']=$ri->item_id;
                                    PIVBalance::create($pivdata);
                                }
                            }
                        }
                }
            }
        }
        $restock_headsave=RestockHead::where("id",$restock_head_id)->first();
        $restock_headsave->update([
            'saved'=> 1,
        ]);
        echo $restock_head_id;
    }
    
    public function save_restock_wh(Request $request){

        try {             
            DB::beginTransaction();       

        $restock_head_id = $request->restock_head_id;
        $pr_no = $request->source_pr;
        $destination = $request->destination;
        $restock_issued_wh = $request->input('restock_issued_wh');
        $restock_no_issue_wh = $request->input('restock_no_issue_wh');
     
        foreach(json_decode($restock_issued_wh) as $riw){
            if($riw->restock_qty != 0 || $riw->restock_qty != ''){
                $item_status = ItemStatus::where('id',$riw->item_status_id)->value('status');
                $item_mode = ItemStatus::where('id',$riw->item_status_id)->value('modes');
                $reason=RestockReason::where('id',$riw->reason_id)->value('reason');

                $pr_qty = IssuanceItems::with('issuance_head')->whereRelation('issuance_head', 'pr_no', '=', $pr_no)->where('variant_id','=',$riw->variant_id)->where('item_id', '=', $riw->item_id)->value('pr_qty');
                $issued_qty = IssuanceItems::with('issuance_head')->whereRelation('issuance_head', 'pr_no', '=', $pr_no)->where('variant_id','=',$riw->variant_id)->where('item_id', '=', $riw->item_id)->value('issued_qty');

                if($pr_qty != $issued_qty){
                    $identifier = 'Incomplete Issuance';
                }else{
                    $identifier = 'Issued';
                }

                if(Variants::where('id','=',$riw->variant_id)->where('item_id', '=', $riw->item_id)->where('item_status_id', '=', $riw->item_status_id)->exists()){
                    $restockdata['restock_head_id'] = $request->restock_head_id;
                    $restockdata['item_id'] = $riw->item_id;
                    $restockdata['item_description'] = $riw->item_desc ?? '';
                    $restockdata['variant_id'] = $riw->variant_id;
                    $restockdata['receive_items_id'] = $riw->receive_items_id;
                    $restockdata['quantity'] =  $riw->restock_qty;
                    $restockdata['reason_id'] = $riw->reason_id;
                    $restockdata['reason'] = $reason ?? '';
                    $restockdata['remarks'] = $riw->remarks ?? '';
                    $restockdata['item_status_id'] = $riw->item_status_id;
                    $restockdata['item_status'] = $item_status ?? '';
                    $restockdata['identifier'] = $identifier;
                    $restockdata['mif_no'] = $riw->mif_no;
                    $restock_save=RestockDetails::create($restockdata);

                    $update_balance = Items::find($riw->item_id);
                    $update_balance->running_balance = $update_balance->running_balance + $riw->restock_qty;
                    $update_balance->save();

                    $updatevariant = Variants::find($riw->variant_id);
                    $updatevariant->quantity = $updatevariant->quantity + $riw->restock_qty;
                    $updatevariant->save();

                    //if(PRItems::where('pr_no','=','WH STOCKS')->where('item_id', '=', $riw->item_id)->exists()){

                        $pritems_id_to = PRItems::where('pr_no','=','WH STOCKS')->where('item_id', '=', $riw->item_id)->value('id');
                        $update_to = PRItems::find($pritems_id_to);
                        $update_to->restock_qty = $update_to->restock_qty + $riw->restock_qty;
                        $update_to->balance = $update_to->balance + $riw->restock_qty;
                        $update_to->save();
                   // } else {

                            // $insert['pr_no'] = "WH STOCKS";
                            // $insert['item_id'] = $riw->item_id;
                            // $insert['restock_qty'] = $riw->restock_qty;
                            // $insert['balance'] = $riw->restock_qty;
                            // $restock_save=PRItems::create($insert);

                    //}

                    $varbal_id = VariantsBalance::where('variant_id','=',$riw->variant_id)->where('item_id', '=', $riw->item_id)->value('id');
                    $updatevar = VariantsBalance::find($varbal_id);
                    $updatevar->restock_qty = $updatevar->restock_qty + $riw->restock_qty;
                    $updatevar->balance = $updatevar->balance + $riw->restock_qty;
                    $updatevar->save();

                    if(PIVBalance::where('pr_no','=','WH STOCKS')->where('item_id', '=', $riw->item_id)->where('variant_id','=',$riw->variant_id)->exists()){
                        $piv_id = PIVBalance::where('pr_no','=','WH STOCKS')->where('item_id', '=', $riw->item_id)->where('variant_id','=',$riw->variant_id)->value('id');
                        $updatePIV = PIVBalance::find($piv_id);
                        $updatePIV->quantity = $updatePIV->quantity + $riw->restock_qty;
                        $updatePIV->save();
                    }else{
                        $pivdata['pr_no']='WH STOCKS';
                        $pivdata['variant_id']=$riw->variant_id;
                        $pivdata['item_id']=$riw->item_id;
                        $pivdata['quantity']=$riw->restock_qty;
                        PIVBalance::create($pivdata);
                    }
                }
                else{
                    $variants_restock = Variants::where('id','=',$riw->variant_id)->get();
                        foreach(json_decode($variants_restock) as $vr){
                            if(Variants::where('supplier_id','=',$vr->supplier_id)
                            ->where('item_id', '=', $vr->item_id)
                            ->where('brand', '=', $vr->brand)
                            ->where('item_status_id', '=', $riw->item_status_id)
                            ->where('expiration', '=', $vr->expiration)
                            ->where('uom', '=', $vr->uom)
                            ->where('color', '=', $vr->color)
                            ->where('size', '=', $vr->size)
                            ->where('average_cost','=',$vr->average_cost)
                            ->exists()){
    
                                $variantid = Variants::where('supplier_id','=',$vr->supplier_id)
                                    ->where('item_id', '=', $vr->item_id)
                                    ->where('brand', '=', $vr->brand)
                                    ->where('item_status_id', '=', $riw->item_status_id)
                                    ->where('expiration', '=', $vr->expiration)
                                    ->where('uom', '=', $vr->uom)
                                    ->where('color', '=', $vr->color)
                                    ->where('size', '=', $vr->size)
                                    ->where('average_cost','=',$vr->average_cost)
                                    ->value('id');
                            }else{
                                $var_data['item_id'] = $vr->item_id;
                                $var_data['supplier_id'] = $vr->supplier_id;
                                $var_data['supplier_name'] = $vr->supplier_name ;
                                $var_data['catalog_no'] = $vr->catalog_no;
                                $var_data['brand'] = $vr->brand;
                                $var_data['color'] = $vr->color;
                                $var_data['size'] = $vr->size;
                                $var_data['barcode'] = $vr->barcode;
                                $var_data['expiration'] = $vr->expiration;
                                $var_data['serial_no'] = $vr->serial_no;
                                $var_data['uom'] = $vr->uom;
                                $var_data['unit_cost'] = $vr->unit_cost;
                                $var_data['shipping_cost'] = $vr->shipping_cost;
                                $var_data['average_cost'] = $vr->unit_cost + $vr->shipping_cost;
                                $var_data['item_status_id'] = $riw->item_status_id;
                                $variantid = Variants::insertGetId($var_data);
                            }

                            $restockdata['restock_head_id'] = $request->restock_head_id;
                            $restockdata['item_id'] = $riw->item_id;
                            $restockdata['item_description'] = $riw->item_desc ?? '';
                            $restockdata['variant_id'] = $variantid;
                            $restockdata['receive_items_id'] = $riw->receive_items_id;
                            $restockdata['quantity'] =  $riw->restock_qty;
                            $restockdata['reason_id'] = $riw->reason_id;
                            $restockdata['reason'] = $reason ?? '';
                            $restockdata['remarks'] = $riw->remarks ?? '';
                            $restockdata['item_status_id'] = $riw->item_status_id;
                            $restockdata['item_status'] = $item_status ?? '';
                            $restockdata['identifier'] = $identifier;
                            $restockdata['mif_no'] = $riw->mif_no;
                            $restock_save=RestockDetails::create($restockdata);

                            if($item_mode == 'add'){
                                $update_balance = Items::find($riw->item_id);
                                $update_balance->running_balance = $update_balance->running_balance + $riw->restock_qty;
                                $update_balance->save();

                                $pritems_id_to = PRItems::where('pr_no','=','WH STOCKS')->where('item_id', '=', $riw->item_id)->value('id');
                                $update_to = PRItems::find($pritems_id_to);
                                $update_to->restock_qty = $update_to->restock_qty + $riw->restock_qty;
                                $update_to->balance = $update_to->balance + $riw->restock_qty;
                                $update_to->save();

                                $varbal_id = VariantsBalance::where('variant_id','=',$riw->variant_id)->where('item_id', '=', $riw->item_id)->value('id');
                                $updatevar = VariantsBalance::find($varbal_id);
                                $updatevar->restock_qty = $updatevar->restock_qty + $riw->restock_qty;
                                $updatevar->balance = $updatevar->balance + $riw->restock_qty;
                                $updatevar->save();
            
                                $piv_id = PIVBalance::where('pr_no','=','WH STOCKS')->where('item_id', '=', $riw->item_id)->where('variant_id','=',$riw->variant_id)->value('id');
                                $updatePIV = PIVBalance::find($piv_id);
                                $updatePIV->quantity = $updatePIV->quantity + $riw->restock_qty;
                                $updatePIV->save();
                            }else{
                                $pritems_id_to = PRItems::where('pr_no','=','WH STOCKS')->where('item_id', '=', $riw->item_id)->value('id');
                                $update_to = PRItems::find($pritems_id_to);
                                $update_to->damage_qty = $update_to->damage_qty + $riw->restock_qty;
                                $update_to->save();

                                $pritems_id_from = PRItems::where('pr_no','=','WH STOCKS')->where('item_id', '=', $riw->item_id)->value('id');
                                $update_from = PRItems::find($pritems_id_from);
                                $update_from->damage_qty = $update_from->transfer_qty + $riw->restock_qty;
                                $update_from->save();

                                if(VariantsBalance::where('variant_id','=',$variantid)->where('item_id', '=', $riw->item_id)->exists()){
                                    $var_bal_id = VariantsBalance::where('variant_id','=',$variantid)->where('item_id', '=', $riw->item_id)->value('id');
                                    $vardata = VariantsBalance::find($var_bal_id);
                                    $vardata->damage_qty = $vardata->damage_qty + $riw->restock_qty;
                                    $vardata->save();
                                }else{
                                    $vardata['variant_id']=$variantid;
                                    $vardata['item_id']=$riw->item_id;
                                    $vardata['damage_qty']=$riw->restock_qty;
                                    VariantsBalance::create($vardata);
                                }

                                if(!PIVBalance::where('pr_no','=','WH STOCKS')->where('variant_id','=',$variantid)->where('item_id', '=', $riw->item_id)->exists()){
                                    $pivdata['pr_no']='WH STOCKS';
                                    $pivdata['variant_id']=$variantid;
                                    $pivdata['item_id']=$riw->item_id;
                                    PIVBalance::create($pivdata);
                                }
                            }
                       }
               }
            }
        }
    
     
        foreach(json_decode($restock_no_issue_wh) as $rniw){
            if($rniw->restock_qty_wh != 0 || $rniw->restock_qty_wh != ''){
                $item_status = ItemStatus::where('id',$rniw->item_status_id_wh)->value('status');
                $item_mode = ItemStatus::where('id',$rniw->item_status_id_wh)->value('modes');
                $reason=RestockReason::where('id',$rniw->reason_id_wh)->value('reason');

                if(Variants::where('id','=',$rniw->variant_id_wh)->where('item_id', '=', $rniw->item_id_wh)->where('item_status_id', '=', $rniw->item_status_id_wh)->exists()){
                    $restockdata['restock_head_id'] = $request->restock_head_id;
                    $restockdata['item_id'] = $rniw->item_id_wh;
                    $restockdata['item_description'] = $rniw->item_desc_wh ?? '';
                    $restockdata['variant_id'] = $rniw->variant_id_wh;
                    $restockdata['receive_items_id'] = $rniw->receive_items_id_wh;
                    $restockdata['quantity'] =  $rniw->restock_qty_wh;
                    $restockdata['reason_id'] = $rniw->reason_id_wh;
                    $restockdata['reason'] = $reason ?? '';
                    $restockdata['remarks'] = $rniw->remarks_wh ?? '';
                    $restockdata['item_status_id'] = $rniw->item_status_id_wh;
                    $restockdata['item_status'] = $item_status ?? '';
                    $restockdata['identifier'] = 'Not Issued';
                    $restockdata['mif_no'] = 'Excess';
                    $restock_save=RestockDetails::create($restockdata);

                 
                    $pritems_id_from = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $rniw->item_id_wh)->value('id');
                    $update_from = PRItems::find($pritems_id_from);
                    $update_from->transfer_qty = $update_from->transfer_qty + $rniw->restock_qty_wh;
                    $update_from->balance = $update_from->balance - $rniw->restock_qty_wh;
                    $update_from->save();

                    $pritems_id_to = PRItems::where('pr_no','=','WH STOCKS')->where('item_id', '=', $rniw->item_id_wh)->value('id');
                    $update_to = PRItems::find($pritems_id_to);
                    $update_to->restock_qty = $update_to->restock_qty + $rniw->restock_qty_wh;
                    $update_to->balance = $update_to->balance + $rniw->restock_qty_wh;
                    $update_to->save();

                    $varbal_id = VariantsBalance::where('variant_id','=',$rniw->variant_id_wh)->where('item_id', '=', $rniw->item_id_wh)->value('id');
                    $updatevar = VariantsBalance::find($varbal_id);
                    $updatevar->restock_qty = $updatevar->restock_qty + $rniw->restock_qty_wh;
                  
                    $updatevar->save();

                    if(PIVBalance::where('pr_no','=','WH STOCKS')->where('item_id', '=', $rniw->item_id_wh)->where('variant_id','=',$rniw->variant_id_wh)->exists()){
                        $piv_id = PIVBalance::where('pr_no','=','WH STOCKS')->where('item_id', '=', $rniw->item_id_wh)->where('variant_id','=',$rniw->variant_id_wh)->value('id');
                        $updatePIV = PIVBalance::find($piv_id);
                        $updatePIV->quantity = $updatePIV->quantity + $rniw->restock_qty_wh;
                        $updatePIV->save();

                        $old_piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $rniw->item_id_wh)->where('variant_id','=',$rniw->variant_id_wh)->value('id');
                        $update_old_piv = PIVBalance::find($old_piv_id);
                        $update_old_piv->quantity = $update_old_piv->quantity - $rniw->restock_qty_wh;
                        $update_old_piv->save();

                    }else{
                        $pivdata['pr_no']='WH STOCKS';
                        $pivdata['variant_id']=$rniw->variant_id_wh;
                        $pivdata['item_id']=$rniw->item_id_wh;
                        $pivdata['quantity']=$rniw->restock_qty_wh;
                        PIVBalance::create($pivdata);

                        $old_piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $rniw->item_id_wh)->where('variant_id','=',$rniw->variant_id_wh)->value('id');
                        $update_old_piv = PIVBalance::find($old_piv_id);
                        $update_old_piv->quantity = $update_old_piv->quantity - $rniw->restock_qty_wh;
                        $update_old_piv->save();
                    }
                }else{
                    $variants_restock = Variants::where('id','=',$rniw->variant_id_wh)->get();
                        foreach(json_decode($variants_restock) as $vr){
                            if(Variants::where('supplier_id','=',$vr->supplier_id)
                            ->where('item_id', '=', $vr->item_id)
                            ->where('brand', '=', $vr->brand)
                            ->where('item_status_id', '=', $rniw->item_status_id_wh)
                            ->where('expiration', '=', $vr->expiration)
                            ->where('uom', '=', $vr->uom)
                            ->where('color', '=', $vr->color)
                            ->where('size', '=', $vr->size)
                            ->where('average_cost','=',$vr->average_cost)
                            ->exists()){
    
                                $variantid = Variants::where('supplier_id','=',$vr->supplier_id)
                                    ->where('item_id', '=', $vr->item_id)
                                    ->where('brand', '=', $vr->brand)
                                    ->where('item_status_id', '=', $rniw->item_status_id_wh)
                                    ->where('expiration', '=', $vr->expiration)
                                    ->where('uom', '=', $vr->uom)
                                    ->where('color', '=', $vr->color)
                                    ->where('size', '=', $vr->size)
                                    ->where('average_cost','=',$vr->average_cost)
                                    ->value('id');
                            }else{
                                $var_data['item_id'] = $vr->item_id;
                                $var_data['supplier_id'] = $vr->supplier_id;
                                $var_data['supplier_name'] = $vr->supplier_name ;
                                $var_data['catalog_no'] = $vr->catalog_no;
                                $var_data['brand'] = $vr->brand;
                                $var_data['color'] = $vr->color;
                                $var_data['size'] = $vr->size;
                                $var_data['barcode'] = $vr->barcode;
                                $var_data['expiration'] = $vr->expiration;
                                $var_data['serial_no'] = $vr->serial_no;
                                $var_data['uom'] = $vr->uom;
                                $var_data['unit_cost'] = $vr->unit_cost;
                                $var_data['shipping_cost'] = $vr->shipping_cost;
                                $var_data['average_cost'] = $vr->unit_cost + $vr->shipping_cost;
                                $var_data['item_status_id'] = $rniw->item_status_id_wh;
                                $variantid = Variants::insertGetId($var_data);
                            }

                            $restockdata['restock_head_id'] = $request->restock_head_id;
                            $restockdata['item_id'] = $rniw->item_id_wh;
                            $restockdata['item_description'] = $rniw->item_desc_wh ?? '';
                            $restockdata['variant_id'] = $variantid;
                            $restockdata['receive_items_id'] = $rniw->receive_items_id_wh;
                            $restockdata['quantity'] =  $rniw->restock_qty_wh;
                            $restockdata['reason_id'] = $rniw->reason_id_wh;
                            $restockdata['reason'] = $reason ?? '';
                            $restockdata['remarks'] = $rniw->remarks_wh ?? '';
                            $restockdata['item_status_id'] = $rniw->item_status_id_wh;
                            $restockdata['item_status'] = $item_status ?? '';
                            $restockdata['identifier'] = 'Not Issued';
                            $restockdata['mif_no'] = 'Excess';
                            $restock_save=RestockDetails::create($restockdata);

                            if($item_mode == 'add'){
                            

                                $pritems_id_from = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $rniw->item_id_wh)->value('id');
                                $update_from = PRItems::find($pritems_id_from);
                                $update_from->transfer_qty = $update_from->transfer_qty + $rniw->restock_qty_wh;
                                $update_from->balance = $update_from->balance - $rniw->restock_qty_wh;
                                $update_from->save();

                                $pritems_id_to = PRItems::where('pr_no','=','WH STOCKS')->where('item_id', '=', $rniw->item_id_wh)->value('id');
                                $update_to = PRItems::find($pritems_id_to);
                                $update_to->restock_qty = $update_to->restock_qty + $rniw->restock_qty_wh;
                                $update_to->balance = $update_to->balance + $rniw->restock_qty_wh;
                                $update_to->save();

                                $varbal_id = VariantsBalance::where('variant_id','=',$rniw->variant_id_wh)->where('item_id', '=', $rniw->item_id_wh)->value('id');
                                $updatevar = VariantsBalance::find($varbal_id);
                                $updatevar->restock_qty = $updatevar->restock_qty + $rniw->restock_qty_wh;
                               
                                $updatevar->save();
            
                                $piv_id = PIVBalance::where('pr_no','=','WH STOCKS')->where('item_id', '=', $rniw->item_id_wh)->where('variant_id','=',$rniw->variant_id_wh)->value('id');
                                $updatePIV = PIVBalance::find($piv_id);
                                $updatePIV->quantity = $updatePIV->quantity + $rniw->restock_qty_wh;
                                $updatePIV->save();

                                $old_piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $rniw->item_id_wh)->where('variant_id','=',$rniw->variant_id_wh)->value('id');
                                $update_old_piv = PIVBalance::find($old_piv_id);
                                $update_old_piv->quantity = $update_old_piv->quantity - $rniw->restock_qty_wh;
                                $update_old_piv->save();
                            }else{
                                $update_balance = Items::find($rniw->item_id_wh);
                                $update_balance->running_balance = $update_balance->running_balance - $rniw->restock_qty_wh;
                                $update_balance->save();

                                $variants_id_old = Variants::find($rniw->variant_id_wh);
                                $variants_id_old->quantity = $variants_id_old->quantity - $rniw->restock_qty_wh;
                                $variants_id_old->save();

                                $pritems_id_from = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $rniw->item_id_wh)->value('id');
                                $update_from = PRItems::find($pritems_id_from);
                                $update_from->transfer_qty = $update_from->transfer_qty + $rniw->restock_qty_wh;
                                $update_from->balance = $update_from->balance - $rniw->restock_qty_wh;
                                $update_from->save();

                                $pritems_id_to = PRItems::where('pr_no','=','WH STOCKS')->where('item_id', '=', $rniw->item_id_wh)->value('id');
                                $update_to = PRItems::find($pritems_id_to);
                                $update_to->damage_qty = $update_to->damage_qty + $rniw->restock_qty_wh;
                                
                                $update_to->save();

                                if(VariantsBalance::where('variant_id','=',$variantid)->where('item_id', '=', $rniw->item_id_wh)->exists()){
                                    $var_bal_id = VariantsBalance::where('variant_id','=',$variantid)->where('item_id', '=', $rniw->item_id_wh)->value('id');
                                    $vardata = VariantsBalance::find($var_bal_id);
                                    $vardata->damage_qty = $vardata->damage_qty + $rniw->restock_qty_wh;
                                    $vardata->save();

                                    $var_bal_id_old = VariantsBalance::where('variant_id','=',$rniw->variant_id_wh)->where('item_id', '=', $rniw->item_id_wh)->value('id');
                                    $var_bal_old = VariantsBalance::find($var_bal_id_old);
                                    $var_bal_old->transfer_qty = $var_bal_old->transfer_qty + $rniw->restock_qty_wh;
                                    $var_bal_old->balance = $var_bal_old->balance - $rniw->restock_qty_wh;
                                    $var_bal_old->save();
                                }else{
                                    $vardata['variant_id']=$variantid;
                                    $vardata['item_id']=$rniw->item_id_wh;
                                    $vardata['damage_qty']=$rniw->restock_qty_wh;
                                    VariantsBalance::create($vardata);

                                    $var_bal_id_old = VariantsBalance::where('variant_id','=',$rniw->variant_id_wh)->where('item_id', '=', $rniw->item_id_wh)->value('id');
                                    $var_bal_old = VariantsBalance::find($var_bal_id_old);
                                    $var_bal_old->transfer_qty = $var_bal_old->transfer_qty + $rniw->restock_qty_wh;
                                    $var_bal_old->balance = $var_bal_old->balance - $rniw->restock_qty_wh;
                                    $var_bal_old->save();
                                }

                                if(!PIVBalance::where('pr_no','=','WH STOCKS')->where('variant_id','=',$variantid)->where('item_id', '=', $rniw->item_id_wh)->exists()){
                                    $pivdata['pr_no']='WH STOCKS';
                                    $pivdata['variant_id']=$variantid;
                                    $pivdata['item_id']=$rniw->item_id_wh;
                                    PIVBalance::create($pivdata);
                                }

                                $piv_bal_old_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $rniw->item_id_wh)->where('variant_id', '=', $rniw->variant_id_wh)->value('id');
                                $piv_bal_old = PIVBalance::find($piv_bal_old_id);
                                $piv_bal_old->quantity = $piv_bal_old->quantity - $rniw->restock_qty_wh;
                                $piv_bal_old->save();
                            }
                        }
                }
            }
        }

        $restock_headsave=RestockHead::where("id",$restock_head_id)->first();
        $restock_headsave->update([
            'saved'=> 1,
        ]);
        echo $restock_head_id;
        DB::commit();                 
    }  catch (\Illuminate\Validation\ValidationException $e) {                 
        // Handle validation errors                 
        DB::rollBack();                 
        $errors = $e->errors();                 
        $errorMessages = implode(' ', array_map(fn($messages) => implode(' ', (array) $messages), $errors));  
        return response()->json(['error' => nl2br($errorMessages)], 422);            
   } catch (\Exception $e) {                 
             // Handle general errors                 
             DB::rollBack();                 
            return response()->json(['error' => $e->getMessage()], 500);             
  }

    }

    public function cancel_transaction_restock($id){
        $head=RestockHead::where('id', '=', $id);
        $head->delete();
        $details=RestockDetails::where('restock_head_id', '=', $id);
        $details->delete();
    }

    public function getshow_details($id){
        $head = RestockHead::find($id);
        $source_pr = RestockHead::where('id',$id)->value('source_pr');
        // $purpose = IssuanceHead::where('pr_no',$source_pr)->value('purpose_name');
        // $enduser = IssuanceHead::where('pr_no',$source_pr)->value('enduse_name');
        // $department = IssuanceHead::where('pr_no',$source_pr)->value('department_name');
        $details = RestockDetails::with('variants')->where('restock_head_id',$id)->get();
        $returned_by=RestockHead::where('id',$id)->value('returned_by');
        $ret_position=User::where('id',$returned_by)->value('position');
        $inspected_by=RestockHead::where('id',$id)->value('inspected_by');
        $ins_position=User::where('id',$inspected_by)->value('position');
        $acknowledged_by=RestockHead::where('id',$id)->value('acknowledged_by');
        $ack_position=User::where('id',$acknowledged_by)->value('position');
        $noted_by=RestockHead::where('id',$id)->value('noted_by');
        $noted_position=User::where('id',$noted_by)->value('position');
        $all_items=array();
            foreach($details AS $rd){
                    $pr_balance=PRItems::where('pr_no',$source_pr)->where('item_id',$rd->item_id)->value('balance');
                    // $supplier_name=Variants::where('id',$rd->variant_id)->value('supplier_name');
                    // $catalog_no=Variants::where('id',$rd->variant_id)->value('catalog_no');
                    // $brand=Variants::where('id',$rd->variant_id)->value('brand');
                    // $serial_no=Variants::where('id',$rd->variant_id)->value('serial_no');
                    // $uom=Variants::where('id',$rd->variant_id)->value('uom');
                    // $color=Variants::where('id',$rd->variant_id)->value('color');
                    // $size=Variants::where('id',$rd->variant_id)->value('size');
                    $all_items[]=[
                        'id'=>$rd->id,
                        'item_id'=>$rd->item_id,
                        'item_description'=>$rd->item_description,
                        'variant_id'=>$rd->variant_id,
                        'brand'=>$rd->variants->brand,
                        'catalog_no'=>$rd->variants->catalog_no,
                        'serial_no'=>$rd->variants->serial_no,
                        'uom'=>$rd->variants->uom,
                        'color'=>$rd->variants->color,
                        'size'=>$rd->variants->size,
                        'supplier_name'=>$rd->variants->supplier_name,
                        'quantity'=>$rd->quantity,
                        'reason'=>$rd->reason,
                        'remarks'=>$rd->remarks,
                        'item_status'=>$rd->item_status,
                        'mif_no'=>$rd->mif_no,
                        'pr_balance'=>$pr_balance,
                    ];
            }
        return response()->json([
            'head'=>$head,
            // 'purpose'=>$purpose,
            // 'enduser'=>$enduser,
            // 'department'=>$department,
            'details'=>$all_items,
            'ret_position'=>$ret_position,
            'ins_position'=>$ins_position,
            'ack_position'=>$ack_position,
            'noted_position'=>$noted_position,
        ],200);
    }

    public function get_all_position($id){
        $position = User::where("id", $id)->value('position');
        return $position;
    }

    public function add_signatory(Request $request){
        $update_data=RestockHead::where('id',$request->id)->first();
        $validated=[
            'returned_by'=>$request->returned_by ?? 0,
            'returned_by_position'=> ($request->ret_position != 'null') ? $request->ret_position : '',
            'returned_by_name'=>User::where('id',$request->returned_by)->value('name'),

            'inspected_by'=>$request->inspected_by ?? 0,
            'inspected_by_name'=>User::where('id',$request->inspected_by)->value('name'),
            'inspected_by_position'=> ($request->ins_position != 'null') ? $request->ins_position : '',

            'acknowledged_by'=>$request->acknowledged_by ?? 0,
            'acknowledged_by_name'=>User::where('id',$request->acknowledged_by)->value('name'),
            'acknowledged_by_position'=> ($request->ack_position != 'null') ? $request->ack_position : '',

            'noted_by'=>$request->noted_by ?? 0,
            'noted_by_name'=>User::where('id',$request->noted_by)->value('name'),
            'noted_by_position'=>($request->noted_position != 'null') ? $request->noted_position : '',
        ];
        $update_data->update($validated);
    }
}
