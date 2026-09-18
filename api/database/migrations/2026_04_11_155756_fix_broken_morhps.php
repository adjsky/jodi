<?php

declare(strict_types=1);

use App\Domain\Recurrence\Models\RecurrenceException;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        RecurrenceException::query()
            ->where('recurrenceable_type', 'App\\Models\\Todo')
            ->update(['recurrenceable_type' => 'todo']);

        RecurrenceException::query()
            ->where('recurrenceable_type', 'App\\Models\\Event')
            ->update(['recurrenceable_type' => 'event']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
