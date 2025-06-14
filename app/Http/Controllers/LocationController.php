<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function get_all_location(Request $request){
        // $location = Location::orderBy('location_name','ASC')->paginate(10);
        // return response()->json($location);
        $filter=$request->get('filter');
        if($filter!=null){
            $location=Location::where('location_name','LIKE',"%$filter%")->orderBy('location_name','ASC')->paginate(10);
            return response()->json($location);
        }else{
            $location = Location::paginate(10);
            return response()->json($location);
        }
    }

    public function search_location(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $locations=Location::where('location_name','LIKE',"%$filter%")->orderBy('location_name','ASC')->paginate(10);
            return response()->json($locations);
        }else{
            $locations = Location::paginate(10);
            return response()->json($locations);
        }
    }

    public function create_location(Request $request){
        $formData=[
            'location_name'=>null,
        ];
        return response()->json($formData);
    }

    public function add_location(Request $request){
        $validated=$this->validate($request,[
            'location_name'=>['unique:locations','required','string']
        ]);
        $location=Location::create($validated);
    }

    public function edit_location($id){
        $locations = Location::find($id);
        return response()->json([
            'locations'=>$locations
        ],200);
    }

    public function update_location(Request $request, $id){
        $locations=Location::where('id',$id)->first();
        $validated=$this->validate($request,[
            'location_name'=>'required|string|unique:locations,location_name,'.$id
        ]);

        $locations->update($validated);
    }
}
