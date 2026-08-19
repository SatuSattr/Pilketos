<?php

use App\Models\Calon;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('halaman voting menampilkan calon dari database', function () {
    Calon::factory()->create([
        'nomor' => '01',
        'nama' => 'Shabira Syahla Alvaliza',
        'kelas' => 'XI-1',
        'foto' => 'shabira-syahla-alvaliza.png',
        'visi' => 'Visi Shabira',
        'misi' => 'Misi Shabira',
    ]);

    Calon::factory()->create([
        'nomor' => '02',
        'nama' => 'Faiz Nabil Akram',
        'kelas' => 'XII-1',
        'foto' => 'faiz-nabil-akram.png',
        'visi' => 'Visi Faiz',
        'misi' => 'Misi Faiz',
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();

    preg_match('/id="calons-data">\s*(.*?)\s*<\/script>/s', $response->getContent(), $matches);

    $calons = json_decode($matches[1], true);

    expect($calons)
        ->toHaveCount(2)
        ->and(array_column($calons, 'nama'))
        ->toContain('Shabira Syahla Alvaliza')
        ->toContain('Faiz Nabil Akram')
        ->not->toContain('Fakih Abdul Karim');
});

test('halaman voting tidak memakai cdn', function () {
    Calon::factory()->count(2)->create();

    $this->get('/')
        ->assertSuccessful()
        ->assertDontSee('cdn.tailwindcss.com')
        ->assertDontSee('cdn.jsdelivr.net')
        ->assertDontSee('fonts.googleapis.com')
        ->assertDontSee('cdnjs.cloudflare.com');
});
