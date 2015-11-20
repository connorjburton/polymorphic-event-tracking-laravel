<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->morphs('eventable');
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('event_types');
            $table->text('data');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        if (Schema::hasTable('events')) {
            Schema::drop('events');
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
