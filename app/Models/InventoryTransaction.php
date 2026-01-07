<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'entry_type_id',
        'quantity',
        'note',
        'expiry_date',
        'user_id'
    ];

    /**
     * Relationship: A transaction belongs to an Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relationship: A transaction belongs to an Entry Type
     */
    public function entryType()
    {
        return $this->belongsTo(EntryType::class);
    }
}
