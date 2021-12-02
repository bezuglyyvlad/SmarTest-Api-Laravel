<?php

namespace Database\Seeders;

use App\Models\Question;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnswersTableSeeder extends Seeder
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
                'text' => 'Відбудеться помилка виконання скрипта',
                'is_correct' => 0,
                'question_id' => 1,
            ],
            [
                'text' => 'Код виведе спочатку "NaN", а потім "5"',
                'is_correct' => 0,
                'question_id' => 1,
            ],
            [
                'text' => 'Код виведе спочатку "null", а потім "5"',
                'is_correct' => 0,
                'question_id' => 1,
            ],
            [
                'text' => 'Код виведе спочатку порожній рядок, а потім "5"',
                'is_correct' => 0,
                'question_id' => 1,
            ],
            [
                'text' => 'Код виведе спочатку "undefined", а потім "5"',
                'is_correct' => 1,
                'question_id' => 1,
            ],

            [
                'text' => 'false, false.',
                'is_correct' => 0,
                'question_id' => 2,
            ],
            [
                'text' => 'false, true.',
                'is_correct' => 1,
                'question_id' => 2,
            ],
            [
                'text' => 'true, false.0',
                'is_correct' => 0,
                'question_id' => 2,
            ],
            [
                'text' => 'true, true.',
                'is_correct' => 0,
                'question_id' => 2,
            ],

            [
                'text' => 'Так.',
                'is_correct' => 1,
                'question_id' => 3,
            ],
            [
                'text' => 'Ні.',
                'is_correct' => 0,
                'question_id' => 3,
            ],
            [
                'text' => 'У першому вираженні помилка, що ще за «!!» ??',
                'is_correct' => 0,
                'question_id' => 3,
            ],

            [
                'text' => 'this',
                'is_correct' => 0,
                'question_id' => 4,
            ],
            [
                'text' => 'instanceof',
                'is_correct' => 0,
                'question_id' => 4,
            ],
            [
                'text' => 'constructor',
                'is_correct' => 0,
                'question_id' => 4,
            ],
            [
                'text' => 'parent',
                'is_correct' => 1,
                'question_id' => 4,
            ],
            [
                'text' => 'new',
                'is_correct' => 0,
                'question_id' => 4,
            ],
            [
                'text' => 'Всі мають спеціальне використання.',
                'is_correct' => 0,
                'question_id' => 4,
            ],

            [
                'text' => 'arr.reduce(function(prev, item) { return Math.max(prev, item) })',
                'is_correct' => 1,
                'question_id' => 5,
            ],
            [
                'text' => 'Math.max.apply(null, arr)',
                'is_correct' => 1,
                'question_id' => 5,
            ],
            [
                'text' => 'Math.max(arr)',
                'is_correct' => 0,
                'question_id' => 5,
            ],
            [
                'text' => 'arr.findMax()',
                'is_correct' => 0,
                'question_id' => 5,
            ],

            [
                'text' => '2',
                'is_correct' => 0,
                'question_id' => 6,
            ],
            [
                'text' => '1',
                'is_correct' => 0,
                'question_id' => 6,
            ],
            [
                'text' => 'null',
                'is_correct' => 1,
                'question_id' => 6,
            ],
            [
                'text' => '0',
                'is_correct' => 0,
                'question_id' => 6,
            ],
            [
                'text' => 'undefined',
                'is_correct' => 0,
                'question_id' => 6,
            ],
            [
                'text' => 'false',
                'is_correct' => 0,
                'question_id' => 6,
            ],

            [
                'text' => '11[object Object]',
                'is_correct' => 0,
                'question_id' => 7,
            ],
            [
                'text' => '2[object Object]',
                'is_correct' => 0,
                'question_id' => 7,
            ],
            [
                'text' => '111',
                'is_correct' => 1,
                'question_id' => 7,
            ],
            [
                'text' => '3',
                'is_correct' => 0,
                'question_id' => 7,
            ],

            [
                'text' => '0',
                'is_correct' => 0,
                'question_id' => 8,
            ],
            [
                'text' => '1',
                'is_correct' => 0,
                'question_id' => 8,
            ],
            [
                'text' => '2',
                'is_correct' => 1,
                'question_id' => 8,
            ],
            [
                'text' => 'undefined',
                'is_correct' => 0,
                'question_id' => 8,
            ],
            [
                'text' => 'NaN',
                'is_correct' => 0,
                'question_id' => 8,
            ],

            [
                'text' => 'null',
                'is_correct' => 0,
                'question_id' => 9,
            ],
            [
                'text' => 'undefined',
                'is_correct' => 0,
                'question_id' => 9,
            ],
            [
                'text' => 'object',
                'is_correct' => 1,
                'question_id' => 9,
            ],
            [
                'text' => 'string',
                'is_correct' => 0,
                'question_id' => 9,
            ],

            [
                'text' => 'undefined',
                'is_correct' => 0,
                'question_id' => 10,
            ],
            [
                'text' => '1',
                'is_correct' => 1,
                'question_id' => 10,
            ],
            [
                'text' => '0',
                'is_correct' => 0,
                'question_id' => 10,
            ],
            [
                'text' => 'Нічого не буде виведено',
                'is_correct' => 0,
                'question_id' => 10,
            ],

            [
                'text' => 'Рівно стільки, скільки вказано у визначенні функції.',
                'is_correct' => 0,
                'question_id' => 11,
            ],
            [
                'text' => 'Скільки зазначено у визначенні функції або менше.',
                'is_correct' => 0,
                'question_id' => 11,
            ],
            [
                'text' => 'Скільки зазначено у визначенні функції або більше.',
                'is_correct' => 0,
                'question_id' => 11,
            ],
            [
                'text' => 'Будь-яка кількість.',
                'is_correct' => 1,
                'question_id' => 11,
            ],

            [
                'text' => 'throw "Помилка"',
                'is_correct' => 1,
                'question_id' => 12,
            ],
            [
                'text' => 'throw new Error("Помилка")',
                'is_correct' => 1,
                'question_id' => 12,
            ],
            [
                'text' => 'throw { message: "Помилка" }',
                'is_correct' => 1,
                'question_id' => 12,
            ],
            [
                'text' => 'throw Error("Помилка")',
                'is_correct' => 1,
                'question_id' => 12,
            ],
            [
                'text' => 'Жоден.',
                'is_correct' => 0,
                'question_id' => 12,
            ],

            [
                'text' => 'Hello',
                'is_correct' => 0,
                'question_id' => 13,
            ],
            [
                'text' => 'undefined',
                'is_correct' => 0,
                'question_id' => 13,
            ],
            [
                'text' => 'Буде помилка.',
                'is_correct' => 1,
                'question_id' => 13,
            ],

            [
                'text' => 'Цифрі.',
                'is_correct' => 1,
                'question_id' => 14,
            ],
            [
                'text' => 'Рядку.',
                'is_correct' => 0,
                'question_id' => 14,
            ],
            [
                'text' => 'NaN',
                'is_correct' => 0,
                'question_id' => 14,
            ],

            [
                'text' => 'Так, спрацює.',
                'is_correct' => 1,
                'question_id' => 15,
            ],
            [
                'text' => 'Ні, виклик повинен стояти після оголошення.',
                'is_correct' => 0,
                'question_id' => 15,
            ],

            [
                'text' => 'Зводить у ступінь.',
                'is_correct' => 1,
                'question_id' => 16,
            ],
            [
                'text' => 'Помножує число на себе.',
                'is_correct' => 0,
                'question_id' => 16,
            ],
            [
                'text' => 'Немає такого оператора.',
                'is_correct' => 0,
                'question_id' => 16,
            ],

            [
                'text' => '*',
                'is_correct' => 0,
                'question_id' => 17,
            ],
            [
                'text' => '/',
                'is_correct' => 0,
                'question_id' => 17,
            ],
            [
                'text' => '+',
                'is_correct' => 1,
                'question_id' => 17,
            ],
            [
                'text' => '-',
                'is_correct' => 0,
                'question_id' => 17,
            ],
            [
                'text' => '>>>',
                'is_correct' => 0,
                'question_id' => 17,
            ],

            [
                'text' => 'parseInt("1px")',
                'is_correct' => 1,
                'question_id' => 18,
            ],
            [
                'text' => 'parseInt("-1.2")',
                'is_correct' => 1,
                'question_id' => 18,
            ],
            [
                'text' => 'parseInt("0 минут")',
                'is_correct' => 1,
                'question_id' => 18,
            ],
            [
                'text' => 'parseInt("$1.2")',
                'is_correct' => 0,
                'question_id' => 18,
            ],

            [
                'text' => 'null',
                'is_correct' => 0,
                'question_id' => 19,
            ],
            [
                'text' => 'undefined',
                'is_correct' => 1,
                'question_id' => 19,
            ],
            [
                'text' => "Об'єкту user.",
                'is_correct' => 0,
                'question_id' => 19,
            ],
            [
                'text' => 'У коді помилка.',
                'is_correct' => 0,
                'question_id' => 19,
            ],

            [
                'text' => 'undefined',
                'is_correct' => 1,
                'question_id' => 20,
            ],
            [
                'text' => 'function',
                'is_correct' => 0,
                'question_id' => 20,
            ],
            [
                'text' => 'null',
                'is_correct' => 0,
                'question_id' => 20,
            ],
            [
                'text' => 'object',
                'is_correct' => 0,
                'question_id' => 20,
            ],
            [
                'text' => 'У коді помилка.',
                'is_correct' => 0,
                'question_id' => 20,
            ],

            [
                'text' => 'Так.',
                'is_correct' => 0,
                'question_id' => 21,
            ],
            [
                'text' => 'Ні.',
                'is_correct' => 1,
                'question_id' => 21,
            ],
            [
                'text' => 'Навпаки, Java - підвид JavaScript.',
                'is_correct' => 0,
                'question_id' => 21,
            ],

            [
                'text' => 'vasya.__proto__',
                'is_correct' => 1,
                'question_id' => 22,
            ],
            [
                'text' => 'vasya.prototype',
                'is_correct' => 0,
                'question_id' => 22,
            ],
            [
                'text' => 'User.__proto__',
                'is_correct' => 0,
                'question_id' => 22,
            ],
            [
                'text' => 'User.prototype',
                'is_correct' => 1,
                'question_id' => 22,
            ],

            [
                'text' => 'Нова мова програмування.',
                'is_correct' => 0,
                'question_id' => 23,
            ],
            [
                'text' => 'Перероблена реалізація JavaScript.',
                'is_correct' => 0,
                'question_id' => 23,
            ],
            [
                'text' => 'Специфікація мови JavaScript.',
                'is_correct' => 1,
                'question_id' => 23,
            ],

            [
                'text' => '1',
                'is_correct' => 0,
                'question_id' => 24,
            ],
            [
                'text' => '2',
                'is_correct' => 1,
                'question_id' => 24,
            ],
            [
                'text' => 'NaN',
                'is_correct' => 0,
                'question_id' => 24,
            ],
            [
                'text' => 'undefined',
                'is_correct' => 0,
                'question_id' => 24,
            ],
            [
                'text' => 'Буде помилка.',
                'is_correct' => 0,
                'question_id' => 24,
            ],

            [
                'text' => 'Різниця у значенні, яке повертає такий виклик.',
                'is_correct' => 1,
                'question_id' => 25,
            ],
            [
                'text' => 'Різниця у значенні i після виклику.',
                'is_correct' => 0,
                'question_id' => 25,
            ],
            [
                'text' => 'Нема ніякої різниці.',
                'is_correct' => 0,
                'question_id' => 25,
            ],

            [
                'text' => '0',
                'is_correct' => 0,
                'question_id' => 26,
            ],
            [
                'text' => '1',
                'is_correct' => 0,
                'question_id' => 26,
            ],
            [
                'text' => '2',
                'is_correct' => 0,
                'question_id' => 26,
            ],
            [
                'text' => '3',
                'is_correct' => 0,
                'question_id' => 26,
            ],
            [
                'text' => '4',
                'is_correct' => 1,
                'question_id' => 26,
            ],
            [
                'text' => 'Більше.',
                'is_correct' => 0,
                'question_id' => 26,
            ],

            [
                'text' => 'Тільки дві: for и while.',
                'is_correct' => 0,
                'question_id' => 27,
            ],
            [
                'text' => 'Тільки одна: for.',
                'is_correct' => 0,
                'question_id' => 27,
            ],
            [
                'text' => 'Три: for, while и do...while.',
                'is_correct' => 1,
                'question_id' => 27,
            ],
        ];

        $answers = [];

        for ($i = 0; $i < count($data); $i++) {
            $answers[] = [
                'text' => $data[$i]['text'],
                'is_correct' => $data[$i]['is_correct'],
                'created_at' => Carbon::now()->subMonth(),
                'updated_at' => Carbon::now()->subDays(rand(10, 30)),
                'question_id' => $data[$i]['question_id'],
            ];
        }

        DB::table('answers')->insert($answers);
    }
}
