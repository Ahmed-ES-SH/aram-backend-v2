<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePage extends Model
{
    protected $fillable = ['image', 'video'];

    public function getFillable()
    {
        $dynamic = [];
        for ($i = 1; $i <= 30; $i++) {
            $dynamic[] = 'column_' . $i;
        }

        return array_merge(parent::getFillable(), $dynamic);
    }
}
