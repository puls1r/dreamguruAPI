<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('course_id');
            $table->foreign('course_id')->references('id')->on('courses');
            $table->unsignedBigInteger('voucher_id')->nullable();
            $table->foreign('voucher_id')->references('id')->on('vouchers');
            $table->string('order_id')->unique()->nullable();
            $table->string('charge_id')->unique()->nullable();
            $table->string('retail_payment_id')->unique()->nullable();
            $table->string('gateway');
            $table->string('payment_type');
            $table->string('bank')->nullable();
            $table->string('status');       //pending, expired, denied, canceled, paid
            $table->integer('amount')->unsigned();
            $table->integer('final_amount')->unsigned();
            $table->dateTime('payment_status_change_date')->nullable();
            $table->dateTime('due_date')->nullable();
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
        Schema::dropIfExists('transactions');
    }
}
