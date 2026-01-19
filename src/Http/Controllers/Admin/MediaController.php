<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Http\Controllers\Admin;

use Blogavel\Blogavel\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

final class MediaController extends Controller
{
    public function index()
    {
        $media = Media::query()->orderByDesc('id')->paginate(30);

        return view('blogavel::admin.media.index', [
            'media' => $media,
        ]);
    }

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

        Media::create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => (int) $file->getSize(),
        ]);

        return redirect()->route('blogavel.admin.media.index');
    }

    public function destroy(Media $medium)
    {
        Storage::disk($medium->disk)->delete($medium->path);
        $medium->delete();

        return redirect()->route('blogavel.admin.media.index');
    }
}
