@extends('layouts.app')

@section('title', 'Edit Disposal Record')
@section('page-title', 'Edit Disposal Record')

@section('content')
<div class="card">
    <div class="panel-note">
        <strong>Equipment:</strong>
        {{ $disposalRecord->equipment->equipment_code }} - {{ $disposalRecord->equipment->name }}
    </div>

    <form action="{{ route('disposal.update', $disposalRecord) }}" method="POST">
        @csrf
        @method('PUT')
        @include('disposal._form', ['record' => $disposalRecord])
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Disposal Record</button>
            <a href="{{ route('equipment.show', $disposalRecord->equipment) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
