<?php

namespace App\Http\Controllers;

use App\Models\BackorderHead;
use App\Models\BackorderDetails;
use App\Models\BackorderItems;
use App\Models\ReceiveHead;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\User;
use App\Models\Items;
use App\Models\Mrec;
use App\Models\PRItems;
use App\Models\ItemStatus;
use App\Models\Variants;
use App\Models\VariantsBalance;
use App\Models\PIVBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Config;

class BackOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get_all_backorder(Request $request){
        // $filter=$request->get('filter');
        // $head = BackorderHead::when($request->get('filter'), function ($query, $filter) {
        //     $query->where('mrecf_no', 'LIKE', '%' . $filter . '%')
        //     //->orWhere('receive_date', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('dr_no', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('po_no', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('si_or', 'LIKE', '%' . $filter . '%');
        // })->where('saved','=','1')->where('draft','=','0')->paginate(10);
        // return response()->json($head);

        $head = BackorderHead::where('saved','=','1')->get();
      
        foreach($head AS $h){
            $allpr='';
            $pr_no = BackorderDetails::select('pr_no')->where('backorder_head_id','=',$h->id)->get();
            
            foreach($pr_no AS $pr){
                $allpr .= $pr->pr_no . ", ";
            }

            $allpr = substr_replace($allpr, '', -2);
            $data[]=[
                'id'=>$h->id,
                'backorder_date'=>$h->backorder_date,
                'mrecf_no'=>$h->mrecf_no,
                'dr_no'=>$h->dr_no,
                'po_no'=>$h->po_no,
                'si_or'=>$h->si_or,
                'pr_no'=>$allpr ,

            ];
        }

