<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\KMeansService;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Parameters can be extended to filter by tipo, hora, día
        $k = (int)($request->input('k', 5));
        $service = new KMeansService();
        $result = $service->analyze($k);

        // Expected $result: ['centroids' => [...], 'counts' => [...], 'points' => [...]]
        $zonesCount = is_array($result['centroids'] ?? null) ? count($result['centroids']) : 0;

        return view('dashboard', [
            'k' => $k,
            'zonesCount' => $zonesCount,
            'clusters' => $result['centroids'] ?? [],
            'counts' => $result['counts'] ?? [],
            'points' => $result['points'] ?? [],
        ]);
    }
}