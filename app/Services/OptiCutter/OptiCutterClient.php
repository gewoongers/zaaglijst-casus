<?php

namespace App\Services\OptiCutter;

use Illuminate\Support\Facades\Http;

class OptiCutterClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly string $bearerToken,
    )
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatus(): array
    {
        return Http::withToken($this->bearerToken)->get($this->baseUrl . 'status')->json();
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function linearCuttingPlan(array $data): array
    {
        $response = Http::withToken($this->bearerToken)
            ->post($this->baseUrl . 'linear', $data);

        throw_if($response->json('errors'), new \Exception($response->json('errors.0.detail')));

        return $response->json();
    }
}
