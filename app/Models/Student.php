<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }


    public function task()
    {
        return $this->belongsToMany(Task::class);
    }

    public function skill()
    {
        return $this->belongsToMany(Skill::class,'skills_students','student_id','skill_id');
    }

    public function application()
    {
        return $this->hasMany(Application::class);
    }
}
