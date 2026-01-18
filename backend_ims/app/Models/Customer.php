<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;
    use SoftDeletes;
}
