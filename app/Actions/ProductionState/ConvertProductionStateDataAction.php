<?php

namespace App\Actions\ProductionState;

use Illuminate\Support\Collection;

class ConvertProductionStateDataAction
{
    public function execute(
        string|bool $productionStateJson
    ): Collection
    {
        throw_if(! $productionStateJson, new \Exception('No production state data found'));

        /** @var string $productionStateJson */
        $productionState = json_decode($productionStateJson, true);

        /** @phpstan-ignore-next-line */
        return collect($productionState)->map(function ($productionState) {
            $headKey = '';
            $newSawData = [];

            foreach ($productionState['saw'] as $key => $saw) {
                if ($key === 'profielkleur') {
                    $headKey = $saw['title'];
                    continue;
                }

                if (!$this->containsProfile($saw['title'])) {
                    continue;
                }

                $splitTitle = explode(' ', $saw['title']);
                foreach ($splitTitle as $text) {
                    if ($this->containsProfile($text)) {
                        $newSawData[$text] = [
                            'length' => $saw['value'],
                            'count' => $saw['amount'],
                        ];
                    }
                }
            }

            return [
                $headKey => $newSawData
            ];
        });
    }

    private function containsProfile(string $string): int|bool
    {
        return preg_match("/G\d{2}/", $string);
    }
}
