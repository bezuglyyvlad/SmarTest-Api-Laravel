<?php
// phpcs:ignoreFile

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->float('quality_coef', 17, 16);
            $table->integer('type');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->boolean('active_record')->default(true);
            $table->unsignedBigInteger('modified_records_parent_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreignId('expert_test_id')->constrained();
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
        Schema::dropIfExists('questions');
    }
}
