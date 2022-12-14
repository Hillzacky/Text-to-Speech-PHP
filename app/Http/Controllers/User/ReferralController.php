<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Events\PayoutRequested;
use App\Models\Setting;
use App\Models\Referral;
use App\Models\Payout;
use App\Models\User;
use DataTables;
use DB;
use Exception;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $referral_information = ['referral_headline', 'referral_guideline'];
        $referral = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $referral_information)) {
                $referral[$row['name']] = $row['value'];
            }
        }

        $total_credits = Referral::select(DB::raw("sum(credits) as data"))->where('referrer_id', auth()->user()->id)->get();
        $total_commission = Referral::select(DB::raw("sum(commission) as data"))->where('referrer_id', auth()->user()->id)->get();

        return view('user.balance.referrals.index', compact('referral', 'total_credits', 'total_commission'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function gateway()
    {
        $user = User::where('id', auth()->user()->id)->first();

        return view('user.balance.referrals.gateway.index', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function gatewayStore(Request $request)
    {
        request()->validate([
            'payment_method' => 'required',
        ]);

        $user = User::where('id', auth()->user()->id)->first();   
        $user->referral_payment_method = request('payment_method');
        $user->referral_paypal = request('paypal');
        $user->referral_bank_requisites = request('bank_requisites');
        $user->save();

        return redirect()->back()->with('success', 'Payment Gateway settings were successfully saved');
    }


    /**
     * Send a inviation email
     */
    public function email(Request $request)
    {   
        try {
            Mail::send(array(), array(), function ($message) {
                $message->from(config('mail.from.address'), auth()->user()->name);
                $message->replyTo(auth()->user()->email, auth()->user()->name);
                $message->to(request('email'));
                $message->subject(request('subject'));
                $message->setBody(request('message'));
            });
    
            if (Mail::failures()) {
                return redirect()->back()->with('error', 'There was an error while sending email');
            }
        
        } catch(Exception $e) {
            return redirect()->back()->with('error', 'SMTP settings are not configured correctly yet. ' . $e->getMessage());
        }     
        

        return redirect()->back()->with('success', 'Email was sent successfully');
    }


    /**
     * List user payout requests.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payouts(Request $request)
    {
        if ($request->ajax()) {
            $data = Payout::where('user_id', auth()->user()->id)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("user.referral.payout.show", $row["id"] ). '"><i class="fa fa-file-text"></i> View</a>                                              
                                                <a class="dropdown-item" data-toggle="modal" id="deletePayoutButton" data-target="#deletePayoutModal" href="" data-attr="'. route("user.referral.payout.cancel", $row["id"] ). '"><i class="fa fa-close"></i> Cancel</a>                                              
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box payout-'.$row["status"].'">'.ucfirst($row["status"]).'</span>';
                        return $custom_status;
                    })
                    ->addColumn('custom-total', function($row){
                        $custom_status = config('payment.default_system_currency_symbol') . $row["total"];
                        return $custom_status;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->rawColumns(['created-on', 'actions', 'custom-status', 'custom-total'])
                    ->make(true);
                    
        }

        return view('user.balance.referrals.payouts.index');
    }


    /**
     * Create payout request.
     *
     * @return \Illuminate\Http\Response
     */
    public function payoutsCreate()
    {
        return view('user.balance.referrals.payouts.create');
    }


    /**
     * Create payout request.
     *
     * @return \Illuminate\Http\Response
     */
    public function payoutsStore(Request $request)
    {        
        $request->validate([
            'payout' => 'required|numeric',
        ]);

        if ($request->payout > auth()->user()->balance) {
            return redirect()->back()->with('error', 'Requested amount is more than your current balance.');
        }

        if ($request->payout < config('payment.referral.payment.threshold')) {
            return redirect()->back()->with('error', 'Requested payout amount is less than minimum payout threshold.');
        }        

        if (auth()->user()->referral_payment_method == '') {
            return redirect()->back()->with('error', 'You will need to set payment method first.');
        }

        $user = User::where('id', auth()->user()->id)->firstOrFail();   
        $user->balance = ($user->balance - $request->payout);
        $user->save();

        Payout::create([
            'request_id' => strtoupper(Str::random(15)),
            'user_id' => auth()->user()->id,
            'total' => $request->payout,
            'gateway' => auth()->user()->referral_payment_method,
            'status' => 'processing',
        ]);       

        event(new PayoutRequested($user));
     
        return redirect()->route('user.referral.payout')->with('success', 'Your request for payout has been created successfully.');
    }


    /**
     * Show payout request.
     *
     * @return \Illuminate\Http\Response
     */
    public function payoutsShow(Payout $id)
    {
        if ($id->user_id != auth()->user()->id) {
            return view('user.balance.referrals.payouts.index');
        }

        return view('user.balance.referrals.payouts.show', compact('id'));
    }


    /**
     * Cancel payout request.
     *
     * @return \Illuminate\Http\Response
     */
    public function payoutsCancel(Payout $id)
    {
        if ($id->user_id != auth()->user()->id) {
            return view('user.balance.referrals.payouts.index');
        }

        return view('user.balance.referrals.payouts.delete', compact('id'));
    }


    /**
     * Decline payout request.
     *
     * @return \Illuminate\Http\Response
     */
    public function payoutsDecline(Payout $id)
    {
        if ($id->status == 'completed') {
            return redirect()->back()->with('error', 'Requested payout has been processed and cannot be cancelled.');
        }

        if ($id->status == 'declined') {
            return redirect()->back()->with('error', 'Requested payout has been declined by admin and cannot be cancelled.');
        }

        if ($id->status == 'cancelled') {
            return redirect()->back()->with('error', 'Requested payout has already been cancelled.');
        }

        Payout::where('id', $id->id)->update(['status' => 'cancelled']);

        $user = User::where('id', $id->user_id)->firstOrFail();   
        $user->balance = ($user->balance + $id->total);
        $user->save();

        return redirect()->back()->with('success', 'Selected payout request has been cancelled successfully.');
    }


    /**
     * Show all payment referrals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function referrals(Request $request)
    {
        if ($request->ajax()) {
            $data = Referral::whereNotNull('order_id')->where('referrer_id', auth()->user()->id)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-rate', function($row){
                        $created_on = '<span>'.$row["rate"].'%</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-payment', function($row){
                        $custom_status = config('payment.default_system_currency_symbol') . $row["payment"];
                        return $custom_status;
                    })
                    ->addColumn('custom-commission', function($row){
                        $custom_status = config('payment.default_system_currency_symbol') . $row["commission"];
                        return $custom_status;
                    })
                    ->rawColumns(['created-on', 'custom-rate', 'custom-payment', 'custom-commission'])
                    ->make(true);
                    
        }

        $total_users = Referral::select(DB::raw("count(DISTINCT referred_id) as data"))->where('referrer_id', auth()->user()->id)->get();
        $total_commission = Referral::select(DB::raw("sum(commission) as data"))->where('referrer_id', auth()->user()->id)->get();

        return view('user.balance.referrals.referrals.index', compact('total_users', 'total_commission'));
    }

}
