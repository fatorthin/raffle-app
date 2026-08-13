<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_number',
        'name',
    ];

    /**
     * Get the winner records associated with this coupon.
     */
    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    /**
     * Get the latest winner record associated with this coupon.
     */
    public function winner(): HasOne
    {
        return $this->hasOne(Winner::class)->latestOfMany();
    }

    /**
     * Scope a query to only include eligible coupons that have never won (valid or annulled).
     */
    public function scopeEligible(Builder $query): Builder
    {
        return $query->whereDoesntHave('winners');
    }
}
