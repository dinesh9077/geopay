<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserLimit;

class UserLimitSeeder extends Seeder
{
    /**
     * Run the database seeds.
    */
    public function run()
    {
        $userLimits = [
            ['name' => 'Basic', 'daily_add_limit' => 10, 'daily_pay_limit' => 1000],
            ['name' => 'Standard', 'daily_add_limit' => 50, 'daily_pay_limit' => 5000],
            ['name' => 'Premium', 'daily_add_limit' => 100, 'daily_pay_limit' => 10000],
        ];

        foreach ($userLimits as $userLimit) {
            UserLimit::updateOrCreate(['name' => $userLimit['name']], $userLimit);
        }
    }
}
