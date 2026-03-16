<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemOption extends Model
{
    protected $guarded = ['id'];

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
