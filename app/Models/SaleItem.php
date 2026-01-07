<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'item_id',
        'item_name',
        'quantity',
        'price',
        'subtotal',
    ];

    /**
     * 🔗 Sale this item belongs to
     */
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /**
     * 🔗 Item reference
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
