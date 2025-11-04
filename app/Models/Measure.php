<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Measure Model
 *
 * Represents a single measurement within a stage (e.g., pour 50ml of vodka).
 */
class Measure extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stage_id',
        'name',
        'target_ml',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_ml' => 'decimal:2',
    ];

    /**
     * Get the stage this measure belongs to.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    /**
     * Get all attempt results for this measure.
     */
    public function attemptResults(): HasMany
    {
        return $this->hasMany(AttemptResult::class);
    }
}
