@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments')

@section('page-actions')
    @if (auth()->user()->canManageInventory())
        <a href="{{ route('departments.create') }}" class="btn btn-primary">Add Department</a>
    @endif
@endsection

@section('content')
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Locations</th>
                <th>Equipment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($departments as $department)
                <tr>
                    <td>{{ $department->name }}</td>
                    <td>{{ $department->description ?? 'N/A' }}</td>
                    <td>{{ $department->locations_count }}</td>
                    <td>{{ $department->equipment_count }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('departments.show', $department) }}" class="btn btn-secondary">View</a>
                            @if (auth()->user()->canManageInventory())
                                <a href="{{ route('departments.edit', $department) }}" class="btn btn-secondary">Edit</a>
                                @if ($department->locations_count === 0 && $department->equipment_count === 0)
                                    <form action="{{ route('departments.destroy', $department) }}" method="POST" onsubmit="return confirm('Delete this department?');">
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
                    <td colspan="5" class="empty-state">No departments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $departments->links() }}
@endsection
