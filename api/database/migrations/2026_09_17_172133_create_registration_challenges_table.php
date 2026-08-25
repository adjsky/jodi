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
        Schema::create('registration_challenges', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->foreignId('registration_invitation_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('email');
            $table->string('code_hash');
            $table->text('data');
            $table->timestamp('expires_at');

            $table->timestamps();

            $table->unique('registration_invitation_id');
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_challenges');
    }
};
