<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    public $timestamps = false;

    /**
     * @var string $table
     */
    protected $table = 'tenant'; 
}
