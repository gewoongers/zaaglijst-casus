<?php

namespace App\Providers;

use App\Services\OptiCutter\OptiCutterClient;
use Illuminate\Support\ServiceProvider;

class OptiCutterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(OptiCutterClient::class, function () {
            return new OptiCutterClient(
                config('services.opticutter.url'),
                config('services.opticutter.bearer_token'),
            );
        });
    }
}
