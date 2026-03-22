@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Tags')

@section('content')
    <div class="header">
        <div>
            <h1>Tags</h1>
            <p class="hint">Label posts with tags for better discovery.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.tags.create') }}">
                <button type="button" class="btn-primary">Create tag</button>
            </a>
        </div>
    </div>

    @if ($tags->count() === 0)
        <p>No tags.</p>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tags as $tag)
                    <tr>
                        <td>{{ $tag->id }}</td>
                        <td>{{ $tag->name }}</td>
                        <td>{{ $tag->slug }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('blogavel.admin.tags.edit', $tag) }}">Edit</a>
                                <form method="POST" action="{{ route('blogavel.admin.tags.destroy', $tag) }}" style="display:inline" onsubmit="return confirm('Delete this tag? This action cannot be undone.');">
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
            {{ $tags->links() }}
        </div>
    @endif
@endsection
