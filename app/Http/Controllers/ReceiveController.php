<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReceiveHead;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\BackorderHead;
use App\Models\BackorderItems;
use App\Models\Mrec;
use App\Models\Department;
use App\Models\Enduse;
use App\Models\User;
use App\Models\Purpose;
use App\Models\Items;
use App\Models\PRItems;
use App\Models\ItemStatus;
use App\Models\Variants;
use App\Models\VariantsBalance;
use App\Models\BorrowDetails;
use App\Models\RequestHead;
use App\Models\RequestItems;
use App\Models\Mreqf;
use App\Models\IssuanceHead;
use App\Models\IssuanceItems;
use App\Models\RestockHead;
use App\Models\MIF;
use App\Models\PIVBalance;
use App\Models\OverrideLogs;
use App\Models\BackorderDetails;
use App\Http\Requests\ReceiveHeadRequest;
use App\Http\Requests\ReceiveDetailRequest;
use App\Http\Requests\ReceiveItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Config;



class ReceiveController extends Controller
{
    
    public function create_receive_head(Request $request){

        
        $curr_year = date('Y');
        $curr_mo = date('m');
        if(Mrec::where('year', '=', $curr_year)->exists()) {
            $mrif = Mrec::where('year', '=', $curr_year)->max('series') + 1;
            $max_value = str_pad($mrif,4,"0",STR_PAD_LEFT);;
        } else {
            $max_value = '0001';
        }

        $mrec_no = 'MRIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
        
        $id = Auth::id();
        $formData=[
            'mrecf_no'=>$mrec_no,
            'waybill_no'=>'',
            'receive_date'=>date("Y-m-d"),
            'dr_no'=>'',
            'po_no'=>'',
            'si_or'=>'',
            'pcf'=>0,
            'user_id'=>$id 
        ];
        return response()->json($formData);
    }

    public function add_receive_head(ReceiveHeadRequest $request){

        $validated = $request->validated();
        $receive_id = ReceiveHead::insertGetId($validated);

        $head=ReceiveHead::where('id',$receive_id)->first();
        $data = [
            'draft'=>'1'
        ];
        $head->update($data);

        $mrec = $request->mrecf_no;
        $ser = explode("-",$mrec);
        $series = $ser[3];

        Mrec::create([
            'year' => date("Y"),
            'series'=>$series
        ]);

        return $receive_id;
    }

    public function get_receive_head($id){
        $head = ReceiveHead::find($id);
        $user_id=ReceiveHead::where('id',$id)->value('user_id');
        $prepared_by=User::where('id',$user_id)->value('name');
        $prepared_position=User::where('id',$user_id)->value('position');
        $received_id=ReceiveHead::where('id',$id)->value('received_by');
        $rec_position=User::where('id',$received_id)->value('position');
        $acknowledged_id=ReceiveHead::where('id',$id)->value('acknowledged_by');
        $ack_position=User::where('id',$acknowledged_id)->value('position');
        $noted_id=ReceiveHead::where('id',$id)->value('noted_by');
        $noted_position=User::where('id',$noted_id)->value('position');
        // $pending_items = ReceiveItems::where('receive_head_id',$id)->where('eval_flag', '0')->count();
        $loop_rec = ReceiveItems::where('receive_head_id',$id)->get();
        $pending_items=0;
        foreach($loop_rec AS $lr){
            $item_status_id = $lr->item_status_id;
            $status_mode = ItemStatus::where('id',$lr->item_status_id)->value('modes');
            if($status_mode=='add'){
                $pending_items= ReceiveItems::with('item_status')->where('receive_head_id',$id)->where('eval_flag', '0')->where('item_status_id',$lr->item_status_id)->count();
            }
        }
        return response()->json([
            'head'=>$head,
            'pending_items'=>$pending_items,
            'user_id'=>$user_id,
            'prepared_by'=>$prepared_by,
            'prepared_position'=>$prepared_position,
            'rec_position'=>$rec_position,
            'ack_position'=>$ack_position,
            'noted_position'=>$noted_position,
            'printed_by_name'=>Auth::user()->name,
        ],200);
    }

    public function create_receive_details(Request $request, $id, $detail_id){
        $department = Department::all();
        $enduse = Enduse::all();
        $user = User::all();
        $inspected = User::where('inspected_flag','=','1')->get();
        $purpose = Purpose::all();
        $currency=Config::get('constants.currency');

        if(ReceiveDetails::where('receive_head_id', '=', $id)->exists()) {
            $max_number = ReceiveDetails::where('receive_head_id', '=', $id)->max('detail_no') + 1;
        } else {
            $max_number = '1';
        }

        if(ReceiveDetails::where('receive_head_id', '=', $id)->where('detail_no','=',$detail_id)->exists()) {

             $details = ReceiveDetails::where('receive_head_id', '=', $id)->where('detail_no','=',$detail_id)->get();
            foreach($details AS $det){
                if($det->department_id == 0){
                    $dept_name = $det->department_name;
                } else {
                    $dept_name = $det->department_name . ' #'. $det->department_id;
                }

                if($det->enduse_id == 0){
                    $end_name = $det->enduse_name;
                } else {
                    $end_name = $det->enduse_name . ' #'. $det->enduse_id;
                }

                if($det->purpose_id == 0){
                    $purp_name = $det->purpose_name;
                } else {
                    $purp_name = $det->purpose_name . ' #'. $det->purpose_id;
                }

                $formData=[
                    'receive_head_id'=>$id,
                    'detail_no'=>$det->detail_no,
                    'pr_no'=>strtoupper($det->pr_no),
                    'department_id'=>$dept_name,
                    'inspected_id'=>$det->inspected_id.'~'.$det->inspected_name,
                    'enduse_id'=>$end_name,
                    'purpose_id'=>$purp_name,
                    'draft'=>'1',
                ];
            }
            
            
        } else{
            $formData=[
                'receive_head_id'=>$id,
                'detail_no'=>$max_number,
                'pr_no'=>'',
                'department_id'=>'',
                'inspected_id'=>'',
                'enduse_id'=>'',
                'purpose_id'=>'',
            ];
        }
        

        return response()->json([
            'department'=>$department,
            'enduse'=>$enduse,
            'user'=>$user,
            'inspected'=>$inspected,
            'purpose'=>$purpose,
            'formData'=>$formData,
            'currency'=>$currency,
        ],200);
    }

    public function save_draft_details(Request $request,  $id){
       

          $items = $request->input('receive_items');
        if($request->input('enduse_id')){
            if (str_contains($request->input('enduse_id'), '#')) {

                $end = explode("#",$request->input('enduse_id'));
                $end_id = $end[1];
                $end_name = $end[0];
            } else {
                
                $end_id = 0;
                $end_name = $request->input('enduse_id');
            }
        } else {
            $end_id = 0;
            $end_name = NULL;
        }

        if($request->input('inspected_id')){
            $ins = explode("~",$request->input('inspected_id'));
            $ins_id = $ins[0];
            $ins_name = $ins[1];
        }else {
            $ins_id = 0;
            $ins_name = NULL;
        }

        if($request->input('department_id')){
            if (str_contains($request->input('department_id'), '#')) {

                $dept = explode("#",$request->input('department_id'));
                $dept_id = $dept[1];
                $dept_name = $dept[0];
            } else {
                $dept_id = 0;
                $dept_name = $request->input('department_id');
            }
        }else {
            $dept_id = 0;
            $dept_name = NULL;
        }

        if($request->input('purpose_id')){
            if (str_contains($request->input('purpose_id'), '#')) {
                $purp = explode("#",$request->input('purpose_id'));
                $purp_id = $purp[1];
                $purp_name = $purp[0];
            } else {
                $purp_id = 0;
                $purp_name =$request->input('purpose_id');
            }
        }else {
            $purp_id = 0;
            $purp_name = NULL;
        }
         $det_no = $request->input('detail_no');
        $receive_head_id=$id;
        if(ReceiveDetails::where('receive_head_id', '=', $receive_head_id)->where('detail_no','=',$det_no)->exists()) {

            
             $receive_details_id = ReceiveDetails::where('receive_head_id', '=', $id)->where('detail_no','=',$det_no)->value('id');
               
             $details=ReceiveDetails::find($receive_details_id)->update(
                [
                    'pr_no' => strtoupper($request->input('pr_no')),
                    'department_id'=>$dept_id,
                    'department_name'=>$dept_name,
                    'enduse_id'=>$end_id,
                    'enduse_name'=>$end_name,
                    'purpose_id'=>$purp_id,
                    'purpose_name'=>$purp_name,
                    'inspected_id'=>$ins_id,
                    'inspected_name'=>$ins_name,
                ]);

        } else { 
           
           $receive_details_id = ReceiveDetails::insertGetId([
                'receive_head_id' => $receive_head_id,
                'detail_no'=>$request->input('detail_no'),
                'pr_no'=>strtoupper($request->input('pr_no')),
                'department_id'=>$dept_id,
                'department_name'=>$dept_name,
                'enduse_id'=>$end_id,
                'enduse_name'=>$end_name,
                'purpose_id'=>$purp_id,
                'purpose_name'=>$purp_name,
                'inspected_id'=>$ins_id,
                'inspected_name'=>$ins_name,
            ]);
        }
        $head=ReceiveHead::where('id',$receive_head_id)->first();
        $data = [
            'draft'=>'1'
        ];
        $head->update($data);

    
            $no = 1;
              foreach(json_decode($items) as $i){

                if($i->description){

                    $desc = explode("~",$i->description);
                    $desc_id = $desc[0];
                    $desc_name = $desc[1];
                    $desc_pn = $desc[2];
                
                }else {
                    $desc_id = 0;
                    $desc_name = NULL;
                    $desc_pn = NULL;
                }

                if($i->supplier){
                    $supp = explode("~",$i->supplier);
                    $supp_id = $supp[0];
                    $supp_name = $supp[1];
                }else {
                    $supp_id = 0;
                    $supp_name = NULL;
                }

                if($i->item_status){
                    $stat = explode("~",$i->item_status);
                    $stat_id = $stat[0];
                    $stat_name = $stat[1];
                }else {
                    $stat_id = 0;
                    $stat_name = NULL;
                }

                if($i->unit_cost){
                    $unit_cost = $i->unit_cost;
                } else {
                    $unit_cost =0;
                }

                if($i->shipping_cost){
                    $shipping_cost = $i->shipping_cost;
                } else {
                    $shipping_cost =0;
                }
                
                

                if($i->exp_quantity){
                    $exp_quantity = $i->exp_quantity;
                } else {
                    $exp_quantity =0;
                }

                if($i->rec_quantity){
                    $rec_quantity = $i->rec_quantity;
                } else {
                    $rec_quantity =0;
                }
              
                if(ReceiveItems::where('receive_head_id','=', $id)->where('receive_details_id','=',$receive_details_id)
                    ->where('item_no','=',$no)->exists()){
                      
                        $items_update=ReceiveItems::where('receive_head_id','=', $id)->where('receive_details_id','=',$receive_details_id)
                        ->where('item_no','=',$no)->first();
                      
                        $itemdata['item_no']=$no;
                        $itemdata['supplier_id']=$supp_id;
                        $itemdata['supplier_name']=$supp_name;
                        $itemdata['item_id']=$desc_id;
                        $itemdata['item_description']=$desc_name;
                        $itemdata['pn_no']=$desc_pn;
                        $itemdata['brand']=$i->brand;
                        $itemdata['color']=$i->color;
                        $itemdata['size']=$i->size;
                        $itemdata['catalog_no']=$i->catalog_no;
                        $itemdata['serial_no']=$i->serial_no;
                        $itemdata['uom']=$i->uom;
                        $itemdata['unit_cost']=$unit_cost;
                        $itemdata['currency']=$i->currency;
                        $itemdata['shipping_cost']=$shipping_cost;
                        $itemdata['exp_quantity']=$exp_quantity;
                        $itemdata['rec_quantity']=$rec_quantity;
                        $itemdata['item_status_id']=$stat_id;
                        $itemdata['item_status']=$stat_name;
                        $itemdata['expiry_date']=$i->expiry_date;
                        $itemdata['location']=$i->location;
                        $itemdata['remarks']=$i->remarks;
                        $itemdata['pr_replenish']=$i->pr_replenish;
                       
                       
                        $items_update->update($itemdata);
                } else {
                   

                    $itemdata['receive_head_id']=$id;
                    $itemdata['receive_details_id']=$receive_details_id;
                    $itemdata['item_no']=$no;
                    $itemdata['supplier_id']=$supp_id;
                    $itemdata['supplier_name']=$supp_name;
                    $itemdata['item_id']=$desc_id;
                    $itemdata['item_description']=$desc_name;
                    $itemdata['pn_no']=$desc_pn;
                    $itemdata['brand']=$i->brand;
                    $itemdata['color']=$i->color;
                    $itemdata['size']=$i->size;
                    $itemdata['catalog_no']=$i->catalog_no;
                    $itemdata['serial_no']=$i->serial_no;
                    $itemdata['uom']=$i->uom;
                    $itemdata['unit_cost']=$unit_cost;
                    $itemdata['currency']=$i->currency;
                    $itemdata['shipping_cost']=$shipping_cost;
                    $itemdata['exp_quantity']=$exp_quantity;
                    $itemdata['rec_quantity']=$rec_quantity;
                    $itemdata['item_status_id']=$stat_id;
                    $itemdata['item_status']=$stat_name;
                    $itemdata['expiry_date']=$i->expiry_date;
                    $itemdata['location']=$i->location;
                    $itemdata['remarks']=$i->remarks;
                    $itemdata['pr_replenish']=$i->pr_replenish;

                    
                    ReceiveItems::create($itemdata);
                }
              
                $no++;
            }
        
    }

    public function create_receive_items(Request $request, $id, $detail_id){

        $receive_details_id = ReceiveDetails::where('receive_head_id', '=', $id)->where('detail_no','=',$detail_id)->value('id');
      
        if(ReceiveItems::where('receive_head_id', '=', $id)->where('receive_details_id','=',$receive_details_id)->exists()) {

            $items = ReceiveItems::where('receive_head_id', '=', $id)->where('receive_details_id','=',$receive_details_id)->get();
         
            //$formItems = $items;
          

          
            foreach($items AS $it){

                if($it->supplier_id==0){
                    $supplier = "";
                } else {
                    $supplier = $it->supplier_id.'~'.$it->supplier_name;
                }
    
                if($it->item_id==0){
                    $item = "";
                } else {
                    $item = $it->item_id.'~'.$it->item_description.'~'.$it->pn_no;
                }
    
                if($it->item_status_id==0){
                    $status = "";
                } else {
                    $status = $it->item_status_id.'~'.$it->item_status;
                }

               $formItems[]=[
                    'id'=>$it->id,
                   'supplier'=>$supplier,
                   'description'=>$item,
                   'pn_no'=>$it->pn_no,
                   'brand'=>$it->brand,
                   'color'=>$it->color,
                   'size'=>$it->size,
                   'catalog_no'=>$it->catalog_no,
                   'serial_no'=>$it->serial_no,
                   'barcode'=>$it->barcode,
                   'location'=>$it->location,
                   'item_status'=>$status,
                   'uom'=>$it->uom,
                   'exp_quantity'=>$it->exp_quantity,
                   'rec_quantity'=>$it->rec_quantity,
                   'unit_cost'=>$it->unit_cost,
                   'currency'=>$it->currency,
                   'shipping_cost'=>$it->shipping_cost,
                   'pr_replenish'=>$it->pr_replenish,
                   'expiry_date'=>$it->expiry_date,
                   'remarks'=>$it->remarks

               ];
            }

       } else{
                $formItems[]=[
                    'id'=>'',
                    'supplier'=>'',
                    'description'=>'',
                    'pn_no'=>'',
                    'brand'=>'',
                    'color'=>'',
                    'size'=>'',
                    'catalog_no'=>'',
                    'serial_no'=>'',
                    'barcode'=>'',
                    'location'=>'',
                    'item_status'=>'',
                    'uom'=>'',
                    'exp_quantity'=>'',
                    'rec_quantity'=>'',
                    'unit_cost'=>'',
                    'currency'=>'',
                    'shipping_cost'=>'',
                    'pr_replenish'=>'',
                    'expiry_date'=>'',
                    'remarks'=>'',

                ];
         }
            
        return response()->json([
            'formItems'=>$formItems
        ],200);
      
    }

