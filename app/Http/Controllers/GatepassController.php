<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GatepassSeries;
use App\Models\GatepassHead;
use App\Models\GatepassItems;
use App\Models\GatepassReturnedHistory;
use Illuminate\Support\Facades\Auth;

class GatepassController extends Controller
{   
    public function get_all_gatepass(Request $request){
            //     $filter=$request->get('filter');
            //     $all_gp = GatepassHead::when($request->get('filter'), function ($query, $filter) {
            //         $query->where('gatepass_no', 'LIKE', '%' . $filter . '%')
            //         ->orWhere('to_company', 'LIKE', '%' . $filter . '%')
            //         ->orWhere('destination', 'LIKE', '%' . $filter . '%')
            //         ->orWhere('vehicle_no', 'LIKE', '%' . $filter . '%')
            //         ->orWhere('date_issued', 'LIKE', '%' . $filter . '%')
            //         ->orWhere('status', 'LIKE', '%' . $filter . '%')
            //         ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
            //     })->paginate(10);
            //     return response()->json($all_gp);

        // $all_gatepass = GatepassItems::with('gatepass_head')->get();

        // $filter=$request->get('filter');
        // $query= GatepassItems::with('gatepass_head')->where(function ($query) use ($filter) {
        //     $query->where('item_description', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('uom', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('quantity', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('remarks', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('type', 'LIKE', '%' . $filter . '%')->paginate(10);
        // })->whereHas('gatepass_head', function ($query) use ($filter){
        // // ->where(function ($query) use ($filter) {
        //     $query->where('date_issued', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('gatepass_no', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('destination', 'LIKE', '%' . $filter . '%')
        //     ->orWhere('status', 'LIKE', '%' . $filter . '%')
        //     ->orderBy('date_issued','DESC')
        //     ->orderBy('gatepass_no','DESC');
        // });
        // $all_gatepass= $query->paginate(10);

        $all_head_dets = GatepassHead::orderBy('date_issued','DESC')->orderBy('gatepass_no','DESC')->get();
        $allgatepass = array();
        foreach($all_head_dets AS $ahd){
                $item_id=GatepassItems::where('gatepass_head_id',$ahd->id)->value('id');
                // $item_description=GatepassItems::where('gatepass_head_id',$ahd->id)->value('item_description');
                // $uom=GatepassItems::where('gatepass_head_id',$ahd->id)->value('uom');
                // $quantity=GatepassItems::where('gatepass_head_id',$ahd->id)->value('quantity');
                // $type=GatepassItems::where('gatepass_head_id',$ahd->id)->value('type');
                // $total_returned_qty = GatepassReturnedHistory::where('gatepass_item_id','=',$item_id)->where('gatepass_head_id','=',$ahd->id)->sum('returned_qty');
                $returned_history=GatepassReturnedHistory::where('gatepass_head_id',$ahd->id)->get();
                $count_history=$returned_history->count();
                $returnable_items=GatepassItems::where('gatepass_head_id',$ahd->id)->where('type','=','Returnable')->get();
                $count_returnable=$returnable_items->count();

                // $all_items=GatepassItems::where('id','=',$item_id)->where('gatepass_head_id',$ahd->id)->where('type','=','Returnable')->get();
               
                    $item_desc=array();
                    $uom=array();
                    $quantity=array();
                    $type=array();
                    foreach($ahd->gatepass_items AS $ait){
                        $item_desc[] = $ait->item_description;
                        $uom[] = $ait->uom;
                        $quantity[] = $ait->quantity;
                        $type[] = $ait->type;
                    }


                $allgatepass[]=[
                    'head_id'=>$ahd->id,
                    'date_issued'=>$ahd->date_issued,
                    'gatepass_no'=>$ahd->gatepass_no,
                    'destination'=>$ahd->destination,
                    'status'=>$ahd->status,
                    'saved'=>$ahd->saved,
                    'remarks'=>$ahd->remarks,
                    'item_id'=>$item_id,
                    'item_desc'=>implode(", ",$item_desc),
                    'uom'=>implode(", ",$uom),
                    'quantity'=>implode(", ",$quantity),
                    'type'=>implode(", ",$type),
                    'count_hist'=>$count_history,
                    'count_ret'=>$count_returnable,
                    // 'returned_qty'=>$total_returned_qty,
                ];
        }
            // return response()->json([
            //     'allgatepass'=>$allgatepass,
            //     'all_items'=>$all_items,
            // ],200);
            return response()->json($allgatepass);
    }

