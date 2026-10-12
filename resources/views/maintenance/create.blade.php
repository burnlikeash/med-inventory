@extends('layouts.app')

@section('title', 'Add Maintenance Record')
@section('page-title', 'Add Maintenance Record')

@section('content')
<div class="card">
    <div class="panel-note">
        <strong>Equipment:</strong>
        {{ $equipment->equipment_code }} - {{ $equipment->name }}
    </div>

    <form action="{{ route('maintenance.store', $equipment) }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label for="maintenance_type">Maintenance Type *</label>
                <select id="maintenance_type" name="maintenance_type" required>
                    <option value="">Select Type</option>
                    @foreach (\App\Models\MaintenanceRecord::TYPES as $type)
                        <option value="{{ $type }}" @selected(old('maintenance_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
                @error('maintenance_type') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="equipment_status">Equipment Status After Maintenance *</label>
                <select id="equipment_status" name="equipment_status" required>
                    @foreach (\App\Models\Equipment::OPERATIONAL_STATUSES as $status)
                        <option value="{{ $status }}" @selected(old('equipment_status', $equipment->status) === $status)>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="maintenance_date">Maintenance Date *</label>
                <input type="date" id="maintenance_date" name="maintenance_date" value="{{ old('maintenance_date', now()->format('Y-m-d')) }}" required>
                @error('maintenance_date') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="performed_by">Performed By</label>
                <input type="text" id="performed_by" name="performed_by" value="{{ old('performed_by') }}" placeholder="Technician or service provider">
            </div>

            <div class="form-group">
                <label for="cost">Cost</label>
                <input type="number" id="cost" name="cost" value="{{ old('cost') }}" min="0" step="0.01">
            </div>

            <div class="form-group">
                <label for="result">Result</label>
                <input type="text" id="result" name="result" value="{{ old('result') }}" placeholder="Example: Passed Inspection">
            </div>

            <div class="form-group">
                <label for="next_maintenance_date">Next Maintenance Date</label>
                <input type="date" id="next_maintenance_date" name="next_maintenance_date" value="{{ old('next_maintenance_date') }}">
                @error('next_maintenance_date') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group full-width">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                @error('description') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group full-width">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Maintenance Record</button>
            <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
