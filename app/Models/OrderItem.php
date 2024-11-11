<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $fillable = ['order_id', 'name', 'price', 'quantity', 'total_price', 'remark'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
