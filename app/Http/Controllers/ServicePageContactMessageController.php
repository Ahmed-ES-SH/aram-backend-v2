<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceContactMessageRequest;
use App\Http\Services\NotificationService;
use App\Http\Traits\ApiResponse;
use App\Models\ServicePage;
use App\Models\ServicePageContactMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class ServicePageContactMessageController extends Controller
{

    use ApiResponse;
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function store(StoreServiceContactMessageRequest $request)
    {
        try {

            $data = $request->validated();
            $service = ServicePageContactMessage::create($data);

            $adminsIds = User::where('role', 'admin')->pluck('id')->toArray();
            $sender = User::where('id', '1')->where('role', 'admin')->first();
            $service  = ServicePage::where('id', $data['service_page_id'])->select('slug')->first();


            $notificationData = [
                'user_ids' => $adminsIds,
                'sender_type' => 'user',
                'content' => "لديك رسالة جديدة من صفحة الخدمة " . $service->slug,
            ];

            $this->notificationService->sendMultipleNotifications($notificationData, $sender);

            return $this->successResponse($service, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }



    public function update(ServicePageContactMessage $messaage, Request $request)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,processing,completed',
            ]);

            $status = $request->status;

            $messaage->update([
                'status' => $status
            ]);

            return $this->successResponse($messaage, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }



    public function destroy(ServicePageContactMessage $messaage)
    {
        try {
            $messaage->delete();
            return $this->successResponse($messaage, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
