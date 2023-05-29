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

    public function getStatus()
    {
        $response = Http::withToken($this->bearerToken)->get($this->baseUrl . 'status');
        return $response->json();
    }
}
