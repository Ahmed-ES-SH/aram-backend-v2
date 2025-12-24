<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\PromoterRatio;
use Illuminate\Http\Request;
use Exception;

class PromoterRatioController extends Controller
{
    use ApiResponse;


    public function getRatiosRatio()
    {
        try {
            $ratios = PromoterRatio::findOrFail(1);
            return $this->successResponse($ratios, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function updateRatiosRatio(Request $request)
    {
        try {
            $request->validate([
                'signup_ratio' => 'sometimes|numeric',
                'purchase_ratio' => 'sometimes|numeric',
                'visit_ratio' => 'sometimes|numeric',
                'service_ratio' => 'sometimes|numeric',
            ]);

            $ratios = PromoterRatio::findOrFail(1);
            $ratios->update($request->only('signup_ratio', 'purchase_ratio', 'visit_ratio', 'service_ratio'));
            return $this->successResponse($ratios, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
