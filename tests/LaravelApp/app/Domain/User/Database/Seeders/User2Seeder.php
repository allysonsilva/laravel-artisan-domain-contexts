<?php

namespace App\Domain\User\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class User2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->line('Run App\Domain\User\Database\Seeders\User2Seeder');

        DB::table('users_2')->insert([
            'name' => 'Name 2_1',
            'email' => 'email2_1@domain.tld',
        ]);

        DB::table('users_2')->insert([
            'name' => 'Name 2_2',
            'email' => 'email2_2@domain.tld',
        ]);
    }
}
