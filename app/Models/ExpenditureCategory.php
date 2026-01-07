<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenditureCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function expenditures()
    {
        return $this->hasMany(Expenditure::class, 'category_id');
    }
}
