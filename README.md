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
composer require blogavel/blogavel
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

`blogavel.admin_middleware` (default: `['web', 'auth']`)

This middleware is applied to the admin web UI routes.

### Manage Blog gate (optional)

Blogavel can register a `manage-blog` Gate and apply it to admin routes.

- `blogavel.manage_blog_gate` (default: `false`)
- `blogavel.manage_blog_allow_local` (default: `true`)
- `blogavel.manage_blog_admin_emails` (default: `[]`)
- `blogavel.manage_blog_admin_ids` (default: `[]`)

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

Admin web UI (default, requires `auth`):

- `/<route_prefix>/<admin_prefix>/posts`
- `/<route_prefix>/<admin_prefix>/categories`
- `/<route_prefix>/<admin_prefix>/tags`
- `/<route_prefix>/<admin_prefix>/media`
- `/<route_prefix>/<admin_prefix>/comments`

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
