<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CalonController extends Controller
{
    public function index(): View
    {
        $calons = Calon::withCount('votes')->orderBy('nomor')->get();

        return view('admin.calon.index', compact('calons'));
    }

    public function create(): View
    {
        return view('admin.calon.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nomor' => ['required', 'string', 'max:10', 'unique:calons,nomor'],
            'nama' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
            'visi' => ['required', 'string'],
            'misi' => ['required', 'string'],
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $fotoFilename = $request->file('foto')->store('foto_calon', 'public');
        $fotoFilename = basename($fotoFilename);

        Calon::create([
            'nomor' => $validated['nomor'],
            'nama' => $validated['nama'],
            'kelas' => $validated['kelas'],
            'visi' => $validated['visi'],
            'misi' => $validated['misi'],
            'foto' => $fotoFilename,
        ]);

        return redirect()->route('admin.calon.index')
            ->with('success', 'Calon berhasil ditambahkan.');
    }

    public function edit(Calon $calon): View
    {
        return view('admin.calon.edit', compact('calon'));
    }

    public function update(Request $request, Calon $calon): RedirectResponse
    {
        $validated = $request->validate([
            'nomor' => ['required', 'string', 'max:10', 'unique:calons,nomor,'.$calon->id],
            'nama' => ['required', 'string', 'max:150'],
            'kelas' => ['required', 'string', 'max:50'],
            'visi' => ['required', 'string'],
            'misi' => ['required', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $updateData = [
            'nomor' => $validated['nomor'],
            'nama' => $validated['nama'],
            'kelas' => $validated['kelas'],
            'visi' => $validated['visi'],
            'misi' => $validated['misi'],
        ];

        if ($request->hasFile('foto')) {
            // Hapus foto lama
            if ($calon->foto && Storage::disk('public')->exists('foto_calon/'.$calon->foto)) {
                Storage::disk('public')->delete('foto_calon/'.$calon->foto);
            }

            $fotoFilename = $request->file('foto')->store('foto_calon', 'public');
            $updateData['foto'] = basename($fotoFilename);
        }

        $calon->update($updateData);

        return redirect()->route('admin.calon.index')
            ->with('success', 'Data calon berhasil diperbarui.');
    }

    public function destroy(Calon $calon): RedirectResponse
    {
        if ($calon->foto && Storage::disk('public')->exists('foto_calon/'.$calon->foto)) {
            Storage::disk('public')->delete('foto_calon/'.$calon->foto);
        }

        $calon->delete();

        return redirect()->route('admin.calon.index')
            ->with('success', 'Calon berhasil dihapus.');
    }
}
