@extends('layouts.app')

@section('title', 'Disposal Management')
@section('page-title', 'Disposal Management')
@section('page-subtitle', 'Equipment awaiting disposal and completed disposal records')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">For Disposal</div>
        <div class="stat-number">{{ $forDisposal->total() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Disposed</div>
        <div class="stat-number">{{ $disposed->total() }}</div>
    </div>
</div>

<div class="card">
    <h2>Equipment For Disposal</h2>
    <div class="table-wrap table-wrap-borderless">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Equipment</th>
                    <th>Department</th>
                    <th>Location</th>
                    <th>Condition</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($forDisposal as $item)
                    <tr>
                        <td>{{ $item->equipment_code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->department?->name ?? 'Unassigned' }}</td>
                        <td>{{ $item->location?->name ?? 'Unassigned' }}</td>
                        <td><x-status-badge :value="$item->condition_status" /></td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('equipment.show', $item) }}" class="btn btn-secondary">View</a>
                                <a href="{{ route('disposal.create', $item) }}" class="btn btn-danger">Dispose</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">No equipment is currently marked for disposal.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $forDisposal->links() }}
</div>

<div class="card">
    <h2>Disposed Equipment</h2>
    <div class="table-wrap table-wrap-borderless">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Equipment</th>
                    <th>Disposal Date</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($disposed as $item)
                    <tr>
                        <td>{{ $item->equipment_code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->disposalRecord?->disposal_date?->format('M d, Y') ?? 'N/A' }}</td>
                        <td>{{ $item->disposalRecord?->disposal_method ?? 'N/A' }}</td>
                        <td>{{ $item->disposalRecord?->disposal_reference ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('equipment.show', $item) }}" class="btn btn-secondary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">No disposed equipment records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $disposed->links() }}
</div>
@endsection
