<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'), 
            'xps' => base64_encode('12345678'), 
            'role' => 'admin', 
            'role_id' => 1, 
            'status' => 1, 
            'assign_by' => 1, 
        ]);
    }
}
