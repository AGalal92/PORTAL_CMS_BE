<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'parent_id'];

    // Relationship to fetch children (subcategories)
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relationship to fetch parent
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Recursive relationship to fetch all descendants
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    // Relationship to fetch category details
    public function details()
    {
        return $this->hasOne(CategoryDetail::class, 'category_id');
    }
}
