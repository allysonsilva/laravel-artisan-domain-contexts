<?php

namespace App\Domain\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class User3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->line('Run App\Domain\User\Database\Seeders\User3Seeder');

        DB::table('users_3')->insert([
            'name' => 'Name 3_1',
            'email' => 'email3_1@domain.tld',
        ]);

        DB::table('users_3')->insert([
            'name' => 'Name 3_2',
            'email' => 'email3_2@domain.tld',
        ]);

        DB::table('users_3')->insert([
            'name' => 'Name 3_3',
            'email' => 'email3_3@domain.tld',
        ]);
    }
}
