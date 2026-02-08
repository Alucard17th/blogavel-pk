<?php

declare(strict_types=1);

namespace Blogavel\Blogavel\Tests\Feature;

use Blogavel\Blogavel\Models\Category;
use Blogavel\Blogavel\Models\Post;
use Blogavel\Blogavel\Models\Tag;
use Blogavel\Blogavel\Tests\Support\User;
use Blogavel\Blogavel\Tests\TestCase;
use Laravel\Sanctum\Sanctum;

final class PostsTest extends TestCase
{
    public function test_public_posts_index_returns_only_published_posts(): void
    {
        Post::create([
            'title' => 'Draft',
            'slug' => 'draft',
            'content' => '...',
            'status' => 'draft',
        ]);

        Post::create([
            'title' => 'Published',
            'slug' => 'published',
            'content' => '...',
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/blogavel/v1/posts');

        $response->assertStatus(200);
        $response->assertJsonMissing(['slug' => 'draft']);
        $response->assertJsonFragment(['slug' => 'published']);
    }

    public function test_public_post_show_returns_post_by_slug(): void
    {
        Post::create([
            'title' => 'Published',
            'slug' => 'published',
            'content' => '...',
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/blogavel/v1/posts/published');

        $response->assertStatus(200);
        $response->assertJsonPath('data.slug', 'published');
    }

    public function test_admin_can_create_update_and_delete_post(): void
    {
        config()->set('blogavel.api_admin_auth', 'sanctum');
        config()->set('blogavel.manage_blog_gate', false);

        $user = User::query()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $category = Category::create([
            'name' => 'Cat',
            'slug' => 'cat',
        ]);

        $tag = Tag::create([
            'name' => 'Tag',
            'slug' => 'tag',
        ]);

        Sanctum::actingAs($user);

        $create = $this->postJson('/api/blogavel/v1/admin/posts', [
            'title' => 'New',
            'content' => 'Body',
            'status' => 'draft',
            'category_id' => $category->id,
            'tags' => [$tag->id],
        ]);

        $create->assertStatus(201);
        $postId = $create->json('data.id');

        $this->assertSame($user->id, Post::query()->findOrFail($postId)->author_id);

        $update = $this->putJson("/api/blogavel/v1/admin/posts/{$postId}", [
            'title' => 'Updated',
            'content' => 'Body 2',
            'status' => 'published',
            'slug' => 'updated',
        ]);

        $update->assertStatus(200);
        $this->assertSame('Updated', Post::query()->findOrFail($postId)->title);

        $delete = $this->deleteJson("/api/blogavel/v1/admin/posts/{$postId}");
        $delete->assertStatus(200);
        $this->assertNull(Post::query()->find($postId));
    }
}
