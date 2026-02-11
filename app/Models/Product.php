<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Product extends Model
{
    use Cachable;

    protected $fillable = [
        'name',
        'price',
        'status'
    ];
    
}