    public function save_receive(ReceiveDetailRequest $request,  $id){

        $items = $request->input('receive_items');
      
      if($request->input('enduse_id')){
        if (str_contains($request->input('enduse_id'), '#')) {

          $end = explode("#",$request->input('enduse_id'));
          $end_id = $end[1];
          $end_name = $end[0];
        } else {

            $end_name =$request->input('enduse_id');
          
            $end_id = Enduse::insertGetId([
                'enduse_name'=>$end_name
            ]);

        }
      } else {
          $end_id = 0;
          $end_name = NULL;
      }

      if($request->input('inspected_id')){
          $ins = explode("~",$request->input('inspected_id'));
          $ins_id = $ins[0];
          $ins_name = $ins[1];
      }else {
          $ins_id = 0;
          $ins_name = NULL;
      }

      if($request->input('department_id')){
        if (str_contains($request->input('department_id'), '#')) {
          $dept = explode("#",$request->input('department_id'));
          $dept_id = $dept[1];
          $dept_name = $dept[0];
        } else {
            $dept_name =$request->input('department_id');
          
            $dept_id = Department::insertGetId([
                'department_name'=>$dept_name
            ]);
        }
      }else {
          $dept_id = 0;
          $dept_name = NULL;
      }

      if($request->input('purpose_id')){
        if (str_contains($request->input('purpose_id'), '#')) {
          $purp = explode("#",$request->input('purpose_id'));
          $purp_id = $purp[1];
          $purp_name = $purp[0];
        } else {
            $purp_name =$request->input('purpose_id');
          
            $purp_id = Purpose::insertGetId([
                'purpose_name'=>$purp_name
            ]);
        }
      }else {
          $purp_id = 0;
          $purp_name = NULL;
      }
       $det_no = $request->input('detail_no');
      $receive_head_id=$id;
      if(ReceiveDetails::where('receive_head_id', '=', $receive_head_id)->where('detail_no','=',$det_no)->exists()) {

          
           $receive_details_id = ReceiveDetails::where('receive_head_id', '=', $id)->where('detail_no','=',$det_no)->value('id');
             
           $details=ReceiveDetails::find($receive_details_id)->update(
              [
                  'pr_no' => strtoupper($request->input('pr_no')),
                  'department_id'=>$dept_id,
                  'department_name'=>$dept_name,
                  'enduse_id'=>$end_id,
                  'enduse_name'=>$end_name,
                  'purpose_id'=>$purp_id,
                  'purpose_name'=>$purp_name,
                  'inspected_id'=>$ins_id,
                  'inspected_name'=>$ins_name,
              ]);

      } else { 
         
            $receive_details_id = ReceiveDetails::insertGetId([
                'receive_head_id' => $receive_head_id,
                'detail_no'=>$request->input('detail_no'),
                'pr_no'=>strtoupper($request->input('pr_no')),
                'department_id'=>$dept_id,
                'department_name'=>$dept_name,
                'enduse_id'=>$end_id,
                'enduse_name'=>$end_name,
                'purpose_id'=>$purp_id,
                'purpose_name'=>$purp_name,
                'inspected_id'=>$ins_id,
                'inspected_name'=>$ins_name,
            ]);
      }
      $head=ReceiveHead::where('id',$receive_head_id)->first();
      $data = [
          'draft'=>'0',
          'saved'=>'1'
      ];
      $head->update($data);

  
          $no = 1;
            foreach(json_decode($items) as $i){

              if($i->description){

                  $desc = explode("~",$i->description);
                  $desc_id = $desc[0];
                  $desc_name = $desc[1];
                  $pn_no = $desc[2];
              
              }else {
                  $desc_id = 0;
                  $desc_name = NULL;
                  $pn_no = NULL;
              }

              if($i->supplier){
                  $supp = explode("~",$i->supplier);
                  $supp_id = $supp[0];
                  $supp_name = $supp[1];
              }else {
                  $supp_id = 0;
                  $supp_name = NULL;
              }

              if($i->item_status){
                  $stat = explode("~",$i->item_status);
                  $stat_id = $stat[0];
                  $stat_name = $stat[1];
              }else {
                  $stat_id = 0;
                  $stat_name = NULL;
              }

              if($i->unit_cost){
                  $unit_cost = $i->unit_cost;
              } else {
                  $unit_cost =0;
              }

              if($i->shipping_cost){
                $shipping_cost = $i->shipping_cost;
                } else {
                    $shipping_cost =0;
                }
            
            

              if($i->exp_quantity){
                  $exp_quantity = $i->exp_quantity;
              } else {
                  $exp_quantity =0;
              }

              if($i->rec_quantity){
                  $rec_quantity = $i->rec_quantity;
              } else {
                  $rec_quantity =0;
              }
              
            if(ReceiveItems::where('receive_head_id','=', $id)->where('receive_details_id','=',$receive_details_id)
                  ->where('item_no','=',$no)->exists()){
                $items_update=ReceiveItems::where('receive_head_id','=', $id)->where('receive_details_id','=',$receive_details_id)
                ->where('item_no','=',$no)->first();
                $itemdata['item_no']=$no;
                $itemdata['supplier_id']=$supp_id;
                $itemdata['supplier_name']=$supp_name;
                $itemdata['item_id']=$desc_id;
                $itemdata['item_description']=$desc_name;
                $itemdata['pn_no']=$pn_no;
                $itemdata['brand']=$i->brand;
                $itemdata['color']=$i->color;
                $itemdata['size']=$i->size;
                $itemdata['catalog_no']=$i->catalog_no;
                $itemdata['serial_no']=$i->serial_no;
                $itemdata['uom']=$i->uom;
                $itemdata['unit_cost']=$unit_cost;
                $itemdata['currency']=$i->currency;
                $itemdata['shipping_cost']=$shipping_cost;
                $itemdata['exp_quantity']=$exp_quantity;
                $itemdata['rec_quantity']=$rec_quantity;
                $itemdata['item_status_id']=$stat_id;
                $itemdata['item_status']=$stat_name;
                $itemdata['expiry_date']=$i->expiry_date;
                $itemdata['location']=$i->location;
                $itemdata['remarks']=$i->remarks;
                $itemdata['pr_replenish']=$i->pr_replenish;
                $items_update->update($itemdata);
            } else {
                $itemdata['receive_head_id']=$id;
                $itemdata['receive_details_id']=$receive_details_id;
                $itemdata['item_no']=$no;
                $itemdata['supplier_id']=$supp_id;
                $itemdata['supplier_name']=$supp_name;
                $itemdata['item_id']=$desc_id;
                $itemdata['item_description']=$desc_name;
                $itemdata['pn_no']=$pn_no;
                $itemdata['brand']=$i->brand;
                $itemdata['color']=$i->color;
                $itemdata['size']=$i->size;
                $itemdata['catalog_no']=$i->catalog_no;
                $itemdata['serial_no']=$i->serial_no;
                $itemdata['uom']=$i->uom;
                $itemdata['unit_cost']=$unit_cost;
                $itemdata['currency']=$i->currency;
                $itemdata['shipping_cost']=$shipping_cost;
                $itemdata['exp_quantity']=$exp_quantity;
                $itemdata['rec_quantity']=$rec_quantity;
                $itemdata['item_status_id']=$stat_id;
                $itemdata['item_status']=$stat_name;
                $itemdata['expiry_date']=$i->expiry_date;
                $itemdata['location']=$i->location;
                $itemdata['remarks']=$i->remarks;
                $itemdata['pr_replenish']=$i->pr_replenish;
                if($desc_id){
                    ReceiveItems::create($itemdata);
                }
            }
            $no++;
        }
      
    }

    public function delete_receive_item($id){
        $items=ReceiveItems::find($id);
        $items->delete(); 
    }

    public function cancel_pr($id, $detail_no){
        $receive_details_id = ReceiveDetails::where('receive_head_id', '=', $id)->where('detail_no','=',$detail_no)->value('id');
        $row_details = ReceiveDetails::where('id','=',$receive_details_id)->exists();
            if($row_details > 0){
            
                $details=ReceiveDetails::find($receive_details_id);
                $details->delete();
            }

        $row_items = ReceiveItems::where('receive_details_id','=',$receive_details_id)->exists();
        if($row_items > 0){
            $items=ReceiveItems::where("receive_details_id", "=", $receive_details_id);
            $items->delete(); 
        }
    }

    public function cancel_transaction($id){
      
            $head=ReceiveHead::where('id', '=', $id);
            $head->delete();

            $details=ReceiveDetails::where('receive_head_id', '=', $id);
            $details->delete();
      
            $items=ReceiveItems::where('receive_head_id', '=', $id);
            $items->delete(); 
        
    }

    public function navigate($id, $detail_no, $path){

        if($path =='back'){
            $next=ReceiveDetails::where('receive_head_id', '=', $id)
                    ->where('detail_no','<',$detail_no)
                    ->orderBy('detail_no', 'desc')
                    ->take(1)
                    ->value('detail_no');
        } else if($path == 'next'){
            $next=ReceiveDetails::where('receive_head_id', '=', $id)
            ->where('detail_no','>',$detail_no)
            ->orderBy('detail_no', 'asc')
            ->take(1)
            ->value('detail_no');
        }

        return $next;
    }

    public function get_draft_count(){
        $id = Auth::id();
        $exist = ReceiveHead::where('user_id', '=', $id)->where('draft','=','1')->exists();
        return $exist;
    }

    public function get_drafts(Request $request){
        // $id = Auth::id();
        // $receive_draft = ReceiveHead::where('user_id', '=', $id)->where('draft','=','1')->paginate(10);
        // return response()->json($receive_draft);
        $filter=$request->get('filter');
        if($filter!=null){

           $query= ReceiveHead::with('receive_details')->where(function ($query) {
                $query->where('draft','=','1');
            })->where(function ($query) use ($filter) {
                $query->where('receive_date', 'LIKE', '%' . $filter . '%')
                ->orWhere('mrecf_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('dr_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('po_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('si_or', 'LIKE', '%' . $filter . '%');
            });
          
            
            $head= $query->orderBy('mrecf_no','DESC')->paginate(10);
         
            return response()->json($head);
        }else{
            $head = ReceiveHead::with('receive_details')->where('draft','=','1')->orderBy('mrecf_no', 'DESC')->paginate(10);
          
           return response()->json($head);
        }

    }

    public function search_drafts(Request $request){
        $filter=$request->get('filter');
        $id = Auth::id();
        if($filter!=null){
            $receive_draft=ReceiveHead::where('user_id', '=', $id)
                    ->where('draft','=','1')
                    ->where(function($query) use ($filter){
                        $query->where('mrecf_no','LIKE',"%$filter%");
                        $query->orWhere('receive_date','LIKE',"%$filter%");
                        $query->orWhere('dr_no','LIKE',"%$filter%");
                        $query->orWhere('po_no','LIKE',"%$filter%");
                        $query->orWhere('si_or','LIKE',"%$filter%");
                    })
                    ->orderBy('receive_date','DESC')->paginate(10);
                     return response()->json($receive_draft);
        }else{
            $receive_draft = ReceiveHead::where('user_id', '=', $id)->where('draft','=','1')->paginate(10);
            return response()->json($receive_draft);
        }
    }

    public function cancel_draft($id){
        $head=ReceiveHead::find($id);
        $head->delete();

        $details=ReceiveDetails::where('receive_head_id','=',$id);
        $details->delete();

        $items=ReceiveItems::where('receive_head_id','=',$id);
        $items->delete();
    }

    public function get_max_detail($id){
        if(ReceiveDetails::where('receive_head_id', '=', $id)->exists()){
            $detail_no = ReceiveDetails::where('receive_head_id', '=', $id)->max('detail_no');
        } else {
            $detail_no=1;
        }

        return $detail_no;
   
    }

    
    public function get_latest_detail_no($id){

        if(ReceiveDetails::where("receive_head_id","=", $id)->exists()){
            $max = ReceiveDetails::where("receive_head_id","=", $id)->max('detail_no');
        } else {
            $max=1;
        }
        
        return $max;
    }

    public function get_receive_details(Request $request, $id){

        $formData=array();
        $detail = ReceiveDetails::where('receive_head_id', '=', $id)->get();
        foreach($detail AS $det){
            
            $items = ReceiveItems::where('receive_head_id', '=', $id)->where('receive_details_id', '=', $det->id)->get();
            $remarks=array();
            $itemstatus_modes=array();
             foreach($items as $i) {
                //$remarks.=$i->remarks.",";
                if (!empty($i->remarks)) {
                    $remarks[] = $i->item_description . " - " . $i->remarks;
                }

                $modes = ItemStatus::where('id', $i->item_status_id)->value('modes');
                $itemstatus_modes[] = $modes;
            }

            if($det->department_id == 0){
                $dept_name = $det->department_name;
            } else {
                $dept_name = $det->department_name . '#'. $det->department_id;
            }

            if($det->enduse_id == 0){
                $end_name = $det->enduse_name;
            } else {
                $end_name = $det->enduse_name . '#'. $det->enduse_id;
            }

            if($det->purpose_id == 0){
                $purp_name = $det->purpose_name;
            } else {
                $purp_name = $det->purpose_name . '#'. $det->purpose_id;
            }
            $imp_modes=implode(",",$itemstatus_modes);
            $formData[]= [
                'id'=>$det->id,
                'receive_details_id'=>$det->id,
                'receive_head_id'=>$id,
                'detail_no'=>$det->detail_no,
                'pr_no'=>strtoupper($det->pr_no),
                'department_id'=>$det->department_name,
                'department'=>$dept_name,
                'inspected_id'=>$det->inspected_name,
                'inspected'=>$det->inspected_id.'~'.$det->inspected_name,
                'enduse_id'=>$det->enduse_name,
                'enduse'=>$end_name,
                'purpose_id'=>$det->purpose_name,
                'purpose'=>$purp_name,
                'remarks'=>implode(", ",$remarks),
                'explode_modes'=>explode(',',$imp_modes),
                'receive_items'=> [
                    'items'=>$items
                ]
            ];
        }
         
     
        return response()->json([
            'details'=>$formData,
        ],200);
    }

    public function edit_receive_head(ReceiveHeadRequest $request, $id){

     
        $head = ReceiveHead::where('id', $id)->first();
        $validated = $request->validated();
        $head->update($validated);

    }

    public function close_transaction($id){
        $post = ReceiveHead::find($id);
        $post->closed = '1';
        $post->save();
        $prno = ReceiveDetails::where('receive_head_id','=',$id)->get();
        foreach($prno AS $pr){
            $items = ReceiveItems::where('receive_details_id','=',$pr->id)->get();
            foreach($items AS $it){
                $mode = ItemStatus::where('id','=',$it->item_status_id)->value('modes');
                if($mode == 'deduct'){
                    if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){
                        $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
                        $update2 = PRItems::find($pritems_id);
                        $update2->damage_qty = $update2->damage_qty + $it->rec_quantity;
                        $update2->save();
                    } else { 
                        $itemdata2['pr_no']=strtoupper($pr->pr_no);
                        $itemdata2['item_id']=$it->item_id;
                        $itemdata2['damage_qty']=$it->rec_quantity;
                        PRItems::create($itemdata2);
                    }
                    $total_cost = $it->unit_cost + $it->shipping_cost;
                    if(Variants::where('supplier_id','=',$it->supplier_id)->where('item_id', '=', $it->item_id)->where('brand', '=', $it->brand)->where('item_status_id', '=', $it->item_status_id)->where('expiration', '=', $it->expiry_date)->where('uom', '=', $it->uom)->where('color', '=', $it->color)->where('size', '=', $it->size)->where('average_cost','=',$total_cost)->exists()){
                        $variant_id = Variants::where('supplier_id','=',$it->supplier_id)->where('item_id', '=', $it->item_id)->where('brand', '=', $it->brand)->where('item_status_id', '=', $it->item_status_id)->where('expiration', '=', $it->expiry_date)->where('uom', '=', $it->uom)->where('color', '=', $it->color)->where('size', '=', $it->size)->where('average_cost','=',$total_cost)->value('id');
                        $update_var = Variants::find($variant_id);
                        $update_var->quantity = $update_var->quantity + $it->rec_quantity;
                        $update_var->receive_flag = '1';
                        $update_var->save();
                        $update_rec = ReceiveItems::find($it->id);
                        $update_rec->variant_id = $variant_id;
                        $update_rec->save();
                        if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                            $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                            $update = VariantsBalance::find($varbal_id);
                            $update->damage_qty = $update->damage_qty + $it->rec_quantity;
                            $update->save();
                        } else { 
                            $vardata['variant_id']=$variant_id;
                            $vardata['item_id']=$it->item_id;
                            $vardata['damage_qty']=$it->rec_quantity;
                            VariantsBalance::create($vardata);
                        }
                    } else {
                        $var_data['item_id'] = $it->item_id;
                        $var_data['supplier_id'] = $it->supplier_id;
                        $var_data['supplier_name'] = $it->supplier_name ;
                        $var_data['catalog_no'] = $it->catalog_no;
                        $var_data['brand'] = $it->brand;
                        $var_data['color'] = $it->color;
                        $var_data['size'] = $it->size;
                        $var_data['barcode'] = $it->barcode;
                        $var_data['expiration'] = $it->expiry_date;
                        $var_data['serial_no'] = $it->serial_no;
                        $var_data['uom'] = $it->uom;
                        $var_data['quantity'] = $it->rec_quantity;
                        $var_data['unit_cost'] = $it->unit_cost;
                        $var_data['currency'] = $it->currency;
                        $var_data['shipping_cost'] = $it->shipping_cost;
                        $var_data['average_cost'] = $it->unit_cost + $it->shipping_cost;
                        $var_data['item_status_id'] = $it->item_status_id;
                        $var_data['receive_flag'] = 1;
                        $variant_id = Variants::insertGetId($var_data);
                        $update_rec = ReceiveItems::find($it->id);
                        $update_rec->variant_id = $variant_id;
                        $update_rec->save();
                        if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                            $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                            $update = VariantsBalance::find($varbal_id);
                            $update->damage_qty = $update->damage_qty + $it->rec_quantity;
                            $update->save();
                        } else { 
                            $vardata1['variant_id']=$variant_id;
                            $vardata1['item_id']=$it->item_id;
                            $vardata1['damage_qty']=$it->rec_quantity;
                            VariantsBalance::create($vardata1);
                        }
                    }
                }
            }
        }
        // $prno = ReceiveDetails::where('receive_head_id','=',$id)->get();

