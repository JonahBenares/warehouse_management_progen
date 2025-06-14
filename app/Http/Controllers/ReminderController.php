<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Reminders;
use App\Models\User;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function reminderlist(Request $request){
        $filter=$request->get('filter');
        if($filter!=null){
            $head = Reminders::when($request->get('filter'), function ($query, $filter) {
                $query->where('reminder_date', 'LIKE', '%' . $filter . '%')
                ->orWhere('title', 'LIKE', '%' . $filter . '%')
                ->orWhere('notes', 'LIKE', '%' . $filter . '%')
                ->orWhere('person_to_notify_name', 'LIKE', '%' . $filter . '%');
            })->paginate(10);
        }else{
            $head = Reminders::paginate(10);
        }

        // return response()->json($head);
        return response()->json([
            'head'=>$head
          ],200);
    }

    public function create_reminder(Request $request){
        $formData=[
            'reminder_date'=>'',
            'title'=>'',
            'notes'=>'',
            'person_to_notify_name'=>'',
        ];
        return response()->json($formData);
    }

    public function addreminder(Request $request){
        $added_by_id = Auth::id();

        $validated=[
            'reminder_date'=>$request->reminder_date,
            'title'=>$request->title,
            'notes'=>$request->notes,
            'person_to_notify_id'=>$request->person_to_notify,
            'person_to_notify_name'=>User::where('id',$request->person_to_notify)->value('name'),
            'added_by_id'=>$added_by_id,
            'added_by_name'=>User::where('id',$added_by_id)->value('name')
        ];
        Reminders::create($validated);
    }

    public function editreminder($id){
        $reminder = Reminders::find($id);
        return response()->json([
            'reminder'=>$reminder,
        ],200);
    }

    public function updatereminder(Request $request, $id){
        $reminder=Reminders::where('id',$id)->first();
        $validated=[
            'reminder_date'=>$request->reminder_date,
            'title'=>$request->title,
            'notes'=>$request->notes,
            'person_to_notify_id'=>$request->person_to_notify,
            'person_to_notify_name'=>User::where('id',$request->person_to_notify)->value('name')
        ];
        $reminder->update($validated);
    }

    public function delete_reminder($id){
      
        $reminder=Reminders::where('id', '=', $id);
        $reminder->delete();
    }

}
