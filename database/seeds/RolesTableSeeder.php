<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('roles')->delete();
        \DB::table('roles')->insert([
            0 => [
                'id' => 1,
                'name' => 'Admin',
                'guard_name' => 'web',
                'created_at' => '2020-04-26 03:06:07',
                'updated_at' => '2020-04-26 03:06:07',
            ]
        ]);
    }
}
