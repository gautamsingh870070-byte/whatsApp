<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'daily_limit',
        'role',
        'created_by',
    ];

    public function isSuperAdmin()
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

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

    // Creator relationships
    public function parent()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    public function hasPermission($permissionName)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->permissions()->where('name', $permissionName)->exists();
    }

    public function canManageClient($clientId)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        
        $client = User::find($clientId);
        // Use loose equality (==) because PDO might return created_by as a string (e.g. "3") which breaks strict equality against integer ID
        return $client && $client->created_by == $this->id;
    }

    public function getHomeRoute()
    {
        $routes = [
            'dashboard.view' => 'dashboard',
            'livechat.view' => 'live.chat',
            'history.view' => 'history',
            'contacts.view' => 'contacts.index',
            'messages.send' => 'send.wa.form',
            'templates.view' => 'templates.index',
            'users.view' => 'manage.user',
        ];

        foreach ($routes as $permission => $route) {
            if ($this->hasPermission($permission)) {
                return route($route);
            }
        }
        
        // Absolute fallback if they have no UI permissions at all
        return route('profile.edit');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }
}
