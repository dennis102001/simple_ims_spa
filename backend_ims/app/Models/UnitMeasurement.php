<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitMeasurement extends Model
{
    protected $fillable = ['unit_name', 'description'];
    /** @use HasFactory<\Database\Factories\UnitMeasurementFactory> */
    use HasFactory;
    use SoftDeletes;
}
