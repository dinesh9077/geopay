<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\BusinessType;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		BusinessType::insert(
			[
				['business_type' => 'Sole Proprietorship', 'is_director' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['business_type' => 'General Partnership', 'is_director' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['business_type' => 'Limited Liability Partnership (LLP)', 'is_director' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['business_type' => 'Private Limited Company (Pvt. Ltd.)', 'is_director' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['business_type' => 'Public Limited Company (PLC)', 'is_director' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['business_type' => 'One Person Company (OPC)', 'is_director' => 0, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['business_type' => 'Non-Profit Organization (NGO)', 'is_director' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['business_type' => 'Limited Partnership (LP)', 'is_director' => 1, 'status' => 1, 'created_at' => now(), 'updated_at' => now()]
			]
		); 
    }
}
