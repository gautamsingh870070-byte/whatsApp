<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'description',
    ];

    public function roles()
    {
        return $this->belongsToMany(RolePermission::class, 'role_permissions', 'permission_id', 'role_name');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_permissions');
    }
}
