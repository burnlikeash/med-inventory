@extends('layouts.app')

@section('title', 'Edit Equipment')
@section('page-title', 'Edit Equipment')
@section('page-subtitle', $equipment->equipment_code.' — '.$equipment->name)

@section('content')
<div class="card">
    <p class="muted">Department and location are changed through Move Equipment so movement history stays complete.</p>

    <form action="{{ route('equipment.update', $equipment) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label for="equipment_code">Equipment Code *</label>
                <input type="text" id="equipment_code" name="equipment_code" value="{{ old('equipment_code', $equipment->equipment_code) }}" required>
                @error('equipment_code') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="barcode">Barcode</label>
                <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $equipment->barcode) }}">
                @error('barcode') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="name">Equipment Name *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $equipment->name) }}" required>
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $equipment->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" id="brand" name="brand" value="{{ old('brand', $equipment->brand) }}">
            </div>

            <div class="form-group">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" value="{{ old('model', $equipment->model) }}">
            </div>

            <div class="form-group">
                <label for="serial_number">Serial Number</label>
                <input type="text" id="serial_number" name="serial_number" value="{{ old('serial_number', $equipment->serial_number) }}">
            </div>

            <div class="form-group">
                <label for="property_number">Property Number</label>
                <input type="text" id="property_number" name="property_number" value="{{ old('property_number', $equipment->property_number) }}">
            </div>

            <div class="form-group">
                <label for="purchase_date">Purchase Date</label>
                <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', optional($equipment->purchase_date)->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label for="purchase_price">Purchase Price</label>
                <input type="number" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $equipment->purchase_price) }}" step="0.01" min="0">
            </div>

            <div class="form-group">
                <label for="supplier">Supplier</label>
                <input type="text" id="supplier" name="supplier" value="{{ old('supplier', $equipment->supplier) }}">
            </div>

            <div class="form-group">
                <label for="warranty_expiry">Warranty Expiry</label>
                <input type="date" id="warranty_expiry" name="warranty_expiry" value="{{ old('warranty_expiry', optional($equipment->warranty_expiry)->format('Y-m-d')) }}">
            </div>

            <div class="form-group">
                <label for="status">Operational Status *</label>
                <select id="status" name="status" required>
                    @foreach (\App\Models\Equipment::OPERATIONAL_STATUSES as $status)
                        <option value="{{ $status }}" @selected(old('status', $equipment->status) === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="condition_status">Condition *</label>
                <select id="condition_status" name="condition_status" required>
                    @foreach (\App\Models\Equipment::CONDITIONS as $condition)
                        <option value="{{ $condition }}" @selected(old('condition_status', $equipment->condition_status) === $condition)>
                            {{ $condition }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="lifecycle_status">Lifecycle Status *</label>
                <select id="lifecycle_status" name="lifecycle_status" required>
                    @foreach (\App\Models\Equipment::EDITABLE_LIFECYCLE_STATUSES as $lifecycleStatus)
                        <option value="{{ $lifecycleStatus }}" @selected(old('lifecycle_status', $equipment->lifecycle_status) === $lifecycleStatus)>
                            {{ $lifecycleStatus }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group full-width">
                <label for="remarks">Remarks</label>
                <textarea id="remarks" name="remarks">{{ old('remarks', $equipment->remarks) }}</textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Equipment</button>
            <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
