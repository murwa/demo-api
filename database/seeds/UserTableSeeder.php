<?php

use App\Account;
use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class)->times(rand(2, 10))->create()->each(function (User $user) {
            $user->accounts()->saveMany(factory(Account::class)->times(rand(2, 4))->make());
        });
    }
}
