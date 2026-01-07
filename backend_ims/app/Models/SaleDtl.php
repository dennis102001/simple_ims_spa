<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDtl extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    /** @use HasFactory<\Database\Factories\SaleDtlFactory> */
    use HasFactory;
}
