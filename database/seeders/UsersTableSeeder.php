<?php

namespace Database\Seeders;

use App\Models\User;
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
        $divisions = ['Allergy', 'Ambulatory', 'Cardiology', 'Chest', 'Critical Care', 'Endocrinology', 'Gastroenterology', 'Gen Med', 'Genetics', 'Geriatric', 'Hematology', 'Hypertension', 'Infectious', 'Nephrology', 'Neurology', 'Nutrition', 'Oncology', 'Rheumatology'];
        foreach ($divisions as $division) {
            $users = User::factory()->count(5)->create();
            foreach ($users as $user) {
                $user->assignDivision(strtolower($division));
            }
        }
    }
}
