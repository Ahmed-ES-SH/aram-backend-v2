<?php

namespace App\Models;

use App\Helpers\TextNormalizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Organization extends Model
{

    use HasApiTokens, Notifiable, HasFactory;


    protected $fillable = [
        'email',
        'password',
        'title',
        'description',
        'location',
        'features',
        'accaptable_message',
        'unaccaptable_message',
        'confirmation_price',
        'confirmation_status',
        'phone_number',
        'open_at',
        'verification_code',
        'close_at',
        'url',
        'email_verified',
        'email_verified_at',
        'email_verification_token',
        'rating',
        'status',
        'order',
        'image',
        'logo',
        'booking_status',
        'is_signed',
        'number_of_reservations',
        'account_type',
        'cooperation_file',
        'active'
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'location' => 'array', // Laravel will always return it as array
    ];


    public function subCategories()
    {
        return $this->belongsToMany(
            SubCategory::class,
            'organization_sub_categories',   // pivot table
            'organization_id',           // FK in pivot
            'subcategory_id'             // FK in pivot
        );
    }


    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'organization_categories',   // pivot table
            'organization_id',           // FK in pivot
            'category_id'             // FK in pivot
        );
    }




    public function offers()
    {
        return $this->hasMany(Offer::class);
    }


    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'organization_keywords', 'organization_id', 'keyword_id')
            ->withTimestamps();
    }


    public function benefits()
    {
        return $this->hasMany(OrganizationBenefit::class);
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

    public function  notifications()
    {
        return $this->hasMany(Notification::class, 'recipient_id')->where('recipient_type', 'organization');
    }


    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id')
            ->where('receiver_type', 'organization');
    }


    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id')
            ->where('sender_type', 'organization');
    }


    public function conversationsAsFirst()
    {
        return $this->morphMany(Conversation::class, 'participantOne');
    }

    public function conversationsAsSecond()
    {
        return $this->morphMany(Conversation::class, 'participantTwo');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }





    // Many-to-Many with Coupons
    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_center')
            ->withTimestamps();
    }

    public function scopeSearchNormalized($query, $term)
    {
        if (!$term) {
            return $query;
        }

        $normalizedQuery = TextNormalizer::normalizeArabic($term);

        $normalizedTitle = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(title, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
        $normalizedEmail = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(email, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
        $normalizedDescription = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(description, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";

        return $query->where(function ($q) use ($normalizedQuery, $normalizedTitle, $normalizedEmail, $normalizedDescription) {
            $q->whereRaw("$normalizedTitle LIKE ?", ["%$normalizedQuery%"])
                ->orWhereRaw("$normalizedEmail LIKE ?", ["%$normalizedQuery%"])
                ->orWhereRaw("$normalizedDescription LIKE ?", ["%$normalizedQuery%"]);
        });
    }

    public static function generateUniqueOrder()
    {
        $retry = 0;
        $maxRetries = 3;

        while ($retry < $maxRetries) {
            try {
                // Lock table and get latest order safely
                $maxOrder = self::lockForUpdate()->max('order') ?? 0;
                return $maxOrder + 1;
            } catch (\Illuminate\Database\QueryException $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $retry++;
                    usleep(100000); // wait 100ms and retry
                    continue;
                }
                throw $e;
            }
        }

        throw new \Exception('Failed to assign unique order after multiple attempts.');
    }
}
