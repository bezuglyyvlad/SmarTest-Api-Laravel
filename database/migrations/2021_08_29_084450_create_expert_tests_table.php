<?php
// phpcs:ignoreFile

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpertTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expert_tests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('is_published')->default(false);

            $table->unsignedBigInteger('modified_records_parent_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('expert_tests');
    }
}
