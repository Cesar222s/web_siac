<?php

namespace App\Http\Controllers;

use App\Services\SupervisedModelService;
use App\Services\MultiSupervisedModelsService;

class ModelController extends Controller
{
    public function show()
    {
        $filePath = storage_path('app/data/atus_2017.csv');
        $single = (new SupervisedModelService($filePath))->evaluate();
        $multi = (new MultiSupervisedModelsService($filePath))->evaluateAll();
        return view('supervised', compact('single','multi'));
    }
}
