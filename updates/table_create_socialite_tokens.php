<?php namespace Lovata\Buddies\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Class TableCreateSocialiteTokens
 * @package Lovata\Buddies\Updates
 */
class TableCreateSocialiteTokens extends Migration
{
    const TABLE_NAME = 'lovata_buddies_socialite_tokens';

    /**
     * Apply migration
     */
    public function up()
    {
        if(Schema::hasTable(self::TABLE_NAME)) {
            return;
        }
        
        Schema::create(self::TABLE_NAME, function(Blueprint $obTable)
        {
            $obTable->engine = 'InnoDB';
            $obTable->increments('id');
            $obTable->integer('user_id')->unsigned();
            $obTable->string('code');
            $obTable->string('external_id');
            $obTable->timestamps();

            $obTable->index('user_id');
            $obTable->index('code');
            $obTable->index('external_id');
        });
    }

    /**
     * Rollback migration
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
}