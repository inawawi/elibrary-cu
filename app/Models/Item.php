<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = 'item';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    protected $fillable = [
        'biblio_id',
        'call_number',
        'coll_type_id',
        'item_code',
        'inventory_code',
        'received_date',
        'supplier_id',
        'order_no',
        'location_id',
        'order_date',
        'item_status_id',
        'site',
        'source',
        'invoice',
        'price',
        'price_currency',
        'invoice_date',
        'input_date',
        'last_update',
        'uid',
    ];

    public function biblio()
    {
        return $this->belongsTo(Biblio::class, 'biblio_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'location_id');
    }

    public function collType()
    {
        return $this->belongsTo(CollType::class, 'coll_type_id');
    }

    public function itemStatus()
    {
        return $this->belongsTo(ItemStatus::class, 'item_status_id', 'item_status_id');
    }

    public function activeLoan()
    {
        return $this->hasOne(Loan::class, 'item_code', 'item_code')->where('is_return', 0);
    }

    public function isAvailable(): bool
    {
        return !$this->activeLoan()->exists();
    }
}
