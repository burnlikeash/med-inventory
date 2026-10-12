@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')
@section('page-subtitle', 'Track important actions performed within the system')

@section('content')
<div class="card">
    <form action="{{ route('audit.index') }}" method="GET">
        <div class="filter-grid">
            <div class="form-group">
                <label for="action">Action</label>
                <select id="action" name="action">
                    <option value="">All Actions</option>
                    @foreach (['CREATE', 'UPDATE', 'DELETE', 'ARCHIVE', 'RESTORE', 'MOVE', 'MAINTENANCE', 'DISPOSAL'] as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="table_name">Table</label>
                <select id="table_name" name="table_name">
                    <option value="">All Tables</option>
                    @foreach (['equipment', 'equipment_movements', 'maintenance_records', 'disposal_records'] as $table)
                        <option value="{{ $table }}" @selected(request('table_name') === $table)>{{ $table }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="filter-actions">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('audit.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Date / Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Table</th>
                <th>Record ID</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td>{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                    <td>{{ $log->user?->name ?? 'System / Unknown' }}</td>
                    <td>{{ $log->action }}</td>
                    <td>{{ $log->table_name }}</td>
                    <td>{{ $log->record_id ?? 'N/A' }}</td>
                    <td>{{ $log->description ?? 'N/A' }}</td>
                    <td><a href="{{ route('audit.show', $log) }}" class="btn btn-secondary">View</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">No audit logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $logs->links() }}
@endsection
