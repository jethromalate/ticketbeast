<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    //protected from MassAssignment
    protected $guarded = [];

    protected $dates = ['date'];
}
