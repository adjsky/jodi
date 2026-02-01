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
            $table->renameColumn('todo_date', 'scheduled_at');
        });
        Schema::table('todos', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->change();
            $table->boolean('has_time')->default(false)->before('recurrence_id');
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
