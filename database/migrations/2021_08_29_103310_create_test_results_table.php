<?php
// phpcs:ignoreFile

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->integer('serial_number');
            $table->boolean('is_correct_answer')->default(false);
            $table->float('score', 7, 4)->default(0);
            $table->float('max_score', 7, 4)->default(0);
            $table->json('user_answer')->nullable();
            $table->json('answer_ids');

            $table->foreignId('test_id')->constrained()
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('question_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_results');
    }
}
