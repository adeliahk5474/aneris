<?php

namespace App\Services;

class CloudinaryService
{
    public static function upload($file, string $folder = 'general'): string
    {
        $realPath = $file->getRealPath();

        if (!$realPath || !file_exists($realPath)) {
            throw new \RuntimeException('File tidak valid atau upload gagal: ' . var_export($realPath, true));
        }

        $result = cloudinary()->uploadFile($realPath, [
            'folder' => 'aneris/' . $folder,
        ]);

        if (empty($result['secure_url'])) {
            throw new \RuntimeException('Upload ke Cloudinary gagal: secure_url tidak ditemukan.');
        }

        return (string) $result['secure_url'];
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
                cloudinary()->destroy($publicId);
            } catch (\Throwable) {
            }
        }
    }

    public static function uploadBase64(string $base64, string $folder = 'general'): string
    {
        $data = base64_decode(preg_replace('/^data:\w+\/\w+;base64,/', '', $base64));

        if ($data === false || $data === '') {
            throw new \RuntimeException('Data base64 tidak valid.');
        }

        // Simpan sementara ke temp file, lalu upload ke Cloudinary
        $tmpPath = tempnam(sys_get_temp_dir(), 'cloudinary_') . '.jpg';
        file_put_contents($tmpPath, $data);

        try {
            $result = cloudinary()->uploadFile($tmpPath, [
                'folder' => 'aneris/' . $folder,
            ]);

            if (empty($result['secure_url'])) {
                throw new \RuntimeException('Upload base64 ke Cloudinary gagal.');
            }

            return (string) $result['secure_url'];
        } finally {
            // Hapus temp file apapun hasilnya
            if (file_exists($tmpPath)) {
                unlink($tmpPath);
            }
        }
    }
}
