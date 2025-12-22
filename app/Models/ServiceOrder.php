<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrder extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
        'invoice_id',
        'service_page_id',
        'metadata',
    ];


    protected $casts = [
        'metadata' => 'array'
    ];



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function organization()
    {
        return $this->belongsTo(Organization::class, 'user_id');
    }


    public function service()
    {
        return $this->belongsTo(ServicePage::class, 'service_page_id');
    }


    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
