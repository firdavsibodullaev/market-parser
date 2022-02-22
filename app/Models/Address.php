<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region',
        'city',
        'street',
        'house',
        'post',
        'lon',
        'lat',
        'namebrand',
        'typeshop',
        'tc',
        'uin',
        'sqTorg',
    ];
}
