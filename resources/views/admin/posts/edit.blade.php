@extends('blogavel::admin.layout')

@section('title', 'Blogavel Admin - Edit Post')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill-table-better@1/dist/quill-table-better.css" rel="stylesheet"/>
    <style>
        .ql-editor .ql-table-better {
            width: unset;
        }
    </style>
@endpush

@section('content')
    <div class="header">
        <div>
            <h1>Edit post</h1>
            <p class="hint">Update content, publishing status, and metadata.</p>
        </div>
        <div class="actions">
            <a href="{{ route('blogavel.admin.posts.index') }}">
                <button type="button">Back</button>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('blogavel.admin.posts.update', $post) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row cols-2">
            <div>
                <label>Title</label>
                <input name="title" value="{{ old('title', $post->title) }}" />
                @error('title')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Slug (optional)</label>
                <input name="slug" value="{{ old('slug', $post->slug) }}" />
                @error('slug')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row cols-2" style="margin-top:12px">
            <div>
                <label>Category</label>
                <select name="category_id">
                    <option value="">(none)</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) old('category_id', $post->category_id) === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Status</label>
                <select name="status">
                    @foreach (['draft','scheduled','published'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $post->status) === $status)>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row cols-2" style="margin-top:12px">
            <div>
                <label>Published at</label>
                @php($publishedAtValue = (string) old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')))
                @php($publishedAtValue = $publishedAtValue !== '' ? str_replace(' ', 'T', substr($publishedAtValue, 0, 16)) : '')
                <input type="datetime-local" name="published_at" value="{{ $publishedAtValue }}" />
                @error('published_at')<div class="error">{{ $message }}</div>@enderror
            </div>

            <div>
                <label>Featured image</label>
                @if ($post->featuredMedia)
                    <div style="margin-bottom:10px">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk($post->featuredMedia->disk)->url($post->featuredMedia->path) }}" alt="" style="max-height:110px" />
                    </div>
                    <label style="margin:0; display:flex; align-items:center; gap:8px; color:var(--text)">
                        <input style="width:auto" type="checkbox" name="remove_featured_image" value="1" />
                        <span>Remove featured image</span>
                    </label>
                @endif
                <div style="margin-top:10px">
                    <input type="file" name="featured_image" />
                    @error('featured_image')<div class="error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Tags</label>
                @php($selectedTags = (array) old('tags', $post->tags->pluck('id')->all()))
                <div class="actions">
                    @foreach ($tags as $tag)
                        <label style="margin:0; display:flex; align-items:center; gap:8px; color:var(--text)">
                            <input style="width:auto" type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array((string) $tag->id, array_map('strval', $selectedTags), true)) />
                            <span>{{ $tag->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('tags')<div class="error">{{ $message }}</div>@enderror
                @error('tags.*')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row" style="margin-top:12px">
            <div>
                <label>Content</label>
                <textarea id="content" name="content" style="display:none">{{ old('content', $post->content) }}</textarea>
                <div id="root" style="margin-bottom:14px; position:relative; height:360px"></div>
                @error('content')<div class="error">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn-primary">Save</button>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-table-better@1/dist/quill-table-better.js"></script>
    <script>
        (function () {
            var inputEl = document.getElementById('content');
            var rootEl = document.getElementById('root');
            if (!rootEl || !inputEl) return;

            Quill.register({
                'modules/table-better': QuillTableBetter
            }, true);

            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                ['link', 'image', 'video', 'formula'],
                [{ 'header': 1 }, { 'header': 2 }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }, { 'list': 'check' }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'direction': 'rtl' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'font': [] }],
                [{ 'align': [] }],
                ['clean'],
                ['table-better']
            ];

            var options = {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions,
                    table: false,
                    'table-better': {
                        toolbarTable: true,
                        menus: ['column', 'row', 'merge', 'table', 'cell', 'wrap', 'copy', 'delete'],
                    },
                    keyboard: {
                        bindings: QuillTableBetter.keyboardBindings
                    }
                }
            };

            var editor = new Quill(rootEl, options);

            function getCsrfToken() {
                var meta = document.querySelector('meta[name="csrf-token"]');
                return meta ? meta.getAttribute('content') : '';
            }

            function uploadImage(file) {
                var formData = new FormData();
                formData.append('file', file);

                return fetch("{{ route('blogavel.admin.media.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': getCsrfToken(),
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                }).then(function (res) {
                    return res.json().then(function (json) {
                        if (!res.ok) {
                            throw json;
                        }
                        return json;
                    });
                });
            }

            function imageHandler() {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.click();

                input.onchange = function () {
                    var file = input.files ? input.files[0] : null;
                    if (!file) return;

                    uploadImage(file)
                        .then(function (json) {
                            var range = editor.getSelection(true);
                            var index = range ? range.index : editor.getLength();
                            editor.insertEmbed(index, 'image', json.url);
                            editor.setSelection(index + 1);
                        })
                        .catch(function () {
                            alert('Image upload failed.');
                        });
                };
            }

            var toolbar = editor.getModule('toolbar');
            if (toolbar && typeof toolbar.addHandler === 'function') {
                toolbar.addHandler('image', imageHandler);
            }

            var html = inputEl.value || '';
            if (html !== '') {
                var delta = editor.clipboard.convert({ html: html });
                var range = editor.getSelection();
                editor.updateContents(delta, Quill.sources.USER);
                editor.setSelection(delta.length() - ((range && range.length) ? range.length : 0), Quill.sources.SILENT);
                editor.scrollSelectionIntoView();
            }

            var form = rootEl.closest('form');
            if (!form) return;

            form.addEventListener('submit', function () {
                inputEl.value = editor.getSemanticHTML();
            });
        })();
    </script>
@endpush
