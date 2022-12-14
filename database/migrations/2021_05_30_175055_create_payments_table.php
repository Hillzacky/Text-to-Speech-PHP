<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('plan_type')->comment('prepaid|subscription');
            $table->string('order_id');
            $table->unsignedBigInteger('plan_id');
            $table->float('amount');
            $table->float('discount')->nullable();
            $table->string('currency');
            $table->string('gateway');
            $table->string('status');
            $table->string('plan_name');
            $table->integer('characters');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
