@extends('layouts.app')

@section('title', 'Maintenance Due')
@section('page-title', 'Maintenance Due')
@section('page-subtitle', 'Equipment overdue or scheduled for maintenance within the next 30 days')

@section('content')
@php
    $overdueCount = $equipment->getCollection()->filter(fn ($item) => $item->nextMaintenanceOn()?->isPast())->count();
    $dueSoonCount = $equipment->getCollection()->filter(function ($item) {
        $date = $item->nextMaintenanceOn();
        return $date && ! $date->isPast();
    })->count();
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-title">Overdue (this page)</div>
        <div class="stat-number">{{ $overdueCount }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-title">Due Within 30 Days (this page)</div>
        <div class="stat-number">{{ $dueSoonCount }}</div>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Equipment</th>
                <th>Department</th>
                <th>Location</th>
                <th>Status</th>
                <th>Next Maintenance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($equipment as $item)
                @php $nextDate = $item->nextMaintenanceOn(); @endphp
                <tr>
                    <td>{{ $item->equipment_code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->department?->name ?? 'Unassigned' }}</td>
                    <td>{{ $item->location?->name ?? 'Unassigned' }}</td>
                    <td><x-status-badge :value="$item->status" /></td>
                    <td>
                        @if ($nextDate?->isPast())
                            <span class="text-overdue">Overdue: {{ $nextDate->format('M d, Y') }}</span>
                        @else
                            <span class="text-due-soon">Due: {{ $nextDate?->format('M d, Y') }}</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('equipment.show', $item) }}" class="btn btn-secondary">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">No equipment is overdue or due within the next 30 days.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $equipment->links() }}
@endsection
