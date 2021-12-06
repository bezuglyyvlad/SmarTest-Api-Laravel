<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('passport:install');
        User::factory(100)->create();
        $this->call([
            SpatieSeeder::class,
            TestCategoriesTableSeeder::class,
            ExpertTestsTableSeeder::class,
            QuestionsTableSeeder::class,
            AnswersTableSeeder::class,
            TestProcessSeeder::class
//            TestResultsTableSeeder::class,
//            TestsTableSeeder::class,
        ]);
    }
}
