<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'direction',
        'description',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function transactions()
{
    return $this->hasMany(InventoryTransaction::class);
}

}
