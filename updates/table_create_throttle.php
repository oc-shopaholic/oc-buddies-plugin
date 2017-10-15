<?php namespace Lovata\Buddies\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateThrottle
 * @package Lovata\Buddies\Updates
 */
class TableCreateThrottle extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable('lovata_buddies_throttle')) {
            return;
        }
        
        Schema::create('lovata_buddies_throttle', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->integer('user_id')->nullable();
            $obTable->string('ip_address')->nullable();
            $obTable->integer('attempts')->default(0);
            $obTable->timestamp('last_attempt_at')->nullable();
            $obTable->boolean('is_suspended')->default(0);
            $obTable->timestamp('suspended_at')->nullable();
            $obTable->boolean('is_banned')->default(0);
            $obTable->timestamp('banned_at')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists('lovata_buddies_throttle');
    }
}