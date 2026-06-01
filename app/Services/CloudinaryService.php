<?php

namespace App\Services;

class CloudinaryService
{
    /**
     * Upload file, return secure URL.
     * Pakai helper cloudinary() dari package cloudinary-laravel
     * — sama persis dengan yang dipakai di artwork upload (sudah terbukti jalan).
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $folder  Sub-folder di bawah 'aneris/'
     * @return string  Secure URL
     */
    public static function upload($file, string $folder = 'general'): string
    {
        $result = cloudinary()->upload(
            $file->getRealPath(),
            [
                'folder'        => 'aneris/' . $folder,
                'resource_type' => 'auto',
                'transformation' => [
                    'quality'      => 'auto',
                    'fetch_format' => 'auto',
                ],
            ]
        );

        return $result->getSecurePath();
    }

    /**
     * Upload file, return [url, public_id].
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  string  $folder
     * @return array{url: string, public_id: string}
     */
    public static function uploadWithId($file, string $folder = 'general'): array
    {
        $result = cloudinary()->upload(
            $file->getRealPath(),
            [
                'folder'        => 'aneris/' . $folder,
                'resource_type' => 'auto',
                'transformation' => [
                    'quality'      => 'auto',
                    'fetch_format' => 'auto',
                ],
            ]
        );

        return [
            'url'       => $result->getSecurePath(),
            'public_id' => $result->getPublicId(),
        ];
    }

    /**
     * Upload dari path file langsung (bukan UploadedFile).
     *
     * @param  string  $filePath  Path absolut ke file
     * @param  string  $folder
     * @return array{url: string, public_id: string}
     */
    public static function uploadFromPath(string $filePath, string $folder = 'general'): array
    {
        $result = cloudinary()->upload(
            $filePath,
            [
                'folder'        => 'aneris/' . $folder,
                'resource_type' => 'auto',
                'transformation' => [
                    'quality'      => 'auto',
                    'fetch_format' => 'auto',
                ],
            ]
        );

        return [
            'url'       => $result->getSecurePath(),
            'public_id' => $result->getPublicId(),
        ];
    }

    /**
     * Hapus file dari Cloudinary berdasarkan public_id.
     *
     * @param  string  $publicId
     */
    public static function destroy(string $publicId): void
    {
        if (!$publicId) return;

        try {
            cloudinary()->destroy($publicId);
        } catch (\Throwable $e) {
            \Log::warning('Cloudinary destroy failed: ' . $e->getMessage(), [
                'public_id' => $publicId,
            ]);
        }
    }
}
