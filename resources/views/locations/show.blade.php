@extends('layouts.app')

@section('title', $location->name)
@section('page-title', $location->name)
@section('page-subtitle', 'Department: '.($location->department?->name ?? 'N/A'))

@section('page-actions')
    @if (auth()->user()->canManageInventory())
        <a href="{{ route('locations.edit', $location) }}" class="btn btn-primary">Edit</a>
    @endif
    <a href="{{ route('locations.index') }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
@if ($location->description)
    <p class="muted">{{ $location->description }}</p>
@endif

<div class="card">
    <h2>Equipment Assigned to This Location</h2>
    @if ($equipment->count())
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Condition</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipment as $item)
                    <tr>
                        <td>{{ $item->equipment_code }}</td>
                        <td><a href="{{ route('equipment.show', $item) }}">{{ $item->name }}</a></td>
                        <td>{{ $item->category?->name ?? 'N/A' }}</td>
                        <td><x-status-badge :value="$item->status" /></td>
                        <td><x-status-badge :value="$item->condition_status" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $equipment->links() }}
    @else
        <div class="empty-state">No active equipment is currently assigned to this location.</div>
    @endif
</div>
@endsection
