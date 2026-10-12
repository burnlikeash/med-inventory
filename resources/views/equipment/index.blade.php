@extends('layouts.app')

@section('title', 'Equipment Inventory')
@section('page-title', 'Equipment Inventory')
@section('page-subtitle', 'Active equipment records')

@section('page-actions')
    @if (auth()->user()->canManageInventory())
        <a href="{{ route('equipment.create') }}" class="btn btn-primary">Add Equipment</a>
    @endif
@endsection

@section('content')
<div class="card">
    <h2>Scan Equipment Barcode</h2>
    <form action="{{ route('equipment.scan') }}" method="GET">
        <div class="scan-row">
            <input
                type="text"
                id="barcode_scan"
                name="barcode"
                placeholder="Click here, then scan barcode"
                autocomplete="off"
                autofocus
                required
            >
            <button type="submit" class="btn btn-primary">Find Equipment</button>
        </div>
        <span class="help-text">Most USB barcode scanners behave like a keyboard and automatically press Enter after scanning.</span>
    </form>
</div>

<div class="card">
    <form action="{{ route('equipment.index') }}" method="GET">
        <div class="filter-grid">
            <div class="form-group">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Code, name, barcode, serial...">
            </div>
            <div class="form-group">
                <label for="department_id">Department</label>
                <select id="department_id" name="department_id">
                    <option value="">All Departments</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" @selected(request('department_id') == $department->id)>{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="status">Operational Status</label>
                <select id="status" name="status">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\Equipment::OPERATIONAL_STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="condition_status">Condition</label>
                <select id="condition_status" name="condition_status">
                    <option value="">All Conditions</option>
                    @foreach (\App\Models\Equipment::CONDITIONS as $condition)
                        <option value="{{ $condition }}" @selected(request('condition_status') === $condition)>{{ $condition }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="lifecycle_status">Lifecycle</label>
                <select id="lifecycle_status" name="lifecycle_status">
                    <option value="">All Lifecycle Statuses</option>
                    @foreach (\App\Models\Equipment::LIFECYCLE_STATUSES as $lifecycleStatus)
                        <option value="{{ $lifecycleStatus }}" @selected(request('lifecycle_status') === $lifecycleStatus)>{{ $lifecycleStatus }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="result-count">
    Showing {{ $equipment->count() }} of {{ $equipment->total() }} matching equipment records.
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Department</th>
                <th>Location</th>
                <th>Status</th>
                <th>Condition</th>
                <th>Lifecycle</th>
                <th>Next Maintenance</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipment as $item)
                @php $nextDate = $item->nextMaintenanceOn(); @endphp
                <tr>
                    <td>{{ $item->equipment_code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category?->name ?? 'N/A' }}</td>
                    <td>{{ $item->brand ?? 'N/A' }}</td>
                    <td>{{ $item->department?->name ?? 'Unassigned' }}</td>
                    <td>{{ $item->location?->name ?? 'Unassigned' }}</td>
                    <td><x-status-badge :value="$item->status" /></td>
                    <td><x-status-badge :value="$item->condition_status" /></td>
                    <td><x-status-badge :value="$item->lifecycle_status" /></td>
                    <td>
                        @if (! $nextDate)
                            <span class="text-muted">Not scheduled</span>
                        @elseif ($nextDate->isPast())
                            <span class="text-overdue">Overdue: {{ $nextDate->format('M d, Y') }}</span>
                        @else
                            {{ $nextDate->format('M d, Y') }}
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('equipment.show', $item) }}" class="btn btn-secondary">View</a>
                            @if (auth()->user()->canManageInventory())
                                <a href="{{ route('equipment.edit', $item) }}" class="btn btn-secondary">Edit</a>
                                <form action="{{ route('equipment.destroy', $item) }}" method="POST" onsubmit="return confirm('Archive this equipment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Archive</button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="empty-state">No equipment records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $equipment->links() }}
@endsection
