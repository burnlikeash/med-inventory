<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->restrictOnDelete();

            // Previous assignment
            $table->foreignId('from_department_id')
                ->nullable()
                ->constrained('departments')
                ->restrictOnDelete();

            $table->foreignId('from_location_id')
                ->nullable()
                ->constrained('locations')
                ->restrictOnDelete();

            // New assignment
            $table->foreignId('to_department_id')
                ->nullable()
                ->constrained('departments')
                ->restrictOnDelete();

            $table->foreignId('to_location_id')
                ->nullable()
                ->constrained('locations')
                ->restrictOnDelete();

            $table->dateTime('movement_date');

            $table->string('reason');
            $table->text('remarks')->nullable();

            // User who performed the movement
            $table->foreignId('moved_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index('movement_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_movements');
    }
};