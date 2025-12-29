<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->latest()->get();
        return response()->json([
            'success' => true,
            'data'    => $menus
        ]);
    }

    public function show(Menu $menu)
    {
        $menu->load('category');
        return response()->json([
            'success' => true,
            'data'    => $menu
        ]);
    }
}
