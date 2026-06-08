<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageService
{
    public static function upload(UploadedFile $file, string $folder = 'general'): string
    {
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $folder . '/' . $fileName;

        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return asset('storage/' . $path);
    }

    public static function delete(?string $url): void
    {
        if (!$url) return;

        $base = asset('storage/') . '/';
        $path = str_replace($base, '', $url);

        if ($path && $path !== $url) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function uploadBase64(string $base64, string $folder = 'general'): string
    {
        $data = base64_decode(preg_replace('/^data:\w+\/\w+;base64,/', '', $base64));

        if ($data === false || $data === '') {
            throw new \RuntimeException('Data base64 tidak valid.');
        }

        $fileName = Str::uuid() . '.jpg';
        $path = $folder . '/' . $fileName;

        Storage::disk('public')->put($path, $data);

        return asset('storage/' . $path);
    }
}
