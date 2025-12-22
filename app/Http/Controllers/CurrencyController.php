<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCurrencyRequest;
use App\Http\Requests\UpdateCurrencyRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{

    use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $currencies = Currency::all();
            return $this->successResponse($currencies, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCurrencyRequest $request)
    {
        try {
            $data = $request->validated();
            $currency = Currency::create($data);
            return $this->successResponse($currency, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($currencyId)
    {
        try {
            $currency = Currency::findOrFail($currencyId);
            return $this->successResponse($currency, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCurrencyRequest $request,  $currencyId)
    {
        try {
            $data = $request->validated();
            $currency = Currency::findOrFail($currencyId);
            $currency->update($data);
            return $this->successResponse($currency, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($currencyId)
    {
        try {
            $currency = Currency::findOrFail($currencyId);
            $currency->delete();
            return $this->successResponse($currency, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
