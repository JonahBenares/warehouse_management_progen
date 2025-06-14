<?php

namespace App\Http\Controllers;

use App\Models\RestockReason;
use Illuminate\Http\Request;

class RestockReasonController extends Controller
{
    public function get_all_restockreason(Request $request){
        // $reason = RestockReason::orderBy('reason','ASC')->paginate(10);
        // return response()->json($reason);
        $filter=$request->get('filter');
        if($filter!=null){
            $reason=RestockReason::where('reason','LIKE',"%$filter%")->orderBy('reason','ASC')->paginate(10);
            return response()->json($reason);
        }else{
            $reason = RestockReason::orderBy('reason','ASC')->paginate(10);
            return response()->json($reason);
        }
    }
    
    public function search_restockreason(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $reason=RestockReason::where('reason','LIKE',"%$filter%")->orderBy('reason','ASC')->paginate(10);
            return response()->json($reason);
        }else{
            $reason = RestockReason::orderBy('reason','ASC')->paginate(10);
            return response()->json($reason);
        }
    }

    public function create_restockreason(Request $request){
        $formData=[
            'reason'=>'',
        ];
        return response()->json($formData);
    }

    public function add_restockreason(Request $request){
        $validated=$this->validate($request,[
            'reason'=>['unique:restock_reasons','required','string']
        ]);
        $reason=RestockReason::create($validated);
    }

    public function edit_restockreason($id){
        $reason = RestockReason::find($id);
        return response()->json([
            'reason'=>$reason
        ],200);
    }

    public function update_restockreason(Request $request, $id){
        $reason=RestockReason::where('id',$id)->first();
        $validated=$this->validate($request,[
            'reason'=>'required|string|unique:restock_reasons,reason,'.$id,
        ]);
        //$rack->reason=$request->reason;
        $reason->update($validated);
    }
}
