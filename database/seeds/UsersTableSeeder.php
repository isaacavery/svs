<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        	'name' => 'Isaac Avery',
        	'email' => 'isaac.j.avery@gmail.com',
        	'password' => bcrypt('P@ssw0rd1!'),
        	'admin' => 1
        ]);
        DB::table('users')->insert([
        	'name' => 'Ryan Steen',
        	'email' => 'ryansteen76@gmail.com',
        	'password' => bcrypt('P@ssw0rd1!'),
        	'admin' => 1
        ]);
        DB::table('users')->insert([
        	'name' => 'Jeff Jimerson',
        	'email' => 'jeff@oregonlifeunited.org',
        	'password' => bcrypt('P@ssw0rd1!'),
        	'admin' => 1
        ]);
    }
}
