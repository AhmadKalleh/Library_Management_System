<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;

class AssetsSeeder extends Seeder
{


    public function run(): void
    {
        $this->copyFolder('books');
        $this->copyFolder('users');
        $this->copyFolder('library');
    }

    private function copyFolder(string $folder): void
    {
        $sourcePath = database_path("seeders/assets/{$folder}");
        Storage::disk('public')->makeDirectory($folder);

        if (!File::exists($sourcePath)) {
            return;
        }

        $files = File::files($sourcePath);

        foreach ($files as $file) {

            $destination = "{$folder}/" . $file->getFilename();


            if (!Storage::disk('public')->exists($destination)) {


                Storage::disk('public')->put(
                    $destination,
                    File::get($file)
                );
            }
        }


    }
}
