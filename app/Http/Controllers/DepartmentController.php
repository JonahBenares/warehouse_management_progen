<?php

namespace App\Http\Controllers;
use App\Models\Department;
use App\Models\Group;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function get_all_department(Request $request){
        // $departments = Department::orderBy('department_name','ASC')->paginate(10);
        // return response()->json($departments);
        $filter=$request->get('filter');
        if($filter!=null){
            $departments=Department::where('department_name','LIKE',"%$filter%")->orderBy('department_name','ASC')->paginate(10);
            return response()->json($departments);
        }else{
            $departments = Department::orderBy('department_name','ASC')->paginate(10);
            return response()->json($departments);
        }
    }

    public function search_department(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $departments=Department::where('department_name','LIKE',"%$filter%")->orderBy('department_name','ASC')->paginate(10);
            return response()->json($departments);
        }else{
            $departments = Department::orderBy('department_name','ASC')->paginate(10);
            return response()->json($departments);
        }
    }

    public function create_department(Request $request){
        $formData=[
            'department_name'=>'',
        ];
        return response()->json($formData);
    }

    public function add_department(Request $request){

        $validated=$this->validate($request,[
            'department_name'=>['unique:departments','required','string']
        ]);
        Department::create($validated);
    }

    public function edit_department($id){
        $departments = Department::find($id);
        return response()->json([
            'departments'=>$departments
        ],200);
    }

    public function update_department(Request $request, $id){
        $departments=Department::where('id',$id)->first();
        $validated=$this->validate($request,[
            'department_name'=>'required|string|unique:departments,department_name,'.$id
        ]);

        $departments->update($validated);
    }

}
