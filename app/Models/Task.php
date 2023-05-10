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
        'taStart_date',
        'taEnd_date',
        'trainer_id'
    ];


    public function student()
    {
        return $this->belongsToMany(Student::class);
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }
}
