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
        $data = [
            ['title' => 'Веб-програмування', 'parent_id' => null, 'user_id' => 2],
            ['title' => 'JavaScript', 'parent_id' => 1, 'user_id' => 3],
            ['title' => 'HTML5/CSS3', 'parent_id' => 1, 'user_id' => 3],
            ['title' => 'Vue 3', 'parent_id' => 1, 'user_id' => 3],
            ['title' => 'AngularJs', 'parent_id' => 1, 'user_id' => 3],
            ['title' => 'Node.js', 'parent_id' => 1, 'user_id' => 3],
            ['title' => 'React', 'parent_id' => 1, 'user_id' => 3],
            ['title' => 'TypeScript', 'parent_id' => 1, 'user_id' => 3],
            ['title' => 'Основи JavaScript', 'parent_id' => 2, 'user_id' => 4],
            ['title' => 'Масиви', 'parent_id' => 2, 'user_id' => 5],
            ['title' => 'Рядки', 'parent_id' => 2, 'user_id' => 4],
            ['title' => 'ООП', 'parent_id' => 2, 'user_id' => 4],
            ['title' => 'Події', 'parent_id' => 2, 'user_id' => 4],

            ['title' => 'Бази даних', 'parent_id' => null, 'user_id' => 2],
            ['title' => 'Програмування на Python', 'parent_id' => null, 'user_id' => 2],
            ['title' => 'Системне програмування', 'parent_id' => null, 'user_id' => 2],
            ['title' => 'Іноземна мова', 'parent_id' => null, 'user_id' => 2],
            ['title' => 'Дискретна математика', 'parent_id' => null, 'user_id' => 2],
        ];

        $categories = [];
        $expert = Role::findByName('expert');
        foreach (array_column($data, 'user_id') as $user_id) {
            User::find($user_id)->assignRole($expert);
        }

        for ($i = 0; $i < count($data); $i++) {
            $delete_at = !(($i + 1) % 10) ? Carbon::now()->subDays(rand(1, 10)) : null;
            $categories[] = [
                'title' => $data[$i]['title'],
                'parent_id' => $data[$i]['parent_id'],
                'created_at' => Carbon::now()->modify('-1 month'),
                'updated_at' => Carbon::now()->modify('-10 day'),
                'deleted_at' => $delete_at,
                'user_id' => $data[$i]['user_id'],
            ];
        }

        DB::table('test_categories')->insert($categories);
    }
}
