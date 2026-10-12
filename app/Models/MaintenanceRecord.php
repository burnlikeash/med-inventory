<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    use HasFactory;

    public const TYPES = [
        'Preventive Maintenance',
        'Corrective Maintenance',
        'Repair',
        'Inspection',
        'Calibration',
        'Cleaning',
        'Other',
    ];

    protected $fillable = [
        'equipment_id',
        'maintenance_type',
        'maintenance_date',
        'performed_by',
        'description',
        'cost',
        'result',
        'next_maintenance_date',
        'remarks',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}