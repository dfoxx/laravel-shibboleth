<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_resets');
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unity_id', 100)->unique();
            $table->string('name');
            $table->string('email', 100)->unique();
            $table->string('auth_type')->default('shibboleth');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
