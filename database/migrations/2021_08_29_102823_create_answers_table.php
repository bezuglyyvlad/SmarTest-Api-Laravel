<?php
// phpcs:ignoreFile

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->boolean('is_correct');

            $table->boolean('active_record')->default(true);
            $table->unsignedInteger('modified_records_parent_id')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('question_id')->constrained();
            $table->index('active_record');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}