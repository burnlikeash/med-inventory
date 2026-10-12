<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DisposalRecordController;
use App\Http\Controllers\EquipmentCategoryController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentMovementController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('equipment-scan', [EquipmentController::class, 'scan'])->name('equipment.scan');

    Route::middleware('role:Administrator,Inventory Officer')->group(function () {
        Route::redirect('equipment-archived', '/equipment/archived');
        Route::get('equipment/archived', [EquipmentController::class, 'archived'])->name('equipment.archived');
        Route::patch('equipment/{equipment}/restore', [EquipmentController::class, 'restore'])->name('equipment.restore');
        Route::get('equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
        Route::post('equipment', [EquipmentController::class, 'store'])->name('equipment.store');
        Route::get('equipment/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
        Route::match(['put', 'patch'], 'equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');
        Route::delete('equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');

        Route::get('equipment/{equipment}/move', [EquipmentMovementController::class, 'create'])->name('equipment.move');
        Route::post('equipment/{equipment}/move', [EquipmentMovementController::class, 'store'])->name('equipment.move.store');

        Route::get('disposals', [DisposalRecordController::class, 'index'])->name('disposal.index');
        Route::get('equipment/{equipment}/disposal/create', [DisposalRecordController::class, 'create'])->name('disposal.create');
        Route::post('equipment/{equipment}/disposal', [DisposalRecordController::class, 'store'])->name('disposal.store');
        Route::get('disposal/{disposalRecord}/edit', [DisposalRecordController::class, 'edit'])->name('disposal.edit');
        Route::put('disposal/{disposalRecord}', [DisposalRecordController::class, 'update'])->name('disposal.update');
        Route::delete('disposal/{disposalRecord}', [DisposalRecordController::class, 'destroy'])->name('disposal.destroy');

        Route::resource('categories', EquipmentCategoryController::class)->except(['show']);
        Route::resource('departments', DepartmentController::class)->except(['index', 'show']);
        Route::resource('locations', LocationController::class)->except(['index', 'show']);

        Route::get('reports/equipment', [ReportController::class, 'equipment'])->name('reports.equipment');
        Route::get('reports/equipment/csv', [ReportController::class, 'equipmentCsv'])->name('reports.equipment.csv');
        Route::get('reports/maintenance', [ReportController::class, 'maintenance'])->name('reports.maintenance');
        Route::get('reports/disposal', [ReportController::class, 'disposal'])->name('reports.disposal');
    });

    Route::middleware('role:Administrator,Inventory Officer,Maintenance Staff')->group(function () {
        Route::get('maintenance/due', [MaintenanceRecordController::class, 'due'])->name('maintenance.due');
        Route::get('equipment/{equipment}/maintenance/create', [MaintenanceRecordController::class, 'create'])->name('maintenance.create');
        Route::post('equipment/{equipment}/maintenance', [MaintenanceRecordController::class, 'store'])->name('maintenance.store');
        Route::get('maintenance/{maintenanceRecord}/edit', [MaintenanceRecordController::class, 'edit'])->name('maintenance.edit');
        Route::put('maintenance/{maintenanceRecord}', [MaintenanceRecordController::class, 'update'])->name('maintenance.update');
        Route::delete('maintenance/{maintenanceRecord}', [MaintenanceRecordController::class, 'destroy'])->name('maintenance.destroy');
    });

    Route::resource('departments', DepartmentController::class)->only(['index', 'show']);
    Route::resource('locations', LocationController::class)->only(['index', 'show']);

    Route::middleware('role:Administrator')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
        Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit.show');
    });

    Route::get('equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
});
