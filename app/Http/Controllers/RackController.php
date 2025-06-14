<?php

namespace App\Http\Controllers;

use App\Models\Rack;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function get_all_rack(Request $request){
        // $rack = Rack::orderBy('rack_name','ASC')->paginate(10);
        // return response()->json($rack);
        $filter=$request->get('filter');
        if($filter!=null){
            $rack=Rack::where('rack_name','LIKE',"%$filter%")->orderBy('rack_name','ASC')->paginate(10);
            return response()->json($rack);
        }else{
            $rack = Rack::orderBy('rack_name','ASC')->paginate(10);
            return response()->json($rack);
            //return $this->get_all_warehouse();
        }
    }
    
    public function search_rack(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $rack=Rack::where('rack_name','LIKE',"%$filter%")->orderBy('rack_name','ASC')->paginate(10);
            return response()->json($rack);
        }else{
            $rack = Rack::orderBy('rack_name','ASC')->paginate(10);
            return response()->json($rack);
            //return $this->get_all_warehouse();
        }
    }

    public function create_rack(Request $request){
        $formData=[
            'rack_name'=>'',
        ];
        return response()->json($formData);
    }

    public function add_rack(Request $request){
        $validated=$this->validate($request,[
            'rack_name'=>['unique:racks','required','string']
        ]);
        $rack=Rack::create($validated);
    }

    public function edit_rack($id){
        $racks = Rack::find($id);
        return response()->json([
            'racks'=>$racks
        ],200);
    }

    public function update_rack(Request $request, $id){
        $rack=Rack::where('id',$id)->first();
        $validated=$this->validate($request,[
            'rack_name'=>'required|string|unique:racks,rack_name,'.$id
        ]);
        //$rack->rack_name=$request->rack_name;
        $rack->update($validated);
    }
}
