<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use GuzzleHttp\Client;

class CloudinaryService
{
    private static function client(): Cloudinary
    {
        // Download cacert.pem dari curl.se dan point ke sana,
        // atau disable verify untuk development Windows
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

    public static function upload($file, string $folder = 'general'): string
    {
        self::disableSslVerify();

        $cloudinary = self::client();

        $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder'        => 'aneris/' . $folder,
            'resource_type' => 'auto',
        ]);

        return $result['secure_url'];
    }

    public static function delete(?string $url): void
    {
        if (!$url || !str_contains($url, 'cloudinary.com')) return;

        self::disableSslVerify();

        preg_match('/upload\/(?:v\d+\/)?(.+)\.[a-z]+$/i', $url, $matches);
        if (!empty($matches[1])) {
            $cloudinary = self::client();
            $cloudinary->uploadApi()->destroy($matches[1]);
        }
    }

    private static function disableSslVerify(): void
    {
        // Hanya untuk development Windows — jangan pakai di production
        stream_context_set_default([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);
    }
}
