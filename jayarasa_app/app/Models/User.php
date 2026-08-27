<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'uuid';
    protected $fillable = [
        'name',
        'role',
        'username',
        'password',
        'token',
        'profile_picture',
        'email',
        'phone_number',
        'gender',
        'address',
        'daily_salary',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        // Admin always has all permissions
        if ($this->role === 'admin') {
            return true;
        }

        // Fetch role from the database
        $roleRecord = \App\Models\Role::where('name', $this->role)->first();
        if (!$roleRecord) {
            if ($this->role === 'cashier' || $this->role === 'kasir') {
                $cashierPermissions = ['access_cashier', 'view_reports', 'manage_stock'];
                return in_array($permission, $cashierPermissions);
            }
            if ($this->role === 'dapur') {
                $dapurPermissions = ['view_kitchen_queue', 'manage_stock'];
                return in_array($permission, $dapurPermissions);
            }
            return false;
        }

        // Check if the permission exists in the role's permissions array
        $permissions = $roleRecord->permissions;
        return is_array($permissions) && in_array($permission, $permissions);
    }

    /**
     * Get the salary adjustments for this user.
     */
    public function salaryAdjustments()
    {
        return $this->hasMany(SalaryAdjustment::class, 'user_id', 'uuid');
    }
}
