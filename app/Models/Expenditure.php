<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Expenditure.php
class Expenditure extends Model
{
    protected $fillable = [
        'user_id',        // ✅ ADD THIS
        'category_id',
        'title',
        'amount',
        'description',
        'date'
    ];

    public function category()
    {
        return $this->belongsTo(ExpenditureCategory::class, 'category_id');
    }
}

