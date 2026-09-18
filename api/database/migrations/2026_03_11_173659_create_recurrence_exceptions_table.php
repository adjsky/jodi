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
        Schema::create('recurrence_exceptions', function (Blueprint $table) {
            $table->id();
            $table->string('recurrenceable_type');
            $table->unsignedBigInteger('recurrenceable_id');
            $table->date('occurs_at');
            $table->boolean('is_cancelled');
            $table->json('overrides');
            $table->timestamps();

            $table->unique(['recurrenceable_type', 'recurrenceable_id', 'occurs_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurrence_exceptions');
    }
};
