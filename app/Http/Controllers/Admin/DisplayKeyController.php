<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisplayKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisplayKeyController extends Controller
{
    public function index(): View
    {
        $displayKeys = DisplayKey::latest()->get();
        $suggestedKey = DisplayKey::generateKey();

        return view('admin.display-key.index', compact('displayKeys', 'suggestedKey'));
    }

    public function create(): View
    {
        $suggestedKey = DisplayKey::generateKey();

        return view('admin.display-key.create', compact('suggestedKey'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'key' => ['required', 'string', 'max:50', 'unique:display_keys,key'],
        ]);

        DisplayKey::create([
            'nama' => $validated['nama'],
            'key' => strtoupper($validated['key']),
            'is_active' => true,
        ]);

        return redirect()->route('admin.display-key.index')
            ->with('success', 'Display key berhasil dibuat.');
    }

    public function destroy(DisplayKey $displayKey): RedirectResponse
    {
        $displayKey->delete();

        return redirect()->route('admin.display-key.index')
            ->with('success', 'Display key berhasil dihapus.');
    }

    public function toggle(DisplayKey $displayKey): RedirectResponse
    {
        $displayKey->update(['is_active' => ! $displayKey->is_active]);

        $statusLabel = $displayKey->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.display-key.index')
            ->with('success', "Display key berhasil {$statusLabel}.");
    }

    public function resetStats(DisplayKey $displayKey): RedirectResponse
    {
        $displayKey->update([
            'successful_votes' => 0,
            'failed_attempts' => 0,
        ]);

        return redirect()->route('admin.display-key.index')
            ->with('success', 'Statistik display key berhasil direset.');
    }
}
