@extends('layouts.app')

@section('title', 'Edit Department')
@section('page-title', 'Edit Department')

@section('content')
<div class="card">
    <form action="{{ route('departments.update', $department) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-stack">
        <div class="form-group">
            <label for="name">Department Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $department->name) }}" required>
            @error('name') <div class="field-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-stack">
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $department->description) }}</textarea>
        </div>

        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Department</button>
            <a href="{{ route('departments.show', $department) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
