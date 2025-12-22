<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Helpers\TextNormalizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_number',
        'name',
        'email',
        'password',
        'role',
        'status',
        'failed_attempts',
        'last_login_at',
        'account_type',
        'image',
        'phone',
        'country',
        'location',
        'gender',
        'birth_date',
        'social_id',
        'social_type',
        'is_signed',
        'email_verified_at',
        'email_verification_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'location' => 'array'
        ];
    }

    public function promoter()
    {
        return $this->hasOne(Promoter::class, 'promoter_id');
    }

    public function messages()
    {
        return $this->morphMany(Message::class, 'sender');
    }

    public function conversationsAsFirst()
    {
        return $this->morphMany(Conversation::class, 'participantOne');
    }

    public function conversationsAsSecond()
    {
        return $this->morphMany(Conversation::class, 'participantTwo');
    }

    public function  notifications()
    {
        return $this->hasMany(Notification::class, 'recipient_id')->where('recipient_type', 'user');
    }

    // Messages sent by the user
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id')
            ->where('sender_type', 'user');
    }

    // Messages received by the user
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id')
            ->where('receiver_type', 'user');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_user')
            ->withTimestamps();
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }


    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id');
    }




    public function getIdNumberAttribute($value)
    {
        if (!$value) {
            return null;
        }

        return substr($value, 0, 3) . '-xxx-xxx-xx';
    }

    public function scopeSearchNormalized($query, $term)
    {
        if (!$term) {
            return $query;
        }

        $normalizedQuery = TextNormalizer::normalizeArabic($term);

        $normalizedName = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(name, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
        $normalizedEmail = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(email, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
        $normalizedPhone = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
        $normalizedCountry = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(country, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";

        return $query->where(function ($q) use ($normalizedQuery, $normalizedName, $normalizedEmail, $normalizedPhone, $normalizedCountry) {
            $q->whereRaw("$normalizedName LIKE ?", ["%$normalizedQuery%"])
                ->orWhereRaw("$normalizedEmail LIKE ?", ["%$normalizedQuery%"])
                ->orWhereRaw("$normalizedPhone LIKE ?", ["%$normalizedQuery%"])
                ->orWhereRaw("$normalizedCountry LIKE ?", ["%$normalizedQuery%"]);
        });
    }


    public function scopeFilterNonPromoters($query)
    {
        return $query->whereDoesntHave('promoter', function ($q) {
            $q->where('promoter_type', 'user');
        });
    }
}
