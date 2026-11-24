<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTemplate extends Model
{
    use HasFactory;

    protected $table = 'user_templates';

    protected $fillable = [
        'user_id',
        'template_name',
        'category',
        'language',
        'components',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
