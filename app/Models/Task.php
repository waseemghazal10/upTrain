<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;


    protected $fillable = [
        'taStatus',
        'taTitle',
        'taDescription',
        'taDeadline',
        'program_id',
        'trainer_id'
    ];


    public function student()
    {
        return $this->belongsToMany(Student::class,'students_tasks','task_id','student_id');;
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
