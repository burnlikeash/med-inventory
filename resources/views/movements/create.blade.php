@extends('layouts.app')

@section('title', 'Move Equipment')
@section('page-title', 'Move Equipment')

@section('content')
<div class="card">
    <div class="panel-note">
        <strong>Equipment:</strong>
        {{ $equipment->equipment_code }} - {{ $equipment->name }}<br>
        <strong>Current Department:</strong>
        {{ $equipment->department?->name ?? 'Unassigned' }}<br>
        <strong>Current Location:</strong>
        {{ $equipment->location?->name ?? 'Unassigned' }}
    </div>

    <form action="{{ route('equipment.move.store', $equipment) }}" method="POST">
        @csrf

        <div class="form-stack">
            <div class="form-group">
                <label for="department_id">Destination Department *</label>
                <select id="department_id" name="department_id" required>
                    <option value="">Select Department</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="location_id">Destination Location *</label>
                <select id="location_id" name="location_id" required>
                    <option value="">Select Location</option>
                    @foreach ($locations as $location)
                        <option
                            value="{{ $location->id }}"
                            data-department="{{ $location->department_id }}"
                            @selected(old('location_id') == $location->id)
                        >
                            {{ $location->name }} - {{ $location->department?->name }}
                        </option>
                    @endforeach
                </select>
                @error('location_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="reason">Movement Reason *</label>
                <input
                    type="text"
                    id="reason"
                    name="reason"
                    value="{{ old('reason') }}"
                    placeholder="Example: Transferred to Radiology"
                    required
                >
                @error('reason') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Move Equipment</button>
            <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
