# Blogavel

Blogavel is a blog/CMS package for Laravel.

It ships:

- Web pages for public posts and a Blade-based admin UI.
- JSON API endpoints (public + admin).
- Database migrations and Eloquent models.
- Optional admin authorization gate.
- Optional API admin authentication via Laravel Sanctum or a simple API key.

## Requirements

- PHP ^8.2
- Laravel / Illuminate ^12.0

## Installation

Install via Composer:

```bash
composer require alucard17th/blogavel-pk
```

### Publish configuration (recommended)

```bash
php artisan vendor:publish --tag=blogavel-config
```

This will publish `config/blogavel.php`.

### Run migrations

```bash
php artisan migrate
```

Blogavel registers its migrations automatically via the service provider.

### (Optional) Publish views and translations

If you want to customize the Blade templates or translations:

```bash
php artisan vendor:publish --tag=blogavel-views
php artisan vendor:publish --tag=blogavel-lang
```

## Configuration

All configuration lives under `config/blogavel.php`.

### Route prefixes

- `blogavel.route_prefix` (default: `blogavel`)
- `blogavel.public_posts_prefix` (default: `posts`)
- `blogavel.admin_prefix` (default: `admin`)

### Admin web middleware

`blogavel.admin_middleware` (default: `['web', 'blogavel.admin']`)

This middleware is applied to the admin web UI routes.

By default, Blogavel uses a package middleware that redirects unauthenticated users to the Blogavel admin login page.

If you have previously published `config/blogavel.php`, make sure your published config matches this new default (or update it), otherwise you may still be using `auth` and be redirected to your app’s default login route.

### Manage Blog gate (optional)

Blogavel can register a `manage-blog` Gate and apply it to admin routes.

- `blogavel.manage_blog_gate` (default: `false`)
- `blogavel.manage_blog_allow_local` (default: `true`)
- `blogavel.manage_blog_admin_emails` (default: `[]`)
- `blogavel.manage_blog_admin_ids` (default: `[]`)

Environment variables:

```env
BLOGAVEL_MANAGE_BLOG_GATE=false
BLOGAVEL_MANAGE_BLOG_ALLOW_LOCAL=true
BLOGAVEL_MANAGE_BLOG_ADMIN_EMAILS=
BLOGAVEL_MANAGE_BLOG_ADMIN_IDS=
```

When enabled:

- Web admin routes add `can:manage-blog` to the middleware stack.
- API admin routes (Sanctum mode) add `can:manage-blog` to the middleware stack.

### Media storage

- `blogavel.media_disk` (default: `public`)
- `blogavel.media_directory` (default: `blogavel`)

### API admin authentication

`blogavel.api_admin_auth` controls authentication for API admin endpoints.

Supported modes:

- `sanctum` (default)
- `api_key`

#### Sanctum mode

Set:

```env
BLOGAVEL_API_ADMIN_AUTH=sanctum
```

You must have `laravel/sanctum` installed in your app:

```bash
composer require laravel/sanctum
```

Notes:

- Blogavel uses `auth:sanctum` middleware for admin API endpoints.
- Blogavel also provides simple auth endpoints under `/api/<route_prefix>/v1/auth/*` (login/me/logout).
- If Sanctum is not installed and you configured `sanctum`, Blogavel will throw a runtime exception when routes are loaded.

#### API key mode

Set:

```env
BLOGAVEL_API_ADMIN_AUTH=api_key
BLOGAVEL_API_KEY_HEADER=X-API-KEY
BLOGAVEL_API_KEYS=key1,key2
```

- Requests to admin API endpoints must include the configured header.
- The value must match one of the configured keys.

## Routes

Blogavel registers routes automatically.

### Web routes

All web routes are under the `web` middleware group.

Default URLs:

- Public home: `/<route_prefix>`
- Public posts index: `/<route_prefix>/<public_posts_prefix>`
- Public post page: `/<route_prefix>/<public_posts_prefix>/{post}`
- Public comment submit: `POST /<route_prefix>/<public_posts_prefix>/{post}/comments`

Admin web UI:

- `/<route_prefix>/<admin_prefix>/posts`
- `/<route_prefix>/<admin_prefix>/categories`
- `/<route_prefix>/<admin_prefix>/tags`
- `/<route_prefix>/<admin_prefix>/media`
- `/<route_prefix>/<admin_prefix>/comments`

Blogavel admin auth:

- `GET /<route_prefix>/<admin_prefix>/login`
- `POST /<route_prefix>/<admin_prefix>/login`
- `POST /<route_prefix>/<admin_prefix>/logout`

Admin profile:

