<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    public function program()
    {
        return $this->belongsToMany(Program::class);
    }

    public function student()
    {
        return $this->belongsToMany(Student::class);
    }
}
