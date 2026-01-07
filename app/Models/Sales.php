<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no','customer_name','total_amount','amount_paid','balance','payment_status'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
