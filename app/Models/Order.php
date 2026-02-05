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

    public function scopeGetToday()
    {
        return $this->whereDate('created_at', now()->toDateString());
    }

    public function scopeFilter($query, $request)
    {
        if ($request->has('order_code') && !empty($request->order_code)) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        if ($request->has('table_slug') && !empty($request->table_slug)) {
            $query->whereHas('table', function ($q) use ($request) {
                $q->where('slug', $request->table_slug);
            });
        }

        if ($request->has('order_type') && !empty($request->order_type)) {
            $query->where('order_type', $request->order_type);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->whereIn('status', $request->status == 'all' ? ['pending', 'preparing', 'delivered', 'completed', 'cancelled'] : [$request->status]);
        }

        // if ($request->has('date_from') && !empty($request->date_from)) {
        //     $query->whereDate('created_at', '>=', $request->date_from);
        // }

        // if ($request->has('date_to') && !empty($request->date_to)) {
        //     $query->whereDate('created_at', '<=', $request->date_to);
        // }

        return $query;
    }
}
