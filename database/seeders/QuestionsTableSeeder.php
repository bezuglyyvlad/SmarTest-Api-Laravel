<?php

namespace Database\Seeders;

use App\Models\ExpertTest;
use App\Models\Question;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'text' => 'Що станеться в результаті виконання наступного коду:',
                'quality_coef' => 1,
                'type' => 1,
                'description' => 'Код виконається без помилок. Перший alert виведе повідомлення "undefined", а другий - "5"',
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що виведе цей код?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чи є різниця між виразами? !!( a && b ) и (a && b)',
                'quality_coef' => 2,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Яке з цих слів не має спеціального використання в JavaScript, ніяк не згадується в стандарті?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' =>
                    'Які символи коректно знайдуть максимальне значення в непорожньому масиві arr?',
                'quality_coef' => 3,
                'type' => 2,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює 2 && 1 && null && 0 && undefined?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => '&& повертає перший false, а null - false',
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює a + b + c?',
                'quality_coef' => 2,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Який результат буде у вираза? null + {0:1}[0] + [,[1],][1][0]',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює typeof null в режимі use strict?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що буде виведено на екран таким кодом?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => "Створили об'єкт а, так як це масив то у нього є атрибут length проініціалізіроаний нулем a.length = 0, у 2-му рядку елементу масиву з індексом 0 привласнюють значення 0, в масиві 1 елемент, відповідно a.length = 1.",
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Скільки параметрів можна передати функції?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Які з цих викликів синтаксично вірно згенерують виняток?',
                'quality_coef' => 1,
                'type' => 2,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що виведе alert?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює значення виразу 4 - "5" + 0xf - "1e1"?',
                'quality_coef' => 3,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чи спрацює виклик функції до оголошення в цьому коді:',
                'quality_coef' => 2,
                'type' => 1,
                'description' => null,
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що робить оператор **?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => false,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Який оператор із цих виконує не лише математичні операції?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => false,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Які виклики parseInt повернуть число?',
                'quality_coef' => 1,
                'type' => 2,
                'description' => null,
                'image' => false,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнюватиме this?',
                'quality_coef' => 2,
                'type' => 1,
                'description' => null,
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що виведе код?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Мова JavaScript є підвидом мови Java - вірно?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => false,
                'expert_test_id' => 5
            ],
            [
                'text' => "Після виконання цього коду - у яких об'єктах зі списку міститься властивість name?",
                'quality_coef' => 3,
                'type' => 2,
                'description' => null,
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що таке ECMAScript?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => false,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що виведе код?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чи є різниця між викликами i++ та ++i?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => false,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює довжина arr.length масиву arr?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => true,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Які конструкції для циклів є в JavaScript?',
                'quality_coef' => 1,
                'type' => 1,
                'description' => null,
                'image' => false,
                'expert_test_id' => 5
            ],
        ];

        $questions = [];
        $easyCoef = [
            0.55, 0.6833333333333332, 0.7035714285714285, 0.75, 0.75625,
            0.77, 0.775, 0.7833333333333333, 0.7937500000000001, 0.825
        ];
        $medCoef = [
            0.8968750000000001, 0.9285714285714286, 0.953125, 0.96875, 1.035,
            1.0499999999999998, 1.0805555555555555, 1.1062499999999997,
            1.1305555555555555
        ];
        $hardCoef = [
            1.1749999999999998, 1.2349999999999999, 1.2499999999999998,
            1.265, 1.2749999999999997, 1.2964285714285713, 1.2999999999999998,
            1.3166666666666664, 1.4249999999999998, 1.4499999999999997
        ];


        for ($i = 0; $i < count($data); $i++) {
            $quality_coef = $easyCoef[array_rand($easyCoef)];
            if ($data[$i]['quality_coef'] === 2) {
                $quality_coef = $medCoef[array_rand($medCoef)];
            } elseif ($data[$i]['quality_coef'] === 3) {
                $quality_coef = $hardCoef[array_rand($hardCoef)];
            }
            $questions[] = [
                'text' => $data[$i]['text'],
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'quality_coef' => $quality_coef,
                'type' => $data[$i]['type'],
                'description' => $data[$i]['description'],
                'created_at' => Carbon::now()->subMonth(),
                'updated_at' => Carbon::now()->subDays(rand(10, 30)),
                'expert_test_id' => $data[$i]['expert_test_id'],
            ];
        }

        DB::table('questions')->insert($questions);
    }
}
