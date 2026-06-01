<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryService
{
    public static function upload($file, string $folder = 'general'): string
    {
        $result = Cloudinary::upload(
            is_string($file) ? $file : $file->getRealPath(),
            [
                'folder'        => 'aneris/' . $folder,
                'resource_type' => 'auto',
            ]
        );

        return $result->getSecurePath();
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
                Cloudinary::destroy($publicId);
            } catch (\Throwable) {}
        }
    }

    public static function uploadBase64(string $base64, string $folder = 'general'): string
    {
        $result = Cloudinary::upload($base64, [
            'folder'        => 'aneris/' . $folder,
            'resource_type' => 'auto',
        ]);

        return $result->getSecurePath();
    }
}
