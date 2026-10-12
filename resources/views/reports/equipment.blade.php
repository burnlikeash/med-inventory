@extends('layouts.app')

@section('title', 'Equipment Inventory Report')
@section('page-title', 'Equipment Inventory Report')
@section('page-subtitle', 'Generated: '.now()->format('M d, Y h:i A'))

@section('page-actions')
    <button type="button" class="btn btn-secondary no-print" onclick="window.print()">Print</button>
    <a href="{{ route('reports.equipment.csv', request()->query()) }}" class="btn btn-success no-print">Export CSV</a>
@endsection

@section('content')
<div class="card no-print">
    <form action="{{ route('reports.equipment') }}" method="GET">
        <div class="filter-grid">
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
        <div class="checkbox-row">
            <input type="checkbox" id="include_archived" name="include_archived" value="1" @checked(request('include_archived'))>
            <label for="include_archived">Include archived equipment</label>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="{{ route('reports.equipment') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="result-count">Total records: <strong>{{ $equipment->count() }}</strong></div>

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
                <th>Purchase Price</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipment as $item)
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
                        @if ($item->purchase_price)
                            ₱{{ number_format((float) $item->purchase_price, 2) }}
                        @else
                            N/A
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="empty-state">No equipment records match the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
