<?php

namespace App\Http\Controllers;

use App\Models\Calon;
use App\Models\DisplayKey;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VotingController extends Controller
{
    public function index(): View
    {
        $calons = Calon::orderBy('nomor')->get()->map(fn (Calon $calon): array => [
            'id' => $calon->id,
            'nomor' => $calon->nomor,
            'nama' => $calon->nama,
            'firstName' => Str::before($calon->nama, ' '),
            'lastName' => Str::after($calon->nama, ' '),
            'kelas' => $calon->kelas,
            'urlFoto' => asset('storage/foto_calon/'.$calon->foto),
            'visi' => $calon->visi,
            'misi' => $calon->misi,
        ])->values();

        return view('voting', ['calons' => $calons]);
    }

    public function validateDisplayKey(Request $request): JsonResponse
    {
        $request->validate([
            'key' => ['required', 'string'],
        ]);

        $displayKey = DisplayKey::where('key', strtoupper($request->string('key')->toString()))
            ->where('is_active', true)
            ->first();

        if (! $displayKey) {
            return response()->json(['success' => false, 'message' => 'Key tidak valid atau tidak aktif.']);
        }

        return response()->json(['success' => true]);
    }

    public function vote(Request $request): JsonResponse
    {
        $request->validate([
            'calon_id' => ['required', 'integer', 'exists:calons,id'],
            'nama_pemilih' => ['required', 'string', 'max:200'],
            'display_key' => ['required', 'string'],
        ]);

        // Validasi display key
        $displayKey = DisplayKey::where('key', strtoupper($request->string('display_key')->toString()))
            ->where('is_active', true)
            ->first();

        if (! $displayKey) {
            return response()->json([
                'success' => false,
                'message' => 'Key tidak valid atau tidak aktif.',
            ], 422);
        }

        // Cari voter berdasarkan nama (case-insensitive, prefix match)
        $namaPemilih = strtolower(trim($request->string('nama_pemilih')->toString()));

        $matchingVoters = Voter::whereRaw('LOWER(nama) LIKE ?', [$namaPemilih.'%'])->get();

        if ($matchingVoters->isEmpty()) {
            $displayKey->incrementFailedAttempts();

            return response()->json([
                'success' => false,
                'type' => 'not_found',
                'message' => 'Nama tidak ditemukan dalam daftar pemilih.',
            ], 422);
        }

        if ($matchingVoters->count() > 1) {
            $displayKey->incrementFailedAttempts();

            return response()->json([
                'success' => false,
                'type' => 'ambiguous',
                'message' => 'Tolong tulis nama lengkap Anda.',
            ], 422);
        }

        $voter = $matchingVoters->first();

        if ($voter->has_voted) {
            $displayKey->incrementFailedAttempts();

            return response()->json([
                'success' => false,
                'type' => 'already_voted',
                'message' => 'Anda sudah melakukan pemilihan sebelumnya.',
            ], 422);
        }

        // Simpan vote
        Vote::create([
            'voter_id' => $voter->id,
            'calon_id' => $request->integer('calon_id'),
            'display_key_id' => $displayKey->id,
            'ip_address' => $request->ip(),
        ]);

        $voter->update(['has_voted' => true]);
        $displayKey->incrementSuccessfulVotes();

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Suara Anda telah berhasil direkam.',
        ]);
    }
}
