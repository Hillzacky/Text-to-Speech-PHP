<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Support;
use App\Models\User;
use App\Mailers\AppMailer;
use Carbon\Carbon;
use DataTables;


class SupportController extends Controller
{

    /**
     * Display all support cases
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Support::all()->sortByDesc("created_at");
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>
                                        <a href="'. route("admin.support.show", $row["ticket_id"] ). '"><i class="fa-solid fa-message-question voice-action-buttons rename-voice" title="View Support Ticket"></i></a>
                                        <a class="deleteNotificationButton" id="'. $row["id"] .'" href="#"><i class="fa-solid fa-trash-xmark voice-action-buttons deactivate-voice" title="Delete Support Ticket"></i></a> 
                                    </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box support-'.strtolower($row["status"]).'">'.$row["status"].'</span>';
                        return $custom_status;
                    })
                    ->addColumn('custom-priority', function($row){
                        $custom_priority = '<span class="cell-box priority-'.strtolower($row["priority"]).'">'.$row["priority"].'</span>';
                        return $custom_priority;
                    })
                    ->addColumn('username', function($row){
                        $username = '<span>'.User::find($row["user_id"])->name.'</span>';
                        return $username;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on', 'custom-priority', 'username'])
                    ->make(true);
                    
        }

        return view('admin.support.index');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($ticket_id)
    {   
        $ticket = Support::where('ticket_id', $ticket_id)->firstOrFail();
        
        $created_by = User::find($ticket->user_id)->name;

        return view('admin.support.show', compact('ticket', 'created_by'));     

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AppMailer $mailer, $ticket_id)
    {
        request()->validate([
            'response-status' => 'required',
            'response' => 'required'
        ]);
        
        if (request('response-status') !== 'Pending' || request('response-status') !== 'Escalated') {
            $resolved_on = Carbon::now();
            $resolved_by = Auth::user()->name;
            $notify = true;
        } else {
            $resolved_on = '';
            $resolved_by = '';
            $notify = false;
        }

        $response = Support::where('ticket_id', $ticket_id)->firstOrFail();

        $response->status = request('response-status');
        $response->response = htmlspecialchars(request('response'));
        $response->resolved_on = $resolved_on;
        $response->resolved_by = $resolved_by;

        $response->save();

        if ($notify) {
            $user = User::find($response->user_id);
            $mailer->sendSupportTicketStatusNotification($user, $response);
        }        

        return redirect()->route('admin.support')->with("success", "Support ticket response has been saved");
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {   
        if ($request->ajax()) {

            $ticket = Support::where('id', request('id'))->firstOrFail();  

            if ($ticket){

                $ticket->delete();

                return response()->json('success');   

            } else{
                return response()->json('error');
            } 
        }   
    }
}
