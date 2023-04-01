<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;


    public function student()
    {
        return $this->hasMany(Student::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function application()
    {
        return $this->hasMany(Application::class);
    }

    public function skill()
    {
        return $this->belongsToMany(Skill::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
