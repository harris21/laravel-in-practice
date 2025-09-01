<?php

namespace App\Models;

use App\Collections\OrderCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\CollectedBy;

#[CollectedBy(OrderCollection::class)]
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->where('status', 'completed');
    }

    #[Scope]
    protected function thisMonth(Builder $query): void
    {
        $query->where('created_at', '>=', now()->startOfMonth());
    }

    #[Scope]
    protected function popular(Builder $query, $minTotal = 100): void
    {
        $query->where('total', '>=', $minTotal);
    }

    #[Scope]
    protected function withUser(Builder $query): void
    {
        $query->with('user');
    }

    #[Scope]
    protected function forPeriod(Builder $query, string $period): void
    {
        match($period) {
            'today' => $query->whereDate('created_at', today()),
            'week' => $query->where('created_at', '>=', now()->startOfWeek()),
            'month' => $query->where('created_at', '>=', now()->startOfMonth()),
            default => $query
        };
    }

    protected function lastMonth(Builder $query): void
    {
        $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
    }
}
