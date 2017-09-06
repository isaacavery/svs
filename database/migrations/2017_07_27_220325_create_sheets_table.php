<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::create('sheets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('circulator_id')
                ->references('id')
                ->on('circulators')
                ->nullable();
            $table->char('filename', 255);
            $table->char('original_filename', 255);
            $table->char('md5_hash',32)->unique();
            $table->integer('batch_id');
            $table->integer('flagged_by')->nullable();
            $table->integer('signatures_completed_by')->nullable();
            $table->integer('circulator_completed_by')->nullable();
            $table->integer('reviewed_by')->nullable();
            $table->date('date_signed')->nullable();
            $table->integer('signature_count')->nullable();
            $table->boolean('self_signed')->default(false);
            $table->dateTime('checked_out')->default(0);
            $table->integer('user_id')
                ->references('id')
                ->on('users');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sheets');
        Schema::dropIfExists('batches');
    }
}
