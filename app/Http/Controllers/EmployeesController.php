<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\EmployeeEditRequest;
use Illuminate\Support\Str;
class EmployeesController extends Controller
{
    public function get_all_employees(Request $request){
        // $employee = User::with('department')->orderBy('name','ASC')->paginate(10);
        // return response()->json($employee);
        $filter=$request->get('filter');
        //if($filter!=null){
        $employee=User::query()->when($request->get('filter'), function ($query, $filter) {
            $query->where('name', 'LIKE', '%' . $filter . '%')
            ->orWhere('position', 'LIKE', '%' . $filter . '%')
            ->orWhere('email', 'LIKE', '%' . $filter . '%')
            ->orWhere('contact_no', 'LIKE', '%' . $filter . '%')
            ->orWhereHas('department', function($query) use ($filter) {
                $query->where('department_name', 'LIKE', '%'.$filter.'%');
            });
        })->with('department')->orderBy('name','ASC')->paginate(10);
        //$employees=User::with('department')->where('name','LIKE',"%$filter%")->orWhere('position','LIKE',"%$filter%")->orWhere('email','LIKE',"%$filter%")->orWhere('department_name','LIKE',"%$filter%")->orderBy('name','ASC')->paginate(10);
        return response()->json($employee);
        // }else{
        //     $employee = User::with('department')->orderBy('name','ASC')->paginate(10);
        //     return response()->json($employee);
        //     //return $this->get_all_warehouse();
        // }
    }

    public function search_employees(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $employees=User::query()->when($request->get('filter'), function ($query, $filter) {
                $query->where('name', 'LIKE', '%' . $filter . '%')
                ->orWhere('position', 'LIKE', '%' . $filter . '%')
                ->orWhere('email', 'LIKE', '%' . $filter . '%');
            })->orWhereHas('department', function($query) use ($filter) {
                $query->where('department_name', 'LIKE', '%'.$filter.'%');
            })->with('department')->orderBy('name','ASC')->paginate(10);
            //$employees=User::with('department')->where('name','LIKE',"%$filter%")->orWhere('position','LIKE',"%$filter%")->orWhere('email','LIKE',"%$filter%")->orWhere('department_name','LIKE',"%$filter%")->orderBy('name','ASC')->paginate(10);
            return response()->json($employees);
        }else{
            $employees = User::with('department')->orderBy('name','ASC')->paginate(10);
            return response()->json($employees);
            //return $this->get_all_warehouse();
        }
    }

    public function create_employees(Request $request){
        $randomString = Str::random(10);
        $formData=[
            'name'=>'',
            'email'=>'',
            'password'=>'',
            'user_type'=>'',
            'department_id'=>'',
            'position'=>'',
            'contact_no'=>'',
            'acknowledge_flag'=>0,
            'approved_flag'=>0,
            'requested_flag'=>0,
            'released_flag'=>0,
            'reviewed_flag'=>0,
            'delivered_flag'=>0,
            'inspected_flag'=>0,
            'noted_flag'=>0,
            'received_flag'=>0,
            'returned_flag'=>0,
            'recommend_flag'=>0,
            // 'accepted_flag'=>0,
            // 'rejected_flag'=>0,
            'access'=>0,
            'random_string'=>$randomString,
        ];
        return response()->json($formData);
    }

    public function add_employees(EmployeeRequest $request){
        $validated=$request->validated();
        $user=User::create($validated);
    }

    public function edit_employees($id){
        $employees = User::find($id);
        $randomString = Str::random(10);
        return response()->json([
            'employees'=>$employees,
            'random_string'=>$randomString,
        ],200);
    }

    public function update_employees(EmployeeEditRequest $request, $id){
        $employees=User::where('id',$id)->first();
        $validated = $request->validated();
        if($request->access=='1'){
            // if($request->temp_password!='null' && $request->email!='null'){
            $validated = [
                'access'=>1,
                'name'=>$request->name,
                'email'=>$request->email,
                'position'=>$request->position,
                'user_type'=>$request->user_type,
                'department_id'=>$request->department_id,
                'contact_no'=>$request->contact_no,
                'password'=>$request->temp_password,
                'temp_password'=>$request->temp_password,
                'acknowledge_flag'=>$request->acknowledge_flag,
                'approved_flag'=>$request->approved_flag,
                'requested_flag'=>$request->requested_flag,
                'released_flag'=>$request->released_flag,
                'reviewed_flag'=>$request->reviewed_flag,
                'delivered_flag'=>$request->delivered_flag,
                'inspected_flag'=>$request->inspected_flag,
                'noted_flag'=>$request->noted_flag,
                'received_flag'=>$request->received_flag,
                'returned_flag'=>$request->returned_flag,
                'recommend_flag'=>$request->recommend_flag,
                // 'accepted_flag'=>$request->accepted_flag,
                // 'rejected_flag'=>$request->rejected_flag,
            ];
        }
        // else if($request->email=='null' && $request->temp_password!='null'){
        //     $validated = [
        //         'access'=>1,
        //         'name'=>$request->name,
        //         'email'=>$request->email,
        //         'position'=>$request->position,
        //         'department_id'=>$request->department_id,
        //         'contact_no'=>$request->contact_no,
        //         'email'=>null,
        //         'acknowledge_flag'=>$request->acknowledge_flag,
        //         'approved_flag'=>$request->approved_flag,
        //         'requested_flag'=>$request->requested_flag,
        //         'released_flag'=>$request->released_flag,
        //         'reviewed_flag'=>$request->reviewed_flag,
        //         'delivered_flag'=>$request->delivered_flag,
        //         'inspected_flag'=>$request->inspected_flag,
        //         'noted_flag'=>$request->noted_flag,
        //         'received_flag'=>$request->received_flag,
        //         'returned_flag'=>$request->returned_flag,
        //         'recommend_flag'=>$request->recommend_flag,
        //     ];
        // }
        else if($request->access==0){
            $validated = [
                'access'=>0,
                'name'=>$request->name,
                'position'=>$request->position,
                'department_id'=>$request->department_id,
                'contact_no'=>$request->contact_no,
                //'email'=>'',
                'temp_password'=>'',
                'user_type'=>'',
                'acknowledge_flag'=>$request->acknowledge_flag,
                'approved_flag'=>$request->approved_flag,
                'requested_flag'=>$request->requested_flag,
                'released_flag'=>$request->released_flag,
                'reviewed_flag'=>$request->reviewed_flag,
                'delivered_flag'=>$request->delivered_flag,
                'inspected_flag'=>$request->inspected_flag,
                'noted_flag'=>$request->noted_flag,
                'received_flag'=>$request->received_flag,
                'returned_flag'=>$request->returned_flag,
                'recommend_flag'=>$request->recommend_flag,
                // 'accepted_flag'=>$request->accepted_flag,
                // 'rejected_flag'=>$request->rejected_flag,
            ];
        }
        $employees->update($validated);
    }
}
