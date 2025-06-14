<?php

namespace App\Http\Controllers;

use App\Models\Items;
use App\Models\ItemStatus;
use App\Models\ItemSubCategory;
use App\Models\ItemCategory;
use App\Models\CompositeItems;
use App\Models\Variants;
use App\Models\NoVariants;
use App\Models\supplier;
use App\Models\PRItems;
use App\Models\VariantsBalance;
use App\Models\PNSeries;
use App\Models\RequestItems;
use App\Models\BorrowDetails;
use App\Models\IssuanceItems;
use App\Models\RestockDetails;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\PIVBalance;
use Illuminate\Http\Request;
use App\Http\Requests\ItemsRequest;
use Config;

class ItemController extends Controller
{
    public function get_all_items(Request $request){
        $items_all = Items::with('composite_items')->orderBy('pn_no','ASC')->get();
        $x=0;
        $itemarray=array();
        foreach($items_all AS $ia){
            $qty=CompositeItems::select('item_id','quantity')->where('item_id',$ia->id)->value('quantity');
            $itemid=CompositeItems::select('item_id','quantity')->where('item_id',$ia->id)->value('item_id');
            $itemarray[]=[
                'id'=>$ia->id,
                'draft'=>$ia->draft,
                'qty'=>$qty,
                'item_id'=>$itemid,
                'item_description'=>$ia->item_description,
                $ia->pn_no,
                $ia->item_description,
                $ia->location_description,
                $ia->rack_description,
                $ia->running_balance,
                $ia->moq,
                ''
            ];
            $x++;
        }
        return response()->json([
            'itemsarray'=>$itemarray,
        ],200);
    }

    // public function search_items(Request $request){
    //     $filter=$request->get('filter');
    //     if($filter!=null){
    //         $items=Items::select('items.id','items.pn_no','items.item_description','items.uom','items.moq','items.location_description','items.rack_description','items.running_balance','items.moq')->with('category')->with('location')->with('rack')->where('item_description','LIKE',"%$filter%")->orWhere('pn_no','LIKE',"%$filter%")->orWhere('location_description','LIKE',"%$filter%")->orWhere('rack_description','LIKE',"%$filter%")->orWhere('moq','LIKE',"%$filter%")->orderBy('item_description','ASC')->paginate(10);
    //         return response()->json($items);
    //     }else{
    //         $items = Items::with('category')->with('location')->with('rack')->orderBy('item_description','ASC')->paginate(10);
    //         return response()->json($items);
    //     }
    // }

    public function choose_subcat($subcat){
        $category_id = ItemSubCategory::where('id',$subcat)->pluck('item_category_id');
        $subcat_prefix = ItemSubCategory::where('id',$subcat)->value('subcat_prefix');
        $category_name = ItemCategory::where('id',$category_id)->pluck('category_name');
        //$counter=Items::where('pn_no','LIKE',"%$subcat_prefix%")->get();
        $counter=PNSeries::where('subcat_prefix','LIKE',"%$subcat_prefix%")->get();
        $count=$counter->count();
        if($count==0){
            $pn_no= $subcat_prefix."_1001";
        }else{
            $series=PNSeries::where('subcat_prefix',$subcat_prefix)->max('series');
            //$maxsubcat=Items::where('item_sub_category_id',$subcat)->max('pn_no');
            // $checker = strpos($maxsubcat, '_');
            // if (false !== $checker) {
            // $pn_noexp=explode('_',$maxsubcat);
            // $series = $pn_noexp[1];
            $next=$series+1;
            $pn_no = $subcat_prefix."_".$next;
        }
        return response()->json([
            'category_id'=>$category_id,
            'category_name'=>$category_name,
            'pn_no'=>$pn_no,
        ],200);
    }

    public function create_items(Request $request){
        $formData=[
            'item_category_id'=>0,
            'item_sub_category_id'=>0,
            'item_description'=>'',
            'location_id'=>0,
            'location_description'=>'',
            'warehouse_id'=>0,
            'warehouse_description'=>'',
            'group_id'=>0,
            'group_description'=>'',
            'rack_id'=>0,
            'rack_description'=>'',
            'uom'=>'',
            'moq'=>'',
            'image1'=>'',
            'image2'=>'',
            'image3'=>'',
            'running_balance'=>0,
            'composite_flag'=>0,
            'variant_flag'=>0,
            'pn_no'=>'',
            'begbal'=>'',
        ];
        return response()->json($formData);
    }

