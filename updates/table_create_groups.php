<?php namespace Lovata\Buddies\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateGroups
 * @package Lovata\Buddies\Updates
 */
class TableCreateGroups extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(!Schema::hasTable('lovata_buddies_groups')) {
            Schema::create('lovata_buddies_groups', function(Blueprint $obTable)
            {
                $obTable->engine = 'InnoDB';
                $obTable->increments('id');
                $obTable->string('name');
                $obTable->string('code');
                $obTable->text('description')->nullable();
                $obTable->timestamps();
            });
        }

        if(!Schema::hasTable('lovata_buddies_users_groups')) {
            Schema::create('lovata_buddies_users_groups', function ($obTable) {
                $obTable->engine = 'InnoDB';
                $obTable->integer('user_id')->unsigned();
                $obTable->integer('group_id')->unsigned();
                $obTable->primary(['user_id', 'group_id'], 'user_group');
            });
        }
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists('lovata_buddies_groups');
        Schema::dropIfExists('lovata_buddies_users_groups');
    }
}