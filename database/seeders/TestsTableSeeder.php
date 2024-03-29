<?php

namespace Database\Seeders;

use App\Models\ExpertTest;
use App\Models\TestCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tests = [];
        $countOfUsers = User::count();
        $countOfExpertTests = ExpertTest::count();

        for ($i = 1; $i <= 50; $i++) {
            $expert_test_id = rand(1, $countOfExpertTests);
            $subDays = rand(1, 30);
            $tests[] = [
                'start_date' => Carbon::now()->subDays($subDays)->toIso8601String(),
                'finish_date' => Carbon::now()
                    ->subDays($subDays)
                    ->addHour()
                    ->toIso8601String(),
                'user_id' => rand(1, $countOfUsers),
                'expert_test_id' => $expert_test_id,
                'test_category_id' => ExpertTest::findOrFail($expert_test_id)->test_category_id
            ];
        }

        DB::table('tests')->insert($tests);
    }
}
