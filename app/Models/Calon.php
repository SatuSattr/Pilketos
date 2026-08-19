<?php

namespace App\Models;

use Database\Factories\CalonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calon extends Model
{
    /** @use HasFactory<CalonFactory> */
    use HasFactory;

    protected $fillable = [
        'nomor',
        'nama',
        'kelas',
        'foto',
        'visi',
        'misi',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }
}
