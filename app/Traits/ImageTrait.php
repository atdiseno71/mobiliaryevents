<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

trait ImageTrait
{
    public static function uploadWebp($file, $folder = 'productos')
    {
        if (!$file) {
            return null;
        }

        // Manager para v3
        $manager = new ImageManager(new Driver());

        // Nombre final
        $filename = Str::random(40) . '.webp';

        // Asegurar que la carpeta exista en storage/app/public/
        $directory = "public/{$folder}";
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0777, true);
        }

        // Ruta completa del archivo
        $path = storage_path("app/{$directory}/{$filename}");

        // Procesar y guardar la imagen como .webp
        $manager->read($file)
            ->encode(new WebpEncoder(quality: 80))
            ->save($path);

        return $filename;
    }
}
