<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModifierStoreRequest;
use App\Http\Requests\ModifierUpdateRequest;
use App\Models\Modifier;
use Illuminate\Http\Request;

class ModifierController extends Controller
{
    public function index()
    {
        $modifiers = Modifier::filter()->latest()->paginate(10);
        return view('modifiers.index', compact('modifiers'));
    }

    public function show(Modifier $modifier)
    {
        return response()->json($modifier);
    }

    public function store(ModifierStoreRequest $request)
    {
        Modifier::create([
            'eng_name' => $request->eng_name,
            'mm_name' => $request->mm_name,
            'type' => $request->type,
            'price' => $request->price,
            'selection_type' => $request->selection_type,
        ]);

        session()->flash('success', 'Modifier created successfully.');

        return response()->json(['redirectUrl' => route('modifiers.index')]);
    }

    public function update(ModifierUpdateRequest $request, Modifier $modifier)
    {
        $modifier->update([
            'eng_name' => $request->edit_eng_name,
            'mm_name' => $request->edit_mm_name,
            'type' => $request->edit_type,
            'price' => $request->edit_price,
            'selection_type' => $request->edit_selection_type,
        ]);

        session()->flash('success', 'Modifier updated successfully.');

        return response()->json(['redirectUrl' => route('modifiers.index')]);
    }

    public function destroy(Modifier $modifier)
    {
        $modifier->delete();

        session()->flash('success', 'Modifier deleted successfully.');

        return redirect()->route('modifiers.index');
    }
}
