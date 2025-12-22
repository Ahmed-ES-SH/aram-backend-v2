<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Http\Traits\ApiResponse;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{

    use ApiResponse;

    public function index()
    {
        try {
            $Messages = ContactMessage::orderBy('created_at', 'desc')->paginate(30);
            if ($Messages->isEmpty()) {
                return $this->noContentResponse();
            }
            return $this->paginationResponse($Messages, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactMessageRequest $request)
    {
        try {
            $data = $request->validated();
            $message = new ContactMessage();
            $message->fill($data);
            $message->save();
            return $this->successResponse($message, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            return $this->successResponse($message, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            if ($request->has('status')) {
                $message->status = $request->status;
                $message->save();
                return $this->successResponse($message,  200);
            }
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $message = ContactMessage::findOrFail($id);
            $message->delete();
            return $this->successResponse($message, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
