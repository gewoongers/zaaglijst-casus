<?php

namespace App\Http\Controllers;

class ProductionStateController extends Controller
{
    public function index()
    {
        $data = json_decode(file_get_contents(storage_path() . "/data/ProductieStaat.json"), true);
        
        // filter voor individueel saw object
        foreach($data as $part) {
            $saws[] = $part['saw'];
            // filter voor de unique titels en stop het in een array
            foreach($saws as $saw) {
                if(isset($saw['profielkleur']['title'])) {
                    $title = $saw['profielkleur']['title'];
                    $uniqueTitles[] = $saw['profielkleur']['title'];
                }
            }
        }
        
        $uniqueTitles = array_unique($uniqueTitles);
        $uniqueTitles = array_values($uniqueTitles);

        // pak een individueel saw object
        foreach($saws as $saw => $profielen) {
            // filter alle arrays op G(nummer)
            $filteredSubArrays = array_filter($profielen, function($key) {
                return preg_match('/^.*g\d+$/i', $key);

            }, ARRAY_FILTER_USE_KEY);

            // Geef de profielen een andere naam
            foreach($filteredSubArrays as $key => $subArray) {
                preg_match('/g(\d+)/i', $key, $matches);
                $renamedSubArrays['G'. $matches[1]] = ['length' => $subArray['value'] , 'count' => $subArray['amount']];
            }

            foreach($uniqueTitles as $title) {
                if(isset($profielen['profielkleur']['title'])) {
                    if($profielen['profielkleur']['title'] === $title) {
                        $uniqueTitlesAssoc[$title] = $renamedSubArrays;
                    }
                }
            }
        }

        // dd($uniqueTitlesAssoc);

        return response()->json($uniqueTitlesAssoc);

    }
}