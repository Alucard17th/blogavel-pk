@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Posts')

@section('content')
    <div class="header">
        <div>
            <h1>Posts</h1>
            <p class="hint">Create, edit, and manage your blog posts.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.posts.create') }}">
                <button type="button" class="btn-primary">Create post</button>
            </a>
        </div>
    </div>

    @if ($posts->count() === 0)
        <p>No posts.</p>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($posts as $post)
                    <tr>
                        <td>{{ $post->id }}</td>
                        <td>{{ $post->title }}</td>
                        <td>{{ $post->status }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('blogavel.admin.posts.edit', $post) }}">Edit</a>
                                <form method="POST" action="{{ route('blogavel.admin.posts.destroy', $post) }}" style="display:inline" onsubmit="return confirm('Delete this post? This action cannot be undone.');">
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
            {{ $posts->links() }}
        </div>
    @endif
@endsection
