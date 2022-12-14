<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReferralTrackingToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_id')->nullable();
            $table->string('referred_by')->nullable();
            $table->string('referral_payment_method')->nullable();
            $table->string('referral_paypal')->nullable();
            $table->text('referral_bank_requisites')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('referral_id');
            $table->dropColumn('referred_by');
            $table->dropColumn('referral_payment_method');
            $table->dropColumn('referral_paypal');
            $table->dropColumn('referral_bank_requisites');
        });
    }
}
