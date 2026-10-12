@extends('layouts.app')

@section('title', 'Equipment Categories')
@section('page-title', 'Equipment Categories')
@section('page-subtitle', 'Manage categories used for equipment records')

@section('page-actions')
    <a href="{{ route('categories.create') }}" class="btn btn-primary">Add Category</a>
@endsection

@section('content')
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Equipment Count</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description ?? 'N/A' }}</td>
                    <td>{{ $category->equipment_count }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary">Edit</a>
                            @if ($category->equipment_count === 0)
                                <form
                                    action="{{ route('categories.destroy', $category) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this category?');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty-state">No categories found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $categories->links() }}
@endsection
