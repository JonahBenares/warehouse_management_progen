<?php

namespace App\Http\Controllers;

use App\Models\MBR;
use App\Models\Mreqf;
use App\Models\MIF;
use App\Models\BorrowHead;
use App\Models\BorrowDetails;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\RequestHead;
use App\Models\RequestItems;
use App\Models\IssuanceHead;
use App\Models\IssuanceItems;
use App\Models\Items;
use App\Models\PRItems;
use App\Models\Variants;
use App\Models\PIVBalance;
use App\Models\VariantsBalance;
use App\Models\CompositeItems;
use App\Models\Department;
use App\Models\Enduse;
use App\Models\Purpose;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowController extends Controller
{

    public function get_all_borrowed(Request $request){
        $filter=$request->get('filter');
        $head = BorrowHead::when($request->get('filter'), function ($query, $filter) {
            $query->where('borrow_date', 'LIKE', '%' . $filter . '%')
            ->orWhere('borrow_time', 'LIKE', '%' . $filter . '%')
            ->orWhere('mbr_no', 'LIKE', '%' . $filter . '%');
            // ->orWhere('borrowed_from', 'LIKE', '%' . $filter . '%')
            // ->orWhere('borrowed_by', 'LIKE', '%' . $filter . '%');
        // })->paginate(10);
        })->where('saved','=','1')->paginate(10);
        return response()->json($head);
    }


    public function create_borrow_head(){
        $curr_year = date('Y');
        $curr_mo = date('m');

        $department = Department::all();
        $enduse = Enduse::all();
        $purpose = Purpose::all();
        $user = User::all();


        if(MBR::where('year', '=', $curr_year)->exists()) {
            $mbr = MBR::where('year', '=', $curr_year)->max('series') + 1;
            $max_value = str_pad($mbr,4,"0",STR_PAD_LEFT);;
        } else {
            $max_value = '0001';
        }

        $mbr_no = 'MBR-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
        
        $borrow_items = Items::where('running_balance','!=','0')->where('draft','=','0')->orderBy('item_description','ASC')->get();

        //$id = Auth::id();
        $formData=[
            'mbr_no'=>$mbr_no,
            'borrow_date'=>date("Y-m-d"),
            'borrow_time'=>date("H:i:s"),
            // 'department'=>'',
            // 'enduse'=>'',
            // 'purpose'=>'',
            // 'pr_no'=>'',
            // 'user_id'=>$id,
            // 'remarks'=>''
        ];
        return response()->json([
          'BorrowItems'=>$borrow_items,
          'formData'=>$formData,    
          'department'=>$department,
          'enduse'=>$enduse,
          'user'=>$user,
          'purpose'=>$purpose,
        ],200);
    }

    public function search_availablepr($itemid){
        // $prlist=PRItems::where('item_id',$itemid)->where('balance','!=','0')->get();
        $prlist=PIVBalance::with(['variants'])->where('item_id',$itemid)->where('quantity','!=','0')->get();
        foreach($prlist AS $pl){
            $item_name = Items::where('id',$pl->item_id)->value('item_description');
                if($pl->variant_id != 0){
                    if($pl->variants->brand != 'null' && $pl->variants->brand != ''){
                        $brand= ', Brand: '.$pl->variants->brand;
                    }else{
                        $brand = '';
                    }

                    if($pl->variants->catalog_no != 'null' && $pl->variants->catalog_no != ''){
                        $catalog_no= ', Catalog: '.$pl->variants->catalog_no;
                    }else{
                        $catalog_no = '';
                    }

                    if($pl->variants->serial_no != 'null' && $pl->variants->serial_no != ''){
                        $serial_no= ', Serial: '.$pl->variants->serial_no;
                    }else{
                        $serial_no = '';
                    }

                    if($pl->variants->size != 'null' && $pl->variants->size != ''){
                        $size= ', Size: '.$pl->variants->size;
                    }else{
                        $size = '';
                    }

                    if($pl->variants->color != 'null' && $pl->variants->color != ''){
                        $color= ', Color: '.$pl->variants->color;
                    }else{
                        $color = '';
                    }

                    $variant_desc = 'Supplier: '.$pl->variants->supplier_name.''.$brand.''.$catalog_no.''.$serial_no.''.$size.''.$color;
                    $VariantData[] = [
                        'variant_data'=>$variant_desc,
                        'pr_no'=>$pl->pr_no,
                        'item_id'=>$pl->item_id,
                        'item_name'=>$item_name,
                        'variant_id'=>$pl->variant_id,
                        'quantity'=>$pl->quantity,
                        // 'unit_cost'=>$pl->variants->unit_cost,
                        // 'shipping_cost'=>$pl->variants->shipping_cost,
                        // 'supplier'=>$pl->variants->supplier_name,
                        // 'brand'=>$pl->variants->brand,
                        // 'cat_no'=>$pl->variants->catalog_no,
                        // 'serial'=>$pl->variants->serial_no,
                        // 'size'=>$pl->variants->size,
                        // 'color'=>$pl->variants->color,
                        // 'composite_id'=>'',
                    ];
                }else{
                    $variant_desc = '';
                    $VariantData[] = [
                        'variant_data'=>$variant_desc,
                        'pr_no'=>$pl->pr_no,
                        'item_id'=>$pl->item_id,
                        'item_name'=>$item_name,
                        'variant_id'=>$pl->variant_id,
                        'quantity'=>$pl->quantity,
                        // 'unit_cost'=>0,
                        // 'shipping_cost'=>0,
                        // 'supplier'=>'',
                        // 'brand'=>'',
                        // 'cat_no'=>'',
                        // 'serial'=>'',
                        // 'size'=>'',
                        // 'color'=>'',
                        // 'composite_id'=>'',
                    ];
                }
            }

        // return response()->json($prlist);
        return response()->json([
            'varaints'=>$VariantData,
        ],200);
    }

    public function save_borrow(Request $request){
        $user_id = Auth::id();

        $validated['mbr_no'] = $request->mbr_no;
        $validated['borrow_date']= $request->borrow_date;
        $validated['borrow_time']= $request->borrow_time;
        $validated['borrowed_by_user']= $request->borrowers_name;
        $validated['borrowed_by_user_name']=User::where('id',$request->borrowers_name)->value('name');
        $validated['user_id']= $user_id;
        $borrow_id=BorrowHead::insertGetId($validated);

        $mbr = $request->mbr_no;
        $ser = explode("-",$mbr);
        $series = $ser[3];

        MBR::create([
            'year' => date("Y"),
            'series'=>$series
        ]);
        $item_list = $request->input('borrow_items');
        foreach(json_decode($item_list) as $il){
            $pr_id = ReceiveDetails::where('pr_no','=',$il->prno)->value('id');
            $variant_id = ReceiveItems::where('receive_details_id','=',$pr_id)->where('item_id','=',$il->itemid)->value('variant_id');

            if(empty($il->remarks)){
                $remarks = '';
            } else {
                $remarks=$il->remarks;
            }

            $borrow_det['borrow_head_id']=$borrow_id;
            $borrow_det['borrowed_by']=$il->borrowed_by;
            $borrow_det['borrowed_from']=$il->prno;
            $borrow_det['item_id']=$il->itemid;
            $borrow_det['item_description']=$il->item_desc;
            $borrow_det['variant_id']=$il->variantid;
            $borrow_det['avail_qty']=$il->avail_qty;
            $borrow_det['quantity']=$il->borrowed_qty;
            $borrow_det['balance']=$il->borrowed_qty;
            $borrow_det['department_id']=$il->dept_id;
            $borrow_det['department_name']=$il->department;
            $borrow_det['enduse_id']=$il->end_id;
            $borrow_det['enduse_name']=$il->enduse;
            $borrow_det['purpose_id']=$il->pur_id;
            $borrow_det['purpose_name']=$il->purpose;
            $borrow_det['remarks']=$remarks;
            BorrowDetails::create($borrow_det);

            $pritems_id = PRItems::where('pr_no','=',$il->prno)->where('item_id', '=', $il->itemid)->value('id');
            $update = PRItems::find($pritems_id);
            $update->borrow_deduct = $update->borrow_deduct + $il->borrowed_qty;
            $update->balance = $update->balance - $il->borrowed_qty;
            $update->save();

            if(PRItems::where('pr_no','=',$il->borrowed_by)->where('item_id', '=', $il->itemid)->exists()){
                $pritems_id = PRItems::where('pr_no','=',$il->borrowed_by)->where('item_id', '=', $il->itemid)->value('id');
                $update = PRItems::find($pritems_id);
                $update->borrow_add = $update->borrow_add + $il->borrowed_qty;
                $update->balance = $update->balance + $il->borrowed_qty;
                $update->save();
            } else {
                $itemdata['pr_no']=$il->borrowed_by;
                $itemdata['item_id']=$il->itemid;
                $itemdata['borrow_add']=$il->borrowed_qty;
                $itemdata['balance']=$il->borrowed_qty;
                PRItems::create($itemdata);
            }

            if(PIVBalance::where('pr_no','=',$il->borrowed_by)->where('item_id', '=', $il->itemid)->where('variant_id','=',$il->variantid)->exists()){
                $piv_id = PIVBalance::where('pr_no','=',$il->borrowed_by)->where('item_id', '=', $il->itemid)->where('variant_id','=',$il->variantid)->value('id');
                $update = PIVBalance::find($piv_id);
                $update->quantity = $update->quantity + $il->borrowed_qty;
                $update->save();
            } else {
                $pivdata['pr_no']=$il->borrowed_by;
                $pivdata['variant_id']=$il->variantid;
                $pivdata['item_id']=$il->itemid;
                $pivdata['quantity']=$il->borrowed_qty;
                PIVBalance::create($pivdata);
            }

            $head=BorrowHead::where('id',$borrow_id)->first();
            $data = [
                'saved'=>'1'
            ];
            $head->update($data);


            ///unit cost and shipping cost
            $c_flag = Items::where('id', '=', $il->itemid)->value('composite_flag');
            if(Variants::where('id', '=', $il->variantid)->where('item_id', '=', $il->itemid)->exists()) {
                $unit_cost = Variants::where('id', '=', $il->variantid)->where('item_id', '=', $il->itemid)->value('unit_cost');
                $shipping_cost = Variants::where('id', '=', $il->variantid)->where('item_id', '=', $il->itemid)->value('shipping_cost');
            } else if($c_flag != 0) {
                $unit_cost = Items::where('id', '=', $il->itemid)->value('composite_cost');
                $shipping_cost = 0;
            }else{
                $unit_cost = 0;
                $shipping_cost = 0;
            }

            /// composite items
            if(CompositeItems::where('compose_item_id','=',$il->itemid)->exists()){
                $composite_id = CompositeItems::where('compose_item_id','=',$il->itemid)->value('id');
            } else {
                $composite_id = 0;
            }

            /// Request for Borrowed By
            $curr_year = date('Y');
            $curr_mo = date('m');
            if(Mreqf::where('year', '=', $curr_year)->exists()) {
                $mreqf_by = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
                $max_value_by = str_pad($mreqf_by,4,"0",STR_PAD_LEFT);
            } else {
                $max_value_by = '0001';
            }

            $mreqf_no_by = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_by;

            if(!RequestHead::where('request_date',$request->borrow_date)->where('request_time',$request->borrow_time)->where('pr_no',$il->borrowed_by)->where('department_id',$il->dept_id)->where('enduse_id',$il->end_id)->where('purpose_id',$il->pur_id)->exists()){
                    $remarks_list = $request->input('head_remarks');
                    $pr_remarks=array();
                    foreach(json_decode($remarks_list) as $rl){
                        if(($rl->r_by == $il->borrowed_by && $rl->dept_by == $il->dept_id  && $rl->end_by == $il->end_id  && $rl->pur_by == $il->pur_id) && !is_array($rl->pr)){
                            $pr_remarks[$rl->pr]=$rl->pr;
                        }
                    }
                
                $borrowed_by_req['mreqf_no'] = $mreqf_no_by;
                $borrowed_by_req['request_date'] = $request->borrow_date;
                $borrowed_by_req['request_time'] = $request->borrow_time;
                $borrowed_by_req['request_type'] = 'With PR';
                $borrowed_by_req['pr_no'] = $il->borrowed_by;
                $borrowed_by_req['department_id']=$il->dept_id;
                $borrowed_by_req['department_name']=$il->department;
                $borrowed_by_req['enduse_id']=$il->end_id;
                $borrowed_by_req['enduse_name']=$il->enduse;
                $borrowed_by_req['purpose_id']=$il->pur_id;
                $borrowed_by_req['purpose_name']=$il->purpose;
                $borrowed_by_req['user_id']=$user_id;
                $borrowed_by_req['remarks'] ='Borrowed from '.implode(", ",$pr_remarks);
                $borrowed_by_req['saved'] = 1;
                $borrowed_by_req['borrowed_id'] = $borrow_id;
                $borrowed_by_req_id=RequestHead::insertGetId($borrowed_by_req);

                $mreqf_by = $mreqf_no_by;
                $ser_by = explode("-",$mreqf_by);
                $series_by = $ser_by[3];

                    Mreqf::create([
                        'year' => date("Y"),
                        'series'=>$series_by
                    ]);
            }

                $borrowed_by_req_items['request_head_id']=RequestHead::where('request_date',$request->borrow_date)->where('request_time',$request->borrow_time)->where('pr_no',$il->borrowed_by)->where('department_id',$il->dept_id)->where('enduse_id',$il->end_id)->where('purpose_id',$il->pur_id)->value('id');
                $borrowed_by_req_items['item_id']=$il->itemid;
                $borrowed_by_req_items['item_description']=$il->item_desc;
                $borrowed_by_req_items['variant_id']=$il->variantid;
                $borrowed_by_req_items['composite_id']=$composite_id;
                $borrowed_by_req_items['quantity']=$il->borrowed_qty;
                $borrowed_by_req_items['unit_cost']=$unit_cost;
                $borrowed_by_req_items['shipping_cost']=$shipping_cost;
                RequestItems::create($borrowed_by_req_items);
            
            

            /// Request for Borrowed from
            if(Mreqf::where('year', '=', $curr_year)->exists()) {
                $mreqf_from = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
                $max_value_from = str_pad($mreqf_from,4,"0",STR_PAD_LEFT);;
            } else {
                $max_value_from = '0001';
            }

            $mreqf_no_from = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_from;


            if(!RequestHead::where('request_date',$request->borrow_date)->where('request_time',$request->borrow_time)->where('pr_no',$il->prno)->where('department_id',$il->dept_id)->where('enduse_id',$il->end_id)->where('purpose_id',$il->pur_id)->exists()){
                if($il->prno == 'WH STOCKS'){
                    $request_type = 'WH Stocks';
                }else{
                    $request_type = 'With PR';
                }
                // $borrowed_from_remarks[] = $il->borrowed_by;
                // $all_borrowed_from_remarks = implode(", ",$borrowed_from_remarks);

                $borrowed_from_req['mreqf_no'] = $mreqf_no_from;
                $borrowed_from_req['request_date'] = $request->borrow_date;
                $borrowed_from_req['request_time'] = $request->borrow_time;
                $borrowed_from_req['request_type'] = $request_type;
                $borrowed_from_req['pr_no'] = $il->prno;
                $borrowed_from_req['department_id']=$il->dept_id;
                $borrowed_from_req['department_name']=$il->department;
                $borrowed_from_req['enduse_id']=$il->end_id;
                $borrowed_from_req['enduse_name']=$il->enduse;
                $borrowed_from_req['purpose_id']=$il->pur_id;
                $borrowed_from_req['purpose_name']=$il->purpose;
                $borrowed_from_req['user_id']=$user_id;
                $borrowed_from_req['remarks'] ='Borrowed by '.$il->borrowed_by;
                $borrowed_from_req['saved'] = 1;
                $borrowed_from_req['close'] = 1;
                $borrowed_from_req['borrowed_id'] = $borrow_id;
                $borrowed_from_req_id=RequestHead::insertGetId($borrowed_from_req);

                $mreqf_from = $mreqf_no_from;
                $ser_from = explode("-",$mreqf_from);
                $series_from = $ser_from[3];

                Mreqf::create([
                    'year' => date("Y"),
                    'series'=>$series_from
                ]);

            }
            
                $borrowed_from_req_items['request_head_id']= RequestHead::where('request_date',$request->borrow_date)->where('request_time',$request->borrow_time)->where('pr_no',$il->prno)->where('department_id',$il->dept_id)->where('enduse_id',$il->end_id)->where('purpose_id',$il->pur_id)->value('id');
                $borrowed_from_req_items['item_id']=$il->itemid;
                $borrowed_from_req_items['item_description']=$il->item_desc;
                $borrowed_from_req_items['variant_id']=$il->variantid;
                $borrowed_from_req_items['composite_id']=$composite_id;
                $borrowed_from_req_items['quantity']=$il->borrowed_qty;
                $borrowed_from_req_items['issued_qty']=$il->borrowed_qty;
                $borrowed_from_req_items['unit_cost']=$unit_cost;
                $borrowed_from_req_items['shipping_cost']=$shipping_cost;
                // RequestItems::create($borrowed_from_req_items);
                $borrowed_from_iss_itemid=RequestItems::insertGetId($borrowed_from_req_items);
            

                //// Issuance Borrowed
                if(MIF::where('year', '=', $curr_year)->exists()) {
                    $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
                    $max_value = str_pad($mif,4,"0",STR_PAD_LEFT);;
                } else {
                    $max_value = '0001';
                }
        
                $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
            
            if(!IssuanceHead::where('issuance_date',$request->borrow_date)->where('issuance_time',$request->borrow_time)->where('pr_no',$il->prno)->where('department_id',$il->dept_id)->where('enduse_id',$il->end_id)->where('purpose_id',$il->pur_id)->exists()){
                $borrowed_from_iss['request_head_id'] = $borrowed_from_req_id;
                $borrowed_from_iss['mreqf_no'] = RequestHead::where('id', '=', $borrowed_from_req_id)->value('mreqf_no');
                $borrowed_from_iss['mif_no'] = $mif_no;
                $borrowed_from_iss['pr_no'] = $il->prno;
                $borrowed_from_iss['issuance_date'] = $request->borrow_date;
                $borrowed_from_iss['issuance_time'] = $request->borrow_time;
                $borrowed_from_iss['department_id']=$il->dept_id;
                $borrowed_from_iss['department_name']=$il->department;
                $borrowed_from_iss['purpose_id']=$il->pur_id;
                $borrowed_from_iss['purpose_name']=$il->purpose;
                $borrowed_from_iss['enduse_id']=$il->end_id;
                $borrowed_from_iss['enduse_name']=$il->enduse;
                $borrowed_from_iss['user_id']=$user_id;
                $borrowed_from_iss['remarks'] =RequestHead::where('id', '=', $borrowed_from_req_id)->value('remarks');
                $borrowed_from_iss['saved'] = 1;
                $borrowed_from_iss_id=IssuanceHead::insertGetId($borrowed_from_iss);

                $ser = explode("-",$mif_no);
                $series = $ser[3];
        
                MIF::create([
                    'year' => date("Y"),
                    'series'=>$series
                ]);
            }

                $borrowed_from_iss_items['issuance_head_id']=IssuanceHead::where('issuance_date',$request->borrow_date)->where('issuance_time',$request->borrow_time)->where('pr_no',$il->prno)->where('department_id',$il->dept_id)->where('enduse_id',$il->end_id)->where('purpose_id',$il->pur_id)->value('id');
                $borrowed_from_iss_items['item_id']=$il->itemid;
                $borrowed_from_iss_items['item_description']=$il->item_desc;
                $borrowed_from_iss_items['variant_id']=$il->variantid;
                $borrowed_from_iss_items['composite_item_id']=$composite_id;
                $borrowed_from_iss_items['request_items_id']=$borrowed_from_iss_itemid;
                $borrowed_from_iss_items['inventory_balance']=Items::where('id','=',$il->itemid)->value('running_balance');
                $borrowed_from_iss_items['request_qty']=$il->borrowed_qty;
                $borrowed_from_iss_items['issued_qty']=$il->borrowed_qty;
                $borrowed_from_iss_items['unit_cost']=$unit_cost;
                $borrowed_from_iss_items['shipping_cost']=$shipping_cost;
                $borrowed_from_iss_items['remarks']=$remarks;
                IssuanceItems::create($borrowed_from_iss_items);

            if($composite_id!=0 && $il->variantid!=0){   ///// part of the composite item
                $compo_qty = CompositeItems::where("id","=",$composite_id)->value('quantity');
                $issued_item_id = CompositeItems::where("id","=",$composite_id)->value('compose_item_id');
                $issued_variant_id = CompositeItems::where("id","=",$composite_id)->value('variant_id');
              
                    $compoitems=CompositeItems::find($composite_id);
                    $compoitems->quantity =  $compoitems->quantity - $il->borrowed_qty;
                    $compoitems->save();
                
                $pritems_id = PRItems::where('pr_no','=',$il->prno)->where('item_id', '=', $il->itemid)->value('id');
                $update = PRItems::find($pritems_id);
                $update->composite_qty = $update->composite_qty - $il->borrowed_qty;
                // $update->issuance_qty = $update->issuance_qty + $il->borrowed_qty;
                $update->save();

                $varbal_id = VariantsBalance::where('variant_id','=',$il->variantid)->where('item_id', '=', $il->itemid)->value('id');
                $updatevar = VariantsBalance::find($varbal_id);
                $updatevar->composite_qty = $updatevar->composite_qty - $il->borrowed_qty;
                // $updatevar->issuance_qty = $updatevar->issuance_qty + $il->borrowed_qty;
                $updatevar->save();

                $piv_id = PIVBalance::where('pr_no','=',$il->prno)->where('item_id', '=', $il->itemid)->where('variant_id','=',$il->variantid)->value('id');
                    
                    $update = PIVBalance::find($piv_id);
                    $update->quantity = $update->quantity - $il->borrowed_qty;
                    $update->save();
            
            } else if($composite_id==0 && $il->variantid!=0){ /// single item, not composite but with variant

            //     $update_balance = Items::find($il->itemid);
            //     $update_balance->running_balance = $update_balance->running_balance - $il->borrowed_qty;
            //     $update_balance->save();

            //     // $pritems_id = PRItems::where('pr_no','=',$il->prno)->where('item_id', '=', $il->itemid)->value('id');
            //     // $update = PRItems::find($pritems_id);
            //     // $update->issuance_qty = $update->issuance_qty + $il->borrowed_qty;
            //     // $update->balance = $update->balance - $il->borrowed_qty;
            //     // $update->save();

            //     // $varbal_id = VariantsBalance::where('variant_id','=',$il->variantid)->where('item_id', '=', $il->itemid)->value('id');
            //     // $updatevar = VariantsBalance::find($varbal_id);
            //     // $updatevar->issuance_qty = $updatevar->issuance_qty + $il->borrowed_qty;
            //     // $updatevar->balance = $updatevar->balance - $il->borrowed_qty;
            //     // $updatevar->save();

            //     $updatevariant = Variants::find($il->variantid);
            //     $updatevariant->quantity = $updatevariant->quantity - $il->borrowed_qty;
            //     $updatevariant->save();

            $piv_id = PIVBalance::where('pr_no','=',$il->prno)->where('item_id', '=', $il->itemid)->where('variant_id','=',$il->variantid)->value('id');
                    
                    $update = PIVBalance::find($piv_id);
                    $update->quantity = $update->quantity - $il->borrowed_qty;
                    $update->save();

            } else if($composite_id==0 && $il->variantid==0){ ///// all composite item

                // $update_balance = Items::find($il->itemid);
                // $update_balance->running_balance = $update_balance->running_balance - $il->borrowed_qty;
                // $update_balance->save();

                $all_compo = CompositeItems::where('item_id','=', $il->itemid)->where('quantity','!=', '0')->get();
       
                $parent_pritems_id = PRItems::where('pr_no','=',$il->prno)->where('item_id', '=', $il->itemid)->value('id');

                // $updatePR = PRItems::find($parent_pritems_id);
                // $updatePR->issuance_qty = $updatePR->issuance_qty + $il->borrowed_qty;
                // $updatePR->balance = $updatePR->balance - $il->borrowed_qty;
                // $updatePR->save();

                // $parent_varbal_id = VariantsBalance::where('item_id', '=', $il->itemid)->value('id');
                   
                // $updatevarparent = VariantsBalance::find($parent_varbal_id);
                // $updatevarparent->issuance_qty = $updatevarparent->issuance_qty + $il->borrowed_qty;
                // $updatevarparent->balance = $updatevarparent->balance - $il->borrowed_qty;
                
                // $updatevarparent->save();

                $piv_id = PIVBalance::where('pr_no','=',$il->prno)->where('item_id', '=', $il->itemid)->where('variant_id','=',$il->variantid)->value('id');
                    
                    $update = PIVBalance::find($piv_id);
                    $update->quantity = $update->quantity - $il->borrowed_qty;
                    $update->save();
              
                 foreach($all_compo AS $ac){
                 
                    $pritems_id = PRItems::where('pr_no','=',$il->prno)->where('item_id', '=', $ac->compose_item_id)->value('id');
                    $update = PRItems::find($pritems_id);
                    $update->composite_qty = $update->composite_qty - $il->borrowed_qty;
                    // $update->issuance_qty = $update->issuance_qty + $il->borrowed_qty;
                    $update->save();


                    $varbal_id = VariantsBalance::where('variant_id','=',$ac->variant_id)->where('item_id', '=', $ac->compose_item_id)->value('id');
                    $updatevar = VariantsBalance::find($varbal_id);
                    $updatevar->composite_qty = $updatevar->composite_qty - $il->borrowed_qty;
                    // $updatevar->issuance_qty = $updatevar->issuance_qty + $il->borrowed_qty;
                    $updatevar->save();
                  
                }

            } else if($composite_id!=0 && $il->variantid==0){ ///// part of the composite begbal

                $compo_qty = CompositeItems::where("id","=",$composite_id)->value('quantity');
                $issued_item_id = CompositeItems::where("id","=",$composite_id)->value('compose_item_id');
                $issued_variant_id = CompositeItems::where("id","=",$composite_id)->value('variant_id');
              
                    $compoitems=CompositeItems::find($composite_id);
                    $compoitems->quantity =  $compoitems->quantity - $il->borrowed_qty;
                    $compoitems->save();
                
                $pritems_id = PRItems::where('pr_no','=',$il->prno)->where('item_id', '=', $issued_item_id)->value('id');
                $update = PRItems::find($pritems_id);
                $update->composite_qty = $update->composite_qty - $il->borrowed_qty;
                // $update->issuance_qty = $update->issuance_qty + $il->borrowed_qty;
                $update->save();

                $piv_id = PIVBalance::where('pr_no','=',$il->prno)->where('item_id', '=', $il->itemid)->where('variant_id','=',$il->variantid)->value('id');
                    
                    $update = PIVBalance::find($piv_id);
                    $update->quantity = $update->quantity - $il->borrowed_qty;
                    $update->save();

            }
        }
            return $borrow_id;
    }

    public function get_borrow_head($id){
        $head = BorrowHead::find($id);
        $details = BorrowDetails::find($id);
        $user_id=BorrowHead::where('id',$id)->value('user_id');
        $prepared_by=User::where('id',$user_id)->value('name');
        $prep_position=User::where('id',$user_id)->value('position');
        $requested_id=BorrowHead::where('id',$id)->value('requested_by');
        $req_position=User::where('id',$requested_id)->value('position');
        $reviewed_id=BorrowHead::where('id',$id)->value('reviewed_by');
        $rev_position=User::where('id',$reviewed_id)->value('position');
        $approved_id=BorrowHead::where('id',$id)->value('approved_by');
        $app_position=User::where('id',$approved_id)->value('position');
        $noted_id=BorrowHead::where('id',$id)->value('noted_by');
        $noted_position=User::where('id',$noted_id)->value('position');

        // $request_items = RequestHead::where('borrowed_id',$id)->get();
        $request_items = RequestHead::select('id','pr_no','mreqf_no')->where("borrowed_id", "=", $id)->get();
        foreach($request_items AS $req){
            $issuance = IssuanceHead::select('id','pr_no','mif_no')->where("request_head_id", "=", $req->id)->get();

            foreach($issuance AS $iss){
                $issuance_items[] = [
                    'id'=>$iss->id,
                    'pr_no'=>strtoupper($iss->pr_no),
                    'mif_no'=>$iss->mif_no,
                ];
            }
        }

        return response()->json([
            'head'=>$head,
            'user_id'=>$user_id,
            'prepared_by'=>$prepared_by,
            'prep_position'=>$prep_position,
            'req_position'=>$req_position,
            'rev_position'=>$rev_position,
            'app_position'=>$app_position,
            'noted_position'=>$noted_position,
            'request_items'=>$request_items,
            'issuance_items'=>$issuance_items,
        ],200);
    }

    public function get_borrow_details(Request $request, $id){
        $formData=array();
        $detail = BorrowDetails::where('borrow_head_id', '=', $id)->get();
        $count=$detail->count();
        $remarks=array();
        foreach($detail AS $det){
            $remarks[]=$det->item_description."- ".$det->remarks;
            if($count != 0){
                $formData[]= [
                    'borrowed_by'=>$det->borrowed_by,
                    'borrowed_from'=>$det->borrowed_from,
                    'item_description'=>$det->item_description,
                    'avail_qty'=>$det->avail_qty,
                    'quantity'=>$det->quantity,
                    'department'=>$det->department_name,
                    'enduse'=>$det->enduse_name,
                    'purpose'=>$det->purpose_name,
                    'remarks'=>implode(", ",$remarks),
                    'remarks_item'=>$det->remarks,
                ];
            }
        }
        return response()->json([
            'details'=>$formData,
        ],200);
    }

    public function get_print_details(Request $request, $id){

        $formData=array();
        $detail = BorrowDetails::where('borrow_head_id', '=', $id)->groupBy('borrowed_by','department_id','purpose_id','enduse_id')->get();
        foreach($detail AS $det){
            $items = BorrowDetails::where('borrow_head_id', '=', $id)->where('borrowed_by', '=', $det->borrowed_by)->where('department_id', '=', $det->department_id)->where('purpose_id', '=', $det->purpose_id)->where('enduse_id', '=', $det->enduse_id)->get();
            $count=$items->count();

            $remarks=array();
            foreach($items AS $i){
                $remarks[]=$i->item_description."- ".$i->remarks;
            }
            if($count != 0){
                $formData[]= [
                    'department'=>$det->department_name,
                    'enduse'=>$det->enduse_name,
                    'purpose'=>$det->purpose_name,
                    'remarks'=>implode(", ",$remarks),
                    'borrow_items'=> [
                        'items'=>$items
                    ]
                ];
            }
            
        }

        return response()->json([
            'details'=>$formData,
        ],200);
    }

    public function add_borrow_signatory(Request $request){
        $update_data=BorrowHead::where('id',$request->id)->first();
        //if($request->requested_by == 0 && $request->reviewed_by == 0 && $request->approved_by == 0 && $request->noted_by == 0){
            $validated=[
                'prepared_by'=>$request->user_id,
                'prepared_by_name'=>User::where('id',$request->user_id)->value('name'),
                'prepared_by_position'=>User::where('id',$request->user_id)->value('position'),
                'requested_by'=>($request->requested_by != 'null') ? $request->requested_by : '',
                'requested_by_name'=>User::where('id',$request->requested_by)->value('name'),
                'requested_by_position'=> ($request->req_position != 'null') ? $request->req_position : '',
                // 'requested_by_position'=>User::where('id',$request->requested_by)->value('position'),
                'reviewed_by'=> ($request->reviewed_by != 'null') ? $request->reviewed_by : '',
                'reviewed_by_name'=>User::where('id',$request->reviewed_by)->value('name'),
                // 'reviewed_by_position'=>User::where('id',$request->reviewed_by)->value('position'),
                'reviewed_by_position'=> ($request->rev_position != 'null') ? $request->rev_position : '',
                'approved_by'=> ($request->approved_by != 'null') ? $request->approved_by : '',
                'approved_by_name'=>User::where('id',$request->approved_by)->value('name'),
                'approved_by_position'=>($request->app_position != 'null') ? $request->app_position : '',
                // 'approved_by_position'=>User::where('id',$request->approved_by)->value('position'),
                'noted_by'=> ($request->approved_by != 'null') ? $request->noted_by : '',
                'noted_by_name'=>User::where('id',$request->noted_by)->value('name'),
                'noted_by_position'=>($request->noted_position != 'null') ? $request->noted_position : '',
                // 'noted_by_position'=>User::where('id',$request->noted_by)->value('position'),
            ];
            $update_data->update($validated);
        //}       
    }   

    public function get_borrow_position($id){
        $position = User::where("id", $id)->value('position');
        return $position;
    }

}