    public function get_completed_gatepass(Request $request){
        $completed_head_dets = GatepassHead::where('status','Completed')->orderBy('date_issued','DESC')->orderBy('gatepass_no','DESC')->get();
        $completedgatepass = array();
        foreach($completed_head_dets AS $chd){
                $item_id=GatepassItems::where('gatepass_head_id',$chd->id)->value('id');
                // $item_description=GatepassItems::where('gatepass_head_id',$chd->id)->value('item_description');
                // $uom=GatepassItems::where('gatepass_head_id',$chd->id)->value('uom');
                // $quantity=GatepassItems::where('gatepass_head_id',$chd->id)->value('quantity');
                // $type=GatepassItems::where('gatepass_head_id',$chd->id)->value('type');
                // $total_returned_qty = GatepassReturnedHistory::where('gatepass_item_id','=',$item_id)->where('gatepass_head_id','=',$chd->id)->sum('returned_qty');
                $returned_history=GatepassReturnedHistory::where('gatepass_head_id',$chd->id)->get();
                $count_history=$returned_history->count();
                $returnable_items=GatepassItems::where('gatepass_head_id',$chd->id)->where('type','=','Returnable')->get();
                $count_returnable=$returnable_items->count();

                    $item_desc=array();
                    $uom=array();
                    $quantity=array();
                    $type=array();
                    foreach($chd->gatepass_items AS $ait){
                        $item_desc[] = $ait->item_description;
                        $uom[] = $ait->uom;
                        $quantity[] = $ait->quantity;
                        $type[] = $ait->type;
                    }

                $completedgatepass[]=[
                    'head_id'=>$chd->id,
                    'date_issued'=>$chd->date_issued,
                    'gatepass_no'=>$chd->gatepass_no,
                    'destination'=>$chd->destination,
                    'status'=>$chd->status,
                    'saved'=>$chd->saved,
                    'remarks'=>$chd->remarks,
                    'item_id'=>$item_id,
                    'item_desc'=>implode(", ",$item_desc),
                    'uom'=>implode(", ",$uom),
                    'quantity'=>implode(", ",$quantity),
                    'type'=>implode(", ",$type),
                    'count_hist'=>$count_history,
                    'count_ret'=>$count_returnable,
                    // 'returned_qty'=>$total_returned_qty,
                ];
        }
            // return response()->json([
            //     'allgatepass'=>$allgatepass,
            // ],200);
            return response()->json($completedgatepass);
    }

    public function get_incomplete_gatepass(Request $request){
        $incomplete_head_dets = GatepassHead::where('status','Incomplete')->orderBy('date_issued','DESC')->orderBy('gatepass_no','DESC')->get();
        $incompletegatepass = array();
        foreach($incomplete_head_dets AS $ihd){
                $item_id=GatepassItems::where('gatepass_head_id',$ihd->id)->value('id');
                // $item_description=GatepassItems::where('gatepass_head_id',$ihd->id)->value('item_description');
                // $uom=GatepassItems::where('gatepass_head_id',$ihd->id)->value('uom');
                // $quantity=GatepassItems::where('gatepass_head_id',$ihd->id)->value('quantity');
                // $type=GatepassItems::where('gatepass_head_id',$ihd->id)->value('type');
                // $total_returned_qty = GatepassReturnedHistory::where('gatepass_item_id','=',$item_id)->where('gatepass_head_id','=',$ihd->id)->sum('returned_qty');
                $returned_history=GatepassReturnedHistory::where('gatepass_head_id',$ihd->id)->get();
                $count_history=$returned_history->count();
                $returnable_items=GatepassItems::where('gatepass_head_id',$ihd->id)->where('type','=','Returnable')->get();
                $count_returnable=$returnable_items->count();
                // $all_items=GatepassItems::where('id','=',$item_id)->where('gatepass_head_id',$ihd->id)->where('type','=','Returnable')->get();

                    $item_desc=array();
                    $uom=array();
                    $quantity=array();
                    $type=array();
                    foreach($ihd->gatepass_items AS $ait){
                        $item_desc[] = $ait->item_description;
                        $uom[] = $ait->uom;
                        $quantity[] = $ait->quantity;
                        $type[] = $ait->type;
                    }

                    $returned_qty_list = GatepassReturnedHistory::where('gatepass_head_id','=',$ihd->id)->orderBy('gatepass_item_id','ASC')->get()->unique('gatepass_item_id');;
                    $returned_qty=array();
                    foreach($returned_qty_list AS $rql){
                        $total_returned_qty = GatepassReturnedHistory::where('gatepass_item_id','=',$rql->gatepass_item_id)->where('gatepass_head_id','=',$rql->gatepass_head_id)->sum('returned_qty');
                        $returned_qty[] = $total_returned_qty;
                    }

                $incompletegatepass[]=[
                    'head_id'=>$ihd->id,
                    'date_issued'=>$ihd->date_issued,
                    'gatepass_no'=>$ihd->gatepass_no,
                    'destination'=>$ihd->destination,
                    'status'=>$ihd->status,
                    'saved'=>$ihd->saved,
                    'remarks'=>$ihd->remarks,
                    'item_id'=>$item_id,
                    'item_desc'=>implode(", ",$item_desc),
                    'uom'=>implode(", ",$uom),
                    'quantity'=>implode(", ",$quantity),
                    'type'=>implode(", ",$type),
                    'count_hist'=>$count_history,
                    'count_ret'=>$count_returnable,
                    'returned_qty'=>implode(", ",$returned_qty),
                ];
        }
            // return response()->json([
            //     'incompletegatepass'=>$incompletegatepass,
            //     'all_items'=>$all_items,
            // ],200);
            return response()->json($incompletegatepass);
    }

