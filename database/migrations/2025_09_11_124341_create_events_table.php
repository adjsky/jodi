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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index()->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->foreignId('recurrence_id')->nullable()->constrained();
            $table->string('color')->nullable();
            $table->datetime('starts_at')->index();
            $table->datetime('ends_at')->nullable();
            $table->boolean('is_all_day');
            $table->datetime('notify_at')->nullable()->index();
            $table->enum('notify_status', ['processing', 'sent'])->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
