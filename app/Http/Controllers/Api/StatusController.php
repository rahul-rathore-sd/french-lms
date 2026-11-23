<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Vocabulary;
use App\Support\Api;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $out = ['name' => 'TCF Canada Prep', 'version' => '6.0.0', 'status' => 'operational', 'timestamp' => Api::nowIso()];
        if (Api::actor($request) !== null) {
            $out['statistics'] = [
                'materials' => Material::count(),
                'materials_daily' => Material::where('source', 'daily')->count(),
                'vocabulary' => Vocabulary::count(),
            ];
        }

        return Api::json($out);
    }
}
