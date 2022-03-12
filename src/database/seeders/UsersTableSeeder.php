<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arrUser = [
            ['Super Admin','super@mail.com'],
            ['User 1','user1@mail.com'],
            ['User 2','user2@mail.com'],
        ];
        foreach ($arrUser as $user) {
            User::create([
                'name' => $user[0],
                'email' => $user[1],
                'point' => 1000,
                'email_verified_at' => time(),
                'password' => bcrypt('password123'),
            ]);
        }
    }
}
