@extends('layouts.app')

@section('title', $department->name)
@section('page-title', $department->name)
@section('page-subtitle', $department->description ?? 'No description.')

@section('page-actions')
    @if (auth()->user()->canManageInventory())
        <a href="{{ route('departments.edit', $department) }}" class="btn btn-primary">Edit</a>
    @endif
    <a href="{{ route('departments.index') }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
<div class="card">
    <h2>Locations</h2>
    @if ($locations->count())
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($locations as $location)
                    <tr>
                        <td><a href="{{ route('locations.show', $location) }}">{{ $location->name }}</a></td>
                        <td>{{ $location->description ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">No locations assigned to this department.</div>
    @endif
</div>

<div class="card">
    <h2>Assigned Equipment</h2>
    @if ($equipment->count())
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipment as $item)
                    <tr>
                        <td>{{ $item->equipment_code }}</td>
                        <td><a href="{{ route('equipment.show', $item) }}">{{ $item->name }}</a></td>
                        <td>{{ $item->category?->name ?? 'N/A' }}</td>
                        <td>{{ $item->location?->name ?? 'Unassigned' }}</td>
                        <td><x-status-badge :value="$item->status" /></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $equipment->links() }}
    @else
        <div class="empty-state">No active equipment assigned to this department.</div>
    @endif
</div>
@endsection
