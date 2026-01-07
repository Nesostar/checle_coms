<?php
class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id', 'item_name', 'quantity', 'unit_price', 'total'
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
