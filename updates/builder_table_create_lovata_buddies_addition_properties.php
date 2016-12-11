<?php namespace Lovata\Buddies\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLovataBuddiesAdditionProperties extends Migration
{
    public function up()
    {
        Schema::create('lovata_buddies_addition_properties', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->boolean('active')->default(1);
            $table->string('title');
            $table->string('code')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->default('input');
            $table->text('settings')->nullable();
            $table->integer('sort_order')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_buddies_addition_properties');
    }
}