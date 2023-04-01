<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function program()
    {
        return $this->hasMany(Program::class);
    }
}
