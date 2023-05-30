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

    public function test_can_get_linear_cutting_plan()
    {
        $data = [
            'stocks' => [
                [
                    'length' => 1987,
                    'count' => 2,
                ],
            ],
            'requirements' => [
                [
                    'length' => '250',
                    'count' => 5,
                ],
                [
                    'length' => '557',
                    'count' => 2,
                ],
            ],
            'settings' => [
                'kerf' => '1',
            ]
        ];

        $plan = $this->client->linearCuttingPlan($data);

        $solution = $plan['solution'];
        $requiredStocks = $plan['solution']['requiredStocks'];
        $layouts = $plan['solution']['layouts'];
        $this->assertIsArray($plan);
        $this->assertEquals(2, $solution['totalRequiredStocks']);
        $this->assertEquals(2, $requiredStocks[0]['count']);
        $this->assertEquals(1987, $requiredStocks[0]['length']);
        $this->assertEquals(1, $layouts[0]['count']);
        $this->assertEquals(1987, $layouts[0]['stock']['length']);
        $this->assertEquals(557, $layouts[0]['parts'][0]['length']);
        $this->assertEquals(1, $layouts[0]['parts'][0]['count']);
        $this->assertEquals(250, $layouts[0]['parts'][1]['length']);
        $this->assertEquals(5, $layouts[0]['parts'][1]['count']);
        $this->assertEquals(6, $layouts[0]['waste']['cut']);
        $this->assertEquals(174, $layouts[0]['waste']['material']);
    }

    public function test_get_linear_cutting_plan_fails(): void
    {
        $data = [
            'stocks' => [
                [
                    'length' => 100,
                    'count' => 2,
                ],
            ],
            'requirements' => [
                [
                    'length' => '250',
                    'count' => 5,
                ],
                [
                    'length' => '557',
                    'count' => 2,
                ],
            ],
            'settings' => [
                'kerf' => '1',
            ]
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Must be equal or smaller than the longest stock (100).');

        $this->client->linearCuttingPlan($data);
    }
}
