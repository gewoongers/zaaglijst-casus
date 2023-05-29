<?php

namespace App\Http\Controllers;

use App\Actions\ProductionState\ConvertProductionStateDataAction;
use Illuminate\Http\JsonResponse;

class ProductionStateController extends Controller
{
    public function __invoke(
        ConvertProductionStateDataAction $convertProductionStateDataAction,
    ): bool|JsonResponse|string {
        $productionState = file_get_contents(storage_path() . "/data/ProductieStaat.json");

        try {
            return json_encode($convertProductionStateDataAction->execute($productionState));
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
