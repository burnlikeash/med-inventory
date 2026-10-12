@extends('layouts.app')

@section('title', 'Disposal Report')
@section('page-title', 'Disposal Report')
@section('page-subtitle', 'Generated: '.now()->format('M d, Y h:i A'))

@section('page-actions')
    <button type="button" class="btn btn-secondary no-print" onclick="window.print()">Print</button>
@endsection

@section('content')
<div class="card no-print">
    <form action="{{ route('reports.disposal') }}" method="GET">
        <div class="filter-grid">
            <div class="form-group">
                <label for="disposal_method">Disposal Method</label>
                <select id="disposal_method" name="disposal_method">
                    <option value="">All Disposal Methods</option>
                    @foreach (\App\Models\DisposalRecord::METHODS as $method)
                        <option value="{{ $method }}" @selected(request('disposal_method') === $method)>{{ $method }}</option>
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
            <a href="{{ route('reports.disposal') }}" class="btn btn-secondary">Reset</a>
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
                <th>Method</th>
                <th>Reference</th>
                <th>Approved By</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $record)
                <tr>
                    <td>{{ $record->disposal_date?->format('M d, Y') }}</td>
                    <td>{{ $record->equipment?->equipment_code }} - {{ $record->equipment?->name }}</td>
                    <td>{{ $record->equipment?->department?->name ?? 'Unassigned' }}</td>
                    <td>{{ $record->disposal_method ?? 'N/A' }}</td>
                    <td>{{ $record->disposal_reference ?? 'N/A' }}</td>
                    <td>{{ $record->approvedBy?->name ?? 'Not recorded' }}</td>
                    <td>{{ $record->reason }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">No disposal records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
