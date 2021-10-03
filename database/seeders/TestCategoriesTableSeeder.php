<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class TestCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [];
        $countOfUsers = User::count();
        $expert = Role::findByName('expert');

        for ($i = 1; $i <= 50; $i++) {
            $user_id = rand(1, $countOfUsers);
            User::find($user_id)->assignRole($expert);
            $categories[] = [
                'title' => 'Категорія №' . $i,
                'parent_id' => rand(0, $i - 1) ?: null,
                'created_at' => Carbon::now()->modify('-1 month'),
                'updated_at' => Carbon::now()->modify('-10 day'),
                'deleted_at' => !($i % 10) ? Carbon::now()->modify('-5 day') : null,
                'user_id' => $user_id,
            ];
        }

        DB::table('test_categories')->insert($categories);
    }
}
