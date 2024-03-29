<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'skName',
    ];
    public function program()
    {
        return $this->belongsToMany(Program::class,'skills_programs','skill_id','program_id');
    }

    public function student()
    {
        return $this->belongsToMany(Student::class,'skills_students','skill_id','student_id');
    }
}
