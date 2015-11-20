<?php

use Illuminate\Database\Seeder;

class EventTypesSeeder extends Seeder
{
    // Seed for command types ("name")
    private $command_types = array(
        'view'
    );
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::statement('SET FOREIGN_KEY_CHECKS=0');
		DB::table('event_types')->truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS=1');
		
        foreach($this->command_types as $type) {
            DB::table('event_types')->insert(['name' => $type]);
        }
    }
}
