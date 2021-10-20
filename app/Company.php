<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $timestamps = true;
    protected $table = "companies";
    protected $fillable = [
        'id',
        'name',
        'email',
        'logo',
    ];
}
