<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'address',
        'zipcode',
        'city',
        'country',
        'latitude',
        'longitude',
    ];
}
