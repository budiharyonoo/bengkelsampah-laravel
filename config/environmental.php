<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | CO2 Emission Calculation Constants (Small City Model)
    |--------------------------------------------------------------------------
    |
    | Global Warming Potential (GWP) values from IPCC guidelines.
    | Emission Factors (EF) for small city waste composition.
    |
    */

    'gwp' => [
        'co2' => 1,
        'ch4' => 21,     // GWP for Methane (small city)
        'n2o' => 310,    // GWP for Nitrous Oxide (small city)
    ],

    'emission_factors' => [
        'ch4' => 0.0065,   // kg CH4 per kg waste
        'n2o' => 0.00015,  // kg N2O per kg waste
    ],

    /*
    |--------------------------------------------------------------------------
    | Pre-calculated Combined Multiplier
    |--------------------------------------------------------------------------
    |
    | Formula: 1 + (EF_CH4 * GWP_CH4) + (EF_N2O * GWP_N2O)
    | = 1 + (0.0065 * 21) + (0.00015 * 310)
    | = 1 + 0.1365 + 0.0465
    | = 1.18305
    |
    */
    'co2e_multiplier' => 1.18305,

    /*
    |--------------------------------------------------------------------------
    | Derived Impact Factors
    |--------------------------------------------------------------------------
    |
    | Additional conversion factors for environmental impact metrics.
    |
    */

    'derived' => [
        'tree_co2_absorption_kg_per_year' => 21,    // 1 tree absorbs 21 kg CO2/year
        'car_co2_emission_kg_per_year' => 4600,     // 1 car emits 4600 kg CO2/year
        'energy_saved_kwh_per_kg' => 2.5,           // kWh saved per kg recycled
        'landfill_volume_m3_per_kg' => 0.0015,      // m3 landfill per kg
        'household_energy_kwh_per_year' => 2200,    // Average Indonesian household
        'water_saved_liters_per_kg' => 25,          // Water saved per kg
        'fossil_energy_mj_per_kg' => 35,            // MJ fossil energy per kg
        'fuel_mj_per_liter' => 32,                  // MJ per liter gasoline
    ],
];
