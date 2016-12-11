<?php namespace Lovata\Buddies\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLovataBuddiesGroups extends Migration
{
    public function up()
    {
        Schema::create('lovata_buddies_groups', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('lovata_buddies_users_groups', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->primary(['user_id', 'group_id'], 'user_group');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_buddies_groups');
        Schema::dropIfExists('lovata_buddies_users_groups');
    }
}