<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('employee_id', 9)->unique();
            $table->string('unity_id', 8)->unique();
            $table->string('email', 100)->unique();
            $table->string('name');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('preferred_name')->nullable();
            $table->string('honorific')->nullable();
            $table->string('auth_type')->default('shibboleth');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        //
    }
}
