@extends('layouts.app')

@section('title', $equipment->name)
@section('page-title', $equipment->name)
@section('page-subtitle', $equipment->equipment_code)

@section('page-actions')
    @php $user = auth()->user(); @endphp

    @if ($equipment->isArchived() && $user->canManageInventory())
        <form action="{{ route('equipment.restore', $equipment) }}" method="POST" onsubmit="return confirm('Restore this equipment?');">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">Restore</button>
        </form>
    @endif

    @if (! $equipment->isDisposed() && ! $equipment->isArchived() && $user->canManageInventory())
        <a href="{{ route('equipment.move', $equipment) }}" class="btn btn-primary">Move Equipment</a>
        <a href="{{ route('equipment.edit', $equipment) }}" class="btn btn-secondary">Edit</a>
    @endif

    <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Back</a>

    @if (! $equipment->isArchived() && $user->canManageInventory())
        <form action="{{ route('equipment.destroy', $equipment) }}" method="POST" onsubmit="return confirm('Archive this equipment?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Archive</button>
        </form>
    @endif
@endsection

@section('content')
@if ($equipment->isArchived())
    <div class="alert alert-info">This equipment is archived and excluded from the active inventory.</div>
@endif

<div class="card">
    <h2>Equipment Information</h2>
    <div class="details-grid">
        <div class="detail-item">
            <div class="detail-label">Equipment Code</div>
            <div class="detail-value">{{ $equipment->equipment_code }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Barcode</div>
            <div class="detail-value">{{ $equipment->barcode ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Category</div>
            <div class="detail-value">{{ $equipment->category?->name ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Brand</div>
            <div class="detail-value">{{ $equipment->brand ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Model</div>
            <div class="detail-value">{{ $equipment->model ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Serial Number</div>
            <div class="detail-value">{{ $equipment->serial_number ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Property Number</div>
            <div class="detail-value">{{ $equipment->property_number ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Supplier</div>
            <div class="detail-value">{{ $equipment->supplier ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Purchase Date</div>
            <div class="detail-value">{{ $equipment->purchase_date?->format('M d, Y') ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Purchase Price</div>
            <div class="detail-value">
                @if ($equipment->purchase_price)
                    ₱{{ number_format((float) $equipment->purchase_price, 2) }}
                @else
                    N/A
                @endif
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Warranty Expiry</div>
            <div class="detail-value">{{ $equipment->warranty_expiry?->format('M d, Y') ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Department</div>
            <div class="detail-value">{{ $equipment->department?->name ?? 'Unassigned' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Location</div>
            <div class="detail-value">{{ $equipment->location?->name ?? 'Unassigned' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Operational Status</div>
            <div class="detail-value"><x-status-badge :value="$equipment->status" /></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Condition</div>
            <div class="detail-value"><x-status-badge :value="$equipment->condition_status" /></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Lifecycle Status</div>
            <div class="detail-value"><x-status-badge :value="$equipment->lifecycle_status" /></div>
        </div>
        <div class="detail-item full-width">
            <div class="detail-label">Remarks</div>
            <div class="detail-value">{{ $equipment->remarks ?? 'No remarks.' }}</div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Movement History</h2>
    @if ($equipment->movements->count())
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Reason</th>
                    <th>Moved By</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipment->movements as $movement)
                    <tr>
                        <td>{{ $movement->movement_date?->format('M d, Y h:i A') }}</td>
                        <td>{{ $movement->fromDepartment?->name ?? 'N/A' }} / {{ $movement->fromLocation?->name ?? 'N/A' }}</td>
                        <td>{{ $movement->toDepartment?->name ?? 'N/A' }} / {{ $movement->toLocation?->name ?? 'N/A' }}</td>
                        <td>{{ $movement->reason }}</td>
                        <td>{{ $movement->movedBy?->name ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">No movement history yet.</div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <h2>Maintenance History</h2>
        @if (! $equipment->isDisposed() && ! $equipment->isArchived() && auth()->user()->canMaintainEquipment())
            <a href="{{ route('maintenance.create', $equipment) }}" class="btn btn-primary">Add Maintenance</a>
        @endif
    </div>

    @php
        $nextMaintenance = $equipment->maintenanceRecords
            ->whereNotNull('next_maintenance_date')
            ->sortBy('next_maintenance_date')
            ->first();
        $nextDate = $nextMaintenance?->next_maintenance_date;
    @endphp

    @if ($nextDate)
        @if ($nextDate->isPast())
            <div class="alert alert-error">
                <strong>Maintenance Overdue:</strong>
                This equipment was scheduled for maintenance on {{ $nextDate->format('M d, Y') }}.
            </div>
        @elseif ($nextDate->lte(now()->addDays(30)))
            <div class="alert alert-warning">
                <strong>Upcoming Maintenance:</strong>
                This equipment is scheduled for maintenance on {{ $nextDate->format('M d, Y') }}.
            </div>
        @endif
    @endif

    @if ($equipment->maintenanceRecords->count())
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Performed By</th>
                    <th>Result</th>
                    <th>Cost</th>
                    @if (auth()->user()->canMaintainEquipment())
                        <th>Actions</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($equipment->maintenanceRecords as $record)
                    <tr>
                        <td>{{ $record->maintenance_date?->format('M d, Y') }}</td>
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
                        @if (auth()->user()->canMaintainEquipment())
                            <td>
                                @if (! $equipment->isDisposed() && ! $equipment->isArchived())
                                    <div class="table-actions">
                                        <a href="{{ route('maintenance.edit', $record) }}" class="btn btn-secondary">Edit</a>
                                        <form action="{{ route('maintenance.destroy', $record) }}" method="POST" onsubmit="return confirm('Delete this maintenance record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="muted">History locked</span>
                                @endif
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">No maintenance history yet.</div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <h2>Disposal Information</h2>
        @if (! $equipment->disposalRecord && $equipment->lifecycle_status === 'For Disposal' && ! $equipment->isArchived() && auth()->user()->canManageInventory())
            <a href="{{ route('disposal.create', $equipment) }}" class="btn btn-danger">Dispose Equipment</a>
        @endif
    </div>

    @if ($equipment->disposalRecord)
        <div class="details-grid">
            <div class="detail-item">
                <div class="detail-label">Disposal Date</div>
                <div class="detail-value">{{ $equipment->disposalRecord->disposal_date?->format('M d, Y') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Method</div>
                <div class="detail-value">{{ $equipment->disposalRecord->disposal_method ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Reference</div>
                <div class="detail-value">{{ $equipment->disposalRecord->disposal_reference ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Approved By</div>
                <div class="detail-value">{{ $equipment->disposalRecord->approvedBy?->name ?? 'Not recorded' }}</div>
            </div>
            <div class="detail-item full-width">
                <div class="detail-label">Reason</div>
                <div class="detail-value">{{ $equipment->disposalRecord->reason }}</div>
            </div>
            <div class="detail-item full-width">
                <div class="detail-label">Remarks</div>
                <div class="detail-value">{{ $equipment->disposalRecord->remarks ?? 'No remarks.' }}</div>
            </div>
        </div>

        @if (auth()->user()->canManageInventory() && ! $equipment->isArchived())
            <div class="form-actions">
                <a href="{{ route('disposal.edit', $equipment->disposalRecord) }}" class="btn btn-secondary">Edit Disposal Record</a>
                <form action="{{ route('disposal.destroy', $equipment->disposalRecord) }}" method="POST" onsubmit="return confirm('Remove this disposal record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove Disposal Record</button>
                </form>
            </div>
        @elseif ($equipment->isArchived() && auth()->user()->canManageInventory())
            <p class="muted">Restore this equipment before changing its disposal record.</p>
        @endif
    @else
        <div class="empty-state">No disposal record.</div>
    @endif
</div>
@endsection
