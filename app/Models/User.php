<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'avatar',
        'phone',
        'last_login_at',
        'last_login_ip',
        'mfa_enabled',
        'mfa_code',
        'mfa_code_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'password'          => 'hashed',
        'mfa_enabled'       => 'boolean',
    ];

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isManager(): bool
    {
        return $this->hasRole('manager');
    }

    public function isCashier(): bool
    {
        return $this->hasRole('cashier');
    }

    public function isWaiter(): bool
    {
        return $this->hasRole('waiter');
    }

    public function isKitchenStaff(): bool
    {
        return $this->hasRole('kitchen_staff');
    }

    public function isAdminOrManager(): bool
    {
        return $this->hasAnyRole(['admin', 'manager']);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'    => 'green',
            'inactive'  => 'gray',
            'suspended' => 'red',
            default     => 'gray',
        };
    }
}