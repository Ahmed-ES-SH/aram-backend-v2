<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Mail\SendOTPCode;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\PromotionActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Exception;

class AuthController extends Controller
{

    use ApiResponse;

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }


    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'social_id' => $googleUser->getId(),
                    'social_type' => 'google',
                    'image' => $googleUser->getAvatar(),
                ]
            );

            if (!$user->image && $googleUser->getAvatar()) {
                $user->update(['image' => $googleUser->getAvatar()]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return redirect()->to(
                config('app.frontend_url') . '/auth/callback?' . http_build_query([
                    'token' => $token,
                ])
            );
        } catch (\Throwable $e) {

            return redirect()->to(
                config('app.frontend_url') . '/auth/callback?' . http_build_query([
                    'error' => 'google_login_failed',
                ])
            );
        }
    }




    public function login(Request $request)
    {
        try {
            // Basic validation
            $validation = Validator::make($request->all(), [
                'login' => 'required|string',
                'password' => 'required|string'
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'errors' => $validation->errors()
                ], 422);
            }

            $loginInput = $request->input('login');
            $password = $request->input('password');

            $account = null; // can be User or Organization
            $type = null;    // 'user' or 'organization'

            // If email: try user first, then organization
            if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
                $account = User::where('email', $loginInput)->first();
                if ($account) {
                    $type = 'user';
                } else {
                    $account = Organization::where('email', $loginInput)->first();
                    if ($account) $type = 'organization';
                }
            }
            // If phone format: try user first, then organization
            elseif (preg_match('/^\+?[0-9]{8,15}$/', $loginInput)) {
                $account = User::where('phone', $loginInput)->first();
                if ($account) {
                    $type = 'user';
                } else {
                    $account = Organization::where('phone_number', $loginInput)->first();
                    if ($account) $type = 'organization';
                }
            } else {
                return response()->json([
                    'message' => 'Invalid login format'
                ], 422);
            }

            // Not found
            if (!$account) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Check password
            if (!Hash::check($password, $account->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Ensure model supports token creation (HasApiTokens)
            if (!method_exists($account, 'createToken')) {
                return response()->json([
                    'message' => 'API tokens not enabled on the model. Add Laravel\Sanctum\HasApiTokens trait to the model.'
                ], 500);
            }

            // Create token
            $tokenResult = $account->createToken('auth_token');
            $plainToken = $tokenResult->plainTextToken;
            $account->is_signed = 1;


            // Then count unread messages
            $unreadMessagesCount = $account->receivedMessages()
                ->where('is_read', false)
                ->count();

            // Then count unread notifications
            $unreadNoftificationsCount = $account->notifications()
                ->where('is_read', false)
                ->count();


            $account->save();

            // Hide sensitive fields
            if (method_exists($account, 'makeHidden')) {
                $account->makeHidden(['password', 'remember_token']);
            }


            $notifications = Notification::where('recipient_id', $account->id)
                ->where('recipient_type', $type)
                ->where('is_read', false)
                ->with('sender')
                ->get();

            return response()->json([
                'message' => ucfirst($type) . ' login successful',
                'account' => $account,
                'unread_count' => $unreadMessagesCount,
                'unread_notifications_count' => $unreadNoftificationsCount,
                'notifications' => $notifications,
                'token' => $plainToken,
                'type' => $type,
                'data' => true
            ], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    private function generateOTP($length = 5)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }
        return $otp;
    }

    public function sendOTP(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            $email = $request->email;
            $otp = $this->generateOTP();

            // حفظ أو تحديث OTP في جدول password_reset_tokens
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => $otp,
                    'created_at' => Carbon::now()
                ]
            );

            // إرسال البريد
            Mail::to($email)->send(new SendOTPCode($otp));

            return response()->json([
                'message' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني.'
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:5'
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$record) {
            return response()->json(['message' => 'الكود غير صحيح.'], 422);
        }

        // تحقق من صلاحية الكود (5 دقائق)
        if (Carbon::now()->diffInMinutes($record->created_at) > 5) {
            return response()->json(['message' => 'انتهت صلاحية الكود.'], 422);
        }

        return response()->json(['message' => 'تم التحقق بنجاح.']);
    }


    public function resetPassword(Request $request)
    {
        // 1 - Validate inputs
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // 2 - Check OTP from password_reset_tokens
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)
            ->first();

        if (!$resetRecord) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired OTP'
            ], 400);
        }

        // 3 - Update user password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // 4 - Delete OTP after use
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password has been reset successfully'
        ]);
    }




    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $user->currentAccessToken()->delete();
            $user->is_signed = 0;
            $user->save();
            return response()->json(['message' => 'Logged out successfully'], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getCurrentUser(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            // Count unread messages
            $unreadMessagesCount = $user->receivedMessages()
                ->where('is_read', false)
                ->count();

            // Count unread notifications
            $unreadNotificationsCount = $user->notifications()
                ->where('is_read', false)
                ->count();

            // Detect user type dynamically
            $type = $user instanceof Organization ? 'organization' : 'user';

            if ($type === 'organization') {
                $user->load(['subCategories', 'categories', 'keywords', 'benefits']);
            }

            // -------------------------
            // ✅ Handle promoter stats
            // -------------------------
            $promoter = $user->promoter;



            if ($promoter && $promoter->status === 'active') {

                $signups = PromotionActivity::where('promoter_id', $promoter->promoter_id)
                    ->where('promoter_type', $type)
                    ->where('activity_type', 'signup')
                    ->count();

                $purchases = PromotionActivity::where('promoter_id', $promoter->promoter_id)
                    ->where('promoter_type', $type)
                    ->where('activity_type', 'purchase')
                    ->count();

                $earnings = PromotionActivity::where('promoter_id', $promoter->promoter_id)
                    ->where('promoter_type', $type)
                    ->whereNotNull('commission_amount')
                    ->sum('commission_amount');

                // Update promoter stats only if values changed
                $promoter->update([
                    'total_signups' => $signups,
                    'total_purchases' => $purchases,
                    'total_earnings' => $earnings,
                ]);
            }

            // Determine if current user is a promoter
            $isPromoter = $promoter && $promoter->status === 'active';

            return response()->json([
                'data' => $user,
                'unread_count' => $unreadMessagesCount,
                'unread_notifications_count' => $unreadNotificationsCount,
                'type' => $type,
                'is_promoter' => $isPromoter,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