        // foreach($prno AS $pr){
        //     $items = ReceiveItems::where('receive_details_id','=',$pr->id)->get();

          
        //     foreach($items AS $it){

        //         $mode = ItemStatus::where('id','=',$it->item_status_id)->value('modes');
             
        //         if($mode == 'add'){

        //             if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){

        //                 $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
        //                 $update = PRItems::find($pritems_id);
        //                 $update->receive_qty = $update->receive_qty + $it->rec_quantity;
        //                 $update->balance = $update->balance + $it->rec_quantity;
        //                 $update->save();

        //             } else { 

        //                 $itemdata['pr_no']=strtoupper($pr->pr_no);
        //                 $itemdata['item_id']=$it->item_id;
        //                 $itemdata['receive_qty']=$it->rec_quantity;
        //                 $itemdata['balance']=$it->rec_quantity;
        //                 PRItems::create($itemdata);
        //             }

        //            $exists_flag= Items::where('id','=',$it->item_id)->where('composite_flag','=','0')->where('variant_flag', '=', '0')->exists();
        //             $update_balance = Items::find($it->item_id);
        //             $update_balance->running_balance = $update_balance->running_balance + $it->rec_quantity;

        //             if($exists_flag > 0){
        //                 $update_balance->variant_flag ='1';
        //             }
        //             $update_balance->save();

        //             if($it->pr_replenish == 1){

        //                  $add_to_pr = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->value('borrowed_from');
        //                  $borrowed_qty = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->value('quantity');

        //                  $borrow_id = BorrowDetails::where("item_id","=", $it->item_id)
        //                                     ->where("borrowed_by", "=", $pr->pr_no)
        //                                     ->where("borrowed_from","=", $add_to_pr)
        //                                     ->value('id');

        //                 $updatepr_rep = ReceiveItems::where("id","=", $it->id)->first();
        //                 $updatepr_rep->prno_replenish = $add_to_pr;
        //                 $updatepr_rep->save();

        //                 $update_borrow   = BorrowDetails::find($borrow_id);        
        //                 $update_borrow->balance = $update_borrow->balance - $borrowed_qty;
        //                 $update_borrow->replenished_qty = $borrowed_qty;
        //                 $update_borrow->save();

        //                 $updateDeductPR = PRItems::where("item_id","=", $it->item_id)->where("pr_no", "=",$pr->pr_no)->first();
        //                 $updateDeductPR->replenish_deduct = $updateDeductPR->replenish_deduct + $borrowed_qty;
        //                 $updateDeductPR->balance = $updateDeductPR->balance - $borrowed_qty;  
        //                 $updateDeductPR->save();

        //                 $updateaddPR = PRItems::where("item_id","=", $it->item_id)->where("pr_no", "=", $add_to_pr)->first();
        //                 $updateaddPR->replenish_add = $updateaddPR->replenish_add  + $borrowed_qty;
        //                 $updateaddPR->balance = $updateaddPR->balance + $borrowed_qty;  
        //                 $updateaddPR->save();

                       
        //             }

        //         } else if($mode == 'deduct'){

        //             if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){

                      
        //                 $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
        //                 $update2 = PRItems::find($pritems_id);
        //                 $update2->damage_qty = $update2->damage_qty + $it->rec_quantity;
        //                 $update2->save();

        //             } else { 

        //                 $itemdata2['pr_no']=strtoupper($pr->pr_no);
        //                 $itemdata2['item_id']=$it->item_id;
        //                 $itemdata2['damage_qty']=$it->rec_quantity;
        //                 PRItems::create($itemdata2);
        //             }

        //         }

        //         $total_cost = $it->unit_cost + $it->shipping_cost;
        //         if(Variants::where('supplier_id','=',$it->supplier_id)
        //                 ->where('item_id', '=', $it->item_id)
        //                 ->where('brand', '=', $it->brand)
        //                 ->where('item_status_id', '=', $it->item_status_id)
        //                 ->where('expiration', '=', $it->expiry_date)
        //                 ->where('uom', '=', $it->uom)
        //                 ->where('color', '=', $it->color)
        //                 ->where('size', '=', $it->size)
        //                 ->where('average_cost','=',$total_cost)
        //                 ->exists()){

        //             $variant_id = Variants::where('supplier_id','=',$it->supplier_id)
        //                         ->where('item_id', '=', $it->item_id)
        //                         ->where('brand', '=', $it->brand)
        //                         ->where('item_status_id', '=', $it->item_status_id)
        //                         ->where('expiration', '=', $it->expiry_date)
        //                         ->where('uom', '=', $it->uom)
        //                         ->where('color', '=', $it->color)
        //                         ->where('size', '=', $it->size)
        //                         ->where('average_cost','=',$total_cost)
        //                         ->value('id');

        //                 $update_var = Variants::find($variant_id);

        //                 // $existing_cost = $update_var->unit_cost + $update_var->shipping_cost;
        //                 // $new_cost = $it->unit_cost + $it->shipping_cost;

        //                 // if($existing_cost > $new_cost){
        //                 //     $ship_cost= $update_var->shipping_cost;
        //                 //     $u_cost =  $update_var->unit_cost;
        //                 // } else {
        //                 //     $ship_cost= $it->shipping_cost;
        //                 //     $u_cost =  $it->unit_cost;
        //                 // }
        //                 //$ave_cost =  ($update_var->average_cost + $it->unit_cost + $it->shipping_cost) / 2;
        //                 $update_var->quantity = $update_var->quantity + $it->rec_quantity;
        //                 // $update_var->unit_cost=$it->unit_cost;
        //                 // $update_var->shipping_cost=$it->shipping_cost;
        //                 // $update_var->average_cost = $total_cost;
        //                 $update_var->receive_flag = '1';
        //                 $update_var->save();

        //                 $update_rec = ReceiveItems::find($it->id);
        //                 $update_rec->variant_id = $variant_id;
        //                 $update_rec->save();

        //                 if($mode == 'add'){
        //                     if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){

        //                         $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                
        //                         $update = VariantsBalance::find($varbal_id);
        //                         $update->receive_qty = $update->receive_qty + $it->rec_quantity;
        //                         $update->balance = $update->balance + $it->rec_quantity;
        //                         $update->save();


        //                     } else { 
        //                         $vardata['variant_id']=$variant_id;
        //                         $vardata['item_id']=$it->item_id;
        //                         $vardata['receive_qty']=$it->rec_quantity;
        //                         $vardata['balance']=$it->rec_quantity;
        //                         VariantsBalance::create($vardata);
        //                     }
        //                     if($it->pr_replenish != 1){
        //                         if(PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->exists()){
        //                             $piv_id = PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    
        //                             $update = PIVBalance::find($piv_id);
        //                             $update->quantity = $update->quantity + $it->rec_quantity;
        //                             $update->save();
        //                         } else {
        //                             $pivdata['pr_no']=strtoupper($pr->pr_no);
        //                             $pivdata['variant_id']=$variant_id;
        //                             $pivdata['item_id']=$it->item_id;
        //                             $pivdata['quantity']=$it->rec_quantity;
        //                             PIVBalance::create($pivdata);
        //                         }
        //                  }
        //                     //if($it->pr_replenish == 1){
        //                         ///////////////////////////////// CREATE REQUEST ///////////////////////////////

        //                         // $user_id_req=ReceiveHead::where('id',$id)->value('user_id');
        //                         // $curr_year = date('Y');
        //                         // $curr_mo = date('m');
        //                         // if(Mreqf::where('year', '=', $curr_year)->exists()) {
        //                         //     $mreqf = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
        //                         //     $max_value = str_pad($mreqf,4,"0",STR_PAD_LEFT);;
        //                         // } else {
        //                         //     $max_value = '0001';
        //                         // }

                                
        //                         // Mreqf::create([
        //                         //     'year' => date("Y"),
        //                         //     'series'=>$max_value
        //                         // ]);

                        
        //                         // $mreqf_no = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;

        //                         // $reqhead['request_date'] = date('Y-m-d');
        //                         // $reqhead['request_time'] = date('H:i:s');
        //                         // $reqhead['mreqf_no'] = $mreqf_no;
        //                         // $reqhead['request_type']='With PR';
        //                         // $reqhead['pr_no']=$pr->pr_no;
        //                         // $reqhead['department_id']=$pr->department_id;
        //                         // $reqhead['department_name']=$pr->department_name;
        //                         // $reqhead['purpose_id']=$pr->purpose_id;
        //                         // $reqhead['purpose_name']=$pr->purpose_name;
        //                         // $reqhead['enduse_id']=$pr->enduse_id;
        //                         // $reqhead['enduse_name']=$pr->enduse_name;
        //                         // $reqhead['remarks']="Replenishment for PR# ". $add_to_pr;
        //                         // $reqhead['user_id']=$user_id_req;
        //                         // $reqhead['close']='1';
        //                         // $reqhead['saved']='1';
        //                         // $request_head_id = RequestHead::insertGetId($reqhead);
                            
        //                         // $reqdet['request_head_id']=$request_head_id;
        //                         // $reqdet['item_id']=$it->item_id;
        //                         // $reqdet['item_description']=$it->item_description;
        //                         // $reqdet['variant_id']=$variant_id;
        //                         // $reqdet['quantity']=$borrowed_qty;
        //                         // $reqdet['issued_qty']=$borrowed_qty;

        //                         // $request_item_id = RequestItems::insertGetId($reqdet);

        //                         ///////////////////////////////// CREATE ISSUANCE ///////////////////////////////

        //                         // if(MIF::where('year', '=', $curr_year)->exists()) {
        //                         //     $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
        //                         //     $max_value_iss = str_pad($mif,4,"0",STR_PAD_LEFT);;
        //                         // } else {
        //                         //     $max_value_iss = '0001';
        //                         // }
                                
        //                         // MIF::create([
        //                         //     'year' => date("Y"),
        //                         //     'series'=>$max_value_iss
        //                         // ]);

        //                         // $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_iss;

        //                         // $prepared_by=User::where('id',$user_id_req)->value('name');
        //                         // $prepared_des=User::where('id',$user_id_req)->value('position');

        //                         // $isshead['request_head_id']=$request_head_id;
        //                         // $isshead['mreqf_no']=$mreqf_no;
        //                         // $isshead['mif_no']=$mif_no;
        //                         // $isshead['pr_no']=$pr->pr_no;
        //                         // $isshead['issuance_date']=date('Y-m-d');
        //                         // $isshead['issuance_time']=date('H:i:s');
        //                         // $isshead['department_id']=$pr->department_id;
        //                         // $isshead['department_name']=$pr->department_name;
        //                         // $isshead['purpose_id']=$pr->purpose_id;
        //                         // $isshead['purpose_name']=$pr->purpose_name;
        //                         // $isshead['enduse_id']=$pr->enduse_id;
        //                         // $isshead['enduse_name']=$pr->enduse_name;
        //                         // $isshead['remarks']="Replenishment for PR# ". $add_to_pr;
        //                         // $isshead['user_id']=$user_id_req;
        //                         // $isshead['prepared_by']=$user_id_req;
        //                         // $isshead['prepared_by_name']=$prepared_by;
        //                         // $isshead['prepared_by_pos']=$prepared_des;

        //                         // $instock = Items::where('id','=',$it->item_id)->value('running_balance');
        //                         // $issuance_head_id = IssuanceHead::insertGetId($isshead);

        //                         // $issitems['issuance_head_id']=$issuance_head_id;
        //                         // $issitems['item_id']=$it->item_id;
        //                         // $issitems['item_description']=$it->item_description;
        //                         // $issitems['variant_id']=$variant_id;
        //                         // $issitems['request_items_id']=$request_item_id;
        //                         // $issitems['inventory_balance']=$instock;
        //                         // $issitems['request_qty']=$borrowed_qty;
        //                         // $issitems['issued_qty']=$borrowed_qty;
        //                         // IssuanceItems::create($issitems);

        //                     //}
                           
        //                 } else {
        //                     if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){

        //                         $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
        //                         $update = VariantsBalance::find($varbal_id);
        //                         $update->damage_qty = $update->damage_qty + $it->rec_quantity;
        //                         $update->save();
        
        //                     } else { 
        //                         $vardata['variant_id']=$variant_id;
        //                         $vardata['item_id']=$it->item_id;
        //                         $vardata['damage_qty']=$it->rec_quantity;
        //                         VariantsBalance::create($vardata);
        //                     }
        //                 }
                    
        //         } else {

        //                 $var_data['item_id'] = $it->item_id;
        //                 $var_data['supplier_id'] = $it->supplier_id;
        //                 $var_data['supplier_name'] = $it->supplier_name ;
        //                 $var_data['catalog_no'] = $it->catalog_no;
        //                 $var_data['brand'] = $it->brand;
        //                 $var_data['color'] = $it->color;
        //                 $var_data['size'] = $it->size;
        //                 $var_data['barcode'] = $it->barcode;
        //                 $var_data['expiration'] = $it->expiry_date;
        //                 $var_data['serial_no'] = $it->serial_no;
        //                 $var_data['uom'] = $it->uom;
        //                 $var_data['quantity'] = $it->rec_quantity;
        //                 $var_data['unit_cost'] = $it->unit_cost;
        //                 $var_data['currency'] = $it->currency;
        //                 $var_data['shipping_cost'] = $it->shipping_cost;
        //                 $var_data['average_cost'] = $it->unit_cost + $it->shipping_cost;
        //                 $var_data['item_status_id'] = $it->item_status_id;
        //                 $var_data['receive_flag'] = 1;
        //                 $variant_id = Variants::insertGetId($var_data);

        //                 if($mode == 'add'){
        //                     if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){

        //                         $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
        //                         $update = VariantsBalance::find($varbal_id);
        //                         $update->receive_qty = $update->receive_qty + $it->rec_quantity;
        //                         $update->balance = $update->balance + $it->rec_quantity;
        //                         $update->save();
        
        //                     } else { 
        //                         $vardata['variant_id']=$variant_id;
        //                         $vardata['item_id']=$it->item_id;
        //                         $vardata['receive_qty']=$it->rec_quantity;
        //                         $vardata['balance']=$it->rec_quantity;
        //                         VariantsBalance::create($vardata);
        //                     }
        //                     if($it->pr_replenish != 1){
        //                         if(PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->exists()){
        //                             $piv_id = PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    
        //                             $update = PIVBalance::find($piv_id);
        //                             $update->quantity = $update->quantity + $it->rec_quantity;
        //                             $update->save();
        //                         } else {
        //                             $pivdata['pr_no']=strtoupper($pr->pr_no);
        //                             $pivdata['variant_id']=$variant_id;
        //                             $pivdata['item_id']=$it->item_id;
        //                             $pivdata['quantity']=$it->rec_quantity;
        //                             PIVBalance::create($pivdata);
        //                         }
        //                     }

                       
                              

        //                     //if($it->pr_replenish == 1){
        //                         ///////////////////////////////// CREATE REQUEST ///////////////////////////////

        //                         // $user_id_req=ReceiveHead::where('id',$id)->value('user_id');
        //                         // $curr_year = date('Y');
        //                         // $curr_mo = date('m');
        //                         // if(Mreqf::where('year', '=', $curr_year)->exists()) {
        //                         //     $mreqf = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
        //                         //     $max_value = str_pad($mreqf,4,"0",STR_PAD_LEFT);;
        //                         // } else {
        //                         //     $max_value = '0001';
        //                         // }

                                
        //                         // Mreqf::create([
        //                         //     'year' => date("Y"),
        //                         //     'series'=>$max_value
        //                         // ]);

                        
        //                         // $mreqf_no = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;

        //                         // $reqhead['request_date'] = date('Y-m-d');
        //                         // $reqhead['request_time'] = date('H:i:s');
        //                         // $reqhead['mreqf_no'] = $mreqf_no;
        //                         // $reqhead['request_type']='With PR';
        //                         // $reqhead['pr_no']=$pr->pr_no;
        //                         // $reqhead['department_id']=$pr->department_id;
        //                         // $reqhead['department_name']=$pr->department_name;
        //                         // $reqhead['purpose_id']=$pr->purpose_id;
        //                         // $reqhead['purpose_name']=$pr->purpose_name;
        //                         // $reqhead['enduse_id']=$pr->enduse_id;
        //                         // $reqhead['enduse_name']=$pr->enduse_name;
        //                         // $reqhead['remarks']="Replenishment for PR# ". $add_to_pr;
        //                         // $reqhead['user_id']=$user_id_req;
        //                         // $reqhead['close']='1';
        //                         // $reqhead['saved']='1';
        //                         // $request_head_id = RequestHead::insertGetId($reqhead);
                            
