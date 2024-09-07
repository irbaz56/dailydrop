<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $timestamps = false;

    /**
     * @var string $table
     */
    protected $table = 'users'; 
}
