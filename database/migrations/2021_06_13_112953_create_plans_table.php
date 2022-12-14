<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('paypal_gateway_plan_id')->nullable();
            $table->string('stripe_gateway_plan_id')->nullable();
            $table->string('plan_type')->comment('subscription');
            $table->string('plan_name')->unique();
            $table->decimal('cost', 15, 2)->unsigned();
            $table->string('currency')->default('USD');
            $table->string('status')->default('active')->comment('active|closed');
            $table->integer('characters');
            $table->integer('bonus')->nullable()->default(0);
            $table->string('primary_heading')->nullable();
            $table->string('secondary_heading')->nullable();
            $table->longText('plan_features')->nullable();
            $table->integer('duration_in_days')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
