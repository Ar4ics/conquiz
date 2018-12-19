<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //factory(User::class, 5)->create();
        $faker = Faker\Factory::create();
        DB::table('users')->insert([
            'name' => 'Person 1',
            'email' => 'p1@mail.com',
            'password' => bcrypt('secret'),
        ]);
        DB::table('users')->insert([
            'name' => 'Person 2',
            'email' => 'p2@mail.com',
            'password' => bcrypt('secret'),
        ]);
        DB::table('users')->insert([
            'name' => 'Person 3',
            'email' => 'p3@mail.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
