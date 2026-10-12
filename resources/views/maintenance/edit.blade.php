@extends('layouts.app')

@section('title', 'Edit Maintenance Record')
@section('page-title', 'Edit Maintenance Record')

@section('content')
<div class="card">
    <div class="panel-note">
        <strong>Equipment:</strong>
        {{ $maintenanceRecord->equipment->equipment_code }}
        -
        {{ $maintenanceRecord->equipment->name }}
    </div>

    <form action="{{ route('maintenance.update', $maintenanceRecord) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="maintenance_type">Maintenance Type *</label>
                <select id="maintenance_type" name="maintenance_type" required>
                    @foreach (\App\Models\MaintenanceRecord::TYPES as $type)
                        <option value="{{ $type }}" @selected(old('maintenance_type', $maintenanceRecord->maintenance_type) === $type)>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="equipment_status">Equipment Status After Maintenance *</label>
                <select id="equipment_status" name="equipment_status" required>
                    @foreach (\App\Models\Equipment::OPERATIONAL_STATUSES as $status)
                        <option value="{{ $status }}" @selected(old('equipment_status', $maintenanceRecord->equipment->status) === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="maintenance_date">Maintenance Date *</label>
                <input
                    type="date"
                    id="maintenance_date"
                    name="maintenance_date"
                    value="{{ old('maintenance_date', optional($maintenanceRecord->maintenance_date)->format('Y-m-d')) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="performed_by">Performed By</label>
                <input type="text" id="performed_by" name="performed_by" value="{{ old('performed_by', $maintenanceRecord->performed_by) }}">
            </div>

            <div class="form-group">
                <label for="cost">Cost</label>
                <input type="number" id="cost" name="cost" value="{{ old('cost', $maintenanceRecord->cost) }}" min="0" step="0.01">
            </div>

            <div class="form-group">
                <label for="result">Result</label>
                <input type="text" id="result" name="result" value="{{ old('result', $maintenanceRecord->result) }}">
            </div>

            <div class="form-group">
                <label for="next_maintenance_date">Next Maintenance Date</label>
                <input
                    type="date"
                    id="next_maintenance_date"
                    name="next_maintenance_date"
                    value="{{ old('next_maintenance_date', optional($maintenanceRecord->next_maintenance_date)->format('Y-m-d')) }}"
                >
            </div>

            <div class="form-group full-width">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required>{{ old('description', $maintenanceRecord->description) }}</textarea>
                @error('description') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group full-width">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks">{{ old('remarks', $maintenanceRecord->remarks) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Maintenance Record</button>
            <a href="{{ route('equipment.show', $maintenanceRecord->equipment) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
