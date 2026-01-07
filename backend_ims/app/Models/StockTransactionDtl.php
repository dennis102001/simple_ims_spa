<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransactionDtl extends Model
{

    protected $fillable = ['item_id', 'item_name', 'price', 'cost', 'quantity', 'hdr_id'];

    /** @use HasFactory<\Database\Factories\StockTransactionDtlFactory> */
    use HasFactory;
}
