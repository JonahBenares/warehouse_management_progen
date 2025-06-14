<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Group;

class GroupController extends Controller
{
    public function get_all_group(Request $request){
        // $groups = Group::orderBy('group_name','ASC')->paginate(10);
        // return response()->json($groups);
        $filter=$request->get('filter');
        if($filter!=null){
            $groups=Group::where('group_name','LIKE',"%$filter%")->orderBy('group_name','ASC')->paginate(10);
            return response()->json($groups);
        }else{
            $groups = Group::paginate(10);
            return response()->json($groups);
        }
    }

    public function search_group(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $groups=Group::where('group_name','LIKE',"%$filter%")->orderBy('group_name','ASC')->paginate(10);
            return response()->json($groups);
        }else{
            $groups = Group::paginate(10);
            return response()->json($groups);
        }
    }

    public function create_group(Request $request){
        $formData=[
            'group_name'=>'',
        ];
        return response()->json($formData);
    }

    public function add_group(Request $request){

        $validated=$this->validate($request,[
            'group_name'=>['unique:groups','required','string']
        ]);
        $group=Group::create($validated);
    }

    
    public function edit_group($id){
        $groups = Group::find($id);
        return response()->json([
            'groups'=>$groups
        ],200);
    }

    public function update_group(Request $request, $id){
        $groups=Group::where('id',$id)->first();
        $validated=$this->validate($request,[
            'group_name'=>'required|string|unique:groups,group_name,'.$id
        ]);

        $groups->update($validated);
    }
}
