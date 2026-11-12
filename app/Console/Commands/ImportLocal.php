<?php

namespace App\Console\Commands;

use App\Models\Folder;
use App\Models\Media;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\File;
use RuntimeException;

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

            if (!preg_match('#^(image|video)/#', $file->getMimeType())) {
                continue;
            }

            try {
                Media::upload($file, $file->getFilename(), $parentId);
            } catch (RuntimeException $e) {
                $this->warn("Skipped {$entry}: {$e->getMessage()}");

                continue;
            }

            $this->line("Imported {$entry}");
        }
    }
}
