<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Because of the amount of overhead required to import voter data, we are only going to
        // add the Schema once, and after that it will be managed by the command `php artisan
        // voters:import` as needed.
        if (!Schema::hasTable('voters')) {
            Schema::create('voters', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('voter_id');
                $table->char('first_name',32)->nullable();
                $table->char('middle_name',32)->nullable();
                $table->char('last_name',32)->nullable();
                $table->char('name_suffix',16)->nullable();
                $table->integer('birth_date')->nullable();
                $table->char('confidential',16)->nullable();
                $table->date('eff_regn_date')->nullable();
                $table->char('status',1)->nullable();
                $table->char('party_code',3)->nullable();
                $table->char('county',32)->nullable();
                $table->char('res_address_1',255)->nullable();
                $table->char('res_address_2',255)->nullable();
                $table->integer('house_num')->nullable();
                $table->char('house_suffix',16)->nullable();
                $table->char('pre_direction',8)->nullable();
                $table->char('street_name',64)->nullable();
                $table->char('street_type',8)->nullable();
                $table->char('post_direction',8)->nullable();
                $table->char('unit_type',8)->nullable();
                $table->char('unit_num',16)->nullable();
                $table->char('addr_non_std',255)->nullable();
                $table->char('city',100)->nullable();
                $table->char('state',20)->nullable();
                $table->integer('zip_code')->nullable();
                $table->integer('zip_plus_four')->nullable();
                $table->char('eff_address_1',255)->nullable();
                $table->char('eff_address_2',255)->nullable();
                $table->char('eff_address_3',255)->nullable();
                $table->char('eff_address_4',255)->nullable();
                $table->char('eff_city',64)->nullable();
                $table->char('eff_state',8)->nullable();
                $table->integer('eff_zip_code')->nullable();
                $table->integer('eff_zip_plus_four')->nullable();
                $table->char('absentee_type',32)->nullable();
                $table->char('precinct_name',64)->nullable();
                $table->integer('precinct')->nullable();
                $table->char('split',16)->nullable();
                $table->index('first_name');
                $table->index('last_name');
                $table->index('voter_id');
                $table->index('house_num');
                $table->index('street_name');
                $table->index('city');
                $table->index('zip_code');
                $table->index('eff_zip_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Excluding the default drop schema to avoid reloading voter data.
        // Schema::dropIfExists('voters');
    }
}
