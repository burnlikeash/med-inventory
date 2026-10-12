<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaintenanceRecordRequest;
use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class MaintenanceRecordController extends Controller
{
    public function create(Equipment $equipment)
    {
        if ($this->cannotReceiveMaintenance($equipment)) {
            return $this->blocked($equipment);
        }

        return view('maintenance.create', compact('equipment'));
    }

    public function store(MaintenanceRecordRequest $request, Equipment $equipment)
    {
        if ($this->cannotReceiveMaintenance($equipment)) {
            return $this->blocked($equipment);
        }

        $validated = $request->validated();
        $equipmentStatus = $validated['equipment_status'];
        unset($validated['equipment_status']);
        $validated['equipment_id'] = $equipment->id;

        DB::transaction(function () use ($validated, $equipment, $equipmentStatus) {
            $oldEquipmentValues = $equipment->toArray();
            $maintenanceRecord = MaintenanceRecord::create($validated);

            $equipment->update(['status' => $equipmentStatus]);
            $equipment->refresh();

            AuditLogger::log(
                'MAINTENANCE',
                'maintenance_records',
                $maintenanceRecord->id,
                $oldEquipmentValues,
                [
                    'maintenance_record' => $maintenanceRecord->toArray(),
                    'equipment' => $equipment->toArray(),
                ],
                'Added maintenance record for equipment: '.$equipment->equipment_code.' - '.$equipment->name
            );
        });

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Maintenance record added and equipment status updated successfully.');
    }

    public function edit(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->load('equipment');

        if ($this->cannotReceiveMaintenance($maintenanceRecord->equipment)) {
            return $this->blocked($maintenanceRecord->equipment);
        }

        return view('maintenance.edit', compact('maintenanceRecord'));
    }

    public function update(MaintenanceRecordRequest $request, MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->load('equipment');

        if ($this->cannotReceiveMaintenance($maintenanceRecord->equipment)) {
            return $this->blocked($maintenanceRecord->equipment);
        }

        $validated = $request->validated();
        $equipmentStatus = $validated['equipment_status'];
        unset($validated['equipment_status']);

        DB::transaction(function () use ($validated, $maintenanceRecord, $equipmentStatus) {
            $oldMaintenanceValues = $maintenanceRecord->toArray();
            $oldEquipmentValues = $maintenanceRecord->equipment->toArray();

            $maintenanceRecord->update($validated);
            $maintenanceRecord->equipment->update(['status' => $equipmentStatus]);
            $maintenanceRecord->refresh();
            $maintenanceRecord->equipment->refresh();

            AuditLogger::log(
                'MAINTENANCE',
                'maintenance_records',
                $maintenanceRecord->id,
                [
                    'maintenance_record' => $oldMaintenanceValues,
                    'equipment' => $oldEquipmentValues,
                ],
                [
                    'maintenance_record' => $maintenanceRecord->toArray(),
                    'equipment' => $maintenanceRecord->equipment->toArray(),
                ],
                'Updated maintenance record for equipment: '.$maintenanceRecord->equipment->equipment_code.' - '.$maintenanceRecord->equipment->name
            );
        });

        return redirect()
            ->route('equipment.show', $maintenanceRecord->equipment_id)
            ->with('success', 'Maintenance record and equipment status updated successfully.');
    }

    public function destroy(MaintenanceRecord $maintenanceRecord)
    {
        $equipment = $maintenanceRecord->equipment;

        if ($this->cannotReceiveMaintenance($equipment)) {
            return $this->blocked($equipment);
        }

        $oldValues = $maintenanceRecord->toArray();
        $maintenanceRecordId = $maintenanceRecord->id;

        $maintenanceRecord->delete();

        AuditLogger::log(
            'DELETE',
            'maintenance_records',
            $maintenanceRecordId,
            $oldValues,
            null,
            'Deleted maintenance record for equipment: '.$equipment->equipment_code.' - '.$equipment->name
        );

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Maintenance record deleted successfully.');
    }

    public function due()
    {
        $equipment = Equipment::query()
            ->with(['category', 'department', 'location'])
            ->dueForMaintenance()
            ->withNextMaintenanceDate()
            ->orderBy('next_maintenance_date')
            ->paginate(15);

        return view('maintenance.due', compact('equipment'));
    }

    private function cannotReceiveMaintenance(Equipment $equipment): bool
    {
        return $equipment->isDisposed() || $equipment->isArchived();
    }

    private function blocked(Equipment $equipment)
    {
        return redirect()
            ->route('equipment.show', $equipment)
            ->with('error', 'This equipment cannot receive maintenance records in its current state.');
    }
}
