@extends('layouts.app')

@section('title', 'Maintenance Report')
@section('page-title', 'Maintenance Report')
@section('page-subtitle', 'Generated: '.now()->format('M d, Y h:i A'))

@section('page-actions')
    <button type="button" class="btn btn-secondary no-print" onclick="window.print()">Print</button>
@endsection

@section('content')
<div class="card no-print">
    <form action="{{ route('reports.maintenance') }}" method="GET">
        <div class="filter-grid">
            <div class="form-group">
                <label for="maintenance_type">Maintenance Type</label>
                <select id="maintenance_type" name="maintenance_type">
                    <option value="">All Maintenance Types</option>
                    @foreach (\App\Models\MaintenanceRecord::TYPES as $type)
                        <option value="{{ $type }}" @selected(request('maintenance_type') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="date_from">From</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="form-group">
                <label for="date_to">To</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}">
            </div>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="{{ route('reports.maintenance') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="result-count">Total records: <strong>{{ $records->count() }}</strong></div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Equipment</th>
                <th>Department</th>
                <th>Type</th>
                <th>Performed By</th>
                <th>Result</th>
                <th>Cost</th>
                <th>Next Maintenance</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->maintenance_date?->format('M d, Y') }}</td>
                    <td>{{ $record->equipment?->equipment_code }} - {{ $record->equipment?->name }}</td>
                    <td>{{ $record->equipment?->department?->name ?? 'Unassigned' }}</td>
                    <td>{{ $record->maintenance_type }}</td>
                    <td>{{ $record->performed_by ?? 'N/A' }}</td>
                    <td>{{ $record->result ?? 'N/A' }}</td>
                    <td>
                        @if ($record->cost)
                            ₱{{ number_format((float) $record->cost, 2) }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ $record->next_maintenance_date?->format('M d, Y') ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">No maintenance records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
