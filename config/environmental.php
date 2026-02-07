<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | CO2 Emission Calculation Constants (IPCC 2006 Guidelines)
    |--------------------------------------------------------------------------
    |
    | Environmental impact calculations based on international standards:
    | - IPCC 2006 Guidelines for National Greenhouse Gas Inventories
    | - FAO International Standards for tree carbon absorption
    |
    | Global Warming Potential (GWP) values from IPCC guidelines.
    | Emission Factors (EF) for municipal solid waste composition.
    |
    */

    'gwp' => [
        'co2' => 1,
        'ch4' => 21,     // GWP for Methane (IPCC small city standard)
        'n2o' => 310,    // GWP for Nitrous Oxide (IPCC small city standard)
    ],

    'emission_factors' => [
        'ch4' => 0.0065,   // kg CH4 per kg waste (IPCC 2006)
        'n2o' => 0.00015,  // kg N2O per kg waste (IPCC 2006)
    ],

    /*
    |--------------------------------------------------------------------------
    | Pre-calculated Combined Multiplier (IPCC Formula)
    |--------------------------------------------------------------------------
    |
    | International Formula: 1 + (EF_CH4 * GWP_CH4) + (EF_N2O * GWP_N2O)
    | = 1 + (0.0065 * 21) + (0.00015 * 310)
    | = 1 + 0.1365 + 0.0465
    | = 1.18305
    |
    | This multiplier converts kg of waste to kg of CO2 equivalent (CO2e)
    |
    */
    'co2e_multiplier' => 1.18305,

    /*
    |--------------------------------------------------------------------------
    | Derived Impact Factors (International Standards)
    |--------------------------------------------------------------------------
    |
    | Additional conversion factors for environmental impact metrics.
    | Based on international scientific research and standards.
    |
    | Trees Equivalent Formula (FAO & IPCC):
    |   Trees = (CO2e in kg) / 21
    |   One mature tree absorbs approximately 21 kg CO2 per year
    |
    */

    'derived' => [
        'tree_co2_absorption_kg_per_year' => 21,    // 1 mature tree absorbs 21 kg CO2/year (FAO/IPCC)
        'car_co2_emission_kg_per_year' => 4600,     // 1 car emits 4600 kg CO2/year (EPA)
        'energy_saved_kwh_per_kg' => 2.5,           // kWh saved per kg recycled (waste-to-energy studies)
        'landfill_volume_m3_per_kg' => 0.0015,      // m3 landfill space per kg waste
        'household_energy_kwh_per_year' => 2200,    // Average Indonesian household consumption
        'water_saved_liters_per_kg' => 25,          // Water saved per kg recycled material
        'fossil_energy_mj_per_kg' => 35,            // MJ fossil energy saved per kg recycled
        'fuel_mj_per_liter' => 32,                  // MJ per liter gasoline equivalent
    ],
];
