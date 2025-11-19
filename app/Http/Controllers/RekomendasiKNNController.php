<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Rute;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekomendasiKNNController extends Controller
{
    public function index(Request $request)
    {
        $routes = Rute::with('kendaraans')->get();
        $recommendations = null;

        if ($request->filled(['rute_id', 'jam_keberangkatan'])) {
            $ruteId = (int) $request->rute_id;
            $targetTime = Carbon::createFromFormat('H:i', $request->jam_keberangkatan);

            $dataset = Pesanan::with(['kendaraan'])
                ->where('rute_id', $ruteId)
                ->whereIn('status', ['dikonfirmasi', 'dalam_perjalanan', 'selesai'])
                ->get();

            $neighbors = $dataset
                ->map(function (Pesanan $pesanan) use ($targetTime) {
                    $distance = $this->timeDistance($targetTime, Carbon::createFromFormat('H:i:s', $pesanan->jam_keberangkatan));
                    return [
                        'distance' => $distance,
                        'pesanan' => $pesanan,
                    ];
                })
                ->sortBy('distance')
                ->take(3);

            if ($neighbors->isNotEmpty()) {
                $avgMinutes = $neighbors->avg(fn ($row) => $this->timeInMinutes($row['pesanan']->jam_keberangkatan));
                $recommendedTime = Carbon::createFromTimeString('00:00')->addMinutes((int) round($avgMinutes));
                $kendaraanFavorit = $neighbors
                    ->pluck('pesanan')
                    ->filter(fn ($p) => !empty($p->kendaraan_id))
                    ->groupBy('kendaraan_id')
                    ->sortByDesc(fn ($group) => $group->count())
                    ->keys()
                    ->first();

                $recommendations = [
                    'waktu' => $recommendedTime->format('H:i'),
                    'kendaraan' => $kendaraanFavorit,
                    'totalSampel' => $dataset->count(),
                ];
            }
        }

        return view('rekomendasi.index', [
            'routes' => $routes,
            'recommendations' => $recommendations,
        ]);
    }

    private function timeDistance(Carbon $a, Carbon $b): int
    {
        return abs($a->diffInMinutes($b, false));
    }

    private function timeInMinutes(string $time): int
    {
        [$hour, $minute] = explode(':', $time);
        return ((int) $hour * 60) + (int) $minute;
    }
}
