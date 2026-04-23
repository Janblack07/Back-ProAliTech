<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private readonly Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    public function uploadImage(UploadedFile $file, string $folder = 'alimenticios'): array
    {
        $result = $this->cloudinary->uploadApi()->upload(
            $file->getRealPath(),
            [
                'folder' => $folder,
                'resource_type' => 'image',
            ]
        );

        return [
            'public_id'  => $result['public_id'] ?? null,
            'secure_url' => $result['secure_url'] ?? null,
            'url'        => $result['secure_url'] ?? null,
        ];
    }

    public function deleteImage(?string $publicId): bool
    {
        if (blank($publicId)) {
            return false;
        }

        $this->cloudinary->uploadApi()->destroy($publicId);

        return true;
    }
}