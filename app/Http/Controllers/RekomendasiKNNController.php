<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Rute;
use Illuminate\Http\Request;

class RekomendasiKNNController extends Controller
{
    public function index()
    {
        return view('rekomendasi.index', [
            'rute' => Rute::all(),
            'dataset' => $this->buildDataset(),
        ]);
    }

    public function rekomendasi(Request $request)
    {
        $data = $request->validate([
            'jam_keberangkatan' => 'required',
            'rute_id' => 'required|exists:rutes,id',
            'status_sebelumnya' => 'required|in:menunggu,dikonfirmasi,selesai,dibatalkan',
            'k' => 'nullable|integer|min:1|max:10',
        ]);

        $dataset = $this->buildDataset();
        $k = $data['k'] ?? 3;

        $inputVector = [
            $this->jamToNumber($data['jam_keberangkatan']),
            $this->ruteToNumber($data['rute_id']),
            $this->statusToNumber($data['status_sebelumnya']),
        ];

        $neighbors = $this->nearestNeighbors($inputVector, $dataset, $k);
        $rekomendasi = collect($neighbors)
            ->groupBy('label')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();

        return back()->with([
            'rekomendasi' => $rekomendasi,
            'neighbors' => $neighbors,
            'input' => $data,
        ]);
    }

    private function buildDataset(): array
    {
        $history = Pesanan::with('rute')
            ->whereIn('status', ['dikonfirmasi', 'selesai'])
            ->get();

        if ($history->isEmpty()) {
            return [
                ['features' => [$this->jamToNumber('07:00'), $this->ruteToNumber(1), $this->statusToNumber('dikonfirmasi')], 'label' => '08:00'],
                ['features' => [$this->jamToNumber('09:00'), $this->ruteToNumber(2), $this->statusToNumber('selesai')], 'label' => '09:30'],
                ['features' => [$this->jamToNumber('13:00'), $this->ruteToNumber(3), $this->statusToNumber('menunggu')], 'label' => '14:00'],
                ['features' => [$this->jamToNumber('15:00'), $this->ruteToNumber(1), $this->statusToNumber('selesai')], 'label' => '15:30'],
                ['features' => [$this->jamToNumber('17:00'), $this->ruteToNumber(2), $this->statusToNumber('dikonfirmasi')], 'label' => '17:30'],
            ];
        }

        return $history->map(function ($pesanan) {
            return [
                'features' => [
                    $this->jamToNumber($pesanan->jam_keberangkatan),
                    $this->ruteToNumber($pesanan->rute_id),
                    $this->statusToNumber($pesanan->status),
                ],
                'label' => $pesanan->jam_keberangkatan,
            ];
        })->toArray();
    }

    private function nearestNeighbors(array $input, array $dataset, int $k): array
    {
        $scored = collect($dataset)->map(function ($row) use ($input) {
            $distance = $this->euclideanDistance($input, $row['features']);
            return [
                'label' => $row['label'],
                'distance' => $distance,
            ];
        })->sortBy('distance')->values()->take($k);

        return $scored->toArray();
    }

    private function euclideanDistance(array $a, array $b): float
    {
        return sqrt(collect($a)->zip($b)->reduce(function ($carry, $pair) {
            [$x, $y] = $pair;
            return $carry + pow($x - $y, 2);
        }, 0));
    }

    private function jamToNumber(string $jam): int
    {
        [$hour, $minute] = explode(':', $jam);
        return ((int) $hour) * 60 + (int) $minute;
    }

    private function ruteToNumber(int $ruteId): int
    {
        return $ruteId;
    }

    private function statusToNumber(string $status): int
    {
        return match ($status) {
            'menunggu' => 0,
            'dikonfirmasi' => 1,
            'selesai' => 2,
            'dibatalkan' => 3,
            default => 0,
        };
    }
}
