<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcsContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'phone_number', 'country_code', 'is_rcs_capable'
    ];

    protected $casts = [
        'is_rcs_capable' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
