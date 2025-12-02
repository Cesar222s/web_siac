<?php

namespace App\Http\Controllers;


class HomeController extends Controller
{
    public function index()
    {
        $filePath = storage_path('app/data/atus_2017.csv');
        $summary = null;
        $mapPoints = [];
        try {
            $svc = new \App\Services\SupervisedKNNService($filePath, 3000, 5);
            $summary = $svc->evaluate();
            // Extrae puntos (lat, lon) básicos para mapa desde CSV
            if (file_exists($filePath)) {
                $fh = fopen($filePath, 'r');
                $header = fgetcsv($fh);
                if ($header) {
                    $idx = array_flip($header);
                    $latCol = $idx['lat'] ?? null; $lonCol = $idx['lon'] ?? null;
                    $count = 0;
                    while (($row = fgetcsv($fh)) !== false && $count < 1000) {
                        if ($latCol !== null && $lonCol !== null) {
                            $lat = $row[$latCol] ?? null; $lon = $row[$lonCol] ?? null;
                            if (is_numeric($lat) && is_numeric($lon)) {
                                $mapPoints[] = [floatval($lat), floatval($lon)];
                                $count++;
                            }
                        }
                    }
                }
                fclose($fh);
            }
        } catch (\Throwable $e) {
            $summary = ['available' => false, 'message' => 'Error al evaluar: '.$e->getMessage()];
        }
        return view('home', ['supervised' => $summary, 'mapPoints' => $mapPoints]);
    }

    public function about()
    {
        return view('about');
    }
}
