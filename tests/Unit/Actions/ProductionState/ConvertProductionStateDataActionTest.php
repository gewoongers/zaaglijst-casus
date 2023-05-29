<?php

namespace Tests\Unit\Actions\ProductionState;

use App\Actions\ProductionState\ConvertProductionStateDataAction;
use Tests\TestCase;

class ConvertProductionStateDataActionTest extends TestCase
{
    private ConvertProductionStateDataAction $action;
    private array $productionStateData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->action = $this->app->make(ConvertProductionStateDataAction::class);

        $this->productionStateData = [
            [
                "saw" => [
                    "staanderg62" => [
                        "title" => "Staander G62",
                        "type" => "staander",
                        "color" => "#303334",
                        "amount" => 2,
                        "value" => 1987,
                        "unit" => "mm",
                        "QR" => "?D:QR_identiek\\OD_QR?P:G62_OD_FRAME_SLOT?F:DH?L:1987?V1:1022?V2:679?V3:656?V4:0?V5:0?V6:0",
                        "print" => [
                            [
                                "value" => "{orderNumber}, opdek-{doorCount}, Loop, Rechts, 1987",
                                "amount" => 1
                            ]
                        ],
                        "order" => 1
                    ],
                    "staanderverstekg61" => [
                        "title" => "Staander verstek G61",
                        "type" => "staander",
                        "color" => "#303334",
                        "amount" => 2,
                        "value" => 1987,
                        "unit" => "mm",
                        "QR" => "?D:QR_identiek\\OD_QR?P:G61_OD_KLIK_SLOT?F:DH?L:1987?V1:1022?V2:238?V3:734?V4:764?V5:0",
                        "print" => [
                            [
                                "value" => "{orderNumber}, opdek-{doorCount}, Loop, Rechts, 1987",
                                "amount" => 1
                            ]
                        ],
                        "order" => 1.1
                    ],
                    "exactinputopdekdeur_zagen" => [
                        "title" => "-",
                        "type" => "staander",
                        "color" => "#303334",
                        "amount" => 1,
                        "value" => 0,
                        "unit" => "mm",
                        "QR" => "",
                        "order" => 1
                    ],
                    "Onderprofielg41g60" => [
                        "title" => "Onderprofiel G41 + G60",
                        "type" => "ligger",
                        "color" => "#303334",
                        "amount" => 1,
                        "value" => 785,
                        "unit" => "mm",
                        "QR" => "",
                        "order" => 22
                    ],
                    "profielkleur" => [
                        "title" => "PROFIELKLEUR: RAL 7021 Zwartgrijs",
                        "type" => "window",
                        "color" => "#303334",
                        "amount" => null,
                        "value" => null,
                        "unit" => "",
                        "QR" => "",
                        "order" => 0.01
                    ]
                ],
            ]
        ];
    }

    public function test_can_convert_production_state_data(): void
    {
        $productionStateData = json_encode($this->productionStateData);

        $productionState = $this->action->execute($productionStateData);

        $this->assertJson($productionState);
        $this->assertArrayHasKey('PROFIELKLEUR: RAL 7021 Zwartgrijs', $productionState[0]);
        $this->assertArrayHasKey('G62', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
        $this->assertArrayHasKey('G61', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
        $this->assertArrayHasKey('G41', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
        $this->assertArrayHasKey('G60', $productionState[0]['PROFIELKLEUR: RAL 7021 Zwartgrijs']);
    }

    public function test_error_when_no_production_state_data_found(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No production state data found');

        $this->action->execute(false);
    }
}
