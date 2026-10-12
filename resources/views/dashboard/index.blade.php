@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Operational overview for the current inventory')

@section('content')
@php $user = auth()->user(); @endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Total Active Equipment</div>
        <div class="stat-number"><a href="{{ route('equipment.index') }}">{{ $totalEquipment }}</a></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Working</div>
        <div class="stat-number"><a href="{{ route('equipment.index', ['status' => 'Working']) }}">{{ $workingEquipment }}</a></div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Needs Attention</div>
        <div class="stat-number">{{ $needsAttention }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Maintenance Due</div>
        <div class="stat-number">
            @if ($user->canMaintainEquipment())
                <a href="{{ route('maintenance.due') }}">{{ $maintenanceDue }}</a>
            @else
                {{ $maintenanceDue }}
            @endif
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-title">For Disposal</div>
        <div class="stat-number">
            @if ($user->canManageInventory())
                <a href="{{ route('disposal.index') }}">{{ $forDisposal }}</a>
            @else
                {{ $forDisposal }}
            @endif
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Archived</div>
        <div class="stat-number">
            @if ($user->canManageInventory())
                <a href="{{ route('equipment.archived') }}">{{ $archived }}</a>
            @else
                {{ $archived }}
            @endif
        </div>
    </div>
</div>

<div class="card">
    <h2>Quick Links</h2>
    <div class="quick-links">
        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Browse Equipment</a>

        @if ($user->canManageInventory())
            <a href="{{ route('equipment.create') }}" class="btn btn-primary">Add Equipment</a>
            <a href="{{ route('disposal.index') }}" class="btn btn-secondary">Disposal</a>
            <a href="{{ route('reports.equipment') }}" class="btn btn-secondary">Equipment Report</a>
        @endif

        @if ($user->canMaintainEquipment())
            <a href="{{ route('maintenance.due') }}" class="btn btn-secondary">Maintenance Due</a>
        @endif

        @if ($user->isAdministrator())
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Users</a>
            <a href="{{ route('audit.index') }}" class="btn btn-secondary">Audit Logs</a>
        @endif
    </div>
</div>

@if ($user->canMaintainEquipment())
    <div class="card">
        <div class="card-header">
            <h2>Upcoming / Overdue Maintenance</h2>
            <a href="{{ route('maintenance.due') }}" class="btn btn-secondary">View all</a>
        </div>

        <div class="table-wrap table-wrap-borderless">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Equipment</th>
                        <th>Assignment</th>
                        <th>Next Maintenance</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($upcomingMaintenance as $item)
                        @php $nextDate = $item->nextMaintenanceOn(); @endphp
                        <tr>
                            <td>{{ $item->equipment_code }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->department?->name ?? 'Unassigned' }} / {{ $item->location?->name ?? 'Unassigned' }}</td>
                            <td>
                                @if ($nextDate?->isPast())
                                    <span class="text-overdue">Overdue: {{ $nextDate->format('M d, Y') }}</span>
                                @else
                                    <span class="text-due-soon">{{ $nextDate?->format('M d, Y') }}</span>
                                @endif
                            </td>
                            <td><a href="{{ route('equipment.show', $item) }}" class="btn btn-secondary">View</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty-state">No maintenance is overdue or due within 30 days.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif

@if ($user->isAdministrator())
    <div class="card">
        <div class="card-header">
            <h2>Recent Activity</h2>
            <a href="{{ route('audit.index') }}" class="btn btn-secondary">Audit logs</a>
        </div>

        <div class="table-wrap table-wrap-borderless">
            <table>
                <thead>
                    <tr>
                        <th>Date / Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentAuditLogs as $log)
                        <tr>
                            <td>{{ $log->created_at?->format('M d, Y h:i A') }}</td>
                            <td>{{ $log->user?->name ?? 'System / Unknown' }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->description ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty-state">No recent activity found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
