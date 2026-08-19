<?php

namespace App\Models;

use Database\Factories\VoteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    /** @use HasFactory<VoteFactory> */
    use HasFactory;

    protected $fillable = [
        'voter_id',
        'calon_id',
        'display_key_id',
        'ip_address',
    ];

    public function voter(): BelongsTo
    {
        return $this->belongsTo(Voter::class);
    }

    public function calon(): BelongsTo
    {
        return $this->belongsTo(Calon::class);
    }

    public function displayKey(): BelongsTo
    {
        return $this->belongsTo(DisplayKey::class);
    }
}
