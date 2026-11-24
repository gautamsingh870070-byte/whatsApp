<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditHistory extends Model
{
    protected $fillable = ['user_id', 'amount', 'action', 'assigned_by'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
