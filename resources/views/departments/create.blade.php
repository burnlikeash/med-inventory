@extends('layouts.app')

@section('title', 'Add Department')
@section('page-title', 'Add Department')

@section('content')
<div class="card">
    <form action="{{ route('departments.store') }}" method="POST">
        @csrf

        <div class="form-stack">
        <div class="form-group">
            <label for="name">Department Name *</label>
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
            <button type="submit" class="btn btn-primary">Save Department</button>
            <a href="{{ route('departments.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
