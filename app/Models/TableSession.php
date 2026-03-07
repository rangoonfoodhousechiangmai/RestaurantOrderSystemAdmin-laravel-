<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TableSession extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public static function validateTableSession($session_token) {
        $tableSession = TableSession::where('session_token', $session_token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$tableSession) {
            throw new \Exception('Scan the Qr code plz.');
        }

        return $tableSession;
    }
}
