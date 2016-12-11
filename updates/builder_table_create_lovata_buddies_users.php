<?php namespace Lovata\Buddies\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLovataBuddiesUsers extends Migration
{
    public function up()
    {
        Schema::create('lovata_buddies_users', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('email');
            $table->string('password');
            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('activation_code')->nullable();
            $table->string('persist_code')->nullable();
            $table->string('reset_password_code')->nullable();
            $table->text('permissions')->nullable();
            $table->boolean('is_activated')->default(0);
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->boolean('is_superuser')->default(0);
            $table->text('property')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lovata_buddies_users');
    }
}