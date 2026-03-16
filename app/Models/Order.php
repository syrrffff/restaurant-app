<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'table_id', 'invoice_number', 'customer_name','subtotal', 'tax_amount', 'total_amount',
        'kitchen_status', 'payment_status', 'payment_method',
        'order_type', 'cashier_id' // <--- Tambahkan 2 ini
    ];

    // Tambahkan relasi Kasir
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
