<?php

namespace Database\Seeders;

use App\Models\ExpertTest;
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
            $expert_test_time = ExpertTest::where('id', $expert_test_id)->first()->time;
            $floorDays = rand(1, 30);
            $tests[] = [
                'start_date' => Carbon::now()->floorDays($floorDays),
                'finish_date' => Carbon::now()->floorDays($floorDays)->addMinutes((int)($expert_test_time * 0.8)),
                'user_id' => rand(1, $countOfUsers),
                'expert_test_id' => $expert_test_id
            ];
        }

        DB::table('tests')->insert($tests);
    }
}