    public function create_gatepass_head(Request $request){
        $curr_year = date('Y');
        $curr_mo = date('m');
        if(GatepassSeries::where('year', '=', $curr_year)->exists()) {
            $mgp = GatepassSeries::where('year', '=', $curr_year)->max('series') + 1;
            $max_value = str_pad($mgp,4,"0",STR_PAD_LEFT);;
        } else {
            $max_value = '0001';
        }

        $gatepass_no = 'MGP-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
        $prepared_by = User::all();
        $noted_by = User::where('noted_flag','=','1')->get();
        $approved_by = User::where('approved_flag','=','1')->get();


        $id = Auth::id();
        $formData=[
            'gatepass_no'=>$gatepass_no,
            'to_company'=>'',
            'destination'=>'',
            'vehicle_no'=>'',
            'issue_date'=>date("Y-m-d"),
            'remarks'=>'',
            'user_id'=>$id,
            'prepared_by_id'=>'',
            'noted_by_id'=>'',
            'approved_by_id'=>'',
        ];
        // return response()->json($formData);
        return response()->json([
            'formData'=>$formData,
            'prepared_by'=>$prepared_by,
            'noted_by'=>$noted_by,
            'approved_by'=>$approved_by,
        ],200);
    }

    public function add_gatepass_head(Request $request){
        if($request->input('prepared_by_id')){
                $pb = explode("_",$request->input('prepared_by_id'));
                $prepared_by_id = $pb[0];
                $prepared_by_name = $pb[1];
                $prepared_by_position = $pb[2];
        } else {
            $prepared_by_id = 0;
            $prepared_by_name = NULL;
            $prepared_by_position = NULL;
        }

        if($request->input('noted_by_id')){
            $nb = explode("_",$request->input('noted_by_id'));
            $noted_by_id = $nb[0];
            $noted_by_name = $nb[1];
            $noted_by_position = $nb[2];
        } else {
            $noted_by_id = 0;
            $noted_by_name = NULL;
            $noted_by_position = NULL;
        }

        if($request->input('approved_by_id')){
            $ab = explode("_",$request->input('approved_by_id'));
            $approved_by_id = $ab[0];
            $approved_by_name = $ab[1];
            $approved_by_position = $ab[2];
        } else {
            $approved_by_id = 0;
            $approved_by_name = NULL;
            $approved_by_position = NULL;
        }

        $validated=[
            'gatepass_no'=>$request->gatepass_no,
            'to_company'=>$request->to_company,
            'date_issued'=>$request->issue_date,
            'destination'=>$request->destination,
            'vehicle_no'=>$request->vehicle_no,
            'prepared_by'=>$prepared_by_id,
            'prepared_by_name'=>$prepared_by_name,
            'prepared_by_position'=>$prepared_by_position,
            'noted_by'=>$noted_by_id,
            'noted_by_name'=>$noted_by_name,
            'noted_by_position'=>$noted_by_position,
            'approved_by'=>$approved_by_id,
            'approved_by_name'=>$approved_by_name,
            'approved_by_position'=>$approved_by_position,
            'status'=>'Incomplete',
            'remarks'=>$request->remarks,
            'user_id'=>$request->user_id,
        ];
        $gatepass_id=GatepassHead::insertGetId($validated);

        $gatepass = $request->gatepass_no;
        $ser = explode("-",$gatepass);
        $series = $ser[3];

        GatepassSeries::create([
            'year' => date("Y"),
            'series'=>$series
        ]);

        return $gatepass_id;
    }

