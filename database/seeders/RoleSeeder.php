<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamps = Carbon::now()->format('Y-m-d H:i:s');

        $data = [
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => $timestamps,
                'updated_at' => $timestamps,
            ],
            [
                'name' => 'member',
                'guard_name' => 'web',
                'created_at' => $timestamps,
                'updated_at' => $timestamps,
            ],
        ];

        DB::table('roles')->insert($data);
    }
}
