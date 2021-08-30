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
            $table->boolean('correct_answer')->default(false);
            $table->float('score')->default(0);
            $table->json('user_answer')->nullable();

            /** @phpstan-ignore-next-line */
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
