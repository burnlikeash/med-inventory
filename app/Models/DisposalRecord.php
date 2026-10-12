<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposalRecord extends Model
{
    use HasFactory;

    public const METHODS = [
        'Condemnation',
        'Donation',
        'Transfer',
        'Sale',
        'Recycling',
        'Scrapping',
        'Return to Supplier',
        'Other',
    ];

    protected $fillable = [
        'equipment_id',
        'reason',
        'disposal_date',
        'approved_by',
        'disposal_method',
        'disposal_reference',
        'remarks',
    ];

    protected $casts = [
        'disposal_date' => 'date',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}