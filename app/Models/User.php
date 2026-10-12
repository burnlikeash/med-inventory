<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMINISTRATOR = 'Administrator';
    public const ROLE_INVENTORY_OFFICER = 'Inventory Officer';
    public const ROLE_MAINTENANCE_STAFF = 'Maintenance Staff';
    public const ROLE_DEPARTMENT_STAFF = 'Department Staff';
    public const ROLE_VIEWER = 'Viewer';

    public const ROLES = [
        self::ROLE_ADMINISTRATOR,
        self::ROLE_INVENTORY_OFFICER,
        self::ROLE_MAINTENANCE_STAFF,
        self::ROLE_DEPARTMENT_STAFF,
        self::ROLE_VIEWER,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdministrator(): bool
    {
        return $this->role === self::ROLE_ADMINISTRATOR;
    }

    public function canManageInventory(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMINISTRATOR,
            self::ROLE_INVENTORY_OFFICER,
        ], true);
    }

    public function canMaintainEquipment(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMINISTRATOR,
            self::ROLE_INVENTORY_OFFICER,
            self::ROLE_MAINTENANCE_STAFF,
        ], true);
    }

    public function equipmentMovements()
    {
        return $this->hasMany(EquipmentMovement::class, 'moved_by');
    }

    public function approvedDisposals()
    {
        return $this->hasMany(DisposalRecord::class, 'approved_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
