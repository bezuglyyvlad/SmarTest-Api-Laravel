<?php

namespace Database\Seeders;

use App\Models\Question;
use Carbon\Carbon;
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
                'complexity' => 0,
                'significance' => 2,
                'relevance' => 1,
                'type' => 1,
                'description' => 'Код виконається без помилок. Перший alert виведе повідомлення "undefined", а другий - "5"',
                'image' => 'lock1.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що виведе цей код?',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => 'lock2.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чи є різниця між виразами? !!( a && b ) и (a && b)',
                'complexity' => 3,
                'significance' => 5,
                'relevance' => 5,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Яке з цих слів не має спеціального використання в JavaScript, ніяк не згадується в стандарті?',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' =>
                    'Які символи коректно знайдуть максимальне значення в непорожньому масиві arr?',
                'complexity' => 9,
                'significance' => 9,
                'relevance' => 10,
                'type' => 2,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює 2 && 1 && null && 0 && undefined?',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 2,
                'type' => 1,
                'description' => '&& повертає перший false, а null - false',
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює a + b + c?',
                'complexity' => 5,
                'significance' => 4,
                'relevance' => 4,
                'type' => 1,
                'description' => null,
                'image' => 'lock7.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Який результат буде у вираза? null + {0:1}[0] + [,[1],][1][0]',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює typeof null в режимі use strict?',
                'complexity' => 1,
                'significance' => 2,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що буде виведено на екран таким кодом?',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => "Створили об`єкт а, так як це масив то у нього є атрибут length проініціалізіроаний нулем a.length = 0, у 2-му рядку елементу масиву з індексом 0 привласнюють значення 0, в масиві 1 елемент, відповідно a.length = 1.",
                'image' => 'lock10.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Скільки параметрів можна передати функції?',
                'complexity' => 0,
                'significance' => 2,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Які з цих викликів синтаксично вірно згенерують виняток?',
                'complexity' => 1,
                'significance' => 2,
                'relevance' => 1,
                'type' => 2,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що виведе alert?',
                'complexity' => 0,
                'significance' => 1,
                'relevance' => 0,
                'type' => 1,
                'description' => null,
                'image' => 'lock13.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює значення виразу 4 - "5" + 0xf - "1e1"?',
                'complexity' => 9,
                'significance' => 9,
                'relevance' => 7,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чи спрацює виклик функції до оголошення в цьому коді:',
                'complexity' => 4,
                'significance' => 5,
                'relevance' => 5,
                'type' => 1,
                'description' => null,
                'image' => 'lock15.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що робить оператор **?',
                'complexity' => 0,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Який оператор із цих виконує не лише математичні операції?',
                'complexity' => 2,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Які виклики parseInt повернуть число?',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'type' => 2,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнюватиме this?',
                'complexity' => 6,
                'significance' => 5,
                'relevance' => 5,
                'type' => 1,
                'description' => null,
                'image' => 'lock19.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що виведе код?',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => 'lock20.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Мова JavaScript є підвидом мови Java - вірно?',
                'complexity' => 0,
                'significance' => 1,
                'relevance' => 0,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => "Після виконання цього коду - у яких об`єктах зі списку міститься властивість name?",
                'complexity' => 10,
                'significance' => 10,
                'relevance' => 10,
                'type' => 2,
                'description' => null,
                'image' => 'lock22.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що таке ECMAScript?',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Що виведе код?',
                'complexity' => 0,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => 'lock24.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чи є різниця між викликами i++ та ++i?',
                'complexity' => 1,
                'significance' => 2,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
            [
                'text' => 'Чому дорівнює довжина arr.length масиву arr?',
                'complexity' => 1,
                'significance' => 2,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => 'lock26.png',
                'expert_test_id' => 5
            ],
            [
                'text' => 'Які конструкції для циклів є в JavaScript?',
                'complexity' => 1,
                'significance' => 1,
                'relevance' => 1,
                'type' => 1,
                'description' => null,
                'image' => null,
                'expert_test_id' => 5
            ],
        ];

        $questions = [];

        for ($i = 0; $i < count($data); $i++) {
            $questions[] = [
                'text' => $data[$i]['text'],
                'complexity' => $data[$i]['complexity'],
                'significance' => $data[$i]['significance'],
                'relevance' => $data[$i]['relevance'],
                'quality_coef' => Question::getQualityCoefByFuzzyLogic($data[$i]),
                'type' => $data[$i]['type'],
                'description' => $data[$i]['description'],
                'image' => $data[$i]['image'],
                'created_at' => Carbon::now()->subMonth(),
                'updated_at' => Carbon::now()->subDays(rand(10, 30)),
                'expert_test_id' => $data[$i]['expert_test_id'],
            ];
        }

        DB::table('questions')->insert($questions);
    }
}
