<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'fName',
        'collage_id',
        'employee_id'
    ];

    public function student()
    {
        return $this->hasMany(Student::class);
    }

    public function college()
    {
        return $this->belongsTo(College::class);
    }

    public function branch()
    {
        return $this->hasMany(Branch::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
