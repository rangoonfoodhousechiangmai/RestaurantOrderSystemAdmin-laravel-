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
        'table_name',
        'table_session_token',
        'order_type',
        'total_price',
        'total_qty',
        'status',
        'payment_type',
        'payment_status',
        'payment_image_path',
        'payment_verified_at',
        'payment_verified_by'
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }


    public function isPaid(): bool
    {
        return (bool) $this->payment_status;
    }


    public function isPaymentVerified(): bool
    {
        return !is_null($this->payment_verified_at);
    }

}
