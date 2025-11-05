<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Competitor Model
 *
 * Represents a bartender competing in pour test competitions.
 */
class Competitor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'bar_name',
        'country_code',
        'instagram',
        'diffords_profile',
    ];

    /**
     * Get the competitions this competitor is registered for.
     */
    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'competition_competitor');
    }

    /**
     * Get all attempts by this competitor.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(Attempt::class);
    }
}
