<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;


    protected $fillable = [
        'cEmail',
        'cName',
        'cPassword',
        'cWebSite',
        'cLocation',
        'cDescription',
        'cPhoto',
        'verification_token'
    ];

    protected $hidden = [
        'cPassword',
        'remember_token',
    ];

    public function getPhotoAttribute()
    {
        if (!isset($this->attributes['photo']) || $this->attributes['photo'] === null || $this->attributes['photo'] === '') {
            return "";
        }

        $image = asset('companyProfile/' . $this->attributes['photo']);
        return $image;
    }


    public function trainer()
    {
        return $this->hasMany(Trainer::class);
    }

    public function program()
    {
        return $this->hasMany(Program::class);
    }
}
