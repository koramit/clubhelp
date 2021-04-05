<?php

namespace Database\Seeders;

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbilityRoleTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ability_role')->truncate();
        Role::truncate();
        Ability::truncate();

        $now = now()->format('Y-m-d H:i:s');

        $roles = [
            ['created_at' => $now, 'updated_at' => $now, 'name' => 'root'],
            ['created_at' => $now, 'updated_at' => $now, 'name' => 'admin'],
            ['created_at' => $now, 'updated_at' => $now, 'name' => 'resident'],
            ['created_at' => $now, 'updated_at' => $now, 'name' => 'fellow'],
            ['created_at' => $now, 'updated_at' => $now, 'name' => 'attending'],
        ];

        $abilities = [
            ['created_at' => $now, 'updated_at' => $now, 'name' => 'view_any_locations'],
            ['created_at' => $now, 'updated_at' => $now, 'name' => 'view_any_encounters'],
        ];

        Role::insert($roles);
        Ability::insert($abilities);

        $roles = Role::all();
        $abilities = Ability::all();
        foreach ($roles as $role) {
            foreach ($abilities as $ability) {
                $role->allowTo($ability);
            }
        }
    }
}
