@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Categories')

@section('content')
    <div class="header">
        <div>
            <h1>Categories</h1>
            <p class="hint">Organize your posts with categories.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.categories.create') }}">
                <button type="button" class="btn-primary">Create category</button>
            </a>
        </div>
    </div>

    @if ($categories->count() === 0)
        <p>No categories.</p>
    @else
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Parent</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->slug }}</td>
                        <td>{{ optional($category->parent)->name }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('blogavel.admin.categories.edit', $category) }}">Edit</a>
                                <form method="POST" action="{{ route('blogavel.admin.categories.destroy', $category) }}" style="display:inline" onsubmit="return confirm('Delete this category? This action cannot be undone.');">
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
            {{ $categories->links() }}
        </div>
    @endif
@endsection
