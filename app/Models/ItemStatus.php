<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemStatus extends Model
{
    protected $table = 'mst_item_status';
    protected $primaryKey = 'item_status_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'item_status_id',
        'item_status_name',
        'rules',
        'no_loan',
        'skip_stock_take',
        'input_date',
        'last_update',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'item_status_id', 'item_status_id');
    }
}
