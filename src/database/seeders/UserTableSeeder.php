<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => '高山朝陽',
            'email' => 'asahi.1029@icloud.com',
            'password' => Hash::make('H1410290h'),
            'is_admin' => true,
        ];
        DB::table('users')->insert($param);

        $param = [
            'name' => '高山晴輝',
            'email' => 'haruki.1208@icloud.com',
            'password' => Hash::make('H1212080h'),
        ];
        DB::table('users')->insert($param);
    }
}
