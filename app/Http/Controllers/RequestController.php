<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\RequestHead;
use App\Models\RequestItems;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\Variants;
use App\Models\PIVBalance;
use App\Models\VariantsBalance;
use App\Models\CompositeItems;
use App\Models\Item;
use App\Models\ItemStatus;
use App\Models\Mreqf;
use App\Models\PRItems;
use App\Models\User;
use App\Models\Department;
use App\Models\Enduse;
use App\Models\Purpose;
use App\Http\Requests\RequestHeadRequest;
use App\Http\Requests\RequestItemsRequest;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function get_all_request(Request $request){

        //$requests = RequestHead::with(['request_items'])->paginate(10);
        $requests = RequestHead::query()->when($request->get('filter'), function ($query, $filter) {
            $query->where('request_date', 'LIKE', '%' . $filter . '%')
            ->orWhere('mreqf_no', 'LIKE', '%' . $filter . '%')
            ->orWhere('pr_no', 'LIKE', '%' . $filter . '%')
            ->orWhere('department_name', 'LIKE', '%' . $filter . '%')
            ->orWhere('purpose_name', 'LIKE', '%' . $filter . '%')
            ->orWhere('enduse_name', 'LIKE', '%' . $filter . '%');
        })->orderBy('mreqf_no','DESC')->paginate(10);
        return response()->json($requests);
    }

    public function create_head(Request $request){
        $curr_year = date('Y');
        $curr_mo = date('m');
        if(Mreqf::where('year', '=', $curr_year)->exists()) {
            $mreqf = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
            $max_value = str_pad($mreqf,4,"0",STR_PAD_LEFT);;
        } else {
            $max_value = '0001';
        }

        $mreqf_no = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;


        $id = Auth::id();
        $formData=[
            'mreqf_no'=>$mreqf_no,
            'request_date'=>date("Y-m-d"),
            'request_time'=>date("H:i:s"),
            'request_type'=>'',
            'pr_no'=>'',
            'department_name'=>'',
            'department_id'=>'',
            'enduse_name'=>'',
            'enduse_id'=>'',
            'purpose_name'=>'',
            'purpose_id'=>'',
            'remarks'=>'',
            'user_id'=>$id
        ];
        return response()->json($formData);
    }

    public function add_head(RequestHeadRequest $request){
        // $validated=$request->validated();
        // $validated['department_id']=($request->department_id) ? $request->department_id : 0;
        // $validated['department_name']=($request->department_name) ? $request->department_name : NULL;
        // $validated['enduse_id']=($request->enduse_id) ? $request->enduse_id : 0;
        // $validated['enduse_name']=($request->enduse_name) ? $request->enduse_name : NULL;
        // $validated['purpose_id']=($request->purpose_id) ? $request->purpose_id : 0;
        // $validated['purpose_name']=($request->purpose_name) ? $request->purpose_name : NULL;
        // $validated['remarks']=($request->remarks) ? $request->remarks : NULL;
        // $request_id=RequestHead::insertGetId($validated);

        if(empty($request->remarks)){
            $remarks = '';
        } else {
            $remarks=$request->remarks;
        }

        if(empty($request->department_id)){
            $department_id = 0;
            $department_name = '';
        } else {
            $department_id = $request->department_id;
            $department_name = Department::where('id',$request->department_id)->value('department_name');
        }

        if(empty($request->enduse_id)){
            $enduse_id = 0;
            $enduse_name = '';
        } else {
            $enduse_id = $request->enduse_id;
            $enduse_name = Enduse::where('id',$request->enduse_id)->value('enduse_name');
        }

        if(empty($request->purpose_id)){
            $purpose_id = 0;
            $purpose_name = '';
        } else {
            $purpose_id = $request->purpose_id;
            $purpose_name = Purpose::where('id',$request->purpose_id)->value('purpose_name');
        }


        $validated=[
            'mreqf_no'=>$request->mreqf_no,
            'request_date'=>$request->request_date,
            'request_time'=>$request->request_time,
            'request_type'=>$request->request_type,
            'pr_no'=>$request->pr_no,
            'department_id'=>$department_id,
            'department_name'=>$department_name,
            'enduse_id'=>$enduse_id,
            'enduse_name'=>$enduse_name,
            'purpose_id'=>$purpose_id,
            'purpose_name'=>$purpose_name,
            'remarks'=>$remarks,
            'user_id'=>$request->user_id,
        ];
        $request_id=RequestHead::insertGetId($validated);

        $mreqf = $request->mreqf_no;
        $ser = explode("-",$mreqf);
        $series = $ser[3];

        Mreqf::create([
            'year' => date("Y"),
            'series'=>$series
        ]);

        return $request_id;
    }

    // public function create_details(Request $request, $id){
    //     $request_head = RequestHead::find($id);
    //     //$request_head = RequestHead::with(['request_items'])->find($id);
    //     $pr_no = RequestHead::where('id',$id)->value('pr_no');
    //     $req_type = RequestHead::where('id',$id)->value('request_type');
    //     //$request_details = ReceiveDetails::with('receive_items')->where('pr_no',$pr_no)->get();
    //     $receiveid = ReceiveDetails::where('pr_no',$pr_no)->value('id');
    //     //$receiveitems = ReceiveItems::with('variants')->where('receive_details_id',$receiveid)->get();

    //         // $itemData=array();
    //         $itemData=array();
    //         if($req_type == 'WH STOCKS'){
    //             $itemData=array();
    //         }else{
    //             // if(ReceiveDetails::with(['receive_items'])->where('pr_no',$pr_no)->exists()) {
    //                 //$receive_details = ReceiveDetails::with(['receive_items'])->where('pr_no',$pr_no)->get();
    //                 $receive_details = ReceiveDetails::where('pr_no',$pr_no)->get();

    //             foreach($receive_details AS $rd){
    //                 $rec_items = ReceiveItems::where('receive_details_id',$rd->id)->Orwhere('prno_replenish',$rd->pr_no)->get();
    //                 // foreach($rd->receive_items AS $ri){
    //                     foreach($rec_items AS $ri){
    //                     $instock = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $ri->item_id)->value('balance');
    //                     //$request_qty = RequestHead::with(['request_items'])->where('pr_no',$rd->pr_no)->where('item_id',$ri->item_id)->sum('request_items.quantity')->get();
    //                     //$purchases = RequestHead::table('request_head')->join('request_items', 'request_head.id', '=', 'request_items.request_head_id')->where('request_head.pr_no',$rd->pr_no)->where('request_items.item_id',$ri->item_id)->sum('request_items.quantity');
    //                     //$remaining_qty = $ri->rec_quantity - $request_qty;
    //                     // if($instock != 0){
    //                     //     $rec_qty = VariantsBalance::where('item_id', '=', $ri->item_id)->where('variant_id', '=', $ri->variant_id)->value('balance');
    //                     //     $quantity = $rec_qty - $ri->borrow_qty;
    //                     $itemData[] = [
    //                         'req_qty'=>0,
    //                         'rec_qty'=>$instock,
    //                         'supplier'=>$ri->supplier_name,
    //                         'variant_id'=>$ri->variant_id,
    //                         'item_id'=>$ri->item_id,
    //                         'item_description'=>$ri->item_description,
    //                         'unit_cost'=>$ri->unit_cost,
    //                         'shipping_cost'=>$ri->shipping_cost,
    //                         'uom'=>$ri->uom,
    //                         'catalog_no'=>$ri->catalog_no,
    //                         'brand'=>$ri->brand,
    //                         'serial_no'=>$ri->serial_no,
    //                         'size'=>$ri->size,
    //                         'color'=>$ri->color,
    //                         'item_status'=>$ri->item_status,
    //                         'expiry_date'=>$ri->expiry_date
    //                     ];
    //                     // }
    //                 }
    //             }
    //         // }
    //     }
    //     return response()->json([
    //         'request_head'=>$request_head,
    //         'receiveitems'=>$itemData,
    //     ],200);

    // }

    public function create_details(Request $request, $id){
        $request_head = RequestHead::find($id);
        $pr_no = RequestHead::where('id',$id)->value('pr_no');
        $req_type = RequestHead::where('id',$id)->value('request_type');
        $itemData=array();
        if($req_type == 'WH STOCKS'){
            $itemData=array();
                    }else{
                        $receive_details = ReceiveDetails::where('pr_no',$pr_no)->get();
                            foreach($receive_details AS $rd){
                                $pr_qty = PRItems::where('pr_no','=',$pr_no)->value('balance');
                                $pr_replenish = ReceiveItems::where('receive_details_id', '=', $rd->id)->value('pr_replenish');
                                // $prno_replenish = ReceiveItems::where('receive_details_id', '=', $rd->id)->value('prno_replenish');
                                if($pr_replenish == '1' && $pr_qty == 0){
                                // if($pr_replenish == '1'){
                                    $rec_items = ReceiveItems::with('variants')->where('prno_replenish', '=', $rd->pr_no)->where('rec_quantity','!=','0')->where('eval_flag','1')->orWhere('eval_flag','2')->get();
                                    // $rec_items = PIVBalance::with('variants')->where('pr_no', '=', $prno_replenish)->where('quantity', '!=', '0')->get();
                                }else if($pr_replenish == '1' && $pr_qty != 0){
                                    $rec_items = ReceiveItems::with('variants')->where('receive_details_id', '=', $rd->id)->where('rec_quantity','!=','0')->where('eval_flag','1')->orWhere('eval_flag','2')->get();
                                }else{
                                    $rec_items = ReceiveItems::with('variants')->where('receive_details_id', '=', $rd->id)->where('pr_replenish','!=','1')->where('rec_quantity','!=','0')->where('eval_flag','1')->orWhere('eval_flag','2')->get();
                                    // $rec_items = PIVBalance::with('variants')->where('pr_no', '=', $pr_no)->where('quantity', '!=', '0')->get();
                                }
                            // }
                            

                            foreach($rec_items AS $ri){
                                $instock = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $ri->item_id)->where('variant_id', '=', $ri->variant_id)->value('quantity');
                                // $instock = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $ri->item_id)->where('variant_id', '=', $ri->variant_id)->value('quantity');

                                // $counter=ReceiveItems::where('item_id', '=', $ri->item_id)->where('variant_id','=',$ri->variant_id)->get();
                                // $count=$counter->count();
                                // if($count==1){
                                //     $unit_cost = $ri->variants->unit_cost;
                                //     $shipping_cost = $ri->variants->shipping_cost;
                                // }else{
                                //     $unit_cost = $ri->variants->average_cost;
                                //     $shipping_cost = 0;
                                // }

                                // $item_description = Item::where('id',$ri->item_id)->value('item_description');
                                // $item_status = ItemStatus::where('id',$ri->variants->item_status_id)->value('status');
                                if($instock != 0){
                                    $itemData[] = [
                                        'req_qty'=>0,
                                        'rec_qty'=>$instock,
                                        // 'rec_qty'=>$ri->quantity,
                                        'supplier'=>$ri->supplier_name,
                                        'variant_id'=>$ri->variant_id,
                                        'item_id'=>$ri->item_id,
                                        'item_description'=>$ri->item_description,
                                        'unit_cost'=>$ri->unit_cost,
                                        'shipping_cost'=>$ri->shipping_cost,
                                        'total_cost'=>$ri->variants->average_cost,
                                        'currency'=>$ri->variants->currency,
                                        'uom'=>$ri->uom,
                                        'catalog_no'=>$ri->catalog_no,
                                        'brand'=>$ri->brand,
                                        'serial_no'=>$ri->serial_no,
                                        'size'=>$ri->size,
                                        'color'=>$ri->color,
                                        'item_status'=>$ri->item_status,
                                        'expiry_date'=>$ri->expiry_date
                                    ];
                                }

                                $itemData = $this->unique_multidim_array($itemData, "variant_id",);
                    }
                }
            }
            return response()->json([
                'request_head'=>$request_head,
                'receiveitems'=>$itemData,
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

    public function get_request_head($id){
        $head = RequestHead::find($id);
        $user_id=RequestHead::where('id',$id)->value('user_id');
        $prepared_by=User::where('id',$user_id)->value('name');
        $prep_position=User::where('id',$user_id)->value('position');
        $requested_id=RequestHead::where('id',$id)->value('requested_by');
        $req_position=User::where('id',$requested_id)->value('position');
        $reviewed_id=RequestHead::where('id',$id)->value('reviewed_by');
        $rev_position=User::where('id',$reviewed_id)->value('position');
        $approved_id=RequestHead::where('id',$id)->value('approved_by');
        $app_position=User::where('id',$approved_id)->value('position');
        $noted_id=RequestHead::where('id',$id)->value('noted_by');
        $noted_position=User::where('id',$noted_id)->value('position');
        return response()->json([
            'head'=>$head,
            'user_id'=>$user_id,
            'prepared_by'=>$prepared_by,
            'prep_position'=>$prep_position,
            'req_position'=>$req_position,
            'rev_position'=>$rev_position,
            'app_position'=>$app_position,
            'noted_position'=>$noted_position,
        ],200);
    }

    public function get_request_items(Request $request, $id){
       
        $items = RequestItems::with(['variants'])->where('request_head_id', '=', $id)->get();
        foreach($items AS $i){
            $compose_item = CompositeItems::where('id','=',$i->composite_id)->value('compose_item_id');
            $compose_itemname = Item::where('id','=',$compose_item)->value('item_description');
            $c_flag = Item::where('id','=',$i->item_id)->value('composite_flag');
            // $supplier_name = Variants::where('id','=',$i->variant_id)->value('supplier_name');
            // $uom = Variants::where('id','=',$i->item_id)->value('uom');
            if($i->variant_id != 0){
                $uom = $i->variants->uom;
                $supplier_name = $i->variants->supplier_name;
                $catalog_no = $i->variants->catalog_no;
                $brand = $i->variants->brand;
                $serial_no = $i->variants->serial_no;
                $size = $i->variants->size;
                $color = $i->variants->color;
                $expiry_date = $i->variants->expiration;
                // $status = ItemStatus::where('id','=',$i->item_status_id)->value('status');
                $status = $i->variants->item_status->status;

            }else if($i->variant_id == 0){
                //$uom = Item::where('id','=',$i->item_id)->value('uom');
                $uom = '';
                $supplier_name = 'Begbal';
                $catalog_no = '';
                $brand = '';
                $serial_no = '';
                $size = '';
                $color = '';
                $expiry_date = '';
                $status = '';
            }else{
                //$uom = Item::where('id','=',$i->item_id)->value('uom');
                $uom = '';
                $supplier_name = '';
                $catalog_no = '';
                $brand = '';
                $serial_no = '';
                $size = '';
                $color = '';
                $expiry_date = '';
                $status = '';

            }
            //$status = ItemStatus::where('id','=',$i->variants->item_status_id)->value('status');
            $pn_no = Item::where('id','=',$i->item_id)->value('pn_no');


            if($i->composite_id == '0'){
                $all_compo = CompositeItems::with('items')
                ->where('item_id','=', $i->item_id)->where('quantity','!=', '0')->get();
                
                $item_description = $i->item_description . " (";

                foreach($all_compo AS $ac){
                    
                    $item_description .= $ac->items->item_description . ", ";
                }
                $item_description = substr_replace($item_description, '', -2);
                if($c_flag != 0){
                    $item_description .= ")";
                }else{
                    $item_description .= "";
                }
            }else{
                $item_description = $i->item_description;
            }
                
           

            $req_items[]=[
                //'pr_no'=>$det->pr_no,
                'req_qty'=>$i->quantity,
                'unit_cost'=>$i->unit_cost,
                'currency'=>$i->currency,
                'shipping_cost'=>$i->shipping_cost,
                'item_description'=>$item_description,
                'compose_id'=>$i->composite_id,
                'compose_itemname'=>$compose_itemname,
                // 'from_pr'=>$i->from_pr,
                'supplier_name'=>$supplier_name,
                'uom'=>$uom,
                'pn_no'=>$pn_no,
                'catalog_no'=>$catalog_no,
                'brand'=>$brand,
                'serial_no'=>$serial_no,
                'color'=>$color,
                'size'=>$size,
                'item_status'=>$status,
                'expiry_date'=>$expiry_date
            ];
        }
        return response()->json([
            'req_items'=>$req_items,
        ],200);
    }

    public function choose_prno($prno){
        $department_id = ReceiveDetails::where('pr_no',$prno)->value('department_id');
        $department_name = ReceiveDetails::where('pr_no',$prno)->value('department_name');
        $enduse_id = ReceiveDetails::where('pr_no',$prno)->value('enduse_id');
        $enduse_name = ReceiveDetails::where('pr_no',$prno)->value('enduse_name');
        $purpose_id = ReceiveDetails::where('pr_no',$prno)->value('purpose_id');
        $purpose_name = ReceiveDetails::where('pr_no',$prno)->value('purpose_name');
        return response()->json([
            'department_id'=>$department_id,
            'department_name'=>$department_name,
            'enduse_id'=>$enduse_id,
            'enduse_name'=>$enduse_name,
            'purpose_id'=>$purpose_id,
            'purpose_name'=>$purpose_name,
        ],200);
    }

    public function search_wh_variant(Request $request){
        $filter=$request->get('filter');
        
        if($filter!=null){
            $v_flag = Item::where('id',$filter)->value('variant_flag');
            $c_flag = Item::where('id',$filter)->value('composite_flag');
            $nv_flag = Item::where('id',$filter)->value('novariant_flag');
            $item_desc = Item::where('id',$filter)->value('item_description');
            $com_cost = Item::where('id',$filter)->value('composite_cost');
            //$variant_id = VariantsBalance::where('item_id',$filter)->value('variant_id');
            $wh_balance=PRItems::where('item_id',$filter)->where('pr_no','WH STOCKS')->value('balance');
                $VariantData=array();

                if($v_flag != 0 && $c_flag == 0){
                    // $variants_bal = VariantsBalance::with(['variants'])->where('item_id',$filter)->where('whstocks_qty','!=','0')->Orwhere('restock_qty','!=','0')->get();
                    $variants_bal = PIVBalance::with(['variants'])->where('item_id',$filter)->where('pr_no','=','WH STOCKS')->where('quantity','!=','0')->get();
                    foreach($variants_bal AS $vb){
                        //$item_name = Item::where('id',$vb->item_id)->value('item_description');
                            if($vb->variant_id != 0){
                                if($vb->variants->brand != 'null' && $vb->variants->brand != ''){
                                    $brand= ', Brand: '.$vb->variants->brand;
                                }else{
                                    $brand = '';
                                }

                                if($vb->variants->catalog_no != 'null' && $vb->variants->catalog_no != ''){
                                    $catalog_no= ', Catalog: '.$vb->variants->catalog_no;
                                }else{
                                    $catalog_no = '';
                                }

                                if($vb->variants->serial_no != 'null' && $vb->variants->serial_no != ''){
                                    $serial_no= ', Serial: '.$vb->variants->serial_no;
                                }else{
                                    $serial_no = '';
                                }

                                if($vb->variants->size != 'null' && $vb->variants->size != ''){
                                    $size= ', Size: '.$vb->variants->size;
                                }else{
                                    $size = '';
                                }

                                if($vb->variants->color != 'null' && $vb->variants->color != ''){
                                    $color= ', Color: '.$vb->variants->color;
                                }else{
                                    $color = '';
                                }

                                $variant_desc = 'Supplier: '.$vb->variants->supplier_name.''.$brand.''.$catalog_no.''.$serial_no.''.$size.''.$color;
                                $VariantData[] = [
                                    'variant_data'=>$variant_desc,
                                    'unit_cost'=>$vb->variants->unit_cost,
                                    'currency'=>$vb->variants->currency,
                                    'shipping_cost'=>$vb->variants->shipping_cost,
                                    'total_cost'=>$vb->variants->average_cost,
                                    'supplier'=>$vb->variants->supplier_name,
                                    'variant_id'=>$vb->variant_id,
                                    'item_name'=>$item_desc,
                                    'item_id'=>$vb->item_id,
                                    'brand'=>$vb->variants->brand,
                                    'cat_no'=>$vb->variants->catalog_no,
                                    'serial'=>$vb->variants->serial_no,
                                    'size'=>$vb->variants->size,
                                    'color'=>$vb->variants->color,
                                    'composite_id'=>'',
                                ];
                            }else{
                                $variant_desc = 'Begbal';
                                $VariantData[] = [
                                    'variant_data'=>$variant_desc,
                                    'unit_cost'=>0,
                                    'currency'=>'',
                                    'shipping_cost'=>0,
                                    'total_cost'=>0,
                                    'supplier'=>'',
                                    'variant_id'=>$vb->variant_id,
                                    'item_name'=>$item_desc,
                                    'item_id'=>$vb->item_id,
                                    'brand'=>'',
                                    'cat_no'=>'',
                                    'serial'=>'',
                                    'size'=>'',
                                    'color'=>'',
                                    'composite_id'=>'',
                                ];
                            }
                        }
                }else if($v_flag == 0 && $c_flag != 0){
                    $composite_variants = CompositeItems::with(['variants'])->where('item_id',$filter)->where('quantity','!=','0')->get();
                    foreach($composite_variants AS $cv){
                        $item_name = Item::where('id',$cv->compose_item_id)->value('item_description');
                            if($cv->variant_id != 0){
                                
                                if($cv->variants->brand != 'null' && $cv->variants->brand != ''){
                                    $brand= ', Brand: '.$cv->variants->brand;
                                }else{
                                    $brand = '';
                                }

                                if($cv->variants->catalog_no != 'null' && $cv->variants->catalog_no != ''){
                                    $catalog_no= ', Catalog: '.$cv->variants->catalog_no;
                                }else{
                                    $catalog_no = '';
                                }

                                if($cv->variants->serial_no != 'null' && $cv->variants->serial_no != ''){
                                    $serial_no= ', Serial: '.$cv->variants->serial_no;
                                }else{
                                    $serial_no = '';
                                }

                                if($cv->variants->size != 'null' && $cv->variants->size != ''){
                                    $size= ', Size: '.$cv->variants->size;
                                }else{
                                    $size = '';
                                }

                                if($cv->variants->color != 'null' && $cv->variants->color != ''){
                                    $color= ', Color: '.$cv->variants->color;
                                }else{
                                    $color = '';
                                }

                                $variant_desc = $item_name.' - Supplier: '.$cv->variants->supplier_name.''.$brand.''.$catalog_no.''.$serial_no.''.$size.''.$color;
                                $VariantData[] = [
                                    'variant_data'=>$variant_desc,
                                    'unit_cost'=>$cv->variants->unit_cost,
                                    'currency'=>$cv->variants->currency,
                                    'shipping_cost'=>$cv->variants->shipping_cost,
                                    'total_cost'=>$cv->variants->average_cost,
                                    'supplier'=>$cv->variants->supplier_name,
                                    'variant_id'=>$cv->variant_id,
                                    'item_name'=>$item_name,
                                    'item_id'=>$cv->compose_item_id,
                                    'brand'=>$cv->variants->brand,
                                    'cat_no'=>$cv->variants->catalog_no,
                                    'serial'=>$cv->variants->serial_no,
                                    'size'=>$cv->variants->size,
                                    'color'=>$cv->variants->color,
                                    'composite_id'=>$cv->id,
                                ];
                            }else{
                                $variant_desc =   $item_name . ' - Begbal';
                                $VariantData[] = [
                                    'variant_data'=>$variant_desc,
                                    'unit_cost'=>0,
                                    'currency'=>"",
                                    'shipping_cost'=>0,
                                    'total_cost'=>0,
                                    'supplier'=>'',
                                    'variant_id'=>$cv->variant_id,
                                    'item_name'=>$item_name,
                                    'item_id'=>$cv->compose_item_id,
                                    'brand'=>'',
                                    'cat_no'=>'',
                                    'serial'=>'',
                                    'size'=>'',
                                    'color'=>'',
                                    'composite_id'=>$cv->id,
                                ];
                            }
                    }
                }
                       
                    return response()->json([
                        'items'=>$VariantData,
                        'item_desc'=>$item_desc,
                        'v_flag'=>$v_flag,
                        'c_flag'=>$c_flag,
                        'quantity'=>$wh_balance,
                        'composite_cost'=>$com_cost,
                    ],200);
        }else{
            return response()->json([]);
        }
    }

    public function search_whvariantqty($variant_id, $var_itemid, $item_id){
        $c_flag = Item::where('id',$item_id)->value('composite_flag');
        $v_flag = Item::where('id',$item_id)->value('variant_flag');

        if($c_flag != 0){
                $quantity=CompositeItems::where('item_id',$item_id)->where('compose_item_id',$var_itemid)->where('variant_id',$variant_id)->value('quantity');
        }else if($v_flag != 0){
            // if($variant_id!=0 && $item_id!=0){
                $quantity=PIVBalance::where('pr_no','WH STOCKS')->where('item_id',$item_id)->where('variant_id',$variant_id)->value('quantity');
            // }else if($variant_id==0 && $item_id!=0){
            //     $quantity=VariantsBalance::where('item_id',$item_id)->where('variant_id','0')->value('balance');
            // }
        }

        return response()->json([
            'quantity'=>$quantity
        ],200);
    }

    public function save_request(RequestItemsRequest $request,  $id){

        // $request_type = RequestHead::where('id',$id)->value('request_type');

        $item_lists = $request->input('request_insert_wh');
        $items = $request->input('request_items');
        
        $head=RequestHead::where('id',$id)->first();

            // if($head->request_type == 'WH STOCKS'){
            //     $desc = $request->input('description');
            //     $itemid = $request->input('itemid');
            //     $variantid = $request->input('variantid');
            // }
            
            $data = [
                //'draft'=>'0',
                'saved'=>'1'
            ];
            $head->update($data);

        if($head->request_type == 'With PR'){
            foreach(json_decode($items) as $i){
                $qty = $i->req_qty;
                if($qty != 0){
                    // if($head->request_type == 'WH STOCKS'){
                    //     $req_items['request_head_id'] = $id;
                    //     $req_items['item_id'] = ($itemid != '') ? $itemid : 0;
                    //     $req_items['item_description'] = $desc;
                    //     $req_items['variant_id'] = ($variantid != '') ? $variantid : 0;
                    //     $req_items['quantity'] = $i->req_qty;
                    // }else{
                        $req_items['request_head_id'] = $id;
                        $req_items['item_id'] = ($i->item_id != '') ? $i->item_id : 0;
                        $req_items['item_description'] = $i->item_description;
                        $req_items['variant_id'] = ($i->variant_id != '') ? $i->variant_id : 0;
                        $req_items['quantity'] = $i->req_qty;
                        $req_items['unit_cost'] = $i->unit_cost;
                        $req_items['currency'] = $i->currency;
                        $req_items['currency'] = $i->currency;
                        $req_items['shipping_cost'] = $i->shipping_cost;
                    //}
                    $sub = RequestItems::create($req_items);
                }
            }
        }else{
            foreach(json_decode($list_items) as $li){

                $req_items['request_head_id'] = $id;
                $req_items['item_id'] = ($li->item_id != '') ? $li->item_id : 0;
                $req_items['item_description'] = $li->description;
                $req_items['variant_id'] = ($li->variantid != '') ? $li->variantid : 0;
                $req_items['quantity'] = $li->req_qty;
                $req_items['composite_id'] = ($li->compositeid != '') ? $li->compositeid : 0;
                $req_items['unit_cost'] = $li->unitcost;
                $req_items['currency'] = $li->currency ?? '';
                $req_items['shipping_cost'] = $li->shippingcost;
                $sub = RequestItems::create($req_items);
            }
        }
    }
 
    public function cancel_transaction($id){
      
        $head=RequestHead::where('id', '=', $id);
        $head->delete();

        $details=RequestItems::where('request_head_id', '=', $id);
        $details->delete();
    }

    public function get_request_position($id){
        $position = User::where("id", $id)->value('position');
        return $position;
    }

    public function add_request_signatory(Request $request){
            $update_data=RequestHead::where('id',$request->id)->first();
            //if($request->requested_by == 0 && $request->reviewed_by == 0 && $request->approved_by == 0 && $request->noted_by == 0){
                $validated=[
                    'prepared_by'=>$request->user_id,
                    'prepared_by_name'=>User::where('id',$request->user_id)->value('name'),
                    'prepared_by_position'=>User::where('id',$request->user_id)->value('position'),
                    'requested_by'=>$request->requested_by,
                    'requested_by_name'=>User::where('id',$request->requested_by)->value('name'),
                    'requested_by_position'=>User::where('id',$request->requested_by)->value('position'),
                    'reviewed_by'=>$request->reviewed_by,
                    'reviewed_by_name'=>User::where('id',$request->reviewed_by)->value('name'),
                    'reviewed_by_position'=>User::where('id',$request->reviewed_by)->value('position'),
                    'approved_by'=>$request->approved_by,
                    'approved_by_name'=>User::where('id',$request->approved_by)->value('name'),
                    'approved_by_position'=>User::where('id',$request->approved_by)->value('position'),
                    'noted_by'=>$request->noted_by,
                    'noted_by_name'=>User::where('id',$request->noted_by)->value('name'),
                    'noted_by_position'=>User::where('id',$request->noted_by)->value('position'),
                ];
                $update_data->update($validated);
            //}
                
    }

}