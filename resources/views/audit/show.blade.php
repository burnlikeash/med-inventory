@extends('layouts.app')

@section('title', 'Audit Log Details')
@section('page-title', 'Audit Log Details')

@section('page-actions')
    <a href="{{ route('audit.index') }}" class="btn btn-secondary">Back to Audit Logs</a>
@endsection

@section('content')
<div class="card">
    <div class="details-grid">
        <div class="detail-item">
            <div class="detail-label">User</div>
            <div class="detail-value">{{ $auditLog->user?->name ?? 'System / Unknown' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Date / Time</div>
            <div class="detail-value">{{ $auditLog->created_at?->format('M d, Y h:i A') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Action</div>
            <div class="detail-value">{{ $auditLog->action }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Table</div>
            <div class="detail-value">{{ $auditLog->table_name }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Record ID</div>
            <div class="detail-value">{{ $auditLog->record_id ?? 'N/A' }}</div>
        </div>
        <div class="detail-item full-width">
            <div class="detail-label">Description</div>
            <div class="detail-value">{{ $auditLog->description ?? 'N/A' }}</div>
        </div>
    </div>
</div>

<div class="card">
    <h2>Old Values</h2>
    <pre>{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
</div>

<div class="card">
    <h2>New Values</h2>
    <pre>{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
</div>
@endsection
