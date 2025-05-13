<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'date',
        'venue',
        'price',
        'image_path',
        'category_id',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'date' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    #[Scope()]
    public function filter(Builder $query, array $filters): void
    {
        $query->when(
            $filters['category_id'] ?? null,
            fn() => $query->where('category_id', $filters['category_id'])
        )->when(
                $filters['q'] ?? null,
                fn($q) => $q->where('name', 'like', "%{$filters['q']}%")
                    ->orWhere('description', 'like', "%{$filters['q']}%")
            );
    }

    #[Scope()]
    public function featured(Builder $query): void
    {
        $query->where('featured', true);
    }

    #[Scope()]
    public function withBookingInfo(Builder $query, ?string $userId): void
    {
        if (isset($userId)) {
            $query->addSelect([
                'booked_by_user' => DB::table('bookings')
                    ->whereColumn('bookings.event_id', 'events.id')
                    ->where('bookings.user_id', $userId)
                    ->select(DB::raw('COUNT(*) > 0'))
                    ->limit(1)
            ]);
        }
    }

    public function bookedByUser(string $userId): bool
    {
        return $this->bookings()
            ->where('user_id', $userId)
            ->exists();
    }
}
