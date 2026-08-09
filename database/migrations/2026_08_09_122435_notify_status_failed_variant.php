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
        Schema::table('todos', function (Blueprint $table) {
            $table
                ->enum('notify_status', ['waiting', 'processing', 'sent', 'failed'])
                ->nullable()
                ->change();
        });

        Schema::table('events', function (Blueprint $table) {
            $table
                ->enum('notify_status', ['waiting', 'processing', 'sent', 'failed'])
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
