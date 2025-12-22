<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price_before_discount',
        'price',
        'number_of_promotional_purchases',
        'duration',
        'image',
        'order',
        'active',
        'category_id',
    ];


    public function category()
    {
        return $this->belongsTo(CardCategory::class);
    }


    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'card_keywords', 'card_id', 'keyword_id')
            ->withTimestamps();
    }


    public function benefits()
    {
        return $this->hasMany(CardBenefit::class);
    }
}
