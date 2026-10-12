<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisposalRecordRequest;
use App\Models\Equipment;
use App\Models\DisposalRecord;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;

class DisposalRecordController extends Controller
{
    public function index()
    {
        $forDisposal = Equipment::with(['category', 'department', 'location'])
            ->active()
            ->where('lifecycle_status', 'For Disposal')
            ->orderBy('name')
            ->paginate(15, ['*'], 'pending_page');

        $disposed = Equipment::with(['category', 'department', 'location', 'disposalRecord.approvedBy'])
            ->active()
            ->where('lifecycle_status', 'Disposed')
            ->orderByDesc('updated_at')
            ->paginate(15, ['*'], 'disposed_page');

        return view('disposal.index', compact('forDisposal', 'disposed'));
    }

    public function create(Equipment $equipment)
    {
        if ($redirect = $this->ensureReadyForDisposal($equipment)) {
            return $redirect;
        }

        return view('disposal.create', compact('equipment'));
    }

    public function store(DisposalRecordRequest $request, Equipment $equipment)
    {
        if ($redirect = $this->ensureReadyForDisposal($equipment)) {
            return $redirect;
        }

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $equipment) {
            $oldEquipmentValues = $equipment->toArray();

            $disposalRecord = DisposalRecord::create([
                'equipment_id' => $equipment->id,
                'reason' => $validated['reason'],
                'disposal_date' => $validated['disposal_date'],
                'approved_by' => auth()->id(),
                'disposal_method' => $validated['disposal_method'] ?? null,
                'disposal_reference' => $validated['disposal_reference'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $equipment->update([
                'lifecycle_status' => 'Disposed',
                'status' => 'Not Working',
            ]);
            $equipment->refresh();

            AuditLogger::log(
                'DISPOSAL',
                'disposal_records',
                $disposalRecord->id,
                $oldEquipmentValues,
                [
                    'disposal_record' => $disposalRecord->toArray(),
                    'equipment' => $equipment->toArray(),
                ],
                'Disposed equipment: '.$equipment->equipment_code.' - '.$equipment->name
            );
        });

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Equipment disposal recorded successfully.');
    }

    public function edit(DisposalRecord $disposalRecord)
    {
        $disposalRecord->load('equipment');

        if ($redirect = $this->ensureDisposalRecordEditable($disposalRecord)) {
            return $redirect;
        }

        return view('disposal.edit', compact('disposalRecord'));
    }

    public function update(DisposalRecordRequest $request, DisposalRecord $disposalRecord)
    {
        $disposalRecord->load('equipment');

        if ($redirect = $this->ensureDisposalRecordEditable($disposalRecord)) {
            return $redirect;
        }

        $oldValues = $disposalRecord->toArray();
        $disposalRecord->update($request->validated());
        $disposalRecord->refresh();

        AuditLogger::log(
            'UPDATE',
            'disposal_records',
            $disposalRecord->id,
            $oldValues,
            $disposalRecord->toArray(),
            'Updated disposal record for equipment: '.$disposalRecord->equipment->equipment_code.' - '.$disposalRecord->equipment->name
        );

        return redirect()
            ->route('equipment.show', $disposalRecord->equipment_id)
            ->with('success', 'Disposal record updated successfully.');
    }

    public function destroy(DisposalRecord $disposalRecord)
    {
        $equipment = $disposalRecord->equipment;

        if ($redirect = $this->ensureDisposalRecordEditable($disposalRecord)) {
            return $redirect;
        }

        $oldDisposalValues = $disposalRecord->toArray();
        $oldEquipmentValues = $equipment->toArray();
        $disposalRecordId = $disposalRecord->id;

        DB::transaction(function () use (
            $disposalRecord,
            $equipment,
            $oldDisposalValues,
            $oldEquipmentValues,
            $disposalRecordId
        ) {
            $disposalRecord->delete();
            $equipment->update(['lifecycle_status' => 'For Disposal']);
            $equipment->refresh();

            AuditLogger::log(
                'DELETE',
                'disposal_records',
                $disposalRecordId,
                [
                    'disposal_record' => $oldDisposalValues,
                    'equipment' => $oldEquipmentValues,
                ],
                [
                    'disposal_record' => null,
                    'equipment' => $equipment->toArray(),
                ],
                'Removed disposal record for equipment: '.$equipment->equipment_code.' - '.$equipment->name
            );
        });

        return redirect()
            ->route('equipment.show', $equipment)
            ->with('success', 'Disposal record removed successfully.');
    }

    private function ensureDisposalRecordEditable(DisposalRecord $disposalRecord)
    {
        if ($disposalRecord->equipment->isArchived()) {
            return redirect()
                ->route('equipment.show', $disposalRecord->equipment)
                ->with('error', 'Restore archived equipment before changing its disposal record.');
        }

        return null;
    }

    private function ensureReadyForDisposal(Equipment $equipment)
    {
        if ($equipment->isArchived()) {
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('error', 'Archived equipment cannot be disposed until it is restored.');
        }

        if ($equipment->disposalRecord) {
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('error', 'This equipment already has a disposal record.');
        }

        if ($equipment->lifecycle_status !== 'For Disposal') {
            return redirect()
                ->route('equipment.show', $equipment)
                ->with('error', 'Equipment must first be marked as "For Disposal" before a disposal record can be created.');
        }

        return null;
    }
}
