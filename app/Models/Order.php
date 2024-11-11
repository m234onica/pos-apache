<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = ['order_no', 'price', 'status'];

    public const STATUS_PROCESSING = 'PROCESSING';
    public const STATUS_UNPAID = 'UNPAID';
    public const STATUS_COMPLETED = 'COMPLETED';
    public const STATUS_CANCELLED = 'CANCELLED';

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
