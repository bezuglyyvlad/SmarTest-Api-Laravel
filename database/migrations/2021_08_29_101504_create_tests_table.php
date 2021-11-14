<?php
// phpcs:ignoreFile

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('start_date');
            $table->string('finish_date');
            $table->float('score', 7, 4)->default(0);
            $table->float('max_score', 7, 4)->default(0);

            $table->foreignId('user_id')->constrained()
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('expert_test_id')->constrained();
            $table->foreignId('test_category_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
