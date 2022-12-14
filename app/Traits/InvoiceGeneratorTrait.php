<?php

namespace App\Traits;

use Konekt\PdfInvoice\InvoicePrinter;
use App\Http\Controllers\User\PromocodeController;
use App\Models\Setting;
use App\Models\Payment;
use App\Models\PrepaidPlan;
use App\Models\Plan;
use App\Models\User;

trait InvoiceGeneratorTrait
{
    /**
     * Handle and create invoice after each payment or for past payments
     * 
     */
    public function generateInvoice($order_id, $discount = false)
    {   
        $invoice_rows = ['invoice_currency', 'invoice_language', 'invoice_vendor', 'invoice_vendor_website', 'invoice_address', 'invoice_city', 'invoice_state', 'invoice_postal_code', 'invoice_country', 'invoice_phone', 'invoice_vat_number'];
        $invoice = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $invoice_rows)) {
                $invoice[$row['name']] = $row['value'];
            }
        }

        $promocode = new PromocodeController();
        $payment = Payment::where('order_id', $order_id)->firstOrFail();
        $user = User::where('id', $payment->user_id)->firstOrFail();
        $plan = ($payment->plan_type == 'prepaid') ? PrepaidPlan::where('id', $payment->plan_id)->first() : Plan::where('id', $payment->plan_id)->first();
        $description = 'Total included characters: ' . ($plan->characters + $plan->bonus);
        $discount = ($discount) ? $discount : 0;

        $tax_total = (config('payment.payment_tax') > 0) ? $tax = ($plan->cost * config('payment.payment_tax')) / 100 : 0;
        $total = $tax_total + $plan->cost;

        $discounted_price = ($discount) ? $promocode->calculatePromocode($discount, $total) : 0;
        $discount_value = ($discount) ? $total - $discounted_price : 0;

        $final_total = ($discounted_price) ? $discounted_price : $total;

        $serviceProvider = [
            'Service Provider',
            $invoice['invoice_vendor'],
            $invoice['invoice_vendor_website'],
            $invoice['invoice_address'],
            $invoice['invoice_city'] . ', ' . $invoice['invoice_postal_code'] . ', ' . $invoice['invoice_country'],
            $invoice['invoice_phone'],
            'VAT Number: ' . $invoice['invoice_vat_number'],
        ];

        $serviceUser = [
            'Service User',
            $user->name,
            $user->email,
            $user->company,
            $user->address,
            $user->city . ' ' . $user->postal_code . ' ' . $user->country,
        ];

        $size = 'A4';
        $currency = $invoice['invoice_currency'];
        $language = $invoice['invoice_language'];

        $invoice = new InvoicePrinter($size, $currency, $language);
        
        /* Header settings */
        $invoice->setLogo("img/brand/logo.png");
        $invoice->setColor("#007Bff");      // pdf color scheme
        $invoice->setType("Invoice");    // Invoice Type
        $invoice->setReference($order_id);   // Reference
        $invoice->setDate(date('M dS ,Y',time()));   //Billing Date
        $invoice->setTime(date('h:i:s A',time()));   //Billing Time
        $invoice->setFrom($serviceProvider);
        $invoice->setTo($serviceUser);
        
        $invoice->addItem('Plan Name: '. $plan->plan_name, $description, 1, $tax_total, $plan->cost, $discount_value, $total);
        
        $invoice->addTotal("Total", $total);
        $invoice->addTotal("VAT ". config('payment.payment_tax') ."%", $tax_total);
        $invoice->addTotal("Total Paid", $final_total, true);
        
        $invoice->addBadge("Payment Paid");        
        $invoice->addTitle("Important Notice");        
        $invoice->addParagraph("All subscription cancellations will be processed by the next month.");        
        $invoice->setFooternote(config('payment.company.name'),);
        
        $invoice->render('invoice.pdf','D'); 
    }


    public function bankTransferInvoice($order_id)
    {   
        $invoice_rows = ['bank_requisites'];
        $invoice = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $invoice_rows)) {
                $invoice[$row['name']] = $row['value'];
            }
        }

        $id = Payment::where('order_id', $order_id)->firstOrFail();
        $user = User::where('id', $id->user_id)->firstOrFail();
        $plan = ($id->plan_type == 'prepaid') ? PrepaidPlan::where('id', $id->plan_id)->first() : Plan::where('id', $id->plan_id)->first();
        $description = 'Total included characters: ' . ($plan->characters + $plan->bonus);

        $tax_total = (config('payment.payment_tax') > 0) ? $tax = ($plan->cost * config('payment.payment_tax')) / 100 : 0;
        $total = $tax_total + $plan->cost;

        $serviceProvider = [
            'Bank Requisites',
            $invoice['bank_requisites'],
        ];

        $size = 'A4';
        $currency = $id->currency;
        $language = 'en';

        $invoice = new InvoicePrinter($size, $currency, $language);
        
        /* Header settings */
        $invoice->setLogo("img/brand/logo.png");
        $invoice->setColor("#007Bff");      // pdf color scheme
        $invoice->setType("Invoice");    // Invoice Type
        $invoice->setReference($id->order_id);   // Reference
        $invoice->setDate(date('M dS ,Y',time()));   //Billing Date
        $invoice->setTime(date('h:i:s A',time()));   //Billing Time
        $invoice->setFrom($serviceProvider);
        $invoice->hideToFromHeaders();
        
        $invoice->addItem('Plan Name: '. $plan->plan_name, $description, 1, $tax_total, $plan->cost, $id->discount, $total);
        
        $invoice->addTotal("Total", $total);
        $invoice->addTotal("VAT ". config('payment.payment_tax') ."%", $tax_total);
        $invoice->addTotal("Total Due", $id->amount, true);
        
        $invoice->addBadge("Payment Pending", '#f00');        
        $invoice->addTitle("Important Notice");        
        $invoice->addParagraph("All subscription cancellations will be processed by the next month.");        
        $invoice->setFooternote(config('payment.company.name'),);
        
        $invoice->render('invoice.pdf','D'); 
    }


    public function showInvoice(Payment $id)
    {   
        $invoice_rows = ['invoice_currency', 'invoice_language', 'invoice_vendor', 'invoice_vendor_website', 'invoice_address', 'invoice_city', 'invoice_state', 'invoice_postal_code', 'invoice_country', 'invoice_phone', 'invoice_vat_number'];
        $invoice = [];
        $settings = Setting::all();

        foreach ($settings as $row) {
            if (in_array($row['name'], $invoice_rows)) {
                $invoice[$row['name']] = $row['value'];
            }
        }

        $user = User::where('id', $id->user_id)->firstOrFail();
        $plan = ($id->plan_type == 'prepaid') ? PrepaidPlan::where('id', $id->plan_id)->first() : Plan::where('id', $id->plan_id)->first();
        $description = 'Total included characters: ' . ($plan->characters + $plan->bonus);

        $tax_total = (config('payment.payment_tax') > 0) ? $tax = ($plan->cost * config('payment.payment_tax')) / 100 : 0;
        $total = $tax_total + $plan->cost;

        $serviceProvider = [
            'Service Provider',
            $invoice['invoice_vendor'],
            $invoice['invoice_vendor_website'],
            $invoice['invoice_address'],
            $invoice['invoice_city'] . ', ' . $invoice['invoice_postal_code'] . ', ' . $invoice['invoice_country'],
            $invoice['invoice_phone'],
            'VAT Number: ' . $invoice['invoice_vat_number'],
        ];

        $serviceUser = [
            'Service User',
            $user->name,
            $user->email,
            $user->company,
            $user->address,
            $user->city . ' ' . $user->postal_code . ' ' . $user->country,
        ];

        $size = 'A4';
        $currency = $id->currency;
        $language = $invoice['invoice_language'];

        $invoice = new InvoicePrinter($size, $currency, $language);
        
        /* Header settings */
        $invoice->setLogo("img/brand/logo.png");
        $invoice->setColor("#007Bff");      // pdf color scheme
        $invoice->setType("Invoice");    // Invoice Type
        $invoice->setReference($id->order_id);   // Reference
        $invoice->setDate(date('M dS ,Y',time()));   //Billing Date
        $invoice->setTime(date('h:i:s A',time()));   //Billing Time
        $invoice->setFrom($serviceProvider);
        $invoice->setTo($serviceUser);
        
        $invoice->addItem('Plan Name: '. $plan->plan_name, $description, 1, $tax_total, $plan->cost, $id->discount, $total);
        
        $invoice->addTotal("Total", $total);
        $invoice->addTotal("VAT ". config('payment.payment_tax') ."%", $tax_total);
        $invoice->addTotal("Total Paid", $id->amount, true);
        
        $invoice->addBadge("Payment Paid");        
        $invoice->addTitle("Important Notice");        
        $invoice->addParagraph("All subscription cancellations will be processed by the next month.");        
        $invoice->setFooternote(config('payment.company.name'),);
        
        $invoice->render('invoice.pdf','D'); 
    }
}