<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\GHNService;
use Illuminate\Http\Request;

class GHNController extends Controller
{
    public function getProvinces(GHNService $ghn)
    {
        return response()->json($ghn->getProvinces());
    }

    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    public function getWards(int $districtId, GHNService $ghn)
    {
        $response = $ghn->getWards($districtId);
        if (!empty($response['data']) && is_array($response['data'])) {
            $response['data'] = array_values(array_filter($response['data'], function ($w) {
                return (!isset($w['Status']) || $w['Status'] != 3) && (!isset($w['SupportType']) || $w['SupportType'] != 0);
            }));
        }
        return response()->json($response);
    }

    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $request->validate([
            'to_district_id' => 'required|integer',
            'to_ward_code' => 'required|string',
        ]);

        $cart = session('cart', []);
        if (empty($cart) && auth()->check()) {
            $cart = auth()->user()->cart ?? [];
        }

        $weight = collect($cart)->sum(
            fn ($item) => (int) ($item['weight'] ?? config('services.ghn.default_weight', 200))
            * (int) ($item['quantity'] ?? 1)
        );

        return response()->json($ghn->calculateFee(array_merge([
            'from_district_id' => (int) config('services.ghn.from_district_id', 3440),
            'from_ward_code'   => (string) config('services.ghn.from_ward_code', '13010'),
            'to_district_id'   => (int) $request->to_district_id,
            'to_ward_code'     => (string) $request->to_ward_code,
        ], $ghn->packageParameters($weight))));
    }
}
