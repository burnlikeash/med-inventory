<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->string('equipment_code')->unique();
            $table->string('barcode')->nullable()->unique();

            // Basic information
            $table->string('name');
            $table->foreignId('category_id')
                ->constrained('equipment_categories')
                ->restrictOnDelete();

            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('property_number')->nullable();

            // Purchase information
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 12, 2)->nullable();
            $table->string('supplier')->nullable();
            $table->date('warranty_expiry')->nullable();

            // Operational status
            $table->enum('status', [
                'Working',
                'Under Maintenance',
                'Not Working',
                'For Repair',
                'For Inspection',
            ])->default('Working');

            // Physical condition
            $table->enum('condition_status', [
                'Excellent',
                'Good',
                'Fair',
                'Poor',
                'Damaged',
            ])->default('Good');

            // Lifecycle status
            $table->enum('lifecycle_status', [
                'Active',
                'For Disposal',
                'Disposed',
                'Lost',
                'Retired',
            ])->default('Active');

            // Assignment
            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->restrictOnDelete();

            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->restrictOnDelete();

            // Additional information
            $table->text('remarks')->nullable();

            // Archive
            $table->timestamp('archived_at')->nullable();

            $table->timestamps();

            // Common search fields
            $table->index('name');
            $table->index('serial_number');
            $table->index('property_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};