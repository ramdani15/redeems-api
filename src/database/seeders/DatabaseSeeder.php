<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UsersTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        \App\Models\Gift::factory(10)->create();
        // \App\Models\GiftLike::factory(10)->create();
        // \App\Models\GiftRating::factory(10)->create();
    }
}
