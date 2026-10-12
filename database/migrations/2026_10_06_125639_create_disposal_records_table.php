<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disposal_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipment_id')
                ->unique()
                ->constrained('equipment')
                ->restrictOnDelete();

            $table->text('reason');

            $table->date('disposal_date');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('disposal_method')->nullable();
            $table->string('disposal_reference')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('disposal_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disposal_records');
    }
};