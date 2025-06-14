<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Supplier;
use App\Models\Variants;
use App\Models\ReceiveHead;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\BackorderDetails;
use App\Models\BackorderItems;
use App\Models\ItemStatus;
use App\Models\Location;
use App\Models\Warehouse;
use App\Models\Rack;
use App\Models\group;
use App\Models\uom;
use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use App\Models\Items;
use App\Models\PRItems;
use App\Models\Enduse;
use App\Models\Purpose;
use App\Models\RestockReason;
use App\Models\VariantsBalance;
use Config;

class DropdownController extends Controller
{
    public function all_users(){
        $users=User::where('name','!=','')->orderBy('name','ASC')->get()->unique('name');
        return response()->json([
            'users'=>$users
        ],200);
    }

    public function all_department(){
        $department=Department::where('department_name','!=','')->orderBy('department_name','ASC')->get()->unique('department_name');
        return response()->json([
            'department'=>$department
        ],200);
    }

    public function all_supplier(){
        $supplier=Supplier::where('supplier_name','!=','')->orderBy('supplier_name','ASC')->get()->unique('supplier_name');
        return response()->json([
            'supplier'=>$supplier
        ],200);
    }


    public function all_pr(){
     
        // $pr = ReceiveDetails::with(['receive_head'])
        // ->whereHas('receive_head',function($query) {
        //     return $query->where('saved', '1')
        //             ->where('draft','0');
        // })->distinct()->get(['pr_no']);

        $pr = PRItems::distinct()->get(['pr_no']);

        return response()->json([
            'pr'=>$pr
        ],200);
    }

    public function all_pr_request(){
     
        // $pr_request = ReceiveDetails::with(['receive_head'])
        // ->whereHas('receive_head',function($query) {
        //     return $query->where('closed', '1')->where('pr_no','!=','WH Stocks');
        // })->get();

        $pr_request = PRItems::where('pr_no','!=','WH STOCKS')->where('receive_qty','!=',0)->where('balance','!=',0)->get()->unique('pr_no');

        return response()->json([
            'pr_request'=>$pr_request
        ],200);
    }

    public function all_pr_restock(){
        // $pr_request = PRItems::where('receive_qty','!=',0)->orderByRaw("CASE WHEN pr_no = 'WH Stocks' THEN 1 END")->get()->unique('pr_no');
        $pr_request = PRItems::where('receive_qty','!=',0)->where('pr_no','!=','WH STOCKS')->orderByRaw("CASE WHEN pr_no = 'WH STOCKS' THEN 1 END")->get()->unique('pr_no');
        return response()->json([
            'pr_request'=>$pr_request
        ],200);
    }

    public function all_brand(){
        $brand=Variants::select('brand')
            ->where('brand','!=','')
            ->orderBy('brand','ASC')
            ->get()
            ->unique('brand');
       // return $brand;
        return response()->json([
            'brand'=>$brand
        ],200);
    }

    public function all_catalog(){
        $catalog=Variants::select('catalog_no')
            ->where('catalog_no','!=','')
            ->orderBy('catalog_no','ASC')
            ->get()
            ->unique('catalog_no');
       // return $brand;
        return response()->json([
            'catalog'=>$catalog
        ],200);
    }

    public function all_status(){
        $status=ItemStatus::all();
        return response()->json([
            'status'=>$status
        ],200);
    }

    public function all_enduse(){
        $enduse=Enduse::where('enduse_name','!=','')->orderBy('enduse_name','ASC')->get()->unique('enduse_name');
        return response()->json([
            'enduse'=>$enduse
        ],200);
    }

    public function all_purpose(){
        $purpose=Purpose::where('purpose_name','!=','')->orderBy('purpose_name','ASC')->get()->unique('purpose_name');
        return response()->json([
            'purpose'=>$purpose
        ],200);
    }
  
    public function all_category(){
        $category=ItemCategory::where('category_name','!=','')->orderBy('category_name','ASC')->get()->unique('category_name');
        return response()->json([
            'category'=>$category
        ],200);
    }

    public function all_subcategory(){
        $subcategory=ItemSubCategory::where('subcat_name','!=','')->orderBy('subcat_name','ASC')->get()->unique('subcat_name');
        return response()->json([
            'subcategory'=>$subcategory
        ],200);
    }

    public function get_subcategories($id){
       
        $subcat=ItemSubCategory::where('item_category_id', '=', $id)->where('subcat_name','!=','')->orderBy('subcat_name','ASC')->get();
        return response()->json($subcat);
    }


    public function all_location(){
        $location=Location::where('location_name','!=','')->orderBy('location_name','ASC')->get()->unique('location_name');
        return response()->json([
            'location'=>$location
        ],200);
    }

    public function all_warehouse(){
        $warehouse=Warehouse::where('warehouse_name','!=','')->orderBy('warehouse_name','ASC')->get()->unique('warehouse_name');
        return response()->json([
            'warehouse'=>$warehouse
        ],200);
    }

    public function all_rack(){
        $rack=Rack::where('rack_name','!=','')->orderBy('rack_name','ASC')->get()->unique('rack_name');
        return response()->json([
            'rack'=>$rack
        ],200);
    }

    public function all_group(){
        $group=group::where('group_name','!=','')->orderBy('group_name','ASC')->get()->unique('group_name');
        return response()->json([
            'group'=>$group
        ],200);
    }

    public function all_uom(){
        $uom=uom::where('unit_name','!=','')->orderBy('unit_name','ASC')->get()->unique('unit_name');
        return response()->json([
            'uom'=>$uom
        ],200);
    }

    public function all_item(){
        $items=Items::where('item_description','!=','')->where('draft','=','0')->orderBy('item_description','ASC')->get()->unique('item_description');
        return response()->json([
            'items'=>$items
        ],200);
    }

