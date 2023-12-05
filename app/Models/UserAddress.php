<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'users_addresses';

    protected $fillable = [
        'user_id',
        'country_id',
        'zip_code',
        'region_id',
        'city_id',
        'district_id',
        'street',
        'building',
        'flat',
        'phone'
    ];
}
