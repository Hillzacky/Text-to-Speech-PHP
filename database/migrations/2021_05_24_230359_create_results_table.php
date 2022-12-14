<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('language');
            $table->string('voice');
            $table->string('voice_id')->nullable();
            $table->string('gender')->nullable();
            $table->longText('text')->nullable();
            $table->longText('text_raw')->nullable();
            $table->string('file_name')->nullable();
            $table->string('result_url')->nullable();
            $table->string('result_ext')->nullable();
            $table->string('storage')->nullable()->comment('local|s3');
            $table->string('title')->nullable();
            $table->string('vendor_img')->nullable();
            $table->string('vendor')->comment('aws|azure|gcp|ibm');
            $table->string('vendor_id')->comment('aws_std|aws_nrl|azure_std|azure_nrl|gcp_std|gcp_nrl|ibm_nrl');
            $table->integer('characters')->nullable();;
            $table->string('voice_type')->comment('standard|neural');
            $table->string('plan_type')->comment('free|paid');
            $table->string('audio_type')->nullable();
            $table->string('mode')->nullable();
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
        Schema::dropIfExists('results');
    }
}
