<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\BackorderHead;
use App\Models\BackorderDetails;
use App\Models\BackorderItems;
use App\Models\ReceiveHead;
use App\Models\ReceiveDetails;
use App\Models\ReceiveItems;
use App\Models\Reminders;
use App\Http\Requests\ReminderRequest;

class AuthController extends Controller
{
   public function login_form(){
        $formData = [
            'email'=>null,
            'password'=> null,
        ];

        return response()->json($formData);
   }

   public function login_process(Request $request){
       
    
   
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

         if (Auth::attempt($credentials)) {
            //$user = User::where('email', $request->email)->first();
            $user = $request->user();
           $success['token'] = $user->createToken('MyApp')->plainTextToken;
           $success['name'] = $user->name;

           $response = [
                'success' => true,
                'data' => $success,
                'message' => 'User login successfully'
           ];

           return response()->json($response, 200);
         } else {

            $response = [
                'success' => false,
                'message' => 'Unauthorized'
           ];
           
           return response()->json($response,200);     
        }

      

   
    }

    public function dashboard(){
        if(Auth::check()){
            $credentials=[
                'name' => Auth::user()?->name,
                'temp_password' => Auth::user()?->temp_password,
            ];
        }else{
            $credentials=[
                'name' => '',
                'temp_password' => '',
            ];
        }
        return response()->json($credentials);
   }

   public function change_password(){
        $credentials=[
            'id' => Auth::user()->id,
            'name' => Auth::user()->name,
        ];
        return response()->json($credentials);
    }

    public function create_credential(Request $request){
        $formData=[
            'new_password'=>'',
            'confirm_password'=>'',
        ];
        return response()->json($formData);
    }

    public function edit_password(Request $request){
        $employees=User::where('id',$request->user_id)->where('change_password','=',0)->first();
        $validated=[
            'password' => $request->password,
            'temp_password'=>null,
            'change_password'=>1
        ];
        // $validated=$this->validate($request,[
        //     'password'=>['required','min:6','max:10'],
        //     // 'temp_password'=>null,
        //     // 'change_password'=>1
        // ]);
        // $validated=[
        //     //'password' => $request->password,
        //     'temp_password'=>null,
        //     'change_password'=>1
        // ];
        //return $validated;
        $employees->update($validated);
    }

    public function get_backorder_list(Request $request){
        //$items = ReceiveItems::with(['receive_head', 'receive_details'])->wherecolumn('exp_quantity','!=','rec_quantity')->get();
        $filter=$request->get('filter');
        $query = ReceiveItems::with(['receive_head', 'receive_details'])->when($request->get('filter'), function ($query, $filter) {
            $query->where('item_description', 'LIKE', '%' . $filter . '%')->orWhereHas('receive_head', function($query) use ($filter) {
                $query->where('po_no', 'LIKE', '%'.$filter.'%');
            })->orWhereHas('receive_details', function($query) use ($filter) {
                $query->where('pr_no', 'LIKE', '%'.$filter.'%');
            });
        })->wherecolumn('exp_quantity','!=','rec_quantity')->whereHas('receive_head', function ($query)  {
            $query->where('closed', '=', '1');
            $query->where('draft', '=', '0');
        })->where('eval_flag','1')->orWhere('eval_flag','2');
        // $query->whereHas('receive_head', function ($query)  {
        //     $query->where('closed', '=', '1');
        //     $query->where('draft', '=', '0');
        // });
          
        $items = $query->get();
        $BackorderItems=array();
        foreach($items AS $i){
               
            $total_bo = BackorderItems::where('receive_items_id','=',$i->id)->sum('bo_quantity');
        
            $total_qty = $i->rec_quantity + $total_bo;
            $remaining_qty =$i->exp_quantity - $total_qty;
            if($i->exp_quantity != $total_qty || $i->exp_quantity > $total_qty){
                    $BackorderItems[] = [
                        'headid'=>$i->receive_head_id,
                        'po_no'=>$i->receive_head->po_no,
                        'pr_no'=>$i->receive_details->pr_no,
                        'item_desc'=>$i->item_description,
                        'exp_qty'=>$i->exp_quantity,
                        'rec_qty'=>$i->rec_quantity,
                        'bo_qty'=>$total_bo,
                    ];
                
            }

        }

        return response()->json($BackorderItems);
    }

    public function get_reminder_list(Request $request){
        $filter=$request->get('filter');
        $head = Reminders::when($request->get('filter'), function ($query, $filter) {
            $query->where('reminder_date', 'LIKE', '%' . $filter . '%')
            //->orWhere('receive_date', 'LIKE', '%' . $filter . '%')
            ->orWhere('title', 'LIKE', '%' . $filter . '%')
            ->orWhere('notes', 'LIKE', '%' . $filter . '%')
            ->orWhere('person_to_notify_name', 'LIKE', '%' . $filter . '%');
        })->where('done','=','0')->paginate(10);

        return response()->json([
            'head'=>$head
          ],200);
        // return response()->json($head);
    }

    public function add_reminder(ReminderRequest $request){
        $added_by_id = Auth::id();

        $validated=$request->validated();
        $validated['person_to_notify_name']=User::where('id',$request->person_to_notify_id)->value('name');
        $validated['added_by_id']= $added_by_id;
        $validated['added_by_name']=User::where('id',$added_by_id)->value('name');
        $reminder=Reminders::create($validated);
    }

    public function done_reminder($id){
        $head=Reminders::where('id',$id)->first();
        $data = [
            'done'=>'1'
        ];
        $head->update($data);
    }

    public function edit_reminder($id){
        $reminder_update = Reminders::find($id);
        return response()->json([
            'reminder_update'=>$reminder_update
        ],200);
    }

    public function update_reminder(ReminderRequest $request, $id){
        $reminder=Reminders::where('id',$id)->first();
        $validated=$request->validated();
        $validated['person_to_notify_name']=User::where('id',$request->person_to_notify_id)->value('name');
        $reminder->update($validated);
    }

}
