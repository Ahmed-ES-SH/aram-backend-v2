<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceOrderResource;
use App\Http\Services\NotificationService;
use App\Http\Traits\ApiResponse;
use App\Models\ServiceOrder;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    use ApiResponse;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }


    public function index(Request $request)
    {
        try {
            $filters = $request->all();

            $orders = ServiceOrder::filter($filters)
                ->with([
                    'service',
                    'service.galleryImages',
                    'invoice'
                ])
                ->withCount('tracking')
                ->paginate($request->get('per_page', 15));


            if ($orders->total() === 0) {
                return $this->noContentResponse();
            }

            $orders->getCollection()->transform(function ($order) {
                return $this->normalizeOrder($order);
            });

            return $this->paginationResponse($orders, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function getFilterOptions(): \Illuminate\Http\JsonResponse
    {
        return $this->successResponse([
            'statuses' => ['pending', 'confirmed', 'in_progress', 'on_hold', 'completed', 'canceled', 'refunded'],
            'payment_statuses' => ['pending', 'paid', 'failed'],
            'subscription_statuses' => ['active', 'expired'],
            'user_types' => ['user', 'organization'],
        ], 200);
    }



    public function adminShow(ServiceOrder $serviceOrder)
    {
        try {

            $serviceOrder->load([
                'service',
                'service.galleryImages',
                'tracking',
                'tracking.files',
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
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function checkExpiredSubscriptions()
    {
        try {
            // Trigger the command to handle expiration
            \Illuminate\Support\Facades\Artisan::call('subscriptions:check-expired');

            return $this->successResponse('Expired subscriptions check initiated successfully', 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
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
                    'invoice'
                ])
                ->withCount('tracking')
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


    public function updateStatus(ServiceOrder $serviceOrder, Request $request)
    {
        try {

            $request->validate([
                'status' => 'required|string|in:pending,confirmed,in_progress,on_hold,completed,canceled,refunded',
            ]);

            $serviceOrder->update([
                'status' => $request->status,
            ]);

            return $this->successResponse(
                new ServiceOrderResource($serviceOrder),
                200
            );
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function showOrder(ServiceOrder $serviceOrder, Request $request)
    {
        $serviceOrder->load([
            'service',
            'service.galleryImages',
            'invoice',
            'tracking',
            'tracking.files',
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
