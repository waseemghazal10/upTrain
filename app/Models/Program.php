<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;


    protected $fillable = [
        'pTitle',
        'pStart_date',
        'pEnd_date',
        'pPhoto',
        'pDescription',
        'pDetails',
        'branch_id',
        'company_id',
        'trainer_id'
    ];

    public function getPhotoAttribute()
    {
        if (!isset($this->attributes['photo']) || $this->attributes['photo'] === null || $this->attributes['photo'] === '') {
            return "";
        }

        $image = asset('programProfile/' . $this->attributes['photo']);
        return $image;
    }


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
        return $this->belongsToMany(Skill::class,'skills_programs','program_id','skill_id');
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
