<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    #[Scope()]
    public function filter(Builder $query, array $filters): void
    {
        $query->when(
            $filters['category_id'] ?? null,
            fn() => $query->where('category_id', $filters['category_id'])
        )->when(
                $filters['q'] ?? null,
                fn() => $query->where(function ($query) use ($filters) {
                    $query->where('name', 'like', "%{$filters['q']}%")
                        ->orWhere('description', 'like', "%{$filters['q']}%");
                })
            );
    }

    #[Scope()]
    public function featured(Builder $query): void
    {
        $query->where('featured', true);
    }
}
