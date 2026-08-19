<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calon;
use App\Models\DisplayKey;
use App\Models\Vote;
use App\Models\Voter;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalVoters = Voter::count();
        $totalHasVoted = Voter::where('has_voted', true)->count();
        $totalNotVoted = $totalVoters - $totalHasVoted;
        $participationRate = $totalVoters > 0
            ? round(($totalHasVoted / $totalVoters) * 100, 1)
            : 0;

        $calons = Calon::withCount('votes')->orderBy('nomor')->get();
        $totalVotes = $calons->sum('votes_count');
        $activeKeys = DisplayKey::where('is_active', true)->count();

        return view('admin.dashboard', compact(
            'totalVoters',
            'totalHasVoted',
            'totalNotVoted',
            'participationRate',
            'calons',
            'totalVotes',
            'activeKeys',
        ));
    }

    /**
     * Return cumulative vote time series data per calon.
     * Each data point = { t: ISO timestamp, y: cumulative votes at that moment }
     */
    public function chartData(): JsonResponse
    {
        $calons = Calon::orderBy('nomor')->get();

        $series = $calons->map(function (Calon $calon) {
            $votes = Vote::where('calon_id', $calon->id)
                ->orderBy('created_at')
                ->pluck('created_at');

            $points = [];
            $cumulative = 0;

            foreach ($votes as $timestamp) {
                $cumulative++;
                $points[] = [
                    't' => $timestamp->toIso8601String(),
                    'y' => $cumulative,
                ];
            }

            // Add a starting point at zero (first vote minus 1 minute, or now if no votes)
            $startAt = $votes->isNotEmpty()
                ? $votes->first()->copy()->subMinute()->toIso8601String()
                : now()->toIso8601String();

            array_unshift($points, ['t' => $startAt, 'y' => 0]);

            return [
                'id' => $calon->id,
                'nama' => $calon->nama,
                'nomor' => $calon->nomor,
                'points' => $points,
            ];
        });

        return response()->json($series);
    }
}
