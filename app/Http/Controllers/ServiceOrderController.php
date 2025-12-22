<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceOrderResource;
use App\Http\Traits\ApiResponse;
use App\Models\ServiceOrder;
use Exception;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    use ApiResponse;


    public function index()
    {
        try {
            $orders = ServiceOrder::orderBy("created_at", "desc")->with(
                'service',
                'service.galleryImages',
                'service.trackings',
                'invoice'
            )->paginate(15);


            if ($orders->total() === 0) {
                return $this->noContentResponse();
            }


            return $this->paginationResponse($orders, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }



    public function adminShow(ServiceOrder $serviceOrder)
    {
        try {

            $serviceOrder->load([
                'service',
                'service.galleryImages',
                'service.trackings',
                'invoice',
            ]);

            if ($serviceOrder->user_type === 'user') {
                $serviceOrder->load('user:id,name,email,image');
            } else {
                $serviceOrder->load('organization:id,title as name,email,logo as image');
            }

            $this->normalizeOrder($serviceOrder);

            return $this->successResponse(
                new ServiceOrderResource($serviceOrder),
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }




    public function userServiceOrders(Request $request)
    {
        try {
            $user = $request->user();

            $orders = ServiceOrder::where('user_id', $user->id)
                ->where('user_type', $user->account_type)
                ->with([
                    'service',
                    'service.galleryImages',
                    'service.trackings',
                    'invoice'
                ])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            if ($orders->isEmpty()) {
                return $this->noContentResponse();
            }

            // 🔥 Normalize before response
            $orders->getCollection()->transform(function ($order) {
                return $this->normalizeOrder($order);
            });

            return $this->paginationResponse($orders, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function showOrder(ServiceOrder $serviceOrder, Request $request)
    {
        $serviceOrder->load([
            'service',
            'service.galleryImages',
            'service.trackings',
            'invoice',
        ]);


        $this->normalizeOrder($serviceOrder);

        return $this->successResponse(
            new ServiceOrderResource($serviceOrder),
            200
        );
    }

    private function normalizeOrder(ServiceOrder $order): ServiceOrder
    {
        $metadata = $order->metadata;

        if ($this->isJson($metadata)) {
            $metadata = json_decode($metadata, true);
        }

        if (
            is_array($metadata) &&
            isset($metadata['items']['metadata']) &&
            $this->isJson($metadata['items']['metadata'])
        ) {
            $metadata['items']['metadata'] = json_decode(
                $metadata['items']['metadata'],
                true
            );
        }

        $order->metadata = $metadata;

        return $order;
    }

    private function isJson($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
