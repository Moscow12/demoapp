<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'grandtotal',
        'payment_method',
        'payment_status',
        'status',
        'currency',
        'shipping_amount',
        'shipping_method',
        'notes'
    ];

    public function users(){
        return $this->belongsTo(Users::class);
    }

    public function items(){
        return $this->hasMany(OrderItems::class);
    }

    public function order(){
        return $this->hasOne(Address::class);
    }
}
