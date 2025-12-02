<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KMeansService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Parámetros (extensibles): tipo, hora, día
        $k = (int)($request->input('k', 5));
        $filePath = storage_path('app/data/atus_2017.csv');

        $service = new KMeansService($filePath, $k, 25);
        $result = $service->cluster();

        $zonesCount = is_array($result['centroids'] ?? null) ? count($result['centroids']) : 0;

        return view('dashboard', [
            'k' => $k,
            'available' => $result['available'] ?? true,
            'message' => $result['message'] ?? null,
            'zonesCount' => $zonesCount,
            'clusters' => $result['centroids'] ?? [],
            'counts' => $result['counts'] ?? [],
            'points' => $result['points'] ?? [],
            'columns' => $result['columns'] ?? [],
        ]);
    }
}