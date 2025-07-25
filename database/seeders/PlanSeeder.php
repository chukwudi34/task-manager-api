<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = array(
            ['name' => 'Free', 'status' => 'active', 'description' => 'This Plan is meant for free users'],
            ['name' => 'Pro', 'status' => 'active', 'description' => 'This Plan is meant for Pro Users.'],
        );
        foreach ($categories as $cat) {
            $user = Plan::updateOrCreate($cat);
        }
    }
}