        //                         // $reqdet['request_head_id']=$request_head_id;
        //                         // $reqdet['item_id']=$it->item_id;
        //                         // $reqdet['item_description']=$it->item_description;
        //                         // $reqdet['variant_id']=$variant_id;
        //                         // $reqdet['quantity']=$borrowed_qty;
        //                         // $reqdet['issued_qty']=$borrowed_qty;

        //                         // $request_item_id = RequestItems::insertGetId($reqdet);

        //                         ///////////////////////////////// CREATE ISSUANCE ///////////////////////////////

        //                     //     if(MIF::where('year', '=', $curr_year)->exists()) {
        //                     //         $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
        //                     //         $max_value_iss = str_pad($mif,4,"0",STR_PAD_LEFT);;
        //                     //     } else {
        //                     //         $max_value_iss = '0001';
        //                     //     }
                                
        //                     //     MIF::create([
        //                     //         'year' => date("Y"),
        //                     //         'series'=>$max_value_iss
        //                     //     ]);

        //                     //     $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_iss;

        //                     //     $prepared_by=User::where('id',$user_id_req)->value('name');
        //                     //     $prepared_des=User::where('id',$user_id_req)->value('position');

        //                     //     $isshead['request_head_id']=$request_head_id;
        //                     //     $isshead['mreqf_no']=$mreqf_no;
        //                     //     $isshead['mif_no']=$mif_no;
        //                     //     $isshead['pr_no']=$pr->pr_no;
        //                     //     $isshead['issuance_date']=date('Y-m-d');
        //                     //     $isshead['issuance_time']=date('H:i:s');
        //                     //     $isshead['department_id']=$pr->department_id;
        //                     //     $isshead['department_name']=$pr->department_name;
        //                     //     $isshead['purpose_id']=$pr->purpose_id;
        //                     //     $isshead['purpose_name']=$pr->purpose_name;
        //                     //     $isshead['enduse_id']=$pr->enduse_id;
        //                     //     $isshead['enduse_name']=$pr->enduse_name;
        //                     //     $isshead['remarks']="Replenishment for PR# ". $add_to_pr;
        //                     //     $isshead['user_id']=$user_id_req;
        //                     //     $isshead['prepared_by']=$user_id_req;
        //                     //     $isshead['prepared_by_name']=$prepared_by;
        //                     //     $isshead['prepared_by_pos']=$prepared_des;

        //                     //     $instock = Items::where('id','=',$it->item_id)->value('running_balance');
        //                     //     $issuance_head_id = IssuanceHead::insertGetId($isshead);

        //                     //     $issitems['issuance_head_id']=$issuance_head_id;
        //                     //     $issitems['item_id']=$it->item_id;
        //                     //     $issitems['item_description']=$it->item_description;
        //                     //     $issitems['variant_id']=$variant_id;
        //                     //     $issitems['request_items_id']=$request_item_id;
        //                     //     $issitems['inventory_balance']=$instock;
        //                     //     $issitems['request_qty']=$borrowed_qty;
        //                     //     $issitems['issued_qty']=$borrowed_qty;
        //                     //     IssuanceItems::create($issitems);
        //                     // }
                       

        //                 } else {
        //                     if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){

        //                         $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
        //                         $update = VariantsBalance::find($varbal_id);
        //                         $update->damage_qty = $update->damage_qty + $it->rec_quantity;
        //                         $update->save();
        
        //                     } else { 
        //                         $vardata1['variant_id']=$variant_id;
        //                         $vardata1['item_id']=$it->item_id;
        //                         $vardata1['damage_qty']=$it->rec_quantity;
        //                         VariantsBalance::create($vardata1);
        //                     }
        //                 }

        //                 $update_rec = ReceiveItems::find($it->id);
        //                 $update_rec->variant_id = $variant_id;
        //                 $update_rec->save();


        //         }

        //     }
        // }

         
        //    $get_rep = ReceiveDetails::with('receive_items');
        //     $get_rep->whereHas('receive_items', function ($get_rep) use($id) {
        //         $get_rep->where('receive_head_id','=',$id)->where('pr_replenish','=','1');
        //     });

        //     $rep_pr = $get_rep->get();
        //     foreach($rep_pr AS $rpr){
            
        //       ///////////////////////////////// CREATE REQUEST ///////////////////////////////
        //             $user_id_req=ReceiveHead::where('id',$id)->value('user_id');
        //             $curr_year = date('Y');
        //             $curr_mo = date('m');
        //             if(Mreqf::where('year', '=', $curr_year)->exists()) {
        //                 $mreqf = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
        //                 $max_value = str_pad($mreqf,4,"0",STR_PAD_LEFT);;
        //             } else {
        //                 $max_value = '0001';
        //             }

                    
        //             Mreqf::create([
        //                 'year' => date("Y"),
        //                 'series'=>$max_value
        //             ]);

            
        //             $mreqf_no = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
                    
        //             foreach($rpr->receive_items AS $rep_ri2){
        //                $repl_pr[] = $rep_ri2->prno_replenish;
        //             } 

        //             $all_rep_pr = array_unique($repl_pr);
        //             $remarks = "Replenishment for PR# ";
        //             foreach($all_rep_pr AS $all_rep){
        //                 $remarks .= $all_rep .", ";
        //             }
        //             $remarks = substr($remarks, 0, -2);
                  
                    
        //             $reqhead['request_date'] = date('Y-m-d');
        //             $reqhead['request_time'] = date('H:i:s');
        //             $reqhead['mreqf_no'] = $mreqf_no;
        //             $reqhead['request_type']='With PR';
        //             $reqhead['pr_no']=strtoupper($rpr->pr_no);
        //             $reqhead['department_id']=$rpr->department_id;
        //             $reqhead['department_name']=$rpr->department_name;
        //             $reqhead['purpose_id']=$rpr->purpose_id;
        //             $reqhead['purpose_name']=$rpr->purpose_name;
        //             $reqhead['enduse_id']=$rpr->enduse_id;
        //             $reqhead['enduse_name']=$rpr->enduse_name;
        //             $reqhead['remarks']=$remarks;
        //             $reqhead['user_id']=$user_id_req;
        //              $reqhead['close']='1';
        //             $reqhead['saved']='1';
        //             $reqhead['replenish_id']=$id;
        //             $request_head_id = RequestHead::insertGetId($reqhead);

                    
        //               ///////////////////////////////// CREATE ISSUANCE ///////////////////////////////
        //                if(MIF::where('year', '=', $curr_year)->exists()) {
        //                    $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
        //                    $max_value_iss = str_pad($mif,4,"0",STR_PAD_LEFT);;
        //                } else {
        //                    $max_value_iss = '0001';
        //                }
                    
        //                MIF::create([
        //                    'year' => date("Y"),
        //                    'series'=>$max_value_iss
        //                ]);

        //                $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_iss;

        //                $prepared_by=User::where('id',$user_id_req)->value('name');
        //                $prepared_des=User::where('id',$user_id_req)->value('position');

        //                $isshead['request_head_id']=$request_head_id;
        //                $isshead['mreqf_no']=$mreqf_no;
        //                $isshead['mif_no']=$mif_no;
        //                $isshead['pr_no']=strtoupper($rpr->pr_no);
        //                $isshead['issuance_date']=date('Y-m-d');
        //                $isshead['issuance_time']=date('H:i:s');
        //                $isshead['department_id']=$rpr->department_id;
        //                $isshead['department_name']=$rpr->department_name;
        //                $isshead['purpose_id']=$rpr->purpose_id;
        //                $isshead['purpose_name']=$rpr->purpose_name;
        //                $isshead['enduse_id']=$rpr->enduse_id;
        //                $isshead['enduse_name']=$rpr->enduse_name;
        //                $isshead['remarks']=$remarks;
        //                $isshead['user_id']=$user_id_req;
        //                $isshead['prepared_by']=$user_id_req;
        //                $isshead['prepared_by_name']=$prepared_by;
        //                $isshead['prepared_by_pos']=$prepared_des;

                       
        //                $issuance_head_id = IssuanceHead::insertGetId($isshead);
                    

        //             foreach($rpr->receive_items AS $rep_ri){

        //                 $borrow_qty = BorrowDetails::where('borrowed_by','=',$rpr->pr_no)
        //                                     ->where('item_id','=',$rep_ri->item_id)->value('quantity');

        //                 if($borrow_qty == $rep_ri->rec_quantity){
        //                     $qty = $rep_ri->borrow_qty;
        //                     $piv_qty = 0;
        //                     //$close = 1;
        //                 } else if($borrow_qty < $rep_ri->rec_quantity){
        //                     $qty = $borrow_qty;
        //                     //$close = 0;
        //                     $piv_qty = $rep_ri->rec_quantity - $borrow_qty;
        //                 }

        //                 // $requpdate = RequestHead::find($request_head_id);
        //                 // $requpdate->close = $close;
        //                 // $requpdate->save();

        //                 $reqdet['request_head_id']=$request_head_id;
        //                 $reqdet['item_id']=$rep_ri->item_id;
        //                 $reqdet['item_description']=$rep_ri->item_description;
        //                 $reqdet['variant_id']=$rep_ri->variant_id;
        //                 $reqdet['unit_cost']=$rep_ri->unit_cost;
        //                 $reqdet['shipping_cost']=$rep_ri->shipping_cost;
        //                 $reqdet['quantity']=$qty;
        //                 $reqdet['issued_qty']=$qty;
        //                 // $reqdet['quantity']=$rep_ri->rec_quantity;
        //                 // $reqdet['issued_qty']=$rep_ri->rec_quantity;

        //                 $request_item_id = RequestItems::insertGetId($reqdet);

        //                 $instock = Items::where('id','=',$rep_ri->item_id)->value('running_balance');
        //                 $issitems['issuance_head_id']=$issuance_head_id;
        //                 $issitems['item_id']=$rep_ri->item_id;
        //                 $issitems['item_description']=$rep_ri->item_description;
        //                 $issitems['variant_id']=$rep_ri->variant_id;
        //                 $issitems['request_items_id']=$request_item_id;
        //                 $issitems['inventory_balance']=$instock;
        //                 $issitems['unit_cost']=$rep_ri->unit_cost;
        //                 $issitems['shipping_cost']=$rep_ri->shipping_cost;
        //                 $issitems['request_qty']=$qty;
        //                 $issitems['issued_qty']=$qty;
        //                 // $issitems['request_qty']=$rep_ri->rec_quantity;
        //                 // $issitems['issued_qty']=$rep_ri->rec_quantity;
        //                 IssuanceItems::create($issitems);

        //                 if(PIVBalance::where('pr_no','=',$rpr->pr_no)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->exists()){
        //                     $piv_id = PIVBalance::where('pr_no','=',$rpr->pr_no)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->value('id');
                            
        //                     $update = PIVBalance::find($piv_id);
        //                     $update->quantity = $update->quantity + $piv_qty;
                          
        //                     $update->save();
        //                 } else {
        //                     $pivdata['pr_no']=strtoupper($rpr->pr_no);
        //                     $pivdata['variant_id']=$rep_ri->variant_id;
        //                     $pivdata['item_id']=$rep_ri->item_id;
        //                     $pivdata['quantity']=$piv_qty;
        //                     //$pivdata['quantity']=$rep_ri->rec_quantity;
        //                     PIVBalance::create($pivdata);
        //                 }


        //                 if(PIVBalance::where('pr_no','=',$rep_ri->prno_replenish)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->exists()){
        //                     $piv_id = PIVBalance::where('pr_no','=',$rep_ri->prno_replenish)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->value('id');
                            
        //                     $update = PIVBalance::find($piv_id);
        //                     $update->quantity = $update->quantity + $borrow_qty;
                          
        //                     $update->save();
        //                 } else {
        //                     $pivdata['pr_no']=strtoupper($rep_ri->prno_replenish);
        //                     $pivdata['variant_id']=$rep_ri->variant_id;
        //                     $pivdata['item_id']=$rep_ri->item_id;
        //                     $pivdata['quantity']=$borrow_qty;
        //                     //$pivdata['quantity']=$rep_ri->rec_quantity;
        //                     PIVBalance::create($pivdata);
        //                 }
        //             }
                
        //     }

           ///////////////////////////////// CREATE ISSUANCE ///////////////////////////////

        //    if(MIF::where('year', '=', $curr_year)->exists()) {
        //        $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
        //        $max_value_iss = str_pad($mif,4,"0",STR_PAD_LEFT);;
        //    } else {
        //        $max_value_iss = '0001';
        //    }
           
        //    MIF::create([
        //        'year' => date("Y"),
        //        'series'=>$max_value_iss
        //    ]);

        //    $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_iss;

        //    $prepared_by=User::where('id',$user_id_req)->value('name');
        //    $prepared_des=User::where('id',$user_id_req)->value('position');

        //    $isshead['request_head_id']=$request_head_id;
        //    $isshead['mreqf_no']=$mreqf_no;
        //    $isshead['mif_no']=$mif_no;
        //    $isshead['pr_no']=$pr->pr_no;
        //    $isshead['issuance_date']=date('Y-m-d');
        //    $isshead['issuance_time']=date('H:i:s');
        //    $isshead['department_id']=$pr->department_id;
        //    $isshead['department_name']=$pr->department_name;
        //    $isshead['purpose_id']=$pr->purpose_id;
        //    $isshead['purpose_name']=$pr->purpose_name;
        //    $isshead['enduse_id']=$pr->enduse_id;
        //    $isshead['enduse_name']=$pr->enduse_name;
        //    $isshead['remarks']="Replenishment for PR# ". $add_to_pr;
        //    $isshead['user_id']=$user_id_req;
        //    $isshead['prepared_by']=$user_id_req;
        //    $isshead['prepared_by_name']=$prepared_by;
        //    $isshead['prepared_by_pos']=$prepared_des;

        //    $instock = Items::where('id','=',$it->item_id)->value('running_balance');
        //    $issuance_head_id = IssuanceHead::insertGetId($isshead);

