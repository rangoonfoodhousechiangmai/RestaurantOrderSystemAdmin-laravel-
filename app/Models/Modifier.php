<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modifier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'eng_name',
        'mm_name',
        'type',
        'price',
        'selection_type',
    ];

    public function scopeFilter($query)
    {
        if (request('searchName')) {
            $query->where('eng_name', 'like', '%' . request('searchName') . '%');
        }

        return $query;
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_modifiers', 'modifier_id', 'menu_id')
            ->withPivot('price');;
    }
}
