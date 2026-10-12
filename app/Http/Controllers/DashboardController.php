<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Equipment;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalEquipment = Equipment::query()->active()->count();
        $workingEquipment = Equipment::query()->active()->where('status', 'Working')->count();
        $needsAttention = Equipment::query()->active()->whereIn('status', Equipment::ATTENTION_STATUSES)->count();
        $maintenanceDue = Equipment::query()->dueForMaintenance()->count();
        $forDisposal = Equipment::query()->active()->where('lifecycle_status', 'For Disposal')->count();
        $archived = Equipment::query()->archived()->count();

        $upcomingMaintenance = collect();
        $recentAuditLogs = collect();

        if ($user->canMaintainEquipment()) {
            $upcomingMaintenance = Equipment::query()
                ->with(['department', 'location'])
                ->dueForMaintenance()
                ->withNextMaintenanceDate()
                ->orderBy('next_maintenance_date')
                ->limit(8)
                ->get();
        }

        if ($user->isAdministrator()) {
            $recentAuditLogs = AuditLog::with('user')
                ->latest('created_at')
                ->take(8)
                ->get();
        }

        return view('dashboard.index', compact(
            'totalEquipment',
            'workingEquipment',
            'needsAttention',
            'maintenanceDue',
            'forDisposal',
            'archived',
            'upcomingMaintenance',
            'recentAuditLogs'
        ));
    }
}
