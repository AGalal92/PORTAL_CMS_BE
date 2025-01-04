<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryDetail extends Model
{
    protected $fillable = ['category_id', 'name', 'description'];

    // Relationship back to the category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
