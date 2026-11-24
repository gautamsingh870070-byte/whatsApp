<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'target_user_id',
        'changed_by_user_id',
        'permission_id',
        'action',
    ];

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function changedByUser()
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
