<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryService
{
    /**
     * Upload file ke Cloudinary dan return URL.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $folder  subfolder di Cloudinary (misal: 'avatars', 'artworks')
     * @return string  URL publik gambar
     */
    public static function upload($file, string $folder = 'general'): string
    {
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder'          => 'aneris/' . $folder,
            'resource_type'   => 'auto',
            'transformation'  => [
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ],
        ]);

        return $result->getSecurePath();
    }

    /**
     * Hapus file dari Cloudinary berdasarkan URL.
     *
     * @param  string|null  $url  URL Cloudinary
     * @return void
     */
    public static function delete(?string $url): void
    {
        if (!$url || !str_contains($url, 'cloudinary.com')) return;

        // Extract public_id dari URL
        // Format: https://res.cloudinary.com/{cloud}/image/upload/v{version}/{folder}/{filename}
        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches);
        if (!empty($matches[1])) {
            Cloudinary::destroy($matches[1]);
        }
    }
}
