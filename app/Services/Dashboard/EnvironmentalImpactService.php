<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

/**
 * Service for calculating environmental impact metrics.
 *
 * Uses IPCC 2006 Guidelines for National Greenhouse Gas Inventories
 * and international standards (FAO, EPA) for environmental impact calculations.
 *
 * CO2 Equivalent (tCO2e) Formula Components:
 * - GWP_CO2 = 1 (Global Warming Potential for CO2)
 * - GWP_CH4 = 21 (Global Warming Potential for Methane - IPCC)
 * - GWP_N2O = 310 (Global Warming Potential for Nitrous Oxide - IPCC)
 * - EF_CH4 = 0.0065 (Emission Factor for Methane)
 * - EF_N2O = 0.00015 (Emission Factor for Nitrous Oxide)
 *
 * Combined Multiplier: 1.18305 = 1 + (0.0065 * 21) + (0.00015 * 310)
 * Formula: waste_kg × 1.18305 / 1000 = tCO2e
 *
 * Trees Equivalent Formula (FAO/IPCC International Standard):
 * - 1 mature tree absorbs approximately 21 kg CO2 per year
 * Formula: (CO2e in kg) / 21 = Trees needed for one year of carbon absorption
 */
final class EnvironmentalImpactService
{
    /**
     * Get all environmental impact metrics for given waste amount.
     *
     * @param  float  $wasteKg  Total waste in kilograms
     * @return array{
     *     co2_saved_kg: float,
     *     co2_saved_ton: float,
     *     trees_saved: float,
     *     cars_removed: float,
     *     energy_saved_kwh: float,
     *     household_energy: float,
     *     landfill_saved_m3: float,
     *     water_saved_liters: float,
     *     fossil_energy_saved_mj: float,
     *     fuel_saved_liters: float
     * }
     */
    public function calculate(float $wasteKg): array
    {
        $co2SavedKg = $this->calculateCo2eKg($wasteKg);
        $energySavedKwh = $this->calculateEnergySaved($wasteKg);
        $fossilEnergySavedMj = $this->calculateFossilEnergySaved($wasteKg);

        return [
            'co2_saved_kg' => $co2SavedKg,
            'co2_saved_ton' => $co2SavedKg / 1000,
            'trees_saved' => $this->calculateTreesEquivalent($co2SavedKg),
            'cars_removed' => $this->calculateCarsEquivalent($co2SavedKg),
            'energy_saved_kwh' => $energySavedKwh,
            'household_energy' => $this->calculateHouseholdsEquivalent($energySavedKwh),
            'landfill_saved_m3' => $this->calculateLandfillSaved($wasteKg),
            'water_saved_liters' => $this->calculateWaterSaved($wasteKg),
            'fossil_energy_saved_mj' => $fossilEnergySavedMj,
            'fuel_saved_liters' => $this->calculateFuelSaved($fossilEnergySavedMj),
        ];
    }

    /**
     * Calculate CO2 equivalent in kilograms using IPCC 2006 formula.
     *
     * Formula: waste_kg * (1 + EF_CH4*GWP_CH4 + EF_N2O*GWP_N2O)
     * = waste_kg * 1.18305
     *
     * This represents the amount of CO2 emissions prevented by recycling
     * instead of sending waste to landfill.
     */
    public function calculateCo2eKg(float $wasteKg): float
    {
        $multiplier = (float) config('environmental.co2e_multiplier', 1.18305);

        return $wasteKg * $multiplier;
    }

    /**
     * Calculate CO2 equivalent in tons.
     */
    public function calculateCo2eTon(float $wasteKg): float
    {
        return $this->calculateCo2eKg($wasteKg) / 1000;
    }

    /**
     * Calculate trees equivalent using international standard.
     *
     * Formula: CO2_kg / 21 = Number of trees
     * Based on FAO/IPCC standard: 1 mature tree absorbs ~21 kg CO2/year
     *
     * This represents how many mature trees would be needed to absorb
     * the same amount of CO2 over one year.
     */
    public function calculateTreesEquivalent(float $co2Kg): float
    {
        $absorption = (float) config('environmental.derived.tree_co2_absorption_kg_per_year', 21);

        return $absorption > 0 ? $co2Kg / $absorption : 0;
    }

    /**
     * Calculate cars equivalent (1 car emits 4600 kg CO2/year).
     */
    public function calculateCarsEquivalent(float $co2Kg): float
    {
        $emission = (float) config('environmental.derived.car_co2_emission_kg_per_year', 4600);

        return $emission > 0 ? $co2Kg / $emission : 0;
    }

    /**
     * Calculate energy saved in kWh.
     */
    public function calculateEnergySaved(float $wasteKg): float
    {
        $factor = (float) config('environmental.derived.energy_saved_kwh_per_kg', 2.5);

        return $wasteKg * $factor;
    }

    /**
     * Calculate households equivalent energy usage.
     */
    public function calculateHouseholdsEquivalent(float $energyKwh): float
    {
        $householdUsage = (float) config('environmental.derived.household_energy_kwh_per_year', 2200);

        return $householdUsage > 0 ? $energyKwh / $householdUsage : 0;
    }

    /**
     * Calculate landfill space saved in m3.
     */
    public function calculateLandfillSaved(float $wasteKg): float
    {
        $factor = (float) config('environmental.derived.landfill_volume_m3_per_kg', 0.0015);

        return $wasteKg * $factor;
    }

    /**
     * Calculate water saved in liters.
     */
    public function calculateWaterSaved(float $wasteKg): float
    {
        $factor = (float) config('environmental.derived.water_saved_liters_per_kg', 25);

        return $wasteKg * $factor;
    }

    /**
     * Calculate fossil energy saved in MJ.
     */
    public function calculateFossilEnergySaved(float $wasteKg): float
    {
        $factor = (float) config('environmental.derived.fossil_energy_mj_per_kg', 35);

        return $wasteKg * $factor;
    }

    /**
     * Calculate fuel equivalent in liters.
     */
    public function calculateFuelSaved(float $fossilEnergyMj): float
    {
        $mjPerLiter = (float) config('environmental.derived.fuel_mj_per_liter', 32);

        return $mjPerLiter > 0 ? $fossilEnergyMj / $mjPerLiter : 0;
    }
}
