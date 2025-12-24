<?php

namespace App\Models;

use App\Helpers\TextNormalizer;
use Illuminate\Database\Eloquent\Model;

class Promoter extends Model
{
    protected $fillable = [
        'promoter_type',
        'promoter_id',
        'referral_code',
        'total_visits',
        'total_signups',
        'total_purchases',
        'total_purchases_services',
        'total_earnings',
        'discount_percentage',
        'status',
    ];


    /**
     * Get the parent promotable model (user or center).
     */
    public function promoter()
    {
        return $this->belongsTo(User::class, 'promoter_id');
    }


    public function scopeSearchInPromoterData($query, $term)
    {
        if (!$term) {
            return $query;
        }

        $normalizedQuery = TextNormalizer::normalizeArabic($term);

        return $query->whereHas('promoter', function ($q) use ($normalizedQuery) {
            $q->where(function ($subQuery) use ($normalizedQuery) {
                $normalizedName = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(name, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
                $normalizedEmail = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(email, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
                $normalizedPhone = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(phone, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";
                $normalizedReferralCode = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(referral_code, 'ة', 'ه'), 'ى', 'ي'), 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ؤ', 'و'))";

                $subQuery->whereRaw("$normalizedName LIKE ?", ["%$normalizedQuery%"])
                    ->orWhereRaw("$normalizedEmail LIKE ?", ["%$normalizedQuery%"])
                    ->orWhereRaw("$normalizedPhone LIKE ?", ["%$normalizedQuery%"])
                    ->orWhereRaw("$normalizedReferralCode LIKE ?", ["%$normalizedQuery%"]);
            });
        });
    }


    /**
     * Generate a new unique referral code.
     */
    public static function generateReferralCode(): string
    {
        do {
            $code = strtoupper(bin2hex(random_bytes(4))); // 8-character random code
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }
    public function referrals()
    {
        return $this->hasMany(Referral::class);
    }

    public function activities()
    {
        return $this->hasMany(PromotionActivity::class, 'promoter_id', 'promoter_id');
    }
}
