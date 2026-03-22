@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Comments')

@section('content')
    <div class="header">
        <div>
            <h1>Comments</h1>
            <p class="hint">Moderate pending comments and keep discussions clean.</p>
        </div>
    </div>

    @if ($comments->count() === 0)
        <p>No comments.</p>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Post</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Content</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($comments as $comment)
                    <tr>
                        <td>{{ $comment->id }}</td>
                        <td>{{ $comment->post_id }}</td>
                        <td>{{ $comment->authorName() }}</td>
                        <td>{{ $comment->status }}</td>
                        <td>{{ $comment->content }}</td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="{{ route('blogavel.admin.comments.approve', $comment) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn-primary">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('blogavel.admin.comments.spam', $comment) }}" style="display:inline">
                                    @csrf
                                    <button type="submit">Spam</button>
                                </form>
                                <form method="POST" action="{{ route('blogavel.admin.comments.destroy', $comment) }}" style="display:inline" onsubmit="return confirm('Delete this comment? This action cannot be undone.');">
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
            {{ $comments->links() }}
        </div>
    @endif
@endsection
