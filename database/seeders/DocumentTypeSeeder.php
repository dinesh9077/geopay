<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DocumentType::insert(
			[
				['label' => 'Memorandum Articles of Association', 'name' => 'memorandum_articles_of_association', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['label' => 'Registration of Shareholders', 'name' => 'registration_of_shareholders', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['label' => 'Registration of Directors', 'name' => 'registration_of_directors', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['label' => 'Proof of Address for Shareholders (Utility bill or bank statement)', 'name' => 'proof_of_address_shareholders', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['label' => 'Proof of Address for Directors (Utility bill or bank statement)', 'name' => 'proof_of_address_directors', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['label' => 'Government ID for Shareholders (Passport, Driving License or National ID)', 'name' => 'govt_id_shareholders', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
				['label' => 'Government ID for Directors (Passport, Driving License or National ID)', 'name' => 'govt_id_directors', 'status' => 1, 'created_at' => now(), 'updated_at' => now()], 
			]
		); 
    }
}
