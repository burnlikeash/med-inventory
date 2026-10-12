@extends('layouts.app')

@section('title', 'Add Equipment')
@section('page-title', 'Add Equipment')
@section('page-subtitle', 'Register a new equipment record')

@section('content')
<div class="card">
    <form action="{{ route('equipment.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label for="equipment_code">Equipment Code *</label>
                <input type="text" id="equipment_code" name="equipment_code" value="{{ old('equipment_code') }}" required>
                @error('equipment_code') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="barcode">Barcode</label>
                <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $scannedBarcode ?? '') }}" placeholder="Scan or type barcode">
                @error('barcode') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="name">Equipment Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" value="{{ old('brand') }}">
            </div>

            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" value="{{ old('model') }}">
            </div>

            <div class="form-group">
                <label for="serial_number">Serial Number</label>
                <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number') }}">
            </div>

            <div class="form-group">
                <label for="property_number">Property Number</label>
                <input type="text" id="property_number" name="property_number" value="{{ old('property_number') }}">
            </div>

            <div class="form-group">
                <label for="purchase_date">Purchase Date</label>
                <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}">
            </div>

            <div class="form-group">
                <label for="purchase_price">Purchase Price</label>
                <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" step="0.01" min="0">
            </div>

            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" id="supplier" name="supplier" value="{{ old('supplier') }}">
            </div>

            <div class="form-group">
                <label for="warranty_expiry">Warranty Expiry</label>
                <input type="date" id="warranty_expiry" name="warranty_expiry" value="{{ old('warranty_expiry') }}">
            </div>

            <div class="form-group">
                <label for="status">Operational Status *</label>
                <select id="status" name="status" required>
                    @foreach (\App\Models\Equipment::OPERATIONAL_STATUSES as $status)
                        <option value="{{ $status }}" @selected(old('status', 'Working') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="condition_status">Condition *</label>
                <select id="condition_status" name="condition_status" required>
                    @foreach (\App\Models\Equipment::CONDITIONS as $condition)
                        <option value="{{ $condition }}" @selected(old('condition_status', 'Good') === $condition)>{{ $condition }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="lifecycle_status">Lifecycle Status *</label>
                <select id="lifecycle_status" name="lifecycle_status" required>
                    @foreach (\App\Models\Equipment::EDITABLE_LIFECYCLE_STATUSES as $lifecycleStatus)
                        <option value="{{ $lifecycleStatus }}" @selected(old('lifecycle_status', 'Active') === $lifecycleStatus)>
                            {{ $lifecycleStatus }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="department_id">Department</label>
                <select id="department_id" name="department_id">
                    <option value="">Unassigned</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(old('department_id') == $department->id)>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="location_id">Location</label>
                <select id="location_id" name="location_id">
                    <option value="">Unassigned</option>
                    @foreach ($locations as $location)
                        <option
                            value="{{ $location->id }}"
                            data-department="{{ $location->department_id }}"
                            @selected(old('location_id') == $location->id)
                        >
                            {{ $location->name }}
                            @if ($location->department)
                                - {{ $location->department->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('location_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group full-width">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Equipment</button>
            <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
