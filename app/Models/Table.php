<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

class Table extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'table_number',
        'slug',
        'qr_token',
        'qr_code_path',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}