        //    $issitems['issuance_head_id']=$issuance_head_id;
        //    $issitems['item_id']=$it->item_id;
        //    $issitems['item_description']=$it->item_description;
        //    $issitems['variant_id']=$variant_id;
        //    $issitems['request_items_id']=$request_item_id;
        //    $issitems['inventory_balance']=$instock;
        //    $issitems['request_qty']=$borrowed_qty;
        //    $issitems['issued_qty']=$borrowed_qty;
        //    IssuanceItems::create($issitems);


    }

    public function get_all_receive(Request $request){


        $filter=$request->get('filter');
        if($filter!=null){

           $query= ReceiveHead::with('receive_details')->where(function ($query) {
                $query->where('saved','=','1')->where('draft','=','0');
            })->where(function ($query) use ($filter) {
                $query->where('receive_date', 'LIKE', '%' . $filter . '%')
                ->orWhere('mrecf_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('dr_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('po_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('si_or', 'LIKE', '%' . $filter . '%');
            });
          
            
            $head= $query->orderBy('mrecf_no','DESC')->paginate(10);
         
            return response()->json($head);
        }else{
            $head = ReceiveHead::with('receive_details')->where('saved','=','1')->where('draft','=','0')->orderBy('mrecf_no', 'DESC')->paginate(10);
          
           return response()->json($head);
        }

    }

    public function get_all_position($id){
       
        $position = User::where("id", $id)->value('position');
        return $position;
    }

    public function add_signatory(Request $request){
        $update_data=ReceiveHead::where('id',$request->id)->first();
        
        $validated=[
         
            'prepared_by'=>$request->user_id,
            'prepared_by_name'=>User::where('id',$request->user_id)->value('name'),
            'prepared_position'=>($request->prepared_position != 'null') ? $request->prepared_position : '',
            'received_by'=>$request->received_by,
            'receive_position'=>($request->rec_position != 'null') ? $request->rec_position : '',
            'received_by_name'=>User::where('id',$request->received_by)->value('name'),
            'acknowledged_by'=>$request->acknowledge_by,
            'acknowledged_by_name'=>User::where('id',$request->acknowledge_by)->value('name'),
            'acknowledged_position'=>($request->ack_position != 'null') ? $request->ack_position : '',
            'noted_by'=>$request->noted_by,
            'noted_name'=>User::where('id',$request->noted_by)->value('name'),
            'noted_position'=>($request->noted_position != 'null') ? $request->noted_position : '',
            'delivered_by'=>($request->delivered_by != 'null') ? $request->delivered_by : '',
        ];
        $update_data->update($validated);
    }

    public function get_PR_replenish($id, $pr){

        $pr = BorrowDetails::select("borrowed_from")->where("item_id","=", $id)
                    ->where("borrowed_by", "=", $pr)
                    ->where("balance", "!=", "0")->get();

        return response()->json($pr);
    }

    public function get_pr_existing($pr){

        $query=ReceiveDetails::with('receive_head')->where('pr_no','=', $pr);
            
        $query->whereHas('receive_head', function ($query) {
            $query->where('closed', '=', '1');
        });

        $exists = $query->exists();

         if($exists){


             $get=ReceiveDetails::with('receive_head')->where('pr_no','=', $pr);
             $get->whereHas('receive_head', function ($get) {
                 $get->where('closed', '=', '1');
             });
 
            //   $prdet= $get->get();
            $prdet = $get->get();

           
             foreach($prdet AS $p){
                $data = [
                    'inspected_id'=>$p->inspected_id . '~'. $p->inspected_name,
                    'department_id'=>$p->department_name  . ' #'. $p->department_id,
                    'enduse_id'=>$p->enduse_name  . ' #'. $p->enduse_id,
                    'purpose_id'=>$p->purpose_name  . ' #'. $p->purpose_id,
                ];
            
             }

        } else {
            $data = [
                'inspected_id'=>'',
                'department_id'=>'',
                'enduse_id'=>'',
                'purpose_id'=>''
            ];
         }

          return $data;

    }

    public function get_printables($id){

        $request_id = RequestHead::select('id','pr_no','mreqf_no')->where("replenish_id", "=", $id)->get();
        $issuance_id=array();
        foreach($request_id AS $req){
            $issuance = IssuanceHead::select('id','pr_no','mif_no')->where("request_head_id", "=", $req->id)->get();
            foreach($issuance AS $iss){
                $issuance_id[] = [
                    'id'=>$iss->id,
                    'pr_no'=>strtoupper($iss->pr_no),
                    'mif_no'=>$iss->mif_no,
                ];
            }
        }
        $data = [
            'request_id'=>$request_id,
            'issuance_id'=>$issuance_id
        ];
        return response()->json($data);
    }

    public function password_checker(Request $request){
        // $override_credentials = [
        //     'password' => $request->override_password,
        //     'user_type' => 'Admin',
        // ];

        $user_type = User::where('email', $request->override_email)->value('user_type');
        
        if($user_type != ''){
            $user = User::where('email', $request->override_email)->where('user_type', '=', 'Admin')->first();
            $hashedPassword = Hash::check($request->override_password, $user->password);
            $override_user_id = User::where('email', $request->override_email)->where('user_type', '=', 'Admin')->value('id');

            //  if (Auth::attempt($override_credentials)) {
            if($hashedPassword != '') {
                $response = [
                        'success' => true,
                        'message' => 'Override successfully',
                        'override_user_id' => $override_user_id
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Unauthorized'
            ];     
            }
        }else{
            $response = [
                'success' => false,
                'message' => 'Unauthorized'
        ];    
        }
        return response()->json($response,200);
    
    }

    public function override_update(Request $request, $id){
        $userid = Auth::id();
        $details_update = $request->input('receive_details');
        foreach(json_decode($details_update) as $du){

            if($du->inspected != ''){
                $ins = explode("~",$du->inspected);
                $ins_id = $ins[0];
                $ins_name = $ins[1];
            }else {
                $ins_id = 0;
                $ins_name = NULL;
            }

            if($du->department != ''){
                if (str_contains($du->department, '#')) {
                    $dept = explode("#",$du->department);
                    $dept_id = $dept[1];
                    $dept_name = $dept[0];
                } else {
                    $dept_name = $du->department;
          
                    $dept_id = Department::insertGetId([
                        'department_name'=>$dept_name
                    ]);
                }
            }else {
                $dept_id = 0;
                $dept_name = NULL;
            }

            if($du->purpose != ''){
                if (str_contains($du->purpose, '#')) {
                    $purp = explode("#",$du->purpose);
                    $purp_id = $purp[1];
                    $purp_name = $purp[0];
                } else {
                    $purp_name = $du->purpose;
          
                    $purp_id = Purpose::insertGetId([
                        'purpose_name'=>$purp_name
                    ]);
                }
            }else {
                $purp_id = 0;
                $purp_name = NULL;
            }

            if($du->enduse != ''){
                if (str_contains($du->enduse, '#')) {
                    $end = explode("#",$du->enduse);
                    $end_id = $end[1];
                    $end_name = $end[0];
                } else {
                    $end_name = $du->enduse;
                  
                    $end_id = Enduse::insertGetId([
                        'enduse_name'=>$end_name
                    ]);
                }
            } else {
                $end_id = 0;
                $end_name = NULL;
            }
        
            if($du->pr_no != 'WH STOCKS'){

                $receive_head_update=ReceiveHead::where('id', $id);
                $rec_head_up['si_or']=$request->si_or;
                $receive_head_update->update($rec_head_up);

                $receive_dets_update=ReceiveDetails::where('pr_no','=',$du->pr_no);
                $rec_det_up['inspected_id']=$ins_id;
                $rec_det_up['inspected_name']=$ins_name;
                $rec_det_up['department_id']=$dept_id;
                $rec_det_up['department_name']=$dept_name;
                $rec_det_up['purpose_id']=$purp_id;
                $rec_det_up['purpose_name']=$purp_name;
                $rec_det_up['enduse_id']=$end_id;
                $rec_det_up['enduse_name']=$end_name;
                $receive_dets_update->update($rec_det_up);

                $rec_id=array();
                if(ReceiveDetails::where('pr_no','=',$du->pr_no)->exists()){
                    $rec_dets = ReceiveDetails::where('pr_no','=',$du->pr_no)->get();
                    foreach($rec_dets as $rd){
                            $rec_id[]=$rd->id;
                        }
                }

                if(BackorderDetails::where('pr_no','=',$du->pr_no)->exists()){
                    $backorder_dets_update=BackorderDetails::where('pr_no','=',$du->pr_no);
                        $back_det_up['inspected_id']=$ins_id;
                        $back_det_up['inspected_name']=$ins_name;
                        $back_det_up['department_id']=$dept_id;
                        $back_det_up['department_name']=$dept_name;
                        $back_det_up['purpose_id']=$purp_id;
                        $back_det_up['purpose_name']=$purp_name;
                        $back_det_up['enduse_id']=$end_id;
                        $back_det_up['enduse_name']=$end_name;
                        $backorder_dets_update->update($back_det_up);
                }

                $bac_id=array();
                if(BackorderDetails::where('pr_no','=',$du->pr_no)->exists()){
                    $bac_dets = BackorderDetails::where('pr_no','=',$du->pr_no)->get();
                    foreach($bac_dets as $bd){
                            $bac_id[]=$bd->id;
                        }
                }

                if(RequestHead::where('pr_no','=',$du->pr_no)->exists()){
                    $request_dets_update=RequestHead::where('pr_no','=',$du->pr_no);
                        $req_det_up['department_id']=$dept_id;
                        $req_det_up['department_name']=$dept_name;
                        $req_det_up['purpose_id']=$purp_id;
                        $req_det_up['purpose_name']=$purp_name;
                        $req_det_up['enduse_id']=$end_id;
                        $req_det_up['enduse_name']=$end_name;
                        $request_dets_update->update($req_det_up);
                }

                $req_id=array();
                if(RequestHead::where('pr_no','=',$du->pr_no)->exists()){
                    $req_dets = RequestHead::where('pr_no','=',$du->pr_no)->get();
                    foreach($req_dets as $red){
                            $req_id[]=$red->id;
                        }
                }

                if(IssuanceHead::where('pr_no','=',$du->pr_no)->exists()){
                    $issuance_dets_update=IssuanceHead::where('pr_no','=',$du->pr_no);
                        $iss_det_up['department_id']=$dept_id;
                        $iss_det_up['department_name']=$dept_name;
                        $iss_det_up['purpose_id']=$purp_id;
                        $iss_det_up['purpose_name']=$purp_name;
                        $iss_det_up['enduse_id']=$end_id;
                        $iss_det_up['enduse_name']=$end_name;
                        $issuance_dets_update->update($iss_det_up);
                }

                $iss_id=array();
                if(IssuanceHead::where('pr_no','=',$du->pr_no)->exists()){
                    $iss_dets = IssuanceHead::where('pr_no','=',$du->pr_no)->get();
                    foreach($iss_dets as $isd){
                            $iss_id[]=$isd->id;
                        }
                }

                if(RestockHead::where('source_pr','=',$du->pr_no)->exists()){
                    $restock_dets_update=RestockHead::where('source_pr','=',$du->pr_no);
                        $res_det_up['department_id']=$dept_id;
                        $res_det_up['department']=$dept_name;
                        $res_det_up['purpose_id']=$purp_id;
                        $res_det_up['purpose']=$purp_name;
                        $res_det_up['enduse_id']=$end_id;
                        $res_det_up['enduse']=$end_name;
                        $restock_dets_update->update($res_det_up);
                }

                $res_id=array();
                if(RestockHead::where('source_pr','=',$du->pr_no)->exists()){
                    $res_dets = RestockHead::where('source_pr','=',$du->pr_no)->get();
                    foreach($res_dets as $rsd){
                            $res_id[]=$rsd->id;
                        }
                }

                $overridelogs['receive']=implode(", ",$rec_id);
                $overridelogs['backorder']=implode(", ",$bac_id);
                $overridelogs['request']=implode(", ",$req_id);
                $overridelogs['issuance']=implode(", ",$iss_id);
                $overridelogs['restock']=implode(", ",$res_id);
                $overridelogs['override_user_id']=$request->input('override_userid');
                $overridelogs['user_id']=$userid;
                OverrideLogs::create($overridelogs);
            }
            
        }
    }

    public function get_all_useracceptance(Request $request){
        $rec_list = ReceiveHead::with('receive_details','receive_items')->where('saved','=','1')->where('draft','=','0')->where('closed','=','1')->get();
        $receivearray=array();
        foreach($rec_list AS $rc){
            // $pending_items= ReceiveItems::with('item_status')->where('receive_head_id',$rc->id)->where('eval_flag', '0')->count();
            $loop_rec = ReceiveItems::where('receive_head_id',$rc->id)->where('eval_flag', '0')->get();
            $pending_items=0;
            foreach($loop_rec AS $lr){
                $item_status_id = $lr->item_status_id;
                $status_mode = ItemStatus::where('id',$lr->item_status_id)->value('modes');
                if($status_mode=='deduct'){
                    $damage_items=ReceiveItems::where('receive_head_id',$rc->id)->where('item_status_id', $lr->item_status_id)->count();
                }else{
                    $pending_items= ReceiveItems::with('item_status')->where('receive_head_id',$rc->id)->where('eval_flag', '0')->where('item_status_id',$lr->item_status_id)->count();
                    $damage_items=0;
                }
            }
            if($pending_items!=0 && $damage_items<=$pending_items){
                $receivearray[]=[
                    'id'=>$rc->id,
                    'receive_details'=>$rc->receive_details,
                    'pending_items'=>$pending_items,
                    'from'=>'Receive',
                    'Receive',
                    $rc->mrecf_no,
                    date('F d,Y',strtotime($rc->receive_date)),
                    $rc->dr_no,
                    $rc->po_no,
                    $rc->si_or,
                    $rc->waybill_no,
                    '',
                    '',
                    ''
                ];
            }
        }

        $backorder_list = BackorderHead::with('backorder_details','backorder_items')->where('saved','=','1')->where('draft','=','0')->get();
        foreach($backorder_list AS $bl){
            $pendingback_items = BackorderItems::where('backorder_head_id',$bl->id)->where('eval_flag', '0')->count();
            $po_no = BackorderHead::where('id',$bl->id)->value('po_no');
            $dr_no = ReceiveHead::where('po_no',$po_no)->value('dr_no');
            if($pendingback_items!=0){
                $receivearray[]=[
                    'id'=>$bl->id,
                    'receive_details'=>$bl->backorder_details,
                    'pending_items'=>$pendingback_items,
                    'from'=>'Backorder',
                    'Backorder',
                    $bl->mrecf_no,
                    date('F d,Y',strtotime($bl->backorder_date)),
                    ($bl->dr_no!=null) ? $bl->dr_no : $dr_no,
                    $bl->po_no,
                    $bl->si_or,
                    $bl->waybill_no,
                    '',
                    '',
                    ''
                ];
            }
        }
        return response()->json([
            'receivearray'=>$receivearray,
        ],200);
    }

    public function get_all_useracceptance_dashboard(Request $request){
        $filter=$request->get('filter');
        $rec_list = ReceiveHead::with('receive_details','receive_items')->where('saved','=','1')->where('draft','=','0')->where('closed','=','1')->when($request->get('filter'), function ($query, $filter) {
            $query->where('mrecf_no', 'LIKE', '%' . $filter . '%')
            ->orWhere('receive_date', 'LIKE', '%' . date('Y-m-d',strtotime($filter)) . '%')
            ->orWhere('dr_no', 'LIKE', '%' . $filter . '%')
            ->orWhere('po_no', 'LIKE', '%' . $filter . '%')
            ->orWhere('si_or', 'LIKE', '%' . $filter . '%')
            ->orWhere('waybill_no', 'LIKE', '%' . $filter . '%')
            ->orWhereHas('receive_details', function($query) use ($filter) {
                $query->where('pr_no', 'LIKE', '%'.$filter.'%');
            });
        })->orderBy('mrecf_no','DESC')->get();
        $receivearray=array();
        foreach($rec_list AS $rc){
            // $pending_items = ReceiveItems::where('receive_head_id',$rc->id)->where('eval_flag', '0')->count();
            $loop_rec = ReceiveItems::where('receive_head_id',$rc->id)->where('eval_flag', '0')->get();
            $pending_items=0;
            foreach($loop_rec AS $lr){
                $item_status_id = $lr->item_status_id;
                $status_mode = ItemStatus::where('id',$lr->item_status_id)->value('modes');
                if($status_mode=='deduct'){
                    $damage_items=ReceiveItems::where('receive_head_id',$rc->id)->where('item_status_id', $lr->item_status_id)->count();
                }else{
                    $pending_items= ReceiveItems::with('item_status')->where('receive_head_id',$rc->id)->where('eval_flag', '0')->where('item_status_id',$lr->item_status_id)->count();
                    $damage_items=0;
                }
            }
            if($pending_items!=0 && $damage_items<=$pending_items){
                $receivearray[]=[
                    'id'=>$rc->id,
                    'receive_details'=>$rc->receive_details,
                    'pending_items'=>$pending_items,
                    'mrecf_no'=>$rc->mrecf_no,
                    'receive_date'=>date('F d,Y',strtotime($rc->receive_date)),
                    'dr_no'=>$rc->dr_no,
                    'po_no'=>$rc->po_no,
                    'si_no'=>$rc->si_or,
                    'waybill_no'=>$rc->waybill_no,
                    'from'=>'Receive',
                ];
            }
        }
        $backorder_list = BackorderHead::with('backorder_details','backorder_items')->where('saved','=','1')->where('draft','=','0')->orderBy('mrecf_no','DESC')->get();
        foreach($backorder_list AS $bl){
            $pendingback_items = BackorderItems::where('backorder_head_id',$bl->id)->where('eval_flag', '0')->count();
            $po_no = BackorderHead::where('id',$bl->id)->value('po_no');
            $dr_no = ReceiveHead::where('po_no',$po_no)->value('dr_no');
            if($pendingback_items!=0){
                $receivearray[]=[
                    'id'=>$bl->id,
                    'receive_details'=>$bl->backorder_details,
                    'pending_items'=>$pendingback_items,
                    'mrecf_no'=>$bl->mrecf_no,
                    'receive_date'=>date('F d,Y',strtotime($bl->backorder_date)),
                    'dr_no'=> ($bl->dr_no!=null) ? $bl->dr_no : $dr_no,
                    'po_no'=>$bl->po_no,
                    'si_no'=>$bl->si_or,
                    'waybill_no'=>$bl->waybill_no,
                    'from'=>'Backorder',
                ];
            }
        }
        return response()->json([
            'receivearray'=>$receivearray,
        ],200);
    }

    public function save_accepted(Request $request){
        if(count(json_decode($request->input("checkbox")))>0){
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!=''){
                    $update_accepted = ReceiveItems::where('id',$c)->first();
                    $update_accepted->update([
                        'eval_flag' => "1",
                        'eval_date' => $request->input("eval_date"),
                        'eval_user' => $request->input("eval_user"),
                    ]);
                }
            }
            $receive_head_id=$request->input('receive_head_id');
            $prno = ReceiveDetails::where('receive_head_id','=',$receive_head_id)->get();
            foreach($prno AS $pr){
                $items = ReceiveItems::where('receive_details_id','=',$pr->id)->where('eval_flag','1')->where('acceptance_done','0')->get();
                foreach($items AS $it){
                    $mode = ItemStatus::where('id','=',$it->item_status_id)->value('modes');
                    if($mode == 'add'){
                        if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){
                            $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
                            $update = PRItems::find($pritems_id);
                            $update->receive_qty = $update->receive_qty + $it->rec_quantity;
                            $update->balance = $update->balance + $it->rec_quantity;
                            $update->save();
                        } else { 
                            $itemdata['pr_no']=strtoupper($pr->pr_no);
                            $itemdata['item_id']=$it->item_id;
                            $itemdata['receive_qty']=$it->rec_quantity;
                            $itemdata['balance']=$it->rec_quantity;
                            PRItems::create($itemdata);
                        }
                        $exists_flag= Items::where('id','=',$it->item_id)->where('composite_flag','=','0')->where('variant_flag', '=', '0')->exists();
                        $update_balance = Items::find($it->item_id);
                        $update_balance->running_balance = $update_balance->running_balance + $it->rec_quantity;
                        if($exists_flag > 0){
                            $update_balance->variant_flag ='1';
                        }
                        $update_balance->save();
                        if($it->pr_replenish == 1){
                            $add_to_pr = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->value('borrowed_from');
                            $borrowed_qty = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->value('quantity');
                            $borrow_id = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->where("borrowed_from","=", $add_to_pr)->value('id');

                            $updatepr_rep = ReceiveItems::where("id","=", $it->id)->first();
                            $updatepr_rep->prno_replenish = $add_to_pr;
                            $updatepr_rep->save();

                            $update_borrow   = BorrowDetails::find($borrow_id);        
                            $update_borrow->balance = $update_borrow->balance - $borrowed_qty;
                            $update_borrow->replenished_qty = $borrowed_qty;
                            $update_borrow->save();

                            $updateDeductPR = PRItems::where("item_id","=", $it->item_id)->where("pr_no", "=",$pr->pr_no)->first();
                            $updateDeductPR->replenish_deduct = $updateDeductPR->replenish_deduct + $borrowed_qty;
                            $updateDeductPR->balance = $updateDeductPR->balance - $borrowed_qty;  
                            $updateDeductPR->save();

                            $updateaddPR = PRItems::where("item_id","=", $it->item_id)->where("pr_no", "=", $add_to_pr)->first();
                            $updateaddPR->replenish_add = $updateaddPR->replenish_add  + $borrowed_qty;
                            $updateaddPR->balance = $updateaddPR->balance + $borrowed_qty;  
                            $updateaddPR->save();                       
                        }
                    } else if($mode == 'deduct'){
                        if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){
                            $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
                            $update2 = PRItems::find($pritems_id);
                            $update2->damage_qty = $update2->damage_qty + $it->rec_quantity;
                            $update2->save();
                        } else { 
                            $itemdata2['pr_no']=strtoupper($pr->pr_no);
                            $itemdata2['item_id']=$it->item_id;
                            $itemdata2['damage_qty']=$it->rec_quantity;
                            PRItems::create($itemdata2);
                        }
                    }
                    $total_cost = $it->unit_cost + $it->shipping_cost;
                    if(Variants::where('supplier_id','=',$it->supplier_id)->where('item_id', '=', $it->item_id)->where('brand', '=', $it->brand)->where('item_status_id', '=', $it->item_status_id)->where('expiration', '=', $it->expiry_date)->where('uom', '=', $it->uom)->where('color', '=', $it->color)->where('size', '=', $it->size)->where('average_cost','=',$total_cost)->exists()){
                        $variant_id = Variants::where('supplier_id','=',$it->supplier_id)->where('item_id', '=', $it->item_id)->where('brand', '=', $it->brand)->where('item_status_id', '=', $it->item_status_id)->where('expiration', '=', $it->expiry_date)->where('uom', '=', $it->uom)->where('color', '=', $it->color)->where('size', '=', $it->size)->where('average_cost','=',$total_cost)->value('id');
                        $update_var = Variants::find($variant_id);
                        $update_var->quantity = $update_var->quantity + $it->rec_quantity;
                        $update_var->receive_flag = '1';
                        $update_var->save();

                        $update_rec = ReceiveItems::find($it->id);
                        $update_rec->variant_id = $variant_id;
                        $update_rec->acceptance_done = '1';
                        $update_rec->save();
                        if($mode == 'add'){
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->receive_qty = $update->receive_qty + $it->rec_quantity;
                                $update->balance = $update->balance + $it->rec_quantity;
                                $update->save();
                            } else { 
                                $vardata['variant_id']=$variant_id;
                                $vardata['item_id']=$it->item_id;
                                $vardata['receive_qty']=$it->rec_quantity;
                                $vardata['balance']=$it->rec_quantity;
                                VariantsBalance::create($vardata);
                            }
                            if($it->pr_replenish != 1){
                                if(PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $it->rec_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=strtoupper($pr->pr_no);
                                    $pivdata['variant_id']=$variant_id;
                                    $pivdata['item_id']=$it->item_id;
                                    $pivdata['quantity']=$it->rec_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            }
                        } else {
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->damage_qty = $update->damage_qty + $it->rec_quantity;
                                $update->save();
                            } else { 
                                $vardata['variant_id']=$variant_id;
                                $vardata['item_id']=$it->item_id;
                                $vardata['damage_qty']=$it->rec_quantity;
                                VariantsBalance::create($vardata);
                            }
                        }
                    } else {
                        $var_data['item_id'] = $it->item_id;
                        $var_data['supplier_id'] = $it->supplier_id;
                        $var_data['supplier_name'] = $it->supplier_name ;
                        $var_data['catalog_no'] = $it->catalog_no;
                        $var_data['brand'] = $it->brand;
                        $var_data['color'] = $it->color;
                        $var_data['size'] = $it->size;
                        $var_data['barcode'] = $it->barcode;
                        $var_data['expiration'] = $it->expiry_date;
                        $var_data['serial_no'] = $it->serial_no;
                        $var_data['uom'] = $it->uom;
                        $var_data['quantity'] = $it->rec_quantity;
                        $var_data['unit_cost'] = $it->unit_cost;
                        $var_data['currency'] = $it->currency;
                        $var_data['shipping_cost'] = $it->shipping_cost;
                        $var_data['average_cost'] = $it->unit_cost + $it->shipping_cost;
                        $var_data['item_status_id'] = $it->item_status_id;
                        $var_data['receive_flag'] = 1;
                        $variant_id = Variants::insertGetId($var_data);
                        if($mode == 'add'){
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->receive_qty = $update->receive_qty + $it->rec_quantity;
                                $update->balance = $update->balance + $it->rec_quantity;
                                $update->save();
                            } else { 
                                $vardata['variant_id']=$variant_id;
                                $vardata['item_id']=$it->item_id;
                                $vardata['receive_qty']=$it->rec_quantity;
                                $vardata['balance']=$it->rec_quantity;
                                VariantsBalance::create($vardata);
                            }
                            if($it->pr_replenish != 1){
                                if(PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $it->rec_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=strtoupper($pr->pr_no);
                                    $pivdata['variant_id']=$variant_id;
                                    $pivdata['item_id']=$it->item_id;
                                    $pivdata['quantity']=$it->rec_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            }
                        } else {
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->damage_qty = $update->damage_qty + $it->rec_quantity;
                                $update->save();

                            } else { 
                                $vardata1['variant_id']=$variant_id;
                                $vardata1['item_id']=$it->item_id;
                                $vardata1['damage_qty']=$it->rec_quantity;
                                VariantsBalance::create($vardata1);
                            }
                        }
                        $update_rec = ReceiveItems::find($it->id);
                        $update_rec->variant_id = $variant_id;
                        $update_rec->acceptance_done = '1';
                        $update_rec->save();
                    }
                }
            }
            $get_rep = ReceiveDetails::with('receive_items');
            $get_rep->whereHas('receive_items', function ($get_rep) use($receive_head_id) {
                $get_rep->where('receive_head_id','=',$receive_head_id)->where('pr_replenish','=','1');
            });
            $rep_pr = $get_rep->get();
            foreach($rep_pr AS $rpr){
                ///////////////////////////////// CREATE REQUEST ///////////////////////////////
                $user_id_req=ReceiveHead::where('id',$receive_head_id)->value('user_id');
                $curr_year = date('Y');
                $curr_mo = date('m');
                if(Mreqf::where('year', '=', $curr_year)->exists()) {
                    $mreqf = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
                    $max_value = str_pad($mreqf,4,"0",STR_PAD_LEFT);;
                } else {
                    $max_value = '0001';
                }
                Mreqf::create([
                    'year' => date("Y"),
                    'series'=>$max_value
                ]);
                $mreqf_no = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
                foreach($rpr->receive_items AS $rep_ri2){
                    $repl_pr[] = $rep_ri2->prno_replenish;
                } 
                $all_rep_pr = array_unique($repl_pr);
                $remarks = "Replenishment for PR# ";
                foreach($all_rep_pr AS $all_rep){
                    $remarks .= $all_rep .", ";
                }
                $remarks = substr($remarks, 0, -2);
                $reqhead['request_date'] = date('Y-m-d');
                $reqhead['request_time'] = date('H:i:s');
                $reqhead['mreqf_no'] = $mreqf_no;
                $reqhead['request_type']='With PR';
                $reqhead['pr_no']=strtoupper($rpr->pr_no);
                $reqhead['department_id']=$rpr->department_id;
                $reqhead['department_name']=$rpr->department_name;
                $reqhead['purpose_id']=$rpr->purpose_id;
                $reqhead['purpose_name']=$rpr->purpose_name;
                $reqhead['enduse_id']=$rpr->enduse_id;
                $reqhead['enduse_name']=$rpr->enduse_name;
                $reqhead['remarks']=$remarks;
                $reqhead['user_id']=$user_id_req;
                $reqhead['close']='1';
                $reqhead['saved']='1';
                $reqhead['replenish_id']=$receive_head_id;
                $request_head_id = RequestHead::insertGetId($reqhead);
                ///////////////////////////////// CREATE ISSUANCE ///////////////////////////////
                if(MIF::where('year', '=', $curr_year)->exists()) {
                    $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
                    $max_value_iss = str_pad($mif,4,"0",STR_PAD_LEFT);;
                } else {
                    $max_value_iss = '0001';
                }
                MIF::create([
                    'year' => date("Y"),
                    'series'=>$max_value_iss
                ]);
                $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_iss;
                $prepared_by=User::where('id',$user_id_req)->value('name');
                $prepared_des=User::where('id',$user_id_req)->value('position');
                $isshead['request_head_id']=$request_head_id;
                $isshead['mreqf_no']=$mreqf_no;
                $isshead['mif_no']=$mif_no;
                $isshead['pr_no']=strtoupper($rpr->pr_no);
                $isshead['issuance_date']=date('Y-m-d');
                $isshead['issuance_time']=date('H:i:s');
                $isshead['department_id']=$rpr->department_id;
                $isshead['department_name']=$rpr->department_name;
                $isshead['purpose_id']=$rpr->purpose_id;
                $isshead['purpose_name']=$rpr->purpose_name;
                $isshead['enduse_id']=$rpr->enduse_id;
                $isshead['enduse_name']=$rpr->enduse_name;
                $isshead['remarks']=$remarks;
                $isshead['user_id']=$user_id_req;
                $isshead['prepared_by']=$user_id_req;
                $isshead['prepared_by_name']=$prepared_by;
                $isshead['prepared_by_pos']=$prepared_des;
                $issuance_head_id = IssuanceHead::insertGetId($isshead);
                foreach($rpr->receive_items AS $rep_ri){
                    $borrow_qty = BorrowDetails::where('borrowed_by','=',$rpr->pr_no)->where('item_id','=',$rep_ri->item_id)->value('quantity');
                    if($borrow_qty == $rep_ri->rec_quantity){
                        $qty = $rep_ri->borrow_qty;
                        $piv_qty = 0;
                    } else if($borrow_qty < $rep_ri->rec_quantity){
                        $qty = $borrow_qty;
                        $piv_qty = $rep_ri->rec_quantity - $borrow_qty;
                    }
                    $reqdet['request_head_id']=$request_head_id;
                    $reqdet['item_id']=$rep_ri->item_id;
                    $reqdet['item_description']=$rep_ri->item_description;
                    $reqdet['variant_id']=$rep_ri->variant_id;
                    $reqdet['unit_cost']=$rep_ri->unit_cost;
                    $reqdet['shipping_cost']=$rep_ri->shipping_cost;
                    $reqdet['quantity']=$qty;
                    $reqdet['issued_qty']=$qty;
                    $request_item_id = RequestItems::insertGetId($reqdet);
                    $instock = Items::where('id','=',$rep_ri->item_id)->value('running_balance');
                    $issitems['issuance_head_id']=$issuance_head_id;
                    $issitems['item_id']=$rep_ri->item_id;
                    $issitems['item_description']=$rep_ri->item_description;
                    $issitems['variant_id']=$rep_ri->variant_id;
                    $issitems['request_items_id']=$request_item_id;
                    $issitems['inventory_balance']=$instock;
                    $issitems['unit_cost']=$rep_ri->unit_cost;
                    $issitems['shipping_cost']=$rep_ri->shipping_cost;
                    $issitems['request_qty']=$qty;
                    $issitems['issued_qty']=$qty;
                    IssuanceItems::create($issitems);
                    if(PIVBalance::where('pr_no','=',$rpr->pr_no)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->exists()){
                        $piv_id = PIVBalance::where('pr_no','=',$rpr->pr_no)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->value('id');
                        $update = PIVBalance::find($piv_id);
                        $update->quantity = $update->quantity + $piv_qty;
                        $update->save();
                    } else {
                        $pivdata['pr_no']=strtoupper($rpr->pr_no);
                        $pivdata['variant_id']=$rep_ri->variant_id;
                        $pivdata['item_id']=$rep_ri->item_id;
                        $pivdata['quantity']=$piv_qty;
                        PIVBalance::create($pivdata);
                    }
                    if(PIVBalance::where('pr_no','=',$rep_ri->prno_replenish)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->exists()){
                        $piv_id = PIVBalance::where('pr_no','=',$rep_ri->prno_replenish)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->value('id');
                        $update = PIVBalance::find($piv_id);
                        $update->quantity = $update->quantity + $borrow_qty;
                        $update->save();
                    } else {
                        $pivdata['pr_no']=strtoupper($rep_ri->prno_replenish);
                        $pivdata['variant_id']=$rep_ri->variant_id;
                        $pivdata['item_id']=$rep_ri->item_id;
                        $pivdata['quantity']=$borrow_qty;
                        PIVBalance::create($pivdata);
                    }
                }        
            }
        }else{
            return 'error';
        }
    }

    public function save_rejected(Request $request){
        if(count(json_decode($request->input("checkbox")))!=0){
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!='' && (float) $request->input("rejected_qty"."$c")!=0){
                    $update_accepted = ReceiveItems::where('id',$c)->first();
                    $rec_qty = ReceiveItems::where('id',$c)->value('rec_quantity');
                    $update_accepted->update([
                        'eval_flag' => "2",
                        'eval_date' => $request->input("eval_date"),
                        'eval_user' => $request->input("eval_user"),
                        // 'rejected_qty' => (float) $request->input("rejected_qty"."$c"),
                        // 'rec_quantity' => (float) $rec_qty - (float) $request->input("rejected_qty"."$c"),
                        'eval_reason' => $request->input("eval_reason"),
                    ]);
                }
            }
            $receive_head_id=$request->input("receive_head_id");
            $prno = ReceiveDetails::where('receive_head_id','=',$receive_head_id)->get();
            foreach($prno AS $pr){
                $items = ReceiveItems::where('eval_flag','=','2')->where('acceptance_done','=','0')->where('receive_details_id','=',$pr->id)->get();
                foreach($items AS $it){
                    $mode = ItemStatus::where('id','=',$it->item_status_id)->value('modes');
                    if($mode == 'add'){
                        if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){
                            $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
                            $update = PRItems::find($pritems_id);
                            $update->receive_qty = $update->receive_qty + $it->rec_quantity;
                            $update->balance = $update->balance + $it->rec_quantity;
                            $update->save();
                        } else { 
                            $itemdata['pr_no']=strtoupper($pr->pr_no);
                            $itemdata['item_id']=$it->item_id;
                            $itemdata['receive_qty']=$it->rec_quantity;
                            $itemdata['balance']=$it->rec_quantity;
                            PRItems::create($itemdata);
                        }
                        $exists_flag= Items::where('id','=',$it->item_id)->where('composite_flag','=','0')->where('variant_flag', '=', '0')->exists();
                        $update_balance = Items::find($it->item_id);
                        $update_balance->running_balance = $update_balance->running_balance + $it->rec_quantity;
                        if($exists_flag > 0){
                            $update_balance->variant_flag ='1';
                        }
                        $update_balance->save();
                        if($it->pr_replenish == 1){
                            $add_to_pr = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->value('borrowed_from');
                            $borrowed_qty = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->value('quantity');
                            $borrow_id = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->where("borrowed_from","=", $add_to_pr)->value('id');

                            $updatepr_rep = ReceiveItems::where("id","=", $it->id)->first();
                            $updatepr_rep->prno_replenish = $add_to_pr;
                            $updatepr_rep->save();

                            $update_borrow   = BorrowDetails::find($borrow_id);        
                            $update_borrow->balance = $update_borrow->balance - $borrowed_qty;
                            $update_borrow->replenished_qty = $borrowed_qty;
                            $update_borrow->save();

                            $updateDeductPR = PRItems::where("item_id","=", $it->item_id)->where("pr_no", "=",$pr->pr_no)->first();
                            $updateDeductPR->replenish_deduct = $updateDeductPR->replenish_deduct + $borrowed_qty;
                            $updateDeductPR->balance = $updateDeductPR->balance - $borrowed_qty;  
                            $updateDeductPR->save();

                            $updateaddPR = PRItems::where("item_id","=", $it->item_id)->where("pr_no", "=", $add_to_pr)->first();
                            $updateaddPR->replenish_add = $updateaddPR->replenish_add  + $borrowed_qty;
                            $updateaddPR->balance = $updateaddPR->balance + $borrowed_qty;  
                            $updateaddPR->save();                       
                        }
                    } else if($mode == 'deduct'){
                        if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){
                            $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
                            $update2 = PRItems::find($pritems_id);
                            $update2->damage_qty = $update2->damage_qty + $it->rec_quantity;
                            $update2->save();
                        } else { 
                            $itemdata2['pr_no']=strtoupper($pr->pr_no);
                            $itemdata2['item_id']=$it->item_id;
                            $itemdata2['damage_qty']=$it->rec_quantity;
                            PRItems::create($itemdata2);
                        }
                    }
                    $total_cost = $it->unit_cost + $it->shipping_cost;
                    if(!Variants::where('supplier_id','=',$it->supplier_id)->where('item_id', '=', $it->item_id)->where('brand', '=', $it->brand)->where('item_status_id', '=', $it->item_status_id)->where('expiration', '=', $it->expiry_date)->where('uom', '=', $it->uom)->where('color', '=', $it->color)->where('size', '=', $it->size)->where('average_cost','=',$total_cost)->exists()){
                        $var_data['item_id'] = $it->item_id;
                        $var_data['supplier_id'] = $it->supplier_id;
                        $var_data['supplier_name'] = $it->supplier_name ;
                        $var_data['catalog_no'] = $it->catalog_no;
                        $var_data['brand'] = $it->brand;
                        $var_data['color'] = $it->color;
                        $var_data['size'] = $it->size;
                        $var_data['barcode'] = $it->barcode;
                        $var_data['expiration'] = $it->expiry_date;
                        $var_data['serial_no'] = $it->serial_no;
                        $var_data['uom'] = $it->uom;
                        $var_data['quantity'] = $it->rec_quantity;
                        $var_data['unit_cost'] = $it->unit_cost;
                        $var_data['currency'] = $it->currency;
                        $var_data['shipping_cost'] = $it->shipping_cost;
                        $var_data['average_cost'] = $it->unit_cost + $it->shipping_cost;
                        $var_data['item_status_id'] = $it->item_status_id;
                        $var_data['receive_flag'] = 1;
                        $variant_id = Variants::insertGetId($var_data);
                        if($mode == 'add'){
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->receive_qty = $update->receive_qty + $it->rec_quantity;
                                $update->balance = $update->balance + $it->rec_quantity;
                                $update->save();
                            } else { 
                                $vardata['variant_id']=$variant_id;
                                $vardata['item_id']=$it->item_id;
                                $vardata['receive_qty']=$it->rec_quantity;
                                $vardata['balance']=$it->rec_quantity;
                                VariantsBalance::create($vardata);
                            }
                            if($it->pr_replenish != 1){
                                if(PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $it->rec_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=strtoupper($pr->pr_no);
                                    $pivdata['variant_id']=$variant_id;
                                    $pivdata['item_id']=$it->item_id;
                                    $pivdata['quantity']=$it->rec_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            }
                        } else {
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->damage_qty = $update->damage_qty + $it->rec_quantity;
                                $update->save();

                            } else { 
                                $vardata1['variant_id']=$variant_id;
                                $vardata1['item_id']=$it->item_id;
                                $vardata1['damage_qty']=$it->rec_quantity;
                                VariantsBalance::create($vardata1);
                            }
                        }
                        $update_rec = ReceiveItems::find($it->id);
                        $update_rec->acceptance_done = '1';
                        $update_rec->variant_id = $variant_id;
                        $update_rec->save();
                    } 
                }
            }
            $get_rep = ReceiveDetails::with('receive_items');
            $get_rep->whereHas('receive_items', function ($get_rep) use($receive_head_id) {
                $get_rep->where('receive_head_id','=',$receive_head_id)->where('pr_replenish','=','1');
            });
            $rep_pr = $get_rep->get();
            foreach($rep_pr AS $rpr){
                ///////////////////////////////// CREATE REQUEST ///////////////////////////////
                $user_id_req=ReceiveHead::where('id',$receive_head_id)->value('user_id');
                $curr_year = date('Y');
                $curr_mo = date('m');
                if(Mreqf::where('year', '=', $curr_year)->exists()) {
                    $mreqf = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
                    $max_value = str_pad($mreqf,4,"0",STR_PAD_LEFT);;
                } else {
                    $max_value = '0001';
                }
                Mreqf::create([
                    'year' => date("Y"),
                    'series'=>$max_value
                ]);
                $mreqf_no = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
                foreach($rpr->receive_items AS $rep_ri2){
                    $repl_pr[] = $rep_ri2->prno_replenish;
                } 
                $all_rep_pr = array_unique($repl_pr);
                $remarks = "Replenishment for PR# ";
                foreach($all_rep_pr AS $all_rep){
                    $remarks .= $all_rep .", ";
                }
                $remarks = substr($remarks, 0, -2);
                $reqhead['request_date'] = date('Y-m-d');
                $reqhead['request_time'] = date('H:i:s');
                $reqhead['mreqf_no'] = $mreqf_no;
                $reqhead['request_type']='With PR';
                $reqhead['pr_no']=strtoupper($rpr->pr_no);
                $reqhead['department_id']=$rpr->department_id;
                $reqhead['department_name']=$rpr->department_name;
                $reqhead['purpose_id']=$rpr->purpose_id;
                $reqhead['purpose_name']=$rpr->purpose_name;
                $reqhead['enduse_id']=$rpr->enduse_id;
                $reqhead['enduse_name']=$rpr->enduse_name;
                $reqhead['remarks']=$remarks;
                $reqhead['user_id']=$user_id_req;
                $reqhead['close']='1';
                $reqhead['saved']='1';
                $reqhead['replenish_id']=$receive_head_id;
                $request_head_id = RequestHead::insertGetId($reqhead);
                ///////////////////////////////// CREATE ISSUANCE ///////////////////////////////
                if(MIF::where('year', '=', $curr_year)->exists()) {
                    $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
                    $max_value_iss = str_pad($mif,4,"0",STR_PAD_LEFT);;
                } else {
                    $max_value_iss = '0001';
                }
                MIF::create([
                    'year' => date("Y"),
                    'series'=>$max_value_iss
                ]);
                $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_iss;
                $prepared_by=User::where('id',$user_id_req)->value('name');
                $prepared_des=User::where('id',$user_id_req)->value('position');
                $isshead['request_head_id']=$request_head_id;
                $isshead['mreqf_no']=$mreqf_no;
                $isshead['mif_no']=$mif_no;
                $isshead['pr_no']=strtoupper($rpr->pr_no);
                $isshead['issuance_date']=date('Y-m-d');
                $isshead['issuance_time']=date('H:i:s');
                $isshead['department_id']=$rpr->department_id;
                $isshead['department_name']=$rpr->department_name;
                $isshead['purpose_id']=$rpr->purpose_id;
                $isshead['purpose_name']=$rpr->purpose_name;
                $isshead['enduse_id']=$rpr->enduse_id;
                $isshead['enduse_name']=$rpr->enduse_name;
                $isshead['remarks']=$remarks;
                $isshead['user_id']=$user_id_req;
                $isshead['prepared_by']=$user_id_req;
                $isshead['prepared_by_name']=$prepared_by;
                $isshead['prepared_by_pos']=$prepared_des;
                $issuance_head_id = IssuanceHead::insertGetId($isshead);
                foreach($rpr->receive_items AS $rep_ri){
                    $borrow_qty = BorrowDetails::where('borrowed_by','=',$rpr->pr_no)->where('item_id','=',$rep_ri->item_id)->value('quantity');
                    if($borrow_qty == $rep_ri->rec_quantity){
                        $qty = $rep_ri->borrow_qty;
                        $piv_qty = 0;
                    } else if($borrow_qty < $rep_ri->rec_quantity){
                        $qty = $borrow_qty;
                        $piv_qty = $rep_ri->rec_quantity - $borrow_qty;
                    }
                    $reqdet['request_head_id']=$request_head_id;
                    $reqdet['item_id']=$rep_ri->item_id;
                    $reqdet['item_description']=$rep_ri->item_description;
                    $reqdet['variant_id']=$rep_ri->variant_id;
                    $reqdet['unit_cost']=$rep_ri->unit_cost;
                    $reqdet['shipping_cost']=$rep_ri->shipping_cost;
                    $reqdet['quantity']=$qty;
                    $reqdet['issued_qty']=$qty;
                    $request_item_id = RequestItems::insertGetId($reqdet);
                    $instock = Items::where('id','=',$rep_ri->item_id)->value('running_balance');
                    $issitems['issuance_head_id']=$issuance_head_id;
                    $issitems['item_id']=$rep_ri->item_id;
                    $issitems['item_description']=$rep_ri->item_description;
                    $issitems['variant_id']=$rep_ri->variant_id;
                    $issitems['request_items_id']=$request_item_id;
                    $issitems['inventory_balance']=$instock;
                    $issitems['unit_cost']=$rep_ri->unit_cost;
                    $issitems['shipping_cost']=$rep_ri->shipping_cost;
                    $issitems['request_qty']=$qty;
                    $issitems['issued_qty']=$qty;
                    IssuanceItems::create($issitems);
                    if(PIVBalance::where('pr_no','=',$rpr->pr_no)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->exists()){
                        $piv_id = PIVBalance::where('pr_no','=',$rpr->pr_no)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->value('id');
                        $update = PIVBalance::find($piv_id);
                        $update->quantity = $update->quantity + $piv_qty;
                        $update->save();
                    } else {
                        $pivdata['pr_no']=strtoupper($rpr->pr_no);
                        $pivdata['variant_id']=$rep_ri->variant_id;
                        $pivdata['item_id']=$rep_ri->item_id;
                        $pivdata['quantity']=$piv_qty;
                        PIVBalance::create($pivdata);
                    }
                    if(PIVBalance::where('pr_no','=',$rep_ri->prno_replenish)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->exists()){
                        $piv_id = PIVBalance::where('pr_no','=',$rep_ri->prno_replenish)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->value('id');
                        $update = PIVBalance::find($piv_id);
                        $update->quantity = $update->quantity + $borrow_qty;
                        $update->save();
                    } else {
                        $pivdata['pr_no']=strtoupper($rep_ri->prno_replenish);
                        $pivdata['variant_id']=$rep_ri->variant_id;
                        $pivdata['item_id']=$rep_ri->item_id;
                        $pivdata['quantity']=$borrow_qty;
                        PIVBalance::create($pivdata);
                    }
                }        
            }
            $x=0;
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!='' && (float) $request->input("rejected_qty"."$c")!=0){
                    $item_id = ReceiveItems::where('id',$c)->value('item_id');
                    $variant_id = ReceiveItems::where('id',$c)->value('variant_id');
                    $receive_details_id = ReceiveItems::where('id',$c)->value('receive_details_id');
                    $pr_no = ReceiveDetails::where('id',$receive_details_id)->value('pr_no');
                    $update_accepted1 = ReceiveItems::where('id',$c)->first();
                    $rec_qty1 = ReceiveItems::where('id',$c)->value('rec_quantity');
                    $rejected_qty = ReceiveItems::where('id',$c)->value('rejected_qty');
                    $update_accepted1->update([
                        'rejected_qty' => (float) $rejected_qty + (float) $request->input("rejected_qty"."$c"),
                        'rec_quantity' => (float) $rec_qty1 - (float) $request->input("rejected_qty"."$c"),
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
                        'rejected_qty' => (float) $rejected_qty + (float) $request->input("rejected_qty"."$c"),
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

    public function get_all_useracceptance_accepted(Request $request){
        $rec_list = ReceiveHead::with('receive_details','receive_items')->where('saved','=','1')->where('draft','=','0')->where('closed','=','1')->get();
        $receivearray=array();
        foreach($rec_list AS $rc){
            // $pending_items = ReceiveItems::where('receive_head_id',$rc->id)->where('eval_flag', '0')->count();
            $loop_rec = ReceiveItems::where('receive_head_id',$rc->id)->get();
            $pending_items=0;
            $damage_items=0;
            foreach($loop_rec AS $lr){
                $item_status_id = $lr->item_status_id;
                $status_mode = ItemStatus::where('id',$lr->item_status_id)->value('modes');
                if($status_mode=='add'){
                    $damage_items=ReceiveItems::where('receive_head_id',$rc->id)->where('item_status_id',$lr->item_status_id)->count();
                    $pending_items= ReceiveItems::with('item_status')->where('receive_head_id',$rc->id)->where('eval_flag', '0')->where('item_status_id',$lr->item_status_id)->count();
                }
            }
            if($pending_items==0 && ($damage_items>=1)){
                $receivearray[]=[
                    'id'=>$rc->id,
                    'receive_details'=>$rc->receive_details,
                    'from'=>'Receive',
                    'Receive',
                    $rc->mrecf_no,
                    date('F d,Y',strtotime($rc->receive_date)),
                    $rc->dr_no,
                    $rc->po_no,
                    $rc->si_or,
                    $rc->waybill_no,
                    '',
                    ''
                ];
            }
        }

        $backorder_list = BackorderHead::with('backorder_details','backorder_items')->where('saved','=','1')->where('draft','=','0')->get();
        foreach($backorder_list AS $bl){
            $pendingback_items = BackorderItems::where('backorder_head_id',$bl->id)->where('eval_flag', '0')->count();
            $po_no = BackorderHead::where('id',$bl->id)->value('po_no');
            $dr_no = ReceiveHead::where('po_no',$po_no)->value('dr_no');
            if($pendingback_items==0){
                $receivearray[]=[
                    'id'=>$bl->id,
                    'receive_details'=>$bl->backorder_details,
                    'pending_items'=>$pendingback_items,
                    'from'=>'Backorder',
                    'Backorder',
                    $bl->mrecf_no,
                    date('F d,Y',strtotime($bl->backorder_date)),
                    ($bl->dr_no!=null) ? $bl->dr_no : $dr_no,
                    $bl->po_no,
                    $bl->si_or,
                    $bl->waybill_no,
                    '',
                    '',
                    ''
                ];
            }
        }
        return response()->json([
            'receivearray'=>$receivearray,
        ],200);
    }

    public function get_all_useracceptance_rejected(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){

           $query= ReceiveHead::with('receive_details','receive_items')->where(function ($query) {
                $query->where('saved','=','1')->where('draft','=','0');
            })->whereHas('receive_items', function ($query) {
                $query->where('eval_flag', '2');
            })->where(function ($query) use ($filter) {
                $query->where('receive_date', 'LIKE', '%' . $filter . '%')
                ->orWhere('mrecf_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('dr_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('po_no', 'LIKE', '%' . $filter . '%')
                ->orWhere('si_or', 'LIKE', '%' . $filter . '%');
            });
            $head= $query->orderBy('mrecf_no','DESC')->paginate(10);
            return response()->json($head);
        }else{
            $head = ReceiveHead::with('receive_details','receive_items')->where('saved','=','1')->where('draft','=','0')->whereHas('receive_items', function ($query) {
                $query->where('eval_flag', '2');
            })->orderBy('mrecf_no', 'DESC')->paginate(10);
            return response()->json($head);
        }
    }

    public function save_newaccepted(Request $request){
        if(count(json_decode($request->input("checkbox")))>0){
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!=''){
                    $update_accepted = ReceiveItems::where('id',$c)->first();
                    $update_accepted->update([
                        'eval_flag' => "1",
                        'eval_date' => $request->input("eval_date"),
                        'eval_user' => $request->input("eval_user"),
                    ]);
                }

                if($c!='' && (float) $request->input("rejected_qty"."$c")!=0){
                    $update_accepted = ReceiveItems::where('id',$c)->first();
                    $rec_qty = ReceiveItems::where('id',$c)->value('rec_quantity');
                    $update_accepted->update([
                        'eval_flag' => "2",
                        'eval_date' => $request->input("eval_date"),
                        'eval_user' => $request->input("eval_user"),
                        'eval_reason' => $request->input("rejected_remarks"."$c"),
                    ]);
                }
            }
            $receive_head_id=$request->input('receive_head_id');
            $prno = ReceiveDetails::where('receive_head_id','=',$receive_head_id)->get();
            foreach($prno AS $pr){
                $items = ReceiveItems::where('receive_details_id','=',$pr->id)->where('eval_flag','!=','0')->where('acceptance_done','0')->get();
                foreach($items AS $it){
                    $mode = ItemStatus::where('id','=',$it->item_status_id)->value('modes');
                    if($mode == 'add'){
                        if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){
                            $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
                            $update = PRItems::find($pritems_id);
                            $update->receive_qty = $update->receive_qty + $it->rec_quantity;
                            $update->balance = $update->balance + $it->rec_quantity;
                            $update->save();
                        } else { 
                            $itemdata['pr_no']=strtoupper($pr->pr_no);
                            $itemdata['item_id']=$it->item_id;
                            $itemdata['receive_qty']=$it->rec_quantity;
                            $itemdata['balance']=$it->rec_quantity;
                            PRItems::create($itemdata);
                        }
                        $exists_flag= Items::where('id','=',$it->item_id)->where('composite_flag','=','0')->where('variant_flag', '=', '0')->exists();
                        $update_balance = Items::find($it->item_id);
                        $update_balance->running_balance = $update_balance->running_balance + $it->rec_quantity;
                        if($exists_flag > 0){
                            $update_balance->variant_flag ='1';
                        }
                        $update_balance->save();
                        if($it->pr_replenish == 1){
                            $add_to_pr = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->value('borrowed_from');
                            $borrowed_qty = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->value('quantity');
                            $borrow_id = BorrowDetails::where("item_id","=", $it->item_id)->where("borrowed_by", "=", $pr->pr_no)->where("borrowed_from","=", $add_to_pr)->value('id');

                            $updatepr_rep = ReceiveItems::where("id","=", $it->id)->first();
                            $updatepr_rep->prno_replenish = $add_to_pr;
                            $updatepr_rep->save();

                            $update_borrow   = BorrowDetails::find($borrow_id);        
                            $update_borrow->balance = $update_borrow->balance - $borrowed_qty ?? 0;
                            $update_borrow->replenished_qty = $borrowed_qty;
                            $update_borrow->save();

                            $updateDeductPR = PRItems::where("item_id","=", $it->item_id)->where("pr_no", "=",$pr->pr_no)->first();
                            $updateDeductPR->replenish_deduct = $updateDeductPR->replenish_deduct + $borrowed_qty;
                            $updateDeductPR->balance = $updateDeductPR->balance - $borrowed_qty;  
                            $updateDeductPR->save();

                            $updateaddPR = PRItems::where("item_id","=", $it->item_id)->where("pr_no", "=", $add_to_pr)->first();
                            $updateaddPR->replenish_add = $updateaddPR->replenish_add  + $borrowed_qty;
                            $updateaddPR->balance = $updateaddPR->balance + $borrowed_qty;  
                            $updateaddPR->save();                       
                        }
                    } else if($mode == 'deduct'){
                        if(PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->exists()){
                            $pritems_id = PRItems::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->value('id');
                            $update2 = PRItems::find($pritems_id);
                            $update2->damage_qty = $update2->damage_qty + $it->rec_quantity;
                            $update2->save();
                        } else { 
                            $itemdata2['pr_no']=strtoupper($pr->pr_no);
                            $itemdata2['item_id']=$it->item_id;
                            $itemdata2['damage_qty']=$it->rec_quantity;
                            PRItems::create($itemdata2);
                        }
                    }
                    $total_cost = $it->unit_cost + $it->shipping_cost;
                    if(Variants::where('supplier_id','=',$it->supplier_id)->where('item_id', '=', $it->item_id)->where('brand', '=', $it->brand)->where('item_status_id', '=', $it->item_status_id)->where('expiration', '=', $it->expiry_date)->where('uom', '=', $it->uom)->where('color', '=', $it->color)->where('size', '=', $it->size)->where('average_cost','=',$total_cost)->exists()){
                        $variant_id = Variants::where('supplier_id','=',$it->supplier_id)->where('item_id', '=', $it->item_id)->where('brand', '=', $it->brand)->where('item_status_id', '=', $it->item_status_id)->where('expiration', '=', $it->expiry_date)->where('uom', '=', $it->uom)->where('color', '=', $it->color)->where('size', '=', $it->size)->where('average_cost','=',$total_cost)->value('id');
                        $update_var = Variants::find($variant_id);
                        $update_var->quantity = $update_var->quantity + $it->rec_quantity;
                        $update_var->receive_flag = '1';
                        $update_var->save();

                        $update_rec = ReceiveItems::find($it->id);
                        $update_rec->variant_id = $variant_id;
                        $update_rec->acceptance_done = '1';
                        $update_rec->save();
                        if($mode == 'add'){
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->receive_qty = $update->receive_qty + $it->rec_quantity;
                                $update->balance = $update->balance + $it->rec_quantity;
                                $update->save();
                            } else { 
                                $vardata['variant_id']=$variant_id;
                                $vardata['item_id']=$it->item_id;
                                $vardata['receive_qty']=$it->rec_quantity;
                                $vardata['balance']=$it->rec_quantity;
                                VariantsBalance::create($vardata);
                            }
                            if($it->pr_replenish != 1){
                                if(PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $it->rec_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=strtoupper($pr->pr_no);
                                    $pivdata['variant_id']=$variant_id;
                                    $pivdata['item_id']=$it->item_id;
                                    $pivdata['quantity']=$it->rec_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            }
                        } else {
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->damage_qty = $update->damage_qty + $it->rec_quantity;
                                $update->save();
                            } else { 
                                $vardata['variant_id']=$variant_id;
                                $vardata['item_id']=$it->item_id;
                                $vardata['damage_qty']=$it->rec_quantity;
                                VariantsBalance::create($vardata);
                            }
                        }
                    } else {
                        $var_data['item_id'] = $it->item_id;
                        $var_data['supplier_id'] = $it->supplier_id;
                        $var_data['supplier_name'] = $it->supplier_name ;
                        $var_data['catalog_no'] = $it->catalog_no;
                        $var_data['brand'] = $it->brand;
                        $var_data['color'] = $it->color;
                        $var_data['size'] = $it->size;
                        $var_data['barcode'] = $it->barcode;
                        $var_data['expiration'] = $it->expiry_date;
                        $var_data['serial_no'] = $it->serial_no;
                        $var_data['uom'] = $it->uom;
                        $var_data['quantity'] = $it->rec_quantity;
                        $var_data['unit_cost'] = $it->unit_cost;
                        $var_data['currency'] = $it->currency;
                        $var_data['shipping_cost'] = $it->shipping_cost;
                        $var_data['average_cost'] = $it->unit_cost + $it->shipping_cost;
                        $var_data['item_status_id'] = $it->item_status_id;
                        $var_data['receive_flag'] = 1;
                        $variant_id = Variants::insertGetId($var_data);
                        if($mode == 'add'){
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->receive_qty = $update->receive_qty + $it->rec_quantity;
                                $update->balance = $update->balance + $it->rec_quantity;
                                $update->save();
                            } else { 
                                $vardata['variant_id']=$variant_id;
                                $vardata['item_id']=$it->item_id;
                                $vardata['receive_qty']=$it->rec_quantity;
                                $vardata['balance']=$it->rec_quantity;
                                VariantsBalance::create($vardata);
                            }
                            if($it->pr_replenish != 1){
                                if(PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->exists()){
                                    $piv_id = PIVBalance::where('pr_no','=',$pr->pr_no)->where('item_id', '=', $it->item_id)->where('variant_id','=',$variant_id)->value('id');
                                    $update = PIVBalance::find($piv_id);
                                    $update->quantity = $update->quantity + $it->rec_quantity;
                                    $update->save();
                                } else {
                                    $pivdata['pr_no']=strtoupper($pr->pr_no);
                                    $pivdata['variant_id']=$variant_id;
                                    $pivdata['item_id']=$it->item_id;
                                    $pivdata['quantity']=$it->rec_quantity;
                                    PIVBalance::create($pivdata);
                                }
                            }
                        } else {
                            if(VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->exists()){
                                $varbal_id = VariantsBalance::where('variant_id','=',$variant_id)->where('item_id', '=', $it->item_id)->value('id');
                                $update = VariantsBalance::find($varbal_id);
                                $update->damage_qty = $update->damage_qty + $it->rec_quantity;
                                $update->save();

                            } else { 
                                $vardata1['variant_id']=$variant_id;
                                $vardata1['item_id']=$it->item_id;
                                $vardata1['damage_qty']=$it->rec_quantity;
                                VariantsBalance::create($vardata1);
                            }
                        }
                        $update_rec = ReceiveItems::find($it->id);
                        $update_rec->variant_id = $variant_id;
                        $update_rec->acceptance_done = '1';
                        $update_rec->save();
                    }
                }
            }
            $get_rep = ReceiveDetails::with('receive_items');
            $get_rep->whereHas('receive_items', function ($get_rep) use($receive_head_id) {
                $get_rep->where('receive_head_id','=',$receive_head_id)->where('pr_replenish','=','1');
            });
            $rep_pr = $get_rep->get();
            foreach($rep_pr AS $rpr){
                ///////////////////////////////// CREATE REQUEST ///////////////////////////////
                $user_id_req=ReceiveHead::where('id',$receive_head_id)->value('user_id');
                $curr_year = date('Y');
                $curr_mo = date('m');
                if(Mreqf::where('year', '=', $curr_year)->exists()) {
                    $mreqf = Mreqf::where('year', '=', $curr_year)->max('series') + 1;
                    $max_value = str_pad($mreqf,4,"0",STR_PAD_LEFT);;
                } else {
                    $max_value = '0001';
                }
                Mreqf::create([
                    'year' => date("Y"),
                    'series'=>$max_value
                ]);
                $mreqf_no = 'MREQF-'.$curr_year.'-'.$curr_mo.'-'.$max_value;
                foreach($rpr->receive_items AS $rep_ri2){
                    $repl_pr[] = $rep_ri2->prno_replenish;
                } 
                $all_rep_pr = array_unique($repl_pr);
                $remarks = "Replenishment for PR# ";
                foreach($all_rep_pr AS $all_rep){
                    $remarks .= $all_rep .", ";
                }
                $remarks = substr($remarks, 0, -2);
                $reqhead['request_date'] = date('Y-m-d');
                $reqhead['request_time'] = date('H:i:s');
                $reqhead['mreqf_no'] = $mreqf_no;
                $reqhead['request_type']='With PR';
                $reqhead['pr_no']=strtoupper($rpr->pr_no);
                $reqhead['department_id']=$rpr->department_id;
                $reqhead['department_name']=$rpr->department_name;
                $reqhead['purpose_id']=$rpr->purpose_id;
                $reqhead['purpose_name']=$rpr->purpose_name;
                $reqhead['enduse_id']=$rpr->enduse_id;
                $reqhead['enduse_name']=$rpr->enduse_name;
                $reqhead['remarks']=$remarks;
                $reqhead['user_id']=$user_id_req;
                $reqhead['close']='1';
                $reqhead['saved']='1';
                $reqhead['replenish_id']=$receive_head_id;
                $request_head_id = RequestHead::insertGetId($reqhead);
                ///////////////////////////////// CREATE ISSUANCE ///////////////////////////////
                if(MIF::where('year', '=', $curr_year)->exists()) {
                    $mif = MIF::where('year', '=', $curr_year)->max('series') + 1;
                    $max_value_iss = str_pad($mif,4,"0",STR_PAD_LEFT);;
                } else {
                    $max_value_iss = '0001';
                }
                MIF::create([
                    'year' => date("Y"),
                    'series'=>$max_value_iss
                ]);
                $mif_no = 'MIF-'.$curr_year.'-'.$curr_mo.'-'.$max_value_iss;
                $prepared_by=User::where('id',$user_id_req)->value('name');
                $prepared_des=User::where('id',$user_id_req)->value('position');
                $isshead['request_head_id']=$request_head_id;
                $isshead['mreqf_no']=$mreqf_no;
                $isshead['mif_no']=$mif_no;
                $isshead['pr_no']=strtoupper($rpr->pr_no);
                $isshead['issuance_date']=date('Y-m-d');
                $isshead['issuance_time']=date('H:i:s');
                $isshead['department_id']=$rpr->department_id;
                $isshead['department_name']=$rpr->department_name;
                $isshead['purpose_id']=$rpr->purpose_id;
                $isshead['purpose_name']=$rpr->purpose_name;
                $isshead['enduse_id']=$rpr->enduse_id;
                $isshead['enduse_name']=$rpr->enduse_name;
                $isshead['remarks']=$remarks;
                $isshead['user_id']=$user_id_req;
                $isshead['prepared_by']=$user_id_req;
                $isshead['prepared_by_name']=$prepared_by;
                $isshead['prepared_by_pos']=$prepared_des;
                $issuance_head_id = IssuanceHead::insertGetId($isshead);
                foreach($rpr->receive_items AS $rep_ri){
                    $borrow_qty = BorrowDetails::where('borrowed_by','=',$rpr->pr_no)->where('item_id','=',$rep_ri->item_id)->value('quantity');
                    if($borrow_qty == $rep_ri->rec_quantity){
                        $qty = $borrow_qty;
                        // $qty = $rep_ri->borrow_qty;
                        $piv_qty = 0;
                    } else if($borrow_qty < $rep_ri->rec_quantity){
                        $qty = $borrow_qty;
                        $piv_qty = $rep_ri->rec_quantity - $borrow_qty;
                    }
                    $reqdet['request_head_id']=$request_head_id;
                    $reqdet['item_id']=$rep_ri->item_id;
                    $reqdet['item_description']=$rep_ri->item_description;
                    $reqdet['variant_id']=$rep_ri->variant_id;
                    $reqdet['unit_cost']=$rep_ri->unit_cost;
                    $reqdet['shipping_cost']=$rep_ri->shipping_cost;
                    $reqdet['quantity']=$qty;
                    $reqdet['issued_qty']=$qty;
                    $request_item_id = RequestItems::insertGetId($reqdet);
                    $instock = Items::where('id','=',$rep_ri->item_id)->value('running_balance');
                    $issitems['issuance_head_id']=$issuance_head_id;
                    $issitems['item_id']=$rep_ri->item_id;
                    $issitems['item_description']=$rep_ri->item_description;
                    $issitems['variant_id']=$rep_ri->variant_id;
                    $issitems['request_items_id']=$request_item_id;
                    $issitems['inventory_balance']=$instock;
                    $issitems['unit_cost']=$rep_ri->unit_cost;
                    $issitems['shipping_cost']=$rep_ri->shipping_cost;
                    $issitems['request_qty']=$qty;
                    $issitems['issued_qty']=$qty;
                    IssuanceItems::create($issitems);
                    if(PIVBalance::where('pr_no','=',$rpr->pr_no)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->exists()){
                        $piv_id = PIVBalance::where('pr_no','=',$rpr->pr_no)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->value('id');
                        $update = PIVBalance::find($piv_id);
                        $update->quantity = $update->quantity + $piv_qty;
                        $update->save();
                    } else {
                        $pivdata['pr_no']=strtoupper($rpr->pr_no);
                        $pivdata['variant_id']=$rep_ri->variant_id;
                        $pivdata['item_id']=$rep_ri->item_id;
                        $pivdata['quantity']=$piv_qty;
                        PIVBalance::create($pivdata);
                    }
                    if(PIVBalance::where('pr_no','=',$rep_ri->prno_replenish)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->exists()){
                        $piv_id = PIVBalance::where('pr_no','=',$rep_ri->prno_replenish)->where('item_id', '=', $rep_ri->item_id)->where('variant_id','=',$rep_ri->variant_id)->value('id');
                        $update = PIVBalance::find($piv_id);
                        $update->quantity = $update->quantity + $borrow_qty;
                        $update->save();
                    } else {
                        $pivdata['pr_no']=strtoupper($rep_ri->prno_replenish);
                        $pivdata['variant_id']=$rep_ri->variant_id;
                        $pivdata['item_id']=$rep_ri->item_id;
                        $pivdata['quantity']=$borrow_qty;
                        PIVBalance::create($pivdata);
                    }
                }        
            }
            $x=0;
            foreach(json_decode($request->input("checkbox")) AS $c){
                if($c!='' && (float) $request->input("rejected_qty"."$c")!=0){
                    $item_id = ReceiveItems::where('id',$c)->value('item_id');
                    $variant_id = ReceiveItems::where('id',$c)->value('variant_id');
                    $receive_details_id = ReceiveItems::where('id',$c)->value('receive_details_id');
                    $pr_no = ReceiveDetails::where('id',$receive_details_id)->value('pr_no');
                    $update_accepted1 = ReceiveItems::where('id',$c)->first();
                    $rec_qty1 = ReceiveItems::where('id',$c)->value('rec_quantity');
                    $rejected_qty = ReceiveItems::where('id',$c)->value('rejected_qty');
                    $update_accepted1->update([
                        'rejected_qty' => (float) $rejected_qty + (float) $request->input("rejected_qty"."$c"),
                        'rec_quantity' => (float) $rec_qty1 - (float) $request->input("rejected_qty"."$c"),
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
                        'rejected_qty' => (float) $rejected_qty + (float) $request->input("rejected_qty"."$c"),
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
