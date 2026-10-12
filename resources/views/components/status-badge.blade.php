@props(['value'])

@php
    $class = match ($value) {
        'Working', 'Excellent', 'Good', 'Active' => 'badge-ok',
        'Under Maintenance', 'For Inspection', 'Fair', 'For Disposal' => 'badge-warn',
        'Not Working', 'For Repair', 'Poor', 'Damaged', 'Disposed', 'Lost' => 'badge-bad',
        default => 'badge-muted',
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge '.$class]) }}>{{ $value }}</span>
