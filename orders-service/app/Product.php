<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $incrementing = false;
    public $timestamps = false;
    
    protected $fillable = ['id', 'name'];
}