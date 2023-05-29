<?php

namespace App\Http\Controllers;

class ProductionStateController extends Controller
{
    public function __invoke(

    )
    {
        $productionState = file_get_contents(storage_path() . "/data/ProductieStaat.json");


    }
}
