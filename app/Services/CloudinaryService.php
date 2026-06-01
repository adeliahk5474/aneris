<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

class CloudinaryService
{
    private static function client(): Cloudinary
    {
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);

        return new Cloudinary();
    }

    /**
     * Upload file ke Cloudinary.
     * $file bisa berupa UploadedFile atau path string.
     */
    public static function upload($file, string $folder = 'general'): string
    {
        $cloudinary = self::client();

        $path = is_string($file) ? $file : $file->getRealPath();

        $result = $cloudinary->uploadApi()->upload($path, [
            'folder'        => 'aneris/' . $folder,
            'resource_type' => 'auto',
        ]);

        return $result['secure_url'];
    }

    /**
     * Hapus file dari Cloudinary berdasarkan URL atau public_id.
     */
    public static function delete(?string $urlOrPublicId): void
    {
        if (!$urlOrPublicId) return;

        $cloudinary = self::client();

        // Kalau berupa URL Cloudinary, ekstrak public_id-nya
        if (str_contains($urlOrPublicId, 'cloudinary.com')) {
            preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $urlOrPublicId, $matches);
            $publicId = $matches[1] ?? null;
        } else {
            $publicId = $urlOrPublicId;
        }

        if ($publicId) {
            try {
                $cloudinary->uploadApi()->destroy($publicId);
            } catch (\Throwable) {
                // Gagal hapus — lanjut saja
            }
        }
    }

    /**
     * Upload dari base64 string.
     */
    public static function uploadBase64(string $base64, string $folder = 'general'): string
    {
        $cloudinary = self::client();

        $result = $cloudinary->uploadApi()->upload($base64, [
            'folder'        => 'aneris/' . $folder,
            'resource_type' => 'auto',
        ]);

        return $result['secure_url'];
    }
}
