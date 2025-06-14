<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Http\Requests\SupplierRequest;

class SupplierController extends Controller
{
    public function get_all_supplier(Request $request){
        // $suppliers = Supplier::orderBy('supplier_name','ASC')->paginate(10);
        // return response()->json($suppliers);
        $filter=$request->get('filter');
        if($filter!=null){
            $suppliers=Supplier::where('supplier_name','LIKE',"%$filter%")
                ->orWhere('address','LIKE',"%$filter%")
                ->orWhere('contact_person','LIKE',"%$filter%")
                ->orWhere('contact_number','LIKE',"%$filter%")
                ->orWhere('email','LIKE',"%$filter%")
                ->orWhere('terms','LIKE',"%$filter%")
                ->orderBy('supplier_name','ASC')->paginate(10);
            return response()->json($suppliers);
        }else{
            $suppliers = Supplier::paginate(10);
            return response()->json($suppliers);
        }
    }

    public function search_supplier(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $suppliers=Supplier::where('supplier_name','LIKE',"%$filter%")
                ->orWhere('address','LIKE',"%$filter%")
                ->orWhere('contact_person','LIKE',"%$filter%")
                ->orWhere('contact_number','LIKE',"%$filter%")
                ->orWhere('email','LIKE',"%$filter%")
                ->orWhere('terms','LIKE',"%$filter%")
                ->orderBy('supplier_name','ASC')->paginate(10);
            return response()->json($suppliers);
        }else{
            $suppliers = Supplier::paginate(10);
            return response()->json($suppliers);
        }
    }


    public function create_supplier(Request $request){
        $formData=[
            'supplier_name'=>'',
            'address'=>'',
            'email'=>'',
            'contact_person'=>'',
            'contact_number'=>'',
            'terms'=>'',
            'is_active'=>0,
        ];
        return response()->json($formData);
    }

    public function add_supplier(SupplierRequest $request){

        $validated = $request->validated();
        Supplier::create($validated);
    }

    public function edit_supplier($id){
        $suppliers = Supplier::find($id);
        return response()->json([
            'suppliers'=>$suppliers
        ],200);
    }

    public function update_supplier(SupplierRequest $request, $id){
        $suppliers=Supplier::where('id',$id)->first();
        $validated = $request->validated();

        $suppliers->update($validated);
    }
        
}
