<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;


    protected $fillable = [
        'status',
        'cv',
        'details',
        'program_id',
        'student_id'
    ];


    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
