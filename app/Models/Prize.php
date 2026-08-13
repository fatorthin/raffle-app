<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prize extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'quota',
        'image_path',
    ];

    /**
     * Get all winner records for this prize.
     */
    public function winners(): HasMany
    {
        return $this->hasMany(Winner::class);
    }

    /**
     * Get only valid winner records for this prize.
     */
    public function validWinners(): HasMany
    {
        return $this->hasMany(Winner::class)->where('status', 'valid');
    }

    /**
     * Calculate remaining quota for this prize.
     */
    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->quota - $this->validWinners()->count());
    }

    /**
     * Check if the prize still has remaining quota.
     */
    public function hasQuota(): bool
    {
        return $this->remaining_quota > 0;
    }
}
