<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * AttemptResult Model
 *
 * Represents the result of a single measure within an attempt.
 */
class AttemptResult extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attempt_id',
        'measure_id',
        'poured_ml',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'poured_ml' => 'decimal:2',
    ];

    /**
     * Get the attempt this result belongs to.
     */
    public function attempt(): BelongsTo
    {
        return $this->belongsTo(Attempt::class);
    }

    /**
     * Get the measure this result is for.
     */
    public function measure(): BelongsTo
    {
        return $this->belongsTo(Measure::class);
    }
}
