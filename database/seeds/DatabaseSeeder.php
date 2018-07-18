<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    	$us = [
    		[
    			'name' => 'Tim',
    			'email' => 'timscouts@sbcglobal.net',
    			'password' => 'Scrounger626'
    		],
    		[
    			'name' => 'Noah',
    			'email' => 'nrleast@gmail.com',
    			'password' => 'Revelations1221'
    		],
    		[
    			'name' => 'Kyle',
    			'email' => 'triiodide@yahoo.com',
    			'passowrd' => 'mangos'
    		]
    	];

        foreach ($us as $person) {
        	App\User::create([
    		'name' => $person['name'],
        	'email' => $person['email'],
        	'password' => bcrypt($person['password'])
        ]);
        }
    }
}