            return response()->json($data);
    }

    public function get_backorder_data($id)
    {
        $BOHead = BackorderHead::find($id);

        if(BackorderDetails::where('backorder_head_id','=',$id)->exists()){
            $details = BackorderDetails::where('backorder_head_id','=',$id)->get();
            $details_id = BackorderDetails::where('backorder_head_id',$id)->value('id');
            foreach($details AS $d){
                $items = BackorderItems::where('backorder_head_id', '=', $id)->where('backorder_details_id', '=', $d->id)->get();
                // $counter=ReceiveItems::where('exp_quantity','<=','rec_quantity')->where('receive_details_id', '=', $d->id)->get();
                // $count=$counter->count();
                // if($count != 0){
                        $BackorderDetails[] = [
                            'details_id'=>$d->id,
                            'pr_no'=>$d->pr_no,
                            'department_name'=>$d->department_name,
                            'enduse_name'=>$d->enduse_name,
                            'purpose_name'=>$d->purpose_name,
                            'receive_items'=> [
                                'items'=>$items
                            ]
                        ];
                // }else{
                //     $BackorderDetails=array();
                // }
            }
        } else {
            $BackorderDetails[] = [
                'pr_no'=>'',
                'department_name'=>'',
                'enduse_name'=>'',
                'purpose_name'=>'',
            ];
        }

        //$BackorderDetails = ReceiveDetails::with(['receive_items'])->where('receive_head_id','=',$id)->get();

        return response()->json([
            'BOHead'=>$BOHead,
            'BODetails'=>$BackorderDetails,
          ],200);
    }

    public function get_backorder_head($id){
        $head = BackorderHead::find($id);
        $po_no = BackorderHead::where('id',$id)->value('po_no');
        $drno = ReceiveHead::where('po_no',$po_no)->value('dr_no');
        $pcf = ReceiveHead::where('po_no',$po_no)->value('pcf');
        $user_id=BackorderHead::where('id',$id)->value('user_id');
        $prepared_by=User::where('id',$user_id)->value('name');
        $prepared_position=User::where('id',$user_id)->value('position');
        $received_id=BackorderHead::where('id',$id)->value('received_by');
        $rec_position=User::where('id',$received_id)->value('position');
        $acknowledged_id=BackorderHead::where('id',$id)->value('acknowledged_by');
        $ack_position=User::where('id',$acknowledged_id)->value('position');
        $noted_id=BackorderHead::where('id',$id)->value('noted_by');
        $noted_position=User::where('id',$noted_id)->value('position');
        $pending_items = BackorderItems::where('backorder_head_id',$id)->where('eval_flag', '0')->count();
        return response()->json([
            'head'=>$head,
            'pending_items'=>$pending_items,
            'user_id'=>$user_id,
            'dr_no'=>$drno,
            'pcf'=>$pcf,
            'current_date'=>date("Y-m-d"),
            'current_time'=>date("H:i:s"),
            'current_month'=>date("F"),
            'current_year'=>date("Y"),
            'prepared_by'=>$prepared_by,
            'prepared_position'=>$prepared_position,
            'rec_position'=>$rec_position,
            'ack_position'=>$ack_position,
            'noted_position'=>$noted_position,
            'printed_by_name'=>Auth::user()->name,
        ],200);
    }

    public function get_backorder_details(Request $request, $id){

        $formData=array();
        $detail = BackorderDetails::where('backorder_head_id', '=', $id)->get();
        foreach($detail AS $det){
            $items = BackorderItems::where('backorder_head_id', '=', $id)->where('backorder_details_id', '=', $det->id)->get();
            $count=$items->count();

            $remarks=array();
            foreach($items AS $i){
                $remarks[]=$i->item_description."- ".$i->remarks;
            }
            if($count != 0){
                $formData[]= [
                    'backorder_details_id'=>$det->id,
                    'backorder_head_id'=>$id,
                    'detail_no'=>$det->detail_no,
                    'pr_no'=>$det->pr_no,
                    'department'=>$det->department_name,
                    'inspected_by'=>$det->inspected_name,
                    'enduse'=>$det->enduse_name,
                    'purpose'=>$det->purpose_name,
                    'remarks'=>implode(", ",$remarks),
                    'receive_items'=> [
                        'items'=>$items
                    ]
                ];
            }
        }
         
     
        return response()->json([
            'details'=>$formData,
        ],200);
    }

    public function add_bo_signatory(Request $request){
        $update_data=BackorderHead::where('id',$request->id)->first();
        // if($request->received_by != '0' && $request->acknowledged_by != '0' && $request->noted_by != '0' && $request->delivered_by != '0'){
        $validated=[
            'dr_no'=>$request->r_dr_no,
            'pcf'=>$request->r_pcf,
            'prepared_by'=>$request->user_id,
            'prepared_by_name'=>User::where('id',$request->user_id)->value('name'),
            'prepared_position'=>($request->prepared_position != 'null') ? $request->prepared_position : '',
            'received_by'=>$request->received_by,
            'receive_position'=>($request->rec_position != 'null') ? $request->rec_position : '',
            'received_by_name'=>User::where('id',$request->received_by)->value('name'),
            'acknowledged_by'=>$request->acknowledged_by,
            'acknowledged_by_name'=>User::where('id',$request->acknowledged_by)->value('name'),
            'acknowledged_position'=>($request->ack_position != 'null') ? $request->ack_position : '',
            'noted_by'=>$request->noted_by,
            'noted_name'=>User::where('id',$request->noted_by)->value('name'),
            'noted_position'=>($request->noted_position != 'null') ? $request->noted_position : '',
            'delivered_by'=>($request->delivered_by != 'null') ? $request->delivered_by : '',
        ];
        // }else{
        //     $validated=[
        //         'dr_no'=>$request->r_dr_no,
        //         'pcf'=>$request->r_pcf,
        //     ];
        // }
        
        $update_data->update($validated);
    }

    public function get_receive_data($id)
    {
        // $detquery= BackorderItems::with(['receive_items']);
        //   $detquery->whereHas('receive_items', function ($detquery) use ($id) {
        //         $detquery->where('receive_head_id','=',$id);
        //         $detquery->wherecolumn('exp_quantity','>','rec_quantity');
        //     }); 
        //$details = $detquery->get();
        $BackorderHead=array();
        $BackorderDetails=array();
        $BackorderItems=array();
        $currency=Config::get('constants.currency');
        //if($id != ''){
        $head = ReceiveHead::where('id',$id)->get();
        foreach($head AS $h){
            $BackorderHead = [
                'mrif_no'=>$h->mrecf_no,
                'date'=>date("Y-m-d"),
                'dr_no'=>$h->dr_no,
                'si_no'=>$h->si_or,
                'waybill_no'=>$h->waybill_no,
                'po_no'=>$h->po_no,
                'pcf'=>$h->pcf
            ];
        }
        // } else {
        //     $BackorderHead = [
        //         'mrif_no'=>'',
        //         'date'=>'',
        //         'dr_no'=>'',
        //         'si_no'=>'',
        //         'po_no'=>'',
        //         'pcf'=>''
        //     ];
        // }
        $details= ReceiveItems::with(['receive_details','receive_head'])
            ->where('receive_head_id','=',$id)->where('eval_flag','!=','0')
            ->wherecolumn('exp_quantity','>','rec_quantity')->get();
        $BackorderDetails=[];
        foreach($details AS $d){
            $bo_qty = BackorderItems::where("receive_items_id", "=", $d->id)->value('bo_quantity');
            $total_qty = $bo_qty + $d->rec_quantity;
            // if($d->exp_quantity > $total_qty)
            $BackorderDetails[] = [
                'details_id'=>$d->receive_details->id,
                'pr_no'=>$d->receive_details->pr_no,
                'department_name'=>$d->receive_details->department_name,
                'enduse_name'=>$d->receive_details->enduse_name,
                'purpose_name'=>$d->receive_details->purpose_name,
            ];
        }
        $BackorderDetails = $this->unique_multidim_array($BackorderDetails, "pr_no");
        if($id != 0){
            $query = ReceiveItems::with(['receive_head', 'receive_details'])->where('eval_flag','!=','0')->wherecolumn('exp_quantity','!=','rec_quantity');
            $query->whereHas('receive_head', function ($query) use($id) {
                $query->where('receive_head_id', '=', $id);
                $query->where('closed', '=', '1');
                $query->where('draft', '=', '0');
            });
            $items = $query->get();
            $BackorderItems=[];
            foreach($items AS $i){
                $total_bo = BackorderItems::where('receive_items_id','=',$i->id)->where('eval_flag','!=','0')->sum('bo_quantity');
                $total_qty = $i->rec_quantity + $total_bo;
                $remaining_qty =$i->exp_quantity - $total_qty;
                if(($i->exp_quantity != $total_qty || $i->exp_quantity > $total_qty) && ($i->eval_flag==1 || $i->eval_flag==2)){
                    $BackorderItems[] = [
                        'receive_items_id'=>$i->id,
                        'dets_id'=>$i->receive_details_id,
                        'item_id'=>$i->item_id,
                        'variant_id'=>$i->variant_id,
                        'exp_quantity'=>$i->exp_quantity,
                        'rec_quantity'=>$i->rec_quantity,
                        'bo_quantity'=>$total_bo,
                        'bo_qty'=>$remaining_qty,
                        'total_bo'=>$remaining_qty,
                        'item_description'=>$i->item_description,
                        'supplier_id'=>$i->supplier_id,
                        'supplier_name'=>$i->supplier_name,
                        'uom'=>$i->uom,
                        'catalog_no'=>$i->catalog_no,
                        'brand'=>$i->brand,
                        'serial_no'=>$i->serial_no,
                        'size'=>$i->size,
                        'color'=>$i->color,
                        'item_status'=>$i->item_status,
                        'expiry_date'=>$i->expiry_date,
                        'unit_cost'=>$i->unit_cost,
                        'currency'=>$i->currency,
                        'shipping_cost'=>$i->shipping_cost,
                        'selling_price'=>$i->selling_price,
                        'item_status_id'=>$i->item_status_id,
                        'item_status'=>$i->item_status,
                        'barcode'=>$i->barcode,
                        'location'=>$i->location,
                        'pr_replenish'=>$i->pr_replenish,
                        'pn_no'=>$i->pn_no,
                    ];
                }
            }
        }
        return response()->json([
            'BODetails'=>$BackorderDetails,
            'BOItems'=>$BackorderItems,
            'BOHead'=>$BackorderHead,
            'currency'=>$currency,
        ],200);
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

    public function save_backorder(Request $request){
        $user_id = Auth::id();
        $head_id = $request->input('receive_head_id');
        $r_heads = ReceiveHead::where('id','=',$head_id)->get();
        $curr_year = date('Y');
        $curr_mo = date('m');
        if(Mrec::where('year', '=', $curr_year)->exists()) {
            $mrif = Mrec::where('year', '=', $curr_year)->max('series') + 1;
            $max_value = str_pad($mrif,4,"0",STR_PAD_LEFT);;
        } else {
            $max_value = '0001';
        }
        $mrec_no = 'MRIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
        foreach($r_heads as $h){
            $backorder_head_id = BackorderHead::insertGetId([
                'backorder_date'=>date("Y-m-d"),
                'mrecf_no' => $mrec_no,
                'waybill_no' => $h->waybill_no,
                'po_no' => $h->po_no,
                // 'dr_no' => $h->dr_no,
                'si_or' => $h->si_or,
                // 'pcf' => $h->pcf,
                'user_id' => $user_id,
            ]);
        }
        Mrec::create([
            'year' => date("Y"),
            'series'=>$max_value
        ]);
        $list_details = $request->input('bodet_insert');
        foreach(json_decode($list_details) as $ld){
            $r_details = ReceiveDetails::where('id','=',$ld->details_id)->get();
            foreach($r_details as $d){
                $backorder_details_id = BackorderDetails::insertGetId([
                    'backorder_head_id' => $backorder_head_id,
                    'pr_no' => $d->pr_no,
                    'department_id' => $d->department_id,
                    'department_name' => $d->department_name,
                    'enduse_id' => $d->enduse_id,
                    'enduse_name' => $d->enduse_name,
                    'purpose_id' => $d->purpose_id,
                    'purpose_name' => $d->purpose_name,
                    'inspected_id' => $d->inspected_id,
                    'inspected_name' => $d->inspected_name,
                ]);
            // }
                $list_items = $request->input('boit_insert');
                foreach(json_decode($list_items) as $li){
                    if(empty($li->remarks)){
                        $remarks = '';
                    } else {
                        $remarks=$li->remarks;
                    }
                    if($li->dets_id == $ld->details_id && $li->bo_qty != 0){
                        $bo_items['backorder_head_id'] = $backorder_head_id;
                        $bo_items['backorder_details_id'] = $backorder_details_id;
                        $bo_items['receive_items_id'] = $li->receive_items_id;
                        $bo_items['bo_quantity'] = $li->bo_qty;
                        $bo_items['remarks'] = $remarks;
                        $bo_items['item_description'] = $li->item_description;
                        $bo_items['item_id'] = $li->item_id;
                        $bo_items['pn_no'] = $li->pn_no;
                        $bo_items['variant_id'] = $li->variant_id;
                        $bo_items['supplier_id'] = $li->supplier_id;
                        $bo_items['supplier_name'] = $li->supplier_name;
                        $bo_items['brand'] = $li->brand;
                        $bo_items['catalog_no'] = $li->catalog_no;
                        $bo_items['serial_no'] = $li->serial_no;
                        $bo_items['barcode'] = $li->barcode;
                        $bo_items['location'] = $li->location;
                        $bo_items['size'] = $li->size;
                        $bo_items['color'] = $li->color;
                        $bo_items['uom'] = $li->uom;
                        $bo_items['item_status_id'] = $li->item_status_id;
                        $bo_items['item_status'] = $li->item_status;
                        $bo_items['unit_cost'] = $li->unit_cost;
                        $bo_items['currency'] = $li->currency;
                        $bo_items['shipping_cost'] = $li->shipping_cost;
                        $bo_items['selling_price'] = $li->selling_price;
                        $bo_items['expiry_date'] = $li->expiry_date;
                        $bo_items['pr_replenish'] = $li->pr_replenish;
                        //$it = BackorderItems::create($bo_items);
                        $backorder_items_id = BackorderItems::insertGetId($bo_items);
                        // $mode = ItemStatus::where('id','=',$li->item_status_id)->value('modes');
                        // if($mode == 'add'){
                        //     if(PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->exists()){
                        //         $pritems_id = PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->value('id');
                        //         $update = PRItems::find($pritems_id);
                        //         $update->backorder_qty = $update->backorder_qty + $li->bo_qty;
                        //         $update->balance = $update->balance + $li->bo_qty;
                        //         $update->save();
                        //     } else { 
                        //         $itemdata['pr_no']=$d->pr_no;
                        //         $itemdata['item_id']=$li->item_id;
                        //         $itemdata['backorder_qty']=$li->bo_qty;
                        //         $itemdata['balance']=$li->bo_qty;
                        //         PRItems::create($itemdata);
                        //     }
                        //     $update_balance = Items::find($li->item_id);
                        //     $update_balance->running_balance = $update_balance->running_balance + $li->bo_qty;
                        //     $update_balance->save();
                        // } else if($mode == 'deduct'){
                        //     if(PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->exists()){
                        //         $pritems_id = PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->value('id');
                        //         $update2 = PRItems::find($pritems_id);
                        //         $update2->damage_qty = $update2->damage_qty + $li->bo_qty;
                        //         $update2->save();
                        //     } else { 
                        //         $itemdata['pr_no']=$d->pr_no;
                        //         $itemdata['item_id']=$li->item_id;
                        //         $itemdata2['damage_qty']=$li->bo_qty;
                        //         PRItems::create($itemdata2);
                        //     }
                        // }

                        // $total_cost = $li->unit_cost + $li->shipping_cost;
                        // if(Variants::where('supplier_id','=',$li->supplier_id)
                        // ->where('item_id', '=', $li->item_id)
                        // ->where('brand', '=', $li->brand)
                        // ->where('item_status_id', '=', $li->item_status_id)
                        // ->where('expiration', '=', $li->expiry_date)
                        // ->where('uom', '=', $li->uom)
                        // ->where('color', '=', $li->color)
                        // ->where('size', '=', $li->size)
                        // ->where('average_cost','=',$total_cost)
                        // ->exists()){
                        //     $variant_id = Variants::where('supplier_id','=',$li->supplier_id)
                        //             ->where('item_id', '=', $li->item_id)
                        //             ->where('brand', '=', $li->brand)
                        //             ->where('item_status_id', '=', $li->item_status_id)
                        //             ->where('expiration', '=', $li->expiry_date)
                        //             ->where('uom', '=', $li->uom)
                        //             ->where('color', '=', $li->color)
                        //             ->where('size', '=', $li->size)
                        //             ->where('average_cost','=',$total_cost)
                        //             ->value('id');
                        //     $update_var = Variants::find($variant_id);
                        //     //$ave_cost =  ($update_var->average_cost + $it->unit_cost) / 2;
                        //     $update_var->quantity = $update_var->quantity + $li->bo_qty;
                        //     //$update_var->average_cost = $ave_cost;
                        //     $update_var->save();
                        //     $update_bac = BackorderItems::find($backorder_items_id);
                        //     $update_bac->variant_id = $variant_id;
                        //     $update_bac->save();
                        //     // if($mode == 'add'){
                        //     //     if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                        //     //         $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                        //     //         $update = VariantsBalance::find($varbal_id);
                        //     //         $update->backorder_qty = $update->backorder_qty + $li->bo_qty;
                        //     //         $update->balance = $update->balance + $li->bo_qty;
                        //     //         $update->save();
                        //     //     } else { 
                        //     //         $vardata['variant_id']=$variant_id;
                        //     //         $vardata['item_id']=$li->item_id;
                        //     //         $vardata['backorder_qty']=$li->bo_qty;
                        //     //         $vardata['balance']=$li->bo_qty;
                        //     //         VariantsBalance::create($vardata);
                        //     //     }

                        //     //     if(PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->exists()){
                        //     //         $piv_id = PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->value('id');
                        //     //         $update = PIVBalance::find($piv_id);
                        //     //         $update->quantity = $update->quantity + $li->bo_qty;
                        //     //         $update->save();
                        //     //     } else {
                        //     //         $pivdata['pr_no']=$d->pr_no;
                        //     //         $pivdata['variant_id']=$variant_id;
                        //     //         $pivdata['item_id']=$li->item_id;
                        //     //         $pivdata['quantity']=$li->bo_qty;
                        //     //         PIVBalance::create($pivdata);
                        //     //     }
                        //     // } else {
                        //     //     if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                        //     //         $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                        //     //         $update = VariantsBalance::find($varbal_id);
                        //     //         $update->damage_qty = $update->damage_qty + $li->bo_qty;
                        //     //         $update->save();            
                        //     //     } else { 
                        //     //         $vardata['variant_id']=$variant_id;
                        //     //         $vardata['item_id']=$li->item_id;
                        //     //         $vardata['damage_qty']=$li->bo_qty;
                        //     //         VariantsBalance::create($vardata);
                        //     //     }
                        //     // }
                        // } else {
                        //     $var_data['item_id'] = $li->item_id;
                        //     $var_data['supplier_id'] = $li->supplier_id;
                        //     $var_data['supplier_name'] = $li->supplier_name ;
                        //     $var_data['catalog_no'] = $li->catalog_no;
                        //     $var_data['brand'] = $li->brand;
                        //     $var_data['color'] = $li->color;
                        //     $var_data['size'] = $li->size;
                        //     $var_data['barcode'] = $li->barcode;
                        //     $var_data['expiration'] = $li->expiry_date;
                        //     $var_data['serial_no'] = $li->serial_no;
                        //     $var_data['uom'] = $li->uom;
                        //     $var_data['quantity'] = $li->bo_qty;
                        //     $var_data['average_cost'] = $total_cost;
                        //     $var_data['currency'] = $li->currency;
                        //     $var_data['item_status_id'] = $li->item_status_id;
                        //     //$var_data['receive_flag'] = 1;
                        //     $variant_id = Variants::insertGetId($var_data);
                        //     // if($mode == 'add'){
                        //     //     if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                        //     //         $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                        //     //         $update = VariantsBalance::find($varbal_id);
                        //     //         $update->backorder_qty = $update->backorder_qty + $li->bo_qty;
                        //     //         $update->balance = $update->balance + $li->bo_qty;
                        //     //         $update->save();
                        //     //     } else { 
                        //     //         $vardata['variant_id']=$variant_id;
                        //     //         $vardata['item_id']=$li->item_id;
                        //     //         $vardata['backorder_qty']=$li->bo_qty;
                        //     //         $vardata['balance']=$li->bo_qty;
                        //     //         VariantsBalance::create($vardata);
                        //     //     }

                        //     //     if(PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->exists()){
                        //     //         $piv_id = PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->value('id');
                        //     //         $update = PIVBalance::find($piv_id);
                        //     //         $update->quantity = $update->quantity + $li->bo_qty;
                        //     //         $update->save();
                        //     //     } else {
                        //     //         $pivdata['pr_no']=$d->pr_no;
                        //     //         $pivdata['variant_id']=$variant_id;
                        //     //         $pivdata['item_id']=$li->item_id;
                        //     //         $pivdata['quantity']=$li->bo_qty;
                        //     //         PIVBalance::create($pivdata);
                        //     //     }
                        //     // } else {
                        //     //     if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                        //     //         $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                        //     //         $update = VariantsBalance::find($varbal_id);
                        //     //         $update->damage_qty = $update->damage_qty + $li->bo_qty;
                        //     //         $update->save();
                        //     //     } else { 
                        //     //         $vardata1['variant_id']=$variant_id;
                        //     //         $vardata1['item_id']=$li->item_id;
                        //     //         $vardata1['damage_qty']=$li->bo_qty;
                        //     //         VariantsBalance::create($vardata1);
                        //     //     }
                        //     // }
                        //     // $update_bac = BackorderItems::find($backorder_items_id);
                        //     // $update_bac->variant_id = $variant_id;
                        //     // $update_bac->save();
                        // }
                    }
                }   
            }   
        }
        $head=BackorderHead::where('id',$backorder_head_id)->first();
        $data = [
            'saved'=>'1'
        ];
        $head->update($data);
        return $backorder_head_id;
    }

    public function get_all_backuseracceptance(Request $request){
        $rec_list = BackorderHead::with('backorder_details','backorder_items')->where('saved','=','1')->where('draft','=','0')->get();
        $backorderarray=array();
        foreach($rec_list AS $rc){
            $pending_items = BackorderItems::where('backorder_head_id',$rc->id)->where('eval_flag', '0')->count();
            $po_no = BackorderHead::where('id',$rc->id)->value('po_no');
            $dr_no = ReceiveHead::where('po_no',$po_no)->value('dr_no');
            if($pending_items!=0){
                $backorderarray[]=[
                    'id'=>$rc->id,
                    'backorder_details'=>$rc->backorder_details,
                    'pending_items'=>$pending_items,
                    $rc->mrecf_no,
                    date('F d,Y',strtotime($rc->backorder_date)),
                    ($rc->dr_no!=null) ? $rc->dr_no : $dr_no,
                    $rc->po_no,
                    $rc->si_or,
                    $rc->waybill_no,
                    '',
                    '',
                    ''
                ];
            }
        }
        return response()->json([
            'backorderarray'=>$backorderarray,
        ],200);
    }

    public function save_backaccepted(Request $request){
        if(count(json_decode($request->input("checkbox")))>0){
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!=''){
                    $update_accepted = BackorderItems::where('id',$c)->first();
                    $update_accepted->update([
                        'eval_flag' => "1",
                        'eval_date' => $request->input("eval_date"),
                        'eval_user' => $request->input("eval_user"),
                    ]);
                }
            }
            $backorder_head_id=$request->input('backorder_head_id');
            $backdetails = BackorderDetails::where('backorder_head_id','=',$backorder_head_id)->get();
            foreach($backdetails AS $d){
                $backitems = BackorderItems::where('backorder_details_id','=',$d->id)->where('eval_flag','1')->where('acceptance_done','0')->get();
                foreach($backitems AS $li){
                    if(empty($li->remarks)){
                        $remarks = '';
                    } else {
                        $remarks=$li->remarks;
                    }
                    if($li->bo_quantity != 0){
                        $mode = ItemStatus::where('id','=',$li->item_status_id)->value('modes');
                        if($mode== 'add'){
                            if(PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->exists()){
                                $pritems_id = PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->value('id');
                                $update = PRItems::find($pritems_id);
                                $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                $update->balance = $update->balance + $li->bo_quantity;
                                $update->save();
                            } else { 
                                $itemdata['pr_no']=$d->pr_no;
                                $itemdata['item_id']=$li->item_id;
                                $itemdata['backorder_qty']=$li->bo_quantity;
                                $itemdata['balance']=$li->bo_quantity;
                                PRItems::create($itemdata);
                            }
                            $update_balance = Items::find($li->item_id);
                            $update_balance->running_balance = $update_balance->running_balance + $li->bo_quantity;
                            $update_balance->save();
                        } else if($mode == 'deduct'){
                            if(PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->exists()){
                                $pritems_id = PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->value('id');
                                $update2 = PRItems::find($pritems_id);
                                $update2->damage_qty = $update2->damage_qty + $li->bo_quantity;
                                $update2->save();
                            } else { 
                                $itemdata['pr_no']=$d->pr_no;
                                $itemdata['item_id']=$li->item_id;
                                $itemdata2['damage_qty']=$li->bo_quantity;
                                PRItems::create($itemdata2);
                            }
                        }
        
                        $total_cost = $li->unit_cost + $li->shipping_cost;
                        if(Variants::where('supplier_id','=',$li->supplier_id)
                        ->where('item_id', '=', $li->item_id)
                        ->where('brand', '=', $li->brand)
                        ->where('item_status_id', '=', $li->item_status_id)
                        ->where('expiration', '=', $li->expiry_date)
                        ->where('uom', '=', $li->uom)
                        ->where('color', '=', $li->color)
                        ->where('size', '=', $li->size)
                        ->where('average_cost','=',$total_cost)
                        ->exists()){
                            $update_var = Variants::find($li->variant_id);
                            $update_var->quantity = $update_var->quantity + $li->bo_quantity;
                            $update_var->save();
                            $update_bac = BackorderItems::find($li->id);
                            $update_bac->acceptance_done = '1';
                            $update_bac->variant_id = $li->variant_id;
                            $update_bac->save();
                            if($mode == 'add'){
                                if(VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                    $update->balance = $update->balance + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata['variant_id']=$li->variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['backorder_qty']=$li->bo_quantity;
                                    $vardata['balance']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
        
                                if(PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$li->variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$li->variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $li->bo_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=$d->pr_no;
                                    $pivdata['variant_id']=$li->variant_id;
                                    $pivdata['item_id']=$li->item_id;
                                    $pivdata['quantity']=$li->bo_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            } else {
                                if(VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->damage_qty = $update->damage_qty + $li->bo_quantity;
                                    $update->save();            
                                } else { 
                                    $vardata['variant_id']=$li->variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['damage_qty']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
                            }
                        } else {
                            $var_data['item_id'] = $li->item_id;
                            $var_data['supplier_id'] = $li->supplier_id;
                            $var_data['supplier_name'] = $li->supplier_name;
                            $var_data['catalog_no'] = $li->catalog_no;
                            $var_data['brand'] = $li->brand;
                            $var_data['color'] = $li->color;
                            $var_data['size'] = $li->size;
                            $var_data['barcode'] = $li->barcode;
                            $var_data['expiration'] = $li->expiry_date;
                            $var_data['serial_no'] = $li->serial_no;
                            $var_data['uom'] = $li->uom;
                            $var_data['quantity'] = $li->bo_quantity;
                            $var_data['average_cost'] = $total_cost;
                            $var_data['currency'] = $li->currency;
                            $var_data['item_status_id'] = $li->item_status_id;
                            $variant_id = Variants::insertGetId($var_data);
                            if($mode == 'add'){
                                if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                    $update->balance = $update->balance + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata['variant_id']=$variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['backorder_qty']=$li->bo_quantity;
                                    $vardata['balance']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
        
                                if(PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $li->bo_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=$d->pr_no;
                                    $pivdata['variant_id']=$variant_id;
                                    $pivdata['item_id']=$li->item_id;
                                    $pivdata['quantity']=$li->bo_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            } else {
                                if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->damage_qty = $update->damage_qty + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata1['variant_id']=$variant_id;
                                    $vardata1['item_id']=$li->item_id;
                                    $vardata1['damage_qty']=$li->bo_quantity;
                                    VariantsBalance::create($vardata1);
                                }
                            }
                            $update_bac = BackorderItems::find($li->id);
                            $update_bac->acceptance_done = '1';
                            $update_bac->variant_id = $variant_id;
                            $update_bac->save();
                        } 
                    }
                }   
            }   
        }else{
            return 'error';
        }
    }

    public function save_backrejected(Request $request){
        if(count(json_decode($request->input("checkbox")))>0){
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!='' && (float) $request->input("rejected_qty"."$c")!=0){
                    $update_accepted1 = BackorderItems::where('id',$c)->first();
                    $update_accepted1->update([
                        'eval_flag' => "2",
                        'eval_date' => $request->input("eval_date"),
                        'eval_user' => $request->input("eval_user"),
                        'eval_reason' => $request->input("eval_reason"),
                    ]);  
                }
            }
            $backorder_head_id=$request->input('backorder_head_id');
            $backdetails = BackorderDetails::where('backorder_head_id','=',$backorder_head_id)->get();
            foreach($backdetails AS $d){
                $backitems = BackorderItems::where('backorder_details_id','=',$d->id)->where('eval_flag','2')->where('acceptance_done','0')->get();
                foreach($backitems AS $li){
                    if(empty($li->remarks)){
                        $remarks = '';
                    } else {
                        $remarks=$li->remarks;
                    }
                    if($li->bo_quantity != 0){
                        $mode = ItemStatus::where('id','=',$li->item_status_id)->value('modes');
                        if($mode== 'add'){
                            if(PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->exists()){
                                $pritems_id = PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->value('id');
                                $update = PRItems::find($pritems_id);
                                $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                $update->balance = $update->balance + $li->bo_quantity;
                                $update->save();
                            } else { 
                                $itemdata['pr_no']=$d->pr_no;
                                $itemdata['item_id']=$li->item_id;
                                $itemdata['backorder_qty']=$li->bo_quantity;
                                $itemdata['balance']=$li->bo_quantity;
                                PRItems::create($itemdata);
                            }
                            $update_balance = Items::find($li->item_id);
                            $update_balance->running_balance = $update_balance->running_balance + $li->bo_quantity;
                            $update_balance->save();
                        } else if($mode == 'deduct'){
                            if(PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->exists()){
                                $pritems_id = PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->value('id');
                                $update2 = PRItems::find($pritems_id);
                                $update2->damage_qty = $update2->damage_qty + $li->bo_quantity;
                                $update2->save();
                            } else { 
                                $itemdata['pr_no']=$d->pr_no;
                                $itemdata['item_id']=$li->item_id;
                                $itemdata2['damage_qty']=$li->bo_quantity;
                                PRItems::create($itemdata2);
                            }
                        }
        
                        $total_cost = $li->unit_cost + $li->shipping_cost;
                        if(Variants::where('supplier_id','=',$li->supplier_id)
                        ->where('item_id', '=', $li->item_id)
                        ->where('brand', '=', $li->brand)
                        ->where('item_status_id', '=', $li->item_status_id)
                        ->where('expiration', '=', $li->expiry_date)
                        ->where('uom', '=', $li->uom)
                        ->where('color', '=', $li->color)
                        ->where('size', '=', $li->size)
                        ->where('average_cost','=',$total_cost)
                        ->exists()){
                            $update_var = Variants::find($li->variant_id);
                            $update_var->quantity = $update_var->quantity + $li->bo_quantity;
                            $update_var->save();
                            $update_bac = BackorderItems::find($li->id);
                            $update_bac->acceptance_done = '1';
                            $update_bac->variant_id = $li->variant_id;
                            $update_bac->save();
                            if($mode == 'add'){
                                if(VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                    $update->balance = $update->balance + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata['variant_id']=$li->variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['backorder_qty']=$li->bo_quantity;
                                    $vardata['balance']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
        
                                if(PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$li->variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$li->variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $li->bo_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=$d->pr_no;
                                    $pivdata['variant_id']=$li->variant_id;
                                    $pivdata['item_id']=$li->item_id;
                                    $pivdata['quantity']=$li->bo_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            } else {
                                if(VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->damage_qty = $update->damage_qty + $li->bo_quantity;
                                    $update->save();            
                                } else { 
                                    $vardata['variant_id']=$li->variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['damage_qty']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
                            }
                        } else {
                            $var_data['item_id'] = $li->item_id;
                            $var_data['supplier_id'] = $li->supplier_id;
                            $var_data['supplier_name'] = $li->supplier_name;
                            $var_data['catalog_no'] = $li->catalog_no;
                            $var_data['brand'] = $li->brand;
                            $var_data['color'] = $li->color;
                            $var_data['size'] = $li->size;
                            $var_data['barcode'] = $li->barcode;
                            $var_data['expiration'] = $li->expiry_date;
                            $var_data['serial_no'] = $li->serial_no;
                            $var_data['uom'] = $li->uom;
                            $var_data['quantity'] = $li->bo_quantity;
                            $var_data['shipping_cost'] = $li->shipping_cost;
                            $var_data['unit_cost'] = $li->unit_cost;
                            $var_data['average_cost'] = $total_cost;
                            $var_data['currency'] = $li->currency;
                            $var_data['item_status_id'] = $li->item_status_id;
                            $variant_id = Variants::insertGetId($var_data);
                            if($mode == 'add'){
                                if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                    $update->balance = $update->balance + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata['variant_id']=$variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['backorder_qty']=$li->bo_quantity;
                                    $vardata['balance']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
        
                                if(PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $li->bo_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=$d->pr_no;
                                    $pivdata['variant_id']=$variant_id;
                                    $pivdata['item_id']=$li->item_id;
                                    $pivdata['quantity']=$li->bo_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            } else {
                                if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->damage_qty = $update->damage_qty + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata1['variant_id']=$variant_id;
                                    $vardata1['item_id']=$li->item_id;
                                    $vardata1['damage_qty']=$li->bo_quantity;
                                    VariantsBalance::create($vardata1);
                                }
                            }
                            $update_bac = BackorderItems::find($li->id);
                            $update_bac->acceptance_done = '1';
                            $update_bac->variant_id = $variant_id;
                            $update_bac->save();
                        } 
                    }
                }   
            }
            $x=0;
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!='' && (float) $request->input("rejected_qty"."$c")!=0){
                    $item_id = BackorderItems::where('id',$c)->value('item_id');
                    $variant_id = BackorderItems::where('id',$c)->value('variant_id');
                    $backorder_details_id = BackorderItems::where('id',$c)->value('backorder_details_id');
                    $pr_no = BackorderDetails::where('id',$backorder_details_id)->value('pr_no');
                    $update_accepted = BackorderItems::where('id',$c)->first();
                    $bo_qty = BackorderItems::where('id',$c)->value('bo_quantity');
                    $rejected_qty = BackorderItems::where('id',$c)->value('rejected_qty');
                    $update_accepted->update([
                        // 'eval_flag' => "2",
                        // 'eval_date' => $request->input("eval_date"),
                        // 'eval_user' => $request->input("eval_user"),
                        'rejected_qty' => (float) $rejected_qty + (float) $request->input("rejected_qty"."$c"),
                        'bo_quantity' => (float) $bo_qty - (float) $request->input("rejected_qty"."$c"),
                        // 'eval_reason' => $request->input("eval_reason"),
                    ]);  
                    
                    $update_items = Items::where('id',$item_id)->first();
                    $running_balance=$update_items->running_balance;
                    $update_items->update([
                        'running_balance' => (float) $running_balance - (float) $request->input("rejected_qty"."$c"),
                    ]);  

                    if($variant_id!=0){
                        $variants = Variants::where('id',$variant_id)->where('item_id',$item_id)->first();
                        $variant_qty=$variants->quantity;
                        $variants->update([
                            'quantity' => (float) $variant_qty - (float) $request->input("rejected_qty"."$c"),
                        ]);  
                    }

                    $pritems = PRItems::where('pr_no',$pr_no)->where('item_id',$item_id)->first();
                    $pr_qty=$pritems->balance;
                    $pr_reject_qty=$pritems->rejected_qty;
                    $pritems->update([
                        'balance' => (float) $pr_qty - (float) $request->input("rejected_qty"."$c"),
                        'rejected_qty' => (float) $pr_reject_qty + (float) $request->input("rejected_qty"."$c"),
                    ]);
                    
                    $varbalance = VariantsBalance::where('variant_id',$variant_id)->where('item_id',$item_id)->first();
                    $varbal_qty=$varbalance->balance;
                    $varbal_reject_qty=$varbalance->rejected_qty;
                    $varbalance->update([
                        'balance' => (float) $varbal_qty - (float) $request->input("rejected_qty"."$c"),
                        'rejected_qty' => (float) $varbal_reject_qty + (float) $request->input("rejected_qty"."$c"),
                    ]);

                    $pivbalance = PIVBalance::where('pr_no',$pr_no)->where('variant_id',$variant_id)->where('item_id',$item_id)->first();
                    $piv_qty=$pivbalance->quantity;
                    $piv_reject_qty=$pivbalance->rejected_qty;
                    $pivbalance->update([
                        'quantity' => (float) $piv_qty - (float) $request->input("rejected_qty"."$c"),
                        'rejected_qty' => (float) $piv_reject_qty + (float) $request->input("rejected_qty"."$c"),
                    ]);
                }
                $x++; 
            }
        }else{
            return 'error';
        }
    }

    public function get_all_useracceptance_completed(Request $request){
        $rec_list = BackorderHead::with('backorder_details','backorder_items')->where('saved','=','1')->where('draft','=','0')->get();
        $backorderarray=array();
        foreach($rec_list AS $rc){
            $pending_items = BackorderItems::where('backorder_head_id',$rc->id)->where('eval_flag', '0')->count();
            $po_no = BackorderHead::where('id',$rc->id)->value('po_no');
            $dr_no = ReceiveHead::where('po_no',$po_no)->value('dr_no');
            if($pending_items==0){
                $backorderarray[]=[
                    'id'=>$rc->id,
                    'backorder_details'=>$rc->backorder_details,
                    'pending_items'=>$pending_items,
                    $rc->mrecf_no,
                    date('F d,Y',strtotime($rc->backorder_date)),
                    ($rc->dr_no!=null) ? $rc->dr_no : $dr_no,
                    $rc->po_no,
                    $rc->si_or,
                    $rc->waybill_no,
                    '',
                    ''
                ];
            }
        }
        return response()->json([
            'backorderarray'=>$backorderarray,
        ],200);
    }

    public function save_newbackaccepted(Request $request){
        if(count(json_decode($request->input("checkbox")))>0){
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!=''){
                    $update_accepted = BackorderItems::where('id',$c)->first();
                    $update_accepted->update([
                        'eval_flag' => "1",
                        'eval_date' => $request->input("eval_date"),
                        'eval_user' => $request->input("eval_user"),
                    ]);
                }

                if($c!='' && (float) $request->input("rejected_qty"."$c")!=0){
                    $update_accepted1 = BackorderItems::where('id',$c)->first();
                    $update_accepted1->update([
                        'eval_flag' => "2",
                        'eval_date' => $request->input("eval_date"),
                        'eval_user' => $request->input("eval_user"),
                        'eval_reason' => $request->input("rejected_remarks"."$c"),
                    ]);  
                }
            }
            $backorder_head_id=$request->input('backorder_head_id');
            $backdetails = BackorderDetails::where('backorder_head_id','=',$backorder_head_id)->get();
            foreach($backdetails AS $d){
                $backitems = BackorderItems::where('backorder_details_id','=',$d->id)->where('eval_flag','!=','0')->where('acceptance_done','0')->get();
                foreach($backitems AS $li){
                    if(empty($li->remarks)){
                        $remarks = '';
                    } else {
                        $remarks=$li->remarks;
                    }
                    if($li->bo_quantity != 0){
                        $mode = ItemStatus::where('id','=',$li->item_status_id)->value('modes');
                        if($mode== 'add'){
                            if(PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->exists()){
                                $pritems_id = PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->value('id');
                                $update = PRItems::find($pritems_id);
                                $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                $update->balance = $update->balance + $li->bo_quantity;
                                $update->save();
                            } else { 
                                $itemdata['pr_no']=$d->pr_no;
                                $itemdata['item_id']=$li->item_id;
                                $itemdata['backorder_qty']=$li->bo_quantity;
                                $itemdata['balance']=$li->bo_quantity;
                                PRItems::create($itemdata);
                            }
                            $update_balance = Items::find($li->item_id);
                            $update_balance->running_balance = $update_balance->running_balance + $li->bo_quantity;
                            $update_balance->save();
                        } else if($mode == 'deduct'){
                            if(PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->exists()){
                                $pritems_id = PRItems::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->value('id');
                                $update2 = PRItems::find($pritems_id);
                                $update2->damage_qty = $update2->damage_qty + $li->bo_quantity;
                                $update2->save();
                            } else { 
                                $itemdata['pr_no']=$d->pr_no;
                                $itemdata['item_id']=$li->item_id;
                                $itemdata2['damage_qty']=$li->bo_quantity;
                                PRItems::create($itemdata2);
                            }
                        }
        
                        $total_cost = $li->unit_cost + $li->shipping_cost;
                        if(Variants::where('supplier_id','=',$li->supplier_id)
                        ->where('item_id', '=', $li->item_id)
                        ->where('brand', '=', $li->brand)
                        ->where('item_status_id', '=', $li->item_status_id)
                        ->where('expiration', '=', $li->expiry_date)
                        ->where('uom', '=', $li->uom)
                        ->where('color', '=', $li->color)
                        ->where('size', '=', $li->size)
                        ->where('average_cost','=',$total_cost)
                        ->exists()){
                            $update_var = Variants::find($li->variant_id);
                            $update_var->quantity = $update_var->quantity + $li->bo_quantity;
                            $update_var->save();
                            $update_bac = BackorderItems::find($li->id);
                            $update_bac->acceptance_done = '1';
                            $update_bac->variant_id = $li->variant_id;
                            $update_bac->save();
                            if($mode == 'add'){
                                if(VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                    $update->balance = $update->balance + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata['variant_id']=$li->variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['backorder_qty']=$li->bo_quantity;
                                    $vardata['balance']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
        
                                if(PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$li->variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$li->variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $li->bo_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=$d->pr_no;
                                    $pivdata['variant_id']=$li->variant_id;
                                    $pivdata['item_id']=$li->item_id;
                                    $pivdata['quantity']=$li->bo_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            } else {
                                if(VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$li->variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->damage_qty = $update->damage_qty + $li->bo_quantity;
                                    $update->save();            
                                } else { 
                                    $vardata['variant_id']=$li->variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['damage_qty']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
                            }
                        } else {
                            $var_data['item_id'] = $li->item_id;
                            $var_data['supplier_id'] = $li->supplier_id;
                            $var_data['supplier_name'] = $li->supplier_name;
                            $var_data['catalog_no'] = $li->catalog_no;
                            $var_data['brand'] = $li->brand;
                            $var_data['color'] = $li->color;
                            $var_data['size'] = $li->size;
                            $var_data['barcode'] = $li->barcode;
                            $var_data['expiration'] = $li->expiry_date;
                            $var_data['serial_no'] = $li->serial_no;
                            $var_data['uom'] = $li->uom;
                            $var_data['quantity'] = $li->bo_quantity;
                            $var_data['average_cost'] = $total_cost;
                            $var_data['currency'] = $li->currency;
                            $var_data['item_status_id'] = $li->item_status_id;
                            $variant_id = Variants::insertGetId($var_data);
                            if($mode == 'add'){
                                if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->backorder_qty = $update->backorder_qty + $li->bo_quantity;
                                    $update->balance = $update->balance + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata['variant_id']=$variant_id;
                                    $vardata['item_id']=$li->item_id;
                                    $vardata['backorder_qty']=$li->bo_quantity;
                                    $vardata['balance']=$li->bo_quantity;
                                    VariantsBalance::create($vardata);
                                }
        
                                if(PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$d->pr_no)->where('item_id', '=', $li->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $li->bo_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=$d->pr_no;
                                    $pivdata['variant_id']=$variant_id;
                                    $pivdata['item_id']=$li->item_id;
                                    $pivdata['quantity']=$li->bo_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            } else {
                                if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->exists()){
                                    $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $li->item_id)->value('id');
                                    $update = VariantsBalance::find($varbal_id);
                                    $update->damage_qty = $update->damage_qty + $li->bo_quantity;
                                    $update->save();
                                } else { 
                                    $vardata1['variant_id']=$variant_id;
                                    $vardata1['item_id']=$li->item_id;
                                    $vardata1['damage_qty']=$li->bo_quantity;
                                    VariantsBalance::create($vardata1);
                                }
                            }
                            $update_bac = BackorderItems::find($li->id);
                            $update_bac->acceptance_done = '1';
                            $update_bac->variant_id = $variant_id;
                            $update_bac->save();
                        } 
                    }
                }   
            }   
            $x=0;
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!='' && (float) $request->input("rejected_qty"."$c")!=0){
                    $item_id = BackorderItems::where('id',$c)->value('item_id');
                    $variant_id = BackorderItems::where('id',$c)->value('variant_id');
                    $backorder_details_id = BackorderItems::where('id',$c)->value('backorder_details_id');
                    $pr_no = BackorderDetails::where('id',$backorder_details_id)->value('pr_no');
                    $update_accepted = BackorderItems::where('id',$c)->first();
                    $bo_qty = BackorderItems::where('id',$c)->value('bo_quantity');
                    $rejected_qty = BackorderItems::where('id',$c)->value('rejected_qty');
                    $update_accepted->update([
                        'rejected_qty' => (float) $rejected_qty + (float) $request->input("rejected_qty"."$c"),
                        'bo_quantity' => (float) $bo_qty - (float) $request->input("rejected_qty"."$c"),
                    ]);  
                    
                    $update_items = Items::where('id',$item_id)->first();
                    $running_balance=$update_items->running_balance;
                    $update_items->update([
                        'running_balance' => (float) $running_balance - (float) $request->input("rejected_qty"."$c"),
                    ]);  

                    if($variant_id!=0){
                        $variants = Variants::where('id',$variant_id)->where('item_id',$item_id)->first();
                        $variant_qty=$variants->quantity;
                        $variants->update([
                            'quantity' => (float) $variant_qty - (float) $request->input("rejected_qty"."$c"),
                        ]);  
                    }

                    $pritems = PRItems::where('pr_no',$pr_no)->where('item_id',$item_id)->first();
                    $pr_qty=$pritems->balance;
                    $pr_reject_qty=$pritems->rejected_qty;
                    $pritems->update([
                        'balance' => (float) $pr_qty - (float) $request->input("rejected_qty"."$c"),
                        'rejected_qty' => (float) $pr_reject_qty + (float) $request->input("rejected_qty"."$c"),
                    ]);
                    
                    $varbalance = VariantsBalance::where('variant_id',$variant_id)->where('item_id',$item_id)->first();
                    $varbal_qty=$varbalance->balance;
                    $varbal_reject_qty=$varbalance->rejected_qty;
                    $varbalance->update([
                        'balance' => (float) $varbal_qty - (float) $request->input("rejected_qty"."$c"),
                        'rejected_qty' => (float) $varbal_reject_qty + (float) $request->input("rejected_qty"."$c"),
                    ]);

                    $pivbalance = PIVBalance::where('pr_no',$pr_no)->where('variant_id',$variant_id)->where('item_id',$item_id)->first();
                    $piv_qty=$pivbalance->quantity;
                    $piv_reject_qty=$pivbalance->rejected_qty;
                    $pivbalance->update([
                        'quantity' => (float) $piv_qty - (float) $request->input("rejected_qty"."$c"),
                        'rejected_qty' => (float) $piv_reject_qty + (float) $request->input("rejected_qty"."$c"),
                    ]);
                }
                $x++; 
            }
        }else{
            return 'error';
        }
    }
}
