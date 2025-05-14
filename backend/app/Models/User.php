<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\RolesEnum;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
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

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function bookedEvents(): HasManyThrough
    {
        return $this->hasManyThrough(Event::class, Booking::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === RolesEnum::ADMIN->value;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === RolesEnum::SUPER_ADMIN->value;
    }

    #[Scope()]
    public function filter(Builder $query, array $filters): void
    {
        $query->when(
            $filters['q'] ?? null,
            fn($q) => $q->where('name', 'like', "%{$filters['q']}%")
                ->orWhere('email', 'like', "%{$filters['q']}%")
        )->when(
                $filters['role'] ?? null,
                fn($q) => $q->where('role', 'like', "%{$filters['role']}%")
            );
    }
}
