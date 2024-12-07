<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => bcrypt('12345678'),
        ]);

        $user->assignRole('admin');

        $member = User::create([
            'name' => 'Member',
            'email' => 'member@mail.com',
            'password' => bcrypt('12345678'),
        ]);

        $member->assignRole('member');

        $newMember = User::create([
            'name' => 'New Member',
            'email' => 'newmember@mail.com',
            'password' => bcrypt('12345678'),
        ]);

        $newMember->assignRole('member');
    }
}
