<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;


    protected $fillable = [
        'email',
        'name',
        'password',
        'webSite',
        'location',
        'description',
        'photo',
        'verification_token'
    ];

    public function trainer()
    {
        return $this->hasMany(Trainer::class);
    }

    public function program()
    {
        return $this->hasMany(Program::class);
    }
}
