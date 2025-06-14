<?php

namespace App\Http\Controllers;

use App\Models\Uom;
use Illuminate\Http\Request;

class UomController extends Controller
{
    public function get_all_uom(Request $request){
        // $uom = Uom::orderBy('unit_name','ASC')->paginate(10);
        // return response()->json($uom);
        $filter=$request->get('filter');
        if($filter!=null){
            $uoms=Uom::where('unit_name','LIKE',"%$filter%")->orderBy('unit_name','ASC')->paginate(10);
            return response()->json($uoms);
        }else{
            $uoms = Uom::paginate(10);
            return response()->json($uoms);
        }
    }

    public function search_uom(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $uoms=Uom::where('unit_name','LIKE',"%$filter%")->orderBy('unit_name','ASC')->paginate(10);
            return response()->json($uoms);
        }else{
            $uoms = Uom::paginate(10);
            return response()->json($uoms);
        }
    }

    public function create_uom(Request $request){
        $formData=[
            'unit_name'=>'',
        ];
        return response()->json($formData);
    }

    public function add_uom(Request $request){
        $validated=$this->validate($request,[
            'unit_name'=>['unique:uoms','required','string']
        ]);
        $uom=Uom::create($validated);
    }

    public function edit_uom($id){
        $uoms = Uom::find($id);
        return response()->json([
            'uoms'=>$uoms
        ],200);
    }

    public function update_uom(Request $request, $id){
        $uoms=Uom::where('id',$id)->first();
        $validated=$this->validate($request,[
            'unit_name'=>['unique:uoms','required','string']
        ]);

        $uoms->update($validated);
    }


}
