<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public $timestamps = false;

    /**
     * @var string $table
     */
    protected $table = 'stocks'; 
}
