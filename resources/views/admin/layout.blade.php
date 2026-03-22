<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Blogavel Admin')</title>
    @stack('styles')
    <style>
        :root {
            --bg: #f4f6fb;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #5b6475;
            --border: rgba(15,23,42,.12);
            --accent: #4f7cff;
            --danger: #e11d48;
            --shadow: 0 12px 35px rgba(15,23,42,.10);
            --radius: 14px;
        }

        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body {
            margin: 0;
            min-height: 100%;
            display: flex;
            flex-direction: column;
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            color: var(--text);
            background: var(--bg);
        }

        a { color: inherit; text-decoration: none; }
        a:hover { text-decoration: underline; text-underline-offset: 3px; }

        .wrap { flex: 1; max-width: 1100px; margin: 0 auto; padding: 26px 16px 36px; width: 100%; }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(10px);
            background: rgba(244,246,251,.82);
            border-bottom: 1px solid var(--border);
        }

        .topbar-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 14px 16px;
            display: flex;
            gap: 14px;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            letter-spacing: .2px;
        }

        .brand-badge {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: var(--accent);
            box-shadow: 0 0 0 6px rgba(79,124,255,.18);
        }

        .nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .nav a {
            font-size: 14px;
            color: var(--muted);
            padding: 8px 10px;
            border-radius: 10px;
        }

        .nav a:hover {
            color: var(--text);
            background: rgba(15,23,42,.05);
            text-decoration: none;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 18px;
            overflow: hidden;
        }

        .header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        h1 {
            font-size: 20px;
            margin: 0;
            letter-spacing: .2px;
        }

        p { margin: 10px 0; color: var(--muted); }

        table.admin-table { width: 100%; border-collapse: collapse; overflow: hidden; border-radius: 12px; background: rgba(15,23,42,.02); }
        thead th {
            text-align: left;
            font-size: 12px;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            background: rgba(15,23,42,.04);
            padding: 12px 12px;
            border-bottom: 1px solid var(--border);
        }
        tbody td {
            padding: 12px;
            border-bottom: 1px solid rgba(15,23,42,.06);
            vertical-align: top;
        }
        tbody tr:hover td { background: rgba(15,23,42,.03); }

        form { margin: 0; }
        label { display: block; font-size: 13px; color: var(--muted); margin-bottom: 6px; }
        input, select, textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 12px;
            border: 1px solid rgba(15,23,42,.14);
            background: rgba(255,255,255,.95);
            color: var(--text);
            outline: none;
        }
        textarea { resize: vertical; }
        input:focus, select:focus, textarea:focus { border-color: rgba(79,124,255,.55); box-shadow: 0 0 0 4px rgba(79,124,255,.14); }

        .row { display: grid; grid-template-columns: 1fr; gap: 12px; }
        .actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        button {
            appearance: none;
            border: 1px solid rgba(15,23,42,.14);
            background: rgba(255,255,255,.90);
            color: var(--text);
            padding: 9px 12px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
        }
        button:hover { background: rgba(15,23,42,.03); }

        .btn-primary {
            border-color: rgba(79,124,255,.40);
            background: rgba(79,124,255,.14);
        }
        .btn-primary:hover { background: rgba(79,124,255,.20); }

        .btn-danger {
            border-color: rgba(225,29,72,.35);
            background: rgba(225,29,72,.10);
        }
        .btn-danger:hover { background: rgba(225,29,72,.16); }

        .hint { font-size: 13px; color: var(--muted); }
        .error { color: #b91c1c; font-size: 13px; margin-top: 6px; }

        .pager { margin-top: 14px; }
        .pager nav { display: flex; justify-content: center; }
        .pager .pagination { gap: 6px; }

        hr { border: 0; border-top: 1px solid var(--border); margin: 16px 0; }

        img { border-radius: 10px; border: 1px solid rgba(15,23,42,.12); }

        @media (min-width: 840px) {
            .row.cols-2 { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>
<div class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <span class="brand-badge" aria-hidden="true"></span>
            <a href="{{ route('blogavel.home') }}">Blogavel</a>
            <span class="hint">Admin</span>
        </div>
        <div class="actions">
            @if (auth()->check())
                <nav class="nav" aria-label="Admin navigation">
                    <a href="{{ route('blogavel.admin.posts.index') }}">Posts</a>
                    <a href="{{ route('blogavel.admin.categories.index') }}">Categories</a>
                    <a href="{{ route('blogavel.admin.tags.index') }}">Tags</a>
                    <a href="{{ route('blogavel.admin.media.index') }}">Media</a>
                    <a href="{{ route('blogavel.admin.comments.index') }}">Comments</a>
                    <a href="{{ route('blogavel.admin.profile.edit') }}">Profile</a>
                </nav>

                @if (\Illuminate\Support\Facades\Route::has('blogavel.admin.logout'))
                    <form method="POST" action="{{ route('blogavel.admin.logout') }}" style="display:inline">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @elseif (\Illuminate\Support\Facades\Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit">Logout</button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>

<div class="wrap">
    <div class="card">
        @yield('content')
    </div>
</div>

@stack('scripts')
</body>
</html>
