<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCirculatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('circulators', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('voter_id')->nullable();
            $table->integer('user_id')
                ->references('id')
                ->on('users');
            $table->char('first_name',255);
            $table->char('middle_name', 255)->nullable();
            $table->char('last_name', 255);
            $table->char('street_name', 255);
            $table->char('street_number',20);
            $table->char('address',255);
            $table->char('city',255);
            $table->integer('zip_code');
            $table->timestamps();
            $table->index('first_name');
            $table->index('last_name');
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('circulators');
    }
}
