<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_id',
        'prize_id',
        'status',
    ];

    /**
     * Get the coupon that won.
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the prize won.
     */
    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }

    /**
     * Scope a query to only include valid winners.
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('status', 'valid');
    }

    /**
     * Scope a query to only include annulled winners.
     */
    public function scopeAnnulled(Builder $query): Builder
    {
        return $query->where('status', 'annulled');
    }

    /**
     * Check if winner record is valid.
     */
    public function isValid(): bool
    {
        return $this->status === 'valid';
    }

    /**
     * Check if winner record is annulled.
     */
    public function isAnnulled(): bool
    {
        return $this->status === 'annulled';
    }

    /**
     * Annul this winning record.
     */
    public function annul(): bool
    {
        return $this->update(['status' => 'annulled']);
    }
}
