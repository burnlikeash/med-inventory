<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    public const OPERATIONAL_STATUSES = [
        'Working',
        'Under Maintenance',
        'Not Working',
        'For Repair',
        'For Inspection',
    ];

    public const ATTENTION_STATUSES = [
        'Under Maintenance',
        'Not Working',
        'For Repair',
        'For Inspection',
    ];

    public const CONDITIONS = [
        'Excellent',
        'Good',
        'Fair',
        'Poor',
        'Damaged',
    ];

    public const LIFECYCLE_STATUSES = [
        'Active',
        'For Disposal',
        'Disposed',
        'Lost',
        'Retired',
    ];

    public const EDITABLE_LIFECYCLE_STATUSES = [
        'Active',
        'For Disposal',
        'Lost',
        'Retired',
    ];

    protected $fillable = [
        'equipment_code',
        'barcode',
        'name',
        'category_id',
        'brand',
        'model',
        'serial_number',
        'property_number',
        'purchase_date',
        'purchase_price',
        'supplier',
        'warranty_expiry',
        'status',
        'condition_status',
        'lifecycle_status',
        'department_id',
        'location_id',
        'remarks',
        'archived_at',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'purchase_price' => 'decimal:2',
        'archived_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(EquipmentCategory::class, 'category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function movements()
    {
        return $this->hasMany(EquipmentMovement::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function disposalRecord()
    {
        return $this->hasOne(DisposalRecord::class);
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    public function isDisposed(): bool
    {
        return $this->lifecycle_status === 'Disposed';
    }

    public function nextMaintenanceOn(): ?Carbon
    {
        $value = $this->attributes['next_maintenance_date'] ?? null;

        return $value ? Carbon::parse($value) : null;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function scopeArchived(Builder $query): Builder
    {
        return $query->whereNotNull('archived_at');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! filled($term)) {
            return $query;
        }

        return $query->where(function (Builder $search) use ($term) {
            $search->where('equipment_code', 'like', "%{$term}%")
                ->orWhere('name', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%")
                ->orWhere('serial_number', 'like', "%{$term}%")
                ->orWhere('property_number', 'like', "%{$term}%");
        });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['department_id'] ?? null, fn (Builder $q, $id) => $q->where('department_id', $id))
            ->when($filters['category_id'] ?? null, fn (Builder $q, $id) => $q->where('category_id', $id))
            ->when($filters['status'] ?? null, fn (Builder $q, $status) => $q->where('status', $status))
            ->when($filters['condition_status'] ?? null, fn (Builder $q, $status) => $q->where('condition_status', $status))
            ->when($filters['lifecycle_status'] ?? null, fn (Builder $q, $status) => $q->where('lifecycle_status', $status));
    }

    public function scopeWithNextMaintenanceDate(Builder $query): Builder
    {
        if (empty($query->getQuery()->columns)) {
            $query->select($this->qualifyColumn('*'));
        }

        return $query->addSelect([
            'next_maintenance_date' => MaintenanceRecord::query()
                ->selectRaw('min(next_maintenance_date)')
                ->whereColumn('equipment_id', $this->qualifyColumn('id'))
                ->whereNotNull('next_maintenance_date'),
        ]);
    }

    public function scopeDueForMaintenance(Builder $query, int $withinDays = 30): Builder
    {
        $deadline = now()->addDays($withinDays)->toDateString();

        return $query
            ->active()
            ->where('lifecycle_status', '!=', 'Disposed')
            ->whereHas('maintenanceRecords', function (Builder $records) use ($deadline) {
                $records->whereNotNull('next_maintenance_date')
                    ->whereDate('next_maintenance_date', '<=', $deadline);
            });
    }
}
