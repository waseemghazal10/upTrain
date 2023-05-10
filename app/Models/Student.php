<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'sPhone_number',
        'sPhoto',
        'user_id',
        'trainer_id',
        'program_id',
        'field_id',
        'company_id'
    ];


    public function getPhotoAttribute()
    {
        if (!isset($this->attributes['photo']) || $this->attributes['photo'] === null || $this->attributes['photo'] === '') {
            return "";
        }

        $image = asset('studentProfile/' . $this->attributes['photo']);
        return $image;
    }


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

    public function company()
    {
        return $this->belongsTo(Company::class);
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
