<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

trait HandlesUploadedImages
{
    protected function storeUploadedImage(UploadedFile $file, string $prefix): string
    {
        $directory = database_path('images');

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
        $filename = sprintf('%s-%s.%s', $prefix, Str::uuid(), $extension);

        $file->move($directory, $filename);

        return $filename;
    }

    protected function deleteManagedImage(?string $filename, string $prefix): void
    {
        $normalized = basename((string) $filename);

        if ($normalized === '' || !str_starts_with($normalized, "{$prefix}-")) {
            return;
        }

        $path = database_path('images/' . $normalized);

        if (File::exists($path)) {
            File::delete($path);
        }
    }

    protected function imageFileResponse(string $filename)
    {
        $safeFilename = basename($filename);
        $path = database_path('images/' . $safeFilename);

        if (!is_file($path)) {
            abort(404, 'A kép nem található.');
        }

        return response()->file($path);
    }
}
