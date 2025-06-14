<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function get_all_warehouse(Request $request){
        // $warehouses = Warehouse::orderBy('warehouse_name','ASC')->paginate(10);
        // return response()->json($warehouses);
        // return response()->json([
        //     'warehouses'=>$warehouses
        // ],200);
        $filter=$request->get('filter');
        if($filter!=null){
            $warehouses=Warehouse::where('warehouse_name','LIKE',"%$filter%")->orderBy('warehouse_name','ASC')->paginate(10);
            return response()->json($warehouses);
        }else{
            $warehouses = Warehouse::orderBy('warehouse_name','ASC')->paginate(10);
            return response()->json($warehouses);
            //return $this->get_all_warehouse();
        }
    }

    public function search_warehouse(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $warehouses=Warehouse::where('warehouse_name','LIKE',"%$filter%")->orderBy('warehouse_name','ASC')->paginate(10);
            return response()->json($warehouses);
        }else{
            $warehouses = Warehouse::orderBy('warehouse_name','ASC')->paginate(10);
            return response()->json($warehouses);
            //return $this->get_all_warehouse();
        }
    }

    public function create_warehouse(Request $request){
        $formData=[
            'warehouse_name'=>'',
        ];
        return response()->json($formData);
    }

    public function add_warehouse(Request $request){
        $validated=$this->validate($request,[
            'warehouse_name'=>['unique:warehouses','required','string']
        ]);
        $warehouse=Warehouse::create($validated);
    }

    public function edit_warehouse($id){
        $warehouses = Warehouse::find($id);
        return response()->json([
            'warehouses'=>$warehouses
        ],200);
    }

    public function update_warehouse(Request $request, $id){
        $warehouse=Warehouse::where('id',$id)->first();
        $validated=$this->validate($request,[
            'warehouse_name'=>'required|string|unique:warehouses,warehouse_name,'.$id
        ]);
        $warehouse->update($validated);
    }

}
