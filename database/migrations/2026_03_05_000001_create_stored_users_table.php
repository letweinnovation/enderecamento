<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('stored_users')) {
            return;
        }

        Schema::create('stored_users', function (Blueprint $table) {
            $table->id();
            $table->string('google_email')->unique();
            $table->string('google_name');
            $table->text('google_picture')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stored_users');
    }
};
