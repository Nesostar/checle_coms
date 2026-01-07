<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['name', 'created_by'];

    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
