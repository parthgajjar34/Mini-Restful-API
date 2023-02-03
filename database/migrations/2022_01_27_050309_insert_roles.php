<?php

use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InsertRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = Carbon::now()->toDateTimeString();
        DB::table('roles')->insert([
            [
                'title'         => 'Lender',
                'created_at'    =>  $now,
                'updated_at'    =>  $now,
            ],
            [
                'title'         => 'Customer',
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
        DB::table('roles')->truncate();
    }
}
