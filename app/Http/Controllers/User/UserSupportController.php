<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mailers\AppMailer;
use App\Models\Support;
use DataTables;


class UserSupportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        if ($request->ajax()) {
            $data = Support::where('user_id', Auth::user()->id)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div>
                                        <a href="'. route("user.support.show", $row["ticket_id"] ). '"><i class="fa-solid fa-message-question voice-action-buttons rename-voice" title="View Support Ticket"></i></a>
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
                    ->addColumn('clear-subject', function($row){
                        $custom_priority = '<span class="cell-box priority-'.strtolower($row["priority"]).'">{!! '.$row["priority"].'!!}</span>';
                        return $custom_priority;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on', 'custom-priority'])
                    ->make(true);
                    
        }

        return view('user.support.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        return view('user.support.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AppMailer $mailer)
    {   
        request()->validate([
            'subject' => 'required|string',
            'message' => 'required|string',
            'priority' => 'required|string',
            'category' => 'required|string',
        ]);

        $ticket = new Support([
            'subject' => htmlspecialchars(request('subject')),
            'message' => htmlspecialchars(request('message')),
            'priority' => htmlspecialchars(request('priority')),
            'category' => htmlspecialchars(request('category')),
            'status' => 'Open',
            'user_id' => Auth::user()->id,
            'ticket_id' => strtoupper(Str::random(10)),
        ]); 
               
        $ticket->save();
        
        if (config('settings.support_email') == 'enabled') {
			$mailer->sendSupportTicketInformation(Auth::user(), $ticket);
		}
        

        return redirect()->route('user.support')->with("success", "Support ticket with ID: #$ticket->ticket_id has been opened");
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

        if ($ticket->user_id == Auth::user()->id){

            return view('user.support.show', compact('ticket'));     

        } else{
            return redirect()->route('user.support');
        }        
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

            if ($ticket->user_id == Auth::user()->id){

                $ticket->delete();

                return response()->json('success');   

            } else{
                return response()->json('error');
            } 
        } 
    }
}
