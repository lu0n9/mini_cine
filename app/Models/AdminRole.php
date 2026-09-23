<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdminRole extends Model
{
    protected $table = 'admin_roles';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_super_admin',
        'is_active',
    ];

    protected $casts = [
        'is_super_admin' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(
            Admin::class,
            'admin_user_role',
            'role_id',
            'admin_id'
        )->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminPermission::class,
            'admin_role_permission',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }
}