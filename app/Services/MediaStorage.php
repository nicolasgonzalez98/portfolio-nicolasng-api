<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Guarda/borra imágenes en el disco de medios (config 'filesystems.media').
 * Abstrae el almacenamiento: local en dev, Cloudflare R2 en prod.
 */
class MediaStorage
{
    private function disk(): string
    {
        return config('filesystems.media', 'r2');
    }

    /**
     * Sube un archivo y devuelve su key (object key) y url pública.
     *
     * @return array{key: string, url: string}
     */
    public function store(UploadedFile $file, string $dir): array
    {
        $disk = Storage::disk($this->disk());
        $key = $disk->putFile($dir, $file);

        return [
            'key' => $key,
            'url' => $disk->url($key),
        ];
    }

    /**
     * Borra un objeto por su key (no falla si no existe o si key es null).
     */
    public function delete(?string $key): void
    {
        if (! $key) {
            return;
        }

        Storage::disk($this->disk())->delete($key);
    }
}
