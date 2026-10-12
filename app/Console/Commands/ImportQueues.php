<?php

namespace App\Console\Commands;

use App\Models\Folder;
use App\Models\Image;
use App\Models\Slideshow;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

#[Signature('app:import-queues')]
#[Description('Import old queue files as slideshows')]
class ImportQueues extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $text = textarea(label: 'Paste the queues', required: true, rows: 20);

        DB::beginTransaction();

        foreach (array_filter(explode('==> ', $text), 'trim') as $queue) {
            [$header, $json] = explode("\n", trim($queue), 2);
            $name = Str::before($header, '.queue.json');
            $items = [];

            foreach (json_decode($json, true) as $entry) {
                $duration = min(max(1, (int) round($entry['config']['timeout'] / 1000)), 2147483647);

                if ($entry['type'] === 'clock') {
                    $items[] = ['type' => 'clock', 'duration' => $duration, 'data' => ['image' => null]];

                    continue;
                }

                if ($entry['type'] !== 'picture') {
                    $this->warn("Unhandled type {$entry['type']} in {$name}: " . json_encode($entry));

                    if (!confirm("Skip slideshow {$name} and continue?")) {
                        DB::rollBack();
                        $this->error("Unhandled type {$entry['type']}, nothing was imported.");

                        return self::FAILURE;
                    }

                    continue 2;
                }

                $url = collect($entry['config']['data'])->firstWhere('name', 'url')['value'];
                $image = $this->findImage($url);

                if (!$image) {
                    $this->warn("Image not found in {$name}: {$url}");
                }

                while (!$image) {
                    $choice = select(
                        label: 'Skip this item and continue?',
                        options: ['yes' => 'Yes', 'no' => 'No', 'manual' => 'Manual path'],
                    );

                    if ($choice === 'yes') {
                        continue 2;
                    }

                    if ($choice === 'no') {
                        DB::rollBack();
                        $this->error("Image {$url} not found, nothing was imported.");

                        return self::FAILURE;
                    }

                    $path = text(label: 'Image path', placeholder: '/Folder/Subfolder/image.jpg', required: true, hint: 'Folder path as shown in the UI followed by the image name');
                    $image = $this->findImage($path);

                    if (!$image) {
                        $this->warn("Image not found: {$path}");
                    }
                }

                $items[] = ['type' => 'image', 'duration' => $duration, 'data' => ['image' => $image->id]];
            }

            Slideshow::create(['name' => $name])->items()->createMany($items);

            $this->line("Imported {$name}");
        }

        DB::commit();
    }

    private function findImage(string $url): ?Image
    {
        $parts = explode('/', trim(Str::after($url, 'album/'), '/'));
        $name = array_pop($parts);
        $folderId = null;

        foreach ($parts as $part) {
            $folderId = Folder::where('name', $part)->where('parent_id', $folderId)->value('id');

            if (!$folderId) {
                return null;
            }
        }

        return Image::where('folder_id', $folderId)->where('name', $name)->first();
    }
}
