<?php

namespace Tests\Unit\Services\OptiCutter;

use App\Services\OptiCutter\OptiCutterClient;
use Tests\TestCase;

class OptiCutterClientTest extends TestCase
{
    private OptiCutterClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->app->make(OptiCutterClient::class);
    }

    public function test_can_get_status(): void
    {
        $status = $this->client->getStatus();

        $this->assertIsArray($status);
        $this->assertEquals('OK', $status['message']);
        $this->assertEquals(0, $status['remaining']);
    }
}
