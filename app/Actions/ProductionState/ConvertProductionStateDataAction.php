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
             return $this->filterProfiles($productionState['saw']);
        })
            ->filter(function ($productionState, $key) {
                return count($productionState) > 0;
            });
    }

    private function containsProfile(string $string): int|bool
    {
        return preg_match("/G\d{2}/", $string);
    }

    /**
     * @param array<string, array<string, mixed>> $sawProfiles
     * @return array<int|string, array>
     */
    private function filterProfiles(array $sawProfiles): array
    {
        $color = collect($sawProfiles)->filter(function ($sawProfile, $key) {
            return $key === 'profielkleur';
        })
            ->reduce(function ($carry, $item) {
                return $item;
            }, [])
            ;

        if (! $color) {
            return [];
        }

        /** @phpstan-ignore-next-line */
        $profiles = collect($sawProfiles)->filter(function ($sawProfile) {
            return $this->containsProfile($sawProfile['title']);
        })
            ->map(function ($sawProfile) {
                $splitTitle = explode(' ', $sawProfile['title']);
                foreach ($splitTitle as $text) {
                    if ($this->containsProfile($text)) {
                        return [
                            $text => [
                                'length' => $sawProfile['value'],
                                'count' => $sawProfile['amount'],
                            ]
                        ];
                    }
                }
            })
            ->filter(function ($sawProfile) {
            return $sawProfile !== null;
            })
            ->reduce(function ($carry, $item) {
                return array_merge($carry, $item);
            }, []);

        return [$color['title'] => $profiles];
    }
}
