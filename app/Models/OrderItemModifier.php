<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemModifier extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function modifier()
    {
        return $this->belongsTo(Modifier::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
