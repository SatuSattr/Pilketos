<?php

namespace App\Models;

use Database\Factories\DisplayKeyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DisplayKey extends Model
{
    /** @use HasFactory<DisplayKeyFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'key',
        'successful_votes',
        'failed_attempts',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'successful_votes' => 'integer',
            'failed_attempts' => 'integer',
        ];
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public static function generateKey(): string
    {
        return Str::upper(Str::random(8));
    }

    public function incrementSuccessfulVotes(): void
    {
        $this->increment('successful_votes');
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_attempts');
    }
}
