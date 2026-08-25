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
        'foto_crop',
        'visi',
        'misi',
    ];

    protected $casts = [
        'foto_crop' => 'array',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getVoteCountAttribute(): int
    {
        return $this->votes()->count();
    }

    /**
     * Normalized crop metadata with defaults (virtual crop, file never modified).
     * x,y = 0-100 focal point (object-position), zoom = 1.0-3.0 scale.
     *
     * @return array{x: float, y: float, zoom: float}
     */
    public function getFotoCropDataAttribute(): array
    {
        $raw = $this->foto_crop ?? [];

        return [
            'x' => isset($raw['x']) ? (float) $raw['x'] : 50.0,
            'y' => isset($raw['y']) ? (float) $raw['y'] : 50.0,
            'zoom' => isset($raw['zoom']) ? (float) $raw['zoom'] : 1.0,
        ];
    }

    /**
     * CSS helper for <x-cropped-img>: object-position + scale.
     *
     * @return array{position: string, transform: string}
     */
    public function getFotoCropStyleAttribute(): array
    {
        $c = $this->foto_crop_data;

        return [
            'position' => $c['x'].'% '.$c['y'].'%',
            'transform' => 'scale('.$c['zoom'].')',
            'origin' => $c['x'].'% '.$c['y'].'%',
        ];
    }
}
