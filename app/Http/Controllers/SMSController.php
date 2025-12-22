<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;


class SMSController extends Controller
{


    public function send(Request $request)
    {
        // ✅ Validate request
        $request->validate([
            'message' => 'required|string|max:918', // SMS max length
            'numbers' => 'required|array',
            'numbers.*' => 'string|regex:/^[0-9]+$/',
            'lang'    => 'nullable|in:0,64', // 0 = English, 64 = Arabic
            'schedule' => 'nullable|date_format:m/d/Y H:i:s',
            'referenceIds' => 'nullable|string'
        ]);

        // ✅ Prepare payload
        $payload = [
            'UserId'       => env('ISMARTSMS_USER'),
            'Password'     => env('ISMARTSMS_PASS'),
            'MobileNo'     => implode(',', $request->numbers), // multiple numbers allowed
            'Message'      => $request->message,
            'PushDateTime' => $request->schedule ?? '',
            'Lang'         => $request->lang ?? 0,
            'Header'       => env('ISMARTSMS_HEADER'),
            'referenceIds' => $request->referenceIds ?? ''
        ];

        try {
            // ✅ Send SMS request to API
            $response = Http::asForm()->timeout(10)->post(env('ISMARTSMS_URL'), $payload);

            return response()->json([
                'status'      => $response->successful() ? 'success' : 'failed',
                'return_code' => $response->body()
            ]);
        } catch (RequestException $e) {
            // ✅ Network/connection errors
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to connect to SMS gateway',
                'error'   => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // ✅ Any other errors
            return response()->json([
                'status'  => 'error',
                'message' => 'Unexpected error occurred',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
