<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'tPhone_number',
        'tPhoto',
        'user_id',
        'company_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function student()
    {
        return $this->hasMany(Student::class);
    }

    public function program()
    {
        return $this->hasMany(Program::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
