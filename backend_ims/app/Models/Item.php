<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    protected $fillable = ['item_name', 'category_id', 'price', 'cost', 'quantity', 'category_id', 'unit_id', 'reorder_level', 'description', 'sku'];
    
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use SoftDeletes;
    use HasFactory;
}
