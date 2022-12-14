<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Statistics\TTSService;
use App\Services\Statistics\CostsService;
use App\Services\Statistics\PaymentsService;
use App\Services\Statistics\RegistrationService;
use App\Services\Statistics\UserRegistrationMonthlyService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin dashboard
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
        $registration = new RegistrationService($year, $month);
        $user_registration = new UserRegistrationMonthlyService($year, $month);
       
        $total_data_monthly = [
            'free_chars' => $tts->getTotalFreeCharsUsageMonthly(),
            'paid_chars' => $tts->getTotalPaidCharsUsageMonthly(),
            'purchased_chars' => $payment->getTotalPurchasedCharactersCurrentMonth(),
            'audio_files' => $tts->getTotalAudioFilesMonthly(),
            'new_users_current_month' => $registration->getNewUsersCurrentMonth(),
            'new_users_past_month' => $registration->getNewUsersPastMonth(),
            'new_subscribers_current_month' => $registration->getNewSubscribersCurrentMonth(),
            'new_subscribers_past_month' => $registration->getNewSubscribersPastMonth(),
            'income_current_month' => $payment->getTotalPaymentsCurrentMonth(),
            'income_past_month' => $payment->getTotalPaymentsPastMonth(),
            'spending_current_month' => $cost->getTotalCostForTextCurrentMonth(),
            'spending_past_month' => $cost->getTotalCostForTextPastMonth(),
        ];

        $total_data_yearly = [
            'total_new_users' => $registration->getNewUsersCurrentYear(),
            'total_new_subscribers' => $registration->getNewSubscribersCurrentYear(),
            'total_income' => $payment->getTotalPaymentsCurrentYear(),
            'total_spending' => $cost->getTotalCostForTextYearly(),
            'total_purchased_chars' => $payment->getTotalPurchasedCharactersCurrentYear(),
            'total_purchased_chars_used' =>$tts->getTotalPaidCharsUsageYearly()
        ];
        
        $chart_data['total_new_users'] = json_encode($registration->getAllUsers());
        $chart_data['monthly_new_users'] = json_encode($user_registration->getRegisteredUsers());
        $chart_data['total_income'] = json_encode($payment->getPayments());

        $percentage['free_current'] = json_encode($tts->getTotalFreeCharsUsageMonthly());
        $percentage['free_past'] = json_encode($tts->getTotalFreeCharsUsagePastMonth());
        $percentage['paid_current'] = json_encode($tts->getTotalPaidCharsUsageMonthly());
        $percentage['paid_past'] = json_encode($tts->getTotalPaidCharsUsagePastMonth());
        $percentage['purchased_current'] = json_encode($payment->getTotalPurchasedCharactersCurrentMonth());
        $percentage['purchased_past'] = json_encode($payment->getTotalPurchasedCharactersPastMonth());
        $percentage['audio_current'] = json_encode($tts->getTotalAudioFilesMonthly());
        $percentage['audio_past'] = json_encode($tts->getTotalAudioFilesPastMonth());
        $percentage['users_current'] = json_encode($registration->getNewUsersCurrentMonth());
        $percentage['users_past'] = json_encode($registration->getNewUsersPastMonth());
        $percentage['subscribers_current'] = json_encode($registration->getNewSubscribersCurrentMonth());
        $percentage['subscribers_past'] = json_encode($registration->getNewSubscribersPastMonth());
        $percentage['income_current'] = json_encode($payment->getTotalPaymentsCurrentMonth());
        $percentage['income_past'] = json_encode($payment->getTotalPaymentsPastMonth());
        $percentage['spending_current'] = json_encode($cost->getTotalCostForTextCurrentMonth());
        $percentage['spending_past'] = json_encode($cost->getTotalCostForTextPastMonth());

        return view('admin.dashboard.index', compact('chart_data', 'percentage', 'total_data_monthly', 'total_data_yearly'));
    }
}
