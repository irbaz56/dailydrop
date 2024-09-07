<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    public $timestamps = false;

    /**
     * @var string $table
     */
    protected $table = 'orderdetails'; 
}
