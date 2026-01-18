<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    protected $fillable = ['category_name', 'description'];
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    use SoftDeletes;
}
