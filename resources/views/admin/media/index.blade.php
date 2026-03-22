@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Media')

@section('content')
    <div class="header">
        <div>
            <h1>Media</h1>
            <p class="hint">Upload images for featured media and post content.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.media.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row cols-2">
            <div>
                <label>Upload image</label>
                <input type="file" name="file" />
                @error('file')<div class="error">{{ $message }}</div>@enderror
            </div>
            <div style="display:flex; align-items:flex-end">
                <div class="actions" style="width:100%">
                    <button type="submit" class="btn-primary">Upload</button>
                </div>
            </div>
        </div>
    </form>

    <hr />

    @if ($media->count() === 0)
        <p>No media.</p>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Preview</th>
                    <th>Path</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($media as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk($item->disk)->url($item->path) }}" alt="" style="max-height:60px" />
                        </td>
                        <td>{{ $item->path }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('blogavel.admin.media.destroy', $item) }}" style="display:inline" onsubmit="return confirm('Delete this media item? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pager">
            {{ $media->links() }}
        </div>
    @endif
@endsection
