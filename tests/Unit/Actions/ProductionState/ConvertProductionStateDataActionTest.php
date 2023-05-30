<?php

namespace Tests\Unit\Actions\ProductionState;

use App\Actions\ProductionState\ConvertProductionStateDataAction;
use Tests\TestCase;

class ConvertProductionStateDataActionTest extends TestCase
{
    private ConvertProductionStateDataAction $action;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = $this->app->make(ConvertProductionStateDataAction::class);
    }

    public function test_can_convert_production_state_data(): void
    {
        $productionStateData = file_get_contents(storage_path() . "/data/ProductieStaat.json");

        $productionState = $this->action->execute($productionStateData);

        $this->assertJson($productionState);
        $this->assertArrayHasKey('PROFIELKLEUR: RAL 7021 Zwartgrijs', $productionState[0]);
        $this->assertArrayHasKey('G62', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
        $this->assertArrayHasKey('G61', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
        $this->assertArrayHasKey('G41', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
        $this->assertArrayHasKey('G40', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
        $this->assertArrayHasKey('G67', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
    }

    public function test_error_when_no_production_state_data_found(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No production state data found');

        $this->action->execute(false);
    }
}