    public function gatepass_details(Request $request, $id){
        $gatepass_head = GatepassHead::find($id);
        $gatepass_items = GatepassItems::where('gatepass_head_id',$id)->get();
        // $received_id=GatepassHead::where('id',$id)->value('received_by');
        // $rec_position=User::where('id',$received_id)->value('position');
        // return response()->json($gatepass_head);
        $display_image = GatepassItems::where('gatepass_head_id',$id)->where('display_flag','1')->value('image');
        return response()->json([
            'gp_head'=>$gatepass_head,
            'gp_items'=>$gatepass_items,
            'display_image'=>$display_image,
            // 'rec_position'=>$rec_position,
            'printed_by_name'=>Auth::user()->name,
        ],200);
    }

    public function insert_item(Request $request){
        $validated['gatepass_head_id']=$request->head_id;
        $validated['item_description']=$request->item_description;
        $validated['quantity']=($request->quantity) ? $request->quantity : 0;
        $validated['uom']=$request->uom;
        $validated['type']=$request->type;
        $validated['remarks']=$request->remarks;
        $validated['display_flag']=($request->display_flag) ? $request->display_flag : 0;

        if($request->file('imagename')){
            // $imagename=$request->file('imagename')->getClientOriginalName();
            $imagename =  $request->head_id."_".$request->item_description.".".$request->file('imagename')->getClientOriginalExtension();
            $request->file('imagename')->storeAs('gatepass_items',$imagename);
            $validated['image']=$imagename;
        }
       
        $gatepass_item_id=GatepassItems::insertGetId($validated);
        // return response()->json($gatepass_item_id);
    }

    
    public function remove_item($id){
        $items=GatepassItems::where('id', '=', $id);
        $items->delete();
    }

    public function save_gatepass_items(Request $request,  $id){
        // $list_items = $request->input('gatepass_items');

        // foreach(json_decode($list_items) as $li){
            // $gatepass_items['gatepass_head_id'] = $id;
            // $gatepass_items['item_description'] = $li->item_desc;
            // $gatepass_items['quantity'] = $li->qty;
            // $gatepass_items['uom'] = $li->uom;
            // $gatepass_items['type'] = $li->type;
            // $gatepass_items['remarks'] = $li->remarks;
            // if($li->file('imageFilename')){
            //     $imagename=$li->file('imageFilename')->getClientOriginalName();
            //     $li->file('imageFilename')->storeAs('images',$imagename);
            //     $gatepass_items['image']=$imagename;
            // }

            // if($li->display_flag == 'Yes'){
            //     $gatepass_items['display_flag'] = 1;
            // }else{
            //     $gatepass_items['display_flag'] = 0;
            // }
            // $gp_items = GatepassItems::create($gatepass_items);
            // if($li->type == 'Non-returnable'){

            $returnableitems=GatepassItems::where('gatepass_head_id',$id)->where('type','=','Returnable')->get();
            $count_returnable_items=$returnableitems->count();
            if($count_returnable_items == 0){
                $head=GatepassHead::where('id',$id)->first();
                $data = [
                    'status'=>'Completed'
                ];
                $head->update($data);
            }
        // }
        $head=GatepassHead::where('id',$id)->first();
        $data = [
            'saved'=>'1'
        ];
        $head->update($data);
    }

    public function save_gatepass_items_try(Request $request,  $id){
        $list_items = $request->input('gatepass_items');

        foreach(json_decode($list_items) as $li){
            $gatepass_items['gatepass_head_id'] = $id;
            $gatepass_items['item_description'] = $li->item_description;
            $gatepass_items['quantity'] = $li->quantity;
            $gatepass_items['uom'] = $li->uom;
            $gatepass_items['type'] = $li->type;
            $gatepass_items['remarks'] = $li->remarks;
            // if($li->file('imageFilename')){
            //     $imagename=$li->file('imageFilename')->getClientOriginalName();
            //     $li->file('imageFilename')->storeAs('images',$imagename);
            //     $gatepass_items['image']=$imagename;
            // }

            // if($li->display_flag == 'Yes'){
            //     $gatepass_items['display_flag'] = 1;
            // }else{
            //     $gatepass_items['display_flag'] = 0;
            // }
            $gp_items = GatepassItems::create($gatepass_items);
            if($li->type == 'Non-returnable'){

            $returnableitems=GatepassItems::where('gatepass_head_id',$id)->where('type','=','Returnable')->get();
            $count_returnable_items=$returnableitems->count();
            if($count_returnable_items == 0){
                $head=GatepassHead::where('id',$id)->first();
                $data = [
                    'status'=>'Completed'
                ];
                $head->update($data);
            }
        }
        $head=GatepassHead::where('id',$id)->first();
        $data = [
            'saved'=>'1'
        ];
        $head->update($data);
    }
}

