<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleHdr extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    /** @use HasFactory<\Database\Factories\SaleHdrFactory> */
    use HasFactory;
}
