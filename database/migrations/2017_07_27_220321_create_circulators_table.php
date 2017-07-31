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
            $table->char('first_name',255);
            $table->char('last_name', 255);
            $table->char('street_name', 255);
            $table->char('street_number',20);
            $table->char('city',255);
            $table->integer('zip');
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
        Schema::dropIfExists('circulators');
    }
}
