<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;


    protected $fillable = [
        'phone_number',
        'photo',
        'verification_token'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->hasMany(Field::class);
    }
}
