<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'parent_id',
        'status'
    ];


    protected $casts = [
    'status' => 'boolean',
];
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->where('status', true)->where('parent_id', null);
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
