<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
 
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory;
    use SoftDeletes;
}
