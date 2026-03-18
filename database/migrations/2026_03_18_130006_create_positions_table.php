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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('positionable_type');
            $table->unsignedBigInteger('positionable_id');
            $table->date('date');
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->unique(['positionable_type', 'positionable_id', 'date']);
        });

        Schema::table('todos', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
