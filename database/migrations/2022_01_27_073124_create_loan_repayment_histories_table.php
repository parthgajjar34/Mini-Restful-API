<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanRepaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_repayment_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('loan_application_id')->unsigned();
            $table->float('amount', 6);
            $table->dateTime('payment_date');
            $table->bigInteger('transaction_id')->nullable();
            $table->boolean('payment_status')->default(0)->comment('0 => unpaid, 1 => paid');
            $table->timestamps();

            $table->foreign('loan_application_id')
                ->references('id')
                ->on('loan_applications')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_repayment_histories');
    }
}
