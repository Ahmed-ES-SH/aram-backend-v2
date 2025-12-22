<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = ['title']; // Adjust based on your table structure



    public function cards()
    {
        return $this->belongsToMany(Card::class, 'card_keywords', 'keyword_id', 'card_id')
            ->withTimestamps();
    }


    public function organizations()
    {
        return $this->belongsToMany(Card::class, 'organization_keywords', 'keyword_id', 'organization_id')
            ->withTimestamps();
    }
}
