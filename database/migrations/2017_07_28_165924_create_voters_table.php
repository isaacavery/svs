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
                $table->char('first_name',100);
                $table->char('middle_name',100)->nullable();
                $table->char('last_name',100);
                $table->char('name_suffix',20)->nullable();
                $table->char('birth_date',60);
                $table->char('confidential',120)->nullable();
                $table->char('eff_regn_date',10);
                $table->char('status',10);
                $table->char('party_code',10);
                $table->char('phone_num',100)->nullable();
                $table->char('unlisted',60)->nullable();
                $table->char('county',60);
                $table->char('res_address_1',255)->nullable();
                $table->char('res_address_2',255)->nullable();
                $table->char('house_num',60);
                $table->char('house_suffix',60)->nullable();
                $table->char('pre_direction',60)->nullable();
                $table->char('street_name',100);
                $table->char('street_type',20)->nullable();
                $table->char('post_direction',60)->nullable();
                $table->char('unit_type',20)->nullable();
                $table->char('unit_num',20)->nullable();
                $table->char('addr_non_std',255)->nullable();
                $table->char('city',100);
                $table->char('state',20);
                $table->char('zip_code',20);
                $table->char('zip_plus_four',20)->nullable();
                $table->char('eff_address_1',255)->nullable();
                $table->char('eff_address_2',255)->nullable();
                $table->char('eff_address_3',255)->nullable();
                $table->char('eff_address_4',255)->nullable();
                $table->char('eff_city',255);
                $table->char('eff_state',20);
                $table->char('eff_zip_code',20);
                $table->char('eff_zip_plus_four',20)->nullable();
                $table->char('absentee_type',20)->nullable();
                $table->char('precinct_name',60);
                $table->char('precinct',60);
                $table->char('split',20);
                $table->timestamps();
                $table->index(['first_name','last_name']);
                $table->index('voter_id');
                $table->index('house_num','street_name');
                $table->index('city');
                $table->index('zip_code');
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
