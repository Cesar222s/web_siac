<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class RiskAnalysisController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $prefilledRisk = session()->get('risk_result');
        $error = session()->get('risk_error');
        return view('risk.analysis', ['user' => $user, 'risk' => $prefilledRisk, 'error' => $error]);
    }

    public function analyze(Request $request)
    {
        $data = $request->validate([
            'edad' => 'required|integer|min:18|max:100',
            'experiencia_anios' => 'required|integer|min:0|max:70',
            'ubicacion' => 'required|string|max:180',
            'horario_uso' => 'required|string|max:50',
        ]);

        $apiUrl = config('services.risk_api.url', env('RISK_API_URL', 'http://localhost:8001/analyze'));

        try {
            $response = Http::timeout(20)->post($apiUrl, $data);
            if (!$response->successful()) {
                return view('risk.analysis', [
                    'user' => Auth::user(),
                    'risk' => null,
                    'error' => 'API no disponible (' . $response->status() . ')',
                ]);
            }
            $riskData = $response->json();
            return view('risk.analysis', [
                'user' => Auth::user(),
                'risk' => $riskData,
                'error' => null,
            ]);
        } catch (\Throwable $e) {
            return view('risk.analysis', [
                'user' => Auth::user(),
                'risk' => null,
                'error' => 'Error llamando a API: ' . $e->getMessage(),
            ]);
        }
    }
}
