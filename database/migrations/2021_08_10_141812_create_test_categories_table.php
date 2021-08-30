<?php
// phpcs:ignoreFile

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('parent_id')->default(0);

            $table->boolean('active_record')->default(true);
            $table->unsignedInteger('modified_records_parent_id')->default(0);

            $table->timestamps();
            $table->softDeletes();

            /** @phpstan-ignore-next-line */
            $table->foreignId('user_id')->nullable()->constrained()
                ->onUpdate('cascade')->onDelete('set null');
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
        Schema::dropIfExists('test_categories');
    }
}
