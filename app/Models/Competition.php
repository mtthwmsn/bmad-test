<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Competition Model
 *
 * Represents a pour test competition event.
 */
class Competition extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'city',
        'country_code',
        'date',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'status' => 'string',
    ];

    /**
     * Get the stages for this competition.
     */
    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class);
    }

    /**
     * Get the competitors in this competition.
     */
    public function competitors(): BelongsToMany
    {
        return $this->belongsToMany(Competitor::class, 'competition_competitor');
    }

    /**
     * Get all attempts for this competition.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
