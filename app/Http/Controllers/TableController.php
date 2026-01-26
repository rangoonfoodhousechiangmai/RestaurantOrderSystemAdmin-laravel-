<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Services\TableQrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TableController extends Controller
{
    public function __construct(protected TableQrService $tableQrService)
    {

    }
    public function index()
    {
        $tables = Table::paginate(10);
        return view('tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number',
        ]);

        try {
            $table = Table::create([
                'table_number' => $request->table_number
            ]);


            session()->flash('success', 'Table created successfully with QR code.');
            return response()->json(['redirectUrl' => route('tables.index')]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create table: ' . $e->getMessage()], 500);
        }
    }

    public function show(Table $table)
    {
        return response()->json($table);
    }

    public function update(Request $request, Table $table)
    {
        $request->validate([
            'table_number' => 'required|string|unique:tables,table_number,' . $table->id,
        ]);

        $table->update($request->only('table_number'));
        session()->flash('success', 'Table updated successfully.');
        return response()->json(['redirectUrl' => route('tables.index')]);
    }

    public function destroy(Table $table)
    {
        if ($table->qr_code_path && Storage::disk('public')->exists($table->qr_code_path)) {
            Storage::disk('public')->delete($table->qr_code_path);
        }

        $table->delete();

        return response()->json(['message' => 'Table deleted successfully']);
    }


    public function reGenerateQr(Table $table)
    {
        $this->tableQrService->regenerateQr($table);

        session()->flash('success', 'QR code regenerated successfully.');
        return response()->json(['message' => 'Table deleted successfully']);
    }
}
