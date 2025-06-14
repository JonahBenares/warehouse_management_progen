<?php

namespace App\Http\Controllers;

use App\Models\MIF;
use App\Models\MGP;
use App\Models\RequestHead;
use App\Models\RequestItems;
use App\Models\Items;
use App\Models\PRItems;
use App\Models\IssuanceHead;
use App\Models\IssuanceItems;
use App\Models\Variants;
use App\Models\VariantsBalance;
use App\Models\CompositeItems;
use App\Models\PIVBalance;
use App\Http\Requests\IssuanceRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class IssuanceController extends Controller
{
    public function create_issuance_head(){
        $curr_year = date('Y');
        $curr_mo = date('m');
        if(MIF::where('year', '=', $curr_year)->exists()) {
            $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
            $max_value = str_pad($mif,4,"0",STR_PAD_LEFT);;
        } else {
            $max_value = '0001';
        }

        $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
        
        $request_no = RequestHead::where('close','=','0')->where('saved','=','1')->get();

        $id = Auth::id();
        $prepared_by=User::where('id',$id)->value('name');
        $prepared_des=User::where('id',$id)->value('position');

        $formData=[
            'mif_no'=>$mif_no,
            'issue_date'=>date("Y-m-d"),
            'issue_time'=>date("H:i:s"),
            
            'department'=>'',
            'enduse'=>'',
            'purpose'=>'',
            'pr_no'=>'',
            'user_id'=>$id,
            'prepared_by'=>$id,
            'prepared_by_name'=>$prepared_by,
            'prepared_by_pos'=>$prepared_des,
            'remarks'=>''
        ];
        return response()->json([
          'request_nos'=>$request_no,
          'formData'=>$formData
        ],200);
    }

    public function get_request_data($mreqf_no){

        $id = Auth::id();
        $prepared_by=User::where('id',$id)->value('name');
        $prepared_des=User::where('id',$id)->value('position');
         $curr_year = date('Y');
         $curr_mo = date('m');
         if(MIF::where('year', '=', $curr_year)->exists()) {
             $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
             $max_value = str_pad($mif,4,"0",STR_PAD_LEFT);;
         } else {
             $max_value = '0001';
         }
 
         $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
        
      

        if(RequestHead::where('mreqf_no','=',$mreqf_no)->exists()){

            $pr_no =  RequestHead::where('mreqf_no','=',$mreqf_no)->value('pr_no');
            $request = RequestHead::where('mreqf_no','=',$mreqf_no)->get();
            foreach($request AS $req){
                $formData=[
                    'request_head_id'=>$req->id,
                    'mreqf_no'=>$mreqf_no,
                    'mif_no'=>$mif_no,
                    'issuance_date'=>date("Y-m-d"),
                    'issuance_time'=>date("H:i:s"),
                    'department_id'=>$req->department_id,
                    'enduse_id'=>$req->enduse_id,
                    'purpose_id'=>$req->purpose_id,
                    'department_name'=>$req->department_name,
                    'enduse_name'=>$req->enduse_name,
                    'purpose_name'=>$req->purpose_name,
                    'pr_no'=>strtoupper($req->pr_no),
                    'remarks'=>'',
                    'user_id'=>$id,
                    'prepared_by'=>$id,
                    'prepared_by_name'=>$prepared_by,
                    'prepared_by_pos'=>$prepared_des,
                ];
            }
        } else {
            $pr_no='';
            $formData=[
                'request_head_id'=>'',
                'mreqf_no'=>'',
                'mif_no'=>$mif_no,
                'issuance_date'=>date("Y-m-d"),
                'issuance_time'=>date("H:i:s"),
                'department_name'=>'',
                'enduse_name'=>'',
                'purpose_name'=>'',
                'remarks'=>'',
                'pr_no'=>'',
                'user_id'=>$id,
                'prepared_by'=>$id,
                'prepared_by_name'=>$prepared_by,
                'prepared_by_pos'=>$prepared_des,
            ];
        }
        $query = RequestItems::with(['request_head','variants']);
        $query->whereHas('request_head', function ($query) use($mreqf_no) {
            $query->where('mreqf_no', $mreqf_no);
        });
        $request_data =  $query->get();

        foreach($request_data AS $ri){

            if($ri->variant_id != 0 && $ri->composite_id == 0) {  //////// NOT COMPOSITE, WITH VARIANT

                $instock = Items::where('id','=',$ri->item_id)->value('running_balance');
                $pr_qty = PRItems::where('item_id', '=',$ri->item_id)->where('pr_no','=',$pr_no)->value('balance');
                $item_description=$ri->item_description;
                $request_qty = $ri->quantity-$ri->issued_qty;
                if($request_qty > 0){
                    $itemData[] = [ 
                        'request_items_id'=>$ri->id,
                        'instock'=>$instock,
                        'pr_qty'=>$pr_qty,
                        'request_qty'=>$request_qty,
                        'issue_qty'=>0,
                        'supplier'=>$ri->variants->supplier_name,
                        'item_id'=>$ri->item_id,
                        'composite_item_id'=>'0',
                        'item_description'=>$ri->item_description,
                        'item_description_display'=>$item_description,
                        'unit_cost'=>$ri->unit_cost,
                        'currency'=>$ri->currency,
                        'shipping_cost'=>$ri->shipping_cost,
                        'uom'=>$ri->variants->uom,
                        'catalog_no'=>$ri->variants->catalog_no,
                        'brand'=>$ri->variants->brand,
                        'color'=>$ri->variants->color,
                        'size'=>$ri->variants->size,
                        'serial_no'=>$ri->variants->serial_no,
                        'item_status'=>$ri->variants->item_status->status,
                        'expiry_date'=>$ri->variants->expiration,
                        'variant_id'=>$ri->variant_id,
                        'remarks'=>$ri->remarks
                    
                    ];
                }
            } else if($ri->variant_id != 0 && $ri->composite_id != 0){ //// PART OF THE COMPOSITE WITH VARIANT
                $requested_item = CompositeItems::where('id','=', $ri->composite_id)->value('compose_item_id');
                $requested_item_name = Items::where('id','=', $requested_item)->value('item_description');
                //$instock = Items::where('id','=',$requested_item)->value('running_balance');
                $instock = CompositeItems::where('compose_item_id','=',$requested_item)->where('item_id','=',$ri->item_id)->value('quantity');
                $item_description=$ri->item_description . " - " .$requested_item_name;

                $request_qty = $ri->quantity-$ri->issued_qty;
                if($request_qty > 0){
                    $itemData[] = [ 
                        'request_items_id'=>$ri->id,
                        'instock'=>$instock,
                        'request_qty'=>$request_qty,
                        'issue_qty'=>0,
                        'supplier'=>$ri->variants->supplier_name,
                        'item_id'=>$ri->item_id,
                        'composite_item_id'=>$ri->composite_id,
                        'item_description'=>$ri->item_description,
                        'item_description_display'=>$item_description,
                        'unit_cost'=>$ri->unit_cost,
                        'shipping_cost'=>$ri->shipping_cost,
                        'uom'=>$ri->variants->uom,
                        'catalog_no'=>$ri->variants->catalog_no,
                        'brand'=>$ri->variants->brand,
                        'color'=>$ri->variants->color,
                        'size'=>$ri->variants->size,
                        'serial_no'=>$ri->variants->serial_no,
                        'item_status'=>$ri->variants->item_status->status,
                        'expiry_date'=>$ri->variants->expiration,
                        'variant_id'=>$ri->variant_id,
                        'remarks'=>$ri->remarks
                    
                    ];
                }
            } else if($ri->variant_id == 0 && $ri->composite_id == 0){ ///// ALL COMPOSITE ITEMS
 
                    $instock = Items::where('id','=',$ri->item_id)->value('running_balance');

                    if(CompositeItems::where('item_id','=',$ri->item_id)->exists()){
                        $all_compo = CompositeItems::with('items')
                        ->where('item_id','=', $ri->item_id)->where('quantity','!=','0')->get();
                            $item_description = $ri->item_description . " (";

                            foreach($all_compo AS $ac){
                                
                                $item_description .= $ac->items->item_description . ", ";
                            }
                            
                            $item_description = substr_replace($item_description, '', -2);
                            $item_description .= ") ";
                    } else {
                        $item_description = $ri->item_description;
                    }

                   
                    $request_qty = $ri->quantity-$ri->issued_qty;

                    if($request_qty > 0){
                        $itemData[] = [ 
                            'request_items_id'=>$ri->id,
                            'instock'=>$instock,
                            'request_qty'=>$request_qty,
                            'issue_qty'=>0,
                            'supplier'=>'',
                            'item_id'=>$ri->item_id,
                            'item_description'=>$ri->item_description,
                            'item_description_display'=>$item_description,
                            'unit_cost'=>$ri->unit_cost,
                            'shipping_cost'=>$ri->shipping_cost,
                            'composite_item_id'=>'0',
                            'uom'=>'',
                            'catalog_no'=>'',
                            'brand'=>'',
                            'color'=>'',
                            'size'=>'',
                            'serial_no'=>'',
                            'item_status'=>'',
                            'expiry_date'=>'',
                            'variant_id'=>'',
                            'remarks'=>''
                        
                        ];
                    }

            } else if($ri->variant_id == 0 && $ri->composite_id != 0){ ////BEGBAL

                $requested_item = CompositeItems::where('id','=', $ri->composite_id)->value('compose_item_id');
                $requested_item_name = Items::where('id','=', $requested_item)->value('item_description');
                $instock = Items::where('id','=',$requested_item)->value('running_balance');
                $item_description=$ri->item_description . " - " .$requested_item_name;
                $request_qty = $ri->quantity-$ri->issued_qty;

                if($request_qty > 0){
                    $itemData[] = [ 
                        'request_items_id'=>$ri->id,
                        'instock'=>$instock,
                        'request_qty'=>$request_qty,
                        'issue_qty'=>0,
                        'supplier'=>'',
                        'item_id'=>$ri->item_id,
                        'item_description'=>$ri->item_description,
                        'item_description_display'=>$item_description,
                        'unit_cost'=>$ri->unit_cost,
                        'shipping_cost'=>$ri->shipping_cost,
                        'composite_item_id'=>$ri->composite_id,
                        'uom'=>'',
                        'catalog_no'=>'',
                        'brand'=>'',
                        'color'=>'',
                        'size'=>'',
                        'serial_no'=>'',
                        'item_status'=>'',
                        'expiry_date'=>'',
                        'variant_id'=>'',
                        'remarks'=>''
                    
                    ];
                }
            }
         
            // if($ri->variant_id != 0){
            //     if($ri->composite_id == 0){
            //             $instock = Items::where('id','=',$ri->item_id)->value('running_balance');
            //             $item_description=$ri->item_description;
            //         } else {
            //             $requested_item = CompositeItems::where('id','=', $ri->composite_id)->value('compose_item_id');
            //             $requested_item_name = Items::where('id','=', $requested_item)->value('item_description');
            //             $instock = Items::where('id','=',$requested_item)->value('running_balance');
            //             $item_description=$ri->item_description . " - " .$requested_item_name;
            //         }
            //             $request_qty = $ri->quantity-$ri->issued_qty;

            //             if($request_qty > 0){
            //                 $itemData[] = [ 
            //                     'request_items_id'=>$ri->id,
            //                     'instock'=>$instock,
            //                     'request_qty'=>$request_qty,
            //                     'issue_qty'=>0,
            //                     'supplier'=>$ri->variants->supplier_name,
            //                     'item_id'=>$ri->item_id,
            //                     'composite_item_id'=>$ri->composite_id,
            //                     'item_description'=>$ri->item_description,
            //                     'item_description_display'=>$item_description,
            //                     'unit_cost'=>$ri->unit_cost,
            //                     'shipping_cost'=>$ri->shipping_cost,
            //                     'uom'=>$ri->variants->uom,
            //                     'catalog_no'=>$ri->variants->catalog_no,
            //                     'brand'=>$ri->variants->brand,
            //                     'color'=>$ri->variants->color,
            //                     'size'=>$ri->variants->size,
            //                     'serial_no'=>$ri->variants->serial_no,
            //                     'item_status'=>$ri->variants->item_status->status,
            //                     'expiry_date'=>$ri->variants->expiration,
            //                     'variant_id'=>$ri->variant_id,
            //                     'remarks'=>$ri->remarks
                            
            //                 ];
            //             }
            
            // } else {
            //     if($ri->composite_id == 0){
            //         $instock = Items::where('id','=',$ri->item_id)->value('running_balance');
            //         $item_description=$ri->item_description;
            //     } else {
            //         $requested_item = CompositeItems::where('id','=', $ri->composite_id)->value('compose_item_id');
            //         $requested_item_name = Items::where('id','=', $requested_item)->value('item_description');
            //         $instock = Items::where('id','=',$requested_item)->value('running_balance');
            //         $item_description=$ri->item_description . " - " .$requested_item_name;
            //     }
            //         $request_qty = $ri->quantity-$ri->issued_qty;

            //         if($request_qty > 0){
            //             $itemData[] = [ 
            //                 'request_items_id'=>$ri->id,
            //                 'instock'=>$instock,
            //                 'request_qty'=>$request_qty,
            //                 'issue_qty'=>0,
            //                 'supplier'=>'',
            //                 'item_id'=>$ri->item_id,
            //                 'item_description'=>$ri->item_description,
            //                 'item_description_display'=>$item_description,
            //                 'unit_cost'=>$ri->unit_cost,
            //                 'shipping_cost'=>$ri->shipping_cost,
            //                 'composite_item_id'=>'',
            //                 'uom'=>'',
            //                 'catalog_no'=>'',
            //                 'brand'=>'',
            //                 'color'=>'',
            //                 'size'=>'',
            //                 'serial_no'=>'',
            //                 'item_status'=>'',
            //                 'expiry_date'=>'',
            //                 'variant_id'=>'',
            //                 'remarks'=>''
                        
            //             ];
            //         }
            // }

        }
     
         return response()->json([
             'formData'=>$formData,
             'itemData' => $itemData
         ],200);
       
    }

    public function save_issuance(IssuanceRequest $request){

        $items = $request->input('issue_items');
       
        $validated = $request->validated();

        $mif_no = $request->mif_no;
        $mreqf_no = $request->mreqf_no;
        $pr_no = strtoupper($request->pr_no);
        $ser = explode("-",$mif_no);
        $series = $ser[3];

        MIF::create([
            'year' => date("Y"),
            'series'=>$series
        ]);

        $issuance_id = IssuanceHead::insertGetId($validated);
        $req_qty=0;
        $iss_qty=0;
        $pr_qty=0;
        foreach(json_decode($items) as $i){
            
            if(empty($i->remarks)){
                $remarks = '';
            } else {
                $remarks=$i->remarks;
            }

            if(empty($i->variant_id)){
                $variant_id = 0;
            } else {
                $variant_id=$i->variant_id;
            }

            if(empty($i->composite_item_id)){
                $composite_item_id = 0;
            } else {
                $composite_item_id=$i->composite_item_id;
            }

            if(empty($i->pr_qty)){
                $pr_qty = 0;
            } else {
                $pr_qty=$i->pr_qty;
            }

            if($i->issue_qty>0){

                $itemData['issuance_head_id'] = $issuance_id;
                $itemData['item_id'] = $i->item_id;
                $itemData['item_description'] = $i->item_description;
                $itemData['composite_item_id'] = $composite_item_id;
                $itemData['variant_id'] = $variant_id;
                $itemData['request_items_id'] = $i->request_items_id;
                $itemData['inventory_balance'] = $i->instock;
                $itemData['request_qty'] = $i->request_qty;
                $itemData['pr_qty'] = $pr_qty;
                $itemData['issued_qty'] = $i->issue_qty;
                $itemData['unit_cost'] = $i->unit_cost;
                $itemData['currency'] = $i->currency ?? '';
                $itemData['shipping_cost'] = $i->shipping_cost;
                $itemData['remarks'] = $remarks;
            
                IssuanceItems::create($itemData);
            
                $updateRequest = RequestItems::find($i->request_items_id);
                $updateRequest->issued_qty = $updateRequest->issued_qty + $i->issue_qty;
                $updateRequest->save();

              
             
                $composite_id = RequestItems::where("id","=",$i->request_items_id)->value('composite_id');
             
                if($composite_id!=0 && $variant_id!=0){   ///// part of the composite item
                    $compo_qty = CompositeItems::where("id","=",$composite_id)->value('quantity');
                    $issued_item_id = CompositeItems::where("id","=",$composite_id)->value('compose_item_id');
                    $issued_variant_id = CompositeItems::where("id","=",$composite_id)->value('variant_id');
                  
                        $compoitems=CompositeItems::find($composite_id);
                        $compoitems->quantity =  $compoitems->quantity - $i->issue_qty;
                        $compoitems->save();
                    
                    $pritems_id = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $issued_item_id)->value('id');
                    $update = PRItems::find($pritems_id);
                    $update->composite_qty = $update->composite_qty - $i->issue_qty;
                    $update->issuance_qty = $update->issuance_qty + $i->issue_qty;
                    $update->save();

                    $varbal_id = VariantsBalance::where('variant_id','=',$issued_variant_id)->where('item_id', '=', $issued_item_id)->value('id');
                    $updatevar = VariantsBalance::find($varbal_id);
                    $updatevar->composite_qty = $updatevar->composite_qty - $i->issue_qty;
                    $updatevar->issuance_qty = $updatevar->issuance_qty + $i->issue_qty;
                    $updatevar->save();

                    
                    
                    $piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $issued_item_id)->where('variant_id','=',$issued_variant_id)->value('id');
                    
                    $update = PIVBalance::find($piv_id);
                    $update->quantity = $update->quantity - $i->issue_qty;
                    $update->save();
                  

                } else if($composite_id==0 && $variant_id!=0){ /// single item, not composite but with variant

                    $update_balance = Items::find($i->item_id);
                    $update_balance->running_balance = $update_balance->running_balance - $i->issue_qty;
                    $update_balance->save();

                    $pritems_id = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $i->item_id)->value('id');
                    $update = PRItems::find($pritems_id);
                    $update->issuance_qty = $update->issuance_qty + $i->issue_qty;
                    $update->balance = $update->balance - $i->issue_qty;
                    $update->save();

                    $varbal_id = VariantsBalance::where('variant_id','=',$i->variant_id)->where('item_id', '=', $i->item_id)->value('id');
                    $updatevar = VariantsBalance::find($varbal_id);
                    $updatevar->issuance_qty = $updatevar->issuance_qty + $i->issue_qty;
                    $updatevar->balance = $updatevar->balance - $i->issue_qty;
                    $updatevar->save();
    
                    $updatevariant = Variants::find($i->variant_id);
                    $updatevariant->quantity = $updatevariant->quantity - $i->issue_qty;
                    $updatevariant->save();

                    $piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $i->item_id)->where('variant_id','=',$i->variant_id)->value('id');
                    
                    $updatePIV = PIVBalance::find($piv_id);
                    $updatePIV->quantity = $updatePIV->quantity - $i->issue_qty;
                    $updatePIV->save();
                    
                    
                } else if($composite_id==0 && $variant_id==0){ ///// all composite item

                    $update_balance = Items::find($i->item_id);
                    $update_balance->running_balance = $update_balance->running_balance - $i->issue_qty;
                    $update_balance->save();

                    $all_compo = CompositeItems::where('item_id','=', $i->item_id)->where('quantity','!=', '0')->get();
           
                    $parent_pritems_id = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $i->item_id)->value('id');

                    $updatePR = PRItems::find($parent_pritems_id);
                    $updatePR->issuance_qty = $updatePR->issuance_qty + $i->issue_qty;
                    $updatePR->balance = $updatePR->balance - $i->issue_qty;
                    $updatePR->save();

                    $parent_varbal_id = VariantsBalance::where('item_id', '=', $i->item_id)->value('id');
                       
                    $updatevarparent = VariantsBalance::find($parent_varbal_id);
                    $updatevarparent->issuance_qty = $updatevarparent->issuance_qty + $i->issue_qty;
                    $updatevarparent->balance = $updatevarparent->balance - $i->issue_qty;
                    
                    $updatevarparent->save();

                    $piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $i->item_id)->where('variant_id','=','0')->value('id');
                    
                    $update = PIVBalance::find($piv_id);
                    $update->quantity = $update->quantity - $i->issue_qty;
                    $update->save();
                  
                     foreach($all_compo AS $ac){
                     
                        $pritems_id = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $ac->compose_item_id)->value('id');
                        $update = PRItems::find($pritems_id);
                        $update->composite_qty = $update->composite_qty - $i->issue_qty;
                        $update->issuance_qty = $update->issuance_qty + $i->issue_qty;
                        $update->save();


                        $varbal_id = VariantsBalance::where('variant_id','=',$ac->variant_id)->where('item_id', '=', $ac->compose_item_id)->value('id');
                        $updatevar = VariantsBalance::find($varbal_id);
                        $updatevar->composite_qty = $updatevar->composite_qty - $i->issue_qty;
                        $updatevar->issuance_qty = $updatevar->issuance_qty + $i->issue_qty;
                        $updatevar->save();
                      
                    }

                } else if($composite_id!=0 && $variant_id==0){ ///// part of the composite begbal

                    $compo_qty = CompositeItems::where("id","=",$composite_id)->value('quantity');
                    $issued_item_id = CompositeItems::where("id","=",$composite_id)->value('compose_item_id');
                    $issued_variant_id = CompositeItems::where("id","=",$composite_id)->value('variant_id');
                  
                        $compoitems=CompositeItems::find($composite_id);
                        $compoitems->quantity =  $compoitems->quantity - $i->issue_qty;
                        $compoitems->save();
                    
                    $pritems_id = PRItems::where('pr_no','=',$pr_no)->where('item_id', '=', $issued_item_id)->value('id');
                    $update = PRItems::find($pritems_id);
                    $update->composite_qty = $update->composite_qty - $i->issue_qty;
                    $update->issuance_qty = $update->issuance_qty + $i->issue_qty;
                    $update->save();

                    $piv_id = PIVBalance::where('pr_no','=',$pr_no)->where('item_id', '=', $i->item_id)->where('variant_id','=','0')->value('id');
                    
                    $update = PIVBalance::find($piv_id);
                    $update->quantity = $update->quantity - $i->issue_qty;
                    $update->save();

                }
            }

        }

      

        $req_qty= RequestItems::where("request_head_id","=",$request->request_head_id)->sum('quantity');
        $iss_qty= RequestItems::where("request_head_id","=",$request->request_head_id)->sum('issued_qty');

        //echo $req_qty . " = " . $iss_qty;
        if($req_qty == $iss_qty){

            $update_close = RequestHead::where('mreqf_no','=', $mreqf_no)->update(['close' => '1']);
        }

         return $issuance_id;

      
    }

    public function all_issuance(Request $request){
        // $issuance = IssuanceHead::orderBy('mif_no','DESC')->paginate(10);
        // return response()->json([
        //     'issuance'=>$issuance
        // ],200);
        $issuance = IssuanceHead::query()->when($request->get('filter'), function ($query, $filter) {
            $query->where('issuance_date', 'LIKE', '%' . $filter . '%')
            ->orWhere('issuance_time', 'LIKE', '%' . $filter . '%')
            ->orWhere('mif_no', 'LIKE', '%' . $filter . '%')
            ->orWhere('mreqf_no', 'LIKE', '%' . $filter . '%')
            ->orWhere('remarks', 'LIKE', '%' . $filter . '%');
        })->orderBy('mif_no','DESC')->paginate(10);
        return response()->json($issuance);
    }

    public function get_issuance_head($id){

        
       // $head = IssuanceHead::with(['request'])->find($id);

        //if(IssuanceHead::with(['request'])->find($id)->exists()){
            $itemData=array();
            $issuance_head = IssuanceHead::with(['request'])->where('id','=',$id)->get();
            $curr_year = date('Y');
            $curr_mo = date('m');
            if(MGP::where('year', '=', $curr_year)->exists()) {
                $mgp = MGP::where('year', '=', $curr_year)->max('series') + 1;
                $max_value = str_pad($mgp ,4,"0",STR_PAD_LEFT);;
            } else {
                $max_value = '0001';
            }
    
            $mgp_no = 'MGP-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
            

            foreach($issuance_head AS $ih){
                
                    $issuanceData = [ 
                        'mreqf_no'=>$ih->mreqf_no,
                        'mgp_no'=>$mgp_no,
                        'mif_no'=>$ih->mif_no,
                        'prepared_by_id'=>$ih->prepared_by,
                        'issuance_date'=>$ih->issuance_date,
                        'issuance_time'=>$ih->issuance_time,
                        'pr_no'=>strtoupper($ih->request->pr_no),
                        'department_name'=>$ih->department_name,
                        'purpose_name'=>$ih->purpose_name,
                        'enduse_name'=>$ih->enduse_name,
                        'released_by'=>$ih->released_by,
                        'released_by_name'=>$ih->released_by_name,
                        'released_by_pos'=>$ih->released_by_pos,
                        'contractor'=>$ih->contractor,
                        'contractor_name'=>$ih->contractor_name,
                        'received_by'=>$ih->received_by,
                        'received_by_name'=>$ih->received_by_name,
                        'received_by_pos'=>$ih->received_by_pos,
                        'noted_by'=>$ih->noted_by,
                        'noted_by_name'=>$ih->noted_name,
                        'noted_by_pos'=>$ih->noted_pos,
                        'prepared_by_name'=>$ih->prepared_by_name,
                        'prepared_by_pos'=>$ih->prepared_by_pos,
                        'requested_by'=>$ih->requested_by,
                        'requested_by_name'=>$ih->requested_by_name,
                        'requested_by_pos'=>$ih->requested_by_pos,
                        'approved_by'=>$ih->approved_by,
                        'approved_by_name'=>$ih->approved_by_name,
                        'approved_by_pos'=>$ih->approved_by_pos,
                        'recommended_by'=>$ih->recommended_by,
                        'recommended_by_name'=>$ih->recommended_by_name,
                        'recommended_by_pos'=>$ih->recommended_by_pos,
                        'inspected_by'=>$ih->inspected_by,
                        'inspected_by_name'=>$ih->inspected_by_name,
                        'inspected_by_pos'=>$ih->inspected_by_pos,
                        'noted_by_gp'=>$ih->noted_by_gp,
                        'noted_by_name_gp'=>$ih->noted_by_name_gp,
                        'noted_by_pos_gp'=>$ih->noted_by_pos_gp,
                        'remarks'=>$ih->remarks,
                    
                    ];
                

            }
            //return  $issuanceData;
            $issuanceItems = IssuanceItems::with(['variants'])->where('issuance_head_id','=',$id)->get();
            
        //    $query = IssuanceItems::query();
        //    $query->whereHas('variants', function ($query) use ($id) {
        //    }
            // $query = Variants::with('issuance_items');
            // $query->whereHas('issuance_items', function ($query) use ($id) {
            //     $query->where('issuance_head_id', $id);
            // });
            // $issuanceItems =  $query->get();

          
            foreach($issuanceItems as $ii){

                //echo $ii->issuance_items->variant_id  . " = " . $ii->issuance_items->composite_item_id ;

                if($ii->issued_qty > 0){
                   
                    if($ii->variant_id != 0 && $ii->composite_item_id == 0){ /// not composite but with variant, single item
                        $itemData[] = [ 
                            'instock'=>$ii->inventory_balance,
                            'request_qty'=>$ii->request_qty,
                            'pr_qty'=>$ii->pr_qty,
                            'issued_qty'=>$ii->issued_qty,
                            'unit_cost'=>$ii->unit_cost,
                            'currency'=>$ii->currency,
                            'shipping_cost'=>$ii->shipping_cost,
                            'supplier'=>$ii->variants->supplier_name,
                            'item_description'=>$ii->item_description,
                            'uom'=>$ii->variants->uom,
                            'catalog_no'=>$ii->variants->catalog_no,
                            'brand'=>$ii->variants->brand,
                            'serial_no'=>$ii->variants->serial_no,
                            'item_status'=>$ii->variants->item_status->status,
                            'expiry_date'=>$ii->variants->expiration,
                            'variant_id'=>$ii->variant_id,
                            'color'=>$ii->variants->color,
                            'size'=>$ii->variants->size,
                            'inventory_balance'=>$ii->inventory_balance,
                            'remarks'=>$ii->remarks,
                        
                        ];
                     }else if($ii->variant_id != 0 && $ii->composite_item_id != 0){ //// part of the composite items with variant

                        $all_compo = CompositeItems::with('items')
                                    ->where('id','=', $ii->composite_item_id)->get();
                        $item_description = $ii->item_description . " (";

                        foreach($all_compo AS $ac){
                            
                            $item_description .= $ac->items->item_description . ", ";
                        }
                        
                        $item_description = substr_replace($item_description, '', -2);
                        $item_description .= ") ";

                        $itemData[] = [ 
                            'instock'=>$ii->inventory_balance,
                            'request_qty'=>$ii->request_qty,
                            'issued_qty'=>$ii->issued_qty,
                            'supplier'=>$ii->variants->supplier_name,
                            'unit_cost'=>$ii->unit_cost,
                            'shipping_cost'=>$ii->shipping_cost,
                            'item_description'=>$item_description,
                            'uom'=>$ii->variants->uom,
                            'catalog_no'=>$ii->variants->catalog_no,
                            'brand'=>$ii->variants->brand,
                            'serial_no'=>$ii->variants->serial_no,
                            'item_status'=>$ii->variants->item_status->status,
                            'expiry_date'=>$ii->variants->expiration,
                            'variant_id'=>$ii->variant_id,
                            'color'=>$ii->variants->color,
                            'size'=>$ii->variants->size,
                            'inventory_balance'=>$ii->inventory_balance,
                            'remarks'=>$ii->remarks,
                        
                        ];
                    } else if($ii->variant_id == 0 && $ii->composite_item_id != 0){ //// part of the composite items begbal

                            $all_compo = CompositeItems::with('items')
                                        ->where('id','=', $ii->composite_item_id)->get();
                            $item_description = $ii->item_description . " (";
    
                            foreach($all_compo AS $ac){
                                
                                $item_description .= $ac->items->item_description . ", ";
                            }
                            
                            $item_description = substr_replace($item_description, '', -2);
                            $item_description .= ") ";
    
                            $itemData[] = [ 
                                'instock'=>$ii->inventory_balance,
                                'request_qty'=>$ii->request_qty,
                                'issued_qty'=>$ii->issued_qty,
                                'unit_cost'=>$ii->unit_cost,
                                'shipping_cost'=>$ii->shipping_cost,
                                'supplier'=>'',
                                'item_description'=> $item_description,
                                'uom'=>'',
                                'catalog_no'=>'',
                                'brand'=>'',
                                'serial_no'=>'',
                                'item_status'=>'',
                                'expiry_date'=>'',
                                'variant_id'=>0,
                                'color'=>'',
                                 'size'=>'',
                                'inventory_balance'=>$ii->inventory_balance,
                                'remarks'=>$ii->remarks,
                            
                            ];
    
                   
                     } else if($ii->variant_id == 0 && $ii->composite_item_id == 0){ //// all composite items

                        
                   

                    if(CompositeItems::where('item_id','=',$ii->item_id)->exists()){
                        $all_compo = CompositeItems::with('items')->where('item_id','=', $ii->item_id)->where('quantity','!=','0')->get();
                        
                        $item_description = $ii->item_description . " (";

                        foreach($all_compo AS $ac){
                           
                            $item_description .=   $ac->items->item_description . ", ";
                        }
                        $item_description = substr_replace($item_description, '', -2);
                        $item_description .= ")";
                    } else {
                        $item_description = $ii->item_description;
                    }

                        $itemData[] = [ 
                            'instock'=>$ii->inventory_balance,
                            'request_qty'=>$ii->request_qty,
                            'issued_qty'=>$ii->issued_qty,
                            'unit_cost'=>$ii->unit_cost,
                            'shipping_cost'=>$ii->shipping_cost,
                            'supplier'=>'',
                            'item_description'=> $item_description,
                            'uom'=>'',
                            'catalog_no'=>'',
                            'brand'=>'',
                            'serial_no'=>'',
                            'item_status'=>'',
                            'expiry_date'=>'',
                            'variant_id'=>0,
                            'color'=>'',
                            'size'=>'',
                            'inventory_balance'=>$ii->inventory_balance,
                            'remarks'=>$ii->remarks,
                        
                        ];

                     }

                }
            }

            return response()->json([
                'issuancehead'=>$issuanceData,
                'issuanceitems'=>$itemData
            ],200);
    }

    public function add_signatory(Request $request){
        //return $request->id;
        $update_data=IssuanceHead::where('id',$request->id)->first();

        
        $validated=[
            'prepared_by'=>$request->prepared_by_id,
            'prepared_by_name'=>$request->prepared_by,
            'prepared_by_pos'=>$request->prepared_by_pos, 
            'received_by'=>$request->received_by,
            'received_by_name'=>User::where('id',$request->received_by)->value('name'),
            'received_by_pos'=>($request->rec_position != 'null') ? $request->rec_position : '',
            'released_by'=>$request->released_by,
            'released_by_name'=>User::where('id',$request->released_by)->value('name'),
            'released_by_pos'=>($request->released_position != 'null') ? $request->released_position : '',
            'noted_by'=>$request->noted_by,
            'noted_name'=>User::where('id',$request->noted_by)->value('name'),
            'noted_pos'=>($request->noted_position != 'null') ? $request->noted_position : '',
        ];

        
        $update_data->update($validated);
    }

    public function add_signatory_gp(Request $request){
        //return $request->id;
        $update_data=IssuanceHead::where('id',$request->id)->first();

    

        $validated=[
            'contractor'=>$request->contractor,
            'contractor_name'=>User::where('id',$request->contractor)->value('name'),
            'requested_by'=>$request->requested_by,
            'requested_by_name'=>User::where('id',$request->requested_by)->value('name'),
            'requested_by_pos'=>($request->req_position != 'null') ? $request->req_position : '',
            'recommended_by'=>$request->recommended_by,
            'recommended_by_name'=>User::where('id',$request->recommended_by)->value('name'),
            'recommended_by_pos'=>($request->rec_position != 'null') ? $request->rec_position : '',
            'approved_by'=>$request->approved_by,
            'approved_by_name'=>User::where('id',$request->approved_by)->value('name'),
            'approved_by_pos'=>($request->app_position != 'null') ? $request->app_position : '',
            'inspected_by'=>$request->inspected_by,
            'inspected_by_name'=>User::where('id',$request->inspected_by)->value('name'),
            'inspected_by_pos'=>($request->ins_position != 'null') ? $request->ins_position : '',
            'noted_by_gp'=>$request->noted_by_gp,
            'noted_by_name_gp'=>User::where('id',$request->noted_by_gp)->value('name'),
            'noted_by_pos_gp'=>($request->noted_by_pos_gp != 'null') ? $request->noted_by_pos_gp : '',
        ];
       
        $update_data->update($validated);
    }
}
