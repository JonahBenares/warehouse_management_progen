<?php

namespace App\Http\Controllers;

use App\Models\Enduse;
use App\Exports\EnduseExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class EnduseController extends Controller
{
    public function get_all_enduse(Request $request){
        // $enduse = Enduse::orderBy('enduse_name','ASC')->paginate(10);
        // return response()->json($enduse);
        $filter=$request->get('filter');
        if($filter!=null){
            $enduse=Enduse::where('enduse_name','LIKE',"%$filter%")->orderBy('enduse_name','ASC')->paginate(10);
            return response()->json($enduse);
        }else{
            $enduse = Enduse::paginate(10);
            return response()->json($enduse);
        }
    }

    public function search_enduse(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $enduses=Enduse::where('enduse_name','LIKE',"%$filter%")->orderBy('enduse_name','ASC')->paginate(10);
            return response()->json($enduses);
        }else{
            $enduses = Enduse::paginate(10);
            return response()->json($enduses);
        }
    }

    public function create_enduse(Request $request){
        $formData=[
            'enduse_name'=>null,
        ];
        return response()->json($formData);
    }

    public function add_enduse(Request $request){
        $validated=$this->validate($request,[
            'enduse_name'=>['unique:enduses','required','string']
        ]);
        $enduse=Enduse::create($validated);
    }

    public function edit_enduse($id){
        $enduses = Enduse::find($id);
        return response()->json([
            'enduses'=>$enduses
        ],200);
    }

    public function update_enduse(Request $request, $id){
        $enduses=Enduse::where('id',$id)->first();
        $validated=$this->validate($request,[
            'enduse_name'=>'required|string|unique:enduses,enduse_name,'.$id
        ]);

        $enduses->update($validated);
    }

    public function export_enduse(){
        //Excel::store(new BeginningBalanceExport, 'begbal.xlsx');
        return Excel::download(new EnduseExport, 'enduse.xlsx');
    }

}
