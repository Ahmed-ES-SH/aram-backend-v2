<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Services\ImageService;
use App\Http\Traits\ApiResponse;
use App\Mail\VerifyEmail;
use App\Models\Organization;
use App\Models\Promoter;
use App\Models\PromoterRatio;
use App\Models\PromotionActivity;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;

class UserController extends Controller
{
    use ApiResponse;

    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }



    public function index() // admin Route
    {
        try {
            $users = User::orderBy('created_at', 'desc')
                ->select('id', 'name', 'image', 'email',  'phone', 'role', 'status',  'gender', 'created_at')
                ->paginate(30);

            if ($users->isEmpty()) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($users, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function usersWithSelectedData(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'query' => 'nullable|string|max:255',
                'for_promoters' => 'nullable|boolean'
            ]);

            $query = $validatedData['query'] ?? null;

            if ($request->for_promoters) {
                $users = User::where('role', 'user')->select('id', 'name', 'email', 'image')
                    ->searchNormalized($query)
                    ->FilterNonPromoters()
                    ->paginate(20);

                return $this->paginationResponse($users, 200);
            }

            $users = User::select('id', 'name', 'email', 'image')
                ->searchNormalized($query)
                ->paginate(20);

            if ($users->total() === 0) {
                return $this->noContentResponse();
            }

            return $this->paginationResponse($users, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }





    public function store(StoreUserRequest $request)
    {
        try {
            $data = $request->validated();

            // تشفير كلمة المرور إن وجدت
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->filled('id_number')) {
                $data['id_number'] = Hash::make($request->id_number);
            }

            // إنشاء المستخدم وملء البيانات
            $user = User::create($data);

            // معالجة الصورة إذا تم رفعها
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $user, 'images/users', 'image');
            }

            // Handle Referral
            if ($request->filled('ref_code')) {
                $this->processReferral($user, $request->ref_code, $request->ip(), $request->device_type);
            }


            if ($user->location) {
                if ($this->isJson($user->location)) {
                    $user->location = json_decode($user->location, true);
                }
            }

            return $this->successResponse($user, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->location) {
                if ($this->isJson($user->location)) {
                    $user->location = json_decode($user->location, true);
                }
            }

            return $this->successResponse($user, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $user = User::findOrFail($id);

            // تحديث كلمة المرور بعد التحقق من وجودها
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // تحديث البيانات
            $user->update($data);

            // تحديث الصورة إذا تم رفع صورة جديدة
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable($request, $user, 'images/users', 'image');
            }


            if ($user->location) {
                if ($this->isJson($user->location)) {
                    $user->location = json_decode($user->location, true);
                }
            }

            return $this->successResponse($user->fresh(), 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    public function destroy($id) // admin route
    {
        try {
            $user = User::findOrFail($id);

            // حذف الصورة إذا وُجدت
            if (!empty($user->image)) {
                $this->imageservice->deleteOldImage($user, 'images/users');
            }

            $user->delete();

            return $this->successResponse(['name', $user->name], 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function searchForUsers(Request $request) // admin Route
    {
        try {
            $validatedData = $request->validate([
                'query' => 'required|string|max:255'
            ]);
            $query = $validatedData['query'];

            $users = User::searchNormalized($query)->paginate(30);

            if ($users->total() === 0) {
                return $this->noContentResponse();
            }




            return $this->paginationResponse($users, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function getUsersIds() // admin Route
    {
        try {
            // استخدام cursor() لتحميل البيانات بشكل تدريجي
            $usersIds = User::cursor()->pluck('id')->toArray();

            // التحقق من وجود بيانات
            if (empty($usersIds)) {
                return $this->noContentResponse();
            }

            return $this->successResponse(array_values($usersIds), 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getPublicUsersIds()
    {
        try {
            $users = User::where('status', 'active')
                ->pluck('id')
                ->map(function ($id) {
                    return ['id' => $id]; // ✅ رجع كائن فيه المفتاح id
                })
                ->values(); // لضبط الاندكس يبدأ من 0

            return $this->successResponse($users, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getUsersCount()
    {
        try {
            $usersCount = User::count();
            return $this->successResponse($usersCount, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    public function checkPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        try {
            $user = User::findOrFail($id);

            if (Hash::check($request->password, $user->password)) {
                return $this->successResponse(['Message' => 'Password is Correct'], 'Done', 200);
            } else {
                return $this->errorResponse("Password does not match", 401);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function sendVerifyEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            $isUser = User::where('email', $request->email)->exists();

            $user = $isUser ?  User::where('email', $request->email)->first()
                : Organization::where('email', $request->email)->first();

            if (!$user) {
                return $this->notFoundResponse("User Not Found With This Email");
            }

            if ($user->email_verified_at) {
                return response()->json(['message' => 'تم تفعيل الحساب مسبقًا.'], 400);
            } else {
                // إنشاء رمز تحقق جديد
                $user->email_verification_token = sha1(time());
                $user->save();

                try {
                    Mail::to($user->email)->send(new VerifyEmail($user));
                } catch (Exception $e) {
                    return $this->errorResponse($e->getMessage(), 500);
                }

                return response()->json(['message' => "Email Send Successfully"], 200);
            }
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function verifyEmail($id, Request $request)
    {
        try {
            $account = $request->account_type == 'user' ?  User::find($id) : Organization::find($id);

            if (!$account) {
                return response()->json(['message' => 'الحساب غير موجود.'], 404);
            }

            if ($account->email_verified_at) {
                return response()->json(['message' => 'تم تفعيل الحساب مسبقًا.'], 400);
            }

            if ($account->email_verification_token !== $request->token) {
                return response()->json(['message' => 'رمز التحقق غير صالح.'], 400);
            }


            $PromoterMember = PromotionActivity::where('member_id', $id)->where('member_type', $request->account_type)->first();

            if ($PromoterMember) {
                $PromoterMember->update([
                    'is_active' => '1'
                ]);
            }

            // تحديث حالة الحساب وتفعيل البريد
            $account->email_verified_at = now();
            $account->email_verification_token = null; // إزالة التوكن بعد الاستخدام
            $account->save();

            // إعادة التوجيه إلى الموقع بعد التفعيل
            return redirect(env("FRONTEND_URL") . "/login");
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function processReferral(User $user, string $refCode, ?string $ip, $device_type)
    {
        $promoter = Promoter::where('referral_code', $refCode)->first();
        $ratios = PromoterRatio::find(1);

        if ($promoter) {
            // Create Referral Record
            Referral::create([
                'promoter_id' => $promoter->id,
                'referred_user_id' => $user->id,
                'ip' => $ip,
                'status' => 'converted', // Or pending if you want to verify something first
                'converted_at' => now(),
            ]);

            // Increment Signups
            $promoter->increment('total_signups');

            // Log Activity
            PromotionActivity::create([
                'promoter_type' => $promoter->promoter_type,
                'promoter_id' => $promoter->id,
                'activity_type' => 'signup',
                'device_type' => $device_type,
                'commission_amount' => $ratios->signup_ratio,
                'metadata' => ['user_id' => $user->id, 'account_type' => 'user'],
                'ip_address' => $ip,
                'ref_code' => $refCode,
            ]);
        }
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
