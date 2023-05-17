<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;


    protected $fillable = [
        'ePhone_number',
        'ePhoto',
        'eRole',
        'user_id',
        'field_id',
        'verification_token'
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function getPhotoAttribute()
    {
        if (!isset($this->attributes['photo']) || $this->attributes['photo'] === null || $this->attributes['photo'] === '') {
            return "";
        }

        $image = asset('employeeProfile/' . $this->attributes['photo']);
        return $image;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }
}
