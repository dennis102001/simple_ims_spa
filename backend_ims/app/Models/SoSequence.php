<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoSequence extends Model
{
    protected $fillable = ['year', 'last_number'];
    /** @use HasFactory<\Database\Factories\SoSequenceFactory> */
    use HasFactory;
}
