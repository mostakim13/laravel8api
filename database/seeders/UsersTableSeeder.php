<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $users = [
            ['name' => 'Mostakim', 'email' => 'mimrzs2013@gmail.com', 'password' => '123456'],
            ['name' => 'Mim', 'email' => 'mimrzs13@gmail.com', 'password' => '123456'],
            ['name' => 'km', 'email' => 'mimrzs@gmail.com', 'password' => '123456'],
        ];
        User::insert($users);
    }
}