    public function item_variant_list(){
        $items=Variants::with('items')->get()->unique('item_id');
        foreach($items AS $i){
            $it[] = [
                'id'=>$i->item_id,
                'item_description'=>$i->items->item_description,
            ];
        }
        return response()->json([
            'items'=>$it
        ],200);
    }

    public function all_item_composite(){
        $currency=Config::get('constants.currency');
        $items=Items::where('item_description','!=','')->where('draft','=','0')->where('composite_flag','=','0')->orderBy('item_description','ASC')->get()->unique('item_description');
        return response()->json([
            'items'=>$items,
            'currency'=>$currency,
        ],200);
    }

    public function all_suppliers(){
        $suppliers=supplier::where('supplier_name','!=','')->orderBy('supplier_name','ASC')->get()->unique('supplier_name');
        return response()->json([
            'suppliers'=>$suppliers
        ],200);
    }

    public function all_itemstatus(){
        $item_status=ItemStatus::where('status','!=','')->orderBy('status','ASC')->get()->unique('status');
        return response()->json([
            'item_status'=>$item_status
        ],200);
    }

    public function wh_items(){
        //$rec_items=ReceiveHead::join('receive_details','receive_details.receive_head_id','=','receive_head.id')->join('receive_items','receive_items.receive_head_id','=','receive_head.id')->where('pr_no','=','WH Stocks')->where('saved','!=','0')->orderBy('receive_items.item_description','ASC')->get()->unique('item_id');
        //$wh_items = PRItems::with(['items'])->where('pr_no','=','WH Stocks')->get();
        //$wh_items = VariantsBalance::with(['items'])->where('whstocks_qty','!=','0')->get();
        // $wh_items=Items::join('pr_items','pr_items.item_id','=','items.id')
        // ->join('variants_balance','variants_balance.item_id','=','items.id')
        // ->where('pr_items.pr_no','=','WH Stocks')
        // ->where('pr_items.balance','!=','0')
        // ->Orwhere('variants_balance.whstocks_qty','!=','0')
        // ->Orwhere('items.begbal','!=','0')
        // ->orderBy('item_description','ASC')
        // ->get()->unique('item_description');
        //$wh_items = Items::where('item_description','!=','')->where('running_balance','!=','0')->orderBy('item_description','ASC')->get()->unique('item_description');
        $query = Items::with(['pritems'])
        ->where('running_balance','!=','0')
        ->where('draft','=','0')
        ->orderBy('item_description','ASC');
        $query->whereHas('pritems', function ($query) {
            $query->where('pr_no', '=', 'WH STOCKS');
            $query->where('balance', '!=', '0');
        });

        $wh_items = $query->get()->unique('item_description');

        return response()->json([
            'wh_items'=>$wh_items
        ],200);
    }

    public function all_restockreason(){
        $restock_reason=RestockReason::where('reason','!=','')->orderBy('reason','ASC')->get()->unique('reason');
        return response()->json([
            'restock_reason'=>$restock_reason
        ],200);
    }
    public function all_po(){
            // $items = ReceiveItems::with(['receive_head', 'receive_details'])->wherecolumn('exp_quantity','!=','rec_quantity')->get()->unique('receive_head_id');

            $query = ReceiveItems::with(['receive_head', 'receive_details'])->wherecolumn('exp_quantity','!=','rec_quantity');
                $query->whereHas('receive_head', function ($query) {
                    $query->where('closed', '=', '1');
                    $query->where('draft', '=', '0');
                });
                
                $items = $query->get();

            $POList=[];
            foreach($items AS $i){
                    
                    $total_bo = BackorderItems::where('receive_items_id','=',$i->id)->sum('bo_quantity');
                    $total_qty = $i->rec_quantity + $total_bo;
                   
                    $total_qty = $i->rec_quantity + $total_bo;
                    if(($i->exp_quantity != $total_qty || $i->exp_quantity > $total_qty) && ($i->eval_flag==1 || $i->eval_flag==2)){
                        $POList[] = [
                            'id'=>$i->receive_head_id,
                            'po_no'=>$i->receive_head->po_no,
                        ];
                    }
                
            }

         
            $result = $this->unique_multidim_array($POList, 'po_no');
            return response()->json($result);
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

        public function receiveemp_list(){
            $users=User::where('name','!=','')->where('received_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function notedemp_list(){
            $users=User::where('name','!=','')->where('noted_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function acknowledgeemp_list(){
            $users=User::where('name','!=','')->where('acknowledge_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function approvedemp_list(){
            $users=User::where('name','!=','')->where('approved_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function requestedemp_list(){
            $users=User::where('name','!=','')->where('requested_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function releasedemp_list(){
            $users=User::where('name','!=','')->where('released_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function reviewedemp_list(){
            $users=User::where('name','!=','')->where('reviewed_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function deliveredemp_list(){
            $users=User::where('name','!=','')->where('delivered_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function inspectedemp_list(){
            $users=User::where('name','!=','')->where('inspected_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function returnedemp_list(){
            $users=User::where('name','!=','')->where('returned_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function recommendemp_list(){
            $users=User::where('name','!=','')->where('recommend_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function acceptedemp_list(){
            $users=User::where('name','!=','')->where('accepted_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function rejectedemp_list(){
            $users=User::where('name','!=','')->where('rejected_flag','1')->orderBy('name','ASC')->get()->unique('name');
            return response()->json([
                'users'=>$users
            ],200);
        }

        public function get_itemname($id){
            $item=Items::where('id', '=', $id)->value('item_description');
            return response()->json([
                'item'=>$item
            ],200);
        }
}
