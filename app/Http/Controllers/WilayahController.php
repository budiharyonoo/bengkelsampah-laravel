<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province;
use Laravolt\Indonesia\Models\Village;

class WilayahController extends Controller
{
    public function provinces(): JsonResponse
    {
        $provinces = Province::query()
            ->select('code', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($provinces);
    }

    public function cities(Request $request): JsonResponse
    {
        $cities = City::query()
            ->select('code', 'name')
            ->where('province_code', $request->query('province_code'))
            ->orderBy('name')
            ->get();

        return response()->json($cities);
    }

    public function districts(Request $request): JsonResponse
    {
        $districts = District::query()
            ->select('code', 'name')
            ->where('city_code', $request->query('city_code'))
            ->orderBy('name')
            ->get();

        return response()->json($districts);
    }

    public function villages(Request $request): JsonResponse
    {
        $villages = Village::query()
            ->select('code', 'name')
            ->where('district_code', $request->query('district_code'))
            ->orderBy('name')
            ->get();

        return response()->json($villages);
    }
}
