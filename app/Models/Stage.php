<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Stage Model
 *
 * Represents a stage within a competition.
 */
class Stage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'competition_id',
        'order',
        'name',
        'expected_seconds',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the competition this stage belongs to.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the measures for this stage.
     */
    public function measures(): HasMany
    {
        return $this->hasMany(Measure::class);
    }

    /**
     * Get all attempts for this stage.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
