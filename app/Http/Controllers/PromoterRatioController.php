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
                'signup_points' => 'sometimes|numeric',
                'purchase_points' => 'sometimes|numeric',
                'visit_points' => 'sometimes|numeric',
            ]);

            $ratios = PromoterRatio::findOrFail(1);
            $ratios->update($request->only('signup_points', 'purchase_points', 'visit_points'));
            return $this->successResponse($ratios, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
