<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\Statistics\UserUsageYearlyService;
use App\Services\Statistics\UserPaymentsService;
use App\Models\Subscription;
use App\Models\Payment;
use DataTables;

class BalanceController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $verify = $this->api->verify_license();

        if($verify['status']!=true){
            return false;
        }
        
        $year = $request->input('year', date('Y'));

        $payment = new UserPaymentsService($year);
        $usage = new UserUsageYearlyService($year);

        $total_data = [
            'total_payment' => $payment->getTotalPayments(),
            'total_purchased_chars' => $payment->getTotalPurchasedCharacters(),
            'total_chars_used' =>$usage->getAllCharsUsage()
        ];

        $chart_data['total_payments'] = json_encode($payment->getPayments());

        return view('user.balance.dashboard.index', compact('chart_data','total_data'));
    }


    /**
     * List all user payments
     */
    public function listPayments(Request $request)
    {   
        if ($request->ajax()) {
            $data = Payment::where('user_id', Auth::user()->id)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("user.balance.payments.show", $row["id"] ). '"><i class="fa fa-file-text"></i> View</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-plan-type', function($row){
                        $plan_type = ($row["plan_type"] == 'subscription') ? 'Subscription' : 'Pre Paid';                          
                        
                        $custom_plan = '<span class="cell-box payment-'.strtolower($row["plan_type"]).'">'.$plan_type.'</span>';
                        return $custom_plan;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box payment-'.strtolower($row["status"]).'">'.$row["status"].'</span>';
                        return $custom_status;
                    })
                    ->addColumn('custom-amount', function($row){
                        $custom_group = $row["amount"] . ' ' . $row["currency"];
                        return $custom_group;
                    })
                    ->addColumn('custom-chars', function($row){
                        $custom_chars = number_format($row["characters"]);
                        return $custom_chars;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on', 'custom-plan-type', 'custom-chars', 'custom-amount'])
                    ->make(true);
                    
        }

        return view('user.balance.payments.index');
    }


    /**
     * List user susbsriptions
     */
    public function listSubscriptions(Request $request)
    {        
        if ($request->ajax()) {
            $data = Subscription::select('subscriptions.*', 'plans.plan_name', 'plans.characters', 'plans.bonus')->join('plans', 'plans.id', '=', 'subscriptions.plan_id')->where('subscriptions.user_id', Auth::user()->id)->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">                                                
                                                <a class="dropdown-item" data-toggle="modal" id="deleteMyPayment" data-target="#deleteModal" href="" data-attr="'. route("user.balance.subscriptions.cancel", $row["id"] ). '"><i class="fa fa-times"></i>Cancel Subscription</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-until', function($row){
                        $created_on = '<span>'.($row["active_until"]).'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box subscription-'.strtolower($row["status"]).'">'.$row["status"].'</span>';
                        return $custom_status;
                    })
                    ->addColumn('custom-chars', function($row){
                        $custom_chars = number_format($row["characters"]);
                        return $custom_chars;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on', 'custom-chars', 'custom-until'])
                    ->make(true);
                    
        }

        return view('user.balance.payments.subscriptions');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $id)
    {
        if ($id->user_id == Auth::user()->id){

            return view('user.balance.payments.show', compact('id'));     

        } else{
            return redirect()->route('user.balance.payments');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = Payment::where('id', $id)->firstOrFail();

        if ($payment->user_id == Auth::user()->id){

            if($payment) {
                $payment->delete();
            }
    
            return redirect()->route('user.balance.payments')->with('success', 'Payment was deleted successfully');  
        
        } else{
            return redirect()->route('user.balance.payments');
        }
    }

    public function delete($id)
    {   
        $payment = Payment::where('id', $id)->firstOrFail();

         if ($payment->user_id == Auth::user()->id){

            return view('user.balance.payments.delete', compact('payment')); 

        } else{
            return redirect()->route('user.balance.payments');
        }
    }


    /**
     * Cancel user subscription
     * 
     */
    public function cancelSubscription(Subscription $id)
    {   
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }
        
         if ($id->user_id == Auth::user()->id){

            return view('user.balance.subscriptions.cancel', compact('id'));                        

        } else{
            return redirect()->route('user.balance.subscriptions');
        }
    }
}
