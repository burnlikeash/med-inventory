<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}