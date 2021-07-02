<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FriendStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('request_status')->insert([
            'status_text' => 'Pending'

        ]);
        DB::table('request_status')->insert([
            'status_text' => 'Friends'

        ]);
    }
}
