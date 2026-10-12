@extends('layouts.app')

@section('title', 'Dispose Equipment')
@section('page-title', 'Dispose Equipment')

@section('content')
<div class="card">
    <div class="panel-note">
        <strong>Equipment:</strong>
        {{ $equipment->equipment_code }} - {{ $equipment->name }}<br>
        <strong>Current Lifecycle:</strong>
        {{ $equipment->lifecycle_status }}
    </div>

    <div class="alert alert-warning">
        Saving this record will change the equipment lifecycle to <strong>Disposed</strong>.
        The equipment record itself will remain in the database.
    </div>

    <form action="{{ route('disposal.store', $equipment) }}" method="POST">
        @csrf
        @include('disposal._form', ['record' => null])
        <div class="form-actions">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Confirm disposal of this equipment?');">
                Confirm Disposal
            </button>
            <a href="{{ route('equipment.show', $equipment) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