    public function add_items(ItemsRequest $request){
        if($request->error_items==0){
            // $validated=$request->validated();
            // $items=Items::create($validated);
            $validated=$request->validated();
            $validated['location_id']= ($request->location_id) ? $request->location_id : 0;
            $validated['location_description']=$request->location_description ?? '';
            $validated['warehouse_id']=($request->warehouse_id) ? $request->warehouse_id : 0;
            $validated['warehouse_description']=$request->warehouse_description ?? '';
            $validated['rack_id']=($request->rack_id) ? $request->rack_id : 0;
            $validated['rack_description']=$request->rack_description ?? '';
            $validated['group_id']=($request->group_id) ? $request->group_id : 0;
            $validated['group_description']=$request->group_description ?? '';
            $validated['begbal']=($request->begbal) ? $request->begbal : 0;
            $validated['moq']=($request->moq) ? $request->moq : 0;
            $validated['copy_qty']=($request->copies) ? $request->copies : 0;
            $validated['composite_cost']=($request->composite_cost) ? $request->composite_cost : 0;
            $checker = strpos($request->pn_no, '_');
            if(false !== $checker){
                $pn_no=explode('_',$request->pn_no);
                $subcat_prefix=$pn_no[0];
                $series=$pn_no[1];
                $data=[
                    'subcat_prefix'=>$subcat_prefix,
                    'series'=>$series
                ];
                $pnseries=PNSeries::create($data);
            }
            if($request->file('image1')){
                $imagename=$request->file('image1')->getClientOriginalName();
                $request->file('image1')->storeAs('images',$imagename, 'public');
                $validated['image1']=$imagename;
            }
            
            if($request->file('image2')){
                $imagename2=$request->file('image2')->getClientOriginalName();
                $request->file('image2')->storeAs('images',$imagename2, 'public');
                $validated['image2']=$imagename2;
            }

            if($request->file('image3')){
                $imagename3=$request->file('image3')->getClientOriginalName();
                $request->file('image3')->storeAs('images',$imagename3, 'public');
                $validated['image3']=$imagename3;
            }
            if($request->begbal!='' || $request->begbal!='undefined'){
                $validated['running_balance']= ($request->begbal) ? $request->begbal : 0;
            }

            //if(count(json_decode($request->input("composite")))==0 && count(json_decode($request->input("variant")))==0 && count(json_decode($request->input("novariant")))==0){
            if(count(json_decode($request->input("composite")))==0 && count(json_decode($request->input("variant")))==0){
                $items=Items::create($validated);
                //if($request->begbal!=0){
                $pritemsdata['begbal']=($request->begbal) ? (double) $request->begbal : 0;
                $pritemsdata['balance']=($request->begbal) ? (double) $request->begbal : 0;
                $pritemsdata['item_id']=$items->id;
                $pritemsdata['pr_no']="WH STOCKS";
                $insertpr=PRItems::create($pritemsdata);
                if($insertpr){
                    $variantbaldata['item_id']=$items->id;
                    $variantbaldata['whstocks_qty']=$request->begbal ?? 0;
                    $variantbaldata['balance']=$request->begbal ?? 0;
                    VariantsBalance::create($variantbaldata);

                    $pivdata['pr_no']='WH STOCKS';
                    $pivdata['item_id']=$items->id;
                    $pivdata['variant_id']='0';
                    $pivdata['quantity']=$request->begbal ?? 0;
                    PIVBalance::create($pivdata);
                }
                //}
            }else{
                if(count(json_decode($request->input("composite")))>0){
                    $validated['composite_flag']=1;
                    $items=Items::create($validated);
                    // if (PRItems::where('item_id', $items->id)->exists()) {
                    //     $updatec=PRItems::where('item_id',$items->id)->first();
                    //     $begbal=PRItems::where('item_id',$items->id)->value('begbal');
                    //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal + $begbal : 0;
                    //     $updatec->update($pritemsdata);
                    // }else{
                    //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal : 0;
                    //     $pritemsdata['item_id']=$items->id;
                    //     PRItems::create($pritemsdata);
                    // }
                    if($items){
                        //if($request->begbal!=0){
                            $pritemsdata['begbal']=((double) $request->begbal) ? (double) $request->begbal : 0;
                            $pritemsdata['balance']=((double) $request->begbal) ? (double) $request->begbal : 0;
                            $pritemsdata['item_id']=$items->id;
                            $pritemsdata['pr_no']="WH STOCKS";
                            $insertpr=PRItems::create($pritemsdata);
                            if($insertpr){
                                $variantbaldata['item_id']=(int) $items->id;
                                $variantbaldata['whstocks_qty']=(double) $request->begbal;
                                $variantbaldata['balance']=(double) $request->begbal;
                                VariantsBalance::create($variantbaldata);

                                $pivdata['pr_no']='WH STOCKS';
                                $pivdata['item_id']=$items->id;
                                $pivdata['variant_id']='0';
                                $pivdata['quantity']=$request->begbal ?? 0;
                                PIVBalance::create($pivdata);
                            }
                        //}
                        $composite=$request->input("composite");  
                        $items_duplicate=array();
                        if($request->copies!=0){
                          
                            for($x=0;$x<$request->copies;$x++){
                                $inc=$x+1;
                                $validated_duplicate=$request->validated();
                                $validated_duplicate['composite_flag']=1;
                                $validated_duplicate['item_description']= ($request->item_description) ? $request->item_description."-Copy ".$inc : 0;
                                $validated_duplicate['location_id']= ($request->location_id) ? $request->location_id : 0;
                                $validated_duplicate['location_description']=$request->location_description ?? '';
                                $validated_duplicate['warehouse_id']=($request->warehouse_id) ? $request->warehouse_id : 0;
                                $validated_duplicate['warehouse_description']=$request->warehouse_description ?? '';
                                $validated_duplicate['rack_id']=($request->rack_id) ? $request->rack_id : 0;
                                $validated_duplicate['rack_description']=$request->rack_description ?? '';
                                $validated_duplicate['group_id']=($request->group_id) ? $request->group_id : 0;
                                $validated_duplicate['group_description']=$request->group_description ?? '';
                                $validated_duplicate['begbal']=($request->begbal) ? $request->begbal : 0;
                                $validated_duplicate['moq']=($request->moq) ? $request->moq : 0;
                                $validated_duplicate['copy_qty']=($request->copies) ? $request->copies : 0;
                                $checker = strpos($request->pn_no, '_');
                                if(false !== $checker){
                                    $pn_no=explode('_',$request->pn_no);
                                    $subcat_prefix=$pn_no[0];
                                    //$series=$pn_no[1];
                                    $series=PNSeries::where('subcat_prefix',$subcat_prefix)->max('series');
                                    $next=$series+1;
                                    $data=[
                                        'subcat_prefix'=>$subcat_prefix,
                                        'series'=>$next
                                    ];
                                    $pnseries=PNSeries::create($data);
                                }
                                $validated_duplicate['pn_no']=($request->pn_no) ? $subcat_prefix."_".$next : '';
                                if($request->file('image1')){
                                    $imagename=$request->file('image1')->getClientOriginalName();
                                    $request->file('image1')->storeAs('images',$imagename, 'public');
                                    $validated_duplicate['image1']=$imagename;
                                }
                                
                                if($request->file('image2')){
                                    $imagename2=$request->file('image2')->getClientOriginalName();
                                    $request->file('image2')->storeAs('images',$imagename2, 'public');
                                    $validated_duplicate['image2']=$imagename2;
                                }

                                if($request->file('image3')){
                                    $imagename3=$request->file('image3')->getClientOriginalName();
                                    $request->file('image3')->storeAs('images',$imagename3, 'public');
                                    $validated_duplicate['image3']=$imagename3;
                                }
                                if($request->begbal!='' || $request->begbal!='undefined'){
                                    $validated_duplicate['running_balance']= ($request->begbal) ? $request->begbal : 0;
                                }
                                $items_duplicate[]=Items::create($validated_duplicate);
                                $pritemsdata['begbal']=((double) $request->begbal) ? (double) $request->begbal : 0;
                                $pritemsdata['balance']=((double) $request->begbal) ? (double) $request->begbal : 0;
                                $pritemsdata['item_id']=$items_duplicate[$x]->id;
                                $pritemsdata['pr_no']="WH STOCKS";
                                $insertpr=PRItems::create($pritemsdata);
                                if($insertpr){
                                    $variantbaldata['item_id']=(int) $items_duplicate[$x]->id;
                                    $variantbaldata['whstocks_qty']=(double) $request->begbal;
                                    $variantbaldata['balance']=(double) $request->begbal;
                                    VariantsBalance::create($variantbaldata);

                                    $pivdata['pr_no']='WH STOCKS';
                                    $pivdata['item_id']=(int) $items_duplicate[$x]->id;
                                    $pivdata['variant_id']='0';
                                    $pivdata['quantity']=$request->begbal ?? 0;
                                    PIVBalance::create($pivdata);
                                }
                            }
                        }
                        foreach(json_decode($composite) AS $c){
                            $variant_id=($c->variant_id=='') ? 0 : $c->variant_id;
                            if($request->begbal==0){
                                $multiply_qty= (double) $c->quantity;
                            }else{
                                $multiply_qty= (double) $request->begbal * (double) $c->quantity;
                            }
                            if($request->copies==0){
                                $compositedata['item_id']=$items->id;
                                $compositedata['variant_id']= $variant_id;
                                $compositedata['compose_item_id']= (int) $c->compose_item_id;
                                $compositedata['quantity']= ($c->quantity) ? (double) $c->quantity : 0;
                                // $compositedata['quantity']= ($c->quantity) ? (double) $multiply_qty : 0;
                                $composites=CompositeItems::create($compositedata);
                                if($composites){
                                    $running_balance=Items::select('running_balance')->where('id',$c->compose_item_id)->value('running_balance');
                                    $whstocks=VariantsBalance::select('whstocks_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('whstocks_qty');
                                    $compositeqty=VariantsBalance::select('composite_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('composite_qty');
                                    $balance=VariantsBalance::select('balance')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('balance');
                                    $vbins=VariantsBalance::where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->update([
                                        'variant_id'=>(int) $variant_id,
                                        //'whstocks_qty'=>$whstocks - $c->quantity,
                                        // 'whstocks_qty'=>$whstocks - $multiply_qty,
                                        'composite_qty'=>(double) $multiply_qty + $compositeqty,
                                        'balance'=>$balance - (double) $multiply_qty,
                                    ]);
                                    if($vbins){
                                        $updaterunbal=Items::where("id",$c->compose_item_id)->first();
                                        $updaterunbal->update([
                                            'running_balance'=> (double) $updaterunbal->running_balance - (double) $multiply_qty,
                                        ]);
                                        if($updaterunbal){
                                            if($variant_id!=0){
                                                $updatevariant=Variants::where('id',$variant_id)->first();
                                                $updatevariant->update([
                                                    'composite_flag'=>1,
                                                    'quantity'=> (double) $updatevariant->quantity - (double)$multiply_qty
                                                ]);
                                            }
                                            //if($updatevariant){
                                            // $pr_receive=PRItems::where("item_id",$c->compose_item_id)->where()->value('pr_no');
                                            if($c->pr_no!=''){
                                                $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                                // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                                $updatebal->update([
                                                    'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                                    'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                                ]);
                                                $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no',$c->pr_no)->first();
                                                $updatepiv->update([
                                                    'quantity'=> (double) $updatepiv->quantity - (double)$multiply_qty,
                                                ]);
                                            }else{
                                                $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                                $updatebal->update([
                                                    'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                                    'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                                ]);
                                                $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no','WH STOCKS')->first();
                                                $updatepiv->update([
                                                    'quantity'=> (double) $updatepiv->quantity - (double)$multiply_qty,
                                                ]);
                                            }
                                            //}
                                        }
                                    }
                                }
                            }else{
                                $compositedata['item_id']=$items->id;
                                $compositedata['variant_id']= (int) $variant_id;
                                $compositedata['compose_item_id']= (int) $c->compose_item_id;
                                $compositedata['quantity']= ($c->quantity) ? (double) $c->quantity : 0;
                                $composites=CompositeItems::create($compositedata);
                                if($composites){
                                    $running_balance=Items::select('running_balance')->where('id',$c->compose_item_id)->value('running_balance');
                                    $whstocks=VariantsBalance::select('whstocks_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('whstocks_qty');
                                    $compositeqty=VariantsBalance::select('composite_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('composite_qty');
                                    $balance=VariantsBalance::select('balance')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('balance');
                                    $vbins=VariantsBalance::where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->update([
                                        'variant_id'=>$variant_id,
                                        //'whstocks_qty'=>$whstocks - $c->quantity,
                                        // 'whstocks_qty'=>$whstocks - $multiply_qty,
                                        'composite_qty'=>(double) $multiply_qty + $compositeqty,
                                        'balance'=>$balance - (double) $multiply_qty,
                                    ]);
                                    if($vbins){
                                        $updaterunbal=Items::where("id",$c->compose_item_id)->first();
                                        $updaterunbal->update([
                                            'running_balance'=> (double) $updaterunbal->running_balance - (double) $multiply_qty,
                                        ]);
                                        if($updaterunbal){
                                            if($variant_id!=0){
                                                $updatevariant=Variants::where('id',$variant_id)->first();
                                                $updatevariant->update([
                                                    'composite_flag'=>1,
                                                    'quantity'=> (double) $updatevariant->quantity - (double)$multiply_qty
                                                ]);
                                            }
                                            //if($updatevariant){
                                            // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                            // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                            // $updatebal->update([
                                            //     'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                            //     'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                            // ]);
                                            if($c->pr_no!=''){
                                                $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                                // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                                $updatebal->update([
                                                    'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                                    'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                                ]);

                                                $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no',$c->pr_no)->first();
                                                $updatepiv->update([
                                                    'quantity'=> (double) $updatepiv->quantity - (double)$multiply_qty,
                                                ]);
                                            }else{
                                                $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                                $updatebal->update([
                                                    'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                                    'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                                ]);

                                                $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no','WH STOCKS')->first();
                                                $updatepiv->update([
                                                    'quantity'=> (double) $updatepiv->quantity - (double)$multiply_qty,
                                                ]);
                                            }
                                            //}
                                        }
                                    }
                                }
                                for($x=0;$x<$request->copies;$x++){
                                    if($request->begbal==0){
                                        $multiply_qty_duplicate= (double) $c->quantity;
                                    }else{
                                        $multiply_qty_duplicate= (double) $request->begbal * (double) $c->quantity;
                                    }
                                    $compositedata['item_id']=$items_duplicate[$x]->id;
                                    $compositedata['variant_id']= (int) $variant_id;
                                    $compositedata['compose_item_id']= (int) $c->compose_item_id;
                                    $compositedata['quantity']= ($c->quantity) ? (double) $c->quantity : 0;
                                    $composites=CompositeItems::create($compositedata);
                                    if($composites){
                                        $running_balance=Items::select('running_balance')->where('id',$c->compose_item_id)->value('running_balance');
                                        $whstocks=VariantsBalance::select('whstocks_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('whstocks_qty');
                                        $compositeqty=VariantsBalance::select('composite_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('composite_qty');
                                        $balance=VariantsBalance::select('balance')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('balance');
                                        $vbins=VariantsBalance::where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->update([
                                            'variant_id'=>(int) $variant_id,
                                            //'whstocks_qty'=>$whstocks - $c->quantity,
                                            // 'whstocks_qty'=>$whstocks - $multiply_qty,
                                            'composite_qty'=>(double) $multiply_qty_duplicate + $compositeqty,
                                            'balance'=>$balance - (double) $multiply_qty_duplicate,
                                        ]);
                                        if($vbins){
                                            $updaterunbal=Items::where("id",$c->compose_item_id)->first();
                                            $updaterunbal->update([
                                                'running_balance'=> (double) $updaterunbal->running_balance - (double) $multiply_qty_duplicate,
                                            ]);
                                            if($updaterunbal){
                                                if($variant_id!=0){
                                                    $updatevariant=Variants::where('id',$variant_id)->first();
                                                    $updatevariant->update([
                                                        'composite_flag'=>1,
                                                        'quantity'=> (double) $updatevariant->quantity - (double)$multiply_qty_duplicate
                                                    ]);
                                                }
                                                //if($updatevariant){
                                                // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                                // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                                // $updatebal->update([
                                                //     'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty_duplicate,
                                                //     'balance'=> (double) $updatebal->balance - (double) $multiply_qty_duplicate,
                                                // ]);
                                                if($c->pr_no!=''){
                                                    $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                                    // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                                    $updatebal->update([
                                                        'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty_duplicate,
                                                        'balance'=> (double) $updatebal->balance - (double) $multiply_qty_duplicate,
                                                    ]);

                                                    $updatepiv1=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no',$c->pr_no)->first();
                                                    $updatepiv1->update([
                                                        'quantity'=> (double) $updatepiv1->quantity - (double)$multiply_qty_duplicate
                                                    ]);
                                                }else{
                                                    $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                                    $updatebal->update([
                                                        'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty_duplicate,
                                                        'balance'=> (double) $updatebal->balance - (double) $multiply_qty_duplicate,
                                                    ]);

                                                    $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no','WH STOCKS')->first();
                                                    $updatepiv2->update([
                                                        'quantity'=> (double) $updatepiv2->quantity - (double)$multiply_qty_duplicate
                                                    ]);
                                                }
                                                //}
                                                
                                            }
                                        }
                                    }
                                }
                                // return response()->json($items_duplicate);
                            }
                        }
                        return response()->json($items_duplicate);
                    }
                }

                if(count(json_decode($request->input("variant")))>0){
                    $validated['variant_flag']=1;
                    $items=Items::create($validated);
                    // if (PRItems::where('item_id', $items->id)->exists()) {
                    //     $updatev=PRItems::where('item_id',$items->id)->first();
                    //     $begbal=PRItems::where('item_id',$items->id)->value('begbal');
                    //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal + $begbal : 0;
                    //     $updatev->update($pritemsdata);
                    // }else{
                    //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal : 0;
                    //     $pritemsdata['item_id']=$items->id;
                    //     PRItems::create($pritemsdata);
                    // }
                    if($request->begbal!=0){
                        // $pritemsdata['begbal']=($request->begbal) ? (double) $request->begbal : 0;
                        // $pritemsdata['balance']=($request->begbal) ? (double) $request->begbal : 0;
                        // $pritemsdata['item_id']=(int) $items->id;
                        // $pritemsdata['pr_no']="WH STOCKS";
                        // $insertpr=PRItems::create($pritemsdata);
                        // if($insertpr){
                            $variantbaldata['item_id']=(int) $items->id;
                            $variantbaldata['whstocks_qty']=(double) $request->begbal;
                            $variantbaldata['balance']=(double) $request->begbal;
                            VariantsBalance::create($variantbaldata);

                            $pivdata['pr_no']='WH STOCKS';
                            $pivdata['item_id']=(int) $items->id;
                            $pivdata['variant_id']='0';
                            $pivdata['quantity']=$request->begbal ?? 0;
                            PIVBalance::create($pivdata);
                        // }
                    }
                    $variant=$request->input("variant");
                    $running_balance=0;
                    $item_status_checker=array();
                    foreach(json_decode($variant) AS $v){
                        $supplier_name=supplier::where('id',$v->supplier_id)->value('supplier_name');
                        $variantdata['item_id']=$items->id;
                        $variantdata['supplier_id']=$v->supplier_id;
                        $variantdata['supplier_name']=$supplier_name;
                        $variantdata['catalog_no']=$v->catalog_no;
                        $variantdata['brand']=$v->brand;
                        $variantdata['serial_no']=$v->serial_no;
                        $variantdata['barcode']=$v->barcode;
                        $variantdata['unit_cost']=$v->unit_cost ?? 0;
                        $variantdata['currency']=$v->currency;
                        $variantdata['quantity']= ($v->quantity) ? $v->quantity : 0;
                        $variantdata['average_cost']= $v->unit_cost ?? 0;
                        $variantdata['expiration']=$v->expiration;
                        $variantdata['uom']=$v->uom;
                        $variantdata['color']=$v->color;
                        $variantdata['size']=$v->size;
                        // $variantdata['unit_cost']= ($v->unit_cost) ? $v->unit_cost : 0;
                        // $variantdata['selling_price']= ($v->selling_price) ? $v->selling_price : 0;
                        $variantdata['item_status_id']= ($v->item_status_id) ? $v->item_status_id : 0;
                        $variant=Variants::create($variantdata);
                        $item_status=ItemStatus::where('id',$v->item_status_id)->value('modes');
                        if($item_status=='add'){
                            $running_balance+=$v->quantity;
                            $update_runbal = Items::find($items->id);
                            $update_runbal->update([
                                'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                            ]);
                            $variantbaldata['item_id']=(int)$items->id;
                            $variantbaldata['variant_id']=(int)$variant->id;
                            $variantbaldata['whstocks_qty']=(double)$v->quantity;
                            $variantbaldata['balance']=(double)$v->quantity;
                            VariantsBalance::create($variantbaldata);
                            $pivdata1['pr_no']='WH STOCKS';
                            $pivdata1['item_id']=(int) $items->id;
                            $pivdata1['variant_id']=$variant->id;
                            $pivdata1['quantity']=$v->quantity;
                            PIVBalance::create($pivdata1);
                        }
                        $item_status_checker[]=ItemStatus::where('id',$v->item_status_id)->value('modes');
                    }
                    if(in_array('add',$item_status_checker)){
                        $pritemsdata['begbal']=$running_balance + (double) $request->begbal;
                        $pritemsdata['balance']=$running_balance + (double) $request->begbal;
                        $pritemsdata['item_id']=$items->id;
                        $pritemsdata['pr_no']="WH STOCKS";
                        $insertpr=PRItems::create($pritemsdata);
                        // if($insertpr){
                        //     $variantbaldata['item_id']=$items->id;
                        //     $variantbaldata['whstocks_qty']=$running_balance;
                        //     $variantbaldata['balance']=$running_balance;
                        //     VariantsBalance::create($variantbaldata);
                        // }
                    }
                }

                // if(count(json_decode($request->input("novariant")))>0 && count(json_decode($request->input("variant")))==0 && count(json_decode($request->input("composite")))==0){
                //     $validated['novariant_flag']=1;
                //     $items=Items::create($validated);
                //     // if (PRItems::where('item_id', $items->id)->exists()) {
                //     //     $updatenv=PRItems::where('item_id',$items->id)->first();
                //     //     $begbal=PRItems::where('item_id',$items->id)->value('begbal');
                //     //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal + $begbal : 0;
                //     //     $updatenv->update($pritemsdata);
                //     // }else{
                //     //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal : 0;
                //     //     $pritemsdata['item_id']=$items->id;
                //     //     PRItems::create($pritemsdata);
                //     // }
                //     //if($request->begbal!=0){
                //         $pritemsdata['begbal']=($request->begbal) ? (double) $request->begbal : 0;
                //         $pritemsdata['balance']=($request->begbal) ? (double) $request->begbal : 0;
                //         $pritemsdata['item_id']=(int) $items->id;
                //         $pritemsdata['pr_no']="WH STOCKS";
                //         $insertpr=PRItems::create($pritemsdata);
                //         if($insertpr){
                //             $variantbaldata['item_id']=(int) $items->id;
                //             $variantbaldata['whstocks_qty']=(double) $request->begbal;
                //             $variantbaldata['balance']=(double) $request->begbal;
                //             VariantsBalance::create($variantbaldata);
                //         }
                //     //}
                //     $novariant=$request->input("novariant");
                //     foreach(json_decode($novariant) AS $n){
                //         $novariantdata['item_id']=$items->id;
                //         $novariantdata['serial_no']=$n->serial_no;
                //         $novariantdata['barcode']=$n->bar_code;
                //         $novariantdata['expiration']=$n->expiration;
                //         $novariantdata['unit_cost']= ($n->unit_cost) ? $n->unit_cost : 0;
                //         $novariantdata['selling_price']= ($n->selling_price) ? $n->selling_price : 0;
                //         $novariantdata['item_status_id']= ($n->item_status) ? $n->item_status : 0;
                //         $novar=NoVariants::create($novariantdata);
                //         if($novar){
                //             $novariantbaldata['item_id']=$items->id;
                //             VariantsBalance::create($novariantbaldata);
                //         }
                //     }
                // }
            }
        }
    }

    public function add_items_draft(Request $request){
        if($request->error_items==0){
            // $validated=$request->validated();
            // $items=Items::create($validated);
            // $validated=$request->validated();
            $validated['item_sub_category_id']= ($request->item_sub_category_id) ? (int) $request->item_sub_category_id : 0;
            $validated['item_category_id']= ($request->item_category_id) ? (int) $request->item_category_id : 0;
            $validated['item_description']= ($request->item_description) ? $request->item_description : NULL;
            $validated['moq']= ($request->moq) ? $request->moq : 0;
            $validated['pn_no']= ($request->pn_no) ? $request->pn_no : NULL;
            $validated['location_id']= ($request->location_id) ? (int) $request->location_id : 0;
            $validated['location_description']=$request->location_description ?? '';
            $validated['warehouse_id']=($request->warehouse_id) ? (int) $request->warehouse_id : 0;
            $validated['warehouse_description']=$request->warehouse_description ?? '';
            $validated['rack_id']=($request->rack_id) ? (int) $request->rack_id : 0;
            $validated['rack_description']=$request->rack_description ?? '';
            $validated['group_id']=($request->group_id) ? (int)  $request->group_id : 0;
            $validated['group_description']=$request->group_description ?? '';
            $validated['begbal']=($request->begbal) ? $request->begbal : 0;
            $validated['copy_qty']=($request->copies) ? $request->copies : 0;
            $validated['composite_cost']=($request->composite_cost) ? $request->composite_cost : 0;
            if($request->begbal!='' || $request->begbal!='undefined'){
                $validated['running_balance']=(double)$request->begbal;
            }
            $validated['draft']=1;
            $checker = strpos($request->pn_no, '_');
            if(false !== $checker){
                $pn_no=explode('_',$request->pn_no);
                $subcat_prefix=$pn_no[0];
                $series=$pn_no[1];
                $data=[
                    'subcat_prefix'=>$subcat_prefix,
                    'series'=>$series
                ];
                if(!PNSeries::where('subcat_prefix',$subcat_prefix)->where('series',$series)->exists()){
                    $pnseries=PNSeries::create($data);
                }
            }
            if($request->file('image1')){
                $imagename=$request->file('image1')->getClientOriginalName();
                $request->file('image1')->storeAs('images',$imagename, 'public');
                $validated['image1']=$imagename;
            }
            
            if($request->file('image2')){
                $imagename2=$request->file('image2')->getClientOriginalName();
                $request->file('image2')->storeAs('images',$imagename2, 'public');
                $validated['image2']=$imagename2;
            }

            if($request->file('image3')){
                $imagename3=$request->file('image3')->getClientOriginalName();
                $request->file('image3')->storeAs('images',$imagename3, 'public');
                $validated['image3']=$imagename3;
            }
            //if(count(json_decode($request->input("composite")))==0 && count(json_decode($request->input("variant")))==0 && count(json_decode($request->input("novariant")))==0){
            if(count(json_decode($request->input("composite")))==0 && count(json_decode($request->input("variant")))==0){
                if ($request->item_id=='') {
                    $items=Items::create($validated);
                    //$items = Items::insertGetId($validated);
                }else{
                    $items=Items::where('id',$request->item_id)->first();
                    $items->update($validated);
                }
                if(!PRItems::where('item_id',$request->item_id)->exists()){
                    $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    $pritemsdata['item_id']=$items->id;
                    $pritemsdata['pr_no']="WH STOCKS";
                    $insertpr=PRItems::create($pritemsdata);
                    if($insertpr){
                        $variantbaldata['item_id']=(int) $items->id;
                        $variantbaldata['whstocks_qty']=(double) $request->begbal;
                        $variantbaldata['balance']=(double) $request->begbal;
                        VariantsBalance::create($variantbaldata);
                        
                        $pivdata['pr_no']='WH STOCKS';
                        $pivdata['item_id']=(int) $items->id;
                        $pivdata['variant_id']='0';
                        $pivdata['quantity']=$request->begbal ?? 0;
                        PIVBalance::create($pivdata);
                    }
                }else{
                    $insertpr=PRItems::where('item_id',$request->item_id)->first();
                    $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    $pritemsdata['item_id']=$items->id;
                    $pritemsdata['pr_no']="WH STOCKS";
                    $insertpr->update($pritemsdata);
                    //$insertpr=PRItems::create($pritemsdata);
                    if($insertpr){
                        $insertvarbal=VariantsBalance::where('item_id',$request->item_id)->where('variant_id','0')->first();
                        $variantbaldata['item_id']=(int) $items->id;
                        $variantbaldata['whstocks_qty']=(double) $request->begbal;
                        $variantbaldata['balance']=(double) $request->begbal;
                        $insertvarbal->update($variantbaldata);

                        $insertpiv=PIVBalance::where('item_id',$request->item_id)->where('variant_id','0')->where('pr_no','WH STOCKS')->first();
                        $pivdata['pr_no']='WH STOCKS';
                        $pivdata['item_id']=(int) $items->id;
                        $pivdata['variant_id']='0';
                        $pivdata['quantity']=$request->begbal;
                        $insertpiv->update($pivdata);
                        //VariantsBalance::create($variantbaldata);
                    }
                }
            }else{
                if(count(json_decode($request->input("composite")))>0){
                    $validated['composite_flag']=1;
                    if ($request->item_id=='') {
                        $items=Items::create($validated);
                        //$items = Items::insertGetId($validated);
                    }else{
                        $items=Items::where('id',$request->item_id)->first();
                        $items->update($validated);
                    }
                    $composite=$request->input("composite");
                    // if (PRItems::where('item_id', $items->id)->exists()) {
                    //     $updatec=PRItems::where('item_id',$items->id)->first();
                    //     $begbal=PRItems::where('item_id',$items->id)->value('begbal');
                    //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal + $begbal : 0;
                    //     $updatec->update($pritemsdata);
                    // }else{
                    //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal : 0;
                    //     $pritemsdata['item_id']=$items->id;
                    //     PRItems::create($pritemsdata);
                    // }
                    if(!PRItems::where('item_id',$request->item_id)->exists()){
                        $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['item_id']=$items->id;
                        $pritemsdata['pr_no']="WH STOCKS";
                        $insertpr=PRItems::create($pritemsdata);
                        if($insertpr){
                            $variantbaldata['item_id']=(int) $items->id;
                            $variantbaldata['whstocks_qty']=(double) $request->begbal;
                            $variantbaldata['balance']=(double) $request->begbal;
                            VariantsBalance::create($variantbaldata);

                            $pivdata['pr_no']='WH STOCKS';
                            $pivdata['item_id']=(int) $items->id;
                            $pivdata['variant_id']='0';
                            $pivdata['quantity']=$request->begbal;
                            PIVBalance::create($pivdata);
                        }
                    }else{
                        $insertpr=PRItems::where('item_id',$request->item_id)->first();
                        $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['item_id']=$items->id;
                        $pritemsdata['pr_no']="WH STOCKS";
                        $insertpr->update($pritemsdata);
                        //$insertpr=PRItems::create($pritemsdata);
                        if($insertpr){
                            $insertvarbal=VariantsBalance::where('item_id',$request->item_id)->where('variant_id','0')->first();
                            $variantbaldata['item_id']=(int) $items->id;
                            $variantbaldata['whstocks_qty']=(double) $request->begbal;
                            $variantbaldata['balance']=(double) $request->begbal;
                            $insertvarbal->update($variantbaldata);

                            $insertpiv=PIVBalance::where('item_id',$request->item_id)->where('variant_id','0')->where('pr_no','WH STOCKS')->first();
                            $pivdata['pr_no']='WH STOCKS';
                            $pivdata['item_id']=(int) $items->id;
                            $pivdata['variant_id']='0';
                            $pivdata['quantity']=$request->begbal;
                            $insertpiv->update($pivdata);
                            //VariantsBalance::create($variantbaldata);
                        }
                    }
                    $z=1;
                    $checker_composite=array();
                    foreach(json_decode($composite) AS $c){
                        $variant_id=($c->variant_id=='') ? 0 : $c->variant_id;
                        $running_balance=Items::select('running_balance')->where('id',$c->compose_item_id)->value('running_balance');
                        $whstocks=VariantsBalance::select('whstocks_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('whstocks_qty');
                        $compositeqty=VariantsBalance::select('composite_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('composite_qty');
                        $balance=VariantsBalance::select('balance')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('balance');
                        if((double) $request->begbal==0){
                            $multiply_qty= (double) $c->quantity;
                        }else{
                            //$multiply_qty= (double) $request->begbal * (double) $c->quantity;
                            $multiply_qty= (double) $request->begbal * (double) $c->quantity;
                        }
                        // $multiply_qty= (double) $request->begbal * (double) $c->quantity;
                        if(!CompositeItems::where('item_id',$request->item_id)->where('compose_item_id',$c->compose_item_id)->where('variant_id',$variant_id)->exists()){
                            $compositedata['item_id']=$items->id;
                            $compositedata['variant_id']= (int) $variant_id;
                            $compositedata['identifier']=$z;
                            $compositedata['compose_item_id']= ($c->compose_item_id!='') ? (int) $c->compose_item_id : 0;
                            $compositedata['quantity']= ($c->quantity!='') ? (double) $multiply_qty : 0;
                            $composites=CompositeItems::create($compositedata);
                            $vbins=VariantsBalance::where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->update([
                                'variant_id'=>(int) $variant_id,
                                // 'whstocks_qty'=>$whstocks - $multiply_qty,
                                'composite_qty'=>(double) $multiply_qty + $compositeqty,
                                'balance'=>$balance - (double) $multiply_qty,
                            ]);
                            if($vbins){
                                $updaterunbal=Items::where("id",$c->compose_item_id)->first();
                                $updaterunbal->update([
                                    'running_balance'=> (double) $updaterunbal->running_balance - (double) $multiply_qty,
                                ]);
                                if($updaterunbal){
                                    if($variant_id!=0){
                                        $updatevariant=Variants::where('id',$variant_id)->first();
                                        $updatevariant->update([
                                            'composite_flag'=>1,
                                            'quantity'=> (double) $updatevariant->quantity - (double)$multiply_qty
                                        ]);
                                    }
                                    //if($updatevariant){
                                    // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                    // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                    // $updatebal->update([
                                    //     'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                    //     'balance'=> (double) $updatebal->balance - (double)$multiply_qty,
                                    // ]);
                                    if($c->pr_no!=''){
                                        $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                        // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                        $updatebal->update([
                                            'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                            'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                        ]);

                                        $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no',$c->pr_no)->first();
                                        $updatepiv->update([
                                            'quantity'=> (double) $updatepiv->quantity - (double)$multiply_qty,
                                        ]);
                                    }else{
                                        $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                        $updatebal->update([
                                            'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                            'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                        ]);

                                        $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no','WH STOCKS')->first();
                                        $updatepiv->update([
                                            'quantity'=> (double) $updatepiv->quantity - (double)$multiply_qty,
                                        ]);
                                    }
                                    //}
                                }
                                // $compositedata['item_id']=$items->id;
                                // $compositedata['variant_id']= (int) $c->variant_id;
                                // $compositedata['identifier']=$z;
                                // $compositedata['compose_item_id']= ($c->compose_item_id!='') ? (int) $c->compose_item_id : 0;
                                // $compositedata['quantity']= ($c->quantity!='') ? (double) $multiply_qty : 0;
                                // $composites=CompositeItems::create($compositedata);
                            }
                        }
                        $z++;
                    }
                }

                if(count(json_decode($request->input("variant")))>0){
                    $validated['variant_flag']=1;
                    if ($request->item_id=='') {
                        $items=Items::create($validated);
                    }else{
                        $items=Items::where('id',$request->item_id)->first();
                        $items->update($validated);
                    }
                    $variant=$request->input("variant");
                    // if(!PRItems::where('item_id',$request->item_id)->exists()){
                    //     // $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    //     // $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    //     // $pritemsdata['item_id']=$items->id;
                    //     // $pritemsdata['pr_no']="WH STOCKS";
                    //     // $insertpr=PRItems::create($pritemsdata);
                    //     // if($insertpr){
                    //     $variantbaldata['item_id']=(int) $items->id;
                    //     $variantbaldata['whstocks_qty']=(double) $request->begbal;
                    //     $variantbaldata['balance']=(double) $request->begbal;
                    //     VariantsBalance::create($variantbaldata);
                    //     //}
                    // }else{
                    //     // $insertpr=PRItems::where('item_id',$request->item_id)->first();
                    //     // $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    //     // $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    //     // $pritemsdata['item_id']=$items->id;
                    //     // $pritemsdata['pr_no']="WH STOCKS";
                    //     // $insertpr->update($pritemsdata);
                    //     // //$insertpr=PRItems::create($pritemsdata);
                    //     // if($insertpr){
                    //     $insertvarbal=VariantsBalance::where('item_id',$request->item_id)->where('variant_id','0')->first();
                    //     $variantbaldata['item_id']=(int) $items->id;
                    //     $variantbaldata['whstocks_qty']=(double) $request->begbal;
                    //     $variantbaldata['balance']=(double) $request->begbal;
                    //     $insertvarbal->update($variantbaldata);
                    //         //VariantsBalance::create($variantbaldata);
                    //     //}
                    // }
                    if(!PRItems::where('item_id',$request->item_id)->exists()){
                        // $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        // $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        // $pritemsdata['item_id']=$items->id;
                        // $pritemsdata['pr_no']="WH STOCKS";
                        // $insertpr=PRItems::create($pritemsdata);
                        //if($insertpr){
                            $variantbaldata['item_id']=(int) $items->id;
                            $variantbaldata['whstocks_qty']=(double) $request->begbal;
                            $variantbaldata['balance']=(double) $request->begbal;
                            VariantsBalance::create($variantbaldata);

                            $pivdata['pr_no']='WH STOCKS';
                            $pivdata['item_id']=(int) $items->id;
                            $pivdata['variant_id']='0';
                            $pivdata['quantity']=$request->begbal ?? 0;
                            PIVBalance::create($pivdata);
                        //}
                    }else{
                        $insertpr=PRItems::where('item_id',$request->item_id)->first();
                        $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['item_id']=$items->id;
                        $pritemsdata['pr_no']="WH STOCKS";
                        $insertpr->update($pritemsdata);
                        //$insertpr=PRItems::create($pritemsdata);
                        if($insertpr){
                            $insertvarbal=VariantsBalance::where('item_id',$request->item_id)->where('variant_id','0')->first();
                            $variantbaldata['item_id']=(int) $items->id;
                            $variantbaldata['whstocks_qty']=(double) $request->begbal;
                            $variantbaldata['balance']=(double) $request->begbal;
                            $insertvarbal->update($variantbaldata);

                            $insertpivdata=PIVBalance::where('item_id',$request->item_id)->where('variant_id','0')->where('pr_no','WH STOCKS')->first();
                            $pivdata['pr_no']='WH STOCKS';
                            $pivdata['item_id']=(int) $items->id;
                            $pivdata['variant_id']='0';
                            $pivdata['quantity']=$request->begbal ?? 0;
                            $insertpivdata->update($pivdata);
                            //VariantsBalance::create($variantbaldata);
                        }
                    }
                    $y=1;
                    $running_balance=0;
                    $item_status_checker=array();
                    foreach(json_decode($variant) AS $v){
                        $supplier_name=supplier::where('id',$v->supplier_id)->value('supplier_name');
                        $checker_var=Variants::where('item_id', $request->item_id)->where('identifier',$y)->first();
                        $item_status=ItemStatus::where('id',$v->item_status_id)->value('modes');
                        if($checker_var==null){
                            $variantdata['identifier']=$y;
                            $variantdata['item_id']=(int) $items->id;
                            $variantdata['supplier_id']= ($v->supplier_id!='') ? $v->supplier_id : 0;
                            $variantdata['supplier_name']=$supplier_name;
                            $variantdata['catalog_no']=$v->catalog_no;
                            $variantdata['brand']=$v->brand;
                            $variantdata['serial_no']=$v->serial_no;
                            $variantdata['barcode']=$v->barcode;
                            $variantdata['unit_cost']=$v->unit_cost;
                            $variantdata['currency']=$v->currency;
                            $variantdata['expiration']=$v->expiration;
                            $variantdata['quantity']= ($v->quantity!='') ? $v->quantity : 0;
                            $variantdata['uom']=$v->uom;
                            $variantdata['color']=$v->color;
                            $variantdata['size']=$v->size;
                            $variantdata['item_status_id']= ($v->item_status_id!='') ? $v->item_status_id : 0;
                            $updatev=Variants::firstOrCreate($variantdata);
                            if($item_status=='add'){
                                $running_balance+=$v->quantity;
                                $update_runbal = Items::find($items->id);
                                $update_runbal->update([
                                    'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                                ]);
                                $variantbaldata['item_id']=(int) $items->id;
                                $variantbaldata['variant_id']=(int) $updatev->id;
                                $variantbaldata['whstocks_qty']=(double) $v->quantity;
                                $variantbaldata['balance']=(double) $v->quantity;
                                VariantsBalance::create($variantbaldata);

                                $pivdata['pr_no']='WH STOCKS';
                                $pivdata['item_id']=(int) $items->id;
                                $pivdata['variant_id']=$updatev->id;
                                $pivdata['quantity']=$v->quantity;
                                PIVBalance::create($pivdata);
                            }else if($item_status==''){
                                $running_balance+=$v->quantity;
                                $update_runbal = Items::find($items->id);
                                $update_runbal->update([
                                    'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                                ]);
                                $variantbaldata['item_id']=(int) $items->id;
                                $variantbaldata['variant_id']=(int) $updatev->id;
                                $variantbaldata['whstocks_qty']=(double) $v->quantity;
                                $variantbaldata['balance']=(double) $v->quantity;
                                VariantsBalance::create($variantbaldata);

                                $pivdata['pr_no']='WH STOCKS';
                                $pivdata['item_id']=(int) $items->id;
                                $pivdata['variant_id']=$updatev->id;
                                $pivdata['quantity']=$v->quantity;
                                PIVBalance::create($pivdata);
                            }
                        }else{
                            $updatev=Variants::where('item_id',$request->item_id)->where('identifier',$y)->first();
                            $variantdata['supplier_id']= ($v->supplier_id!='') ? $v->supplier_id : 0;;
                            $variantdata['supplier_name']=$supplier_name;
                            $variantdata['catalog_no']=$v->catalog_no;
                            $variantdata['brand']=$v->brand;
                            $variantdata['serial_no']=$v->serial_no;
                            $variantdata['barcode']=$v->barcode;
                            $variantdata['unit_cost']=$v->unit_cost;
                            $variantdata['currency']=$v->currency;
                            $variantdata['expiration']=$v->expiration;
                            $variantdata['quantity']=($v->quantity!='') ? $v->quantity : 0;
                            $variantdata['uom']=$v->uom;
                            $variantdata['color']=$v->color;
                            $variantdata['size']=$v->size;
                            $variantdata['item_status_id']=($v->item_status_id!='') ? $v->item_status_id : 0;
                            $updatev->update($variantdata);
                            if($item_status=='add'){
                                $running_balance+=$v->quantity;
                                $update_runbal = Items::find($items->id);
                                $update_runbal->update([
                                    'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                                ]);
                                $updatevb1=VariantsBalance::where('item_id',$request->item_id)->where('variant_id',$v->id)->first();
                                $variantbaldata1['variant_id']=(int) $v->id;
                                $variantbaldata1['whstocks_qty']=(double) $v->quantity;
                                $variantbaldata1['balance']=(double) $v->quantity;
                                $updatevb1->update($variantbaldata1);

                                $updatepivdata=PIVBalance::where('item_id',$request->item_id)->where('variant_id',$v->id)->where('pr_no','WH STOCKS')->first();
                                $pivdataupdate['pr_no']='WH STOCKS';
                                $pivdataupdate['item_id']=(int) $items->id;
                                $pivdataupdate['variant_id']=$v->id;
                                $pivdataupdate['quantity']=$v->quantity;
                                $updatepivdata->update($pivdataupdate);
                            }else if($item_status==''){
                                $running_balance+=$v->quantity;
                                $update_runbal = Items::find($items->id);
                                $update_runbal->update([
                                    'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                                ]);
                                $updatevb2=VariantsBalance::where('item_id',$request->item_id)->where('variant_id',$v->id)->first();
                                $variantbaldata2['variant_id']=(int) $v->id;
                                $variantbaldata2['whstocks_qty']=(double) $v->quantity;
                                $variantbaldata2['balance']=(double) $v->quantity;
                                $updatevb2->update($variantbaldata2);

                                $updatepivdata=PIVBalance::where('item_id',$request->item_id)->where('variant_id',$v->id)->where('pr_no','WH STOCKS')->first();
                                $pivdataupdate['pr_no']='WH STOCKS';
                                $pivdataupdate['item_id']=(int) $items->id;
                                $pivdataupdate['variant_id']=$v->id;
                                $pivdataupdate['quantity']=$v->quantity;
                                $updatepivdata->update($pivdataupdate);
                            }
                        }
                        $y++;
                        $item_status_checker[]=ItemStatus::where('id',$v->item_status_id)->value('modes');
                    }
                    if ($request->item_id=='') {
                        if(in_array('add',$item_status_checker)){
                            $pritemsdata['begbal']=$running_balance + (double) $request->begbal;
                            $pritemsdata['balance']=$running_balance + (double) $request->begbal;
                            $pritemsdata['item_id']=$items->id;
                            $pritemsdata['pr_no']="WH STOCKS";
                            $insertpr=PRItems::create($pritemsdata);
                            // if($insertpr){
                            //     $variantbaldata['item_id']=(int) $items->id;
                            //     $variantbaldata['whstocks_qty']=(int) $request->begbal;
                            //     $variantbaldata['balance']=(int) $request->begbal;
                            //     VariantsBalance::create($variantbaldata);
                            // }
                            // if($insertpr){
                            //     $variantbaldata['item_id']=$items->id;
                            //     $variantbaldata['whstocks_qty']=$running_balance;
                            //     $variantbaldata['balance']=$running_balance;
                            //     VariantsBalance::create($variantbaldata);
                            // }
                        }else if(in_array('',$item_status_checker)){
                            $pritemsdata['begbal']=$running_balance + (double) $request->begbal;
                            $pritemsdata['balance']=$running_balance + (double) $request->begbal;
                            $pritemsdata['item_id']=$items->id;
                            $pritemsdata['pr_no']="WH STOCKS";
                            $insertpr=PRItems::create($pritemsdata);
                            // if($insertpr){
                            //     $variantbaldata['item_id']=(int) $items->id;
                            //     $variantbaldata['whstocks_qty']=(int) $request->begbal;
                            //     $variantbaldata['balance']=(int) $request->begbal;
                            //     VariantsBalance::create($variantbaldata);
                            // }
                            // if($insertpr){
                            //     $variantbaldata['item_id']=$items->id;
                            //     $variantbaldata['whstocks_qty']=$running_balance;
                            //     $variantbaldata['balance']=$running_balance;
                            //     VariantsBalance::create($variantbaldata);
                            // }
                        }
                    }else{
                        if(in_array('add',$item_status_checker)){
                            $update_pritems=PRItems::where('item_id',$request->item_id)->where('pr_no','WH STOCKS')->first();
                            $update_pritems->update([
                                'begbal'=>$running_balance + (double) $request->begbal,
                                'balance'=>$running_balance + (double) $request->begbal
                            ]);
                            // if($update_pritems){
                            //     $updatevb=VariantsBalance::where('item_id',$request->item_id)->first();
                            //     $variantbaldata['whstocks_qty']=$running_balance;
                            //     $variantbaldata['balance']=$running_balance;
                            //     $updatevb->update($variantbaldata);
                            // }
                        }else if(in_array('',$item_status_checker)){
                            $update_pritems=PRItems::where('item_id',$request->item_id)->where('pr_no','WH STOCKS')->first();
                            $update_pritems->update([
                                'begbal'=>$running_balance + (double) $request->begbal,
                                'balance'=>$running_balance + (double) $request->begbal
                            ]);
                            // if($update_pritems){
                            //     $updatevb=VariantsBalance::where('item_id',$request->item_id)->first();
                            //     $variantbaldata['whstocks_qty']=$running_balance;
                            //     $variantbaldata['balance']=$running_balance;
                            //     $updatevb->update($variantbaldata);
                            // }
                        }
                    }
                }

                // if(count(json_decode($request->input("novariant")))>0 && count(json_decode($request->input("variant")))==0 && count(json_decode($request->input("composite")))==0){
                //     //$items=Items::create($validated);
                //     $validated['novariant_flag']=1;
                //     if ($request->item_id=='') {
                //         $items=Items::create($validated);
                //         //$items = Items::insertGetId($validated);
                //     }else{
                //         $items=Items::where('id',$request->item_id)->first();
                //         $items->update($validated);
                //     }
                //     $novariant=$request->input("novariant");
                //     // if (PRItems::where('item_id', $items->id)->exists()) {
                //     //     $updatenv=PRItems::where('item_id',$items->id)->first();
                //     //     $begbal=PRItems::where('item_id',$items->id)->value('begbal');
                //     //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal + $begbal : 0;
                //     //     $updatenv->update($pritemsdata);
                //     // }else{
                //     //     $pritemsdata['begbal']=($request->begbal) ? $request->begbal : 0;
                //     //     $pritemsdata['item_id']=$items->id;
                //     //     PRItems::create($pritemsdata);
                //     // }
                //     //if($request->begbal!=0){
                //     if(!PRItems::where('item_id',$request->item_id)->exists()){
                //         $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                //         $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                //         $pritemsdata['item_id']=$items->id;
                //         $pritemsdata['pr_no']="WH STOCKS";
                //         $insertpr=PRItems::create($pritemsdata);
                //         if($insertpr){
                //             $variantbaldata['item_id']=(int) $items->id;
                //             $variantbaldata['whstocks_qty']=(double) $request->begbal;
                //             $variantbaldata['balance']=(double) $request->begbal;
                //             VariantsBalance::create($variantbaldata);
                //         }
                //     }else{
                //         $insertpr=PRItems::where('item_id',$request->item_id)->first();
                //         $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                //         $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                //         $pritemsdata['item_id']=$items->id;
                //         $pritemsdata['pr_no']="WH STOCKS";
                //         $insertpr->update($pritemsdata);
                //         //$insertpr=PRItems::create($pritemsdata);
                //         if($insertpr){
                //             $insertvarbal=VariantsBalance::where('item_id',$request->item_id)->where('variant_id','0')->first();
                //             $variantbaldata['item_id']=(int) $items->id;
                //             $variantbaldata['whstocks_qty']=(double) $request->begbal;
                //             $variantbaldata['balance']=(double) $request->begbal;
                //             $insertvarbal->update($variantbaldata);
                //             //VariantsBalance::create($variantbaldata);
                //         }
                //     }
                //     //}
                //     $x=1;
                //     foreach(json_decode($novariant) AS $n){
                //         // $novariantdata['item_id']=$item_id;
                //         // $novariantdata['serial_no']=$n->serial_no;
                //         // $novariantdata['barcode']=$n->bar_code;
                //         // $novariantdata['expiration']=$n->expiration;
                //         // $novariantdata['unit_cost']=$n->unit_cost;
                //         // $novariantdata['selling_price']=$n->selling_price;
                //         // $novariantdata['item_status_id']=$n->item_status;
                //         // NoVariants::create($novariantdata);
                //         //if(!NoVariants::where('item_id', $items->id)->exists() || $request->item_id==''){
                //         $checker_novar=NoVariants::where('item_id', $request->item_id)->where('identifier',$x)->first();
                //         if($checker_novar==null){
                //             $novariantdata['identifier']=$x;
                //             $novariantdata['item_id']=$items->id;
                //             $novariantdata['serial_no']=$n->serial_no;
                //             $novariantdata['barcode']=$n->bar_code;
                //             $novariantdata['expiration']=$n->expiration;
                //             $novariantdata['unit_cost']=$n->unit_cost;
                //             $novariantdata['selling_price']=$n->selling_price;
                //             $novariantdata['item_status_id']=$n->item_status;
                //             $novar=NoVariants::firstOrCreate($novariantdata);
                //             if($novar){
                //                 $novariantbaldata['item_id']=$request->item_id;
                //                 VariantsBalance::create($novariantbaldata);
                //             }
                //         }else{
                //             //$novar=NoVariants::where('item_id',$request->item_id)->get();
                //             //foreach($novar AS $no){
                //                 $updatev=NoVariants::where('item_id',$request->item_id)->where('identifier',$x)->first();
                //                 //$novariantdata['identifier']=$x;
                //                 $novariantdata['serial_no']=$n->serial_no;
                //                 $novariantdata['barcode']=$n->bar_code;
                //                 $novariantdata['expiration']=$n->expiration;
                //                 $novariantdata['unit_cost']=$n->unit_cost;
                //                 $novariantdata['selling_price']=$n->selling_price;
                //                 $novariantdata['item_status_id']=$n->item_status;
                //                 $updatev->update($novariantdata);
                //             //}
                            
                //         }
                //         $x++;
                //     }
                // }
            }
            $composite = CompositeItems::where('item_id',$items->id)->get();
            $variants = Variants::where('item_id',$items->id)->get();
            return response()->json([
                'item_id'=>$items->id,
                'item_id_value'=>$request->item_id,
                'composite'=>$composite,
                'variants'=>$variants,
            ],200);
        }
    }

    public function edit_items($id){
        $items = Items::find($id);
        $category_id = Items::where('id',$id)->pluck('item_category_id');
        $category_name=ItemCategory::where('id',$category_id)->pluck('category_name');
        $composite = CompositeItems::where('item_id',$id)->get();
        $variants = Variants::where('item_id',$id)->get();
        $novariants = NoVariants::where('item_id',$id)->get();
        $begbal_checker = Items::where('id',$id)->value('begbal');
        $begbal = VariantsBalance::where('item_id',$id)->where('variant_id','0')->value('balance');
        $currency=Config::get('constants.currency');
        // $begbal = PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
        $checker=array();
        $composite_qty=array();
        foreach($variants AS $v){
            if(($items->variant_flag==1 || $items->composite_flag==1) && (RequestItems::where('item_id', $id)->where('variant_id', $v->id)->exists() || IssuanceItems::where('item_id', $id)->where('variant_id', $v->id)->exists() || BorrowDetails::where('item_id', $id)->where('variant_id', $v->id)->exists()) || RestockDetails::where('item_id', $id)->where('variant_id', $v->id)->exists()) {
                $checker[]=1;
            }else{
                $checker[]=0;
            }
            $composite_qty[]=VariantsBalance::where('item_id',$id)->where('variant_id',$v->id)->value('composite_qty');
        }
        return response()->json([
            'items'=>$items,
            'begbal_checker'=>$begbal_checker,
            'category_name'=>$category_name,
            'composite'=>$composite,
            'variants'=>$variants,
            'novariants'=>$novariants,
            'checker'=>$checker,
            'begbal'=>$begbal,
            'composite_qty'=>$composite_qty,
            'currency'=>$currency,
        ],200);
    }

    public function update_items(ItemsRequest $request, $id){
        if($request->error_items==0){
            $items=Items::where('id',$id)->first();
            $validated = $request->validated();
            $validated['location_id']= ($request->location_id) ? $request->location_id : 0;
            $validated['location_description']=$request->location_description;
            $validated['warehouse_id']=($request->warehouse_id) ? $request->warehouse_id : 0;
            $validated['warehouse_description']=$request->warehouse_description;
            $validated['rack_id']=($request->rack_id) ? $request->rack_id : 0;
            $validated['rack_description']=$request->rack_description;
            $validated['group_id']=($request->group_id) ? $request->group_id : 0;
            $validated['group_description']=$request->group_description;
            $validated['begbal']=(int) $request->begbal;
            $validated['moq']= ($request->moq) ? $request->moq : 0;
            $validated['draft']=0;
            $validated['copy_qty']= ($request->copies) ? $request->copies : 0;
            $validated['composite_cost']= ($request->composite_cost) ? $request->composite_cost : 0;
            $checker = strpos($request->pn_no, '_');
            if(false !== $checker){
                $pn_no=explode('_',$request->pn_no);
                $subcat_prefix=$pn_no[0];
                $series=$pn_no[1];
                $data=[
                    'subcat_prefix'=>$subcat_prefix,
                    'series'=>$series
                ];
                if(!PNSeries::where('subcat_prefix',$subcat_prefix)->where('series',$series)->exists()){
                    $pnseries=PNSeries::create($data);
                }
            }
            if($request->file('image1')){
                $imagename=$request->file('image1')->getClientOriginalName();
                $request->file('image1')->storeAs('images',$imagename, 'public');
                $validated['image1']=$imagename;
            }
            
            if($request->file('image2')){
                $imagename2=$request->file('image2')->getClientOriginalName();
                $request->file('image2')->storeAs('images',$imagename2, 'public');
                $validated['image2']=$imagename2;
            }

            if($request->file('image3')){
                $imagename3=$request->file('image3')->getClientOriginalName();
                $request->file('image3')->storeAs('images',$imagename3, 'public');
                $validated['image3']=$imagename3;
            }
            $begbal_check=Items::where('id',$id)->value('begbal');
            $draft=Items::where('id',$id)->value('draft');
            //if(count(json_decode($request->input("composite")))==0 && count(json_decode($request->input("variant")))==0 && count(json_decode($request->input("novariant")))==0){
            if(count(json_decode($request->input("composite")))==0 && count(json_decode($request->input("variant")))==0){
                // if($request->begbal!=0 && $begbal_check==0){
                if(!PRItems::where('item_id',$id)->exists()){
                    $running_balance=Items::where('id',$id)->value('running_balance');
                    $validated['running_balance']=$request->begbal;
                    $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    $pritemsdata['item_id']=$id;
                    $pritemsdata['pr_no']="WH STOCKS";
                    $insertpr=PRItems::create($pritemsdata);
                    if($insertpr){
                        $variantbaldata['item_id']=(int) $id;
                        $variantbaldata['whstocks_qty']=(double) $request->begbal;
                        $variantbaldata['balance']=(double) $request->begbal;
                        VariantsBalance::create($variantbaldata);

                        $pivdata['pr_no']='WH STOCKS';
                        $pivdata['item_id']=(int) $id;
                        $pivdata['variant_id']='0';
                        $pivdata['quantity']=$request->begbal;
                        PIVBalance::create($pivdata);
                    }
                }else{
                    $running_balance=Items::where('id',$id)->value('running_balance');
                    if($draft==1){
                        $validated['running_balance']=$request->begbal;
                        $insertpr=PRItems::where('item_id',$id)->first();
                        $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['item_id']=$id;
                        $pritemsdata['pr_no']="WH STOCKS";
                        $insertpr->update($pritemsdata);
                        //$insertpr=PRItems::create($pritemsdata);
                        if($insertpr){
                            $insertvarbal=VariantsBalance::where('item_id',$id)->where('variant_id','0')->first();
                            $variantbaldata['item_id']=(int) $id;
                            $variantbaldata['whstocks_qty']=(double) $request->begbal;
                            $variantbaldata['balance']=(double) $request->begbal;
                            $insertvarbal->update($variantbaldata);

                            $insertpivdata=PIVBalance::where('item_id',$id)->where('variant_id','0')->where('pr_no','WH STOCKS')->first();
                            $pivdataupdate['pr_no']='WH STOCKS';
                            $pivdataupdate['item_id']=(int) $id;
                            $pivdataupdate['variant_id']='0';
                            $pivdataupdate['quantity']=$request->begbal;
                            $insertpivdata->update($pivdataupdate);
                            //VariantsBalance::create($variantbaldata);
                        }
                    }else{
                        if($request->begbal!=0 && $begbal_check==0){
                            $validated['running_balance']=$running_balance + $request->begbal;
                            $insertpr=PRItems::where('item_id',$id)->first();
                            $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            $pritemsdata['item_id']=$id;
                            $pritemsdata['pr_no']="WH STOCKS";
                            $insertpr->update($pritemsdata);
                            //$insertpr=PRItems::create($pritemsdata);
                            if($insertpr){
                                $insertvarbal=VariantsBalance::where('item_id',$id)->where('variant_id','0')->first();
                                $variantbaldata['item_id']=(int) $id;
                                $variantbaldata['whstocks_qty']=(double) $request->begbal;
                                $variantbaldata['balance']=(double) $request->begbal;
                                $insertvarbal->update($variantbaldata);

                                $insertpivdata=PIVBalance::where('item_id',$id)->where('variant_id','0')->where('pr_no','WH STOCKS')->first();
                                $pivdataupdate['pr_no']='WH STOCKS';
                                $pivdataupdate['item_id']=(int) $id;
                                $pivdataupdate['variant_id']='0';
                                $pivdataupdate['quantity']=$request->begbal;
                                $insertpivdata->update($pivdataupdate);
                                //VariantsBalance::create($variantbaldata);
                            }
                        }
                    }
                    // if($draft==1){
                    //     //if($begbal_check==0){
                    //     $insertpr=PRItems::where('item_id',$id)->first();
                    //     $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    //     $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    //     $pritemsdata['item_id']=$id;
                    //     $pritemsdata['pr_no']="WH STOCKS";
                    //     $insertpr->update($pritemsdata);
                    //     //$insertpr=PRItems::create($pritemsdata);
                    //     if($insertpr){
                    //         $insertvarbal=VariantsBalance::where('item_id',$id)->where('variant_id','0')->first();
                    //         $variantbaldata['item_id']=(int) $id;
                    //         $variantbaldata['whstocks_qty']=(double) $request->begbal;
                    //         $variantbaldata['balance']=(double) $request->begbal;
                    //         $insertvarbal->update($variantbaldata);
                    //         //VariantsBalance::create($variantbaldata);
                    //     }
                    // }
                }
                $items->update($validated);
            }else{
                // return $validated;
                if(count(json_decode($request->input("composite")))>0){
                    $validated['composite_flag']=1;
                    if(!PRItems::where('item_id',$id)->exists()){
                        //if($request->begbal!=0 && $begbal_check==0){
                        $running_balance=Items::where('id',$id)->value('running_balance');
                        $validated['running_balance']=$request->begbal;

                        $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['item_id']=$id;
                        $pritemsdata['pr_no']="WH STOCKS";
                        $insertpr=PRItems::create($pritemsdata);
                        if($insertpr){
                            $variantbaldata['item_id']=(int) $id;
                            $variantbaldata['whstocks_qty']=(double) $request->begbal;
                            $variantbaldata['balance']=(double) $request->begbal;
                            VariantsBalance::create($variantbaldata);

                            $pivdatains['pr_no']='WH STOCKS';
                            $pivdatains['item_id']=(int) $id;
                            $pivdatains['variant_id']='0';
                            $pivdatains['quantity']=$request->begbal;
                            PIVBalance::create($pivdatains);
                        }
                    }else{
                        if($draft==1){
                            // if($begbal_check==0){
                            $running_balance=Items::where('id',$id)->value('running_balance');
                            $validated['running_balance']=$request->begbal;
                            // $insertpr=PRItems::where('item_id',$id)->first();
                            // $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            // $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            // $pritemsdata['item_id']=$id;
                            // $pritemsdata['pr_no']="WH STOCKS";
                            // $insertpr->update($pritemsdata);
                            // //$insertpr=PRItems::create($pritemsdata);
                            // if($insertpr){
                                $insertvarbal=VariantsBalance::where('item_id',$id)->where('variant_id','0')->first();
                                $variantbaldata['item_id']=(int) $id;
                                $variantbaldata['whstocks_qty']=(double) $request->begbal;
                                $variantbaldata['balance']=(double) $request->begbal;
                                $insertvarbal->update($variantbaldata);

                                $insertpivdata=PIVBalance::where('item_id',$id)->where('variant_id','0')->where('pr_no','WH STOCKS')->first();
                                $pivdataupdate['pr_no']='WH STOCKS';
                                $pivdataupdate['item_id']=(int) $id;
                                $pivdataupdate['variant_id']='0';
                                $pivdataupdate['quantity']=$request->begbal;
                                $insertpivdata->update($pivdataupdate);
                                //VariantsBalance::create($variantbaldata);
                            // }
                        }
                    }
                    $items->update($validated);
                    $composite=$request->input("composite");
                    $items_duplicate=array();
                    // if($request->copies!=0){
                    //     for($x=0;$x<$request->copies;$x++){
                    //         $inc=$x+1;
                    //         $validated_duplicate=$request->validated();
                    //         $validated_duplicate['composite_flag']=1;
                    //         $validated_duplicate['item_description']= ($request->item_description) ? $request->item_description."-Copy ".$inc : 0;
                    //         $validated_duplicate['location_id']= ($request->location_id) ? $request->location_id : 0;
                    //         $validated_duplicate['location_description']=$request->location_description ?? '';
                    //         $validated_duplicate['warehouse_id']=($request->warehouse_id) ? $request->warehouse_id : 0;
                    //         $validated_duplicate['warehouse_description']=$request->warehouse_description ?? '';
                    //         $validated_duplicate['rack_id']=($request->rack_id) ? $request->rack_id : 0;
                    //         $validated_duplicate['rack_description']=$request->rack_description ?? '';
                    //         $validated_duplicate['group_id']=($request->group_id) ? $request->group_id : 0;
                    //         $validated_duplicate['group_description']=$request->group_description ?? '';
                    //         $validated_duplicate['begbal']=($request->begbal) ? $request->begbal : 0;
                    //         $validated_duplicate['moq']=($request->moq) ? $request->moq : 0;
                    //         $validated_duplicate['copy_qty']=($request->copies) ? $request->copies : 0;
                    //         $checker = strpos($request->pn_no, '_');
                    //         if(false !== $checker){
                    //             $pn_no=explode('_',$request->pn_no);
                    //             $subcat_prefix=$pn_no[0];
                    //             //$series=$pn_no[1];
                    //             $series=PNSeries::where('subcat_prefix',$subcat_prefix)->max('series');
                    //             $next=$series+1;
                    //             $data=[
                    //                 'subcat_prefix'=>$subcat_prefix,
                    //                 'series'=>$next
                    //             ];
                    //             $pnseries=PNSeries::create($data);
                    //         }
                    //         $validated_duplicate['pn_no']=($request->pn_no) ? $subcat_prefix."_".$next : '';
                    //         if($request->file('image1')){
                    //             $imagename=$request->file('image1')->getClientOriginalName();
                    //             $request->file('image1')->storeAs('images',$imagename, 'public');
                    //             $validated_duplicate['image1']=$imagename;
                    //         }
                            
                    //         if($request->file('image2')){
                    //             $imagename2=$request->file('image2')->getClientOriginalName();
                    //             $request->file('image2')->storeAs('images',$imagename2, 'public');
                    //             $validated_duplicate['image2']=$imagename2;
                    //         }

                    //         if($request->file('image3')){
                    //             $imagename3=$request->file('image3')->getClientOriginalName();
                    //             $request->file('image3')->storeAs('images',$imagename3, 'public');
                    //             $validated_duplicate['image3']=$imagename3;
                    //         }
                    //         if($request->begbal!='' || $request->begbal!='undefined'){
                    //             $validated_duplicate['running_balance']= ($request->begbal) ? $request->begbal : 0;
                    //         }
                    //         $items_duplicate[]=Items::create($validated_duplicate);
                    //         $pritemsdata['begbal']=((double) $request->begbal) ? (double) $request->begbal : 0;
                    //         $pritemsdata['balance']=((double) $request->begbal) ? (double) $request->begbal : 0;
                    //         $pritemsdata['item_id']=$items_duplicate[$x]->id;
                    //         $pritemsdata['pr_no']="WH STOCKS";
                    //         $insertpr=PRItems::create($pritemsdata);
                    //         if($insertpr){
                    //             $variantbaldata['item_id']=(int) $items_duplicate[$x]->id;
                    //             $variantbaldata['whstocks_qty']=(double) $request->begbal;
                    //             $variantbaldata['balance']=(double) $request->begbal;
                    //             VariantsBalance::create($variantbaldata);
                    //         }
                    //     }
                    // }
                    foreach(json_decode($composite) AS $c){
                        //if($c->id==0){
                            //if($composites){
                        //$multiply_qty= (double) $request->begbal * (double) $c->quantity;
                        $variant_id=($c->variant_id=='') ? 0 : $c->variant_id;
                        //if($request->copies==0){
                            $running_balance=Items::select('running_balance')->where('id',$c->compose_item_id)->value('running_balance');
                            $whstocks=VariantsBalance::select('whstocks_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('whstocks_qty');
                            $compositeqty=VariantsBalance::select('composite_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('composite_qty');
                            if($request->begbal==0){
                                $multiply_qty= (double) $c->quantity;
                            }else{
                                $multiply_qty= (double) $request->begbal * (double) $c->quantity;
                            }
                            if(!CompositeItems::where('item_id',$id)->where('compose_item_id',$c->compose_item_id)->where('variant_id',$variant_id)->exists()){
                                $compositedata['item_id']=(int)$c->item_id;
                                $compositedata['compose_item_id']=(int)$c->compose_item_id;
                                $compositedata['variant_id']=(int) $variant_id;
                                $compositedata['quantity']=$c->quantity;
                                $composites=CompositeItems::create($compositedata);
                                $vbins=VariantsBalance::where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->update([
                                    'variant_id'=>(int) $variant_id,
                                    // 'whstocks_qty'=>$whstocks - $multiply_qty,
                                    'composite_qty'=>(double) $multiply_qty + $compositeqty,
                                    'balance'=>$running_balance - (double) $multiply_qty,
                                ]);
                                if($vbins){
                                    $updaterunbal=Items::where("id",$c->compose_item_id)->first();
                                    $updaterunbal->update([
                                        'running_balance'=> (double) $updaterunbal->running_balance - (double) $multiply_qty,
                                    ]);
                                    if($updaterunbal){
                                        if($variant_id!=0){
                                            $updatevariant=Variants::where('id',$variant_id)->first();
                                            $updatevariant->update([
                                                'composite_flag'=>1,
                                                'quantity'=> (double) $updatevariant->quantity - (double)$multiply_qty
                                            ]);
                                        }
                                        //if($updatevariant){
                                        // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                        // // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                        // $updatebal->update([
                                        //     'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                        //     'balance'=> (double) $updatebal->balance - (double)$multiply_qty,
                                        // ]);
                                        if($c->pr_no!=''){
                                            $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no',$c->pr_no)->first();
                                            // $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                            $updatebal->update([
                                                'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                                'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                            ]);
                                            $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no',$c->pr_no)->first();
                                            $updatepiv2->update([
                                                'quantity'=> (double) $updatepiv2->quantity - (double)$multiply_qty
                                            ]);
                                        }else{
                                            $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                                            $updatebal->update([
                                                'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                                                'balance'=> (double) $updatebal->balance - (double) $multiply_qty,
                                            ]);
                                            $updatepiv=PIVBalance::where("item_id",$c->compose_item_id)->where('variant_id',$variant_id)->where('pr_no','WH STOCKS')->first();
                                            $updatepiv2->update([
                                                'quantity'=> (double) $updatepiv2->quantity - (double)$multiply_qty
                                            ]);
                                        }
                                        //}
                                    }
                                }
                            }
                        // }else{
                        //     for($x=0;$x<$request->copies;$x++){
                        //         $running_balance=Items::select('running_balance')->where('id',$c->compose_item_id)->value('running_balance');
                        //         $whstocks=VariantsBalance::select('whstocks_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('whstocks_qty');
                        //         $compositeqty=VariantsBalance::select('composite_qty')->where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->value('composite_qty');
                        //         if($request->begbal==0){
                        //             $multiply_qty= (double) $c->quantity;
                        //         }else{
                        //             $multiply_qty= (double) $request->begbal * (double) $c->quantity;
                        //         }
                        //         if(!CompositeItems::where('item_id',$items_duplicate[$x]->id)->where('compose_item_id',$c->compose_item_id)->where('variant_id',$variant_id)->exists()){
                        //             $compositedata['item_id']=(int)$items_duplicate[$x]->id;
                        //             $compositedata['compose_item_id']=(int)$c->compose_item_id;
                        //             $compositedata['variant_id']=(int) $variant_id;
                        //             //$compositedata['quantity']=$multiply_qty;
                        //             $compositedata['quantity']=$c->quantity;
                        //             $composites=CompositeItems::create($compositedata);

                        //             $vbins=VariantsBalance::where('item_id',$c->compose_item_id)->where('variant_id',$variant_id)->update([
                        //                 'variant_id'=>(int) $variant_id,
                        //                 // 'whstocks_qty'=>$whstocks - $multiply_qty,
                        //                 'composite_qty'=>(double) $multiply_qty + $compositeqty,
                        //                 'balance'=>$running_balance - (double) $multiply_qty,
                        //             ]);
                        //             if($vbins){
                        //                 $updaterunbal=Items::where("id",$c->compose_item_id)->first();
                        //                 $updaterunbal->update([
                        //                     'running_balance'=> (double) $updaterunbal->running_balance - (double) $multiply_qty,
                        //                 ]);
                        //                 if($updaterunbal){
                        //                     if($variant_id!=0){
                        //                         $updatevariant=Variants::where('id',$variant_id)->first();
                        //                         $updatevariant->update([
                        //                             'composite_flag'=>1,
                        //                             'quantity'=> (double) $updatevariant->quantity - (double)$multiply_qty
                        //                         ]);
                        //                     }
                        //                     //if($updatevariant){
                        //                     $updatebal=PRItems::where("item_id",$c->compose_item_id)->where('pr_no','WH STOCKS')->first();
                        //                     $updatebal->update([
                        //                         'composite_qty'=> (double) $updatebal->composite_qty + (double)$multiply_qty,
                        //                         'balance'=> (double) $updatebal->balance - (double)$multiply_qty,
                        //                     ]);
                        //                     //}
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }
                    }
                    return response()->json($items_duplicate);
                }

                if(count(json_decode($request->input("variant")))>0){
                    $validated['variant_flag']=1;
                    //if($request->begbal!=0 && $begbal_check==0){
                    // if(!PRItems::where('item_id',$id)->exists()){
                    //     $running_balance=Items::where('id',$id)->value('running_balance');
                    //     $validated['running_balance']=$running_balance+$request->begbal;
                        
                    //     // $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    //     // $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                    //     // $pritemsdata['item_id']=$id;
                    //     // $pritemsdata['pr_no']="WH STOCKS";
                    //     // $insertpr=PRItems::create($pritemsdata);
                    //     // if($insertpr){
                    //     $variantbaldata['item_id']=(int) $id;
                    //     $variantbaldata['whstocks_qty']=(double) $request->begbal;
                    //     $variantbaldata['balance']=(double) $request->begbal;
                    //     VariantsBalance::create($variantbaldata);
                    //     //}
                    // }
                    if(!PRItems::where('item_id',$id)->exists()){
                        //if($request->begbal!=0 && $begbal_check==0){
                        $running_balance=Items::where('id',$id)->value('running_balance');
                        $validated['running_balance']=$request->begbal;

                        $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                        $pritemsdata['item_id']=$id;
                        $pritemsdata['pr_no']="WH STOCKS";
                        $insertpr=PRItems::create($pritemsdata);
                        if($insertpr){
                            $variantbaldata['item_id']=(int) $id;
                            $variantbaldata['whstocks_qty']=(double) $request->begbal;
                            $variantbaldata['balance']=(double) $request->begbal;
                            VariantsBalance::create($variantbaldata);

                            $pivdata['pr_no']='WH STOCKS';
                            $pivdata['item_id']=(int) $id;
                            $pivdata['variant_id']='0';
                            $pivdata['quantity']=$request->begbal ?? 0;
                            PIVBalance::create($pivdata);
                        }
                    }else{
                        if($draft==1){
                            // if($begbal_check==0){
                            $running_balance=Items::where('id',$id)->value('running_balance');
                            $validated['running_balance']=$request->begbal;
                            // $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                            // $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                            // $insertpr=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                            // // $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            // // $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            // $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            // $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            // $pritemsdata['item_id']=$id;
                            // $pritemsdata['pr_no']="WH STOCKS";
                            // $insertpr->update($pritemsdata);
                            //$insertpr=PRItems::create($pritemsdata);
                            // if($insertpr){
                                $insertvarbal=VariantsBalance::where('item_id',$id)->where('variant_id','0')->first();
                                $variantbaldata['item_id']=(int) $id;
                                $variantbaldata['whstocks_qty']=(double) $request->begbal;
                                $variantbaldata['balance']=(double) $request->begbal;
                                $insertvarbal->update($variantbaldata);
                                //VariantsBalance::create($variantbaldata);
                                
                                $insertpivdata1=PIVBalance::where('item_id',$id)->where('variant_id','0')->where('pr_no','WH STOCKS')->first();
                                $pivdataupdate1['pr_no']='WH STOCKS';
                                $pivdataupdate1['item_id']=(int) $id;
                                $pivdataupdate1['variant_id']='0';
                                $pivdataupdate1['quantity']=$request->begbal;
                                $insertpivdata1->update($pivdataupdate1);
                            // }
                        }else if($request->begbal!=0 && $begbal_check==0 && $draft==0){
                            $running_balance=Items::where('id',$id)->value('running_balance');
                            $validated['running_balance']=$request->begbal;
                            // $insertpr=PRItems::where('item_id',$id)->first();
                            $insertpr=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                            $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                            $pritemsdata['item_id']=$id;
                            $pritemsdata['pr_no']="WH STOCKS";
                            $insertpr->update($pritemsdata);
                            if($insertpr){
                                $insertvarbal=VariantsBalance::where('item_id',$id)->where('variant_id','=','0')->first();
                                $variantbaldata['item_id']=(int) $id;
                                $variantbaldata['whstocks_qty']=(double) $request->begbal;
                                $variantbaldata['balance']=(double) $request->begbal;
                                $insertvarbal->update($variantbaldata);

                                $insertpivdata2=PIVBalance::where('item_id',$id)->where('variant_id','0')->where('pr_no','WH STOCKS')->first();
                                $pivdataupdate2['pr_no']='WH STOCKS';
                                $pivdataupdate2['item_id']=(int) $id;
                                $pivdataupdate2['variant_id']='0';
                                $pivdataupdate2['quantity']=$request->begbal;
                                $insertpivdata2->update($pivdataupdate2);
                            }
                        }
                    }
                    $items->update($validated);
                    $variant=$request->input("variant");
                    $running_balance=0;
                    $item_status_checker=array();
                    foreach(json_decode($variant) AS $v){
                        $item_status=ItemStatus::where('id',$v->item_status_id)->value('modes');
                        if($v->id==0){
                            $supplier_name=supplier::where('id',$v->supplier_id)->value('supplier_name');
                            $variantdata['item_id']=(int) $v->item_id;
                            $variantdata['supplier_id']= (int) $v->supplier_id;
                            $variantdata['supplier_name']=$supplier_name;
                            $variantdata['catalog_no']=$v->catalog_no;
                            $variantdata['brand']=$v->brand;
                            $variantdata['serial_no']=$v->serial_no;
                            $variantdata['barcode']=$v->barcode;
                            // $variantdata['unit_cost']=$v->unit_cost;
                            $variantdata['unit_cost']=$v->average_cost;
                            $variantdata['currency']=$v->currency;
                            $variantdata['expiration']=$v->expiration;
                            $variantdata['quantity']= (double) $v->quantity;
                            $variantdata['average_cost']= $v->average_cost;
                            $variantdata['uom']=$v->uom;
                            $variantdata['color']=$v->color;
                            $variantdata['size']=$v->size;
                            $variantdata['item_status_id']= (int) $v->item_status_id;
                            $updatev=Variants::create($variantdata);
                            if($item_status=='add'){
                                $running_balance+=$v->quantity;
                                $update_runbal = Items::find($id);
                                $update_runbal->update([
                                    'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                                ]);
                                $variantbaldata['item_id']=(int) $id;
                                $variantbaldata['variant_id']=(int) $updatev->id;
                                $variantbaldata['whstocks_qty']=(double) $v->quantity;
                                $variantbaldata['balance']=(double) $v->quantity;
                                VariantsBalance::create($variantbaldata);

                                $pivdata['pr_no']='WH STOCKS';
                                $pivdata['item_id']=(int) $id;
                                $pivdata['variant_id']=$updatev->id;
                                $pivdata['quantity']=$v->quantity;
                                PIVBalance::create($pivdata);

                                $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                                $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                                $update_pritems1=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                                $update_pritems1->update([
                                    'begbal'=>$prbegbal + $v->quantity,
                                    'balance'=>$prbalance + (double) $v->quantity
                                ]);
                            }else if($item_status==''){
                                $running_balance+=$v->quantity;
                                $update_runbal = Items::find($id);
                                $update_runbal->update([
                                    'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                                ]);
                                $variantbaldata['item_id']=(int) $id;
                                $variantbaldata['variant_id']=(int) $updatev->id;
                                $variantbaldata['whstocks_qty']=(double) $v->quantity;
                                $variantbaldata['balance']=(double) $v->quantity;
                                VariantsBalance::create($variantbaldata);

                                $pivdata['pr_no']='WH STOCKS';
                                $pivdata['item_id']=(int) $id;
                                $pivdata['variant_id']=$updatev->id;
                                $pivdata['quantity']=$v->quantity;
                                PIVBalance::create($pivdata);

                                $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                                $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                                $update_pritems2=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                                $update_pritems2->update([
                                    'begbal'=>$prbegbal + $v->quantity,
                                    'balance'=>$prbalance + (double) $v->quantity
                                ]);
                            }
                        }else{
                            $updatev=Variants::where('id',$v->id)->first();
                            $supplier_name=supplier::where('id',$v->supplier_id)->value('supplier_name');
                            $variantdata['supplier_id']=(int) $v->supplier_id;
                            $variantdata['supplier_name']=$supplier_name;
                            $variantdata['catalog_no']=$v->catalog_no;
                            $variantdata['brand']=$v->brand;
                            $variantdata['serial_no']=$v->serial_no;
                            $variantdata['barcode']=$v->barcode;
                            // $variantdata['unit_cost']=$v->unit_cost;
                            $variantdata['unit_cost']=$v->average_cost;
                            $variantdata['currency']=$v->currency;
                            $variantdata['expiration']=$v->expiration;
                            $variantdata['quantity']=(int) $v->quantity;
                            $variantdata['average_cost']= $v->average_cost;
                            $variantdata['uom']=$v->uom;
                            $variantdata['color']=$v->color;
                            $variantdata['size']=$v->size;
                            $variantdata['item_status_id']= (int) $v->item_status_id;
                            $updatev->update($variantdata);
                            if($item_status=='add'){
                                $running_balance+=$v->quantity;
                                $update_runbal = Items::find($id);
                                $update_runbal->update([
                                    'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                                ]);
                                $receive_flag=Variants::where('id',$v->id)->value('receive_flag');
                                $whstock_check=VariantsBalance::where('item_id',$id)->where('variant_id',$v->id)->value('whstocks_qty');
                                if($begbal_check==0 && $draft==0 && $receive_flag==0){
                                    $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                                    $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                                    $update_pritems=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                                    $update_pritems->update([
                                        'begbal'=>($prbegbal - $whstock_check) + $v->quantity,
                                        'balance'=>($prbegbal - $whstock_check) + $v->quantity
                                    ]);
                                    $updatevb=VariantsBalance::where('item_id',$id)->where('variant_id',$v->id)->first();
                                    $variantbaldatav['variant_id']=(int) $v->id;
                                    $variantbaldatav['whstocks_qty']=(double) $v->quantity;
                                    $variantbaldatav['balance']=(double) $v->quantity;
                                    $updatevb->update($variantbaldatav);

                                    $updatepiv=PIVBalance::where('item_id',$id)->where('variant_id',$v->id)->where('pr_no','WH STOCKS')->first();
                                    $pivdataupdate['pr_no']='WH STOCKS';
                                    $pivdataupdate['item_id']=(int) $id;
                                    $pivdataupdate['variant_id']=$v->id;
                                    $pivdataupdate['quantity']=$v->quantity;
                                    $updatepiv->update($pivdataupdate);
                                }else if($begbal_check!=0 && $draft==0 && $receive_flag==0){
                                    $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                                    $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                                    $update_pritems=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                                    $update_pritems->update([
                                        'begbal'=>($prbegbal - $whstock_check) + $v->quantity,
                                        'balance'=>($prbegbal - $whstock_check) + $v->quantity
                                    ]);
                                    $updatevb=VariantsBalance::where('item_id',$id)->where('variant_id',$v->id)->first();
                                    $variantbaldatav['variant_id']=(int) $v->id;
                                    $variantbaldatav['whstocks_qty']=(double) $v->quantity;
                                    $variantbaldatav['balance']=(double) $v->quantity;
                                    $updatevb->update($variantbaldatav);

                                    $updatepiv=PIVBalance::where('item_id',$id)->where('variant_id',$v->id)->where('pr_no','WH STOCKS')->first();
                                    $pivdataupdate['pr_no']='WH STOCKS';
                                    $pivdataupdate['item_id']=(int) $id;
                                    $pivdataupdate['variant_id']=$v->id;
                                    $pivdataupdate['quantity']=$v->quantity;
                                    $updatepiv->update($pivdataupdate);
                                }
                            }else if($item_status==''){
                                $running_balance+=$v->quantity;
                                $update_runbal = Items::find($id);
                                $update_runbal->update([
                                    'running_balance' => ($running_balance!=0) ? $running_balance + (double) $request->begbal : 0
                                ]);
                                $receive_flag=Variants::where('id',$v->id)->value('receive_flag');
                                $whstock_check=VariantsBalance::where('item_id',$id)->where('variant_id',$v->id)->value('whstocks_qty');
                                if($begbal_check==0 && $draft==0 && $receive_flag==0){
                                    $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                                    $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                                    $update_pritems=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                                    $update_pritems->update([
                                        'begbal'=>($prbegbal - $whstock_check) + $v->quantity,
                                        'balance'=>($prbegbal - $whstock_check) + $v->quantity
                                    ]);
                                    $updatevb=VariantsBalance::where('item_id',$id)->where('variant_id',$v->id)->first();
                                    $variantbaldatav['variant_id']=(int) $v->id;
                                    $variantbaldatav['whstocks_qty']=(double) $v->quantity;
                                    $variantbaldatav['balance']=(double) $v->quantity;
                                    $updatevb->update($variantbaldatav);
                                    
                                    $updatepiv=PIVBalance::where('item_id',$id)->where('variant_id',$v->id)->where('pr_no','WH STOCKS')->first();
                                    $pivdataupdate['pr_no']='WH STOCKS';
                                    $pivdataupdate['item_id']=(int) $id;
                                    $pivdataupdate['variant_id']=$v->id;
                                    $pivdataupdate['quantity']=$v->quantity;
                                    $updatepiv->update($pivdataupdate);
                                }else if($begbal_check!=0 && $draft==0 && $receive_flag==0){
                                    $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                                    $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                                    $update_pritems=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                                    $update_pritems->update([
                                        'begbal'=>($prbegbal - $whstock_check) + $v->quantity,
                                        'balance'=>($prbegbal - $whstock_check) + $v->quantity
                                    ]);
                                    $updatevb=VariantsBalance::where('item_id',$id)->where('variant_id',$v->id)->first();
                                    $variantbaldatav['variant_id']=(int) $v->id;
                                    $variantbaldatav['whstocks_qty']=(double) $v->quantity;
                                    $variantbaldatav['balance']=(double) $v->quantity;
                                    $updatevb->update($variantbaldatav);
                                }
                                // if($v->composite_flag==0){
                                //     $updatevb=VariantsBalance::where('item_id',$id)->where('variant_id',$v->id)->first();
                                //     $variantbaldatav['variant_id']=(int) $v->id;
                                //     $variantbaldatav['balance']=(double) $v->quantity;
                                //     $updatevb->update($variantbaldatav);
                                // }
                            }
                        }
                        // if($item_status=='add'){
                        //     $running_balance+=$v->quantity;
                        //     $update_runbal = Items::find($id);
                        //     $update_runbal->update([
                        //         'running_balance' => ($running_balance!=0) ? $running_balance : 0
                        //     ]);
                        // }
                        $item_status_checker[]=ItemStatus::where('id',$v->item_status_id)->value('modes');
                    }
                    // if(in_array('add',$item_status_checker)){
                    //     $variant_flag=Items::where('id',$id)->value('variant_flag');
                    //     if($draft==1){
                    //         $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                    //         $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                    //         $update_pritems=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                    //         $update_pritems->update([
                    //             // 'begbal'=>$running_balance + (double) $request->begbal,
                    //             'begbal'=>$prbegbal + (double) $request->begbal,
                    //             'balance'=>$running_balance + (double) $request->begbal
                    //         ]);
                    //     }else if($begbal_check==0 && $draft==0){
                    //         $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                    //         $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                    //         $update_pritems=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                    //         $update_pritems->update([
                    //             // 'begbal'=>$running_balance + (double) $request->begbal,
                    //             'begbal'=>$prbegbal + (double) $request->begbal,
                    //             'balance'=>$running_balance + (double) $request->begbal
                    //         ]);
                    //     }else if($begbal_check!=0 && $draft==0 && $variant_flag==0){
                    //         $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                    //         $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                    //         $update_pritems=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                    //         $update_pritems->update([
                    //             // 'begbal'=>$running_balance + (double) $request->begbal,
                    //             'begbal'=>$prbegbal + (double) $request->begbal,
                    //             'balance'=>$running_balance + (double) $request->begbal
                    //         ]);
                    //     }
                    //     // if($update_pritems){
                    //     //     $update_varbal=VariantsBalance::where('item_id',$id)->first();
                    //     //     $variantbaldata['whstocks_qty']=$running_balance;
                    //     //     $variantbaldata['balance']=$running_balance;
                    //     //     $update_varbal->update($variantbaldata);
                    //     // }
                    // }else if(in_array('',$item_status_checker)){
                    //     $prbegbal=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('begbal');
                    //     $prbalance=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->value('balance');
                    //     $update_pritems=PRItems::where('item_id',$id)->where('pr_no','WH STOCKS')->first();
                    //     $update_pritems->update([
                    //         // 'begbal'=>$running_balance + (double) $request->begbal,
                    //         // 'balance'=>$prbalance + (double) $request->begbal
                    //         'begbal'=>$prbegbal + (double) $request->begbal,
                    //         'balance'=>$running_balance + (double) $request->begbal
                    //     ]);
                    //     // if($update_pritems){
                    //     //     $update_varbal=VariantsBalance::where('item_id',$id)->first();
                    //     //     $variantbaldata['whstocks_qty']=$running_balance;
                    //     //     $variantbaldata['balance']=$running_balance;
                    //     //     $update_varbal->update($variantbaldata);
                    //     // }
                    // }
                }

                // if(count(json_decode($request->input("novariant")))>0 && count(json_decode($request->input("variant")))==0 && count(json_decode($request->input("composite")))==0){
                //     $validated['novariant_flag']=1;
                //     if($request->begbal!=0 && $begbal_check==0){
                //         $running_balance=Items::where('id',$id)->value('running_balance');
                //         $validated['running_balance']=$running_balance+$request->begbal;
                        
                //         $pritemsdata['begbal']=((double)$request->begbal) ? (double)$request->begbal : 0;
                //         $pritemsdata['balance']=((double)$request->begbal) ? (double)$request->begbal : 0;
                //         $pritemsdata['item_id']=$id;
                //         $pritemsdata['pr_no']="WH STOCKS";
                //         $insertpr=PRItems::create($pritemsdata);
                //         if($insertpr){
                //             $variantbaldata['item_id']=(int) $id;
                //             $variantbaldata['whstocks_qty']=(double) $request->begbal;
                //             $variantbaldata['balance']=(double) $request->begbal;
                //             VariantsBalance::create($variantbaldata);
                //         }
                //     }
                //     $items->update($validated);
                //     $novariant=$request->input("novariant");
                //     foreach(json_decode($novariant) AS $n){
                //         if($n->id==0){
                //             $novariantdata['item_id']=$n->item_id;
                //             $novariantdata['serial_no']=$n->serial_no;
                //             $novariantdata['barcode']=$n->barcode;
                //             $novariantdata['expiration']=$n->expiration;
                //             $novariantdata['unit_cost']=$n->unit_cost;
                //             $novariantdata['selling_price']=$n->selling_price;
                //             $novariantdata['item_status_id']=$n->item_status_id;
                //             $novar=NoVariants::create($novariantdata);
                //             if($novar){
                //                 $novariantbaldata['item_id']=$id;
                //                 VariantsBalance::create($novariantbaldata);
                //             }
                //         }else{
                //             $updatev=NoVariants::where('id',$n->id)->first();
                //             $novariantdata['serial_no']=$n->serial_no;
                //             $novariantdata['barcode']=$n->barcode;
                //             $novariantdata['expiration']=$n->expiration;
                //             $novariantdata['unit_cost']=$n->unit_cost;
                //             $novariantdata['selling_price']=$n->selling_price;
                //             $novariantdata['item_status_id']=$n->item_status_id;
                //             $updatev->update($novariantdata);
                //         }
                //     }
                // }
            }
         }
    }

    public function delete_composite($id,$clength,$composeitemid,$quantity,$variant,$index,$item_id){
        $composite = CompositeItems::find($id);
        $composite->delete();
        if($clength==1){
            $updatec=Items::where('id',$composeitemid)->first();
            $compositeup['composite_flag']=0;
            $updatec->update($compositeup);
            if($updatec){
                $running_balance=Items::select('running_balance')->where('id',$composeitemid)->value('running_balance');
                $balance=VariantsBalance::select('balance')->where('item_id',$composeitemid)->where('variant_id',$variant)->value('balance');
                // $balance=VariantsBalance::select('whstocks_qty')->where('item_id',$composeitemid)->where('variant_id',$variant)->value('whstocks_qty');
                $compositeqty=VariantsBalance::select('composite_qty')->where('item_id',$composeitemid)->where('variant_id',$variant)->value('composite_qty');
                $vbins=VariantsBalance::where('item_id',$composeitemid)->where('variant_id',$variant)->update([
                    //'whstocks_qty'=>$balance + $quantity,
                    'composite_qty'=>$compositeqty - $quantity,
                    'balance'=>$balance + $quantity,
                ]);
                if($vbins){
                    $updaterunbal=Items::where("id",$composeitemid)->first();
                    $updaterunbal->update([
                        'running_balance'=> $running_balance + $quantity,
                    ]);
                    if($updaterunbal){
                        if($variant!=0){
                            $variantqty=Variants::select('quantity')->where('id',$variant)->value('quantity');
                            $updatevariant=Variants::where('id',$variant)->first();
                            $updatevariant->update([
                                'composite_flag'=>0,
                                'quantity'=> $variantqty + $quantity
                            ]);
                        }
                        // if($updatevariant){
                            $prbalance=PRItems::where("item_id",$composeitemid)->where('pr_no','WH STOCKS')->value('balance');
                            $prcompositeqy=PRItems::where("item_id",$composeitemid)->where('pr_no','WH STOCKS')->value('composite_qty');
                            $updatebal=PRItems::where("item_id",$composeitemid)->where('pr_no','WH STOCKS')->first();
                            $updatebal->update([
                                'composite_qty'=> $prcompositeqy - $quantity,
                                'balance'=> $prbalance + $quantity,
                            ]);
                        //}
                    }
                    // $compositer = CompositeItems::where('item_id',$item_id)->get();
                    // return response()->json([
                    //     'composite'=>$compositer,
                    // ],200);
                }
            }
        }else{
            $running_balance=Items::select('running_balance')->where('id',$composeitemid)->value('running_balance');
            $balance=VariantsBalance::select('balance')->where('item_id',$composeitemid)->where('variant_id',$variant)->value('balance');
            // $balance=VariantsBalance::select('whstocks_qty')->where('item_id',$composeitemid)->where('variant_id',$variant)->value('whstocks_qty');
            $compositeqty=VariantsBalance::select('composite_qty')->where('item_id',$composeitemid)->where('variant_id',$variant)->value('composite_qty');
            $vbins=VariantsBalance::where('item_id',$composeitemid)->where('variant_id',$variant)->update([
                'whstocks_qty'=>$balance + $quantity,
                'composite_qty'=>$compositeqty - $quantity,
                'balance'=>$balance + $quantity,
            ]);
            if($vbins){
                $updaterunbal=Items::where("id",$composeitemid)->first();
                $updaterunbal->update([
                    'running_balance'=> $running_balance + $quantity,
                ]);
                if($updaterunbal){
                    if($variant!=0){
                        $variantqty=Variants::select('quantity')->where('id',$variant)->value('quantity');
                        $updatevariant=Variants::where('id',$variant)->first();
                        $updatevariant->update([
                            'composite_flag'=>0,
                            'quantity'=> $variantqty + $quantity
                        ]);
                    }
                    //if($updatevariant){
                    $prbalance=PRItems::where("item_id",$composeitemid)->where('pr_no','WH STOCKS')->value('balance');
                    $prcompositeqy=PRItems::where("item_id",$composeitemid)->where('pr_no','WH STOCKS')->value('composite_qty');
                    $updatebal=PRItems::where("item_id",$composeitemid)->where('pr_no','WH STOCKS')->first();
                    $updatebal->update([
                        'composite_qty'=> $prcompositeqy - $quantity,
                        'balance'=> $prbalance + $quantity,
                    ]);
                    //}
                }
                // $compositer = CompositeItems::where('item_id',$item_id)->get();
                // return response()->json([
                //     'composite'=>$compositer,
                // ],200);
            }
        }
    }

    public function delete_variant($id,$vlength,$itemid,$quantity){
        $variants = Variants::find($id);
        $variants->delete();
        $running_balance=Items::where('id',$itemid)->value('running_balance');
        $whstock_qty=PRItems::where('item_id',$itemid)->where('pr_no','WH STOCKS')->value('begbal');
        if($vlength==1){
            $updatec=Items::where('id',$itemid)->first();
            $variantup['running_balance']=$running_balance-$quantity;
            $variantup['variant_flag']=0;
            $updatec->update($variantup);

            $update_pritems=PRItems::where('item_id',$itemid)->where('pr_no','WH STOCKS')->first();
            $update_pritems->update([
                'begbal'=>$whstock_qty-$quantity,
                'balance'=>$whstock_qty-$quantity
            ]);
            $variants_balance = VariantsBalance::where('item_id',$itemid)->where('variant_id',$id)->delete();
        }else{
            $updatec=Items::where('id',$itemid)->first();
            $variantup['running_balance']=$running_balance-$quantity;
            $updatec->update($variantup); 

            $update_pritems=PRItems::where('item_id',$itemid)->where('pr_no','WH STOCKS')->first();
            $update_pritems->update([
                'begbal'=>$whstock_qty-$quantity,
                'balance'=>$whstock_qty-$quantity
            ]);
            $variants_balance = VariantsBalance::where('item_id',$itemid)->where('variant_id',$id)->delete();
        }
    }

    public function delete_novariant($id){
        $novariants = NoVariants::find($id);
        $novariants->delete();
    }

    public function get_novariant($item_id){
        if($item_id!=0){
            $novariant = NoVariants::where('item_id',$item_id)->get();
            return response()->json([
                'novariant'=>$novariant
            ],200);
        }else{
            return response()->json([
                'novariant'=>[]
            ],200);
        }
    }

    public function search_colors(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $items=Variants::select('color')->where('color','LIKE',"%$filter%")->orderBy('color','ASC')->get()->unique('color');
            return response()->json($items);
        }else{
            return response()->json([]);
        }
    }

    public function search_size(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $items=Variants::select('size')->where('size','LIKE',"%$filter%")->orderBy('size','ASC')->get()->unique('size');
            return response()->json($items);
        }else{
            return response()->json([]);
        }
    }

    public function search_uom(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $items=Variants::select('uom')->where('uom','LIKE',"%$filter%")->orderBy('uom','ASC')->get()->unique('uom');
            return response()->json($items);
        }else{
            return response()->json([]);
        }
    }

    public function search_brand(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $items=Variants::select('brand')->where('brand','LIKE',"%$filter%")->orderBy('brand','ASC')->get()->unique('brand');
            return response()->json($items);
        }else{
            return response()->json([]);
        }
    }

    public function search_variant(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $balance_checker=PRItems::where('item_id',$filter)->where('pr_no','WH STOCKS')->value('balance');
            //if($balance_checker!=0){
                $items=Variants::where('item_id',"$filter")->orderBy('supplier_name','ASC')->get()->unique('supplier_name');
                //$begbal=Items::where('id',$filter)->value('begbal');
                $begbal=VariantsBalance::where('item_id',$filter)->where('variant_id','0')->value('balance');
                //return response()->json($items);
                return response()->json([
                    'items'=>$items,
                    'begbal'=>$begbal
                ],200);
            // }else{
            //     return response()->json([]);
            // }
        }else{
            return response()->json([]);
        }
    }

    public function search_variantqty($variant_id,$item_id){
        if($variant_id!=0){
            $quantity=VariantsBalance::where('item_id',$item_id)->where('variant_id',$variant_id)->value('balance');
            $receieve_items=ReceiveItems::where('item_id',$item_id)->where('variant_id',$variant_id)->get();
            $pr_no='WH STOCKS';
            foreach($receieve_items AS $ri){
                $pr_no=ReceiveDetails::where('id',$ri->receive_details_id)->value('pr_no');
            }
            return response()->json([
                'quantity'=>(int)$quantity,
                'pr_no'=>$pr_no,
            ],200);
        }else{
            return response()->json([
                'quantity'=>0,
                'pr_no'=>'',
            ],200);
        }
    }

    public function check_composite($id){
        $composite = CompositeItems::where('item_id',$id)->get();
        $data=array();
        foreach($composite AS $c){
            $data=[
                'id'=>$c->item_id,
                'quantity'=>$c->quantity
            ];
        }
        return response()->json([
            'composite'=>$data,
        ],200);
    }

    public function search_compositeqty($variant_id,$item_id){
        $quantity=VariantsBalance::where('item_id',$item_id)->where('variant_id',"$variant_id")->value('composite_qty');
        // return response()->json([
        //     'quantity'=>$quantity
        // ],200);
        echo $quantity;
    }

    public function update_composite_duplicate(Request $request){
        $duplicate=$request->input("itemlist");
        foreach(json_decode($duplicate) AS $c){
            $items=Items::where('id',$c->id)->first();
            $validated['item_description']=$c->item_description;
            $items->update($validated);
        }   
    }

    public function moq_display_dashboard(Request $request){
        $filter=$request->get('filter');
        $items = Items::when($request->get('filter'), function ($query, $filter) {
            $query->where('item_description', 'LIKE', '%' . $filter . '%');
        })->orderBy('pn_no','ASC')->get();
        $data=array();
        foreach($items AS $i){
            if($i->running_balance<=$i->moq && $i->moq!=0){
                $data[]=[
                    'item_desc'=>$i->item_description,
                    'moq'=>$i->moq,
                    'running_balance'=>$i->running_balance,
                ];
            }
        }
        return response()->json([
            'items'=>$data,
        ],200);
    }
}
