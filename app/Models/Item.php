<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\InventoryTransaction;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'subcategory_id',
        'retail_price',
        'whole_price',
        'quantity',      // keep for legacy / display only
        'expiry_date',
        'created_by'
    ];

    // Relationships
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * ✅ SINGLE SOURCE OF TRUTH FOR STOCK
     */
    public function getCurrentStock()
{
    return $this->transactions()
        ->with('entryType')
        ->orderBy('id')
        ->get()
        ->reduce(function ($stock, $trx) {
            return match ($trx->entryType->direction ?? 'in') {
                'in'            => $stock + $trx->quantity,
                'out', 'damage' => $stock - $trx->quantity,
                'adjustment'    => $trx->quantity,
                default         => $stock,
            };
        }, 0);
}
}
