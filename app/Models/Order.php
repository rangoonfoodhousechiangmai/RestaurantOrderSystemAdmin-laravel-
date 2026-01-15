<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'order_token',
        'table_id',
        'table_session_id',
        'order_type',
        'total_price',
        'total_qty',
        'status',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
