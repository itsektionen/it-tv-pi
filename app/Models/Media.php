<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Exceptions\ImageException;
use Intervention\Image\Format;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;
use RuntimeException;

#[Fillable("name", "type", "path", "preview_path", "duration", "folder_id")]
class Media extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'duration' => 'float',
        ];
    }

    public static function upload(File|UploadedFile $file, string $name, ?int $folderId = null): self
    {
        $media = new self();
        $media->id = $media->newUniqueId();
        $media->name = Str::limit($name, 255, '');
        $media->type = str_starts_with($file->getMimeType(), 'video/') ? 'video' : 'image';
        $media->folder_id = $folderId;
        $media->path = Storage::putFileAs('media', $file, "{$media->id}.{$file->extension()}");
        $media->preview_path = "previews/{$media->id}.webp";

        try {
            $media->type === 'video' ? $media->createVideoPreview() : $media->createImagePreview();
        } catch (ImageException | RuntimeException $e) {
            if ($media->type === 'video') {
                Storage::delete($media->path);

                throw new RuntimeException('Could not read the video.', previous: $e);
            }

            Log::warning("Could not create preview for {$media->path}: {$e->getMessage()}");
            $media->preview_path = $media->path;
        }

        $media->save();

        return $media;
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    private function createImagePreview(): void
    {
        $path = Storage::path($this->path);
        [$width, $height] = getimagesize($path) ?: [0, 0];

        if ($width * $height > 25_000_000) {
            throw new RuntimeException("Image is too large ({$width}x{$height})");
        }

        $this->storePreview(Image::decodePath($path));
    }

    private function createVideoPreview(): void
    {
        $path = Storage::path($this->path);
        $frame = Process::run(['ffmpeg', '-v', 'error', '-i', $path, '-frames:v', '1', '-f', 'image2pipe', '-c:v', 'png', '-']);

        if ($frame->failed() || $frame->output() === '') {
            throw new RuntimeException(trim($frame->errorOutput()) ?: 'No video frame found');
        }

        $duration = trim(Process::run(['ffprobe', '-v', 'error', '-show_entries', 'format=duration', '-of', 'csv=p=0', $path])->output());

        $this->duration = is_numeric($duration) && $duration > 0 ? (float) $duration : null;
        $this->storePreview(Image::decodeBinary($frame->output()));
    }

    private function storePreview(ImageInterface $image): void
    {
        Storage::put($this->preview_path, $image->scaleDown(width: 400)->encodeUsingFormat(Format::WEBP)->toString());
    }
}
