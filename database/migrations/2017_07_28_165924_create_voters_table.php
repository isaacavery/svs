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
                $table->char('first_name',255);
                $table->char('middle_name',255)->nullable();
                $table->char('last_name',255);
                $table->text('name_suffix')->nullable();
                $table->text('birth_date');
                $table->text('confidential')->nullable();
                $table->text('eff_regn_date');
                $table->text('status');
                $table->text('party_code');
                $table->text('phone_num')->nullable();
                $table->text('unlisted')->nullable();
                $table->text('county');
                $table->text('res_address_1')->nullable();
                $table->text('res_address_2')->nullable();
                $table->text('house_num');
                $table->text('house_suffix')->nullable();
                $table->text('pre_direction')->nullable();
                $table->text('street_name');
                $table->text('street_type')->nullable();
                $table->text('post_direction')->nullable();
                $table->text('unit_type')->nullable();
                $table->text('unit_num')->nullable();
                $table->text('addr_non_std')->nullable();
                $table->text('city');
                $table->text('state');
                $table->text('zip_code');
                $table->text('zip_plus_four')->nullable();
                $table->text('eff_address_1')->nullable();
                $table->text('eff_address_2')->nullable();
                $table->text('eff_address_3')->nullable();
                $table->text('eff_address_4')->nullable();
                $table->text('eff_city');
                $table->text('eff_state');
                $table->text('eff_zip_code');
                $table->text('eff_zip_plus_four')->nullable();
                $table->text('absentee_type')->nullable();
                $table->text('precinct_name');
                $table->text('precinct');
                $table->text('split');
                $table->timestamps();
                $table->index(['first_name','last_name']);
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
