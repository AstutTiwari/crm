<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    public $timestamps = true;
    protected $table = "employes";
    protected $appends = ['full_name'];
    protected $fillable = [
        'id',
        'company_id',
        'firstname',
        'lastname',
        'phone',
        'email',
    ];
    public function getFullNameAttribute($value) 
    {
       return ucfirst($this->firstname). ' '.ucfirst($this->lastname);
    }
    public function company()
    {
        return $this->belongsTo('App\Company','company_id');
    }
}
