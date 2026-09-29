<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CloudinaryStorage
{
    public function enabled(): bool
    {
        return $this->cloudName() !== '' && $this->apiKey() !== '' && $this->apiSecret() !== '';
    }

    public function storeUploadedFile(UploadedFile $file, string $folder): string
    {
        if (!$this->enabled()) {
            return $file->store($folder, 'public');
        }

        $contents = file_get_contents($file->getRealPath());
        if ($contents === false) {
            throw new RuntimeException('Could not read the uploaded file.');
        }

        return $this->upload($contents, $file->getClientOriginalName() ?: 'upload', $folder);
    }

    public function storeBinary(string $contents, string $filename, string $folder): string
    {
        if (!$this->enabled()) {
            $path = trim($folder, '/').'/'.$filename;
            Storage::disk('public')->put($path, $contents);

            return $path;
        }

        return $this->upload($contents, $filename, $folder);
    }

    private function upload(string $contents, string $filename, string $folder): string
    {
        $cloudFolder = 'doonspedo/'.trim($folder, '/');
        $timestamp = time();
        $signature = sha1('folder='.$cloudFolder.'&timestamp='.$timestamp.$this->apiSecret());

        $response = Http::timeout(30)
            ->attach('file', $contents, $filename)
            ->post($this->endpoint(), [
                'api_key' => $this->apiKey(),
                'timestamp' => $timestamp,
                'signature' => $signature,
                'folder' => $cloudFolder,
            ]);

        $url = $response->json('secure_url');
        if (!$response->successful() || !is_string($url) || !str_starts_with($url, 'https://')) {
            throw new RuntimeException('The file could not be stored. Please try again.');
        }

        return $url;
    }

    private function endpoint(): string
    {
        return 'https://api.cloudinary.com/v1_1/'.$this->cloudName().'/auto/upload';
    }

    private function cloudName(): string
    {
        return trim((string) config('services.cloudinary.cloud_name'));
    }

    private function apiKey(): string
    {
        return trim((string) config('services.cloudinary.api_key'));
    }

    private function apiSecret(): string
    {
        return (string) config('services.cloudinary.api_secret');
    }
}
