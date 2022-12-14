<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGatewayDataToSubsriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->string('paystack_customer_code')->nullable();
            $table->string('paystack_authorization_code')->nullable();
            $table->string('paystack_email_token')->nullable();
            $table->string('mollie_customer_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('paystack_customer_code');
            $table->dropColumn('paystack_authorization_code');
            $table->dropColumn('paystack_email_token');
            $table->dropColumn('mollie_customer_id');
        });
    }
}
