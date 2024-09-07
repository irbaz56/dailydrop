<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    public $timestamps = false;

    /**
     * @var string $table
     */
    protected $table = 'addons'; 
}
