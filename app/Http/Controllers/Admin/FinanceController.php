<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\LicenseController;
use Illuminate\Http\Request;
use App\Services\Statistics\PaymentsService;
use App\Services\Statistics\CostsService;
use App\Services\Statistics\TTSService;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\User;
use DataTables;

class FinanceController extends Controller
{
    private $api;

    public function __construct()
    {
        $this->api = new LicenseController();
    }

    /**
     * Display finance dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('m'));

        $tts = new TTSService($year, $month);
        $cost = new CostsService($year, $month);
        $payment = new PaymentsService($year, $month);

        $total_data_monthly = [
            'paid_chars' => $tts->getTotalPaidCharsUsageMonthly(),
            'paid_chars_past' => $tts->getTotalPaidCharsUsagePastMonth(),
            'purchased_chars' => $payment->getTotalPurchasedCharactersCurrentMonth(),
            'purchased_chars_past' => $payment->getTotalPurchasedCharactersPastMonth(),
            'income_current_month' => $payment->getTotalPaymentsCurrentMonth(),
            'income_past_month' => $payment->getTotalPaymentsPastMonth(),
            'spending_current_month' => $cost->getTotalCostForTextCurrentMonth(),
            'spending_past_month' => $cost->getTotalCostForTextPastMonth(),
            'aws_current_month' => $cost->getTotalAWSCostCurrentMonth(),
            'azure_current_month' => $cost->getTotalAzureCostCurrentMonth(),
            'gcp_current_month' => $cost->getTotalGCPCostCurrentMonth(),
            'ibm_current_month' => $cost->getTotalIBMCostCurrentMonth(),
        ];

        $total_data_yearly = [
            'total_income' => $payment->getTotalPaymentsCurrentYear(),
            'total_spending' => $cost->getTotalCostForTextYearly(),
            'total_purchased_chars' => $payment->getTotalPurchasedCharactersCurrentYear(),
            'total_purchased_chars_used' =>$tts->getTotalPaidCharsUsageYearly(),
            'aws_current_year' => $cost->getTotalAWSCostCurrentYear(),
            'azure_current_year' => $cost->getTotalAzureCostCurrentYear(),
            'gcp_current_year' => $cost->getTotalGCPCostCurrentYear(),
            'ibm_current_year' => $cost->getTotalIBMCostCurrentYear(),
            'aws_current_year_std' => $tts->getAWSStandardUsageYearly(),
            'azure_current_year_std' => $tts->getAzureStandardUsageYearly(),
            'gcp_current_year_std' => $tts->getGCPStandardUsageYearly(),
            'aws_current_year_nrl' => $tts->getAWSNeuralUsageYearly(),
            'azure_current_year_nrl' => $tts->getAzureNeuralUsageYearly(),
            'gcp_current_year_nrl' => $tts->getGCPNeuralUsageYearly(),
            'ibm_current_year_nrl' => $tts->getIBMNeuralUsageYearly(),
        ];

        $chart_data['total_income'] = json_encode($payment->getPayments());
        $chart_data['total_spending'] = json_encode($cost->getSpending());
        $chart_data['total_income_year'] = json_encode($payment->getTotalPaymentsCurrentYear());

        $percentage['income_current'] = json_encode($payment->getTotalPaymentsCurrentMonth());
        $percentage['income_past'] = json_encode($payment->getTotalPaymentsPastMonth());
        $percentage['spending_current'] = json_encode($cost->getTotalCostForTextCurrentMonth());
        $percentage['spending_past'] = json_encode($cost->getTotalCostForTextPastMonth());
        $percentage['purchased_current'] = json_encode($payment->getTotalPurchasedCharactersCurrentMonth());
        $percentage['purchased_past'] = json_encode($payment->getTotalPurchasedCharactersPastMonth());
        $percentage['purchased_used_current'] = json_encode($tts->getTotalPaidCharsUsageMonthly());
        $percentage['purchased_used_past'] = json_encode($tts->getTotalPaidCharsUsagePastMonth());

        return view('admin.finance-management.dashboard.index', compact('percentage', 'chart_data', 'total_data_yearly', 'total_data_monthly'));
    }

    
    /**
     * List all user payments
     */
    public function listPayments(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('users.id', 'users.email', 'users.name', 'users.country', 'payments.*')->join('payments', 'payments.user_id', '=', 'users.id')->orderBy('payments.created_at', 'DESC')->get();       
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        if ($row["gateway"] == 'BankTransfer') {
                            $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("admin.finance.payments.show", $row["id"] ). '"><i class="fa fa-file-text"></i> View</a>
                                                <a class="dropdown-item" href="'. route("admin.finance.payments.edit", $row["id"] ). '"><i class="fa fa-pencil-square-o"></i> Update</a>
                                                <a class="dropdown-item" data-toggle="modal" id="deletePaymentButton" data-target="#deleteModal" href="" data-attr="'. route("admin.finance.payments.delete", $row["id"] ). '"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </div>';
                        } else {
                            $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">
                                                <a class="dropdown-item" href="'. route("admin.finance.payments.show", $row["id"] ). '"><i class="fa fa-file-text"></i> View</a>
                                                <a class="dropdown-item" data-toggle="modal" id="deletePaymentButton" data-target="#deleteModal" href="" data-attr="'. route("admin.finance.payments.delete", $row["id"] ). '"><i class="fa fa-trash"></i> Delete</a>
                                            </div>
                                        </div>';
                        }

                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-plan-type', function($row){
                        $plan_type = ($row["plan_type"] == 'subscription') ? 'Subscription' : 'Prepaid';                         
                        
                        $custom_plan = '<span class="cell-box payment-'.strtolower($row["plan_type"]).'">'.$plan_type.'</span>';
                        return $custom_plan;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_priority = '<span class="cell-box payment-'.strtolower($row["status"]).'">'.$row["status"].'</span>';
                        return $custom_priority;
                    })
                    ->addColumn('custom-amount', function($row){
                        $custom_priority = config('payment.default_system_currency_symbol') . $row["amount"];
                        return $custom_priority;
                    })
                    ->addColumn('custom-chars', function($row){
                        $custom_chars = number_format($row["characters"]);
                        return $custom_chars;
                    })
                    ->addColumn('custom-plan-name', function($row){
                        $custom_status = '<span class="font-weight-bold">'.$row["plan_name"].'</span>';
                        return $custom_status;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on', 'custom-plan-type', 'custom-chars', 'custom-amount', 'custom-plan-name'])
                    ->make(true);
                    
        }

        return view('admin.finance-management.payments.index');
    }


    /**
     * List all user subscriptions
     */
    public function listSubscriptions(Request $request)
    {        
        if ($request->ajax()) {
            $data = Subscription::select('subscriptions.*', 'plans.plan_name', 'plans.characters', 'plans.bonus')->join('plans', 'plans.id', '=', 'subscriptions.plan_id')->latest()->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('actions', function($row){
                        $actionBtn = '<div class="dropdown">
                                            <button class="btn table-actions" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>                       
                                            </button>
                                            <div class="dropdown-menu table-actions-dropdown" role="menu" aria-labelledby="actions">                                                
                                                <a class="dropdown-item" data-toggle="modal" id="deleteMyPayment" data-target="#deleteModal" href="" data-attr="'. route("admin.finance.payments.subscriptions.cancel", $row["id"] ). '"><i class="fa fa-times"></i>Cancel Subscription</a>
                                            </div>
                                        </div>';
                        return $actionBtn;
                    })
                    ->addColumn('created-on', function($row){
                        $created_on = '<span>'.date_format($row["created_at"], 'Y-m-d H:i:s').'</span>';
                        return $created_on;
                    })
                    ->addColumn('custom-until', function($row){
                        $custom_until = '<span>'.date_format($row["active_until"], 'd-m-Y').'</span>';
                        return $custom_until;
                    })
                    ->addColumn('custom-user', function($row){
                        $user = User::where('id', $row["user_id"])->firstOrFail();
                        $user_email = '<span>'.$user->email.'</span>';
                        return $user_email;
                    })
                    ->addColumn('custom-status', function($row){
                        $custom_status = '<span class="cell-box subscription-'.strtolower($row["status"]).'">'.$row["status"].'</span>';
                        return $custom_status;
                    })
                    ->addColumn('custom-plan-name', function($row){
                        $custom_status = '<span class="font-weight-bold">'.$row["plan_name"].'</span>';
                        return $custom_status;
                    })
                    ->addColumn('custom-chars', function($row){
                        $custom_chars = number_format($row["characters"]);
                        return $custom_chars;
                    })
                    ->rawColumns(['actions', 'custom-status', 'created-on', 'custom-chars', 'custom-until', 'custom-user', 'custom-plan-name'])
                    ->make(true);
                    
        }

        return view('admin.finance-management.payments.subscriptions');
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $id)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }

        $user = User::where('id', $id->user_id)->first();

        return view('admin.finance-management.payments.show', compact('id', 'user'));     
    }


    /**
     * Edit the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $id)
    {
        $user = User::where('id', $id->user_id)->first();

        return view('admin.finance-management.payments.edit', compact('id', 'user'));     
    }


    /**
     * Update the specified resource - bank transfer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Payment $id)
    {
        if ($this->api->api_url != 'https://license.berkine.space/') {
            return redirect()->back();
        }

        request()->validate([
            'payment-status' => 'required',
        ]);

        $id->status = request('payment-status');
        $id->save();

        if ($id->plan_type == 'subscription') {
            $plan = $id->plan_id;
        } else {
            $plan = null;
        }

        if ($id->status == 'Success') {

            $user = User::where('id', $id->user_id)->first();
            $group = ($user->hasRole('admin'))? 'admin' : 'subscriber';

            $total_chars = $user->available_chars + $id->characters;
            
            $user->syncRoles($group);    
            $user->group = $group;
            $user->plan_id = $plan;
            $user->available_chars = $total_chars;
            $user->save();   

            if ($id->plan_type == 'subscription') {
                
                $subscription = Subscription::where('subscription_id', $id->order_id)->firstOrFail();
                $subscription->status = 'Active';
                $subscription->save();

                $user->plan_id = $subscription->plan_id;
                $user->save();
            }

        }

        return redirect()->route('admin.finance.payments')->with('success', 'Bank Transfer payment has been updated successfully');     
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

        if($payment) {
            $payment->delete();
        }
    
        return redirect()->route('admin.finance.payments')->with('success', 'Payment was deleted successfully');          
    }


    public function delete($id)
    {   
        $payment = Payment::where('id', $id)->firstOrFail();

        return view('admin.finance-management.payments.delete', compact('payment'));  
    }


    /**
     * Cancel user subscription confirmation
     *
     */
    public function cancelSubscription(Subscription $id)
    {   
        if ($this->api->api_url != 'https://license.berkine.space/') {
            die();
        }
        
        return view('admin.finance-management.payments.cancel', compact('id')); 
    }
}
