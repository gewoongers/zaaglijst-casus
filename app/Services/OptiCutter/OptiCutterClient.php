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
}
