<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationBenefit extends Model
{
    protected $fillable = [
        'title',
        'organization_id'
    ];


    public function organization()
    {
        return $this->belongsTo(Card::class);
    }
}
