@extends('layouts.app')

@section('title', 'Add Location')
@section('page-title', 'Add Location')

@section('content')
<div class="card">
    <form action="{{ route('locations.store') }}" method="POST">
        @csrf

        <div class="form-stack">
        <div class="form-group">
            <label for="department_id">Department *</label>
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

        <div class="form-stack">
        <div class="form-group">
            <label for="name">Location Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-stack">
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
        </div>

        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Location</button>
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