- `GET /<route_prefix>/<admin_prefix>/profile`
- `PUT /<route_prefix>/<admin_prefix>/profile`

### API routes

All API routes are under `api` middleware group and prefixed with `/api`.

Health:

- `GET /api/<route_prefix>/health`

Public API v1:

- `GET /api/<route_prefix>/v1/posts`
- `GET /api/<route_prefix>/v1/posts/{post}`
- `GET /api/<route_prefix>/v1/categories`
- `GET /api/<route_prefix>/v1/categories/{category}`
- `GET /api/<route_prefix>/v1/tags`
- `GET /api/<route_prefix>/v1/tags/{tag}`
- `GET /api/<route_prefix>/v1/posts/{post}/comments`
- `POST /api/<route_prefix>/v1/posts/{post}/comments`

Admin API v1:

- `POST /api/<route_prefix>/v1/admin/posts`
- `PUT /api/<route_prefix>/v1/admin/posts/{post:id}`
- `DELETE /api/<route_prefix>/v1/admin/posts/{post:id}`

- `POST /api/<route_prefix>/v1/admin/categories`
- `PUT /api/<route_prefix>/v1/admin/categories/{category:id}`
- `DELETE /api/<route_prefix>/v1/admin/categories/{category:id}`

- `POST /api/<route_prefix>/v1/admin/tags`
- `PUT /api/<route_prefix>/v1/admin/tags/{tag:id}`
- `DELETE /api/<route_prefix>/v1/admin/tags/{tag:id}`

- `POST /api/<route_prefix>/v1/admin/media`
- `DELETE /api/<route_prefix>/v1/admin/media/{medium}`

- `GET /api/<route_prefix>/v1/admin/comments`
- `POST /api/<route_prefix>/v1/admin/comments/{comment}/approve`
- `POST /api/<route_prefix>/v1/admin/comments/{comment}/spam`
- `DELETE /api/<route_prefix>/v1/admin/comments/{comment}`

Auth endpoints (Sanctum mode only):

- `POST /api/<route_prefix>/v1/auth/login`
- `GET /api/<route_prefix>/v1/auth/me`
- `POST /api/<route_prefix>/v1/auth/logout`

## Models / Database tables

Blogavel uses Eloquent models and ships migrations that create (at least) the following tables:

- `blogavel_posts`
- `blogavel_categories`
- `blogavel_tags`
- `blogavel_comments`
- `blogavel_media`
- `blogavel_post_tag` (pivot)

## Fetching posts in your app

Blogavel ships Eloquent models you can use directly.

Example: fetch a published post (by slug) with its featured image URL:

```php
use Blogavel\Blogavel\Models\Post;

$post = Post::query()
    ->published()
    ->with('featuredMedia')
    ->where('slug', $slug)
    ->firstOrFail();

$featuredImageUrl = $post->featured_image_url;
```

### Content whitespace normalization

Rich-text editors and copy/paste from sources like Google Docs/Word can introduce non-breaking spaces (NBSP, `&nbsp;` / `\u00A0`) into HTML.

To prevent long unbreakable lines (horizontal overflow) and to improve excerpt/SEO text generation, Blogavel normalizes post `content` by converting NBSP whitespace into regular spaces.

## Posts: author + views

Blogavel posts support:

- `author_id` (nullable)
- `views_count` (unsigned integer, default `0`)

Behavior:

- Creating a post from Blogavel admin (web + API) auto-sets `author_id` to the authenticated user, when available.
- Viewing a published post (web show + public API show) automatically increments `views_count`.

The API `PostResource` includes `views_count`, and includes an `author` object when the relationship is loaded.

## Commands

### Create a Blogavel admin user

Use the interactive command:

```bash
php artisan blogavel:make-admin
```

This command:

- Creates a user using your app’s configured `auth.providers.users.model`.
- Adds the created user to `BLOGAVEL_MANAGE_BLOG_ADMIN_EMAILS` (or `BLOGAVEL_MANAGE_BLOG_ADMIN_IDS` when using `--use-id`).
- Enables the `manage-blog` gate via environment configuration.

If you are using config caching, run:

```bash
php artisan config:clear
```

## Development

### Running the package test suite

From the package root:

```bash
composer install
vendor/bin/phpunit -c phpunit.xml
```

### Notes on dependencies

- Runtime dependencies are declared under `require`.
- Test/dev dependencies are declared under `require-dev`.
- `orchestra/testbench` pulls in `laravel/framework` for testing purposes.

## Contributing

- Fork the repo
- Create a feature branch
- Add/adjust tests under `tests/`
- Run the test suite
- Open a PR

## License

MIT
