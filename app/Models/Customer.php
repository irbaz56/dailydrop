<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = false;
    /**
     * @var string $table
     */
    protected $table = 'customers'; 
}
