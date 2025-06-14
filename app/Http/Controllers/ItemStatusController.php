<?php

namespace App\Http\Controllers;

use App\Models\ItemStatus;
use Illuminate\Http\Request;

class ItemStatusController extends Controller
{
    public function get_all_status(Request $request){
        // $status = ItemStatus::orderBy('status','ASC')->paginate(10);
        // return response()->json($status);
        $filter=$request->get('filter');
        if($filter!=null){
            $status=ItemStatus::where('status','LIKE',"%$filter%")->orWhere('modes','LIKE',"%$filter%")->orderBy('status','ASC')->paginate(10);
            return response()->json($status);
        }else{
            $status = ItemStatus::orderBy('status','ASC')->paginate(10);
            return response()->json($status);
        }
    }
    
    public function search_status(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $status=ItemStatus::where('status','LIKE',"%$filter%")->where('modes','LIKE',"%$filter%")->orderBy('status','ASC')->paginate(10);
            return response()->json($status);
        }else{
            $status = ItemStatus::orderBy('status','ASC')->paginate(10);
            return response()->json($status);
        }
    }

    public function create_status(Request $request){
        $formData=[
            'status'=>'',
            'modes'=>'',
        ];
        return response()->json($formData);
    }

    public function add_status(Request $request){
        $validated=$this->validate($request,[
            'status'=>['unique:item_statuses','required','string'],
            'modes'=>['required','string']
        ]);
        $status=ItemStatus::create($validated);
    }

    public function edit_status($id){
        $status = ItemStatus::find($id);
        return response()->json([
            'status'=>$status,
        ],200);
    }

    public function update_status(Request $request, $id){
        $status=ItemStatus::where('id',$id)->first();
        $validated=$this->validate($request,[
            'status'=>'required|string|unique:item_statuses,status,'.$id,
            'modes'=>'required|string',
        ]);
        //$rack->status=$request->status;
        $status->update($validated);
    }
}
