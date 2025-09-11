<?php

declare(strict_types=1);

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
        Schema::create('registration_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inviter_user_id')->constrained('users');
            $table->string('email');
            $table->string('code')->unique();
            $table->datetime('expires_at');
            $table->foreignId('registered_user_id')->nullable()->constrained('users');
            $table->datetime('registered_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_invitations');
    }
};
