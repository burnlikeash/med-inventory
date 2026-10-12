<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentMovementRequest;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentMovement;
use App\Models\Location;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class EquipmentMovementController extends Controller
{
    public function create(Equipment $equipment)
    {
        if ($equipment->isDisposed() || $equipment->isArchived()) {
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('error', 'This equipment cannot be moved in its current state.');
        }

        return view('movements.create', [
            'equipment' => $equipment,
            'departments' => Department::orderBy('name')->get(),
            'locations' => Location::with('department')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreEquipmentMovementRequest $request, Equipment $equipment)
    {
        if ($equipment->isDisposed() || $equipment->isArchived()) {
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('error', 'This equipment cannot be moved in its current state.');
        }

        $validated = $request->validated();

        DB::transaction(function () use ($equipment, $validated) {
            $oldEquipmentValues = $equipment->toArray();

            $movement = EquipmentMovement::create([
                'equipment_id' => $equipment->id,
                'from_department_id' => $equipment->department_id,
                'from_location_id' => $equipment->location_id,
                'to_department_id' => $validated['department_id'],
                'to_location_id' => $validated['location_id'],
                'movement_date' => now(),
                'reason' => $validated['reason'],
                'remarks' => $validated['remarks'] ?? null,
                'moved_by' => auth()->id(),
            ]);

            $equipment->update([
                'department_id' => $validated['department_id'],
                'location_id' => $validated['location_id'],
            ]);
            $equipment->refresh();

            AuditLogger::log(
                'MOVE',
                'equipment_movements',
                $movement->id,
                $oldEquipmentValues,
                $equipment->toArray(),
                'Moved equipment: '.$equipment->equipment_code.' - '.$equipment->name.'. Reason: '.$validated['reason']
            );
        });

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment moved successfully and movement history recorded.');
    }
}
