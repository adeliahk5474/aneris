<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class CloudinaryService
{
    public static function upload($file, string $folder = 'general'): string
    {
        $path = 'aneris/' . $folder . '/' . uniqid() . '.' . $file->getClientOriginalExtension();

        Storage::disk('cloudinary')->put($path, file_get_contents($file->getRealPath()));

        return Storage::disk('cloudinary')->url($path);
    }

    public static function delete(?string $urlOrPublicId): void
    {
        if (!$urlOrPublicId) return;

        if (str_contains($urlOrPublicId, 'cloudinary.com')) {
            preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $urlOrPublicId, $matches);
            $publicId = $matches[1] ?? null;
        } else {
            $publicId = $urlOrPublicId;
        }

        if ($publicId) {
            try {
                Storage::disk('cloudinary')->delete($publicId);
            } catch (\Throwable) {
            }
        }
    }

    public static function uploadBase64(string $base64, string $folder = 'general'): string
    {
        $data = base64_decode(preg_replace('/^data:\w+\/\w+;base64,/', '', $base64));
        $path = 'aneris/' . $folder . '/' . uniqid() . '.jpg';

        Storage::disk('cloudinary')->put($path, $data);

        return Storage::disk('cloudinary')->url($path);
    }
}
