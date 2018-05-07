<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'currency',
        'price',
        'reference_code',
        'payment_gateway',
        'is_success',
        'response_data',
    ];

}