    public function add_signatory(Request $request){
        $update_data=GatepassHead::where('id',$request->id)->first();
        
        $validated=[
            'received_by'=>$request->received_by,
            'received_by_name'=>User::where('id',$request->received_by)->value('name'),
            'received_by_position'=>($request->rec_position != 'null') ? $request->rec_position : '',
            'cpgc_guard_name'=>($request->cpgc_guard != 'null') ? $request->cpgc_guard : '',
            'npc_guard_name'=>($request->npc_guard != 'null') ? $request->npc_guard : '',
        ];
        $update_data->update($validated);
    }

    public function add_return_details(Request $request){
        GatepassReturnedHistory::create([
            'gatepass_head_id' => $request->head_id,
            'gatepass_item_id' => $request->item_id,
            'date_returned' => $request->date_return,
            'returned_qty' => $request->return_qty,
            'remarks' => $request->return_remarks,
            'user_id' => Auth::user()->id,
        ]);

        $item_qty = GatepassItems::where('gatepass_head_id','=',$request->head_id)->where('type','=','Returnable')->sum('quantity');
        $total_returned_qty = GatepassReturnedHistory::where('gatepass_head_id','=',$request->head_id)->sum('returned_qty');

        if($item_qty == $total_returned_qty){
            $head=GatepassHead::where('id',$request->head_id)->first();
            $data = [
                'status'=>'Completed'
            ];
            $head->update($data);
        }
    }

    public function returned_details(Request $request, $headid){
        // $returned_details = GatepassReturnedHistory::with('gatepass_items')->where('gatepass_head_id',$headid)->orderBy('date_returned','DESC')->get();

        $query = GatepassReturnedHistory::with(['gatepass_items'])->where('gatepass_head_id',$headid)->orderBy('gatepass_item_id','ASC')->orderBy('date_returned','DESC');
            $query->whereHas('gatepass_items', function ($query) use ($headid) {
                $query->where('gatepass_head_id', '=', $headid);
            });
            $returned_details = $query->get();

        $returned_items = array();
        foreach($returned_details AS $rd){
            $returned_items[]=[
                'item_desc'=>$rd->gatepass_items->item_description,
                'date_returned'=>$rd->date_returned,
                'returned_qty'=>$rd->returned_qty,
                'remarks'=>$rd->remarks,
            ];
        }
        return response()->json($returned_items);
    }

    public function cancel_gatepass($id){
        $head=GatepassHead::where('id', '=', $id);
        $head->delete();

        $items=GatepassItems::where('gatepass_head_id', '=', $id);
        $items->delete();
    }

    public function update_remarks_head(Request $request){
        $updateremarks=GatepassHead::where('id',$request->head_id)->first();
        $remarkshead['remarks']=$request->remarks_head;
        $updateremarks->update($remarkshead);
    }

    public function item_details(Request $request, $id){
        $issued_qty=GatepassItems::where('id',$id)->value('quantity');
        $total_returned_qty = GatepassReturnedHistory::where('gatepass_item_id','=',$id)->sum('returned_qty');
        $quantity = $issued_qty - $total_returned_qty;
        return response()->json([
            'quantity'=>$quantity,
        ],200);
    }

    public function get_returnable_items(Request $request, $headid){
        $all_items=GatepassItems::where('gatepass_head_id',$headid)->where('type','=','Returnable')->orderBy('item_description','ASC')->get();

        $all_it = array();
        foreach($all_items AS $ai){
            $issued_qty=GatepassItems::where('id',$ai->id)->value('quantity');
            $total_returned_qty = GatepassReturnedHistory::where('gatepass_item_id','=',$ai->id)->sum('returned_qty');
            $quantity = $issued_qty - $total_returned_qty;

            if($quantity != 0){
                $all_it[]=[
                    'id'=>$ai->id,
                    'item_description'=>$ai->item_description,
                    'item_description'=>$ai->item_description,
                ];
            }
            
        }
        
        return response()->json([
            'all_items'=>$all_it
        ],200);
    }

}
