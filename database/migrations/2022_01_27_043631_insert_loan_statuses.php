<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InsertLoanStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = Carbon::now()->toDateTimeString();
        DB::table('loan_statuses')->insert([
            [
                'title'         => 'Pending',
                'created_at'    =>  $now,
                'updated_at'    =>  $now,
            ],
            [
                'title'         => 'Approved',
                'created_at'    =>  $now,
                'updated_at'    =>  $now,
            ],
            [
                'title'         => 'In Progress',
                'created_at'    =>  $now,
                'updated_at'    =>  $now,
            ],
            [
                'title'         => 'Completed',
                'created_at'    =>  $now,
                'updated_at'    =>  $now,
            ],
            [
                'title'         => 'Defaulted',
                'created_at'    =>  $now,
                'updated_at'    =>  $now,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('loan_statuses')->truncate();
    }
}
