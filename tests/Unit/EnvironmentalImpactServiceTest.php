<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Dashboard\EnvironmentalImpactService;
use Tests\TestCase;

class EnvironmentalImpactServiceTest extends TestCase
{
    private EnvironmentalImpactService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EnvironmentalImpactService;
    }

    public function test_calculates_co2e_kg_correctly(): void
    {
        // 1000 kg waste * 1.18305 = 1183.05 kg CO2e
        $result = $this->service->calculateCo2eKg(1000.0);

        $this->assertEqualsWithDelta(1183.05, $result, 0.01);
    }

    public function test_calculates_co2e_ton_correctly(): void
    {
        // 1000 kg * 1.18305 / 1000 = 1.18305 ton
        $result = $this->service->calculateCo2eTon(1000.0);

        $this->assertEqualsWithDelta(1.18305, $result, 0.001);
    }

    public function test_calculate_returns_all_required_metrics(): void
    {
        $result = $this->service->calculate(1000.0);

        $this->assertArrayHasKey('co2_saved_kg', $result);
        $this->assertArrayHasKey('co2_saved_ton', $result);
        $this->assertArrayHasKey('trees_saved', $result);
        $this->assertArrayHasKey('cars_removed', $result);
        $this->assertArrayHasKey('energy_saved_kwh', $result);
        $this->assertArrayHasKey('household_energy', $result);
        $this->assertArrayHasKey('landfill_saved_m3', $result);
        $this->assertArrayHasKey('water_saved_liters', $result);
        $this->assertArrayHasKey('fossil_energy_saved_mj', $result);
        $this->assertArrayHasKey('fuel_saved_liters', $result);
    }

    public function test_handles_zero_waste(): void
    {
        $result = $this->service->calculate(0.0);

        $this->assertEquals(0.0, $result['co2_saved_kg']);
        $this->assertEquals(0.0, $result['co2_saved_ton']);
        $this->assertEquals(0.0, $result['trees_saved']);
        $this->assertEquals(0.0, $result['cars_removed']);
    }

    public function test_trees_calculation(): void
    {
        // 1183.05 kg CO2 / 21 kg per tree = 56.335 trees
        $co2Kg = 1183.05;
        $result = $this->service->calculateTreesEquivalent($co2Kg);

        $this->assertEqualsWithDelta(56.335, $result, 0.01);
    }

    public function test_cars_calculation(): void
    {
        // 1183.05 kg CO2 / 4600 kg per car = 0.257 cars
        $co2Kg = 1183.05;
        $result = $this->service->calculateCarsEquivalent($co2Kg);

        $this->assertEqualsWithDelta(0.257, $result, 0.01);
    }

    public function test_energy_saved_calculation(): void
    {
        // 1000 kg * 2.5 kWh/kg = 2500 kWh
        $result = $this->service->calculateEnergySaved(1000.0);

        $this->assertEqualsWithDelta(2500.0, $result, 0.01);
    }

    public function test_landfill_saved_calculation(): void
    {
        // 1000 kg * 0.0015 m3/kg = 1.5 m3
        $result = $this->service->calculateLandfillSaved(1000.0);

        $this->assertEqualsWithDelta(1.5, $result, 0.01);
    }

    public function test_water_saved_calculation(): void
    {
        // 1000 kg * 25 liters/kg = 25000 liters
        $result = $this->service->calculateWaterSaved(1000.0);

        $this->assertEqualsWithDelta(25000.0, $result, 0.01);
    }

    public function test_full_calculation_integration(): void
    {
        $result = $this->service->calculate(1000.0);

        // Verify CO2 calculation
        $this->assertEqualsWithDelta(1183.05, $result['co2_saved_kg'], 0.01);
        $this->assertEqualsWithDelta(1.18305, $result['co2_saved_ton'], 0.001);

        // Verify derived calculations use CO2 value
        $this->assertEqualsWithDelta(56.335, $result['trees_saved'], 0.01);
        $this->assertEqualsWithDelta(0.257, $result['cars_removed'], 0.01);

        // Verify energy calculations
        $this->assertEqualsWithDelta(2500.0, $result['energy_saved_kwh'], 0.01);
        $this->assertEqualsWithDelta(1.136, $result['household_energy'], 0.01);

        // Verify other metrics
        $this->assertEqualsWithDelta(1.5, $result['landfill_saved_m3'], 0.01);
        $this->assertEqualsWithDelta(25000.0, $result['water_saved_liters'], 0.01);
        $this->assertEqualsWithDelta(35000.0, $result['fossil_energy_saved_mj'], 0.01);
        $this->assertEqualsWithDelta(1093.75, $result['fuel_saved_liters'], 0.01);
    }
}
