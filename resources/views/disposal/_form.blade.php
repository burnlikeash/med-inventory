<div class="form-grid">
    <div class="form-group">
        <label for="disposal_date">Disposal Date *</label>
        <input
            type="date"
            id="disposal_date"
            name="disposal_date"
            value="{{ old('disposal_date', optional($record?->disposal_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
            required
        >
        @error('disposal_date') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
        <label for="disposal_method">Disposal Method</label>
        <select id="disposal_method" name="disposal_method">
            <option value="">Select Method</option>
            @foreach (\App\Models\DisposalRecord::METHODS as $method)
                <option value="{{ $method }}" @selected(old('disposal_method', $record?->disposal_method) === $method)>
                    {{ $method }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group full-width">
        <label for="reason">Reason for Disposal *</label>
        <textarea id="reason" name="reason" required placeholder="Explain why this equipment is being disposed">{{ old('reason', $record?->reason) }}</textarea>
        @error('reason') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group full-width">
        <label for="disposal_reference">Disposal Reference</label>
        <input
            type="text"
            id="disposal_reference"
            name="disposal_reference"
            value="{{ old('disposal_reference', $record?->disposal_reference) }}"
            placeholder="Example: Disposal Form No. 2026-001"
        >
    </div>

    <div class="form-group full-width">
        <label for="remarks">Remarks</label>
        <textarea id="remarks" name="remarks">{{ old('remarks', $record?->remarks) }}</textarea>
    </div>
</div>
