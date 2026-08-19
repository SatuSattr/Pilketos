<?php

namespace App\Models;

use Database\Factories\VoterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Voter extends Model
{
    /** @use HasFactory<VoterFactory> */
    use HasFactory;

    protected $fillable = [
        'nama',
        'has_voted',
    ];

    protected function casts(): array
    {
        return [
            'has_voted' => 'boolean',
        ];
    }

    public function vote(): HasOne
    {
        return $this->hasOne(Vote::class);
    }
}
