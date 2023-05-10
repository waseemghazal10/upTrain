<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class skillsPrograms extends Model
{
    use HasFactory;

    protected $fillable = [
        'skill_id',
        'program_id'
    ];
}
