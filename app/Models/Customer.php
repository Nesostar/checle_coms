<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address'
    ];

    // Relationship: A customer has many invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Relationship: A customer has many quotations
    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
