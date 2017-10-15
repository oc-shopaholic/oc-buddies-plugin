<?php namespace Lovata\Buddies\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateUsers
 * @package Lovata\Buddies\Updates
 */
class TableCreateUsers extends Migration
{
    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable('lovata_buddies_users')) {
            return;
        }
        
        Schema::create('lovata_buddies_users', function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->string('email');
            $obTable->string('password');
            $obTable->string('name')->nullable();
            $obTable->string('last_name')->nullable();
            $obTable->string('middle_name')->nullable();
            $obTable->text('phone')->nullable();
            $obTable->text('phone_short')->nullable();
            $obTable->string('activation_code')->nullable();
            $obTable->string('persist_code')->nullable();
            $obTable->string('reset_password_code')->nullable();
            $obTable->text('permissions')->nullable();
            $obTable->boolean('is_activated')->default(0);
            $obTable->timestamp('activated_at')->nullable();
            $obTable->timestamp('last_login')->nullable();
            $obTable->boolean('is_superuser')->default(0);
            $obTable->mediumText('property')->nullable();
            $obTable->timestamps();
            $obTable->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists('lovata_buddies_users');
    }
}