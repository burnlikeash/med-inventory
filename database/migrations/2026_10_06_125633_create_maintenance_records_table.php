<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->restrictOnDelete();

            $table->string('maintenance_type');
            $table->date('maintenance_date');

            $table->string('performed_by')->nullable();

            $table->text('description');
            $table->decimal('cost', 12, 2)->nullable();

            $table->string('result')->nullable();

            $table->date('next_maintenance_date')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('maintenance_date');
            $table->index('next_maintenance_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};