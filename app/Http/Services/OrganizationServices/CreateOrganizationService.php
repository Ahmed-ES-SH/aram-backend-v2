<?php

namespace App\Http\Services\OrganizationServices;

use App\Http\Traits\ApiResponse;
use App\Mail\VerifyEmail;
use App\Models\Organization;
use App\Models\Promoter;
use App\Models\PromotionActivity;
use App\Models\Referral;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use App\Http\Services\ImageService;
use App\Models\PromoterRatio;

class CreateOrganizationService
{

    use ApiResponse;

    protected $imageservice;

    public function __construct(ImageService $imageService)
    {
        $this->imageservice = $imageService;
    }



    /////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////  Store Organization with offer ///////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////
    public function StoreOgranizationWithOffer($request)
    {
        try {
            Log::info('🚀 Starting StoreOgranizationWithOffer');

            $refCode = $request->ref_code;
            $organizationData = $request->validated();

            Log::info('📝 Validated data received', ['keys' => array_keys($organizationData)]);

            // احتفظ فقط بالحقول المطلوبة من البيانات الموثقة
            $organizationData = collect($organizationData)
                ->only([
                    'email',
                    'password',
                    'title',
                    'description',
                    'phone_number',
                    'open_at',
                    'close_at',
                    'confirmation_price',
                    'confirmation_status',
                    'booking_status',
                ])
                ->toArray();

            $offerData = $request->input('offer');

            // التعامل مع كلمة المرور
            if ($request['password']) {
                $organizationData['password'] = Hash::make($request['password']);
            }

            // Decode location if sent as JSON string
            if ($request->has('location') && is_string($request->location)) {
                $organizationData['location'] = json_decode($request->location, true);
            } elseif ($request->has('location')) {
                $organizationData['location'] = $request->location;
            }

            // 🔒 Use transaction for entire process
            return DB::transaction(function () use ($request, $organizationData, $offerData, $refCode) {

                // Create the organization with unique order
                $organizationData['order'] = Organization::generateUniqueOrder();
                $organizationData['account_type'] = 'organization';
                $organization = Organization::create($organizationData);

                Log::info('✅ Organization created', ['id' => $organization->id]);

                // Generate and assign a unique verification token
                $organization->email_verification_token = sha1(time());
                $organization->save();

                // Handle Referral
                if ($refCode) {
                    Log::info('🔗 Processing referral', ['ref_code' => $refCode]);
                    $this->processReferral($organization, $refCode, $request->ip(), $request->device_type);
                }

                if ($request['image']) {
                    Log::info('🖼️ Uploading organization image...');
                    $this->imageservice->ImageUploaderwithvariable(
                        $request,
                        $organization,
                        'images/organizations',
                        'image'
                    );
                    Log::info('✅ Organization image uploaded successfully');
                }

                if ($request['logo']) {
                    Log::info('🏷️ Uploading organization logo...');
                    $this->imageservice->ImageUploaderwithvariable(
                        $request,
                        $organization,
                        'images/logo-organizations',
                        'logo'
                    );
                    Log::info('✅ Organization logo uploaded successfully');
                }

                // Update Main Categories if provided
                if ($request['categories']) {
                    Log::info('📁 Syncing categories', ['categories' => $request['categories']]);
                    $organization->categories()->sync($request['categories']);
                }

                // Update subCategories if provided
                if ($request['subcategories']) {
                    Log::info('📂 Syncing sub_categories', ['sub_categories' => $request->subcategories]);
                    $organization->subCategories()->sync($request->subcategories);
                }

                // Update keywords if provided
                if ($request['keywords']) {
                    Log::info('🏷️ Syncing keywords');
                    $this->syncKeywords($organization, $request->keywords);
                }

                // Create offer linked to this organization
                Log::info('🎁 Creating offer', ['offer_data' => $offerData]);
                $offer = $organization->offers()->create($offerData);
                Log::info('✅ Offer created', ['offer_id' => $offer->id]);

                // Handle offer image upload directly
                $offerImageFile = $request->file('offer.image') ?? ($request['offer']['image'] ?? null);
                Log::info('🖼️ Checking offer image', [
                    'has_offer_image' => $offerImageFile !== null,
                    'is_file' => $offerImageFile instanceof \Illuminate\Http\UploadedFile
                ]);

                if ($offerImageFile && $offerImageFile instanceof \Illuminate\Http\UploadedFile) {
                    Log::info('🖼️ Uploading offer image...');

                    // Generate unique filename
                    $originalName = pathinfo($offerImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $offerImageFile->getClientOriginalExtension();
                    $filename = $originalName . '_' . uniqid() . '.' . $extension;

                    $storagePath = 'images/offers';

                    // Move the file to public path
                    $offerImageFile->move(public_path($storagePath), $filename);
                    $fullImagePath = url('/') . '/' . $storagePath . '/' . $filename;

                    // Delete old image if exists
                    $old_image = $offer->image;
                    if ($old_image) {
                        $old_image_name = basename(parse_url($old_image, PHP_URL_PATH));
                        $file_path = public_path($storagePath . '/' . $old_image_name);
                        if (File::exists($file_path)) {
                            File::delete($file_path);
                        }
                    }

                    // Update offer image
                    $offer->image = $fullImagePath;
                    $offer->save();

                    Log::info('✅ Offer image uploaded successfully', ['path' => $fullImagePath]);
                }

                // Send verification email (outside transaction concern - won't rollback)
                try {
                    Mail::to($organization->email)->send(new VerifyEmail($organization));
                    Log::info('📧 Verification email sent');
                } catch (Exception $e) {
                    Log::error('❌ Error sending organization verification email', ['error' => $e->getMessage()]);
                }

                Log::info('🎉 Transaction completed successfully');

                return [
                    'offer' => $offer,
                    'organization' => $organization
                ];
            });
        } catch (Exception $e) {
            Log::error('❌ StoreOgranizationWithOffer failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


    /////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////  Store Organization without offer ///////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////



    public function store($request)
    {
        try {
            $data = $request->validated();

            // 🔐 Encrypt password if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Decode location if sent as JSON string
            if ($request->has('location') && is_string($request->location)) {
                $data['location'] = json_decode($request->location, true);
            }

            // ✅ Safe unique "order" generation
            $organization = DB::transaction(function () use ($data) {
                return Organization::create(array_merge($data, [
                    'order' => Organization::generateUniqueOrder(),
                ]));
            });

            // 🖼️ Handle image upload
            if ($request->hasFile('image')) {
                $this->imageservice->ImageUploaderwithvariable(
                    $request,
                    $organization,
                    'images/organizations',
                    'image'
                );
            }

            // 🖼️ Handle logo upload
            if ($request->hasFile('logo')) {
                $this->imageservice->ImageUploaderwithvariable(
                    $request,
                    $organization,
                    'images/logo-organizations',
                    'logo'
                );
            }

            // 🧩 Create related benefits
            if ($request->has('benefits')) {
                foreach ($request->benefits as $benefit) {
                    $organization->benefits()->create([
                        'title' => $benefit['title'],
                    ]);
                }
            }

            // 🧭 Sync main & sub categories
            if ($request->has('categories')) {
                $organization->categories()->sync($request->categories);
            }

            if ($request->has('sub_categories')) {
                $organization->subCategories()->sync($request->sub_categories);
            }

            // 🏷️ Sync keywords (accepts IDs or objects)
            if ($request->has('keywords')) {
                $this->syncKeywords($organization, $request->keywords);
            }

            return $organization;
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////// help functions //////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////

    // Sync Keywords
    private function syncKeywords(Organization $organization, $keywords)
    {
        $keywordIds = collect($keywords)
            ->map(function ($item) {
                if (is_array($item) && isset($item['id'])) {
                    return $item['id'];
                }
                if (is_object($item) && isset($item->id)) {
                    return $item->id;
                }
                return $item;
            })
            ->toArray();

        $organization->keywords()->sync($keywordIds);
    }




    // Process Referral
    private function processReferral(Organization $organization, string $refCode, ?string $ip, $device_type = null)
    {
        $promoter = Promoter::where('referral_code', $refCode)->first();
        $ratios = PromoterRatio::find(1);

        if ($promoter) {
            // Create Referral Record
            Referral::create([
                'promoter_id' => $promoter->id,
                'referred_user_id' => $organization->id,
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
                'member_id' => $organization->id,
                'member_type' => 'organization',
                'activity_type' => 'signup',
                'device_type' => $device_type,
                'commission_amount' => $ratios->signup_ratio,
                'metadata' => ['user_id' => $organization->id, 'account_type' => 'organization'],
                'ip_address' => $ip,
                'ref_code' => $refCode,
            ]);
        }
    }
}
