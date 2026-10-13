<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function booted()
    {
        static::deleting(function (User $user) {
            $user->pets()->chunkById(100, function ($pets) {
                $pets->each->delete();
            });

            $user->reports()->chunkById(100, function ($reports) {
                $reports->each->delete();
            });
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** true = แอดมิน (แก้/ลบของทุกคนได้) */
    public function isAdmin(): bool
    {
        return ($this->role ?? null) === 'admin';
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
