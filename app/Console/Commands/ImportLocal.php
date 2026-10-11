<?php

namespace App\Console\Commands;

use App\Models\Folder;
use App\Models\Image;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

#[Signature('app:import-local {path}')]
#[Description('Import local folder into the database folder structure')]
class ImportLocal extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path');

        $this->import(str_starts_with($path, '/') ? $path : base_path($path));
    }

    private function import(string $directory, ?int $parentId = null): void
    {
        foreach (glob("{$directory}/*") as $entry) {
            if (is_dir($entry)) {
                $folder = Folder::firstOrCreate(['name' => basename($entry), 'parent_id' => $parentId]);

                $this->import($entry, $folder->id);

                continue;
            }

            $file = new File($entry);

            if (!str_starts_with($file->getMimeType(), 'image/')) {
                continue;
            }

            $image = new Image();
            $image->id = $image->newUniqueId();
            $image->name = Str::limit($file->getFilename(), 255, '');
            $image->folder_id = $parentId;
            $image->path = Storage::putFileAs('images', $file, "{$image->id}.{$file->extension()}");
            $image->save();

            $this->line("Imported {$entry}");
        }
    }
}
