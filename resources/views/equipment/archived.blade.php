@extends('layouts.app')

@section('title', 'Archived Equipment')
@section('page-title', 'Archived Equipment')
@section('page-subtitle', 'Equipment removed from the active inventory')

@section('content')
<div class="card">
    <form method="GET" action="{{ route('equipment.archived') }}">
        <div class="scan-row">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search archived equipment..."
            >
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('equipment.archived') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Department</th>
                <th>Location</th>
                <th>Archived At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipment as $item)
                <tr>
                    <td>{{ $item->equipment_code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category?->name ?? 'N/A' }}</td>
                    <td>{{ $item->department?->name ?? 'Unassigned' }}</td>
                    <td>{{ $item->location?->name ?? 'Unassigned' }}</td>
                    <td>{{ $item->archived_at?->format('M d, Y h:i A') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('equipment.show', $item) }}" class="btn btn-secondary">View</a>
                            <form
                                action="{{ route('equipment.restore', $item) }}"
                                method="POST"
                                onsubmit="return confirm('Restore this equipment?');"
                            >
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success">Restore</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">No archived equipment found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $equipment->links() }}
@endsection
