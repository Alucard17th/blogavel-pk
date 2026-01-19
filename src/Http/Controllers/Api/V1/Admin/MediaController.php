<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Api\V1\Admin;

use Blogavel\Blogavel\Http\Resources\MediaResource;
use Blogavel\Blogavel\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

final class MediaController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'image', 'max:5120'],
        ]);

        /** @var \Illuminate\Http\UploadedFile $file */
        $file = $data['file'];

        $disk = (string) config('blogavel.media_disk', 'public');
        $directory = (string) config('blogavel.media_directory', 'blogavel');

        $path = $file->store($directory, $disk);

        $media = Media::create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => (int) $file->getSize(),
        ]);

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    public function destroy(Media $medium)
    {
        Storage::disk($medium->disk)->delete($medium->path);
        $medium->delete();

        return response()->json(['deleted' => true]);
    }
}
