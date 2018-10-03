<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    function scopeAvailable($query)
    {
        return $query->whereNUll('order_id');
    }
}
