<?php

namespace App\Domain\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class User1Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->line('Run App\Domain\User\Database\Seeders\User1Seeder');

        DB::table('users_1')->insert([
            'name' => 'Name 1_1',
            'email' => 'email1_1@domain.tld',
        ]);
    }
}
