@extends('layouts.app')

@section('title', 'Locations')
@section('page-title', 'Locations')

@section('page-actions')
    @if (auth()->user()->canManageInventory())
        <a href="{{ route('locations.create') }}" class="btn btn-primary">Add Location</a>
    @endif
@endsection

@section('content')
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Description</th>
                <th>Equipment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($locations as $location)
                <tr>
                    <td>{{ $location->name }}</td>
                    <td>{{ $location->department?->name ?? 'N/A' }}</td>
                    <td>{{ $location->description ?? 'N/A' }}</td>
                    <td>{{ $location->equipment_count }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('locations.show', $location) }}" class="btn btn-secondary">View</a>
                            @if (auth()->user()->canManageInventory())
                                <a href="{{ route('locations.edit', $location) }}" class="btn btn-secondary">Edit</a>
                                @if ($location->equipment_count === 0)
                                    <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Delete this location?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-state">No locations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $locations->links() }}
@endsection
