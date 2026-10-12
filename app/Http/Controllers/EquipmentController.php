<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEquipmentRequest;
use App\Http\Requests\UpdateEquipmentRequest;
use App\Models\Department;
use App\Models\Equipment;
use App\Models\EquipmentCategory;
use App\Models\Location;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class EquipmentController extends Controller
{
    public function index(Request $request)
    {
        $equipment = Equipment::query()
            ->select('equipment.*')
            ->with(['category', 'department', 'location'])
            ->withNextMaintenanceDate()
            ->active()
            ->search($request->search)
            ->filter($request->only([
                'department_id',
                'category_id',
                'status',
                'condition_status',
                'lifecycle_status',
            ]))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('equipment.index', [
            'equipment' => $equipment,
            'departments' => Department::orderBy('name')->get(),
            'categories' => EquipmentCategory::orderBy('name')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('equipment.create', [
            'categories' => EquipmentCategory::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'locations' => Location::with('department')->orderBy('name')->get(),
            'scannedBarcode' => $request->query('barcode'),
        ]);
    }

    public function store(StoreEquipmentRequest $request)
    {
        $equipment = Equipment::create($request->validated());

        AuditLogger::log(
            'CREATE',
            'equipment',
            $equipment->id,
            null,
            $equipment->toArray(),
            'Created equipment: '.$equipment->equipment_code.' - '.$equipment->name
        );

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment added successfully.');
    }

    public function show(Equipment $equipment)
    {
        $equipment->load([
            'category',
            'department',
            'location',
            'movements' => fn ($query) => $query->latest('movement_date'),
            'movements.fromDepartment',
            'movements.toDepartment',
            'movements.fromLocation',
            'movements.toLocation',
            'movements.movedBy',
            'maintenanceRecords' => fn ($query) => $query->latest('maintenance_date'),
            'disposalRecord.approvedBy',
        ]);

        return view('equipment.show', compact('equipment'));
    }

    public function edit(Equipment $equipment)
    {
        if ($equipment->isDisposed() || $equipment->isArchived()) {
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('error', 'This equipment cannot be edited in its current state.');
        }

        return view('equipment.edit', [
            'equipment' => $equipment,
            'categories' => EquipmentCategory::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateEquipmentRequest $request, Equipment $equipment)
    {
        if ($equipment->isDisposed() || $equipment->isArchived()) {
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('error', 'This equipment cannot be edited in its current state.');
        }

        $oldValues = $equipment->toArray();
        $equipment->update($request->validated());
        $equipment->refresh();

        AuditLogger::log(
            'UPDATE',
            'equipment',
            $equipment->id,
            $oldValues,
            $equipment->toArray(),
            'Updated equipment: '.$equipment->equipment_code.' - '.$equipment->name
        );

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        if ($equipment->isArchived()) {
            return back()->with('error', 'This equipment is already archived.');
        }

        $oldValues = $equipment->toArray();

        $equipment->update(['archived_at' => now()]);
        $equipment->refresh();

        AuditLogger::log(
            'ARCHIVE',
            'equipment',
            $equipment->id,
            $oldValues,
            $equipment->toArray(),
            'Archived equipment: '.$equipment->equipment_code.' - '.$equipment->name
        );

        return redirect()
            ->route('equipment.index')
            ->with('success', 'Equipment archived successfully.');
    }

    public function scan(Request $request)
    {
        $validated = $request->validate([
            'barcode' => 'required|string|max:255',
        ]);

        $equipment = Equipment::where('barcode', $validated['barcode'])->first();

        if ($equipment) {
            $redirect = redirect()->route('equipment.show', $equipment);

            if ($equipment->isArchived()) {
                $redirect->with('info', 'This equipment is archived.');
            }

            return $redirect;
        }

        if (auth()->user()->canManageInventory()) {
            return redirect()
                ->route('equipment.create', ['barcode' => $validated['barcode']])
                ->with('info', 'No equipment was found for this barcode. You can register it as new equipment.');
        }

        return back()->with('error', 'No equipment was found for this barcode.');
    }

    public function archived(Request $request)
    {
        $equipment = Equipment::query()
            ->with(['category', 'department', 'location'])
            ->archived()
            ->search($request->search)
            ->orderByDesc('archived_at')
            ->paginate(15)
            ->withQueryString();

        return view('equipment.archived', compact('equipment'));
    }

    public function restore(Equipment $equipment)
    {
        if (! $equipment->isArchived()) {
            return back()->with('error', 'This equipment is not archived.');
        }

        $oldValues = $equipment->toArray();
        $equipment->update(['archived_at' => null]);
        $equipment->refresh();

        AuditLogger::log(
            'RESTORE',
            'equipment',
            $equipment->id,
            $oldValues,
            $equipment->toArray(),
            'Restored archived equipment: '.$equipment->equipment_code.' - '.$equipment->name
        );

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment restored successfully.');
    }
}
