<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'eng_name',
        'mm_name',
        'price',
        'eng_description',
        'mm_description',
        'image_path',
        'is_available',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function scopeFilter(Builder $query)
    {
        if (request('searchName')) {
            $query->where('eng_name', 'like', '%' . request('searchName') . '%')
                ->orwhere('mm_name', 'like', '%' . request('searchName') . '%');
        }
    }

    public function modifiers()
    {
        return $this->belongsToMany(Modifier::class, 'menu_modifiers')
            ->withPivot('price'); // << include the pivot column here
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
