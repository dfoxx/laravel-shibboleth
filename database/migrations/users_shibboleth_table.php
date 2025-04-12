<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_shibboleth', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('uid')->nullable()->index();
            $table->string('eptid')->nullable()->index();
            $table->string('eppn')->nullable()->index();
            $table->string('cpid')->nullable()->index();
            $table->string('session_index')->nullable()->index();
            $table->string('identity_provider')->nullable()->index();
            $table->longText('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_shibboleth');
    }
};
