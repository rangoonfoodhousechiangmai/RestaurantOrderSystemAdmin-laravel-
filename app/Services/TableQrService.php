<?php

namespace App\Services;

use App\Models\Table;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use Illuminate\Support\Facades\Storage;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class TableQrService
{
    // QR code related functionalities can be added here in the future

    /**
     * Generate the Table URL used for QR
     */
    public function generateTableUrl(Table $table): string
    {
        $frontend = config('fronted.url');
        return "{$frontend}/table/{$table->slug}/{$table->qr_token}";
    }

    /**
     * Generate QR and save file to storage
     */
    public function generateQrCode(Table $table): void
    {
        $url = $this->generateTableUrl($table);

        // Use SVG backend (no imagick required)
        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        // SVG content
        $qrSvg = $writer->writeString($url);

        $directory = 'qr-codes/tables';
        $filename = "table-{$table->table_number}-{$table->qr_token}.svg";
        $path = "{$directory}/{$filename}";

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Save new QR
        Storage::disk('public')->put($path, $qrSvg);

        // Update model quietly
        $table->qr_code_path = $path;
        $table->saveQuietly();
    }

    /**
     * Admin manually regenerates QR
     */
    public function regenerateQr($table): void
    {
        // Delete old QR if exists
        $this->deleteQRCode($table);
        $table->qr_token = Str::random(15);
        $table->saveQuietly(); // Save without triggering events
        // Generate new QR code
        $this->generateQrCode($table);
    }

    /**
     * Get public URL for QR code
     */
    public function getQrCodeUrl(Table $table): ?string
    {
        return $table->qr_code_path ? Storage::url($table->qr_code_path) : null;
    }

    public function deleteQRCode($table)
    {
        if ($table->qr_code_path && Storage::disk('public')->exists($table->qr_code_path)) {
            Storage::disk('public')->delete($table->qr_code_path);
        }

    }
}
