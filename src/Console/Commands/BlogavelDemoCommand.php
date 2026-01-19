<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Console\Commands;

use Blogavel\Blogavel\Models\Category;
use Blogavel\Blogavel\Models\Comment;
use Blogavel\Blogavel\Models\Post;
use Blogavel\Blogavel\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class BlogavelDemoCommand extends Command
{
    protected $signature = 'blogavel:demo {--reset : Delete all blogavel_posts before creating demo data}';

    protected $description = 'Create demo Blogavel posts (draft/scheduled/published).';

    public function handle(): int
    {
        if ($this->option('reset')) {
            Comment::query()->delete();
            Post::query()->delete();
            Tag::query()->delete();
            Category::query()->delete();
            $this->info('Cleared blogavel_posts, blogavel_tags, blogavel_categories.');
        }

        $tech = Category::create([
            'name' => 'Tech',
            'slug' => 'tech',
            'parent_id' => null,
        ]);

        $laravel = Category::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
            'parent_id' => $tech->id,
        ]);

        $tagPhp = Tag::create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        $tagLaravel = Tag::create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $draft = Post::create([
            'category_id' => $laravel->id,
            'title' => 'Draft post',
            'slug' => $this->uniqueSlug('draft-post'),
            'content' => 'This is a draft post.',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $draft->tags()->sync([$tagPhp->id]);

        $scheduled = Post::create([
            'category_id' => $laravel->id,
            'title' => 'Scheduled post',
            'slug' => $this->uniqueSlug('scheduled-post'),
            'content' => 'This post is scheduled for the future.',
            'status' => 'scheduled',
            'published_at' => now()->addDay(),
        ]);

        $scheduled->tags()->sync([$tagPhp->id, $tagLaravel->id]);

        $published = Post::create([
            'category_id' => $laravel->id,
            'title' => 'Published post',
            'slug' => $this->uniqueSlug('published-post'),
            'content' => 'This post is published and should appear publicly.',
            'status' => 'published',
            'published_at' => now()->subHour(),
        ]);

        $published->tags()->sync([$tagLaravel->id]);

        $comment = Comment::create([
            'post_id' => $published->id,
            'parent_id' => null,
            'user_id' => null,
            'guest_name' => 'Alice',
            'guest_email' => 'alice@example.test',
            'content' => 'Nice post!',
            'status' => 'approved',
            'ip' => null,
            'user_agent' => null,
        ]);

        Comment::create([
            'post_id' => $published->id,
            'parent_id' => $comment->id,
            'user_id' => null,
            'guest_name' => 'Bob',
            'guest_email' => 'bob@example.test',
            'content' => 'I agree!',
            'status' => 'approved',
            'ip' => null,
            'user_agent' => null,
        ]);

        $this->info('Created demo posts:');
        $this->line('- Draft: '.$draft->slug);
        $this->line('- Scheduled: '.$scheduled->slug);
        $this->line('- Published: '.$published->slug);

        $this->line('Public list: /'.trim((string) config('blogavel.route_prefix', 'blogavel'), '/').'/'.trim((string) config('blogavel.public_posts_prefix', 'posts'), '/'));

        return self::SUCCESS;
    }

    private function uniqueSlug(string $base): string
    {
        $slug = Str::slug($base);
        $candidate = $slug;
        $i = 2;

        while (Post::query()->where('slug', $candidate)->exists()) {
            $candidate = $slug.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
