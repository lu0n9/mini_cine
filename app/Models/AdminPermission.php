<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdminPermission extends Model
{
    protected $table = 'admin_permissions';

    protected $fillable = [
        'name',
        'slug',
        'module',
        'description',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            'admin_role_permission',
            'permission_id',
            'role_id'
        )->withTimestamps();
    }
}