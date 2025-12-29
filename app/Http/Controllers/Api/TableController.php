<?php

namespace App\Http\Controllers\Api;

use App\Models\Table;
use Illuminate\Support\Str;
use App\Models\TableSession;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TableController extends Controller
{
    public function verify(Request $request)
    {
        // 1. Validate slug and token match
        $table = Table::where('slug', $request->slug)
            ->where('qr_token', $request->token)
            ->first();

        if (!$table) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid table QR code'
            ], 404);
        }

        // 2. Create or get existing session
        $sessionToken = $this->createTableSession($table, $request);

        // 3. Return success with token
        return response()->json([
            'success' => true,
            'message' => 'Table verified successfully',
            'data' => [
                'table' => [
                    'id' => $table->id,
                    'table_number' => $table->table_number,
                    'slug' => $table->slug,
                ],
                'session_token' => $sessionToken,
                'expires_at' => now()->addHours(2)->toIso8601String()
            ]
        ]);
    }

    private function createTableSession($table, $request)
    {
        // Check if session already exists for this device
        $existing = TableSession::where('table_id', $table->id)
            ->where('ip_address', $request->ip())
            ->where('user_agent', $request->header('User-Agent'))
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return $existing->session_token;
        }

        // Create new session
        return TableSession::create([
            'table_id' => $table->id,
            'session_token' => Str::random(32),
            'has_qr_verified' => true,
            'qr_verified_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'expires_at' => now()->addHours(2)
        ])->session_token;
    }
}
