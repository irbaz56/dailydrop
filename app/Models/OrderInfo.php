<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderInfo extends Model
{
    public $timestamps = false;

    /**
     * @var string $table
     */
    protected $table = 'orderinfo'; 
}
