<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'item_id',
        'quantity',
        'purchase_price',
        'supplier',
        'purchase_date',
        'note'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
{
    return $this->belongsTo(Supplier::class);
}

}
