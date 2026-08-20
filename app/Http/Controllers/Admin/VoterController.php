<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoterController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $voters = Voter::query()
            ->when($search, fn ($q) => $q->where('nama', 'like', "%{$search}%"))
            ->orderBy('nama')
            ->paginate(25)
            ->withQueryString();

        return view('admin.voter.index', compact('voters', 'search'));
    }

    public function create(): View
    {
        return view('admin.voter.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
        ]);

        Voter::create(['nama' => $validated['nama']]);

        return redirect()->route('admin.voter.index')
            ->with('success', 'Pemilih berhasil ditambahkan.');
    }

    public function edit(Voter $voter): View
    {
        return view('admin.voter.edit', compact('voter'));
    }

    public function update(Request $request, Voter $voter): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
        ]);

        $voter->update(['nama' => $validated['nama']]);

        return redirect()->route('admin.voter.index')
            ->with('success', 'Data pemilih berhasil diperbarui.');
    }

    public function destroy(Voter $voter): RedirectResponse
    {
        $voter->delete();

        return redirect()->route('admin.voter.index')
            ->with('success', 'Pemilih berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'daftar_nama' => ['required', 'string'],
        ]);

        $namaList = array_filter(
            array_map('trim', explode('|', $request->string('daftar_nama')->toString())),
            fn (string $line): bool => $line !== ''
        );

        $inserted = 0;
        $skipped = [];

        foreach ($namaList as $nama) {
            $result = Voter::firstOrCreate(['nama' => $nama]);

            if ($result->wasRecentlyCreated) {
                $inserted++;
            } else {
                $skipped[] = $nama;
            }
        }

        $message = "{$inserted} pemilih berhasil diimport.";

        if (! empty($skipped)) {
            $skippedNames = implode(', ', $skipped);
            $message .= ' '.count($skipped)." dilewati (sudah ada): {$skippedNames}.";
        }

        return redirect()->route('admin.voter.index')
            ->with('toast_type', 'success')
            ->with('toast_msg', $message);
    }

    public function resetVote(Voter $voter): RedirectResponse
    {
        $voter->vote()?->delete();
        $voter->update(['has_voted' => false]);

        return redirect()->route('admin.voter.index')
            ->with('success', "Status vote {$voter->nama} berhasil direset.");
    }
}
